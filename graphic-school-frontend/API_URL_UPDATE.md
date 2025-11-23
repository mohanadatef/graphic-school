# API URL Update Summary

تم تحديث جميع المراجع من `http://localhost:8000` إلى `http://graphic-school.test`

## ✅ الملفات المحدثة

### 1. Cypress Configuration
- **`cypress.env.json`**: تم تحديث `apiUrl` إلى `http://graphic-school.test/api`

### 2. Documentation
- **`CYPRESS_TROUBLESHOOTING.md`**: تم تحديث جميع الأمثلة والمراجع
- **`cypress/README.md`**: تم تحديث التعليمات

## ✅ الملفات التي تستخدم بالفعل graphic-school.test

هذه الملفات كانت تستخدم `graphic-school.test` بالفعل:

- **`src/services/api/client.js`**: 
  ```javascript
  baseURL: import.meta.env.VITE_API_URL || 'http://graphic-school.test/api'
  ```

- **`src/api.js`**: 
  ```javascript
  baseURL: import.meta.env.VITE_API_URL || 'http://graphic-school.test/api'
  ```

## 📝 ملاحظات

### GitHub Actions Workflow
ملف `.github/workflows/e2e.yml` لا يزال يستخدم `localhost:8000` لأنه يعمل في بيئة CI/CD مختلفة حيث لا يمكن استخدام `graphic-school.test`. هذا صحيح ولا يحتاج تغيير.

### Environment Variables
إذا كنت تريد استخدام URL مختلف، يمكنك تعيين:
```env
VITE_API_URL=http://graphic-school.test/api
```

في ملف `.env.local` أو `.env` في مجلد `graphic-school-frontend`.

## ✅ التحقق

للتحقق من أن كل شيء يعمل:

```powershell
# تحقق من أن API متاح
curl http://graphic-school.test/api/health

# يجب أن ترى استجابة JSON
```

## 🎯 النتيجة

الآن جميع ملفات Cypress والوثائق تستخدم `http://graphic-school.test/api` بدلاً من `http://localhost:8000/api`.

