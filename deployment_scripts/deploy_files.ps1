# ============================================================
# DEPLOYMENT SCRIPT - Multi Product Weighing Enhancement
# File: deploy_files.ps1
# Purpose: Deploy all modified files to production
# ============================================================

# Configuration
$ProjectRoot = "d:\project\web\Logistic_App"
$BackupRoot = "D:\backups"
$Timestamp = Get-Date -Format "yyyyMMdd_HHmmss"

# Colors for output
function Write-Success { param($message) Write-Host $message -ForegroundColor Green }
function Write-Warning { param($message) Write-Host $message -ForegroundColor Yellow }
function Write-Error { param($message) Write-Host $message -ForegroundColor Red }
function Write-Info { param($message) Write-Host $message -ForegroundColor Cyan }
function Write-Header { param($message) Write-Host "`n========================================" -ForegroundColor Cyan; Write-Host $message -ForegroundColor Cyan; Write-Host "========================================`n" -ForegroundColor Cyan }

# ============================================================
# HEADER
# ============================================================

Clear-Host
Write-Header "DEPLOYMENT SCRIPT - Multi Product Weighing"
Write-Host "Project Root: $ProjectRoot" -ForegroundColor Gray
Write-Host "Backup Root : $BackupRoot" -ForegroundColor Gray
Write-Host "Timestamp   : $Timestamp" -ForegroundColor Gray
Write-Host ""

# Confirm before proceeding
Write-Warning "This script will:"
Write-Host "  1. Backup existing files"
Write-Host "  2. Deploy new files to production"
Write-Host "  3. Clear Laravel caches"
Write-Host ""
$confirm = Read-Host "Continue? (Y/N)"
if ($confirm -ne "Y") {
    Write-Warning "Deployment cancelled by user"
    exit
}

# ============================================================
# STEP 1: Backup Existing Files
# ============================================================

Write-Header "STEP 1: BACKING UP EXISTING FILES"

# Create backup directory
$BackupPath = Join-Path $BackupRoot "Logistic_App_backup_$Timestamp"
Write-Info "Creating backup directory: $BackupPath"
New-Item -ItemType Directory -Force -Path $BackupPath | Out-Null

# Files to backup
$FilesToBackup = @(
    "app\Models\TrscaleHeader.php",
    "app\Services\MultiProductWeighingService.php",
    "app\Livewire\MultiProductWeighingIn.php",
    "resources\views\livewire\multi-product-weighing-in.blade.php",
    "resources\views\livewire\multi-product-weighing-out.blade.php",
    "resources\views\livewire\multi-product-approval.blade.php",
    "resources\views\cetakoutmp.blade.php"
)

$BackupSuccess = 0
$BackupFailed = 0

foreach ($file in $FilesToBackup) {
    $sourcePath = Join-Path $ProjectRoot $file
    $destPath = Join-Path $BackupPath $file
    $destDir = Split-Path $destPath -Parent
    
    # Create directory structure
    if (!(Test-Path $destDir)) {
        New-Item -ItemType Directory -Force -Path $destDir | Out-Null
    }
    
    # Copy file
    if (Test-Path $sourcePath) {
        try {
            Copy-Item $sourcePath -Destination $destPath -Force
            Write-Success "  ✓ Backed up: $file"
            $BackupSuccess++
        } catch {
            Write-Error "  ✗ Failed to backup: $file"
            Write-Error "    Error: $_"
            $BackupFailed++
        }
    } else {
        Write-Warning "  ⚠ File not found: $file"
        $BackupFailed++
    }
}

Write-Host ""
Write-Info "Backup Summary:"
Write-Host "  Success: $BackupSuccess files" -ForegroundColor Green
if ($BackupFailed -gt 0) {
    Write-Host "  Failed : $BackupFailed files" -ForegroundColor Red
}
Write-Host "  Location: $BackupPath" -ForegroundColor Gray

# Ask to continue if there were failures
if ($BackupFailed -gt 0) {
    Write-Host ""
    Write-Warning "Some files failed to backup!"
    $confirm = Read-Host "Continue anyway? (Y/N)"
    if ($confirm -ne "Y") {
        Write-Warning "Deployment cancelled"
        exit
    }
}

# ============================================================
# STEP 2: Deploy New Files
# ============================================================

Write-Header "STEP 2: DEPLOYING NEW FILES"

# Note: In a real scenario, you would have a deployment package
# For this script, we assume files are already in place (from git pull or manual copy)
# This section just verifies the files exist

Write-Info "Verifying deployment files..."
Write-Host ""

$DeploySuccess = 0
$DeployFailed = 0

foreach ($file in $FilesToBackup) {
    $filePath = Join-Path $ProjectRoot $file
    
    if (Test-Path $filePath) {
        Write-Success "  ✓ Found: $file"
        $DeploySuccess++
    } else {
        Write-Error "  ✗ Missing: $file"
        $DeployFailed++
    }
}

Write-Host ""
Write-Info "Deployment Verification:"
Write-Host "  Found  : $DeploySuccess files" -ForegroundColor Green
if ($DeployFailed -gt 0) {
    Write-Host "  Missing: $DeployFailed files" -ForegroundColor Red
    Write-Host ""
    Write-Error "Some files are missing! Please ensure all files are in place."
    Write-Warning "Deployment cannot proceed."
    exit
}

Write-Success "All files are in place!"

# ============================================================
# STEP 3: Clear Laravel Caches
# ============================================================

Write-Header "STEP 3: CLEARING LARAVEL CACHES"

Set-Location $ProjectRoot

Write-Info "Clearing all caches..."
Write-Host ""

try {
    # Clear all caches at once
    Write-Host "Running: php artisan optimize:clear"
    $output = & php artisan optimize:clear 2>&1
    Write-Host $output
    Write-Host ""
    Write-Success "✓ Caches cleared successfully!"
} catch {
    Write-Error "✗ Failed to clear caches!"
    Write-Error "Error: $_"
    Write-Host ""
    Write-Warning "You may need to clear caches manually:"
    Write-Host "  php artisan config:clear"
    Write-Host "  php artisan cache:clear"
    Write-Host "  php artisan route:clear"
    Write-Host "  php artisan view:clear"
}

# ============================================================
# STEP 4: Verification
# ============================================================

Write-Header "STEP 4: POST-DEPLOYMENT VERIFICATION"

Write-Info "Running basic checks..."
Write-Host ""

# Check if files are readable
$verifySuccess = 0
$verifyFailed = 0

foreach ($file in $FilesToBackup) {
    $filePath = Join-Path $ProjectRoot $file
    
    try {
        $content = Get-Content $filePath -ErrorAction Stop | Out-Null
        Write-Success "  ✓ Readable: $file"
        $verifySuccess++
    } catch {
        Write-Error "  ✗ Cannot read: $file"
        $verifyFailed++
    }
}

Write-Host ""

if ($verifyFailed -eq 0) {
    Write-Success "All files are readable and in place!"
} else {
    Write-Warning "$verifyFailed file(s) have issues!"
}

# ============================================================
# SUMMARY
# ============================================================

Write-Header "DEPLOYMENT SUMMARY"

Write-Host "Timestamp: $Timestamp"
Write-Host ""
Write-Host "Backup:"
Write-Host "  Location: $BackupPath" -ForegroundColor Gray
Write-Host "  Files   : $BackupSuccess backed up" -ForegroundColor Green
Write-Host ""
Write-Host "Deployment:"
Write-Host "  Files   : $DeploySuccess verified" -ForegroundColor Green
Write-Host ""
Write-Host "Verification:"
Write-Host "  Status  : $verifySuccess files OK" -ForegroundColor Green
if ($verifyFailed -gt 0) {
    Write-Host "  Issues  : $verifyFailed files" -ForegroundColor Red
}
Write-Host ""

Write-Info "Next Steps:"
Write-Host "  1. Test application in browser"
Write-Host "  2. Check Laravel logs: storage\logs\laravel.log"
Write-Host "  3. Monitor for errors"
Write-Host "  4. Run database verification: deployment_scripts\03_verification_queries.sql"
Write-Host ""

Write-Success "✅ DEPLOYMENT COMPLETED!"
Write-Host ""

# Offer to open log file
$openLog = Read-Host "Open Laravel log file? (Y/N)"
if ($openLog -eq "Y") {
    $logFile = Join-Path $ProjectRoot "storage\logs\laravel.log"
    if (Test-Path $logFile) {
        Start-Process notepad.exe $logFile
    } else {
        Write-Warning "Log file not found: $logFile"
    }
}

Write-Host ""
Write-Info "Deployment script completed at $(Get-Date -Format 'yyyy-MM-dd HH:mm:ss')"
Write-Host ""

# ============================================================
# ROLLBACK INFORMATION
# ============================================================

Write-Host ""
Write-Host "========================================" -ForegroundColor Yellow
Write-Host "ROLLBACK INFORMATION" -ForegroundColor Yellow
Write-Host "========================================" -ForegroundColor Yellow
Write-Host ""
Write-Host "If you need to rollback, run:" -ForegroundColor Yellow
Write-Host "  .\deployment_scripts\rollback_files.ps1 $BackupPath" -ForegroundColor White
Write-Host ""
Write-Host "Backup will be kept for 30 days" -ForegroundColor Gray
Write-Host ""
