# Understanding the 401 Login Error

## Overview

A **401 Unauthorized** error occurs when the login request fails due to invalid credentials or authentication issues. This document explains what causes this error and how to debug it.

## Error Flow

### Frontend Flow
1. User submits login form (`LoginPage.vue`)
2. Form calls `authStore.login()` with email and password
3. Auth store calls `authService.login()` which makes a POST request to `/api/login`
4. API client (`client.js`) sends the request with proper headers
5. Backend validates credentials and returns 401 if invalid

### Backend Flow
1. Request arrives at `AuthController::login()`
2. `LoginRequest` validates the input (email and password required)
3. `LoginUserUseCase` executes:
   - Finds user by email
   - Checks if password matches using `PasswordHasherInterface`
   - If user not found OR password doesn't match → throws `DomainException` with 401 status
   - If user is inactive → throws `DomainException` with 403 status
4. Exception is caught by `Handler` and formatted as unified API response

## Common Causes

### 1. Invalid Credentials (Most Common)
- **Symptom**: 401 error with message "Invalid credentials"
- **Cause**: Email doesn't exist in database OR password is incorrect
- **Solution**: 
  - Verify the email address is correct
  - Check if the user exists in the database
  - Verify the password is correct (check password hash in database)

### 2. User Not Found
- **Symptom**: 401 error
- **Cause**: Email doesn't exist in the `users` table
- **Solution**: Create the user or use a different email

### 3. Password Hash Mismatch
- **Symptom**: 401 error even with correct password
- **Cause**: Password in database is not properly hashed or uses different algorithm
- **Solution**: 
  - Check if password is hashed using Laravel's `Hash::make()` or `bcrypt()`
  - Re-hash the password if needed: `Hash::make('password')`

### 4. Account Inactive
- **Symptom**: 403 error (not 401)
- **Cause**: User's `is_active` field is `false`
- **Solution**: Set `is_active = true` in the database

### 5. API Configuration Issues
- **Symptom**: 401 or network errors
- **Causes**:
  - Wrong API base URL (`VITE_API_BASE_URL` environment variable)
  - CORS issues (though this would show different errors)
  - Backend not running
- **Solution**: 
  - Check `.env` file for `VITE_API_BASE_URL`
  - Verify backend is running and accessible
  - Check browser console for CORS errors

## Error Response Format

The backend returns errors in a unified format:

```json
{
  "success": false,
  "message": "Invalid credentials",
  "errors": {
    "email": "Invalid email or password"
  },
  "status": 401
}
```

The frontend extracts the message and displays it to the user.

## Debugging Steps

### 1. Check Browser Console
- Open browser DevTools (F12)
- Go to Console tab
- Look for the error details
- Check Network tab to see the actual request/response

### 2. Check Backend Logs
- Look in `storage/logs/laravel.log`
- Search for "Invalid login credentials" or "Login attempt"
- Check the email address being used

### 3. Verify Database
```sql
-- Check if user exists
SELECT id, email, is_active FROM users WHERE email = 'user@example.com';

-- Check password hash (should be bcrypt hash)
SELECT password FROM users WHERE email = 'user@example.com';
```

### 4. Test Password Hash
In Laravel Tinker:
```php
php artisan tinker
Hash::check('your-password', $user->password); // Should return true
```

### 5. Check API Configuration
- Verify `VITE_API_BASE_URL` in frontend `.env` file
- Default is: `http://graphic-school.test/api`
- For local development: `http://localhost:8000/api` or your backend URL

### 6. Test with Postman/curl
```bash
curl -X POST http://graphic-school.test/api/login \
  -H "Content-Type: application/json" \
  -H "Accept-Language: en" \
  -d '{"email":"user@example.com","password":"password"}'
```

## Code Locations

### Frontend
- **Login Form**: `graphic-school-frontend/src/views/public/LoginPage.vue`
- **Auth Store**: `graphic-school-frontend/src/stores/auth.js`
- **Auth Service**: `graphic-school-frontend/src/services/api/authService.js`
- **API Client**: `graphic-school-frontend/src/services/api/client.js`

### Backend
- **Auth Controller**: `graphic-school-api/Modules/ACL/Auth/Http/Controllers/AuthController.php`
- **Login Use Case**: `graphic-school-api/Modules/ACL/Auth/Application/UseCases/LoginUserUseCase.php`
- **Login Request**: `graphic-school-api/Modules/ACL/Auth/Http/Requests/LoginRequest.php`
- **Exception Handler**: `graphic-school-api/app/Exceptions/Handler.php`

## Recent Improvements

The error handling has been improved to:
1. Better extract error messages from the unified API response format
2. Display field-specific errors (e.g., email errors)
3. Provide fallback messages if API response is malformed
4. Log errors for debugging while maintaining user-friendly messages

## Testing Valid Login

To test a successful login, ensure:
1. User exists in database with `is_active = true`
2. Password is correctly hashed using Laravel's Hash
3. Email and password are sent correctly in the request
4. API endpoint is accessible and CORS is configured

Example test user creation:
```php
php artisan tinker
$user = \Modules\ACL\Users\Models\User::create([
    'name' => 'Test User',
    'email' => 'test@example.com',
    'password' => Hash::make('password123'),
    'is_active' => true,
    'role_id' => 3, // Student role
]);
```

## Related Issues

- **403 Forbidden**: Account is disabled (`is_active = false`)
- **422 Validation Error**: Missing or invalid email/password format
- **500 Server Error**: Database connection or other server issues
- **Network Error**: Backend not running or CORS misconfiguration

