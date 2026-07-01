# 📦 Manual Deployment Guide - Multi Product Weighing Enhancement

**Tanggal Pembuatan:** 2026-07-01  
**Versi:** 1.0  
**Target Environment:** Production  
**Estimasi Waktu:** 45-60 menit (termasuk testing)

---

## 📋 Table of Contents

1. [Overview Perubahan](#overview-perubahan)
2. [Pre-Deployment Checklist](#pre-deployment-checklist)
3. [Fase 1: Backup](#fase-1-backup)
4. [Fase 2: Database Schema Changes](#fase-2-database-schema-changes)
5. [Fase 3: Deploy Application Files](#fase-3-deploy-application-files)
6. [Fase 4: Post-Deployment](#fase-4-post-deployment)
7. [Fase 5: Testing & Verification](#fase-5-testing--verification)
8. [Fase 6: Monitoring](#fase-6-monitoring)
9. [Rollback Procedures](#rollback-procedures)
10. [Troubleshooting](#troubleshooting)

---

## Overview Perubahan

### 🎯 Tujuan Perubahan

Mengubah logika approval pada Multi Product Weighing dari **per-product range validation** menjadi **total range validation** untuk mengurangi jumlah approval yang diperlukan.

### 📊 Perubahan Utama

#### 1. **Business Logic Changes**

- **SEBELUM:** Setiap produk divalidasi terhadap range masing-masing
    - Produk A: 950-1050 kg → jika di luar range, butuh approval
    - Produk B: 900-1100 kg → jika di luar range, butuh approval
    - Produk C: 800-900 kg → jika di luar range, butuh approval
    - **Total approval bisa 3x jika semua out of range**

- **SESUDAH:** Total netto divalidasi terhadap total range
    - Total Range: 2650-3050 kg (sum dari semua min/max)
    - Net Weight: 2800 kg → **IN RANGE, tidak butuh approval**
    - **Hanya 1 approval untuk keseluruhan transaksi**

#### 2. **Validation Rule Changes**

- **SEBELUM:** SPM harus dari truk DAN driver yang sama
- **SESUDAH:** SPM hanya perlu dari truk yang sama, driver boleh berbeda
- Normalisasi plat nomor: spasi dan huruf besar/kecil diabaikan
    - Contoh: "B 123CD" = "b123 cd" = "B123CD"

#### 3. **Database Schema Changes**

- Tambah 2 kolom baru di tabel `trscale_headers`:
    - `total_range_min` (DECIMAL 10,2)
    - `total_range_max` (DECIMAL 10,2)

#### 4. **UI/UX Enhancements**

- Tampilan range lengkap di semua modal (approve, reject, detail)
- Footer dengan Total Range Netto
- Status badge (In Range / Out of Range)
- Print receipt dengan informasi range

---

## Pre-Deployment Checklist

### ✅ Persiapan

- [ ] Akses ke Production Database (SQL Server Management Studio / Azure Data Studio)
- [ ] Akses ke Production Server (RDP / SSH / FTP)
- [ ] Backup drive dengan space cukup (minimal 5GB)
- [ ] Maintenance window sudah dijadwalkan
- [ ] User sudah diinformasikan akan ada downtime (opsional)
- [ ] Tim standby untuk monitoring post-deployment

### ✅ Tools yang Dibutuhkan

- [ ] SQL Server Management Studio / Azure Data Studio
- [ ] Text Editor (VS Code / Notepad++)
- [ ] FTP Client (FileZilla / WinSCP) - jika remote
- [ ] PowerShell / Command Prompt

### ✅ Dokumen Referensi

- [ ] Connection string database production
- [ ] Credentials server production
- [ ] Dokumen ini (DEPLOYMENT_MANUAL_MULTI_PRODUCT.md)

---

## Fase 1: Backup

### 1.1 Backup Database Tables ⚠️ **WAJIB!**

```sql
-- ============================================================
-- BACKUP DATABASE TABLES
-- Jalankan di SQL Server Management Studio / Azure Data Studio
-- Database: [nama_database_production]
-- ============================================================

-- 1. Backup tabel trscale_headers (tabel utama yang akan berubah)
SELECT *
INTO trscale_headers_backup_20260701
FROM trscale_headers;

-- 2. Backup tabel trscale_details (untuk safety)
SELECT *
INTO trscale_details_backup_20260701
FROM trscale_details;

-- 3. Backup tabel trscale_approvals (untuk safety)
SELECT *
INTO trscale_approvals_backup_20260701
FROM trscale_approvals;

-- 4. Verifikasi backup berhasil
SELECT COUNT(*) as total_headers
FROM trscale_headers_backup_20260701;
-- Expected: [sesuai jumlah data production]

SELECT COUNT(*) as total_details
FROM trscale_details_backup_20260701;
-- Expected: [sesuai jumlah data production]

SELECT COUNT(*) as total_approvals
FROM trscale_approvals_backup_20260701;
-- Expected: [sesuai jumlah data production]

-- 5. Catat hasilnya untuk referensi
-- Headers: ___________ rows
-- Details: ___________ rows
-- Approvals: ___________ rows
```

**📝 Catat:**

- ✅ Backup berhasil pada: ******\_****** (tanggal & waktu)
- ✅ Total rows headers: ******\_******
- ✅ Total rows details: ******\_******
- ✅ Total rows approvals: ******\_******

### 1.2 Backup Application Files

**Lokasi Production:** `d:\project\web\Logistic_App`

#### Option A: Backup via PowerShell (Recommended)

```powershell
# ============================================================
# BACKUP APPLICATION FILES
# Jalankan di PowerShell dengan Administrator
# ============================================================

# Set lokasi
cd d:\project\web\Logistic_App

# Buat folder backup dengan timestamp
$backupFolder = "D:\backups\Logistic_App_backup_$(Get-Date -Format 'yyyyMMdd_HHmmss')"
New-Item -ItemType Directory -Force -Path $backupFolder

# Backup specific files yang akan diganti
$filesToBackup = @(
    "app\Models\TrscaleHeader.php",
    "app\Services\MultiProductWeighingService.php",
    "app\Livewire\MultiProductWeighingIn.php",
    "resources\views\livewire\multi-product-weighing-in.blade.php",
    "resources\views\livewire\multi-product-weighing-out.blade.php",
    "resources\views\livewire\multi-product-approval.blade.php",
    "resources\views\cetakoutmp.blade.php"
)

foreach ($file in $filesToBackup) {
    $sourcePath = Join-Path "d:\project\web\Logistic_App" $file
    $destPath = Join-Path $backupFolder $file
    $destDir = Split-Path $destPath -Parent

    # Buat directory structure
    if (!(Test-Path $destDir)) {
        New-Item -ItemType Directory -Force -Path $destDir | Out-Null
    }

    # Copy file
    Copy-Item $sourcePath -Destination $destPath
    Write-Host "✓ Backed up: $file" -ForegroundColor Green
}

Write-Host "`n✅ Backup completed at: $backupFolder" -ForegroundColor Cyan
```

#### Option B: Backup Manual (via Windows Explorer)

1. Buka `d:\project\web\Logistic_App`
2. Buat folder: `D:\backups\Logistic_App_backup_[YYYYMMDD]`
3. Copy 7 files berikut ke backup folder (maintain structure):
    ```
    app\Models\TrscaleHeader.php
    app\Services\MultiProductWeighingService.php
    app\Livewire\MultiProductWeighingIn.php
    resources\views\livewire\multi-product-weighing-in.blade.php
    resources\views\livewire\multi-product-weighing-out.blade.php
    resources\views\livewire\multi-product-approval.blade.php
    resources\views\cetakoutmp.blade.php
    ```

**📝 Catat:**

- ✅ Files backed up to: ****************\_****************
- ✅ Backup completed pada: ******\_****** (tanggal & waktu)

---

## Fase 2: Database Schema Changes

### 2.1 Tambah Kolom Baru di `trscale_headers`

```sql
-- ============================================================
-- ADD NEW COLUMNS TO trscale_headers
-- Kolom untuk menyimpan total range (min & max)
-- ============================================================

USE [nama_database_production];  -- ⚠️ GANTI dengan nama database Anda!
GO

-- Cek apakah kolom sudah ada (safety check)
IF NOT EXISTS (
    SELECT *
    FROM INFORMATION_SCHEMA.COLUMNS
    WHERE TABLE_NAME = 'trscale_headers'
    AND COLUMN_NAME = 'total_range_min'
)
BEGIN
    ALTER TABLE trscale_headers
    ADD total_range_min DECIMAL(10,2) NULL;

    PRINT '✓ Kolom total_range_min berhasil ditambahkan';
END
ELSE
BEGIN
    PRINT '⚠ Kolom total_range_min sudah ada, skip...';
END
GO

IF NOT EXISTS (
    SELECT *
    FROM INFORMATION_SCHEMA.COLUMNS
    WHERE TABLE_NAME = 'trscale_headers'
    AND COLUMN_NAME = 'total_range_max'
)
BEGIN
    ALTER TABLE trscale_headers
    ADD total_range_max DECIMAL(10,2) NULL;

    PRINT '✓ Kolom total_range_max berhasil ditambahkan';
END
ELSE
BEGIN
    PRINT '⚠ Kolom total_range_max sudah ada, skip...';
END
GO

-- ============================================================
-- VERIFICATION QUERY
-- Pastikan kolom berhasil ditambahkan
-- ============================================================

SELECT
    COLUMN_NAME as [Nama Kolom],
    DATA_TYPE as [Tipe Data],
    CHARACTER_MAXIMUM_LENGTH as [Max Length],
    NUMERIC_PRECISION as [Precision],
    NUMERIC_SCALE as [Scale],
    IS_NULLABLE as [Nullable]
FROM INFORMATION_SCHEMA.COLUMNS
WHERE TABLE_NAME = 'trscale_headers'
AND COLUMN_NAME IN ('total_range_min', 'total_range_max')
ORDER BY ORDINAL_POSITION;

-- Expected Result:
-- ┌─────────────────┬───────────┬────────────┬───────────┬───────┬──────────┐
-- │ Nama Kolom      │ Tipe Data │ Max Length │ Precision │ Scale │ Nullable │
-- ├─────────────────┼───────────┼────────────┼───────────┼───────┼──────────┤
-- │ total_range_min │ decimal   │ NULL       │ 10        │ 2     │ YES      │
-- │ total_range_max │ decimal   │ NULL       │ 10        │ 2     │ YES      │
-- └─────────────────┴───────────┴────────────┴───────────┴───────┴──────────┘
```

**📝 Verification Checklist:**

- [ ] Query executed successfully (no errors)
- [ ] `total_range_min` ada dengan type DECIMAL(10,2)
- [ ] `total_range_max` ada dengan type DECIMAL(10,2)
- [ ] Kedua kolom nullable (IS_NULLABLE = YES)

**📸 Screenshot:** Ambil screenshot hasil verification query untuk dokumentasi

---

## Fase 3: Deploy Application Files

### 3.1 Daftar Files yang Harus Di-Deploy

Total **7 files** yang perlu di-update:

| #   | File Path                                                       | Kategori | Perubahan Utama                                                   |
| --- | --------------------------------------------------------------- | -------- | ----------------------------------------------------------------- |
| 1   | `app/Models/TrscaleHeader.php`                                  | Model    | Tambah `total_range_min`, `total_range_max` di $fillable & $casts |
| 2   | `app/Services/MultiProductWeighingService.php`                  | Service  | Logic: total range validation, normalisasi carID                  |
| 3   | `app/Livewire/MultiProductWeighingIn.php`                       | Livewire | Validation: hanya cek carID, bukan driver                         |
| 4   | `resources/views/livewire/multi-product-weighing-in.blade.php`  | View     | UI: info driver boleh beda, contoh normalisasi                    |
| 5   | `resources/views/livewire/multi-product-weighing-out.blade.php` | View     | UI: tampilan range per product + total range                      |
| 6   | `resources/views/livewire/multi-product-approval.blade.php`     | View     | UI: modal approve/reject dengan data lengkap                      |
| 7   | `resources/views/cetakoutmp.blade.php`                          | View     | UI: print receipt dengan range info                               |

### 3.2 Deployment Steps

#### Step 1: Siapkan Deployment Package (Di Local Development)

```powershell
# ============================================================
# CREATE DEPLOYMENT PACKAGE
# Jalankan di local development machine
# ============================================================

cd d:\project\web\Logistic_App

# Buat folder untuk deployment package
$deployFolder = ".\deployment_multiproduct_$(Get-Date -Format 'yyyyMMdd_HHmmss')"
New-Item -ItemType Directory -Force -Path $deployFolder

# List files to deploy
$filesToDeploy = @(
    "app\Models\TrscaleHeader.php",
    "app\Services\MultiProductWeighingService.php",
    "app\Livewire\MultiProductWeighingIn.php",
    "resources\views\livewire\multi-product-weighing-in.blade.php",
    "resources\views\livewire\multi-product-weighing-out.blade.php",
    "resources\views\livewire\multi-product-approval.blade.php",
    "resources\views\cetakoutmp.blade.php"
)

# Copy files dengan maintain structure
foreach ($file in $filesToDeploy) {
    $sourcePath = $file
    $destPath = Join-Path $deployFolder $file
    $destDir = Split-Path $destPath -Parent

    if (!(Test-Path $destDir)) {
        New-Item -ItemType Directory -Force -Path $destDir | Out-Null
    }

    Copy-Item $sourcePath -Destination $destPath -Force
    Write-Host "✓ Copied: $file" -ForegroundColor Green
}

# Compress ke ZIP
$zipPath = ".\deployment_multiproduct_$(Get-Date -Format 'yyyyMMdd_HHmmss').zip"
Compress-Archive -Path "$deployFolder\*" -DestinationPath $zipPath -Force

Write-Host "`n✅ Deployment package created: $zipPath" -ForegroundColor Cyan
Write-Host "📦 Transfer file ZIP ini ke server production" -ForegroundColor Yellow
```

#### Step 2: Upload ke Production Server

**Option A: Via FTP/SFTP**

1. Buka FileZilla / WinSCP
2. Connect ke production server
3. Upload ZIP file ke server (misalnya ke `D:\temp\`)
4. Extract di server

**Option B: Via Remote Desktop**

1. Copy ZIP file via RDP clipboard / shared folder
2. Paste ke production server
3. Extract di lokasi temporary (misalnya `D:\temp\deployment_multiproduct`)

**Option C: Via Git (jika menggunakan version control)**

```powershell
# Di production server
cd d:\project\web\Logistic_App
git pull origin main  # atau branch yang sesuai
```

#### Step 3: Deploy Files ke Lokasi Target

```powershell
# ============================================================
# DEPLOY FILES TO PRODUCTION
# Jalankan di production server
# ============================================================

# Set source (lokasi extract ZIP) dan target
$sourceBase = "D:\temp\deployment_multiproduct"  # Sesuaikan path
$targetBase = "d:\project\web\Logistic_App"

# Files to copy
$files = @(
    "app\Models\TrscaleHeader.php",
    "app\Services\MultiProductWeighingService.php",
    "app\Livewire\MultiProductWeighingIn.php",
    "resources\views\livewire\multi-product-weighing-in.blade.php",
    "resources\views\livewire\multi-product-weighing-out.blade.php",
    "resources\views\livewire\multi-product-approval.blade.php",
    "resources\views\cetakoutmp.blade.php"
)

# Copy each file
foreach ($file in $files) {
    $source = Join-Path $sourceBase $file
    $target = Join-Path $targetBase $file

    if (Test-Path $source) {
        Copy-Item $source -Destination $target -Force
        Write-Host "✓ Deployed: $file" -ForegroundColor Green
    } else {
        Write-Host "✗ File not found: $source" -ForegroundColor Red
    }
}

Write-Host "`n✅ All files deployed!" -ForegroundColor Cyan
```

**📝 Deployment Checklist:**

- [ ] TrscaleHeader.php deployed
- [ ] MultiProductWeighingService.php deployed
- [ ] MultiProductWeighingIn.php deployed
- [ ] multi-product-weighing-in.blade.php deployed
- [ ] multi-product-weighing-out.blade.php deployed
- [ ] multi-product-approval.blade.php deployed
- [ ] cetakoutmp.blade.php deployed

---

## Fase 4: Post-Deployment

### 4.1 Clear All Caches (WAJIB!)

```powershell
# ============================================================
# CLEAR LARAVEL CACHES
# Jalankan di production server
# ============================================================

cd d:\project\web\Logistic_App

# Clear all caches
php artisan optimize:clear

# Atau lebih detail:
php artisan config:clear
php artisan cache:clear
php artisan route:clear
php artisan view:clear
php artisan event:clear

# Optional: Optimize untuk production (setelah yakin tidak ada error)
# php artisan config:cache
# php artisan route:cache
# php artisan view:cache
```

**Expected Output:**

```
INFO  Clearing cached bootstrap files.

  events ........................................................... 14ms DONE
  views ............................................................ 80ms DONE
  cache ............................................................ 71ms DONE
  route ............................................................. 1ms DONE
  config ............................................................ 1ms DONE
  compiled .......................................................... 3ms DONE
```

**📝 Catat:**

- [ ] Cache cleared successfully
- [ ] No errors during cache clear
- [ ] Timestamp: ******\_******

### 4.2 Verify File Permissions (Optional, untuk Linux/Unix)

Jika menggunakan Linux server:

```bash
# Set proper permissions
cd /var/www/Logistic_App
chmod -R 755 app resources
chown -R www-data:www-data app resources
```

---

## Fase 5: Testing & Verification

### 5.1 Database Verification

```sql
-- ============================================================
-- VERIFY DATABASE CHANGES
-- ============================================================

-- 1. Cek struktur kolom baru
SELECT
    COLUMN_NAME,
    DATA_TYPE,
    NUMERIC_PRECISION,
    NUMERIC_SCALE,
    IS_NULLABLE
FROM INFORMATION_SCHEMA.COLUMNS
WHERE TABLE_NAME = 'trscale_headers'
AND COLUMN_NAME IN ('total_range_min', 'total_range_max');

-- Expected: 2 rows returned (total_range_min & total_range_max)

-- 2. Cek apakah ada data existing (seharusnya NULL untuk data lama)
SELECT TOP 10
    hdrID,
    netto,
    total_range_min,
    total_range_max,
    created_at
FROM trscale_headers
ORDER BY created_at DESC;

-- Expected:
-- - Data lama: total_range_min & total_range_max = NULL
-- - Data baru (setelah deployment): akan terisi setelah ada weighing out
```

**📝 Checklist:**

- [ ] Kolom `total_range_min` exists
- [ ] Kolom `total_range_max` exists
- [ ] Data lama tidak corrupt (NULL di kolom baru)

### 5.2 Application Verification

#### Test 1: Basic Page Load (Tidak Error)

Akses halaman-halaman berikut dan pastikan tidak ada error 500:

1. **Multi Product Weighing In**
    - URL: `/multi-product-weighing-in`
    - Expected: Halaman load normal, tabel SPM tampil
    - [ ] ✅ Page loads without error

2. **Multi Product Weighing Out**
    - URL: `/multi-product-weighing-out`
    - Expected: Halaman load normal, tabel transaksi tampil dengan kolom Total Range
    - [ ] ✅ Page loads without error

3. **Multi Product Approval**
    - URL: `/multi-product-approval`
    - Expected: Halaman load normal
    - [ ] ✅ Page loads without error

#### Test 2: Functional Test - Weighing In

**Scenario:** Pilih Multiple SPM dengan carID Sama, Driver Berbeda

1. Akses halaman Multi Product Weighing In
2. Pilih SPM 1: carID = "B 1234 AB", driver = "Budi"
3. Pilih SPM 2: carID = "B1234AB", driver = "Joko" (plat sama tapi beda format & driver)

**Expected Result:**

- [ ] ✅ Kedua SPM berhasil dipilih (tidak ada error)
- [ ] ✅ Info message muncul: "Driver boleh berbeda. Spasi dan huruf besar/kecil pada plat nomor akan diabaikan"
- [ ] ✅ Button "Proses Timbang Masuk" enabled

4. Coba pilih SPM 3: carID = "B 5678 CD", driver = "Budi"

**Expected Result:**

- [ ] ✅ Error muncul: "Semua SPM harus dari truk/kendaraan yang sama (No. Polisi harus sama). Driver boleh berbeda."
- [ ] ✅ SPM 3 tidak terpilih

5. Hapus SPM 3, lanjutkan proses timbang masuk dengan SPM 1 & 2
6. Isi data:
    - Driver: [isi sesuai]
    - No. Kendaraan: [isi sesuai]
    - Customer ID: [isi sesuai]
    - Tare Weight: [isi sesuai]
    - Timbangan: [isi sesuai]

7. Klik "Proses Timbang Masuk"

**Expected Result:**

- [ ] ✅ Proses berhasil
- [ ] ✅ Success message muncul
- [ ] ✅ Data tersimpan di database

8. Verifikasi database:

```sql
-- Cari transaksi yang baru dibuat
SELECT TOP 1
    hdrID,
    carID,
    driver,
    tare,
    created_at
FROM trscale_headers
ORDER BY created_at DESC;
```

**Expected:**

- [ ] ✅ Data header tersimpan
- [ ] ✅ carID & driver sesuai input
- [ ] ✅ total_range_min & total_range_max masih NULL (belum weighing out)

#### Test 3: Functional Test - Weighing Out

**Scenario:** Proses Timbang Keluar & Validasi Total Range

1. Akses halaman Multi Product Weighing Out
2. Cari transaksi yang baru ditimbang masuk (Test 2)
3. Klik "Detail" atau button untuk proses weighing out
4. Lihat tampilan modal/halaman:

**Expected Display:**

- [ ] ✅ Tabel product dengan kolom:
    - Product Name
    - Qty Karung
    - Theoretical Weight
    - Range Min (per kemasan)
    - Range Max (per kemasan)
- [ ] ✅ Footer tabel menampilkan:
    - **Total Range Netto**
    - Total Range Min: XXX kg
    - Total Range Max: XXX kg

5. Input data weighing out:
    - Gross Weight: [isi sesuai]
    - Cek Net Weight otomatis terhitung

6. Klik "Proses Timbang Keluar"

**Expected Result:**

- [ ] ✅ Proses berhasil
- [ ] ✅ Status tampil: "In Range" atau "Out of Range"
- [ ] ✅ Jika "Out of Range" → masuk queue approval
- [ ] ✅ Jika "In Range" → langsung completed

7. Verifikasi database:

```sql
-- Cek data yang baru di-weigh out
SELECT TOP 1
    hdrID,
    netto,
    total_range_min,
    total_range_max,
    CASE
        WHEN netto >= total_range_min AND netto <= total_range_max
        THEN 'In Range'
        ELSE 'Out of Range'
    END as calculated_status
FROM trscale_headers
WHERE total_range_min IS NOT NULL
ORDER BY created_at DESC;
```

**Expected:**

- [ ] ✅ `total_range_min` terisi (bukan NULL)
- [ ] ✅ `total_range_max` terisi (bukan NULL)
- [ ] ✅ `netto` terisi
- [ ] ✅ Status calculated sesuai logic

#### Test 4: Functional Test - Approval (Jika Out of Range)

**Scenario:** Approve/Reject Transaksi Out of Range

1. Buat transaksi yang out of range (sengaja):
    - Theoretical: 1000 kg
    - Range: 950-1050 kg
    - Actual: 1100 kg (di luar range)

2. Akses halaman Multi Product Approval
3. Cari transaksi yang out of range
4. Klik button "Detail" / "Approve" / "Reject"

**Expected Modal Display:**

- [ ] ✅ Alert box menampilkan:
    - Tare: XXX kg
    - Gross: XXX kg
    - Net: XXX kg
    - Theoretical: XXX kg
- [ ] ✅ Tabel product dengan kolom:
    - Product
    - Theoretical
    - Actual Weight
    - Avg per Karung
    - Range Min (per kemasan)
    - Range Max (per kemasan)
- [ ] ✅ Footer tabel:
    - **Total Range Netto**
    - Min: XXX kg
    - Max: XXX kg
- [ ] ✅ Status badge: "Out of Range" (warna merah/warning)

5. Klik "Approve" atau "Reject"

**Expected Result:**

- [ ] ✅ Proses berhasil
- [ ] ✅ Transaksi pindah dari queue approval
- [ ] ✅ Status updated di database

#### Test 5: Print Receipt

**Scenario:** Cetak Tiket Setelah Weighing Out

1. Pilih transaksi yang sudah complete (weighing out done)
2. Klik button "Print" / "Cetak"

**Expected Print Preview:**

- [ ] ✅ Header transaksi (hdrID, date, driver, carID)
- [ ] ✅ Tabel product dengan kolom:
    - Product Name
    - Qty Karung
    - Theoretical
    - Actual
    - **Range Min**
    - **Range Max**
- [ ] ✅ Footer section:
    - Tare: XXX kg
    - Gross: XXX kg
    - Net: XXX kg
    - **Total Range Netto:**
        - Min: XXX kg
        - Max: XXX kg
- [ ] ✅ Status row: "In Range" atau "Out of Range"

3. Print atau save as PDF

**📝 Catat:**

- [ ] Print preview looks good
- [ ] All data displayed correctly
- [ ] No layout issues

---

## Fase 6: Monitoring

### 6.1 Monitor Laravel Logs

```powershell
# ============================================================
# MONITOR APPLICATION LOGS
# Jalankan di production server
# ============================================================

# Real-time monitoring
Get-Content "d:\project\web\Logistic_App\storage\logs\laravel.log" -Tail 50 -Wait

# Atau buka file langsung:
# storage\logs\laravel-YYYY-MM-DD.log
```

### 6.2 Keyword Error yang Harus Diwaspadai

**Database Errors:**

- `SQLSTATE[42S22]` → Kolom tidak ditemukan
    - **Cause:** ALTER TABLE belum dijalankan
    - **Fix:** Run Fase 2 (Database Schema Changes)

- `SQLSTATE[HY000]` → General database error
    - **Cause:** Connection issue atau query invalid
    - **Fix:** Check connection string, verify database accessible

**Application Errors:**

- `Class 'App\Services\MultiProductWeighingService' not found`
    - **Cause:** File tidak ter-copy atau cache belum di-clear
    - **Fix:** Re-copy file, run `php artisan optimize:clear`

- `Call to undefined method`
    - **Cause:** Code tidak sync atau cache issue
    - **Fix:** Clear cache, restart PHP-FPM / web server

- `View [livewire.multi-product-weighing-in] not found`
    - **Cause:** View file tidak ter-copy
    - **Fix:** Re-copy view files, clear view cache

### 6.3 Monitor Database Activity

```sql
-- ============================================================
-- MONITOR NEW TRANSACTIONS
-- Jalankan setiap 5-10 menit selama 1 jam pertama
-- ============================================================

-- 1. Cek transaksi baru dengan total_range terisi
SELECT
    hdrID,
    carID,
    driver,
    netto,
    total_range_min,
    total_range_max,
    CASE
        WHEN netto >= total_range_min AND netto <= total_range_max
        THEN 'In Range'
        ELSE 'Out of Range'
    END as status,
    created_at
FROM trscale_headers
WHERE total_range_min IS NOT NULL
AND created_at >= DATEADD(hour, -1, GETDATE())
ORDER BY created_at DESC;

-- 2. Cek ada error atau anomali
SELECT
    COUNT(*) as total_transactions,
    SUM(CASE WHEN total_range_min IS NULL THEN 1 ELSE 0 END) as without_range,
    SUM(CASE WHEN total_range_min IS NOT NULL THEN 1 ELSE 0 END) as with_range
FROM trscale_headers
WHERE created_at >= DATEADD(hour, -1, GETDATE());
```

### 6.4 Performance Monitoring

**Check Response Time:**

- Multi Product Weighing In page: < 2 seconds
- Multi Product Weighing Out page: < 3 seconds
- Multi Product Approval page: < 3 seconds

**Check Database Query Performance:**

```sql
-- Cek slow queries (jika ada monitoring tool)
-- Atau gunakan SQL Server Profiler
```

### 6.5 Monitoring Checklist (First 24 Hours)

**Jam Pertama:**

- [ ] Check logs every 10 minutes
- [ ] Monitor 3-5 test transactions
- [ ] Verify no errors in logs
- [ ] Confirm total_range fields populated correctly

**3 Jam Pertama:**

- [ ] Check logs every 30 minutes
- [ ] Monitor ~10-20 real transactions
- [ ] Check approval flow working correctly
- [ ] Verify print receipts working

**24 Jam Pertama:**

- [ ] Check logs every 2-3 hours
- [ ] Monitor all transactions
- [ ] Collect user feedback
- [ ] Document any issues

---

## Rollback Procedures

### When to Rollback? ⚠️

Rollback jika:

- ❌ Error 500 di semua halaman multi-product
- ❌ Database corruption atau data loss
- ❌ Critical bug yang menghalangi operations
- ❌ Performance degradation severe (>10x slower)

### Rollback Step-by-Step

#### Option 1: Rollback Application Files Only (Recommended First)

```powershell
# ============================================================
# ROLLBACK APPLICATION FILES
# Jalankan di production server
# ============================================================

cd d:\project\web\Logistic_App

# Restore dari backup
$backupFolder = "D:\backups\Logistic_App_backup_[TIMESTAMP]"  # Sesuaikan path

# List files to restore
$filesToRestore = @(
    "app\Models\TrscaleHeader.php",
    "app\Services\MultiProductWeighingService.php",
    "app\Livewire\MultiProductWeighingIn.php",
    "resources\views\livewire\multi-product-weighing-in.blade.php",
    "resources\views\livewire\multi-product-weighing-out.blade.php",
    "resources\views\livewire\multi-product-approval.blade.php",
    "resources\views\cetakoutmp.blade.php"
)

foreach ($file in $filesToRestore) {
    $source = Join-Path $backupFolder $file
    $target = Join-Path "d:\project\web\Logistic_App" $file

    if (Test-Path $source) {
        Copy-Item $source -Destination $target -Force
        Write-Host "✓ Restored: $file" -ForegroundColor Yellow
    } else {
        Write-Host "✗ Backup not found: $source" -ForegroundColor Red
    }
}

# Clear cache WAJIB setelah rollback!
php artisan optimize:clear

Write-Host "`n✅ Rollback completed!" -ForegroundColor Cyan
Write-Host "⚠ Verify application working before proceeding" -ForegroundColor Yellow
```

**Verification After Rollback:**

- [ ] Multi Product pages accessible
- [ ] No errors in logs
- [ ] Old logic working (per-product validation)

#### Option 2: Rollback Database (⚠️ LAST RESORT!)

**⚠️ WARNING:** Hanya lakukan ini jika:

- Database corrupt setelah deployment
- Ada data loss
- Application rollback (Option 1) tidak cukup

```sql
-- ============================================================
-- ROLLBACK DATABASE - REMOVE NEW COLUMNS
-- ⚠️ HATI-HATI: Ini akan menghapus data di kolom baru!
-- ============================================================

USE [nama_database_production];
GO

-- Step 1: Backup current state (untuk jaga-jaga)
SELECT *
INTO trscale_headers_before_rollback_$(Get-Date -Format 'yyyyMMdd_HHmmss')
FROM trscale_headers;

-- Step 2: Drop kolom baru
ALTER TABLE trscale_headers DROP COLUMN total_range_min;
ALTER TABLE trscale_headers DROP COLUMN total_range_max;

-- Step 3: Verify
SELECT COLUMN_NAME
FROM INFORMATION_SCHEMA.COLUMNS
WHERE TABLE_NAME = 'trscale_headers'
AND COLUMN_NAME IN ('total_range_min', 'total_range_max');

-- Expected: 0 rows (kolom sudah hilang)
```

**Full Database Restore (EXTREME LAST RESORT):**

```sql
-- ⚠️⚠️⚠️ ONLY if database completely corrupted ⚠️⚠️⚠️
-- This will lose ALL data after backup!

-- Drop current table
DROP TABLE trscale_headers;

-- Restore from backup
SELECT *
INTO trscale_headers
FROM trscale_headers_backup_20260701;

-- Do the same for other tables if needed
```

### Post-Rollback Checklist

- [ ] Application accessible
- [ ] No errors in logs
- [ ] Test basic functions (weighing in, weighing out)
- [ ] Inform team about rollback
- [ ] Document rollback reason
- [ ] Plan for re-deployment (fix issues first)

---

## Troubleshooting

### Problem 1: Error "Column 'total_range_min' not found"

**Symptoms:**

- Error saat akses weighing out page
- Error: `SQLSTATE[42S22]: Column not found: 'total_range_min'`

**Root Cause:**

- ALTER TABLE belum dijalankan atau gagal

**Solution:**

1. Check database:
    ```sql
    SELECT COLUMN_NAME
    FROM INFORMATION_SCHEMA.COLUMNS
    WHERE TABLE_NAME = 'trscale_headers'
    AND COLUMN_NAME IN ('total_range_min', 'total_range_max');
    ```
2. Jika tidak ada hasil → Run Fase 2 (Database Schema Changes)
3. Clear cache: `php artisan optimize:clear`

---

### Problem 2: Error "Semua SPM harus dari truk dan driver yang sama" masih muncul

**Symptoms:**

- Tidak bisa pilih SPM dengan driver berbeda
- Error message lama masih muncul

**Root Cause:**

- File `MultiProductWeighingIn.php` belum ter-update
- Cache belum di-clear

**Solution:**

1. Verify file deployed:
    ```powershell
    Get-Content "app\Livewire\MultiProductWeighingIn.php" | Select-String "driver yang sama"
    ```

    - Jika ada match → file belum ter-update
2. Re-copy file dari deployment package
3. Clear cache: `php artisan optimize:clear`
4. Restart web server (jika perlu)

---

### Problem 3: Modal Approval tidak tampilkan Total Range

**Symptoms:**

- Modal approve/reject tidak ada info Total Range
- Footer tidak muncul

**Root Cause:**

- View file belum ter-update
- Browser cache

**Solution:**

1. Re-copy view file:
    - `resources\views\livewire\multi-product-approval.blade.php`
2. Clear Laravel cache: `php artisan view:clear`
3. Clear browser cache (Ctrl + F5)
4. Check file content:
    ```powershell
    Get-Content "resources\views\livewire\multi-product-approval.blade.php" | Select-String "Total Range"
    ```

---

### Problem 4: Print Receipt tidak ada Range info

**Symptoms:**

- Print receipt tidak ada kolom Range Min/Max
- Footer tidak ada Total Range Netto

**Root Cause:**

- View file `cetakoutmp.blade.php` belum ter-update

**Solution:**

1. Re-copy view file: `resources\views\cetakoutmp.blade.php`
2. Clear view cache: `php artisan view:clear`
3. Test print lagi

---

### Problem 5: Slow Performance after deployment

**Symptoms:**

- Page load > 5 seconds
- Database query slow

**Root Cause:**

- Cache not optimized
- Missing indexes

**Solution:**

1. Optimize caches:
    ```powershell
    php artisan config:cache
    php artisan route:cache
    php artisan view:cache
    ```
2. Check database indexes (optional):
    ```sql
    -- Check if indexes exist on trscale_headers
    EXEC sp_helpindex 'trscale_headers';
    ```
3. Monitor slow queries and optimize if needed

---

### Problem 6: Data lama corrupt setelah migration

**Symptoms:**

- Data yang sudah ada sebelum deployment jadi error
- Tidak bisa akses transaksi lama

**Root Cause:**

- Code expect `total_range_min/max` to be NOT NULL for old data

**Solution:**

1. Verify old data:
    ```sql
    SELECT COUNT(*) as total_old_data
    FROM trscale_headers
    WHERE total_range_min IS NULL
    AND created_at < '2026-07-01';  -- before deployment
    ```
2. Code should handle NULL gracefully (sudah di-handle di code)
3. If still error → check error log untuk spesifik issue

---

## Appendix

### A. Quick Reference Commands

**Clear All Caches:**

```powershell
php artisan optimize:clear
```

**Clear Specific Cache:**

```powershell
php artisan config:clear
php artisan cache:clear
php artisan route:clear
php artisan view:clear
```

**Check Database Columns:**

```sql
SELECT COLUMN_NAME, DATA_TYPE, IS_NULLABLE
FROM INFORMATION_SCHEMA.COLUMNS
WHERE TABLE_NAME = 'trscale_headers'
ORDER BY ORDINAL_POSITION;
```

**Monitor Latest Transactions:**

```sql
SELECT TOP 10 hdrID, netto, total_range_min, total_range_max, created_at
FROM trscale_headers
ORDER BY created_at DESC;
```

### B. Contact Information

**Technical Support:**

- Developer: ************\_\_\_************
- Email: ************\_\_\_************
- Phone: ************\_\_\_************

**Database Administrator:**

- Name: ************\_\_\_************
- Email: ************\_\_\_************
- Phone: ************\_\_\_************

### C. Deployment Log Template

**Deployment Date:** ******\_****** (DD/MM/YYYY HH:MM)  
**Deployed By:** ******\_******  
**Environment:** Production

**Checklist:**

- [ ] Database backup completed
- [ ] Application files backup completed
- [ ] Database schema updated
- [ ] Application files deployed
- [ ] Caches cleared
- [ ] Testing completed
- [ ] Monitoring in place

**Issues Encountered:**

---

---

**Resolution:**

---

---

**Sign-off:**

- Developer: ******\_****** (Signature & Date)
- QA/Tester: ******\_****** (Signature & Date)
- Manager: ******\_****** (Signature & Date)

---

## Document Version History

| Version | Date       | Author | Changes                  |
| ------- | ---------- | ------ | ------------------------ |
| 1.0     | 2026-07-01 | System | Initial deployment guide |

---

**END OF DOCUMENT**

💡 **Tips:**

- Print dokumen ini untuk reference saat deployment
- Checklist bisa di-print dan di-tick manual
- Simpan backup di lokasi aman (external drive / cloud)
- Update dokumen ini jika ada perubahan prosedur

🔒 **Security Reminder:**

- Jangan commit file ini ke Git jika ada sensitive info
- Keep backup credentials secure
- Document all access for audit trail

📞 **Emergency Contact:**

- Jika ada masalah serius saat deployment, segera hubungi team lead
- Jangan panic, ikuti rollback procedures jika perlu
- Document semua yang terjadi untuk post-mortem

**Good luck with the deployment! 🚀**
