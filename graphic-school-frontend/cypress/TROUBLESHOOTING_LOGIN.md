# Troubleshooting Login Issues in Cypress

## المشكلة: "Timed out retrying after 15000ms: expected URL to satisfy"

هذه المشكلة تحدث عندما:
1. تسجيل الدخول لا ينجح (API error)
2. URL لا يتغير بعد تسجيل الدخول
3. البيانات غير صحيحة

## الحلول

### 1. تحقق من أن Backend يعمل

```powershell
curl http://graphic-school.test/api/health
```

يجب أن ترى استجابة JSON.

### 2. تحقق من بيانات المستخدمين

تأكد من أن المستخدمين موجودين في قاعدة البيانات:

```powershell
cd graphic-school-api
php artisan tinker
```

```php
User::where('email', 'admin@example.com')->first();
User::where('email', 'instructor@example.com')->first();
User::where('email', 'student@example.com')->first();
```

إذا لم تكن موجودة:
```powershell
php artisan db:seed
```

### 3. استخدم الأوامر المبسطة

إذا استمرت المشكلة، استخدم الأوامر المبسطة:

في `cypress/support/e2e.js`، أزل التعليق من:
```javascript
import './login-simple';
```

ثم في الاختبارات، استخدم:
```javascript
cy.loginAsAdminSimple(); // بدلاً من cy.loginAsAdmin()
```

### 4. تحقق من Console في المتصفح

في Cypress UI:
1. افتح DevTools (F12)
2. اذهب إلى Console
3. ابحث عن أخطاء API
4. ابحث عن أخطاء JavaScript

### 5. تحقق من Network Tab

في DevTools:
1. اذهب إلى Network tab
2. حاول تسجيل الدخول
3. ابحث عن طلب `/api/login` أو `/api/v1/login`
4. تحقق من:
   - Status code (يجب أن يكون 200)
   - Response (يجب أن يحتوي على user و token)

### 6. تحقق من CORS

إذا رأيت أخطاء CORS:
- تأكد من أن `FRONTEND_URL` في `.env` صحيح
- تأكد من أن `SANCTUM_STATEFUL_DOMAINS` يحتوي على `localhost:5175`

### 7. اختبار يدوي

جرب تسجيل الدخول يدوياً في المتصفح:
1. افتح `http://localhost:5175/login`
2. سجل دخول باستخدام:
   - Email: `admin@example.com`
   - Password: `password`
3. إذا نجح، المشكلة في Cypress
4. إذا فشل، المشكلة في Backend أو البيانات

## حل سريع

إذا كنت تريد تجاوز المشكلة مؤقتاً:

```javascript
// في الاختبار
cy.visit('/login');
cy.get('#email').type('admin@example.com');
cy.get('#password').type('password');
cy.get('button[type="submit"]').click();
cy.wait(8000); // انتظر أطول
cy.url().should('not.include', '/login'); // فحص بسيط
```

## أسباب شائعة

1. **API غير متاح**: Backend لا يعمل
2. **بيانات خاطئة**: المستخدم غير موجود أو كلمة المرور خاطئة
3. **CORS**: مشكلة في إعدادات CORS
4. **Timeout قصير**: API بطيء، يحتاج وقت أطول
5. **خطأ في API**: Backend يرجح خطأ (401, 500, etc.)

## Debug Commands

أضف هذه الأوامر في الاختبار للتصحيح:

```javascript
cy.visit('/login');
cy.wait(3000);

// Log current URL
cy.url().then(url => cy.log('Current URL:', url));

// Check for errors
cy.get('body').then($body => {
  const errors = $body.find('.error, [role="alert"]');
  if (errors.length > 0) {
    cy.log('Errors found:', errors.text());
  }
});

// Try login
cy.get('#email').type('admin@example.com');
cy.get('#password').type('password');
cy.get('button[type="submit"]').click();

// Wait and log URL again
cy.wait(5000);
cy.url().then(url => cy.log('URL after login:', url));
```

