<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Modules\LMS\Enrollments\Models\Enrollment;
use App\Models\EnrollmentLog;
use App\Models\PaymentMethod;
use App\Models\Invoice;
use App\Models\InvoiceItem;
use App\Models\PaymentTransaction;
use App\Models\Attendance;
use App\Models\CertificateTemplate;
use Modules\LMS\Certificates\Models\Certificate;
use App\Models\Program;
use App\Models\Batch;
use App\Models\Group;
use Modules\ACL\Users\Models\User;
use Modules\LMS\Sessions\Models\Session;
use Carbon\Carbon;

class Phase3DataSeeder extends Seeder
{
    public function run(): void
    {
        // 1. Create Payment Methods
        $paymentMethods = [
            ['name' => 'Cash', 'type' => 'cash', 'is_active' => true],
            ['name' => 'Visa Card', 'type' => 'card', 'is_active' => true],
            ['name' => 'Paymob', 'type' => 'paymob', 'is_active' => true],
        ];

        foreach ($paymentMethods as $method) {
            PaymentMethod::firstOrCreate(
                ['type' => $method['type']],
                $method
            );
        }

        // 2. Create Certificate Template
        $template = CertificateTemplate::firstOrCreate(
            ['title' => 'Default Certificate Template'],
            [
                'layout' => [
                    'student_name' => ['x' => 50, 'y' => 40, 'font_size' => 24],
                    'program_name' => ['x' => 50, 'y' => 50, 'font_size' => 20],
                    'issue_date' => ['x' => 50, 'y' => 70, 'font_size' => 14],
                ],
                'font_main' => 'Cairo',
                'font_headings' => 'Poppins',
                'is_active' => true,
            ]
        );

        // 3. Get Programs, Batches, Groups, Students
        $programs = Program::with('batches.groups')->get();
        $students = User::whereHas('role', fn($q) => $q->where('name', 'student'))->get();
        $cashMethod = PaymentMethod::where('type', 'cash')->first();
        $cardMethod = PaymentMethod::where('type', 'card')->first();

        if ($programs->isEmpty() || $students->isEmpty()) {
            $this->command->warn('No programs or students found. Please run DynamicLearningSeeder first.');
            return;
        }

        // 4. Create Enrollments (6 pending, 6 approved)
        $enrollments = [];
        $program = $programs->first();
        $batch = $program->batches->first();
        $group = $batch ? $batch->groups->first() : null;

        // Pending enrollments
        for ($i = 0; $i < 6; $i++) {
            if ($i >= $students->count()) break;
            
            $enrollment = Enrollment::create([
                'student_id' => $students[$i]->id,
                'program_id' => $program->id,
                'batch_id' => $batch?->id,
                'group_id' => $group?->id,
                'status' => 'pending',
                'payment_status' => 'not_paid',
            ]);

            EnrollmentLog::create([
                'enrollment_id' => $enrollment->id,
                'action' => 'created',
                'metadata' => ['program_id' => $program->id],
            ]);

            $enrollments[] = $enrollment;
        }

        // Approved enrollments
        for ($i = 6; $i < 12; $i++) {
            if ($i >= $students->count()) break;
            
            $enrollment = Enrollment::create([
                'student_id' => $students[$i]->id,
                'program_id' => $program->id,
                'batch_id' => $batch?->id,
                'group_id' => $group?->id,
                'status' => 'approved',
                'payment_status' => 'not_paid',
                'can_attend' => true,
                'approved_at' => Carbon::now()->subDays(rand(1, 30)),
            ]);

            EnrollmentLog::create([
                'enrollment_id' => $enrollment->id,
                'action' => 'approved',
                'metadata' => ['batch_id' => $batch?->id, 'group_id' => $group?->id],
            ]);

            // Create invoice for approved enrollment
            $invoice = Invoice::create([
                'enrollment_id' => $enrollment->id,
                'total_amount' => $program->price ?? 5000,
                'due_date' => Carbon::now()->addDays(30),
                'status' => 'unpaid',
            ]);

            InvoiceItem::create([
                'invoice_id' => $invoice->id,
                'title' => $program->getTranslated('title') ?? 'Program Fee',
                'amount' => $program->price ?? 5000,
                'quantity' => 1,
            ]);

            EnrollmentLog::create([
                'enrollment_id' => $enrollment->id,
                'action' => 'payment-created',
                'metadata' => ['invoice_id' => $invoice->id],
            ]);

            $enrollments[] = $enrollment;
        }

        // 5. Create Invoices (8 total - some paid, some unpaid)
        $approvedEnrollments = Enrollment::where('status', 'approved')->get();
        $invoiceStatuses = ['unpaid', 'unpaid', 'partially_paid', 'paid', 'paid', 'unpaid', 'unpaid', 'paid'];
        
        foreach ($approvedEnrollments->take(8) as $index => $enrollment) {
            if (!isset($invoiceStatuses[$index])) continue;
            
            $invoice = Invoice::firstOrCreate(
                ['enrollment_id' => $enrollment->id],
                [
                    'total_amount' => $program->price ?? 5000,
                    'due_date' => Carbon::now()->addDays(rand(10, 60)),
                    'status' => $invoiceStatuses[$index],
                ]
            );

            if ($invoice->wasRecentlyCreated) {
                InvoiceItem::create([
                    'invoice_id' => $invoice->id,
                    'title' => $program->getTranslated('title') ?? 'Program Fee',
                    'amount' => $program->price ?? 5000,
                    'quantity' => 1,
                ]);
            }

            // Create payment transactions for paid/partially paid invoices
            if (in_array($invoice->status, ['paid', 'partially_paid'])) {
                $amount = $invoice->status === 'paid' 
                    ? $invoice->total_amount 
                    : $invoice->total_amount * 0.5;

                PaymentTransaction::create([
                    'invoice_id' => $invoice->id,
                    'payment_method_id' => rand(0, 1) ? $cashMethod->id : $cardMethod->id,
                    'amount' => $amount,
                    'status' => 'success',
                    'reference_code' => 'PAY-' . strtoupper(uniqid()),
                    'metadata' => ['method' => 'mock'],
                    'processed_at' => Carbon::now()->subDays(rand(1, 20)),
                ]);

                $invoice->updateStatus();
            }
        }

        // 6. Create Attendance Records (40 records)
        if ($group) {
            $sessions = Session::where('group_id', $group->id)->get();
            $groupStudents = $group->students;

            if ($sessions->isNotEmpty() && $groupStudents->isNotEmpty()) {
                $statuses = ['present', 'present', 'present', 'absent', 'late'];
                $count = 0;

                foreach ($sessions->take(10) as $session) {
                    foreach ($groupStudents->take(4) as $student) {
                        if ($count >= 40) break;

                        Attendance::firstOrCreate(
                            [
                                'session_id' => $session->id,
                                'student_id' => $student->id,
                            ],
                            [
                                'status' => $statuses[array_rand($statuses)],
                                'timestamp' => $session->scheduled_at ?? now(),
                            ]
                        );

                        $count++;
                    }
                }
            }
        }

        // 7. Create Certificates (3 issued)
        $certifiedEnrollments = Enrollment::where('status', 'approved')
            ->whereHas('invoices', fn($q) => $q->where('status', 'paid'))
            ->take(3)
            ->get();

        foreach ($certifiedEnrollments as $enrollment) {
            if (!$enrollment->id) {
                continue; // Skip if enrollment has no ID
            }
            
            Certificate::firstOrCreate(
                [
                    'student_id' => $enrollment->student_id,
                    'program_id' => $enrollment->program_id,
                ],
                [
                    'enrollment_id' => $enrollment->id,
                    'certificate_template_id' => $template->id,
                    'verification_code' => 'CERT-' . strtoupper(uniqid()),
                    'issued_at' => Carbon::now()->subDays(rand(1, 90)),
                ]
            );
        }

        $this->command->info('Phase 3 demo data seeded successfully!');
        $this->command->info('- Payment Methods: ' . PaymentMethod::count());
        $this->command->info('- Enrollments: ' . Enrollment::where('program_id', '!=', null)->count());
        $this->command->info('- Invoices: ' . Invoice::count());
        $this->command->info('- Payment Transactions: ' . PaymentTransaction::count());
        $this->command->info('- Attendance Records: ' . Attendance::count());
        $this->command->info('- Certificates: ' . Certificate::where('program_id', '!=', null)->count());
    }
}

