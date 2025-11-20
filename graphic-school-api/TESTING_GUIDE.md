# 🧪 Testing Guide - Graphic School API

## 📋 كيفية تشغيل Tests

### 1. تشغيل جميع Tests
```bash
php artisan test
```

### 2. تشغيل Unit Tests فقط
```bash
php artisan test --testsuite=Unit
```

### 3. تشغيل Feature Tests فقط
```bash
php artisan test --testsuite=Feature
```

### 4. تشغيل Test معين
```bash
php artisan test --filter AuthTest
php artisan test --filter UserRepositoryTest
```

### 5. تشغيل Tests مع Coverage
```bash
php artisan test --coverage
```

---

## 📝 Tests Created

### Unit Tests:
1. **UserRepositoryTest**
   - Test create user
   - Test find user by ID
   - Test find user by email
   - Test update user
   - Test delete user

2. **AuthUseCaseTest**
   - Test user registration

### Feature Tests:
1. **AuthTest**
   - Test register endpoint
   - Test login endpoint
   - Test logout endpoint

2. **CoursesTest**
   - Test list courses
   - Test create course
   - Test update course
   - Test delete course

3. **HealthCheckTest**
   - Test health check endpoint

---

## 🔧 Factories

### Available Factories:
- `User::factory()` - Create user
- `User::factory()->admin()` - Create admin user
- `User::factory()->instructor()` - Create instructor user
- `User::factory()->student()` - Create student user
- `Role::factory()` - Create role
- `Course::factory()` - Create course
- `Category::factory()` - Create category

### Example Usage:
```php
$user = User::factory()->create();
$admin = User::factory()->admin()->create();
$course = Course::factory()->create();
```

---

## ✅ Test Status

- ✅ Unit Tests: Created
- ✅ Feature Tests: Created
- ✅ Factories: Created
- ✅ PHPUnit Config: Updated

---

**Ready for Testing!** 🧪

