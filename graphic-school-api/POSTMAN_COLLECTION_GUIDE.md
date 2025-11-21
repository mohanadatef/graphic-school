# 📮 Postman Collection Guide - Graphic School API

## 📥 كيفية الاستخدام

### 1. استيراد Collection
1. افتح Postman
2. اضغط على **Import**
3. اختر ملف `postman_collection.json`
4. Collection سيتم استيراده مع جميع الـ Requests

### 2. إعداد المتغيرات
1. افتح Collection Settings
2. في تبويب **Variables**:
   - `base_url`: `http://localhost:8000/api` (أو URL الخاص بك)
   - `auth_token`: سيتم ملؤه تلقائياً بعد Login

### 3. الحصول على Token
1. استخدم **Register** أو **Login** Request
2. انسخ الـ `token` من Response
3. الصقه في متغير `auth_token` في Collection Variables

---

## 📋 المجموعات (Folders)

### 1. Authentication
- **Register**: تسجيل مستخدم جديد (student)
- **Login**: تسجيل الدخول والحصول على Token
- **Logout**: تسجيل الخروج

### 2. Public
- **Home Summary**: ملخص الصفحة الرئيسية
- **Courses**: قائمة الكورسات العامة
- **Course Details**: تفاصيل كورس معين
- **Categories**: قائمة الفئات
- **Instructors**: قائمة المدربين
- **Contact**: إرسال رسالة تواصل

### 3. Admin
جميع الـ Requests تحتاج **Bearer Token** مع **role:admin**

#### Users
- **List Users**: قائمة المستخدمين (مع pagination)
- **Create User**: إنشاء مستخدم جديد
- **Update User**: تحديث مستخدم
- **Delete User**: حذف مستخدم

#### Roles
- **List Roles**: قائمة الأدوار
- **Create Role**: إنشاء دور جديد

#### Categories
- **List Categories**: قائمة الفئات
- **Create Category**: إنشاء فئة جديدة

#### Courses
- **List Courses**: قائمة الكورسات (مع pagination + filters)
- **Create Course**: إنشاء كورس جديد
- **Assign Instructors**: تعيين مدربين للكورس
- **Generate Sessions**: توليد جلسات للكورس

#### Sessions
- **List Sessions**: قائمة الجلسات

#### Enrollments
- **List Enrollments**: قائمة التسجيلات
- **Create Enrollment**: إنشاء تسجيل جديد

#### Settings
- **Get Settings**: الحصول على الإعدادات
- **Update Settings**: تحديث الإعدادات

#### Reports
- **Courses Report**: تقرير الكورسات
- **Instructors Report**: تقرير المدربين
- **Financial Report**: التقرير المالي

### 4. Student
جميع الـ Requests تحتاج **Bearer Token** مع **role:student**

- **My Courses**: الكورسات المسجل فيها
- **Enroll in Course**: التسجيل في كورس
- **Course Sessions**: جلسات كورس معين
- **Profile**: الملف الشخصي

### 5. Instructor
جميع الـ Requests تحتاج **Bearer Token** مع **role:instructor**

- **My Courses**: الكورسات الخاصة بالمدرب
- **Store Attendance**: تسجيل الحضور

### 6. System
- **Health Check**: فحص صحة النظام
- **File Upload**: رفع ملف
- **Export Data**: تصدير بيانات (Excel/PDF/CSV)

---

## 🔑 Authentication

### طريقة 1: Manual
1. استخدم **Login** Request
2. انسخ الـ `token` من Response
3. الصقه في Collection Variables → `auth_token`

### طريقة 2: Automatic (Script)
يمكن إضافة Test Script في **Login** Request:
```javascript
if (pm.response.code === 200) {
    const jsonData = pm.response.json();
    pm.collectionVariables.set("auth_token", jsonData.data.token);
}
```

---

## 📊 Response Format

جميع الـ APIs ترجع نفس الصيغة:

```json
{
  "success": true,
  "message": "Success message",
  "data": {},
  "errors": null,
  "status": 200,
  "meta": {
    "pagination": {
      "current_page": 1,
      "per_page": 15,
      "total": 100
    }
  }
}
```

---

## 🎯 أمثلة على الاستخدام

### 1. تسجيل مستخدم جديد
```
POST /api/register
Body: {
  "name": "Test User",
  "email": "test@example.com",
  "password": "password123",
  "password_confirmation": "password123",
  "phone": "1234567890",
  "address": "Test Address"
}
```

### 2. تسجيل الدخول
```
POST /api/login
Body: {
  "email": "test@example.com",
  "password": "password123"
}
```

### 3. إنشاء كورس (Admin)
```
POST /api/admin/courses
Headers: Authorization: Bearer {token}
Body: {
  "title": "New Course",
  "code": "NC001",
  "category_id": 1,
  "price": 1000,
  "session_count": 10
}
```

---

## 📝 ملاحظات

1. **Base URL**: تأكد من تحديث `base_url` في Collection Variables
2. **Token**: يجب تحديث `auth_token` بعد كل Login
3. **Roles**: بعض الـ Endpoints تحتاج رول معين (admin, instructor, student)
4. **Pagination**: معظم List APIs تدعم pagination عبر `page` و `per_page`
5. **Filters**: معظم List APIs تدعم filters (search, status, category_id, etc.)

---

**Ready to use!** 🚀

