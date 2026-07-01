# Deployment Scripts - Multi Product Weighing Enhancement

📦 Kumpulan script untuk deployment manual ke production environment.

---

## 📂 Daftar File

### SQL Scripts

| File                          | Deskripsi                                            | Kapan Digunakan                |
| ----------------------------- | ---------------------------------------------------- | ------------------------------ |
| `01_backup_tables.sql`        | Backup semua tabel yang terpengaruh                  | **WAJIB** sebelum deployment   |
| `02_add_columns.sql`          | Tambah kolom `total_range_min` dan `total_range_max` | Saat deployment schema changes |
| `03_verification_queries.sql` | Verifikasi deployment berhasil                       | Setelah deployment selesai     |
| `99_rollback.sql`             | Rollback database schema changes                     | Jika ada masalah kritis        |

### PowerShell Scripts

| File                 | Deskripsi                              | Kapan Digunakan  |
| -------------------- | -------------------------------------- | ---------------- |
| `deploy_files.ps1`   | Deploy application files ke production | Saat deployment  |
| `rollback_files.ps1` | Rollback application files             | Jika ada masalah |

---

## 🚀 Quick Start Guide

### 1️⃣ Pre-Deployment (WAJIB!)

```powershell
# Jalankan backup database
# Buka SQL Server Management Studio atau Azure Data Studio
# Execute: 01_backup_tables.sql
```

**Hasil yang diharapkan:**

- 3 backup tables created:
    - `trscale_headers_backup_20260701`
    - `trscale_details_backup_20260701`
    - `trscale_approvals_backup_20260701`

### 2️⃣ Database Schema Changes

```powershell
# Execute: 02_add_columns.sql
```

**Hasil yang diharapkan:**

- Kolom `total_range_min` added
- Kolom `total_range_max` added
- Verification query menampilkan 2 kolom baru

### 3️⃣ Deploy Application Files

```powershell
# Jalankan di PowerShell (Administrator)
cd d:\project\web\Logistic_App\deployment_scripts
.\deploy_files.ps1
```

**Script akan:**

1. ✅ Backup existing files
2. ✅ Verify all 7 files in place
3. ✅ Clear Laravel caches
4. ✅ Run verification

### 4️⃣ Verification

```powershell
# Execute: 03_verification_queries.sql
```

**Script akan check:**

- ✅ Schema changes applied
- ✅ Old data integrity maintained
- ✅ New transactions working
- ✅ Range calculation correct
- ✅ Performance acceptable

### 5️⃣ Testing

Lihat section "Testing Checklist" di `DEPLOYMENT_MANUAL_MULTI_PRODUCT.md`

---

## 🔙 Rollback Procedures

### Rollback Application Files

```powershell
cd d:\project\web\Logistic_App\deployment_scripts
.\rollback_files.ps1
```

Script akan:

1. Cari latest backup (atau pilih manual)
2. Backup current state (pre-rollback)
3. Restore files from backup
4. Clear caches
5. Verify files

### Rollback Database (⚠️ Last Resort!)

```sql
-- Execute: 99_rollback.sql
-- WARNING: This will remove columns and lose data!
```

**HANYA lakukan jika:**

- Database corrupt
- Application rollback tidak cukup
- Ada approval dari team lead

---

## 📋 Execution Order

### Normal Deployment Flow

```
1. 01_backup_tables.sql       (SQL - MANDATORY)
2. 02_add_columns.sql          (SQL)
3. deploy_files.ps1            (PowerShell)
4. 03_verification_queries.sql (SQL)
5. Manual Testing              (Browser)
```

### Rollback Flow

```
1. rollback_files.ps1          (PowerShell - rollback app files)
2. Clear cache manually        (if script fails)
3. Test application            (Browser)
4. 99_rollback.sql             (SQL - only if needed)
```

---

## ⚙️ Script Details

### 01_backup_tables.sql

**Purpose:** Backup database tables sebelum perubahan

**Tables backed up:**

- `trscale_headers` → `trscale_headers_backup_20260701`
- `trscale_details` → `trscale_details_backup_20260701`
- `trscale_approvals` → `trscale_approvals_backup_20260701`

**Output:**

```
========================================
✅ BACKUP COMPLETED SUCCESSFULLY!
========================================

Headers backup  : 1234 rows
Details backup  : 5678 rows
Approvals backup: 89 rows
```

**Verification:**

- Row counts should match original tables
- Oldest/newest record timestamps displayed
- No errors during execution

---

### 02_add_columns.sql

**Purpose:** Tambah 2 kolom baru untuk total range tracking

**Changes:**

- Add `total_range_min` DECIMAL(10,2) NULL
- Add `total_range_max` DECIMAL(10,2) NULL

**Safety Features:**

- Checks if columns already exist (idempotent)
- Doesn't affect existing data
- Nullable columns (no breaking changes)

**Output:**

```
✓ Column total_range_min successfully added!
✓ Column total_range_max successfully added!
```

**Verification:**

- Shows column details (type, precision, nullable)
- Sample data check (old records have NULL)
- Row count unchanged

---

### 03_verification_queries.sql

**Purpose:** Comprehensive post-deployment verification

**Checks:**

1. **Schema Verification**
    - Kolom baru exists
    - Type dan properties correct

2. **Data Integrity**
    - Old records have NULL in new columns
    - No data corruption

3. **New Transactions**
    - New weighing out populates range fields
    - Values are reasonable

4. **Range Calculation**
    - Total range = sum of per-product ranges
    - Logic working correctly

5. **Approval Status**
    - In Range vs Out of Range distribution
    - Status logic correct

6. **Performance**
    - Query execution time < 100ms (excellent)
    - No performance degradation

**Output Example:**

```
✅ VERIFICATION COMPLETED!

Schema: ✓ Both columns exist
Data Integrity: ✓ All old records OK
New Transactions: 15 records with range data
Performance: 45ms (excellent)
```

---

### 99_rollback.sql

**Purpose:** Remove new columns and restore schema

**⚠️ WARNING:**

- This will DELETE data in `total_range_min` and `total_range_max`
- Irreversible (unless you restore from backup)
- Only use as LAST RESORT

**Safety Features:**

- Creates backup before rollback
- 5-second wait before proceeding
- Verification after rollback

**Actions:**

1. Backup current state with timestamp
2. Drop `total_range_min` column
3. Drop `total_range_max` column
4. Verify columns removed
5. Check row count unchanged

**Output:**

```
⚠️  WARNING: This will remove columns and data!
Waiting 5 seconds...
Proceeding with rollback...

✓ Column total_range_min dropped
✓ Column total_range_max dropped
✓ Row count unchanged

✅ ROLLBACK COMPLETED!
```

---

### deploy_files.ps1

**Purpose:** Automated deployment of application files

**Features:**

- Interactive confirmation
- Automatic backup before deploy
- Verification after deploy
- Cache clearing
- Colored output for easy reading

**Steps:**

1. Confirm deployment
2. Backup 7 files to timestamped folder
3. Verify files in place
4. Clear Laravel caches
5. Verify files readable
6. Show summary

**Usage:**

```powershell
.\deploy_files.ps1
```

**Output Example:**

```
========================================
STEP 1: BACKING UP EXISTING FILES
========================================

✓ Backed up: app\Models\TrscaleHeader.php
✓ Backed up: app\Services\MultiProductWeighingService.php
...

Backup Summary:
  Success: 7 files
  Location: D:\backups\Logistic_App_backup_20260701_143022

========================================
✅ DEPLOYMENT COMPLETED!
========================================
```

---

### rollback_files.ps1

**Purpose:** Restore application files from backup

**Features:**

- Auto-detect latest backup
- Or specify backup path manually
- Pre-rollback backup (safety)
- Verification after restore
- Colored output

**Usage:**

```powershell
# Auto-detect latest backup
.\rollback_files.ps1

# Or specify backup path
.\rollback_files.ps1 "D:\backups\Logistic_App_backup_20260701_143022"
```

**Steps:**

1. Locate backup (auto or manual)
2. Verify backup contents
3. Confirm rollback
4. Backup current state (pre-rollback)
5. Restore files from backup
6. Clear caches
7. Verify restored files

**Output Example:**

```
========================================
STEP 1: LOCATING BACKUP
========================================

Available backups:
  [1] Logistic_App_backup_20260701_143022
  [2] Logistic_App_backup_20260701_120000

Select backup number: 1

✓ Using backup: D:\backups\Logistic_App_backup_20260701_143022

...

========================================
✅ ROLLBACK COMPLETED SUCCESSFULLY!
========================================
```

---

## 🔍 Troubleshooting

### Problem: "Column already exists"

**Saat execute:** `02_add_columns.sql`

**Cause:** Script sudah pernah dijalankan

**Solution:** Script idempotent, aman untuk re-run. Skip jika kolom sudah ada.

---

### Problem: "Cannot find backup"

**Saat execute:** `rollback_files.ps1`

**Cause:** Backup folder tidak ada atau salah nama

**Solution:**

```powershell
# Check backups manually
Get-ChildItem "D:\backups" -Directory -Filter "Logistic_App_backup_*"

# Use specific path
.\rollback_files.ps1 "D:\backups\Logistic_App_backup_YYYYMMDD_HHMMSS"
```

---

### Problem: "Failed to clear cache"

**Saat execute:** `deploy_files.ps1` atau `rollback_files.ps1`

**Cause:** PHP tidak ditemukan di PATH atau Laravel error

**Solution:**

```powershell
# Clear manually
cd d:\project\web\Logistic_App
php artisan config:clear
php artisan cache:clear
php artisan route:clear
php artisan view:clear
```

---

### Problem: "File not found"

**Saat execute:** `deploy_files.ps1`

**Cause:** Files belum di-copy ke production

**Solution:**

1. Pastikan git pull sudah dijalankan, atau
2. Copy files manual dari deployment package, atau
3. Re-download files dari repository

---

## 📝 Notes

### Script Compatibility

- **SQL Scripts:** SQL Server 2012 atau lebih baru
- **PowerShell Scripts:** PowerShell 5.1 atau lebih baru (built-in di Windows 10/11)
- **Laravel:** Laravel 8.x atau lebih baru

### Permissions Required

- **SQL:** `db_ddl` (untuk ALTER TABLE) dan `db_datawriter` (untuk backup)
- **PowerShell:** Administrator (untuk write ke D:\backups)
- **Laravel:** Write access ke `storage` dan `bootstrap/cache`

### Backup Retention

**Recommended:**

- Keep database backups for 30 days
- Keep file backups for 7 days
- Delete after confirming stable

**Cleanup Commands:**

```powershell
# Delete backups older than 30 days
Get-ChildItem "D:\backups" -Directory -Filter "Logistic_App_backup_*" |
    Where-Object { $_.CreationTime -lt (Get-Date).AddDays(-30) } |
    Remove-Item -Recurse -Force
```

---

## 📞 Support

Jika ada masalah:

1. **Check Laravel logs:**

    ```
    storage\logs\laravel-YYYY-MM-DD.log
    ```

2. **Check SQL Server logs:**

    ```sql
    EXEC sp_readerrorlog;
    ```

3. **Review full manual:**

    ```
    DEPLOYMENT_MANUAL_MULTI_PRODUCT.md
    ```

4. **Contact team:**
    - Developer: [Your Name]
    - DBA: [DBA Name]

---

## ✅ Pre-Deployment Checklist

Sebelum execute scripts:

- [ ] Database backup completed
- [ ] Files backup completed
- [ ] Maintenance window scheduled
- [ ] Team standby
- [ ] Rollback plan ready
- [ ] Testing plan prepared
- [ ] User notification sent (if needed)

---

## 🎯 Success Criteria

Deployment dianggap sukses jika:

- [ ] All SQL scripts executed without errors
- [ ] All 7 files deployed successfully
- [ ] Caches cleared without issues
- [ ] Verification queries pass
- [ ] Application loads without 500 errors
- [ ] Test weighing in/out works
- [ ] Approval flow works
- [ ] Print receipt shows range data
- [ ] No errors in logs

---

**Version:** 1.0  
**Last Updated:** 2026-07-01  
**Author:** GitHub Copilot
