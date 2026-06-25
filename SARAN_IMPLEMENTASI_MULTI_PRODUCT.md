# SARAN IMPLEMENTASI MULTI-PRODUCT WEIGHING

## Dengan Standard Weight (weight_std) dan Approval Mechanism

---

## 📋 RINGKASAN REQUIREMENT

### Existing System (Tetap Dipertahankan):

- 1 createspm = 1 transaksi timbang
- Timbang masuk (IN) → Timbang keluar (OUT) → Net Weight

### New Requirement:

- **Lebih dari 1 createspm dalam 1 kali timbangan**
- Timbang masuk = tare (berat truk kosong)
- Timbang keluar = gross (truk + muatan)
- Net weight = gross - tare
- Gunakan **weight_std** dari table products sebagai standar teoretis
- Validasi dengan **gross_min** dan **gross_max** dari table products
- **Approval** jika rata-rata aktual keluar dari range

---

## 🎯 STRATEGI IMPLEMENTASI (TANPA MENGGANGGU SISTEM LAMA)

### Pendekatan: **Dual-Mode System**

```
┌─────────────────────────────────────────────────┐
│           WEIGHING SYSTEM                       │
├─────────────────────────────────────────────────┤
│                                                 │
│  MODE 1: SINGLE PRODUCT (Existing)             │
│  ├─ 1 SPM = 1 Transaksi                        │
│  ├─ Tetap pakai table timbangans               │
│  └─ Tidak ada perubahan                        │
│                                                 │
│  MODE 2: MULTI PRODUCT (New)                   │
│  ├─ N SPM = 1 Transaksi                        │
│  ├─ Pakai table trscale_headers + details      │
│  └─ Dengan approval mechanism                  │
│                                                 │
└─────────────────────────────────────────────────┘
```

---

## 🗄️ DATABASE SCHEMA

### 1. Table: `trscale_headers` (Parent Transaction)

```sql
CREATE TABLE trscale_headers (
    id BIGINT PRIMARY KEY AUTO_INCREMENT,
    trans_no VARCHAR(50) UNIQUE,          -- TRX/2026/06/0001
    trans_type VARCHAR(20) DEFAULT 'MULTI', -- 'SINGLE' atau 'MULTI'

    -- Vehicle & Driver Info
    driver VARCHAR(255),
    carID VARCHAR(255),

    -- Customer & Transporter
    custID INT,
    custName VARCHAR(255),
    transpID INT,
    transpName VARCHAR(255),

    -- Document Numbers
    doNo VARCHAR(255),
    poNo VARCHAR(255),

    -- Weighing Data
    tare_weight DECIMAL(10,2),            -- Timbang masuk (truk kosong)
    gross_weight DECIMAL(10,2),           -- Timbang keluar (truk + muatan)
    net_weight DECIMAL(10,2),             -- gross - tare

    -- Theoretical Weight
    theoretical_weight DECIMAL(10,2),     -- Total (qty × weight_std)
    correction_factor DECIMAL(8,6),       -- K = net_weight / theoretical_weight

    -- Scale Info
    scale_in_id INT,                      -- ID timbangan masuk
    scale_out_id INT,                     -- ID timbangan keluar

    -- Timestamp
    weigh_in_time DATETIME,
    weigh_out_time DATETIME,

    -- User Info
    user_in_id INT,
    user_out_id INT,

    -- Approval Status
    status VARCHAR(20) DEFAULT 'PENDING', -- PENDING, APPROVED, REJECTED
    need_approval TINYINT(1) DEFAULT 0,   -- 0=tidak perlu, 1=perlu approval
    approved_by INT NULL,
    approved_at DATETIME NULL,
    approval_note TEXT NULL,

    -- Others
    remarks TEXT,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,

    INDEX idx_trans_no (trans_no),
    INDEX idx_status (status),
    INDEX idx_need_approval (need_approval),
    INDEX idx_created_at (created_at)
);
```

### 2. Table: `trscale_details` (Line Items - Multiple Products)

```sql
CREATE TABLE trscale_details (
    id BIGINT PRIMARY KEY AUTO_INCREMENT,
    header_id BIGINT NOT NULL,

    -- SPM & SPPB Reference
    spm_id INT,                           -- FK ke createspm
    sppb_id INT,                          -- FK ke createsppb

    -- Product Info
    itemCode VARCHAR(255),
    itemName VARCHAR(255),
    itemType VARCHAR(255),

    -- Quantity
    qty_karung INT,                       -- Jumlah karung

    -- Weight Standard (dari products table)
    weight_std DECIMAL(10,2),             -- Standar teoretis per karung
    gross_min DECIMAL(10,2),              -- Batas minimum per karung
    gross_max DECIMAL(10,2),              -- Batas maximum per karung

    -- Calculated Weights
    theoretical_weight DECIMAL(10,2),     -- qty_karung × weight_std
    actual_weight DECIMAL(10,2),          -- Hasil distribusi
    avg_per_karung DECIMAL(10,2),         -- actual_weight / qty_karung

    -- Validation
    is_in_range TINYINT(1),               -- 1=dalam range, 0=di luar range
    need_approval TINYINT(1) DEFAULT 0,   -- Khusus produk ini perlu approval?

    -- Others
    remarks TEXT,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,

    FOREIGN KEY (header_id) REFERENCES trscale_headers(id) ON DELETE CASCADE,
    INDEX idx_header_id (header_id),
    INDEX idx_spm_id (spm_id),
    INDEX idx_need_approval (need_approval)
);
```

### 3. Table: `trscale_approvals` (Approval History)

```sql
CREATE TABLE trscale_approvals (
    id BIGINT PRIMARY KEY AUTO_INCREMENT,
    header_id BIGINT NOT NULL,

    -- Approval Action
    action VARCHAR(20),                   -- APPROVED, REJECTED
    approved_by INT,
    approved_by_name VARCHAR(255),
    approval_note TEXT,
    approved_at DATETIME,

    -- Out of Range Details (JSON)
    out_of_range_products TEXT,           -- JSON array produk yang out of range

    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,

    FOREIGN KEY (header_id) REFERENCES trscale_headers(id) ON DELETE CASCADE,
    INDEX idx_header_id (header_id)
);
```

---

## 🔢 ALGORITMA PERHITUNGAN

### FASE 1: TIMBANG MASUK (Tare)

```php
// User input:
// - Driver, CarID, Customer, Transporter
// - Multiple SPM/Products:
//   * SPM No 1 → Product A → Qty Karung
//   * SPM No 2 → Product B → Qty Karung
//   * SPM No N → Product N → Qty Karung
// - Timbang masuk (tare_weight)

Step 1: Get product standards from database
foreach ($products as $product) {
    $productData = DB::table('products')
        ->where('itemCode', $product['itemCode'])
        ->first(['weight_std', 'gross_min', 'gross_max']);

    $product['weight_std'] = $productData->weight_std;
    $product['gross_min'] = $productData->gross_min;
    $product['gross_max'] = $productData->gross_max;
    $product['theoretical_weight'] = $product['qty_karung'] * $productData->weight_std;
}

Step 2: Calculate total theoretical weight
$total_theoretical = array_sum(array_column($products, 'theoretical_weight'));

Step 3: Save to database
$header = TrscaleHeader::create([
    'trans_no' => generate_trans_no(),
    'trans_type' => 'MULTI',
    'driver' => $driver,
    'carID' => $carID,
    'tare_weight' => $tare_weight,
    'theoretical_weight' => $total_theoretical,
    'status' => 'WEIGHING_IN',
    // ... other fields
]);

foreach ($products as $product) {
    TrscaleDetail::create([
        'header_id' => $header->id,
        'spm_id' => $product['spm_id'],
        'itemCode' => $product['itemCode'],
        'qty_karung' => $product['qty_karung'],
        'weight_std' => $product['weight_std'],
        'gross_min' => $product['gross_min'],
        'gross_max' => $product['gross_max'],
        'theoretical_weight' => $product['theoretical_weight'],
        // ... other fields
    ]);
}
```

### FASE 2: TIMBANG KELUAR (Gross) + DISTRIBUSI BERAT

```php
Step 1: Load transaction
$header = TrscaleHeader::with('details')->find($header_id);

Step 2: Input gross weight
$gross_weight = request('gross_weight'); // dari input timbangan

Step 3: Calculate net weight
$net_weight = $gross_weight - $header->tare_weight;

Step 4: Calculate correction factor (K)
$correction_factor = $net_weight / $header->theoretical_weight;

Step 5: Distribute weight to each product
$need_approval = false;
$out_of_range_products = [];

foreach ($header->details as $detail) {
    // Hitung actual weight produk ini
    $actual_weight = $detail->theoretical_weight * $correction_factor;

    // Hitung rata-rata per karung
    $avg_per_karung = $actual_weight / $detail->qty_karung;

    // Validasi: apakah dalam range?
    $is_in_range = ($avg_per_karung >= $detail->gross_min &&
                    $avg_per_karung <= $detail->gross_max);

    if (!$is_in_range) {
        $need_approval = true;
        $detail->need_approval = 1;

        $out_of_range_products[] = [
            'itemName' => $detail->itemName,
            'qty_karung' => $detail->qty_karung,
            'avg_per_karung' => $avg_per_karung,
            'gross_min' => $detail->gross_min,
            'gross_max' => $detail->gross_max,
            'deviation' => $avg_per_karung < $detail->gross_min
                ? 'UNDER WEIGHT'
                : 'OVER WEIGHT'
        ];
    }

    // Update detail
    $detail->update([
        'actual_weight' => $actual_weight,
        'avg_per_karung' => $avg_per_karung,
        'is_in_range' => $is_in_range ? 1 : 0,
        'need_approval' => $detail->need_approval
    ]);
}

Step 6: Update header status
$header->update([
    'gross_weight' => $gross_weight,
    'net_weight' => $net_weight,
    'correction_factor' => $correction_factor,
    'need_approval' => $need_approval ? 1 : 0,
    'status' => $need_approval ? 'PENDING_APPROVAL' : 'COMPLETED',
    'weigh_out_time' => now(),
    'user_out_id' => auth()->id()
]);

Step 7: Jika perlu approval, kirim notifikasi
if ($need_approval) {
    // Send notification to approver
    // Log out of range products
    // Show warning to user
}
```

---

## 📊 CONTOH PERHITUNGAN

### INPUT:

```
Truk: B 1234 XYZ
Driver: Budi

SPM 1 - Product A (Gula Pasir):
  - Qty: 100 karung
  - weight_std: 50 kg/karung
  - gross_min: 48 kg/karung
  - gross_max: 52 kg/karung
  - Theoretical: 100 × 50 = 5,000 kg

SPM 2 - Product B (Gula Kristal):
  - Qty: 50 karung
  - weight_std: 40 kg/karung
  - gross_min: 38 kg/karung
  - gross_max: 42 kg/karung
  - Theoretical: 50 × 40 = 2,000 kg

Total Theoretical Weight: 5,000 + 2,000 = 7,000 kg

Timbang Masuk (Tare): 3,000 kg
```

### PROSES TIMBANG KELUAR:

```
Timbang Keluar (Gross): 9,800 kg

Net Weight: 9,800 - 3,000 = 6,800 kg

Correction Factor (K): 6,800 / 7,000 = 0.971429
```

### DISTRIBUSI:

```
Product A (Gula Pasir):
  Actual Weight: 5,000 × 0.971429 = 4,857.14 kg
  Avg per Karung: 4,857.14 / 100 = 48.57 kg/karung
  ✅ IN RANGE: 48 ≤ 48.57 ≤ 52 → OK

Product B (Gula Kristal):
  Actual Weight: 2,000 × 0.971429 = 1,942.86 kg
  Avg per Karung: 1,942.86 / 50 = 38.86 kg/karung
  ✅ IN RANGE: 38 ≤ 38.86 ≤ 42 → OK

RESULT: Tidak perlu approval ✅
```

### CONTOH OUT OF RANGE:

```
Misalkan Timbang Keluar: 11,000 kg
Net Weight: 11,000 - 3,000 = 8,000 kg
Correction Factor: 8,000 / 7,000 = 1.142857

Product A:
  Actual: 5,000 × 1.142857 = 5,714.29 kg
  Avg: 5,714.29 / 100 = 57.14 kg/karung
  ❌ OUT OF RANGE: 57.14 > 52 (max) → OVER WEIGHT

Product B:
  Actual: 2,000 × 1.142857 = 2,285.71 kg
  Avg: 2,285.71 / 50 = 45.71 kg/karung
  ❌ OUT OF RANGE: 45.71 > 42 (max) → OVER WEIGHT

RESULT: ⚠️ NEED APPROVAL
```

---

## 🔧 IMPLEMENTASI BERTAHAP

### TAHAP 1: Database Setup (Week 1)

**1.1 Create Migrations**

```bash
php artisan make:migration create_trscale_headers_table
php artisan make:migration create_trscale_details_table
php artisan make:migration create_trscale_approvals_table
```

**1.2 Create Models**

```bash
php artisan make:model TrscaleHeader
php artisan make:model TrscaleDetail
php artisan make:model TrscaleApproval
```

**1.3 Setup Relationships**

```php
// TrscaleHeader.php
public function details() {
    return $this->hasMany(TrscaleDetail::class, 'header_id');
}

public function approvals() {
    return $this->hasMany(TrscaleApproval::class, 'header_id');
}

// TrscaleDetail.php
public function header() {
    return $this->belongsTo(TrscaleHeader::class, 'header_id');
}

public function spm() {
    return $this->belongsTo(Createspm::class, 'spm_id');
}

public function product() {
    return $this->belongsTo(Product::class, 'itemCode', 'itemCode');
}
```

### TAHAP 2: Service Layer (Week 1-2)

**2.1 Create Service Class**

```bash
php artisan make:class Services/MultiProductWeighingService
```

```php
<?php

namespace App\Services;

use App\Models\TrscaleHeader;
use App\Models\TrscaleDetail;
use Illuminate\Support\Facades\DB;

class MultiProductWeighingService
{
    /**
     * Create weighing IN transaction
     */
    public function createWeighIn($data)
    {
        return DB::transaction(function () use ($data) {
            // Generate trans_no
            $transNo = $this->generateTransNo();

            // Calculate total theoretical weight
            $totalTheoretical = 0;
            foreach ($data['products'] as &$product) {
                $productData = DB::table('products')
                    ->where('itemCode', $product['itemCode'])
                    ->first(['weight_std', 'gross_min', 'gross_max']);

                $product['weight_std'] = $productData->weight_std;
                $product['gross_min'] = $productData->gross_min;
                $product['gross_max'] = $productData->gross_max;
                $product['theoretical_weight'] = $product['qty_karung'] * $productData->weight_std;

                $totalTheoretical += $product['theoretical_weight'];
            }

            // Create header
            $header = TrscaleHeader::create([
                'trans_no' => $transNo,
                'trans_type' => 'MULTI',
                'driver' => $data['driver'],
                'carID' => $data['carID'],
                'custID' => $data['custID'],
                'custName' => $data['custName'],
                'transpID' => $data['transpID'],
                'transpName' => $data['transpName'],
                'tare_weight' => $data['tare_weight'],
                'theoretical_weight' => $totalTheoretical,
                'scale_in_id' => $data['scale_in_id'],
                'weigh_in_time' => now(),
                'user_in_id' => auth()->id(),
                'status' => 'WEIGHING_IN',
            ]);

            // Create details
            foreach ($data['products'] as $product) {
                TrscaleDetail::create([
                    'header_id' => $header->id,
                    'spm_id' => $product['spm_id'],
                    'sppb_id' => $product['sppb_id'],
                    'itemCode' => $product['itemCode'],
                    'itemName' => $product['itemName'],
                    'qty_karung' => $product['qty_karung'],
                    'weight_std' => $product['weight_std'],
                    'gross_min' => $product['gross_min'],
                    'gross_max' => $product['gross_max'],
                    'theoretical_weight' => $product['theoretical_weight'],
                ]);
            }

            return $header;
        });
    }

    /**
     * Process weighing OUT and distribution
     */
    public function processWeighOut($headerId, $grossWeight, $scaleOutId)
    {
        return DB::transaction(function () use ($headerId, $grossWeight, $scaleOutId) {
            $header = TrscaleHeader::with('details')->findOrFail($headerId);

            // Calculate net weight
            $netWeight = $grossWeight - $header->tare_weight;

            // Calculate correction factor
            $correctionFactor = $netWeight / $header->theoretical_weight;

            // Distribute weight and validate
            $needApproval = false;
            $outOfRangeProducts = [];

            foreach ($header->details as $detail) {
                $actualWeight = $detail->theoretical_weight * $correctionFactor;
                $avgPerKarung = $actualWeight / $detail->qty_karung;

                $isInRange = ($avgPerKarung >= $detail->gross_min &&
                              $avgPerKarung <= $detail->gross_max);

                if (!$isInRange) {
                    $needApproval = true;

                    $outOfRangeProducts[] = [
                        'itemName' => $detail->itemName,
                        'qty_karung' => $detail->qty_karung,
                        'avg_per_karung' => round($avgPerKarung, 2),
                        'gross_min' => $detail->gross_min,
                        'gross_max' => $detail->gross_max,
                        'deviation' => $avgPerKarung < $detail->gross_min ? 'UNDER' : 'OVER',
                        'diff' => round(abs($avgPerKarung - ($avgPerKarung < $detail->gross_min ? $detail->gross_min : $detail->gross_max)), 2)
                    ];
                }

                $detail->update([
                    'actual_weight' => $actualWeight,
                    'avg_per_karung' => $avgPerKarung,
                    'is_in_range' => $isInRange ? 1 : 0,
                    'need_approval' => $isInRange ? 0 : 1,
                ]);
            }

            // Update header
            $header->update([
                'gross_weight' => $grossWeight,
                'net_weight' => $netWeight,
                'correction_factor' => $correctionFactor,
                'scale_out_id' => $scaleOutId,
                'weigh_out_time' => now(),
                'user_out_id' => auth()->id(),
                'need_approval' => $needApproval ? 1 : 0,
                'status' => $needApproval ? 'PENDING_APPROVAL' : 'COMPLETED',
            ]);

            return [
                'header' => $header->fresh('details'),
                'need_approval' => $needApproval,
                'out_of_range_products' => $outOfRangeProducts,
                'correction_factor' => $correctionFactor,
            ];
        });
    }

    /**
     * Approve transaction
     */
    public function approve($headerId, $note = null)
    {
        return DB::transaction(function () use ($headerId, $note) {
            $header = TrscaleHeader::findOrFail($headerId);

            $header->update([
                'status' => 'APPROVED',
                'approved_by' => auth()->id(),
                'approved_at' => now(),
                'approval_note' => $note,
            ]);

            TrscaleApproval::create([
                'header_id' => $header->id,
                'action' => 'APPROVED',
                'approved_by' => auth()->id(),
                'approved_by_name' => auth()->user()->name,
                'approval_note' => $note,
                'approved_at' => now(),
            ]);

            return $header;
        });
    }

    /**
     * Generate transaction number
     */
    private function generateTransNo()
    {
        $date = now()->format('Y/m');
        $lastTrans = TrscaleHeader::where('trans_no', 'like', "TRX/{$date}/%")
            ->orderBy('id', 'desc')
            ->first();

        $sequence = $lastTrans
            ? intval(substr($lastTrans->trans_no, -4)) + 1
            : 1;

        return sprintf('TRX/%s/%04d', $date, $sequence);
    }
}
```

### TAHAP 3: Livewire Components (Week 2-3)

**3.1 Create Component untuk Multi-Product Weighing**

```bash
php artisan make:livewire MultiProductWeighingIn
php artisan make:livewire MultiProductWeighingOut
php artisan make:livewire MultiProductApproval
```

**3.2 UI Features:**

- Pilihan mode: Single Product / Multi Product
- Dynamic form untuk add/remove products
- Real-time calculation preview
- Validation warnings
- Approval interface

### TAHAP 4: Backward Compatibility (Week 3)

**4.1 Update Existing Timbangan Components**
Tambahkan pilihan mode di awal form:

```blade
<div class="mb-3">
    <label class="form-label fw-bold">Tipe Transaksi</label>
    <div class="btn-group w-100" role="group">
        <input type="radio" class="btn-check" wire:model="transType" value="SINGLE" id="single">
        <label class="btn btn-outline-primary" for="single">
            Single Product (1 SPM)
        </label>

        <input type="radio" class="btn-check" wire:model="transType" value="MULTI" id="multi">
        <label class="btn btn-outline-primary" for="multi">
            Multi Product (Beberapa SPM)
        </label>
    </div>
</div>

@if($transType == 'SINGLE')
    {{-- Form timbangan existing --}}
@else
    {{-- Form multi-product baru --}}
@endif
```

### TAHAP 5: Testing & Training (Week 4)

**5.1 Unit Tests**

- Test calculation logic
- Test validation rules
- Test approval flow

**5.2 Integration Tests**

- Test full weighing cycle
- Test database transactions
- Test edge cases

**5.3 User Training**

- Dokumentasi SOP
- Video tutorial
- Live training session

---

## 📱 UI/UX MOCKUP

### Screen 1: Pilih Mode

```
┌──────────────────────────────────────────┐
│  TIMBANGAN - MASUK                       │
├──────────────────────────────────────────┤
│                                          │
│  Pilih Tipe Transaksi:                  │
│                                          │
│  ┌─────────────┐  ┌──────────────────┐  │
│  │ SINGLE      │  │ MULTI PRODUCT    │  │
│  │ PRODUCT     │  │                  │  │
│  │             │  │ (Lebih dari 1    │  │
│  │ (1 SPM)     │  │  SPM/Produk)     │  │
│  │             │  │                  │  │
│  └─────────────┘  └──────────────────┘  │
│                                          │
└──────────────────────────────────────────┘
```

### Screen 2: Multi-Product Input (Weigh IN)

```
┌──────────────────────────────────────────────────┐
│  TIMBANGAN MULTI-PRODUCT - MASUK                 │
├──────────────────────────────────────────────────┤
│                                                  │
│  Driver: [_______________]  CarID: [__________]  │
│  Customer: [_______________]                     │
│  Transporter: [_______________]                  │
│                                                  │
│  ┌─ Products ──────────────────────────────────┐ │
│  │                                             │ │
│  │  SPM 1: [Select SPM ▾]                     │ │
│  │    Product: Gula Pasir                     │ │
│  │    Qty Karung: [100]                       │ │
│  │    Weight Std: 50 kg/karung                │ │
│  │    Theoretical: 5,000 kg                   │ │
│  │    [❌ Remove]                              │ │
│  │                                             │ │
│  │  SPM 2: [Select SPM ▾]                     │ │
│  │    Product: Gula Kristal                   │ │
│  │    Qty Karung: [50]                        │ │
│  │    Weight Std: 40 kg/karung                │ │
│  │    Theoretical: 2,000 kg                   │ │
│  │    [❌ Remove]                              │ │
│  │                                             │ │
│  │  [➕ Tambah Product]                        │ │
│  │                                             │ │
│  │  Total Theoretical Weight: 7,000 kg        │ │
│  └─────────────────────────────────────────────┘ │
│                                                  │
│  Timbang Masuk (Tare): [3,000] kg               │
│                                                  │
│  [💾 Simpan Timbang Masuk]                       │
│                                                  │
└──────────────────────────────────────────────────┘
```

### Screen 3: Weigh OUT + Validation

```
┌──────────────────────────────────────────────────┐
│  TIMBANGAN MULTI-PRODUCT - KELUAR                │
├──────────────────────────────────────────────────┤
│                                                  │
│  Trans No: TRX/2026/06/0001                      │
│  Driver: Budi    CarID: B 1234 XYZ               │
│                                                  │
│  Timbang Masuk (Tare): 3,000 kg                 │
│  Timbang Keluar (Gross): [9,800] kg              │
│  Net Weight: 6,800 kg                            │
│                                                  │
│  Theoretical Weight: 7,000 kg                    │
│  Correction Factor (K): 0.9714                   │
│                                                  │
│  ┌─ Products ──────────────────────────────────┐ │
│  │                                             │ │
│  │  ✅ Gula Pasir (100 karung)                 │ │
│  │     Actual: 4,857 kg                        │ │
│  │     Avg/Karung: 48.57 kg                    │ │
│  │     Range: 48-52 kg → IN RANGE              │ │
│  │                                             │ │
│  │  ✅ Gula Kristal (50 karung)                │ │
│  │     Actual: 1,943 kg                        │ │
│  │     Avg/Karung: 38.86 kg                    │ │
│  │     Range: 38-42 kg → IN RANGE              │ │
│  │                                             │ │
│  └─────────────────────────────────────────────┘ │
│                                                  │
│  Status: ✅ Semua produk dalam range             │
│                                                  │
│  [💾 Simpan Timbang Keluar]                      │
│                                                  │
└──────────────────────────────────────────────────┘
```

### Screen 4: Out of Range Warning

```
┌──────────────────────────────────────────────────┐
│  ⚠️ APPROVAL DIPERLUKAN                          │
├──────────────────────────────────────────────────┤
│                                                  │
│  Terdapat produk dengan berat di luar range:     │
│                                                  │
│  ┌─────────────────────────────────────────────┐ │
│  │ ❌ Gula Pasir                               │ │
│  │    Avg/Karung: 57.14 kg                     │ │
│  │    Range: 48-52 kg                          │ │
│  │    Status: OVER WEIGHT (+5.14 kg)           │ │
│  │                                             │ │
│  │ ❌ Gula Kristal                             │ │
│  │    Avg/Karung: 45.71 kg                     │ │
│  │    Range: 38-42 kg                          │ │
│  │    Status: OVER WEIGHT (+3.71 kg)           │ │
│  └─────────────────────────────────────────────┘ │
│                                                  │
│  Transaksi disimpan dengan status PENDING.       │
│  Menunggu approval dari supervisor.              │
│                                                  │
│  [💾 Simpan & Request Approval]                  │
│                                                  │
└──────────────────────────────────────────────────┘
```

---

## ✅ CHECKLIST IMPLEMENTASI

### Database & Models

- [ ] Create migration: trscale_headers
- [ ] Create migration: trscale_details
- [ ] Create migration: trscale_approvals
- [ ] Create Model: TrscaleHeader + relationships
- [ ] Create Model: TrscaleDetail + relationships
- [ ] Create Model: TrscaleApproval
- [ ] Add weight_std, gross_min, gross_max to products table (if not exist)

### Service Layer

- [ ] Create MultiProductWeighingService
- [ ] Method: createWeighIn()
- [ ] Method: processWeighOut()
- [ ] Method: approve()
- [ ] Method: reject()
- [ ] Method: generateTransNo()

### Livewire Components

- [ ] Create MultiProductWeighingIn component
- [ ] Create MultiProductWeighingOut component
- [ ] Create MultiProductApproval component
- [ ] Update existing Timbangan components untuk dual-mode

### Views & UI

- [ ] Mode selector UI
- [ ] Multi-product dynamic form
- [ ] Validation display
- [ ] Approval interface
- [ ] Reports/print layout

### Business Logic

- [ ] Calculation engine
- [ ] Validation rules
- [ ] Approval workflow
- [ ] Notification system

### Testing

- [ ] Unit tests untuk calculation
- [ ] Unit tests untuk validation
- [ ] Integration tests
- [ ] User acceptance testing

### Documentation & Training

- [ ] Technical documentation
- [ ] User manual / SOP
- [ ] Video tutorial
- [ ] Training materials

---

## 🎓 KESIMPULAN & REKOMENDASI

### ✅ KEUNGGULAN SOLUSI INI:

1. **Backward Compatible**: Sistem lama tetap jalan tanpa gangguan
2. **Flexible**: Support single dan multi-product
3. **Accurate**: Menggunakan weight_std sebagai baseline
4. **Controlled**: Approval mechanism untuk outliers
5. **Scalable**: Mudah dikembangkan ke depannya
6. **Traceable**: Full audit trail

---

## ✅ KEPUTUSAN FINAL (APPROVED)

### Strategi: **Opsi A - Sistem Terpisah**

**Alasan:**

1. Frekuensi multi-product: **Jarang**
2. User handling: **Multi user**
3. Sistem existing: **Critical** (tidak boleh diganggu)
4. Reporting: **Perlu gabungan single + multi**

### Struktur Akhir:

```
Single Product (TIDAK DIUBAH):
├─ Controller: Timbanganoa.php
├─ View: timbanganoa.blade.php
├─ Table: timbangans
└─ Route: /timbanganoa

Multi Product (BARU):
├─ Controller: MultiProductWeighing.php
├─ View: multiproductweighing.blade.php
├─ Tables: trscale_headers, trscale_details, trscale_approvals
└─ Route: /timbangan-multi

Reporting:
├─ Database View: v_all_weighing_transactions
├─ Service: WeighingReportService
└─ Export: ExportAllWeighing.php
```

---

## 📅 IMPLEMENTATION PHASES (APPROVED)

### **Phase 1: Database Setup** (Day 1-2)

**Status:** 🔄 IN PROGRESS

**Tasks:**

- [x] Create migration: create_trscale_headers_table
- [x] Create migration: create_trscale_details_table
- [x] Create migration: create_trscale_approvals_table
- [ ] Create Model: TrscaleHeader
- [ ] Create Model: TrscaleDetail
- [ ] Create Model: TrscaleApproval
- [ ] Setup relationships (hasMany, belongsTo)
- [ ] Create database view: v_all_weighing_transactions
- [ ] Run migration di development
- [ ] Test database structure

**Deliverables:**

- ✅ 3 migration files
- ⏳ 3 model files dengan relationships
- ⏳ 1 database view
- ⏳ Migration testing report

---

### **Phase 2: Service Layer** (Day 3-4)

**Status:** ⏳ NOT STARTED

**Tasks:**

- [ ] Create MultiProductWeighingService.php
    - [ ] Method: createWeighIn()
    - [ ] Method: processWeighOut()
    - [ ] Method: approve()
    - [ ] Method: reject()
    - [ ] Method: generateTransNo()
- [ ] Create WeighingReportService.php
    - [ ] Method: getAllTransactions()
    - [ ] Method: getSummary()
    - [ ] Method: getDetailByTransNo()
- [ ] Create unit tests
    - [ ] Test calculation logic
    - [ ] Test correction factor
    - [ ] Test validation rules
    - [ ] Test approval flow

**Deliverables:**

- ⏳ MultiProductWeighingService.php
- ⏳ WeighingReportService.php
- ⏳ Unit test files
- ⏳ Service documentation

---

### **Phase 3: Livewire Components** (Day 5-8)

**Status:** ⏳ NOT STARTED

**Tasks:**

- [ ] Create MultiProductWeighingIn.php
    - [ ] Form untuk input driver, carID, customer
    - [ ] Dynamic add/remove products
    - [ ] Get weight_std from products table
    - [ ] Calculate theoretical weight
    - [ ] Save to trscale_headers + details
- [ ] Create MultiProductWeighingOut.php
    - [ ] Load pending transactions
    - [ ] Input gross weight
    - [ ] Calculate distribution
    - [ ] Validate against gross_min/max
    - [ ] Show approval warning if needed
- [ ] Create MultiProductApproval.php
    - [ ] List pending approvals
    - [ ] Show out of range details
    - [ ] Approve/Reject actions
    - [ ] Approval notes
- [ ] Create views dengan Bootstrap styling
- [ ] Add form validation
- [ ] Add loading states
- [ ] Add confirmation dialogs

**Deliverables:**

- ⏳ 3 Livewire components
- ⏳ 3 Blade view files
- ⏳ Component documentation

---

### **Phase 4: Integration & Reports** (Day 9-10)

**Status:** ⏳ NOT STARTED

**Tasks:**

- [ ] Add menu navigation
    - [ ] Sidebar menu: "Timbangan Multi Product"
    - [ ] Submenu: Weigh IN, Weigh OUT, Approval
- [ ] Create ExportAllWeighing.php
    - [ ] Export gabungan single + multi
    - [ ] Excel formatting
    - [ ] Summary sheet
- [ ] Create unified report dashboard
    - [ ] Chart: Single vs Multi transactions
    - [ ] Summary cards
    - [ ] Filter by date range
- [ ] Route configuration
- [ ] Permission/role setup
- [ ] Integration testing

**Deliverables:**

- ⏳ Navigation menu updates
- ⏳ Export Excel feature
- ⏳ Report dashboard
- ⏳ Route definitions
- ⏳ Integration test results

---

### **Phase 5: Go Live** (Day 11-12)

**Status:** ⏳ NOT STARTED

**Tasks:**

- [ ] User training
    - [ ] Create user manual (PDF)
    - [ ] Record video tutorial
    - [ ] Live training session
    - [ ] Q&A session
- [ ] Documentation
    - [ ] Technical documentation
    - [ ] SOP for operators
    - [ ] Troubleshooting guide
    - [ ] API documentation (if any)
- [ ] Deployment
    - [ ] Backup production database
    - [ ] Run migration di production
    - [ ] Deploy code
    - [ ] Smoke testing
- [ ] Monitoring
    - [ ] Setup error logging
    - [ ] Monitor first transactions
    - [ ] Collect user feedback
    - [ ] Bug fixing (if any)

**Deliverables:**

- ⏳ User manual (PDF)
- ⏳ Video tutorial
- ⏳ Training materials
- ⏳ Production deployment checklist
- ⏳ Go-live report

---

## 📊 PROGRESS TRACKING

| Phase                        | Status         | Progress | Start Date | End Date   | Notes                                 |
| ---------------------------- | -------------- | -------- | ---------- | ---------- | ------------------------------------- |
| Phase 1: Database            | ✅ Complete    | 100%     | 2026-06-23 | 2026-06-23 | All migrations, models & view created |
| Phase 2: Service Layer       | ✅ Complete    | 100%     | 2026-06-23 | 2026-06-23 | Business logic & reporting complete   |
| Phase 3: Livewire Components | ✅ Complete    | 100%     | 2026-06-23 | 2026-06-23 | UI components dengan Bootstrap        |
| Phase 4: Integration         | ✅ Complete    | 100%     | 2026-06-23 | 2026-06-23 | Menu, export, & dashboard complete    |
| Phase 5: Go Live             | ⏳ Not Started | 0%       | -          | -          | -                                     |

**Overall Progress: 80%** (Phase 1-4 complete)

---

## 📞 NEXT ACTIONS

✅ **COMPLETED:**

- Diskusi dan approval strategi
- Dokumentasi lengkap
- ✅ Phase 1: Database Setup Complete!
    - ✅ Create migration: trscale_headers
    - ✅ Create migration: trscale_details
    - ✅ Create migration: trscale_approvals
    - ✅ Run migrations (batch 18)
    - ✅ Create Model: TrscaleHeader
    - ✅ Create Model: TrscaleDetail
    - ✅ Create Model: TrscaleApproval
    - ✅ Setup relationships, scopes, helpers
    - ✅ Create database view: v_all_weighing_transactions (8,552 records)
    - ✅ Test database structure
- ✅ Phase 2: Service Layer Complete!
    - ✅ Create MultiProductWeighingService.php
        - ✅ createWeighIn() - Timbang masuk dengan multi SPM
        - ✅ processWeighOut() - Timbang keluar dengan perhitungan faktor koreksi
        - ✅ approve() - Approve transaksi out of range
        - ✅ reject() - Reject transaksi
        - ✅ generateTransNo() - Generate nomor transaksi TRX/YYYY/MM/0001
        - ✅ Helper methods (getByTransNo, getPendingApprovals, cancelTransaction)
    - ✅ Create WeighingReportService.php
        - ✅ getAllTransactions() - Query dengan filter & pagination
        - ✅ getSummary() - Dashboard statistics
        - ✅ getDetailByTransNo() - Detail transaksi
        - ✅ getProductStatistics() - Analisa product out of range
        - ✅ getCorrectionFactorTrends() - Trend correction factor
        - ✅ Export methods untuk Excel/PDF
    - ✅ Test service classes loaded successfully
- ✅ Phase 3: Livewire Components Complete!
    - ✅ Create MultiProductWeighingIn.php
        - ✅ Multi-select SPM dengan checkbox
        - ✅ Auto-populate form dari SPM pertama
        - ✅ Validation untuk driver, carID, tare weight
        - ✅ Integration dengan MultiProductWeighingService
    - ✅ Create MultiProductWeighingOut.php
        - ✅ List transaksi status WEIGHING_IN
        - ✅ Detail view untuk setiap transaksi
        - ✅ Form input gross weight
        - ✅ Auto-calculate net, correction factor, validasi range
        - ✅ Cancel transaction functionality
    - ✅ Create MultiProductApproval.php
        - ✅ Filter by status (Pending/Approved/Rejected)
        - ✅ Approve dengan optional remarks
        - ✅ Reject dengan required remarks
        - ✅ Detail view dengan product breakdown
        - ✅ Approval history display
    - ✅ Create blade views dengan Bootstrap styling
        - ✅ multi-product-weighing-in.blade.php
        - ✅ multi-product-weighing-out.blade.php
        - ✅ multi-product-approval.blade.php
    - ✅ Create layout views
        - ✅ resources/views/multi-product-weighing-in.blade.php
        - ✅ resources/views/multi-product-weighing-out.blade.php
        - ✅ resources/views/multi-product-approval.blade.php
    - ✅ Add routes dengan middleware authentication & role
        - ✅ /multi-product-weighing-in
        - ✅ /multi-product-weighing-out
        - ✅ /multi-product-approval
- ✅ Phase 4: Integration & Reports Complete!
    - ✅ Add navigation menu items
        - ✅ Desktop dropdown menu "Timbangan"
        - ✅ Mobile dropdown menu "Timbangan"
        - ✅ Menu items: Timbang Multi-Product IN, OUT, Approval, Laporan Gabungan
    - ✅ Create ExportAllWeighing.php
        - ✅ Export gabungan single + multi product
        - ✅ Excel formatting dengan headers & styling
        - ✅ Filter by date range, trans type, search
        - ✅ Uses v_all_weighing_transactions view
    - ✅ Create WeighingReportDashboard component
        - ✅ Summary cards (Total, Single, Multi, Net Weight)
        - ✅ Filter by date range, trans type, search
        - ✅ Data table dengan pagination
        - ✅ Export Excel button
        - ✅ Bootstrap styling consistent dengan UI lainnya
    - ✅ Create route /weighing-report-dashboard
    - ✅ Fix model relationships
        - ✅ Createspm: trscaleDetails, customer, transporter, product, sppb, tiket
        - ✅ Fixed CreateTMS → createTM (case-sensitive class name)

🔄 **NEXT:**

- Phase 5: Go Live Preparation
    - User training materials
    - Documentation finalization
    - Production deployment checklist

⏭️ **NEXT UP:**

- Complete Phase 1: Create models
- Create database view
- Test migrations

---

**Last Updated:** 2026-06-23
**Document Version:** 2.0
**Status:** APPROVED & IN PROGRESS
