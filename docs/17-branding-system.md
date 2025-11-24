# 🎨 Branding System Documentation - Graphic School

## نظام العلامة التجارية

هذا الملف يوثق نظام العلامة التجارية الكامل في النظام.

---

## 📋 Overview

نظام العلامة التجارية يسمح للأدمن بتخصيص مظهر الموقع بالكامل من خلال لوحة التحكم.

---

## 🎯 Features

### 1. Logo Management
- **Main Logo**: Logo الرئيسي للموقع
- **Favicon**: أيقونة المتصفح
- **Upload**: رفع الصور
- **Preview**: معاينة قبل الحفظ

### 2. Color Customization
- **Primary Color**: اللون الأساسي
- **Secondary Color**: اللون الثانوي
- **Background Color**: لون الخلفية
- **Text Color**: لون النص
- **Color Picker**: أداة اختيار الألوان

### 3. Font Customization
- **Main Font**: خط النصوص الرئيسية
- **Headings Font**: خط العناوين
- **Font Source**: 
  - System fonts (Google Fonts)
  - Custom fonts (upload)
- **Font Loading**: تحميل تلقائي للخطوط

### 4. Layout Customization
- **Border Radius**: درجة استدارة الزوايا
- **Shadow Level**: مستوى الظلال
- **Spacing**: المسافات

---

## 🔧 Implementation

### Frontend

#### BrandingStore
**Location**: `graphic-school-frontend/src/stores/branding.js`

**Functions**:
- `fetchBranding()` - جلب إعدادات العلامة التجارية
- `applyBrandingToDOM()` - تطبيق العلامة التجارية على DOM
- `loadFont()` - تحميل الخطوط

#### BrandingEditor Component
**Location**: `graphic-school-frontend/src/views/dashboard/admin/BrandingEditor.vue`

**Features**:
- Logo upload
- Color picker
- Font selector
- Live preview
- Save/Cancel buttons

### Backend

#### API Endpoint
**Route**: `GET /api/branding/frontend`

**Response Format**:
```json
{
  "success": true,
  "data": {
    "branding.name.display": "Graphic School",
    "branding.logo.main": "/storage/logos/main.png",
    "branding.logo.favicon": "/storage/logos/favicon.ico",
    "branding.colors.primary": "#3b82f6",
    "branding.colors.secondary": "#6366f1",
    "branding.fonts.main": "Inter",
    "branding.fonts.headings": "Poppins",
    "branding.fonts.source": "system"
  }
}
```

#### Controller
**Location**: `graphic-school-api/app/Http/Controllers/Admin/BrandingController.php`

---

## 🎨 CSS Variables

النظام يستخدم CSS Variables لتطبيق العلامة التجارية:

```css
:root {
  --primary: #3b82f6;
  --secondary: #6366f1;
  --background: #ffffff;
  --text-color: #1f2937;
  --font-main: "Inter", sans-serif;
  --font-headings: "Poppins", sans-serif;
  --radius: 0.5rem;
  --shadow-level: medium;
}
```

---

## 📝 Usage

### In Components

```vue
<template>
  <div class="bg-primary text-white">
    <h1 class="font-headings">Title</h1>
    <p class="font-main">Content</p>
  </div>
</template>
```

### In Tailwind Config

```js
theme: {
  extend: {
    colors: {
      primary: 'var(--primary)',
      secondary: 'var(--secondary)',
    },
    fontFamily: {
      main: 'var(--font-main)',
      headings: 'var(--font-headings)',
    },
  },
}
```

---

## 🔄 Dynamic Application

العلامة التجارية تُطبق ديناميكياً عند:
1. تحميل الصفحة
2. تغيير الإعدادات
3. تحديث المتصفح

---

## 📱 Responsive

العلامة التجارية تعمل على جميع الأجهزة:
- Desktop
- Tablet
- Mobile

---

**آخر تحديث**: 2025-01-27  
**الإصدار**: 1.0.0

