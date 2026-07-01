# ============================================================
# ROLLBACK SCRIPT - Multi Product Weighing Enhancement
# File: rollback_files.ps1
# Purpose: Rollback application files to previous version
# ============================================================

param(
    [Parameter(Mandatory=$false)]
    [string]$BackupPath
)

# Configuration
$ProjectRoot = "d:\project\web\Logistic_App"

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
Write-Header "ROLLBACK SCRIPT - Multi Product Weighing"
Write-Host "Project Root: $ProjectRoot" -ForegroundColor Gray
Write-Host ""

Write-Warning "⚠️  WARNING: This will restore files from backup!"
Write-Warning "⚠️  Current files will be overwritten!"
Write-Host ""

# ============================================================
# STEP 1: Locate Backup
# ============================================================

Write-Header "STEP 1: LOCATING BACKUP"

# If backup path not provided, find latest backup
if ([string]::IsNullOrEmpty($BackupPath)) {
    Write-Info "No backup path provided. Searching for latest backup..."
    Write-Host ""
    
    $backupRoot = "D:\backups"
    $backupFolders = Get-ChildItem -Path $backupRoot -Directory -Filter "Logistic_App_backup_*" | Sort-Object Name -Descending
    
    if ($backupFolders.Count -eq 0) {
        Write-Error "No backup folders found in $backupRoot"
        Write-Host ""
        Write-Info "Expected folder pattern: Logistic_App_backup_YYYYMMDD_HHMMSS"
        Write-Host ""
        exit
    }
    
    Write-Info "Available backups:"
    Write-Host ""
    for ($i = 0; $i -lt [Math]::Min(10, $backupFolders.Count); $i++) {
        Write-Host "  [$($i+1)] $($backupFolders[$i].Name)" -ForegroundColor White
    }
    Write-Host ""
    
    $selection = Read-Host "Select backup number (or Q to quit)"
    if ($selection -eq "Q") {
        Write-Warning "Rollback cancelled"
        exit
    }
    
    $selectedIndex = [int]$selection - 1
    if ($selectedIndex -lt 0 -or $selectedIndex -ge $backupFolders.Count) {
        Write-Error "Invalid selection"
        exit
    }
    
    $BackupPath = $backupFolders[$selectedIndex].FullName
}

# Verify backup path exists
if (!(Test-Path $BackupPath)) {
    Write-Error "Backup path not found: $BackupPath"
    exit
}

Write-Success "✓ Using backup: $BackupPath"
Write-Host ""

# ============================================================
# STEP 2: Verify Backup Contents
# ============================================================

Write-Header "STEP 2: VERIFYING BACKUP CONTENTS"

# Files that should be in backup
$FilesToRestore = @(
    "app\Models\TrscaleHeader.php",
    "app\Services\MultiProductWeighingService.php",
    "app\Livewire\MultiProductWeighingIn.php",
    "resources\views\livewire\multi-product-weighing-in.blade.php",
    "resources\views\livewire\multi-product-weighing-out.blade.php",
    "resources\views\livewire\multi-product-approval.blade.php",
    "resources\views\cetakoutmp.blade.php"
)

Write-Info "Checking backup files..."
Write-Host ""

$foundFiles = 0
$missingFiles = 0
$missingFilesList = @()

foreach ($file in $FilesToRestore) {
    $backupFilePath = Join-Path $BackupPath $file
    
    if (Test-Path $backupFilePath) {
        Write-Success "  ✓ Found: $file"
        $foundFiles++
    } else {
        Write-Error "  ✗ Missing: $file"
        $missingFiles++
        $missingFilesList += $file
    }
}

Write-Host ""
Write-Info "Backup Verification:"
Write-Host "  Found  : $foundFiles files" -ForegroundColor Green
if ($missingFiles -gt 0) {
    Write-Host "  Missing: $missingFiles files" -ForegroundColor Red
}

if ($missingFiles -gt 0) {
    Write-Host ""
    Write-Warning "Backup is incomplete!"
    Write-Host "Missing files:" -ForegroundColor Red
    foreach ($file in $missingFilesList) {
        Write-Host "  - $file" -ForegroundColor Red
    }
    Write-Host ""
    $confirm = Read-Host "Continue with partial rollback? (Y/N)"
    if ($confirm -ne "Y") {
        Write-Warning "Rollback cancelled"
        exit
    }
}

# ============================================================
# STEP 3: Confirm Rollback
# ============================================================

Write-Header "STEP 3: CONFIRM ROLLBACK"

Write-Warning "You are about to restore $foundFiles file(s) from backup:"
Write-Host "  From: $BackupPath" -ForegroundColor Gray
Write-Host "  To  : $ProjectRoot" -ForegroundColor Gray
Write-Host ""
Write-Warning "Current files will be OVERWRITTEN!"
Write-Host ""

$confirm = Read-Host "Proceed with rollback? (Y/N)"
if ($confirm -ne "Y") {
    Write-Warning "Rollback cancelled"
    exit
}

# ============================================================
# STEP 4: Backup Current State (Before Rollback)
# ============================================================

Write-Header "STEP 4: BACKING UP CURRENT STATE"

$Timestamp = Get-Date -Format "yyyyMMdd_HHmmss"
$PreRollbackBackup = "D:\backups\Logistic_App_pre_rollback_$Timestamp"

Write-Info "Creating pre-rollback backup: $PreRollbackBackup"
New-Item -ItemType Directory -Force -Path $PreRollbackBackup | Out-Null

$preBackupSuccess = 0
foreach ($file in $FilesToRestore) {
    $sourcePath = Join-Path $ProjectRoot $file
    $destPath = Join-Path $PreRollbackBackup $file
    $destDir = Split-Path $destPath -Parent
    
    if (Test-Path $sourcePath) {
        if (!(Test-Path $destDir)) {
            New-Item -ItemType Directory -Force -Path $destDir | Out-Null
        }
        Copy-Item $sourcePath -Destination $destPath -Force
        Write-Success "  ✓ Backed up: $file"
        $preBackupSuccess++
    }
}

Write-Host ""
Write-Success "Pre-rollback backup completed: $preBackupSuccess files"
Write-Host ""

# ============================================================
# STEP 5: Restore Files
# ============================================================

Write-Header "STEP 5: RESTORING FILES FROM BACKUP"

$restoreSuccess = 0
$restoreFailed = 0
$failedFiles = @()

foreach ($file in $FilesToRestore) {
    $backupFilePath = Join-Path $BackupPath $file
    $targetPath = Join-Path $ProjectRoot $file
    
    if (Test-Path $backupFilePath) {
        try {
            Copy-Item $backupFilePath -Destination $targetPath -Force -ErrorAction Stop
            Write-Success "  ✓ Restored: $file"
            $restoreSuccess++
        } catch {
            Write-Error "  ✗ Failed to restore: $file"
            Write-Error "    Error: $_"
            $restoreFailed++
            $failedFiles += $file
        }
    } else {
        Write-Warning "  ⚠ Skipped (not in backup): $file"
    }
}

Write-Host ""
Write-Info "Restore Summary:"
Write-Host "  Success: $restoreSuccess files" -ForegroundColor Green
if ($restoreFailed -gt 0) {
    Write-Host "  Failed : $restoreFailed files" -ForegroundColor Red
}

if ($restoreFailed -gt 0) {
    Write-Host ""
    Write-Error "Some files failed to restore:"
    foreach ($file in $failedFiles) {
        Write-Host "  - $file" -ForegroundColor Red
    }
}

# ============================================================
# STEP 6: Clear Laravel Caches
# ============================================================

Write-Header "STEP 6: CLEARING LARAVEL CACHES"

Set-Location $ProjectRoot

Write-Info "Clearing all caches..."
Write-Host ""

try {
    Write-Host "Running: php artisan optimize:clear"
    $output = & php artisan optimize:clear 2>&1
    Write-Host $output
    Write-Host ""
    Write-Success "✓ Caches cleared successfully!"
} catch {
    Write-Error "✗ Failed to clear caches!"
    Write-Error "Error: $_"
    Write-Host ""
    Write-Warning "Clear caches manually:"
    Write-Host "  php artisan config:clear"
    Write-Host "  php artisan cache:clear"
    Write-Host "  php artisan route:clear"
    Write-Host "  php artisan view:clear"
}

# ============================================================
# STEP 7: Verification
# ============================================================

Write-Header "STEP 7: POST-ROLLBACK VERIFICATION"

Write-Info "Verifying restored files..."
Write-Host ""

$verifySuccess = 0
$verifyFailed = 0

foreach ($file in $FilesToRestore) {
    $filePath = Join-Path $ProjectRoot $file
    
    if (Test-Path $filePath) {
        try {
            $content = Get-Content $filePath -ErrorAction Stop | Out-Null
            Write-Success "  ✓ OK: $file"
            $verifySuccess++
        } catch {
            Write-Error "  ✗ Cannot read: $file"
            $verifyFailed++
        }
    } else {
        Write-Error "  ✗ Missing: $file"
        $verifyFailed++
    }
}

Write-Host ""

if ($verifyFailed -eq 0) {
    Write-Success "All files verified successfully!"
} else {
    Write-Warning "$verifyFailed file(s) have issues!"
}

# ============================================================
# SUMMARY
# ============================================================

Write-Header "ROLLBACK SUMMARY"

Write-Host "Timestamp: $(Get-Date -Format 'yyyy-MM-dd HH:mm:ss')"
Write-Host ""
Write-Host "Restored from:"
Write-Host "  $BackupPath" -ForegroundColor Gray
Write-Host ""
Write-Host "Pre-rollback backup:"
Write-Host "  $PreRollbackBackup" -ForegroundColor Gray
Write-Host ""
Write-Host "Restore Results:"
Write-Host "  Success: $restoreSuccess files" -ForegroundColor Green
if ($restoreFailed -gt 0) {
    Write-Host "  Failed : $restoreFailed files" -ForegroundColor Red
}
Write-Host ""
Write-Host "Verification:"
Write-Host "  OK     : $verifySuccess files" -ForegroundColor Green
if ($verifyFailed -gt 0) {
    Write-Host "  Issues : $verifyFailed files" -ForegroundColor Red
}
Write-Host ""

if ($restoreFailed -eq 0 -and $verifyFailed -eq 0) {
    Write-Success "✅ ROLLBACK COMPLETED SUCCESSFULLY!"
} else {
    Write-Warning "⚠️  ROLLBACK COMPLETED WITH ISSUES!"
    Write-Host ""
    Write-Warning "Please review failed files and restore manually if needed"
}

Write-Host ""

# ============================================================
# NEXT STEPS
# ============================================================

Write-Info "Next Steps:"
Write-Host "  1. Test application in browser"
Write-Host "  2. Verify old functionality works"
Write-Host "  3. Check Laravel logs for errors"
Write-Host "  4. If stable, consider rolling back database (99_rollback.sql)"
Write-Host ""

# Offer to open log
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
Write-Info "Rollback script completed"
Write-Host ""

# ============================================================
# CLEANUP INFORMATION
# ============================================================

Write-Host "========================================" -ForegroundColor Gray
Write-Host "BACKUP CLEANUP" -ForegroundColor Gray
Write-Host "========================================" -ForegroundColor Gray
Write-Host ""
Write-Host "Pre-rollback backup location:" -ForegroundColor Gray
Write-Host "  $PreRollbackBackup" -ForegroundColor White
Write-Host ""
Write-Host "You can delete this backup after confirming rollback is successful" -ForegroundColor Gray
Write-Host ""
