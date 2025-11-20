# PowerShell Script to Complete Module Migration
# This script will help automate the remaining file migrations

Write-Host "========================================" -ForegroundColor Cyan
Write-Host "  Graphic School - Module Migration" -ForegroundColor Cyan
Write-Host "========================================" -ForegroundColor Cyan
Write-Host ""

# Note: This is a helper script
# Actual file migration with namespace updates should be done carefully
# Use this as a reference for file mappings

$mappings = @{
    # Services
    "app/Services/CategoryService.php" = "Modules/LMS/Categories/Services/CategoryService.php"
    "app/Services/CourseService.php" = "Modules/LMS/Courses/Services/CourseService.php"
    "app/Services/SessionService.php" = "Modules/LMS/Sessions/Services/SessionService.php"
    "app/Services/EnrollmentService.php" = "Modules/LMS/Enrollments/Services/EnrollmentService.php"
    "app/Services/AttendanceService.php" = "Modules/LMS/Attendance/Services/AttendanceService.php"
    "app/Services/SettingService.php" = "Modules/CMS/Settings/Services/SettingService.php"
    "app/Services/ContactMessageService.php" = "Modules/CMS/Contacts/Services/ContactMessageService.php"
    "app/Services/TestimonialService.php" = "Modules/CMS/Testimonials/Services/TestimonialService.php"
    "app/Services/PublicSiteService.php" = "Modules/CMS/PublicSite/Services/PublicSiteService.php"
    "app/Services/TranslationService.php" = "Modules/Core/Localization/Services/TranslationService.php"
    "app/Services/DashboardService.php" = "Modules/Operations/Dashboard/Services/DashboardService.php"
    "app/Services/InstructorService.php" = "Modules/ACL/Users/Services/InstructorService.php"
    "app/Services/StudentService.php" = "Modules/ACL/Users/Services/StudentService.php"
    
    # Controllers
    "app/Http/Controllers/Admin/CategoryController.php" = "Modules/LMS/Categories/Http/Controllers/CategoryController.php"
    "app/Http/Controllers/Admin/CourseController.php" = "Modules/LMS/Courses/Http/Controllers/CourseController.php"
    "app/Http/Controllers/Admin/SessionController.php" = "Modules/LMS/Sessions/Http/Controllers/SessionController.php"
    "app/Http/Controllers/Admin/EnrollmentController.php" = "Modules/LMS/Enrollments/Http/Controllers/EnrollmentController.php"
    "app/Http/Controllers/Admin/AttendanceController.php" = "Modules/LMS/Attendance/Http/Controllers/AttendanceController.php"
    "app/Http/Controllers/Admin/SettingController.php" = "Modules/CMS/Settings/Http/Controllers/SettingController.php"
    "app/Http/Controllers/Admin/ContactController.php" = "Modules/CMS/Contacts/Http/Controllers/ContactController.php"
    "app/Http/Controllers/Admin/TestimonialController.php" = "Modules/CMS/Testimonials/Http/Controllers/TestimonialController.php"
    "app/Http/Controllers/PublicController.php" = "Modules/CMS/PublicSite/Http/Controllers/PublicController.php"
    "app/Http/Controllers/LanguageController.php" = "Modules/Core/Localization/Http/Controllers/LanguageController.php"
    "app/Http/Controllers/Admin/TranslationController.php" = "Modules/Core/Localization/Http/Controllers/TranslationController.php"
    "app/Http/Controllers/Admin/DashboardController.php" = "Modules/Operations/Dashboard/Http/Controllers/DashboardController.php"
    "app/Http/Controllers/Admin/ReportController.php" = "Modules/Operations/Reports/Http/Controllers/ReportController.php"
    "app/Http/Controllers/InstructorController.php" = "Modules/ACL/Users/Http/Controllers/InstructorController.php"
    "app/Http/Controllers/StudentController.php" = "Modules/ACL/Users/Http/Controllers/StudentController.php"
}

Write-Host "File Mappings Defined:" -ForegroundColor Green
Write-Host "Total mappings: $($mappings.Count)" -ForegroundColor Yellow
Write-Host ""
Write-Host "Next Steps:" -ForegroundColor Cyan
Write-Host "1. Copy files to new locations" -ForegroundColor White
Write-Host "2. Update namespaces in copied files" -ForegroundColor White
Write-Host "3. Update imports in all files" -ForegroundColor White
Write-Host "4. Update Service Providers" -ForegroundColor White
Write-Host "5. Update Routes" -ForegroundColor White
Write-Host "6. Test application" -ForegroundColor White
Write-Host ""

# Function to show mapping
function Show-Mapping {
    param($source, $destination)
    Write-Host "  $source" -ForegroundColor Gray
    Write-Host "    -> $destination" -ForegroundColor DarkGray
}

Write-Host "Mappings:" -ForegroundColor Cyan
foreach ($mapping in $mappings.GetEnumerator()) {
    Show-Mapping $mapping.Key $mapping.Value
}

Write-Host ""
Write-Host "Script completed. Use this as reference for manual migration." -ForegroundColor Green

