# Unified API Response Format - Frontend Integration

## Overview
The backend now returns all API responses in a unified format. The frontend has been updated to automatically handle this format through axios interceptors.

## Backend Response Format

All API responses follow this structure:

```json
{
  "success": true,
  "message": "Operation completed successfully",
  "data": { ... } | [ ... ],
  "errors": null,
  "status": 200,
  "meta": {
    "pagination": {
      "current_page": 1,
      "per_page": 10,
      "total": 100,
      "last_page": 10,
      "from": 1,
      "to": 10
    }
  }
}
```

## Frontend Handling

### Automatic Extraction
The axios interceptor in `src/services/api/client.js` automatically:
1. Extracts `data` from the unified response
2. Attaches `meta` to the response object if it exists
3. Handles errors in unified format

### Usage in Services

Services can now use responses directly:

```javascript
// Before (old format)
const { data } = await api.get('/courses');
const courses = data.data || [];

// After (unified format - handled by interceptor)
const response = await api.get('/courses');
const courses = Array.isArray(response) ? response : (response.data || []);
const pagination = response.meta?.pagination;
```

### Usage in Stores

Stores handle the extracted data:

```javascript
async function fetchAll(params = {}) {
  const response = await courseService.getAll(params);
  // Interceptor already extracted data
  const data = Array.isArray(response) ? response : (response.data || []);
  courses.value = Array.isArray(data) ? data : [];
  
  // Pagination is attached to response.meta
  if (response.meta?.pagination) {
    pagination.value = response.meta.pagination;
  }
}
```

### Error Handling

Errors also follow unified format:

```json
{
  "success": false,
  "message": "Validation failed",
  "errors": {
    "email": ["The email field is required."],
    "password": ["The password must be at least 8 characters."]
  },
  "status": 422
}
```

The error handler automatically extracts the message and errors.

## Migration Notes

- All services have been updated to work with the unified format
- All stores have been updated to extract data correctly
- The interceptor handles extraction automatically
- Backward compatibility is maintained where possible

## Testing

To test the integration:
1. Check that login/register work correctly
2. Verify that course lists display with pagination
3. Confirm that error messages display correctly
4. Test that all CRUD operations work as expected

