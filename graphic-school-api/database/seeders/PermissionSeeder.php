<?php

namespace Database\Seeders;

use Modules\ACL\Permissions\Models\Permission;
use Illuminate\Database\Seeder;

class PermissionSeeder extends Seeder
{
    public function run(): void
    {
        $permissions = [
            // Dashboard
            ['name' => 'Dashboard Access', 'slug' => 'dashboard.access', 'module' => 'dashboard'],
            ['name' => 'Dashboard Reports', 'slug' => 'dashboard.reports', 'module' => 'dashboard'],
            
            // Users
            ['name' => 'View Users', 'slug' => 'users.view', 'module' => 'users'],
            ['name' => 'Manage Users', 'slug' => 'users.manage', 'module' => 'users'],
            
            // Roles & Permissions
            ['name' => 'View Roles', 'slug' => 'roles.view', 'module' => 'roles'],
            ['name' => 'Manage Roles', 'slug' => 'roles.manage', 'module' => 'roles'],
            ['name' => 'View Permissions', 'slug' => 'permissions.view', 'module' => 'roles'],
            ['name' => 'Manage Permissions', 'slug' => 'permissions.manage', 'module' => 'roles'],
            
            // Categories
            ['name' => 'View Categories', 'slug' => 'categories.view', 'module' => 'catalog'],
            ['name' => 'Manage Categories', 'slug' => 'categories.manage', 'module' => 'catalog'],
            
            // Courses
            ['name' => 'View Courses', 'slug' => 'courses.view', 'module' => 'catalog'],
            ['name' => 'Manage Courses', 'slug' => 'courses.manage', 'module' => 'catalog'],
            
            // Sessions
            ['name' => 'View Sessions', 'slug' => 'sessions.view', 'module' => 'sessions'],
            ['name' => 'Manage Sessions', 'slug' => 'sessions.manage', 'module' => 'sessions'],
            
            // Enrollments
            ['name' => 'View Enrollments', 'slug' => 'enrollments.view', 'module' => 'enrollments'],
            ['name' => 'Manage Enrollments', 'slug' => 'enrollments.manage', 'module' => 'enrollments'],
            
            // Attendance
            ['name' => 'View Attendance', 'slug' => 'attendance.view', 'module' => 'attendance'],
            ['name' => 'Manage Attendance', 'slug' => 'attendance.manage', 'module' => 'attendance'],
            ['name' => 'Take Attendance', 'slug' => 'attendance.take', 'module' => 'attendance'],
            
            // Payments
            ['name' => 'View Payments', 'slug' => 'payments.view', 'module' => 'payments'],
            ['name' => 'Manage Payments', 'slug' => 'payments.manage', 'module' => 'payments'],
            
            // Assessments
            ['name' => 'View Quizzes', 'slug' => 'quizzes.view', 'module' => 'assessments'],
            ['name' => 'Manage Quizzes', 'slug' => 'quizzes.manage', 'module' => 'assessments'],
            ['name' => 'View Projects', 'slug' => 'projects.view', 'module' => 'assessments'],
            ['name' => 'Manage Projects', 'slug' => 'projects.manage', 'module' => 'assessments'],
            
            // Progress
            ['name' => 'View Progress', 'slug' => 'progress.view', 'module' => 'progress'],
            ['name' => 'Manage Progress', 'slug' => 'progress.manage', 'module' => 'progress'],
            
            // Certificates
            ['name' => 'View Certificates', 'slug' => 'certificates.view', 'module' => 'certificates'],
            ['name' => 'Manage Certificates', 'slug' => 'certificates.manage', 'module' => 'certificates'],
            
            // Reviews
            ['name' => 'View Reviews', 'slug' => 'reviews.view', 'module' => 'reviews'],
            ['name' => 'Manage Reviews', 'slug' => 'reviews.manage', 'module' => 'reviews'],
            
            // Messaging
            ['name' => 'View Messaging', 'slug' => 'messaging.view', 'module' => 'messaging'],
            ['name' => 'Manage Messaging', 'slug' => 'messaging.manage', 'module' => 'messaging'],
            
            // Notifications
            ['name' => 'View Notifications', 'slug' => 'notifications.view', 'module' => 'notifications'],
            ['name' => 'Manage Notifications', 'slug' => 'notifications.manage', 'module' => 'notifications'],
            
            // CMS - Pages
            ['name' => 'View CMS Pages', 'slug' => 'cms.pages.view', 'module' => 'cms'],
            ['name' => 'Manage CMS Pages', 'slug' => 'cms.pages.manage', 'module' => 'cms'],
            
            // CMS - Media
            ['name' => 'View Media', 'slug' => 'media.view', 'module' => 'cms'],
            ['name' => 'Manage Media', 'slug' => 'media.manage', 'module' => 'cms'],
            
            // CMS - Sliders
            ['name' => 'View Sliders', 'slug' => 'sliders.view', 'module' => 'cms'],
            ['name' => 'Manage Sliders', 'slug' => 'sliders.manage', 'module' => 'cms'],
            
            // CMS - Testimonials
            ['name' => 'View Testimonials', 'slug' => 'testimonials.view', 'module' => 'cms'],
            ['name' => 'Manage Testimonials', 'slug' => 'testimonials.manage', 'module' => 'cms'],
            
            // CMS - FAQ
            ['name' => 'View FAQ', 'slug' => 'faq.view', 'module' => 'cms'],
            ['name' => 'Manage FAQ', 'slug' => 'faq.manage', 'module' => 'cms'],
            
            // CMS - Settings
            ['name' => 'View Settings', 'slug' => 'settings.view', 'module' => 'settings'],
            ['name' => 'Manage Settings', 'slug' => 'settings.manage', 'module' => 'settings'],
            
            // CMS - Contacts
            ['name' => 'View Contacts', 'slug' => 'contacts.view', 'module' => 'contacts'],
            ['name' => 'Manage Contacts', 'slug' => 'contacts.manage', 'module' => 'contacts'],
            
            // Localization
            ['name' => 'View Translations', 'slug' => 'translations.view', 'module' => 'localization'],
            ['name' => 'Manage Translations', 'slug' => 'translations.manage', 'module' => 'localization'],
            ['name' => 'View Languages', 'slug' => 'languages.view', 'module' => 'localization'],
            ['name' => 'Manage Languages', 'slug' => 'languages.manage', 'module' => 'localization'],
            
            // Reports
            ['name' => 'View Reports', 'slug' => 'reports.view', 'module' => 'reports'],
            ['name' => 'Manage Reports', 'slug' => 'reports.manage', 'module' => 'reports'],
            ['name' => 'Export Reports', 'slug' => 'reports.export', 'module' => 'reports'],
            
            // Analytics
            ['name' => 'View Analytics', 'slug' => 'analytics.view', 'module' => 'analytics'],
            
            // Audit Log
            ['name' => 'View Audit Logs', 'slug' => 'audit_logs.view', 'module' => 'audit'],
            
            // Tickets
            ['name' => 'View Tickets', 'slug' => 'tickets.view', 'module' => 'support'],
            ['name' => 'Manage Tickets', 'slug' => 'tickets.manage', 'module' => 'support'],
            
            // Notes
            ['name' => 'Manage Notes', 'slug' => 'notes.manage', 'module' => 'sessions'],
            
            // Role-based access
            ['name' => 'Instructor Access', 'slug' => 'instructor.access', 'module' => 'instructor'],
            ['name' => 'Student Access', 'slug' => 'student.access', 'module' => 'student'],
        ];

        foreach ($permissions as $permission) {
            Permission::updateOrCreate(
                ['slug' => $permission['slug']],
                $permission
            );
        }
        
        $this->command->info('Permissions seeded successfully!');
    }
}
