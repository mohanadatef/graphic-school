# Testing Strategy

## Overview

This document outlines the comprehensive testing strategy for Graphic School 2.0, covering unit tests, integration tests, and end-to-end tests.

## Testing Pyramid

### Unit Tests (Base)

- Fast execution
- Isolated components
- High coverage
- Quick feedback

### Integration Tests (Middle)

- Component interactions
- API endpoints
- Database operations
- Moderate speed

### E2E Tests (Top)

- Full user flows
- Browser automation
- Slow execution
- Critical paths only

## Backend Testing

### Unit Tests

#### Model Tests

Test models:
```php
// tests/Unit/Models/GroupTest.php
public function test_group_belongs_to_course()
{
    $course = Course::factory()->create();
    $group = Group::factory()->create(['course_id' => $course->id]);
    
    $this->assertEquals($course->id, $group->course->id);
}
```

#### Service Tests

Test services:
```php
// tests/Unit/Services/EnrollmentServiceTest.php
public function test_approve_enrollment()
{
    $enrollment = Enrollment::factory()->create(['status' => 'pending']);
    
    $this->enrollmentService->approveEnrollment($enrollment->id);
    
    $this->assertEquals('approved', $enrollment->fresh()->status);
}
```

### Feature Tests

#### API Tests

Test API endpoints:
```php
// tests/Feature/Api/GroupControllerTest.php
public function test_admin_can_create_group()
{
    $admin = User::factory()->admin()->create();
    
    $response = $this->actingAs($admin, 'api')
        ->postJson('/api/admin/groups', [
            'course_id' => 1,
            'code' => 'A',
            'capacity' => 20,
        ]);
    
    $response->assertStatus(201)
        ->assertJson(['success' => true]);
}
```

#### Authentication Tests

Test authentication:
```php
public function test_user_can_login()
{
    $user = User::factory()->create([
        'email' => 'test@example.com',
        'password' => Hash::make('password'),
    ]);
    
    $response = $this->postJson('/api/login', [
        'email' => 'test@example.com',
        'password' => 'password',
    ]);
    
    $response->assertStatus(200)
        ->assertJsonStructure(['token']);
}
```

## Frontend Testing

### Unit Tests

#### Component Tests

Test Vue components:
```javascript
// tests/unit/CourseForm.spec.js
import { mount } from '@vue/test-utils';
import CourseForm from '@/views/dashboard/admin/CourseForm.vue';

describe('CourseForm', () => {
  it('renders form fields', () => {
    const wrapper = mount(CourseForm);
    expect(wrapper.find('input[name="title"]').exists()).toBe(true);
  });
});
```

#### Composable Tests

Test composables:
```javascript
// tests/unit/useApi.spec.js
import { useApi } from '@/composables/useApi';

describe('useApi', () => {
  it('makes GET request', async () => {
    const { get } = useApi();
    const data = await get('/api/courses');
    expect(data).toBeDefined();
  });
});
```

### Integration Tests

#### Store Tests

Test Pinia stores:
```javascript
// tests/integration/stores/course.spec.js
import { setActivePinia, createPinia } from 'pinia';
import { useCourseStore } from '@/stores/course';

describe('Course Store', () => {
  beforeEach(() => {
    setActivePinia(createPinia());
  });
  
  it('fetches courses', async () => {
    const store = useCourseStore();
    await store.fetchAll();
    expect(store.courses.length).toBeGreaterThan(0);
  });
});
```

## E2E Testing

### Cypress Tests

#### Admin Flow

```javascript
// cypress/e2e/admin_flow.cy.js
describe('Admin Flow', () => {
  it('can create course', () => {
    cy.loginAsAdmin();
    cy.visit('/dashboard/admin/courses/new');
    cy.get('[data-e2e="course-title"]').type('Test Course');
    cy.get('[data-e2e="course-submit"]').click();
    cy.url().should('include', '/dashboard/admin/courses');
  });
});
```

#### Student Flow

```javascript
// cypress/e2e/student_flow.cy.js
describe('Student Flow', () => {
  it('can view courses', () => {
    cy.loginAsStudent();
    cy.visit('/dashboard/student/my-courses');
    cy.get('h1').should('contain', 'My Courses');
  });
});
```

## Test Coverage

### Coverage Goals

- **Unit Tests:** 80%+ coverage
- **Integration Tests:** 60%+ coverage
- **E2E Tests:** Critical paths 100%

### Coverage Reports

Generate reports:
```bash
# Backend
php artisan test --coverage

# Frontend
npm run test:coverage
```

## Test Data

### Factories

Use factories for test data:
```php
// database/factories/GroupFactory.php
Group::factory()->create([
    'course_id' => $course->id,
    'capacity' => 20,
]);
```

### Seeders

Use seeders for test data:
```bash
php artisan db:seed --class=TestDataSeeder
```

## Continuous Testing

### CI/CD Integration

Run tests on:
- Pull requests
- Commits to main
- Scheduled runs

### Test Automation

```yaml
# .github/workflows/tests.yml
name: Tests
on: [push, pull_request]
jobs:
  test:
    runs-on: ubuntu-latest
    steps:
      - uses: actions/checkout@v2
      - name: Run tests
        run: php artisan test
```

## Best Practices

1. **Write Tests First**
   - TDD approach
   - Test-driven development
   - Better design

2. **Keep Tests Fast**
   - Mock external services
   - Use in-memory database
   - Parallel execution

3. **Maintain Tests**
   - Update with code changes
   - Remove obsolete tests
   - Refactor test code

4. **Test Edge Cases**
   - Boundary conditions
   - Error scenarios
   - Invalid inputs

## Conclusion

Comprehensive testing ensures:
- Code quality
- Reliability
- Confidence in changes
- Faster development

A good testing strategy is essential for production systems.

