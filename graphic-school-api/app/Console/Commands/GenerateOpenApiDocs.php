<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\Route;
use Illuminate\Support\Facades\File;
use ReflectionClass;
use ReflectionMethod;

class GenerateOpenApiDocs extends Command
{
    protected $signature = 'openapi:generate';
    protected $description = 'Generate OpenAPI 3.1 documentation from routes and controllers';

    private $schemas = [];
    private $paths = [];
    private $tags = [];

    public function handle()
    {
        $this->info('Generating OpenAPI 3.1 documentation...');

        // Start building OpenAPI spec
        $spec = [
            'openapi' => '3.1.0',
            'info' => $this->getInfo(),
            'servers' => $this->getServers(),
            'tags' => $this->getTags(),
            'paths' => [],
            'components' => $this->getComponents(),
        ];

        // Process all routes
        $this->processRoutes($spec);

        // Write YAML file
        $yaml = $this->arrayToYaml($spec);
        File::put(base_path('openapi.yaml'), $yaml);

        // Write JSON file
        File::put(base_path('openapi.json'), json_encode($spec, JSON_PRETTY_PRINT | JSON_UNESCAPED_SLASHES));

        $this->info('OpenAPI documentation generated successfully!');
        $this->info('Files: openapi.yaml, openapi.json');

        return 0;
    }

    private function getInfo()
    {
        return [
            'title' => 'Graphic School LMS API',
            'description' => $this->getDescription(),
            'version' => '1.0.0',
            'contact' => [
                'name' => 'API Support',
                'email' => 'support@graphicschool.com',
            ],
            'license' => [
                'name' => 'MIT',
                'url' => 'https://opensource.org/licenses/MIT',
            ],
        ];
    }

    public function getDescription(): string
    {
        return <<<'DESC'
Complete API documentation for Graphic School Learning Management System.

## Authentication
This API uses Laravel Sanctum for authentication. Include the Bearer token in the Authorization header:
```
Authorization: Bearer {your-token}
```

## Response Format
All responses follow a unified format:
```json
{
  "success": true,
  "message": "Success message",
  "data": {},
  "errors": null,
  "status": 200,
  "meta": {}
}
```

## Pagination
Paginated responses include pagination metadata in the `meta.pagination` object.

## Rate Limiting
Authentication endpoints are rate-limited to 5 requests per minute.
DESC;
    }

    private function getServers()
    {
        return [
            [
                'url' => config('app.url') . '/api',
                'description' => 'Current Server',
            ],
            [
                'url' => 'http://graphic-school.test/api',
                'description' => 'Local Development',
            ],
        ];
    }

    private function getTags()
    {
        return [
            ['name' => 'Auth', 'description' => 'Authentication endpoints'],
            ['name' => 'Users', 'description' => 'User management'],
            ['name' => 'Roles & Permissions', 'description' => 'Role and permission management'],
            ['name' => 'Categories', 'description' => 'Course category management'],
            ['name' => 'Courses', 'description' => 'Course management'],
            ['name' => 'Curriculum', 'description' => 'Course curriculum (modules, lessons, resources)'],
            ['name' => 'Sessions', 'description' => 'Course session management'],
            ['name' => 'Attendance', 'description' => 'Attendance tracking'],
            ['name' => 'Enrollments', 'description' => 'Student enrollments'],
            ['name' => 'Certificates', 'description' => 'Certificate generation and management'],
            ['name' => 'Quizzes', 'description' => 'Quiz and assessment management'],
            ['name' => 'Projects', 'description' => 'Student project management'],
            ['name' => 'Media', 'description' => 'Media library management'],
            ['name' => 'Payments', 'description' => 'Payment processing and tracking'],
            ['name' => 'Reports', 'description' => 'Analytics and reporting'],
            ['name' => 'Settings', 'description' => 'System settings'],
            ['name' => 'Notifications', 'description' => 'In-app notifications'],
            ['name' => 'Messaging', 'description' => 'Student-Instructor messaging'],
            ['name' => 'CMS', 'description' => 'Content management (Pages, Sliders, Testimonials, FAQ, Contacts)'],
            ['name' => 'Localization', 'description' => 'Translation and localization'],
            ['name' => 'Tickets', 'description' => 'Support ticket system'],
            ['name' => 'Audit Logs', 'description' => 'System audit logs'],
            ['name' => 'Public', 'description' => 'Public-facing endpoints'],
        ];
    }

    private function processRoutes(&$spec)
    {
        $routes = Route::getRoutes();
        
        foreach ($routes as $route) {
            if (str_starts_with($route->uri(), 'api/')) {
                $path = '/' . str_replace('api/', '', $route->uri());
                $method = strtolower($route->methods()[0]);
                
                if (!isset($spec['paths'][$path])) {
                    $spec['paths'][$path] = [];
                }

                $operation = $this->buildOperation($route, $method);
                if ($operation) {
                    $spec['paths'][$path][$method] = $operation;
                }
            }
        }
    }

    private function buildOperation($route, $method)
    {
        $action = $route->getAction();
        $controller = $action['controller'] ?? null;

        if (!$controller || is_string($controller) && str_contains($controller, '@')) {
            [$controllerClass, $methodName] = explode('@', $controller);
        } else {
            return null;
        }

        $tag = $this->getTagFromPath($route->uri());
        $summary = $this->getSummary($controllerClass, $methodName, $method);
        $operationId = $this->getOperationId($route->uri(), $method);

        $operation = [
            'tags' => [$tag],
            'summary' => $summary,
            'description' => $this->getMethodDescription($controllerClass, $methodName),
            'operationId' => $operationId,
            'parameters' => $this->getParameters($route),
            'responses' => $this->getResponses($controllerClass, $methodName, $method),
        ];

        // Add request body for POST, PUT, PATCH
        if (in_array($method, ['post', 'put', 'patch'])) {
            $requestBody = $this->getRequestBody($controllerClass, $methodName);
            if ($requestBody) {
                $operation['requestBody'] = $requestBody;
            }
        }

        // Add security
        $middleware = $route->gatherMiddleware();
        if (in_array('auth:api', $middleware)) {
            $operation['security'] = [['bearerAuth' => []]];
        } else {
            $operation['security'] = [];
        }

        return $operation;
    }

    private function getTagFromPath($uri)
    {
        if (str_contains($uri, 'admin/users')) return 'Users';
        if (str_contains($uri, 'admin/roles')) return 'Roles & Permissions';
        if (str_contains($uri, 'admin/categories')) return 'Categories';
        if (str_contains($uri, 'admin/courses')) return 'Courses';
        if (str_contains($uri, 'admin/sessions')) return 'Sessions';
        if (str_contains($uri, 'admin/enrollments')) return 'Enrollments';
        if (str_contains($uri, 'admin/attendance')) return 'Attendance';
        if (str_contains($uri, 'admin/payments')) return 'Payments';
        if (str_contains($uri, 'admin/tickets')) return 'Tickets';
        if (str_contains($uri, 'admin/audit-logs')) return 'Audit Logs';
        if (str_contains($uri, 'admin/media')) return 'Media';
        if (str_contains($uri, 'admin/faqs')) return 'CMS';
        if (str_contains($uri, 'admin/pages')) return 'CMS';
        if (str_contains($uri, 'admin/sliders')) return 'CMS';
        if (str_contains($uri, 'admin/testimonials')) return 'CMS';
        if (str_contains($uri, 'admin/contacts')) return 'CMS';
        if (str_contains($uri, 'admin/settings')) return 'Settings';
        if (str_contains($uri, 'admin/reports')) return 'Reports';
        if (str_contains($uri, 'admin/translations')) return 'Localization';
        if (str_contains($uri, 'admin/quizzes')) return 'Quizzes';
        if (str_contains($uri, 'admin/modules') || str_contains($uri, 'admin/lessons')) return 'Curriculum';
        if (str_contains($uri, 'student/courses')) return 'Courses';
        if (str_contains($uri, 'student/sessions')) return 'Sessions';
        if (str_contains($uri, 'student/attendance')) return 'Attendance';
        if (str_contains($uri, 'student/payments')) return 'Payments';
        if (str_contains($uri, 'student/quizzes')) return 'Quizzes';
        if (str_contains($uri, 'student/projects')) return 'Projects';
        if (str_contains($uri, 'instructor')) return 'Courses';
        if (str_contains($uri, 'messaging')) return 'Messaging';
        if (str_contains($uri, 'notifications')) return 'Notifications';
        if (str_contains($uri, 'login') || str_contains($uri, 'register') || str_contains($uri, 'logout')) return 'Auth';
        if (str_contains($uri, 'locale') || str_contains($uri, 'translations')) return 'Localization';
        return 'Public';
    }

    private function getSummary($controllerClass, $methodName, $method)
    {
        $methodMap = [
            'index' => 'List',
            'store' => 'Create',
            'show' => 'Get',
            'update' => 'Update',
            'destroy' => 'Delete',
            'login' => 'Login',
            'register' => 'Register',
            'logout' => 'Logout',
        ];

        $action = $methodMap[$methodName] ?? ucfirst($methodName);
        return $action . ' ' . $this->getResourceName($controllerClass);
    }

    private function getResourceName($controllerClass)
    {
        $name = class_basename($controllerClass);
        return str_replace('Controller', '', $name);
    }

    private function getMethodDescription($controllerClass, $methodName)
    {
        // Try to get from docblock
        try {
            $reflection = new ReflectionClass($controllerClass);
            $method = $reflection->getMethod($methodName);
            $docComment = $method->getDocComment();
            if ($docComment) {
                $lines = explode("\n", $docComment);
                foreach ($lines as $line) {
                    if (str_contains($line, '@') || str_contains($line, '*')) {
                        $line = trim(str_replace(['*', '/'], '', $line));
                        if (!empty($line) && !str_starts_with($line, '@')) {
                            return $line;
                        }
                    }
                }
            }
        } catch (\Exception $e) {
            // Ignore
        }

        return ucfirst($methodName) . ' ' . strtolower($this->getResourceName($controllerClass));
    }

    private function getOperationId($uri, $method)
    {
        $parts = explode('/', trim($uri, '/'));
        $parts = array_filter($parts, fn($p) => $p !== 'api');
        $id = implode('', array_map('ucfirst', $parts));
        return lcfirst($id) . ucfirst($method);
    }

    private function getParameters($route)
    {
        $parameters = [];
        $uri = $route->uri();

        // Path parameters
        preg_match_all('/\{(\w+)\}/', $uri, $matches);
        foreach ($matches[1] as $param) {
            $parameters[] = [
                'name' => $param,
                'in' => 'path',
                'required' => true,
                'schema' => ['type' => 'integer'],
                'description' => ucfirst($param) . ' ID',
            ];
        }

        // Query parameters for GET requests
        if (in_array('GET', $route->methods())) {
            $parameters[] = [
                'name' => 'page',
                'in' => 'query',
                'schema' => ['type' => 'integer', 'default' => 1],
                'description' => 'Page number',
            ];
            $parameters[] = [
                'name' => 'per_page',
                'in' => 'query',
                'schema' => ['type' => 'integer', 'default' => 15],
                'description' => 'Items per page',
            ];
            $parameters[] = [
                'name' => 'search',
                'in' => 'query',
                'schema' => ['type' => 'string'],
                'description' => 'Search query',
            ];
        }

        return $parameters;
    }

    private function getRequestBody($controllerClass, $methodName)
    {
        try {
            $reflection = new ReflectionClass($controllerClass);
            $method = $reflection->getMethod($methodName);
            $parameters = $method->getParameters();

            foreach ($parameters as $param) {
                $type = $param->getType();
                if ($type && class_exists($type->getName())) {
                    $requestClass = $type->getName();
                    if (is_subclass_of($requestClass, \Illuminate\Foundation\Http\FormRequest::class)) {
                        return $this->buildRequestBodyFromRequest($requestClass);
                    }
                }
            }
        } catch (\Exception $e) {
            // Ignore
        }

        return null;
    }

    private function buildRequestBodyFromRequest($requestClass)
    {
        try {
            $reflection = new ReflectionClass($requestClass);
            $rulesMethod = $reflection->getMethod('rules');
            $rules = $rulesMethod->invoke(new $requestClass());

            $properties = [];
            $required = [];

            foreach ($rules as $field => $ruleArray) {
                $fieldRules = is_array($ruleArray) ? $ruleArray : explode('|', $ruleArray);
                $schema = $this->buildSchemaFromRules($fieldRules);
                
                if (in_array('required', $fieldRules)) {
                    $required[] = $field;
                }

                $properties[$field] = $schema;
            }

            return [
                'required' => true,
                'content' => [
                    'application/json' => [
                        'schema' => [
                            'type' => 'object',
                            'required' => $required,
                            'properties' => $properties,
                        ],
                    ],
                ],
            ];
        } catch (\Exception $e) {
            return null;
        }
    }

    private function buildSchemaFromRules($rules)
    {
        $schema = ['type' => 'string'];

        foreach ($rules as $rule) {
            if (str_starts_with($rule, 'min:')) {
                $schema['minLength'] = (int) str_replace('min:', '', $rule);
            } elseif (str_starts_with($rule, 'max:')) {
                $schema['maxLength'] = (int) str_replace('max:', '', $rule);
            } elseif ($rule === 'email') {
                $schema['format'] = 'email';
            } elseif ($rule === 'integer' || $rule === 'numeric') {
                $schema['type'] = 'number';
            } elseif ($rule === 'boolean') {
                $schema['type'] = 'boolean';
            } elseif ($rule === 'date') {
                $schema['type'] = 'string';
                $schema['format'] = 'date';
            } elseif ($rule === 'nullable') {
                $schema['nullable'] = true;
            } elseif (str_starts_with($rule, 'in:')) {
                $values = explode(',', str_replace('in:', '', $rule));
                $schema['enum'] = array_map('trim', $values);
            }
        }

        return $schema;
    }

    private function getResponses($controllerClass, $methodName, $method)
    {
        $responses = [];

        // Success response
        $statusCode = match($method) {
            'post' => '201',
            'delete' => '200',
            default => '200',
        };

        $responses[$statusCode] = [
            'description' => $this->getSuccessDescription($methodName),
            'content' => [
                'application/json' => [
                    'schema' => $this->getResponseSchema($controllerClass, $methodName),
                ],
            ],
        ];

        // Error responses
        $responses['401'] = ['$ref' => '#/components/responses/Unauthorized'];
        $responses['403'] = ['$ref' => '#/components/responses/Forbidden'];
        $responses['404'] = ['$ref' => '#/components/responses/NotFound'];
        $responses['422'] = ['$ref' => '#/components/responses/ValidationError'];

        return $responses;
    }

    private function getSuccessDescription($methodName)
    {
        return match($methodName) {
            'index' => 'List retrieved successfully',
            'store' => 'Resource created successfully',
            'show' => 'Resource retrieved successfully',
            'update' => 'Resource updated successfully',
            'destroy' => 'Resource deleted successfully',
            default => 'Operation successful',
        };
    }

    private function getResponseSchema($controllerClass, $methodName)
    {
        if ($methodName === 'index') {
            return [
                'allOf' => [
                    ['$ref' => '#/components/schemas/SuccessResponse'],
                    [
                        'type' => 'object',
                        'properties' => [
                            'data' => [
                                'type' => 'array',
                                'items' => ['$ref' => '#/components/schemas/' . $this->getSchemaName($controllerClass)],
                            ],
                            'meta' => [
                                'type' => 'object',
                                'properties' => [
                                    'pagination' => ['$ref' => '#/components/schemas/PaginationMeta'],
                                ],
                            ],
                        ],
                    ],
                ],
            ];
        }

        return [
            'allOf' => [
                ['$ref' => '#/components/schemas/SuccessResponse'],
                [
                    'type' => 'object',
                    'properties' => [
                        'data' => ['$ref' => '#/components/schemas/' . $this->getSchemaName($controllerClass)],
                    ],
                ],
            ],
        ];
    }

    private function getSchemaName($controllerClass)
    {
        $name = class_basename($controllerClass);
        $name = str_replace('Controller', '', $name);
        return $name . 'Response';
    }

    private function getComponents()
    {
        return [
            'securitySchemes' => [
                'bearerAuth' => [
                    'type' => 'http',
                    'scheme' => 'bearer',
                    'bearerFormat' => 'JWT',
                    'description' => 'Laravel Sanctum Bearer Token',
                ],
            ],
            'parameters' => $this->getCommonParameters(),
            'schemas' => $this->getCommonSchemas(),
            'responses' => $this->getCommonResponses(),
        ];
    }

    private function getCommonParameters()
    {
        return [
            'Page' => [
                'name' => 'page',
                'in' => 'query',
                'schema' => ['type' => 'integer', 'minimum' => 1, 'default' => 1],
                'description' => 'Page number',
            ],
            'PerPage' => [
                'name' => 'per_page',
                'in' => 'query',
                'schema' => ['type' => 'integer', 'minimum' => 1, 'maximum' => 100, 'default' => 15],
                'description' => 'Items per page',
            ],
            'Search' => [
                'name' => 'search',
                'in' => 'query',
                'schema' => ['type' => 'string'],
                'description' => 'Search query',
            ],
        ];
    }

    private function getCommonSchemas()
    {
        return [
            'SuccessResponse' => [
                'type' => 'object',
                'required' => ['success', 'message', 'status'],
                'properties' => [
                    'success' => ['type' => 'boolean', 'example' => true],
                    'message' => ['type' => 'string', 'example' => 'Operation successful'],
                    'data' => ['type' => 'object', 'nullable' => true],
                    'errors' => ['type' => 'object', 'nullable' => true],
                    'status' => ['type' => 'integer', 'example' => 200],
                    'meta' => ['type' => 'object', 'nullable' => true],
                ],
            ],
            'ErrorResponse' => [
                'type' => 'object',
                'required' => ['success', 'message', 'status'],
                'properties' => [
                    'success' => ['type' => 'boolean', 'example' => false],
                    'message' => ['type' => 'string', 'example' => 'Error message'],
                    'data' => ['type' => 'object', 'nullable' => true],
                    'errors' => ['type' => 'object', 'nullable' => true],
                    'status' => ['type' => 'integer', 'example' => 400],
                ],
            ],
            'PaginationMeta' => [
                'type' => 'object',
                'properties' => [
                    'current_page' => ['type' => 'integer', 'example' => 1],
                    'per_page' => ['type' => 'integer', 'example' => 15],
                    'total' => ['type' => 'integer', 'example' => 100],
                    'last_page' => ['type' => 'integer', 'example' => 7],
                    'from' => ['type' => 'integer', 'example' => 1],
                    'to' => ['type' => 'integer', 'example' => 15],
                ],
            ],
        ];
    }

    private function getCommonResponses()
    {
        return [
            'Unauthorized' => [
                'description' => 'Unauthorized - Invalid or missing token',
                'content' => [
                    'application/json' => [
                        'schema' => ['$ref' => '#/components/schemas/ErrorResponse'],
                    ],
                ],
            ],
            'Forbidden' => [
                'description' => 'Forbidden - Insufficient permissions',
                'content' => [
                    'application/json' => [
                        'schema' => ['$ref' => '#/components/schemas/ErrorResponse'],
                    ],
                ],
            ],
            'NotFound' => [
                'description' => 'Resource not found',
                'content' => [
                    'application/json' => [
                        'schema' => ['$ref' => '#/components/schemas/ErrorResponse'],
                    ],
                ],
            ],
            'ValidationError' => [
                'description' => 'Validation error',
                'content' => [
                    'application/json' => [
                        'schema' => ['$ref' => '#/components/schemas/ErrorResponse'],
                    ],
                ],
            ],
        ];
    }

    private function arrayToYaml($array, $indent = 0)
    {
        $yaml = '';
        $spaces = str_repeat('  ', $indent);

        foreach ($array as $key => $value) {
            if (is_array($value)) {
                if ($this->isAssoc($value)) {
                    $yaml .= $spaces . $key . ":\n";
                    $yaml .= $this->arrayToYaml($value, $indent + 1);
                } else {
                    foreach ($value as $item) {
                        $yaml .= $spaces . "- " . (is_array($item) ? "\n" . $this->arrayToYaml($item, $indent + 1) : $this->formatValue($item));
                    }
                }
            } else {
                $yaml .= $spaces . $key . ": " . $this->formatValue($value) . "\n";
            }
        }

        return $yaml;
    }

    private function isAssoc($array)
    {
        return array_keys($array) !== range(0, count($array) - 1);
    }

    private function formatValue($value)
    {
        if (is_bool($value)) {
            return $value ? 'true' : 'false';
        }
        if (is_null($value)) {
            return 'null';
        }
        if (is_string($value) && (str_contains($value, ':') || str_contains($value, "\n"))) {
            return '"' . addslashes($value) . '"';
        }
        return $value;
    }
}

