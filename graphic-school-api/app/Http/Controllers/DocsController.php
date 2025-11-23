<?php

namespace App\Http\Controllers;

use Illuminate\Http\JsonResponse;
use Illuminate\Http\Response;
use Illuminate\Support\Facades\File;

class DocsController extends Controller
{
    /**
     * Display Swagger UI
     */
    public function index(): Response
    {
        $openApiJson = $this->getOpenApiJson();
        $swaggerUiHtml = $this->getSwaggerUiHtml($openApiJson);

        return response($swaggerUiHtml, 200, [
            'Content-Type' => 'text/html',
        ]);
    }

    /**
     * Get OpenAPI JSON
     */
    public function json(): JsonResponse
    {
        $json = $this->getOpenApiJson();
        return response()->json(json_decode($json, true));
    }

    /**
     * Get OpenAPI YAML
     */
    public function yaml(): Response
    {
        $yaml = File::exists(base_path('openapi.yaml'))
            ? File::get(base_path('openapi.yaml'))
            : $this->generateBasicYaml();

        return response($yaml, 200, [
            'Content-Type' => 'text/yaml',
        ]);
    }

    private function getOpenApiJson(): string
    {
        if (File::exists(base_path('openapi.json'))) {
            return File::get(base_path('openapi.json'));
        }

        // Generate from YAML if exists
        if (File::exists(base_path('openapi.yaml'))) {
            $yaml = File::get(base_path('openapi.yaml'));
            $data = yaml_parse($yaml);
            return json_encode($data, JSON_PRETTY_PRINT | JSON_UNESCAPED_SLASHES);
        }

        // Return basic JSON
        return json_encode($this->getBasicOpenApi(), JSON_PRETTY_PRINT | JSON_UNESCAPED_SLASHES);
    }

    private function getSwaggerUiHtml(string $openApiJson): string
    {
        $jsonUrl = url('/api/docs-json');
        
        return <<<HTML
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Graphic School LMS API Documentation</title>
    <link rel="stylesheet" type="text/css" href="https://unpkg.com/swagger-ui-dist@5.10.0/swagger-ui.css" />
    <style>
        html {
            box-sizing: border-box;
            overflow: -moz-scrollbars-vertical;
            overflow-y: scroll;
        }
        *, *:before, *:after {
            box-sizing: inherit;
        }
        body {
            margin:0;
            background: #fafafa;
        }
    </style>
</head>
<body>
    <div id="swagger-ui"></div>
    <script src="https://unpkg.com/swagger-ui-dist@5.10.0/swagger-ui-bundle.js"></script>
    <script src="https://unpkg.com/swagger-ui-dist@5.10.0/swagger-ui-standalone-preset.js"></script>
    <script>
        window.onload = function() {
            const ui = SwaggerUIBundle({
                url: "{$jsonUrl}",
                dom_id: '#swagger-ui',
                deepLinking: true,
                presets: [
                    SwaggerUIBundle.presets.apis,
                    SwaggerUIStandalonePreset
                ],
                plugins: [
                    SwaggerUIBundle.plugins.DownloadUrl
                ],
                layout: "StandaloneLayout",
                validatorUrl: null,
                docExpansion: "list",
                filter: true,
                showExtensions: true,
                showCommonExtensions: true
            });
        };
    </script>
</body>
</html>
HTML;
    }

    private function generateBasicYaml(): string
    {
        return <<<YAML
openapi: 3.1.0
info:
  title: Graphic School LMS API
  version: 1.0.0
  description: Complete API documentation for Graphic School Learning Management System
servers:
  - url: {url}/api
    description: Current Server
paths: {}
components:
  securitySchemes:
    bearerAuth:
      type: http
      scheme: bearer
      bearerFormat: JWT
YAML;
    }

    private function getBasicOpenApi(): array
    {
        return [
            'openapi' => '3.1.0',
            'info' => [
                'title' => 'Graphic School LMS API',
                'version' => '1.0.0',
                'description' => 'Complete API documentation for Graphic School Learning Management System',
            ],
            'servers' => [
                [
                    'url' => config('app.url') . '/api',
                    'description' => 'Current Server',
                ],
            ],
            'paths' => [],
            'components' => [
                'securitySchemes' => [
                    'bearerAuth' => [
                        'type' => 'http',
                        'scheme' => 'bearer',
                        'bearerFormat' => 'JWT',
                    ],
                ],
            ],
        ];
    }
}
