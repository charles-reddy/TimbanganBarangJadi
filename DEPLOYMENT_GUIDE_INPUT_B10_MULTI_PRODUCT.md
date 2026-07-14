# Deployment Guide: Input B10 Multi Product dengan Koreksi

## 📋 Overview

Implementasi lengkap untuk input B10 di Multi Product Weighing dengan flow terpisah dan kemampuan koreksi jika average out of range (Opsi B modifikasi).

---

## 🚀 Deployment Steps

### 1. Backup Database

```powershell
# Backup database terlebih dahulu
# Simpan backup di folder aman
```

### 2. Run Migrations

Jalankan migrations untuk menambahkan kolom-kolom baru:

```powershell
php artisan migrate
```

**Migrations yang akan dijalankan:**

- `2026_07_13_000001_add_b10_fields_to_trscale_details.php`
- `2026_07_13_000002_add_b10_status_fields_to_trscale_headers.php`
- `2026_07_13_000003_create_trscale_b10_corrections_table.php`

### 3. Clear Cache

```powershell
php artisan optimize:clear
php artisan config:clear
php artisan route:clear
php artisan view:clear
```

### 4. Test Implementation

1. **Test Menu Navigation**
    - Login sebagai `operator-b10` atau `supervisor-b10`
    - Cek menu baru: "Input B10 Multi Product" dan "Koreksi B10"

2. **Test Flow Lengkap**
    - Lakukan timbang masuk multi product
    - Input B10 data (qty karung, batch, krani, foto)
    - Timbang keluar
    - Jika out of range, lakukan koreksi B10
    - Submit untuk approval jika diperlukan

---

## 📁 Files Created/Modified

### New Files Created:

**Migrations (3 files):**

- `database/migrations/2026_07_13_000001_add_b10_fields_to_trscale_details.php`
- `database/migrations/2026_07_13_000002_add_b10_status_fields_to_trscale_headers.php`
- `database/migrations/2026_07_13_000003_create_trscale_b10_corrections_table.php`

**Models (1 new):**

- `app/Models/TrscaleB10Correction.php`

**Livewire Components (2 new):**

- `app/Livewire/MultiProductInputB10.php`
- `app/Livewire/MultiProductKoreksiB10.php`

**Blade Views (4 new):**

- `resources/views/multi-product-input-b10.blade.php`
- `resources/views/multi-product-koreksi-b10.blade.php`
- `resources/views/livewire/multi-product-input-b10.blade.php`
- `resources/views/livewire/multi-product-koreksi-b10.blade.php`

### Modified Files:

**Models (2 updated):**

- `app/Models/TrscaleDetail.php` - Added B10 fields & relationships
- `app/Models/TrscaleHeader.php` - Added B10 tracking fields

**Services (1 updated):**

- `app/Services/MultiProductWeighingService.php` - Updated processWeighOut to use b10QtyKarung

**Livewire (1 updated):**

- `app/Livewire/MultiProductWeighingOut.php` - Updated status filter

**Routes (1 updated):**

- `routes/web.php` - Added 2 new routes

---

## 🗄️ Database Changes

### `trscale_details` - New Columns:

**Input B10:**

- `b10QtyKarung` - INT NULL - Qty karung actual dari B10
- `b10BatchNo` - VARCHAR(50) NULL - Batch number dari B10
- `kontainerNo` - VARCHAR(50) NULL - Container number
- `krani` - VARCHAR(100) NULL - Nama krani yang input
- `imgFormLoading` - VARCHAR(255) NULL - Path foto form loading

**Koreksi B10:**

- `b10QtyKarung_original` - INT NULL - Qty original sebelum koreksi
- `b10_correction_count` - INT DEFAULT 0 - Jumlah koreksi
- `b10_corrected_by` - BIGINT NULL - User ID yang koreksi
- `b10_corrected_at` - DATETIME NULL - Tanggal koreksi
- `buktiKoreksi1` - VARCHAR(255) NULL - Foto bukti koreksi 1
- `buktiKoreksi2` - VARCHAR(255) NULL - Foto bukti koreksi 2
- `buktiKoreksi3` - VARCHAR(255) NULL - Foto bukti koreksi 3

### `trscale_headers` - New Columns:

- `b10_input_by` - BIGINT NULL - User ID yang input B10
- `b10_input_at` - DATETIME NULL - Tanggal input B10
- `needs_b10_correction` - BOOLEAN DEFAULT 0 - Flag perlu koreksi B10
- `correction_submitted` - BOOLEAN DEFAULT 0 - Flag sudah submit koreksi

**Status Enum Updated:**

- `WEIGHING_IN` - Setelah timbang masuk
- `READY_FOR_WEIGH_OUT` - ✨ NEW: Setelah input B10 selesai
- `WEIGHING_OUT` - Setelah timbang keluar
- `PENDING_B10_CORRECTION` - ✨ NEW: Out of range, menunggu koreksi B10
- `PENDING_APPROVAL` - Submitted untuk approval
- `APPROVED` - Approved oleh manager
- `REJECTED` - Rejected
- `COMPLETED` - In range dan selesai

### New Table: `trscale_b10_corrections`

History semua koreksi B10:

- `id` - Primary key
- `header_id` - FK to trscale_headers
- `detail_id` - FK to trscale_details
- `correction_number` - Koreksi ke-n
- `old_b10_qty_karung` - Qty sebelum koreksi
- `new_b10_qty_karung` - Qty setelah koreksi
- `old_avg_per_karung` - Average sebelum
- `new_avg_per_karung` - Average setelah
- `reason` - Alasan koreksi
- `corrected_by` - User yang koreksi
- `corrected_at` - Tanggal koreksi
- `bukti_foto_1/2/3` - Path foto bukti
- `created_at`, `updated_at` - Timestamps

---

## 🔄 Flow Diagram

```
1. TIMBANG MASUK (Multi Product Weighing In)
   ↓ Status: WEIGHING_IN

2. INPUT B10 (Multi Product Input B10) ← Menu Baru
   • Input B10 Qty Karung (actual)
   • Input Batch No, Krani, Container No
   • Upload Foto Form Loading
   ↓ Status: READY_FOR_WEIGH_OUT

3. TIMBANG KELUAR (Multi Product Weighing Out)
   • Perhitungan average menggunakan B10 Qty Karung
   • Average = Actual Weight / B10QtyKarung
   ↓
   ├─ In Range → Status: COMPLETED ✅
   └─ Out of Range → Status: PENDING_B10_CORRECTION

4a. KOREKSI B10 (Multi Product Koreksi B10) ← Menu Baru
    • Edit B10 Qty Karung yang salah
    • Upload bukti koreksi (3 foto)
    • Live preview perhitungan baru
    ↓
    ├─ In Range setelah koreksi → Status: COMPLETED ✅
    └─ Masih Out of Range → Bisa koreksi lagi atau submit approval

4b. SUBMIT APPROVAL (dari Koreksi B10)
    • Tidak koreksi, langsung submit
    ↓ Status: PENDING_APPROVAL

5. APPROVAL (Multi Product Approval)
   ↓
   ├─ Approve → Status: APPROVED ✅
   └─ Reject → Status: REJECTED ❌
```

---

## 👥 User Roles & Permissions

### Input B10 Multi Product

**Roles:** `administrator`, `manager-logistik`, `operator-b10`, `supervisor-b10`

- Menu: "Input B10 Multi Product"
- Route: `/multi-product-input-b10`
- Fungsi: Input data B10 untuk transaksi WEIGHING_IN

### Koreksi B10 Multi Product

**Roles:** `administrator`, `manager-logistik`, `operator-b10`, `supervisor-b10`

- Menu: "Koreksi B10 Multi Product"
- Route: `/multi-product-koreksi-b10`
- Fungsi: Koreksi qty karung untuk transaksi PENDING_B10_CORRECTION

### Multi Product Approval

**Roles:** `administrator`, `manager-logistik`, `supervisor-b10`

- Menu: "Multi Product Approval"
- Route: `/multi-product-approval`
- Fungsi: Approve/Reject transaksi PENDING_APPROVAL

---

## 🎯 Key Features

### 1. Input B10 Multi Product

✅ Tampilkan transaksi dengan status WEIGHING_IN  
✅ Input data per product dalam satu form  
✅ Validasi wajib untuk semua field penting  
✅ Upload foto form loading per product  
✅ Auto update status ke READY_FOR_WEIGH_OUT

### 2. Koreksi B10 Multi Product

✅ Tampilkan transaksi dengan status PENDING_B10_CORRECTION  
✅ Live preview perhitungan jika qty diubah  
✅ Upload bukti koreksi (3 foto)  
✅ History koreksi tersimpan di database  
✅ Auto COMPLETED jika setelah koreksi sudah in range  
✅ Opsi submit untuk approval jika masih out of range

### 3. Perhitungan Menggunakan B10

✅ Average = Actual Weight / **B10QtyKarung** (bukan qty_karung dari SPM)  
✅ Total Range = Σ(**B10QtyKarung** × gross_min/max)  
✅ Validation menggunakan B10 data

### 4. Audit Trail

✅ History semua koreksi di `trscale_b10_corrections`  
✅ Tracking siapa yang input B10  
✅ Tracking siapa yang koreksi  
✅ Tracking jumlah koreksi

---

## 🧪 Testing Checklist

### Basic Flow

- [ ] Timbang masuk multi product berhasil
- [ ] Menu "Input B10 Multi Product" muncul untuk role yang sesuai
- [ ] Input B10 untuk semua product berhasil
- [ ] Status berubah dari WEIGHING_IN → READY_FOR_WEIGH_OUT
- [ ] Timbang keluar hanya tampilkan transaksi READY_FOR_WEIGH_OUT
- [ ] Perhitungan average menggunakan B10QtyKarung

### In Range Flow

- [ ] Jika timbang keluar in range, status langsung COMPLETED
- [ ] Tidak perlu koreksi

### Out of Range Flow

- [ ] Jika timbang keluar out of range, status PENDING_B10_CORRECTION
- [ ] Menu "Koreksi B10" menampilkan transaksi tersebut
- [ ] Preview calculation bekerja dengan baik
- [ ] Koreksi B10 berhasil disimpan
- [ ] History koreksi tercatat di database

### Koreksi Berhasil

- [ ] Setelah koreksi, jika in range, status auto COMPLETED
- [ ] Detail transaksi menampilkan data koreksi

### Submit Approval

- [ ] Jika masih out of range, bisa submit approval
- [ ] Status berubah PENDING_APPROVAL
- [ ] Muncul di menu "Multi Product Approval"

### File Upload

- [ ] Upload foto form loading berhasil
- [ ] Upload foto bukti koreksi berhasil
- [ ] File tersimpan di folder yang benar

---

## 🔧 Troubleshooting

### Migration Error

```powershell
# Jika ada error saat migrate, rollback:
php artisan migrate:rollback --step=3

# Cek status migrations:
php artisan migrate:status

# Migrate ulang:
php artisan migrate
```

### Foreign Key Error

Pastikan tabel `users`, `trscale_headers`, `trscale_details` sudah ada.

### Route Not Found

```powershell
# Clear route cache:
php artisan route:clear
php artisan route:cache
```

### View Not Found

```powershell
# Clear view cache:
php artisan view:clear
```

### Livewire Not Loading

```powershell
# Publish livewire assets:
php artisan livewire:publish --assets
```

---

## 📊 Monitoring

Setelah deployment, monitor:

1. **Database Growth** - Tabel `trscale_b10_corrections` akan bertambah setiap koreksi
2. **File Storage** - Folder `storage/app/public/uploads/formloading` dan `uploads/koreksi`
3. **User Behavior** - Berapa banyak transaksi yang perlu koreksi?
4. **Average Correction Count** - Rata-rata koreksi per transaksi

---

## 📝 Notes

- **Backward Compatibility**: Transaksi lama tanpa B10 data masih bisa berjalan
- **Migration Reversible**: Bisa rollback jika ada masalah
- **No Breaking Changes**: Tidak mengubah flow existing yang sudah jalan
- **Audit Ready**: Semua perubahan tercatat untuk audit

---

## 🎉 Success Criteria

✅ Migrations berhasil dijalankan tanpa error  
✅ Menu baru muncul untuk role yang sesuai  
✅ Flow input B10 berjalan lancar  
✅ Perhitungan average menggunakan B10QtyKarung  
✅ Koreksi B10 bisa dilakukan dengan preview  
✅ History koreksi tercatat  
✅ Submit approval bekerja dengan baik

---

**Deployment Date:** 2026-07-13  
**Version:** 1.0.0  
**Status:** Ready for Production ✅
