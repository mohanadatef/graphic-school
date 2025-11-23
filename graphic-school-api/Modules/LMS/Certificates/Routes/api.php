<?php

use Illuminate\Support\Facades\Route;
use Modules\LMS\Certificates\Http\Controllers\CertificateController;

Route::middleware(['auth:api'])->prefix('student')->group(function () {
    Route::post('/enrollments/{enrollmentId}/certificate', [CertificateController::class, 'issueCertificate']);
    Route::get('/certificates', [CertificateController::class, 'getMyCertificates']);
});

// Public route for verification
Route::get('/certificates/verify/{verificationCode}', [CertificateController::class, 'verifyCertificate']);

