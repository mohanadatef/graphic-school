# Frontend Testing Guide

This directory contains all frontend tests for the Graphic School LMS application.

## Test Setup

The project uses:
- **Vitest** - Fast unit test framework
- **Vue Test Utils** - Official Vue.js testing utilities
- **MSW (Mock Service Worker)** - API mocking for tests
- **Happy DOM** - Lightweight DOM implementation for testing

## Running Tests

```bash
# Run tests in watch mode
npm run test

# Run tests with UI
npm run test:ui

# Run tests once
npm run test:run

# Run tests with coverage
npm run test:coverage
```

## Test Structure

```
tests/
├── setup.js                 # Test setup and global mocks
├── mocks/
│   └── handlers.js          # MSW API mock handlers
├── utils/
│   └── test-utils.js        # Test utility functions
├── stores/                  # Store tests
├── services/                # Service tests
├── composables/             # Composable tests
├── components/              # Component tests
├── utils/                   # Utility function tests
└── views/                   # View component tests
```

## Writing Tests

### Component Tests

```javascript
import { describe, it, expect } from 'vitest';
import { mountWithPlugins } from '../utils/test-utils';
import MyComponent from '../../src/components/MyComponent.vue';

describe('MyComponent', () => {
  it('should render correctly', () => {
    const wrapper = mountWithPlugins(MyComponent, {
      props: {
        title: 'Test Title',
      },
    });

    expect(wrapper.text()).toContain('Test Title');
  });
});
```

### Store Tests

```javascript
import { describe, it, expect, beforeEach } from 'vitest';
import { setActivePinia, createPinia } from 'pinia';
import { useMyStore } from '../../src/stores/myStore';

describe('MyStore', () => {
  beforeEach(() => {
    setActivePinia(createPinia());
  });

  it('should initialize with default state', () => {
    const store = useMyStore();
    expect(store.items).toEqual([]);
  });
});
```

### Service Tests

```javascript
import { describe, it, expect, vi } from 'vitest';
import { myService } from '../../src/services/myService';
import api from '../../src/services/api/client';

vi.mock('../../src/services/api/client');

describe('MyService', () => {
  it('should call API correctly', async () => {
    api.get.mockResolvedValue({ data: { id: 1 } });
    const result = await myService.getData();
    expect(api.get).toHaveBeenCalledWith('/endpoint');
    expect(result).toEqual({ id: 1 });
  });
});
```

### Composable Tests

```javascript
import { describe, it, expect } from 'vitest';
import { useMyComposable } from '../../src/composables/useMyComposable';

describe('useMyComposable', () => {
  it('should return correct values', () => {
    const { value, increment } = useMyComposable();
    expect(value.value).toBe(0);
    increment();
    expect(value.value).toBe(1);
  });
});
```

## API Mocking with MSW

MSW handlers are defined in `tests/mocks/handlers.js`. To add new handlers:

```javascript
import { http, HttpResponse } from 'msw';

export const handlers = [
  http.get('/api/users', () => {
    return HttpResponse.json({
      success: true,
      data: [{ id: 1, name: 'Test User' }],
    });
  }),
];
```

## Snapshot Testing

Snapshot tests help ensure UI doesn't change unexpectedly:

```javascript
it('should match snapshot', () => {
  const wrapper = mountWithPlugins(MyComponent);
  expect(wrapper.html()).toMatchSnapshot();
});
```

Update snapshots with: `npm run test:run -- -u`

## Best Practices

1. **Test behavior, not implementation** - Focus on what the component does, not how
2. **Use descriptive test names** - "should display error message when API fails"
3. **Keep tests isolated** - Each test should be independent
4. **Mock external dependencies** - Mock API calls, localStorage, etc.
5. **Test edge cases** - Empty states, error states, loading states
6. **Use test utilities** - Leverage `mountWithPlugins` and other helpers

## Coverage Goals

- **Stores**: 80%+ coverage
- **Services**: 80%+ coverage
- **Composables**: 80%+ coverage
- **Components**: 70%+ coverage
- **Utils**: 90%+ coverage

## Troubleshooting

### Tests failing with "Cannot find module"
- Ensure all dependencies are installed: `npm install`
- Check that file paths are correct

### MSW not intercepting requests
- Ensure `server.listen()` is called in `setup.js`
- Check that handlers are properly exported

### Component not rendering
- Check that all required props are provided
- Ensure plugins (Pinia, i18n, router) are set up correctly
- Use `mountWithPlugins` helper

## Examples

See existing test files for examples:
- `tests/stores/auth.test.js` - Store testing
- `tests/services/authService.test.js` - Service testing
- `tests/components/FilterDropdown.test.js` - Component testing
- `tests/utils/validation.test.js` - Utility testing

