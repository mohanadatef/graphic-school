# Roles and Permissions

## Overview

Graphic School 2.0 uses a role-based access control (RBAC) system with fine-grained permissions. This document explains the roles, permissions, and access control implementation.

## Role Structure

### Roles

#### Admin
- Full system access
- All administrative functions
- User management
- System configuration

#### Instructor
- Course management (assigned courses)
- Group management (assigned groups)
- Attendance management
- Student management (assigned groups)

#### Student
- View enrolled courses
- View assigned group
- View sessions
- View attendance history
- Submit assignments

## Permission System

### Permission Structure

Permissions are organized by resource:
- `courses.*` - Course management
- `groups.*` - Group management
- `sessions.*` - Session management
- `enrollments.*` - Enrollment management
- `attendance.*` - Attendance management
- `users.*` - User management
- `roles.*` - Role management
- `pages.*` - CMS page management
- `settings.*` - System settings

### Permission Actions

- `view` - View resource
- `create` - Create resource
- `update` - Update resource
- `delete` - Delete resource
- `manage` - Full management

## Default Permissions

### Admin Permissions

Admin has all permissions:
- `courses.*`
- `groups.*`
- `sessions.*`
- `enrollments.*`
- `attendance.*`
- `users.*`
- `roles.*`
- `pages.*`
- `settings.*`

### Instructor Permissions

Instructor has limited permissions:
- `courses.view` (assigned courses)
- `groups.view` (assigned groups)
- `groups.update` (assigned groups)
- `sessions.view` (assigned groups)
- `sessions.create` (assigned groups)
- `sessions.update` (assigned groups)
- `attendance.view` (assigned groups)
- `attendance.create` (assigned groups)
- `attendance.update` (assigned groups)
- `users.view` (assigned students)

### Student Permissions

Student has read-only permissions:
- `courses.view` (enrolled courses)
- `groups.view` (assigned group)
- `sessions.view` (assigned group)
- `attendance.view` (own attendance)

## Permission Implementation

### Middleware

Role middleware:
```php
Route::middleware('role:admin')->group(function () {
    // Admin routes
});
```

Permission middleware:
```php
Route::middleware('permission:courses.create')->group(function () {
    // Routes requiring permission
});
```

### Frontend Guards

Route guards:
```javascript
{
  path: 'admin/courses',
  meta: { middleware: [authMiddleware, roleMiddleware('admin')] }
}
```

Component guards:
```vue
<script setup>
import { useAuthStore } from '@/stores/auth';

const authStore = useAuthStore();

if (!authStore.hasPermission('courses.create')) {
  router.push('/unauthorized');
}
</script>
```

## Access Control Rules

### Course Access

- **Admin:** All courses
- **Instructor:** Assigned courses only
- **Student:** Enrolled courses only

### Group Access

- **Admin:** All groups
- **Instructor:** Assigned groups only
- **Student:** Assigned group only

### Enrollment Access

- **Admin:** All enrollments
- **Instructor:** Enrollments in assigned groups
- **Student:** Own enrollments only

### Attendance Access

- **Admin:** All attendance
- **Instructor:** Attendance in assigned groups
- **Student:** Own attendance only

## Permission Checks

### Backend Checks

Controller level:
```php
public function store(Request $request)
{
    $this->authorize('courses.create');
    // Create course
}
```

Service level:
```php
public function createCourse($data)
{
    if (!auth()->user()->can('courses.create')) {
        throw new UnauthorizedException();
    }
    // Create course
}
```

### Frontend Checks

Component level:
```vue
<template>
  <button v-if="canCreate" @click="create">Create</button>
</template>

<script setup>
const canCreate = computed(() => 
  authStore.hasPermission('courses.create')
);
</script>
```

## Custom Permissions

### Creating Permissions

```php
Permission::create([
    'name' => 'courses.publish',
    'slug' => 'courses.publish',
    'description' => 'Publish courses',
]);
```

### Assigning Permissions

```php
$role->permissions()->attach($permissionId);
```

## Permission Inheritance

### Role Hierarchy

Roles can inherit permissions:
- Admin inherits all permissions
- Custom roles can inherit from base roles

## Best Practices

1. **Principle of Least Privilege**
   - Grant minimum necessary permissions
   - Review permissions regularly
   - Remove unused permissions

2. **Explicit Permissions**
   - Check permissions explicitly
   - Don't rely on role alone
   - Use permission checks

3. **Consistent Naming**
   - Use resource.action format
   - Keep naming consistent
   - Document permissions

4. **Regular Audits**
   - Review permissions quarterly
   - Remove unused permissions
   - Update as needed

## Conclusion

The roles and permissions system provides:
- Flexible access control
- Fine-grained permissions
- Role-based access
- Security enforcement

Proper permission management ensures users have appropriate access to system resources.

