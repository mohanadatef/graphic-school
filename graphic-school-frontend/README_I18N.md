# Multi-Language Support (i18n)

This frontend application supports multiple languages (English and Arabic) using Vue I18n.

## Setup

1. **Install dependencies:**
   ```bash
   npm install
   ```

2. **The application will automatically:**
   - Detect the saved language preference from localStorage
   - Set the document direction (RTL for Arabic, LTR for English)
   - Send the language preference to the backend API

## Usage

### Using Translations in Components

```vue
<template>
  <div>
    <h1>{{ $t('auth.login') }}</h1>
    <p>{{ $t('messages.success') }}</p>
  </div>
</template>
```

### Programmatic Translation

```javascript
import { useI18n } from 'vue-i18n';

const { t } = useI18n();
const message = t('auth.login');
```

### Language Switcher

The `LanguageSwitcher` component is already integrated into:
- `PublicLayout` - Public site header
- `DashboardLayout` - Dashboard header

### Changing Language

Users can change language using the language switcher component, or programmatically:

```javascript
import { useI18n } from './composables/useI18n';

const { setLocale } = useI18n();
await setLocale('en'); // or 'ar'
```

## Translation Files

Translations are stored in:
- `src/i18n/locales/en.json` - English translations
- `src/i18n/locales/ar.json` - Arabic translations

## Adding New Translations

1. Add the translation key to both `en.json` and `ar.json`:

**en.json:**
```json
{
  "mySection": {
    "myKey": "My English Text"
  }
}
```

**ar.json:**
```json
{
  "mySection": {
    "myKey": "نصي بالعربية"
  }
}
```

2. Use in components:
```vue
{{ $t('mySection.myKey') }}
```

## Backend Integration

The frontend automatically:
- Sends `Accept-Language` header with API requests
- Syncs language preference with backend via `/api/locale/{locale}` endpoint
- Stores language preference in localStorage

## RTL Support

Arabic language automatically enables RTL (Right-to-Left) layout:
- Document direction is set to `rtl`
- CSS classes adjust for RTL layout
- Flexbox directions are reversed

## Current Translations

The following sections are translated:
- `common` - Common UI elements (buttons, actions)
- `auth` - Authentication (login, register, logout)
- `navigation` - Navigation items
- `course` - Course-related terms
- `dashboard` - Dashboard terms
- `student` - Student-specific terms
- `instructor` - Instructor-specific terms
- `admin` - Admin-specific terms
- `language` - Language switcher labels
- `messages` - General messages (success, error, etc.)

