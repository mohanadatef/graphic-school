<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;
use Modules\ACL\Users\Models\User;
use App\Models\WebsiteSetting;

class PrepareProductionCommand extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'app:prepare-production {--force : Force execution without confirmation}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Prepare the system for first client (clean demo data, keep essentials)';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        if (!$this->option('force')) {
            if (!$this->confirm('This will delete all business data (programs, students, enrollments, etc.). Continue?')) {
                $this->info('Operation cancelled.');
                return 0;
            }
        }

        $this->info('Preparing system for first client...');
        $this->newLine();

        DB::beginTransaction();

        try {
            // 1. Delete business data
            $this->info('Cleaning business data...');
            
            // Helper function to safely delete from table
            $safeDelete = function ($tableName) {
                if (Schema::hasTable($tableName)) {
                    DB::table($tableName)->delete();
                    return true;
                }
                return false;
            };
            
            // Programs and related
            $safeDelete('program_translations');
            $safeDelete('batch_translations');
            $safeDelete('group_translations');
            $safeDelete('groups');
            $safeDelete('batch_schedules');
            $safeDelete('batches');
            $safeDelete('programs');

            // Courses and related
            $safeDelete('course_translations');
            $safeDelete('course_instructor');
            $safeDelete('sessions');
            $safeDelete('courses');
            $safeDelete('categories');

            // Enrollments
            $safeDelete('enrollment_logs');
            $safeDelete('enrollments');

            // Attendance (note: table name is 'attendance' not 'attendances')
            $safeDelete('attendance_logs');
            $safeDelete('attendance'); // Singular, not plural
            $safeDelete('qr_tokens');

            // Assignments
            $safeDelete('assignment_logs');
            $safeDelete('assignment_submissions');
            $safeDelete('assignments');

            // Gradebook
            $safeDelete('gradebook_entries');

            // Certificates
            $safeDelete('certificate_templates');

            // Invoices and Payments
            $safeDelete('invoice_items');
            $safeDelete('invoices');
            $safeDelete('payment_transactions');
            $safeDelete('payments');

            // Community
            $safeDelete('community_reports');
            $safeDelete('community_likes');
            $safeDelete('community_replies');
            $safeDelete('community_comments');
            $safeDelete('community_tags');
            $safeDelete('community_posts');

            // Gamification
            $safeDelete('gamification_user_badges');
            $safeDelete('gamification_points_wallets');
            $safeDelete('gamification_events');

            // Quizzes and Assessments
            $safeDelete('quiz_attempts');
            $safeDelete('quiz_questions');
            $safeDelete('quizzes');
            $safeDelete('student_projects');

            // Calendar
            $safeDelete('calendar_events');

            // Page Builder (keep templates, delete custom pages)
            if (Schema::hasTable('page_builder_structures')) {
                DB::table('page_builder_structures')->delete();
            }
            if (Schema::hasTable('page_builder_pages')) {
                DB::table('page_builder_pages')->where('slug', '!=', 'home')->delete();
            }

            // Delete non-admin users (students, instructors)
            if (Schema::hasTable('users') && Schema::hasTable('roles')) {
                try {
                    // Get admin role IDs
                    $adminRoleIds = DB::table('roles')
                        ->whereIn('name', ['admin', 'super_admin', 'hq'])
                        ->pluck('id')
                        ->toArray();
                    
                    if (!empty($adminRoleIds)) {
                        // Delete users who don't have admin roles
                        // Using role_id directly since User has a role() BelongsTo relationship
                        User::whereNotIn('role_id', $adminRoleIds)->delete();
                        
                        // Also clean up model_has_roles if table exists (for Spatie permissions)
                        if (Schema::hasTable('model_has_roles')) {
                            DB::table('model_has_roles')
                                ->whereNotIn('role_id', $adminRoleIds)
                                ->delete();
                        }
                    } else {
                        // If no admin roles found, keep all users
                        $this->warn('No admin roles found. Keeping all users.');
                    }
                } catch (\Exception $e) {
                    // If there's an issue with the query, just skip user deletion
                    $this->warn('Could not delete non-admin users: ' . $e->getMessage());
                }
            }

            $this->info('✓ Business data cleaned');

            // 2. Reset website settings (mark as not activated)
            $this->info('Resetting website settings...');
            
            // Check if website_settings table exists
            if (!Schema::hasTable('website_settings')) {
                $this->warn('website_settings table does not exist. Skipping website settings reset.');
                $this->warn('Please run migrations first: php artisan migrate');
            } else {
                // Now get or create default website settings
                try {
                    $websiteSetting = WebsiteSetting::getDefault();
                    $websiteSetting->update([
                        'is_activated' => false,
                        'activated_at' => null,
                        'homepage_id' => null,
                    ]);
                    $this->info('✓ Website settings reset');
                } catch (\Exception $e) {
                    $this->warn('Could not reset website settings: ' . $e->getMessage());
                    // Don't fail the entire command if website settings can't be reset
                }
            }

            // 3. Keep essentials (already done by not deleting):
            // - Super Admin users
            // - Roles & permissions
            // - Website settings row
            // - System settings

            DB::commit();

            // 4. Sync Super Admin account
            $this->newLine();
            $this->info('Syncing Super Admin account...');
            $this->call('app:sync-admin-account');

            $this->newLine();
            $this->info('✓ System prepared for first client!');
            $this->newLine();

        } catch (\Exception $e) {
            DB::rollBack();
            $this->error('Error: ' . $e->getMessage());
            $this->error('Transaction rolled back.');
            return 1;
        }

        return 0;
    }
}

