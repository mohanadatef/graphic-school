<?php

namespace Database\Seeders;

use App\Models\Translation;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class TranslationSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $translations = [
            // Auth translations
            ['key' => 'auth.login', 'en' => 'Login', 'ar' => 'تسجيل الدخول'],
            ['key' => 'auth.logout', 'en' => 'Logout', 'ar' => 'تسجيل الخروج'],
            ['key' => 'auth.register', 'en' => 'Register', 'ar' => 'التسجيل'],
            ['key' => 'auth.login_success', 'en' => 'Logged in successfully', 'ar' => 'تم تسجيل الدخول بنجاح'],
            ['key' => 'auth.logout_success', 'en' => 'Logged out successfully', 'ar' => 'تم تسجيل الخروج بنجاح'],
            ['key' => 'auth.register_success', 'en' => 'Registered successfully', 'ar' => 'تم التسجيل بنجاح'],
            ['key' => 'auth.invalid_credentials', 'en' => 'Invalid credentials', 'ar' => 'بيانات الدخول غير صحيحة'],
            ['key' => 'auth.account_disabled', 'en' => 'Account disabled', 'ar' => 'الحساب معطل'],
            ['key' => 'auth.already_enrolled', 'en' => 'Student already enrolled', 'ar' => 'الطالب مسجل بالفعل'],
            ['key' => 'auth.student_role_not_found', 'en' => 'Student role not found', 'ar' => 'دور الطالب غير موجود'],
            
            // Course translations
            ['key' => 'course.created', 'en' => 'Course created successfully', 'ar' => 'تم إنشاء الدورة بنجاح'],
            ['key' => 'course.updated', 'en' => 'Course updated successfully', 'ar' => 'تم تحديث الدورة بنجاح'],
            ['key' => 'course.deleted', 'en' => 'Course deleted successfully', 'ar' => 'تم حذف الدورة بنجاح'],
            ['key' => 'course.not_found', 'en' => 'Course not found', 'ar' => 'الدورة غير موجودة'],
            
            // Enrollment translations
            ['key' => 'enrollment.created', 'en' => 'Enrollment created successfully', 'ar' => 'تم إنشاء التسجيل بنجاح'],
            ['key' => 'enrollment.updated', 'en' => 'Enrollment updated successfully', 'ar' => 'تم تحديث التسجيل بنجاح'],
            ['key' => 'enrollment.approved', 'en' => 'Enrollment approved', 'ar' => 'تم الموافقة على التسجيل'],
            
            // General messages
            ['key' => 'messages.success', 'en' => 'Operation completed successfully', 'ar' => 'تمت العملية بنجاح'],
            ['key' => 'messages.error', 'en' => 'An error occurred', 'ar' => 'حدث خطأ'],
            ['key' => 'messages.not_found', 'en' => 'Resource not found', 'ar' => 'المورد غير موجود'],
            ['key' => 'messages.unauthorized', 'en' => 'Unauthorized access', 'ar' => 'وصول غير مصرح به'],
            ['key' => 'messages.validation_error', 'en' => 'Validation error', 'ar' => 'خطأ في التحقق'],
            
            // Log messages
            ['key' => 'log.user_registration_attempt', 'en' => 'User registration attempt', 'ar' => 'محاولة تسجيل مستخدم'],
            ['key' => 'log.user_registered', 'en' => 'User registered successfully', 'ar' => 'تم تسجيل المستخدم بنجاح'],
            ['key' => 'log.user_login_attempt', 'en' => 'User login attempt', 'ar' => 'محاولة تسجيل دخول'],
            ['key' => 'log.user_logged_in', 'en' => 'User logged in successfully', 'ar' => 'تم تسجيل الدخول بنجاح'],
            ['key' => 'log.user_logged_out', 'en' => 'User logged out', 'ar' => 'تم تسجيل الخروج'],
            ['key' => 'log.login_failed', 'en' => 'User login failed', 'ar' => 'فشل تسجيل الدخول'],
            ['key' => 'log.course_created', 'en' => 'Course created', 'ar' => 'تم إنشاء الدورة'],
            ['key' => 'log.course_updated', 'en' => 'Course updated', 'ar' => 'تم تحديث الدورة'],
            ['key' => 'log.course_deleted', 'en' => 'Course deleted', 'ar' => 'تم حذف الدورة'],
            ['key' => 'log.enrollment_created', 'en' => 'Enrollment created', 'ar' => 'تم إنشاء التسجيل'],
            ['key' => 'log.enrollment_approved', 'en' => 'Enrollment approved', 'ar' => 'تم الموافقة على التسجيل'],
        ];

        foreach ($translations as $translation) {
            // English
            Translation::updateOrCreate(
                [
                    'key' => $translation['key'],
                    'locale' => 'en',
                    'group' => 'messages',
                ],
                [
                    'value' => $translation['en'],
                ]
            );

            // Arabic
            Translation::updateOrCreate(
                [
                    'key' => $translation['key'],
                    'locale' => 'ar',
                    'group' => 'messages',
                ],
                [
                    'value' => $translation['ar'],
                ]
            );
        }

        $this->command->info('Translations seeded successfully!');
    }
}
