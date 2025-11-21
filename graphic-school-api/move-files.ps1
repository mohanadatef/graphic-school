# PowerShell script to move files and update namespaces
# This script will be executed step by step

Write-Host "Starting file migration..." -ForegroundColor Green

# Define file mappings
$fileMappings = @{
    # ACL/Auth
    "app/Http/Controllers/AuthController.php" = "Modules/ACL/Auth/Http/Controllers/AuthController.php"
    "app/Services/AuthService.php" = "Modules/ACL/Auth/Services/AuthService.php"
    "app/Http/Requests/Auth/LoginRequest.php" = "Modules/ACL/Auth/Http/Requests/LoginRequest.php"
    "app/Http/Requests/Auth/RegisterRequest.php" = "Modules/ACL/Auth/Http/Requests/RegisterRequest.php"
    "app/Http/Requests/Auth/LogoutRequest.php" = "Modules/ACL/Auth/Http/Requests/LogoutRequest.php"
    
    # ACL/Users - Controllers
    "app/Http/Controllers/Admin/UserController.php" = "Modules/ACL/Users/Http/Controllers/UserController.php"
    "app/Http/Controllers/InstructorController.php" = "Modules/ACL/Users/Http/Controllers/InstructorController.php"
    "app/Http/Controllers/StudentController.php" = "Modules/ACL/Users/Http/Controllers/StudentController.php"
    
    # ACL/Users - Resources
    "app/Http/Resources/UserResource.php" = "Modules/ACL/Users/Http/Resources/UserResource.php"
    
    # ACL/Users - Requests
    "app/Http/Requests/Admin/User/ListUserRequest.php" = "Modules/ACL/Users/Http/Requests/ListUserRequest.php"
    "app/Http/Requests/Admin/User/StoreUserRequest.php" = "Modules/ACL/Users/Http/Requests/StoreUserRequest.php"
    "app/Http/Requests/Admin/User/UpdateUserRequest.php" = "Modules/ACL/Users/Http/Requests/UpdateUserRequest.php"
}

# Function to update namespace in file
function Update-Namespace {
    param(
        [string]$FilePath,
        [string]$OldNamespace,
        [string]$NewNamespace
    )
    
    if (Test-Path $FilePath) {
        $content = Get-Content $FilePath -Raw
        $content = $content -replace [regex]::Escape($OldNamespace), $NewNamespace
        Set-Content $FilePath $content -NoNewline
    }
}

# Move files
foreach ($mapping in $fileMappings.GetEnumerator()) {
    $source = $mapping.Key
    $destination = $mapping.Value
    
    if (Test-Path $source) {
        $destDir = Split-Path $destination -Parent
        if (-not (Test-Path $destDir)) {
            New-Item -ItemType Directory -Path $destDir -Force | Out-Null
        }
        
        Copy-Item $source $destination -Force
        Write-Host "Copied: $source -> $destination" -ForegroundColor Yellow
        
        # Update namespace in the copied file
        $oldNs = "App\Http\Controllers" 
        $newNs = "Modules\ACL\Auth\Http\Controllers"
        # This is simplified - actual namespace updates will be done manually
    } else {
        Write-Host "Source not found: $source" -ForegroundColor Red
    }
}

Write-Host "File migration completed!" -ForegroundColor Green

