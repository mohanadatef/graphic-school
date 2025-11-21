# 🔄 System Flows - Graphic School

## تدفقات النظام الرئيسية

---

## Flow 1: تسجيل مستخدم جديد

```
User
  ↓
[فتح صفحة التسجيل]
  ↓
[ملء النموذج: اسم، بريد، كلمة مرور]
  ↓
[Submit Form]
  ↓
Frontend → [POST /api/register]
  ↓
Backend → [RegisterUserUseCase]
  ↓
[Validation]
  ├─ Success → [Create User]
  │              ↓
  │         [Assign Role: student]
  │              ↓
  │         [Generate Token]
  │              ↓
  │         [Return User + Token]
  │              ↓
  │         Frontend → [Save Token]
  │              ↓
  │         [Redirect to Dashboard]
  │
  └─ Error → [Return Validation Errors]
              ↓
         Frontend → [Display Errors]
```

---

## Flow 2: تسجيل الدخول

```
User
  ↓
[فتح صفحة تسجيل الدخول]
  ↓
[إدخال البريد وكلمة المرور]
  ↓
[Submit]
  ↓
Frontend → [POST /api/login]
  ↓
Backend → [LoginUserUseCase]
  ↓
[Find User by Email]
  ├─ Not Found → [Return 401]
  │
  └─ Found → [Verify Password]
              ├─ Invalid → [Return 401]
              │
              └─ Valid → [Generate Token]
                          ↓
                     [Return User + Token]
                          ↓
                     Frontend → [Save Token]
                          ↓
                     [Redirect to Dashboard based on Role]
```

---

## Flow 3: إنشاء كورس جديد

```
Admin
  ↓
[فتح صفحة إدارة الكورسات]
  ↓
[Click "إضافة كورس جديد"]
  ↓
[ملء النموذج: عنوان، وصف، سعر، إلخ]
  ↓
[Submit]
  ↓
Frontend → [POST /api/admin/courses]
  ↓
Backend → [CreateCourseUseCase]
  ↓
[Validation]
  ├─ Success → [Create Course]
  │              ↓
  │         [Upload Image (if exists)]
  │              ↓
  │         [Save Course]
  │              ↓
  │         [Fire CourseCreated Event]
  │              ↓
  │         [Return Course]
  │              ↓
  │         Frontend → [Show Success Message]
  │              ↓
  │         [Redirect to Courses List]
  │
  └─ Error → [Return Validation Errors]
              ↓
         Frontend → [Display Errors]
```

---

## Flow 4: التسجيل في كورس

```
Student
  ↓
[فتح صفحة تفاصيل كورس]
  ↓
[Click "سجل الآن"]
  ↓
Frontend → [POST /api/student/courses/{id}/enroll]
  ↓
Backend → [EnrollmentService::create]
  ↓
[Check Course Availability]
  ├─ Not Available → [Return 422]
  │
  └─ Available → [Check Existing Enrollment]
                  ├─ Exists → [Return 409]
                  │
                  └─ Not Exists → [Create Enrollment]
                                    ↓
                               [Set Status: pending]
                                    ↓
                               [Set Payment Status: not_paid]
                                    ↓
                               [Save Enrollment]
                                    ↓
                               [Fire EnrollmentCreated Event]
                                    ↓
                               [Return Enrollment]
                                    ↓
                               Frontend → [Show Success Message]
                                    ↓
                               [Notify Admin (if configured)]
```

---

## Flow 5: الموافقة على التسجيل

```
Admin
  ↓
[فتح صفحة التسجيلات]
  ↓
[رؤية قائمة التسجيلات المعلقة]
  ↓
[Click على تسجيل معين]
  ↓
[Click "موافقة"]
  ↓
Frontend → [PUT /api/admin/enrollments/{id}]
  ↓
Backend → [EnrollmentService::update]
  ↓
[Update Enrollment Status: approved]
  ↓
[Set can_attend: true]
  ↓
[Set approved_by: Admin ID]
  ↓
[Set approved_at: now()]
  ↓
[Save Enrollment]
  ↓
[Fire EnrollmentApproved Event]
  ↓
[Notify Student (if configured)]
  ↓
[Return Updated Enrollment]
  ↓
Frontend → [Show Success Message]
  ↓
[Update UI]
```

---

## Flow 6: توليد جلسات تلقائياً

```
Admin
  ↓
[فتح كورس]
  ↓
[Click "توليد جلسات"]
  ↓
Frontend → [POST /api/admin/courses/{id}/sessions/generate]
  ↓
Backend → [GenerateSessionsUseCase]
  ↓
[Read Course Settings]
  ├─ start_date
  ├─ session_count
  ├─ days_of_week
  ├─ default_start_time
  └─ default_end_time
  ↓
[Calculate Session Dates]
  ↓
[Loop: Create Sessions]
  ├─ For each session:
  │   ├─ Calculate date based on days_of_week
  │   ├─ Set start_time
  │   ├─ Set end_time
  │   ├─ Set status: scheduled
  │   └─ Save Session
  ↓
[Return List of Created Sessions]
  ↓
Frontend → [Show Success Message]
  ↓
[Display Sessions List]
```

---

## Flow 7: تسجيل الحضور

```
Instructor
  ↓
[فتح جلسة]
  ↓
[رؤية قائمة الطلاب]
  ↓
[تسجيل حضور/غياب لكل طالب]
  ↓
[Click "حفظ"]
  ↓
Frontend → [POST /api/instructor/attendance]
  ↓
Backend → [AttendanceService::store]
  ↓
[For each student]
  ├─ [Check Enrollment]
  │   ├─ Not Enrolled → Skip
  │   └─ Enrolled → [Create/Update Attendance]
  │                   ├─ student_id
  │                   ├─ session_id
  │                   ├─ status (present/absent/late)
  │                   └─ Save
  ↓
[Return Success]
  ↓
Frontend → [Show Success Message]
  ↓
[Update UI]
```

---

## Flow 8: إجراء Quiz

```
Student
  ↓
[فتح كورس]
  ↓
[رؤية Quiz متاح]
  ↓
[Click "ابدأ Quiz"]
  ↓
Frontend → [GET /api/student/quizzes/{id}]
  ↓
Backend → [QuizService::show]
  ↓
[Check Enrollment]
  ├─ Not Enrolled → [Return 403]
  │
  └─ Enrolled → [Check Attempts]
                  ├─ Max Attempts Reached → [Return 422]
                  │
                  └─ Allowed → [Load Quiz with Questions]
                                ↓
                           [Create QuizAttempt]
                                ↓
                           [Start Timer (if exists)]
                                ↓
                           [Return Quiz]
                                ↓
                           Frontend → [Display Quiz]
                                ↓
                           [Student Answers Questions]
                                ↓
                           [Click "تقديم"]
                                ↓
                           Frontend → [POST /api/student/quizzes/{id}/submit]
                                ↓
                           Backend → [Calculate Score]
                                      ↓
                                 [Update QuizAttempt]
                                      ↓
                                 [Update Progress]
                                      ↓
                                 [Return Result]
                                      ↓
                                 Frontend → [Display Result]
```

---

## Flow 9: تحديث التقدم

```
Student
  ↓
[إكمال Lesson]
  ↓
Frontend → [POST /api/student/progress]
  ↓
Backend → [ProgressService::update]
  ↓
[Check Enrollment]
  ├─ Not Enrolled → [Return 403]
  │
  └─ Enrolled → [Create/Update StudentProgress]
                  ├─ student_id
                  ├─ course_id
                  ├─ lesson_id
                  ├─ is_completed: true
                  ├─ completed_at: now()
                  └─ Save
                  ↓
             [Calculate Overall Progress]
                  ↓
             [Check if Course Completed]
                  ├─ Not Completed → [Return Progress]
                  │
                  └─ Completed (100%) → [Issue Certificate]
                                         ├─ Generate Certificate Number
                                         ├─ Generate Verification Code
                                         ├─ Create Certificate
                                         └─ Notify Student
                                         ↓
                                    [Return Progress + Certificate]
                  ↓
             Frontend → [Update UI]
                  ↓
             [Show Progress Update]
```

---

## Flow 10: إصدار شهادة

```
System (Automatic)
  ↓
[Student Completes Course (100%)]
  ↓
[ProgressService detects completion]
  ↓
[Check if Certificate Already Issued]
  ├─ Exists → [Skip]
  │
  └─ Not Exists → [CertificateService::issue]
                    ↓
               [Generate Certificate Number]
                    ↓
               [Generate Verification Code]
                    ↓
               [Create Certificate]
                    ├─ course_id
                    ├─ student_id
                    ├─ enrollment_id
                    ├─ certificate_number
                    ├─ verification_code
                    ├─ issued_date: now()
                    └─ Save
                    ↓
               [Generate PDF (if configured)]
                    ↓
               [Fire CertificateIssued Event]
                    ↓
               [Notify Student]
                    ↓
               [Update UI]
```

---

## Flow 11: عرض التقارير

```
Admin
  ↓
[فتح صفحة التقارير]
  ↓
[اختيار نوع التقرير]
  ├─ تقرير الكورسات
  ├─ تقرير المدربين
  ├─ تقرير مالي
  └─ تقارير استراتيجية
  ↓
[تطبيق Filters]
  ├─ تاريخ من
  ├─ تاريخ إلى
  └─ تصفية إضافية
  ↓
Frontend → [GET /api/admin/reports/{type}]
  ↓
Backend → [ReportService]
  ↓
[Query Data based on Type]
  ├─ Courses Report → [Query Courses, Enrollments, Revenue]
  ├─ Instructors Report → [Query Instructors, Courses, Students]
  ├─ Financial Report → [Query Enrollments, Payments, Revenue]
  └─ Strategic Report → [Query Analytics, Forecasts]
  ↓
[Calculate Metrics]
  ↓
[Format Data]
  ↓
[Return Report Data]
  ↓
Frontend → [Display Charts & Tables]
  ↓
[Export (if requested)]
  ├─ Excel
  └─ PDF
```

---

## Flow 12: صلاحيات Admin

```
Admin
  ↓
[Access Admin Route]
  ↓
Frontend → [Check Authentication]
  ├─ Not Authenticated → [Redirect to Login]
  │
  └─ Authenticated → [Check Role]
                       ├─ Not Admin → [Return 403]
                       │
                       └─ Admin → [Check Permission]
                                    ├─ No Permission → [Return 403]
                                    │
                                    └─ Has Permission → [Allow Access]
                                                         ↓
                                                    [Load Page]
```

---

**آخر تحديث**: 2025-11-21  
**الإصدار**: 1.0.0

