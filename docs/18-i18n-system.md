# 🌐 i18n System Documentation - Graphic School

## نظام الترجمة متعدد اللغات

هذا الملف يوثق نظام الترجمة الكامل في النظام.

---

## 📋 Overview

النظام يدعم الترجمة الديناميكية من قاعدة البيانات مع fallback للترجمات الثابتة.

---

## 🎯 Supported Languages

### Current Languages:
1. **Arabic (ar)** - اللغة العربية (افتراضي)
2. **English (en)** - الإنجليزية

### Adding New Languages:
يمكن إضافة لغات جديدة من خلال:
- Admin Panel → Translations
- Database → `languages` table

---

## 🔧 Implementation

### Frontend

#### Vue I18n Configuration
**Location**: `graphic-school-frontend/src/i18n/index.js`

**Features**:
- Vue I18n setup
- Static translations (fallback)
- Dynamic translations (from API)
- Locale persistence

#### Translation Files
**Location**: `graphic-school-frontend/src/i18n/locales/`

**Files**:
- `ar.json` - Arabic translations (408 lines)
- `en.json` - English translations (408 lines)

#### Translation Loader
**Location**: `graphic-school-frontend/src/i18n/loader.ts`

**Features**:
- Load translations from API
- Merge with static translations
- Cache translations

#### Language Switcher Component
**Location**: `graphic-school-frontend/src/components/common/LanguageSwitcher.vue`

**Features**:
- Switch between languages
- Persist selection
- Update UI immediately

### Backend

#### API Endpoints

**Get Translations**:
```
GET /api/translations
GET /api/translations/{group}
```

**Get Locale**:
```
GET /api/locale
```

**Set Locale**:
```
POST /api/locale/{locale}
```

**Get Available Locales**:
```
GET /api/locales
```

#### Database Tables

**translations**:
- `id`
- `group` - Translation group (e.g., 'common', 'admin')
- `key` - Translation key
- `locale` - Language code (ar, en)
- `value` - Translation value

**languages**:
- `id`
- `code` - Language code (ar, en)
- `name` - Language name
- `native_name` - Native name
- `is_active` - Active status
- `is_default` - Default language

---

## 📝 Usage

### In Vue Components

```vue
<template>
  <div>
    <h1>{{ $t('common.welcome') }}</h1>
    <p>{{ $t('admin.dashboard.title') }}</p>
  </div>
</template>

<script setup>
import { useI18n } from '../composables/useI18n';

const { t } = useI18n();
const message = t('common.save');
</script>
```

### Translation Keys Structure

```json
{
  "common": {
    "loading": "جاري التحميل...",
    "save": "حفظ",
    "cancel": "إلغاء"
  },
  "admin": {
    "dashboard": {
      "title": "لوحة التحكم",
      "welcome": "مرحباً"
    }
  }
}
```

---

## 🔄 Dynamic Translations

### Loading Process:
1. Load static translations (from JSON files)
2. Load dynamic translations (from API)
3. Merge translations (dynamic overrides static)
4. Apply to Vue I18n

### Caching:
- Translations cached in memory
- Locale persisted in localStorage
- API calls minimized

---

## 🎯 Translation Groups

### Common Groups:
- `common` - Common translations
- `auth` - Authentication
- `admin` - Admin panel
- `instructor` - Instructor panel
- `student` - Student panel
- `public` - Public site
- `setup` - Setup wizard

---

## 📊 Admin Panel

### Translations Management
**Route**: `/dashboard/admin/translations`

**Features**:
- List all translations
- Filter by group, locale
- Create new translation
- Edit translation
- Delete translation
- Search translations

### Translation Form
**Route**: `/dashboard/admin/translations/new` or `/dashboard/admin/translations/:id/edit`

**Fields**:
- Group
- Key
- Locale
- Value

---

## 🔍 Best Practices

1. **Use Translation Keys**: Always use translation keys, never hardcode text
2. **Group Translations**: Organize translations by feature/component
3. **Fallback**: Always provide fallback translations
4. **Consistency**: Use consistent key naming
5. **Context**: Provide context in translation values

---

## 🐛 Troubleshooting

### Missing Translations:
- Check if key exists in JSON files
- Check if key exists in database
- Check locale is set correctly

### Translation Not Updating:
- Clear browser cache
- Check API response
- Verify locale is correct

---

**آخر تحديث**: 2025-01-27  
**الإصدار**: 1.0.0

