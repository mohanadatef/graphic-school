# 📝 Coding Standards - Graphic School

## Naming Conventions

### Backend (PHP/Laravel):

#### Models:
- **PascalCase**: `User`, `Course`, `Enrollment`
- **Singular**: `Course` (not `Courses`)
- **Location**: `Modules/{Module}/Models/{ModelName}.php`

**مثال**:
```php
class Course extends Model
class Enrollment extends Model
```

---

#### Controllers:
- **PascalCase**: `CourseController`, `UserController`
- **Suffix**: `Controller`
- **Location**: `Modules/{Module}/Http/Controllers/{Resource}Controller.php`

**مثال**:
```php
class CourseController extends BaseController
class EnrollmentController extends BaseController
```

---

#### Requests (Form Requests):
- **PascalCase**: `StoreCourseRequest`, `UpdateCourseRequest`
- **Pattern**: `{Action}{Resource}Request`
- **Location**: `Modules/{Module}/Http/Requests/{Action}{Resource}Request.php`

**مثال**:
```php
class StoreCourseRequest extends FormRequest
class UpdateCourseRequest extends FormRequest
```

---

#### Resources (API Resources):
- **PascalCase**: `CourseResource`, `UserResource`
- **Suffix**: `Resource`
- **Location**: `Modules/{Module}/Http/Resources/{Resource}Resource.php`

**مثال**:
```php
class CourseResource extends JsonResource
```

---

#### Use Cases:
- **PascalCase**: `CreateCourseUseCase`, `UpdateCourseUseCase`
- **Pattern**: `{Action}{Resource}UseCase`
- **Location**: `Modules/{Module}/Application/UseCases/{Action}{Resource}UseCase.php`

**مثال**:
```php
class CreateCourseUseCase extends BaseUseCase
```

---

#### DTOs:
- **PascalCase**: `CreateCourseDTO`, `UpdateCourseDTO`
- **Pattern**: `{Action}{Resource}DTO`
- **Location**: `Modules/{Module}/Application/DTOs/{Action}{Resource}DTO.php`

**مثال**:
```php
class CreateCourseDTO
{
    public static function fromArray(array $data): self
}
```

---

#### Services:
- **PascalCase**: `CourseService`, `EnrollmentService`
- **Suffix**: `Service`
- **Location**: `Modules/{Module}/Services/{Resource}Service.php`

**مثال**:
```php
class CourseService
{
    public function __construct(
        private CourseRepositoryInterface $repository
    ) {}
}
```

---

#### Repositories:
- **Interface**: `CourseRepositoryInterface`
- **Implementation**: `CourseRepository`
- **Location**: 
  - Interface: `Modules/{Module}/Repositories/Interfaces/{Resource}RepositoryInterface.php`
  - Implementation: `Modules/{Module}/Repositories/Eloquent/{Resource}Repository.php`

**مثال**:
```php
interface CourseRepositoryInterface
{
    public function find(int $id): ?Course;
}

class CourseRepository extends BaseRepository implements CourseRepositoryInterface
{
    // Implementation
}
```

---

#### Routes:
- **snake_case**: `api.php`
- **RESTful**: استخدام `apiResource` عند الإمكان
- **Location**: `Modules/{Module}/Routes/api.php`

**مثال**:
```php
Route::apiResource('courses', CourseController::class);
Route::post('/courses/{course}/assign-instructors', [CourseController::class, 'assignInstructors']);
```

---

### Frontend (JavaScript/Vue):

#### Components:
- **PascalCase**: `CourseForm.vue`, `AdminDashboard.vue`
- **Location**: `src/components/` أو `src/views/`

**مثال**:
```vue
<!-- CourseForm.vue -->
<script setup>
// Component logic
</script>
```

---

#### Composables:
- **camelCase**: `useAuth.js`, `useLoading.js`
- **Prefix**: `use`
- **Location**: `src/composables/`

**مثال**:
```javascript
export function useAuth() {
  // Composable logic
}
```

---

#### Stores (Pinia):
- **camelCase**: `auth.js`, `course.js`
- **Location**: `src/stores/`

**مثال**:
```javascript
export const useAuthStore = defineStore('auth', () => {
  // Store logic
});
```

---

#### Services:
- **camelCase**: `authService.js`, `courseService.js`
- **Suffix**: `Service`
- **Location**: `src/services/api/`

**مثال**:
```javascript
export const authService = {
  async login(credentials) {
    // Service logic
  }
};
```

---

#### Utils:
- **camelCase**: `validation.js`, `seo.js`
- **Location**: `src/utils/`

---

## Request/Response Style

### Unified API Response Format:

**Success Response**:
```json
{
  "success": true,
  "message": "Operation successful",
  "data": {
    // Response data
  },
  "status": 200,
  "meta": {
    // Pagination or additional metadata
  }
}
```

**Error Response**:
```json
{
  "success": false,
  "message": "Error message",
  "errors": {
    "field": ["Error message"]
  },
  "status": 422
}
```

### BaseController Methods:
```php
$this->success($data, $message = null)
$this->created($data, $message = null)
$this->paginated($data, $message = null)
$this->error($message, $status = 400)
```

---

## Error Handling Patterns

### Backend:
- **Try-Catch**: في Use Cases
- **Validation**: Form Requests
- **Exceptions**: Custom exceptions
- **Global Handler**: `app/Exceptions/Handler.php`

**مثال**:
```php
try {
    $result = $this->handle($input);
    return $result;
} catch (Throwable $e) {
    UseCaseLogger::failure($useCaseClass, $e);
    throw $e;
}
```

### Frontend:
- **Error Boundary**: `ErrorBoundary.vue`
- **API Interceptor**: `client.js`
- **Toast Notifications**: `useToast.js`
- **Global Error Handler**: `App.vue`

**مثال**:
```javascript
api.interceptors.response.use(
  (response) => response,
  (error) => {
    if (error.response?.status === 401) {
      authStore.clearSession();
      window.location.href = '/login';
    }
    return Promise.reject(error);
  }
);
```

---

## Use of Patterns

### 1. DTOs (Data Transfer Objects)
**الاستخدام**: ✅ مستخدم في Application Layer

**البنية**:
```php
class CreateCourseDTO
{
    public function __construct(
        public string $title,
        public int $categoryId,
        public float $price
    ) {}
    
    public static function fromArray(array $data): self
    {
        return new self(
            $data['title'],
            $data['category_id'],
            $data['price']
        );
    }
}
```

---

### 2. Service Layer
**الاستخدام**: ✅ مستخدم في بعض Modules

**البنية**:
```php
class CourseService
{
    public function __construct(
        private CourseRepositoryInterface $repository
    ) {}
    
    public function create(array $data): Course
    {
        // Business logic
        return $this->repository->create($data);
    }
}
```

---

### 3. Repository Pattern
**الاستخدام**: ✅ مستخدم في كل Module

**البنية**:
```php
interface CourseRepositoryInterface
{
    public function find(int $id): ?Course;
    public function create(array $data): Course;
}

class CourseRepository extends BaseRepository implements CourseRepositoryInterface
{
    protected function makeModel(): Model
    {
        return new Course();
    }
}
```

---

### 4. Use Case Pattern
**الاستخدام**: ✅ مستخدم في Application Layer

**البنية**:
```php
class CreateCourseUseCase extends BaseUseCase
{
    protected function handle(CreateCourseDTO $dto): Course
    {
        // Business logic
        return $course;
    }
}
```

---

### 5. Enums
**الاستخدام**: ✅ مستخدم للحالات

**البنية**:
```php
enum EnrollmentStatus: string
{
    case PENDING = 'pending';
    case APPROVED = 'approved';
    case REJECTED = 'rejected';
}
```

---

## Clean Code Patterns

### 1. Single Responsibility Principle (SRP)
- كل Class له مسؤولية واحدة
- Use Cases منفصلة
- Services محددة

### 2. Dependency Injection
- Constructor injection
- Interface-based
- Facade usage محدود

**مثال**:
```php
public function __construct(
    private CourseRepositoryInterface $repository
) {}
```

### 3. Type Hints
- Strong typing في PHP 8.1+
- Return types
- Parameter types

**مثال**:
```php
public function find(int $id): ?Course
```

### 4. DocBlocks
- PHPDoc comments
- Parameter descriptions
- Return type descriptions

**مثال**:
```php
/**
 * Create a new course
 *
 * @param CreateCourseDTO $dto
 * @return Course
 */
public function create(CreateCourseDTO $dto): Course
```

---

## Anti-Patterns (يجب تجنبها)

### 1. ❌ God Classes
- Classes كبيرة جداً
- مسؤوليات متعددة

### 2. ❌ Direct Model Access in Controllers
- يجب استخدام Repositories/Services

### 3. ❌ Business Logic in Controllers
- يجب استخدام Use Cases/Services

### 4. ❌ Hard-coded Values
- يجب استخدام Config/Constants

---

## Recommended Improvements

### 1. Standardize Error Messages
- استخدام Translation keys
- رسائل خطأ موحدة

### 2. Add More Type Hints
- إضافة type hints في كل مكان ممكن
- استخدام PHP 8.1+ features

### 3. Improve Documentation
- إضافة PHPDoc في كل method
- إضافة README في كل Module

### 4. Standardize Validation
- استخدام Form Requests في كل مكان
- رسائل validation موحدة

### 5. Add More Tests
- زيادة Test coverage
- إضافة Integration tests

### 6. Code Formatting
- استخدام Laravel Pint
- Standardize code style

### 7. Reduce Code Duplication
- Extract common logic
- استخدام Traits عند الحاجة

---

## Code Quality Tools

### Current:
- **PHPUnit**: Testing
- **Laravel Pint**: Code formatting (available)

### Recommended:
- **PHPStan**: Static analysis
- **Laravel Pint**: Enforce code style
- **ESLint**: Frontend linting
- **Prettier**: Frontend formatting

---

## Git Conventions

### Branch Naming:
- `feature/feature-name`
- `bugfix/bug-name`
- `hotfix/hotfix-name`

### Commit Messages:
- `feat: Add new feature`
- `fix: Fix bug`
- `refactor: Refactor code`
- `docs: Update documentation`

---

**آخر تحديث**: 2025-11-21  
**الإصدار**: 1.0.0

