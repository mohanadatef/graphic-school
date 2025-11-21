# 🧪 Testing and Postman Notes - Graphic School

## Testing Overview

### Test Types:
- ✅ **Unit Tests**: Testing individual components
- ✅ **Feature Tests**: Testing API endpoints
- ⚠️ **Integration Tests**: محدود
- ❌ **E2E Tests**: غير موجود

---

## Test Coverage

### Current Tests:

#### 1. Comprehensive API Tests
**File**: `tests/Feature/Api/ComprehensiveApiTest.php`

**Coverage**:
- ✅ Authentication (Register, Login, Logout)
- ✅ Authorization (Role-based access control)
- ✅ CRUD Operations (All resources)
- ✅ Edge Cases (SQL Injection, XSS, Rate Limiting)
- ✅ Performance (Large datasets)
- ✅ Validation (Input validation, constraints)

**Test Count**: 40+ test cases

---

#### 2. Unit Tests

**Files**:
- `tests/Unit/Modules/ACL/Users/UserRepositoryTest.php`
- `tests/Unit/Modules/ACL/Auth/AuthUseCaseTest.php`
- `tests/Unit/Services/TableBuilderTest.php`

**Coverage**:
- ✅ Repository methods
- ✅ Use Case logic
- ✅ Service methods

---

#### 3. Feature Tests

**Files**:
- `tests/Feature/Api/AuthTest.php`
- `tests/Feature/Api/CoursesTest.php`
- `tests/Feature/Api/ComprehensiveApiTest.php`

**Coverage**:
- ✅ Authentication flow
- ✅ Course management
- ✅ Comprehensive API testing

---

## Test Categories

### 1. Authentication Tests
- ✅ Register with valid data
- ✅ Register with invalid email
- ✅ Register with weak password
- ✅ Register with mismatched passwords
- ✅ Login with valid credentials
- ✅ Login with invalid credentials
- ✅ Login with nonexistent user
- ✅ Logout with valid token
- ✅ Logout without token
- ✅ Logout with invalid token

---

### 2. Authorization Tests
- ✅ Student cannot access admin routes
- ✅ Instructor cannot access admin routes
- ✅ Admin can access admin routes
- ✅ Student can access student routes
- ✅ Instructor can access instructor routes

---

### 3. CRUD Operations Tests
- ✅ Create, Read, Update, Delete for all resources
- ✅ Pagination and filtering
- ✅ Search functionality
- ✅ Sorting

---

### 4. Security Tests
- ✅ SQL injection attempts
- ✅ XSS attempts
- ✅ CSRF protection
- ✅ Rate limiting
- ✅ Input sanitization
- ✅ Password strength validation
- ✅ Email uniqueness validation

---

### 5. Performance Tests
- ✅ Large dataset performance (< 3 seconds for 1000+ records)
- ✅ Pagination limits
- ✅ Query optimization

---

### 6. Validation Tests
- ✅ Required fields
- ✅ Email format
- ✅ Password strength
- ✅ Unique constraints
- ✅ Foreign key constraints

---

## Running Tests

### All Tests:
```bash
php artisan test
```

### Unit Tests Only:
```bash
php artisan test --testsuite=Unit
```

### Feature Tests Only:
```bash
php artisan test --testsuite=Feature
```

### Specific Test File:
```bash
php artisan test tests/Feature/Api/ComprehensiveApiTest.php
```

### Specific Test Method:
```bash
php artisan test --filter test_user_can_register
```

### With Coverage (if configured):
```bash
php artisan test --coverage
```

---

## Test Structure

### Example Test:
```php
public function test_user_can_register()
{
    $response = $this->postJson('/api/register', [
        'name' => 'Test User',
        'email' => 'test@example.com',
        'password' => 'password123',
        'password_confirmation' => 'password123',
    ]);

    $response->assertStatus(201)
        ->assertJsonStructure([
            'success',
            'data' => [
                'user',
                'token'
            ]
        ]);
}
```

---

## Postman Collection

### File Location:
- **Collection**: `graphic-school-api/postman_collection.json`
- **Documentation**: `graphic-school-api/POSTMAN_COLLECTION_COMPREHENSIVE.md`

### Import Instructions:
1. Open Postman
2. Click **Import**
3. Select `postman_collection.json`
4. Update Collection Variables:
   - `base_url`: `http://graphic-school.test/api` (or your URL)
   - `auth_token`: Will be auto-populated after login

---

## Postman Collection Structure

### Folders:
1. **Authentication**
   - Register
   - Login
   - Logout
   - Get Current User

2. **Public Endpoints**
   - Home
   - Courses
   - Course Details
   - Categories
   - Instructors
   - Settings
   - Sliders
   - Testimonials
   - Contact

3. **Student Endpoints**
   - My Courses
   - Enroll in Course
   - Course Sessions
   - Course Attendance
   - Review Course
   - Profile

4. **Instructor Endpoints**
   - My Courses
   - Course Sessions
   - Store Attendance
   - Session Attendance
   - Session Note

5. **Admin Endpoints**
   - Dashboard
   - Users Management
   - Roles Management
   - Categories Management
   - Courses Management
   - Sessions Management
   - Enrollments Management
   - Attendance Management
   - Settings Management
   - Contacts Management
   - Testimonials Management
   - Translations Management
   - Reports
   - Strategic Reports

6. **System Endpoints**
   - Health Check
   - File Upload
   - Export Data

---

## Postman Test Scripts

### Auto-save Token:
```javascript
if (pm.response.code === 200) {
    const jsonData = pm.response.json();
    if (jsonData.data && jsonData.data.token) {
        pm.collectionVariables.set("auth_token", jsonData.data.token);
    }
}
```

### Test Response Structure:
```javascript
pm.test("Response has correct structure", function () {
    const jsonData = pm.response.json();
    pm.expect(jsonData).to.have.property('success');
    pm.expect(jsonData).to.have.property('data');
    pm.expect(jsonData.success).to.be.true;
});
```

---

## Test Scenarios

### 1. Authentication Flow:
1. Register new user
2. Login with credentials
3. Use token for authenticated requests
4. Logout

### 2. Course Management Flow:
1. Admin creates course
2. Admin assigns instructors
3. Admin generates sessions
4. Student enrolls in course
5. Student views course content

### 3. Attendance Flow:
1. Instructor views session
2. Instructor records attendance
3. Student views attendance

### 4. Assessment Flow:
1. Student starts quiz
2. Student submits answers
3. System calculates score
4. Student views results

---

## Missing Test Cases

### Recommended Additional Tests:

#### 1. Integration Tests:
- ✅ Test complete user flows
- ✅ Test module interactions
- ✅ Test event listeners

#### 2. E2E Tests:
- ❌ Browser-based tests (Dusk)
- ❌ Frontend-backend integration

#### 3. Performance Tests:
- ⚠️ Load testing
- ⚠️ Stress testing
- ⚠️ Database query optimization tests

#### 4. Security Tests:
- ⚠️ Penetration testing
- ⚠️ OWASP Top 10 tests
- ⚠️ Authentication bypass tests

#### 5. API Contract Tests:
- ⚠️ API versioning tests
- ⚠️ Backward compatibility tests

---

## Test Data

### Factories:
- `UserFactory`: Generate test users
- `CourseFactory`: Generate test courses
- `CategoryFactory`: Generate test categories
- `RoleFactory`: Generate test roles

### Seeders:
- `UserSeeder`: Seed users
- `CourseSeeder`: Seed courses
- `CategorySeeder`: Seed categories
- `RoleSeeder`: Seed roles
- `EnrollmentSeeder`: Seed enrollments
- `SessionSeeder`: Seed sessions

---

## Test Environment

### Configuration:
- **Database**: Separate test database
- **Environment**: `.env.testing`
- **Cache**: In-memory cache for tests

### Setup:
```bash
php artisan test --env=testing
```

---

## Continuous Integration (CI)

### Recommended CI Setup:

#### GitHub Actions:
```yaml
name: Tests

on: [push, pull_request]

jobs:
  test:
    runs-on: ubuntu-latest
    steps:
      - uses: actions/checkout@v2
      - name: Setup PHP
        uses: shivammathur/setup-php@v2
        with:
          php-version: '8.1'
      - name: Install Dependencies
        run: composer install
      - name: Run Tests
        run: php artisan test
```

---

## Test Coverage Goals

### Current Coverage:
- **API Endpoints**: ~85%
- **Business Logic**: ~70%
- **Edge Cases**: ~60%

### Target Coverage:
- **API Endpoints**: 95%+
- **Business Logic**: 85%+
- **Edge Cases**: 80%+

---

## Postman Environment Variables

### Development:
```
base_url: http://graphic-school.test/api
auth_token: (auto-populated)
```

### Production:
```
base_url: https://api.your-domain.com/api
auth_token: (auto-populated)
```

---

## Testing Best Practices

### 1. Test Isolation:
- Each test should be independent
- Use database transactions
- Clean up after tests

### 2. Test Naming:
- Descriptive names
- Follow pattern: `test_{what}_{expected_result}`

### 3. Test Data:
- Use factories
- Avoid hard-coded data
- Use realistic data

### 4. Assertions:
- Test both success and failure cases
- Test edge cases
- Test validation

---

## Recommended Improvements

### 1. Increase Test Coverage:
- Add more unit tests
- Add integration tests
- Add E2E tests

### 2. Test Automation:
- Set up CI/CD
- Automated test runs
- Test reports

### 3. Performance Testing:
- Load testing
- Stress testing
- Database optimization tests

### 4. Security Testing:
- Penetration testing
- OWASP Top 10
- Authentication tests

---

**آخر تحديث**: 2025-11-21  
**الإصدار**: 1.0.0

