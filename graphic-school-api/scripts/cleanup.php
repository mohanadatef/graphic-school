<?php

/**
 * Cleanup Script
 * Lists all files to delete for SUPER CLEANUP
 */

$filesToDelete = [
    // Models
    'app/Models/AcademySubscription.php',
    'app/Models/AttendanceLog.php',
    'app/Models/BrandingSetting.php',
    'app/Models/CertificateTemplate.php',
    'app/Models/Conversation.php',
    'app/Models/CourseModuleTranslation.php',
    'app/Models/CourseTranslation.php',
    'app/Models/EnrollmentLog.php',
    'app/Models/FAQ.php',
    'app/Models/FAQTranslation.php',
    'app/Models/GroupTranslation.php',
    'app/Models/Invoice.php',
    'app/Models/InvoiceItem.php',
    'app/Models/LessonTranslation.php',
    'app/Models/Media.php',
    'app/Models/Message.php',
    'app/Models/PageBuilderBlock.php',
    'app/Models/PageBuilderPage.php',
    'app/Models/PageBuilderStructure.php',
    'app/Models/PageBuilderTemplate.php',
    'app/Models/PageTranslation.php',
    'app/Models/Payment.php',
    'app/Models/PaymentMethod.php',
    'app/Models/PaymentTransaction.php',
    'app/Models/QrToken.php',
    'app/Models/SessionTranslation.php',
    'app/Models/SliderTranslation.php',
    'app/Models/TestimonialTranslation.php',
    'app/Models/Version.php',
];

echo "Files to delete: " . count($filesToDelete) . "\n";
foreach ($filesToDelete as $file) {
    echo "- $file\n";
}

