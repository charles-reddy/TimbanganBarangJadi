# Logic untuk Menimbang Multiple Produk dalam Satu Transaksi

## SOLUSI 1: Historical Average Method (REKOMENDASI)

### A. Database Schema Changes

```sql
-- Tabel parent untuk header transaksi
CREATE TABLE trscale_headers (
    id BIGINT PRIMARY KEY AUTO_INCREMENT,
    driver VARCHAR(255),
    carID VARCHAR(255),
    custID INT,
    custName VARCHAR(255),
    transpID INT,
    transpName VARCHAR(255),
    doNo VARCHAR(255),
    poNo VARCHAR(255),
    timbangin DECIMAL(10,2),      -- Total weight IN
    timbangout DECIMAL(10,2),     -- Total weight OUT
    netto DECIMAL(10,2),          -- Total netto
    timbanganID INT,
    timbanganoutID INT,
    jam_in DATETIME,
    jam_out DATETIME,
    userIDIN INT,
    userIDOUT INT,
    remarks TEXT,
    created_at TIMESTAMP,
    updated_at TIMESTAMP
);

-- Tabel detail untuk line items (multiple produk)
CREATE TABLE trscale_details (
    id BIGINT PRIMARY KEY AUTO_INCREMENT,
    header_id BIGINT,              -- FK ke trscale_headers
    itemCode VARCHAR(255),
    itemName VARCHAR(255),
    itemType VARCHAR(255),
    b10QtyKarung INT,              -- Jumlah karung produk ini
    historical_avg DECIMAL(10,2),  -- Avg historis dari master data
    expected_weight DECIMAL(10,2), -- qty × historical_avg
    actual_weight DECIMAL(10,2),   -- Hasil distribusi dari total actual
    avgKarung DECIMAL(10,2),       -- actual_weight / b10QtyKarung
    spmID INT,
    sppbID INT,
    isApp INT,
    created_at TIMESTAMP,
    updated_at TIMESTAMP,
    
    FOREIGN KEY (header_id) REFERENCES trscale_headers(id) ON DELETE CASCADE
);

-- Tabel untuk menyimpan historical average per produk
CREATE TABLE product_avg_history (
    id BIGINT PRIMARY KEY AUTO_INCREMENT,
    itemCode VARCHAR(255),
    avg_per_karung DECIMAL(10,2),
    total_records INT,
    last_updated DATETIME,
    UNIQUE KEY (itemCode)
);
```

### B. Algorithm Flow

```
STEP 1: INPUT PHASE (Timbang Masuk)
┌─────────────────────────────────────────┐
│ User input:                             │
│ - Driver, CarID, Customer, etc          │
│ - Produk 1: itemCode, qty karung        │
│ - Produk 2: itemCode, qty karung        │
│ - Produk N: itemCode, qty karung        │
│ - Timbang Masuk (total weight in)      │
└─────────────────────────────────────────┘
            ↓
┌─────────────────────────────────────────┐
│ System calculate:                       │
│ Untuk setiap produk:                    │
│   1. Ambil historical_avg dari DB       │
│   2. expected_weight = qty × hist_avg   │
│   3. Total expected = SUM(expected_wt)  │
└─────────────────────────────────────────┘
            ↓
┌─────────────────────────────────────────┐
│ Save to database:                       │
│ - Header → trscale_headers              │
│ - Each product → trscale_details        │
└─────────────────────────────────────────┘

STEP 2: OUTPUT PHASE (Timbang Keluar)
┌─────────────────────────────────────────┐
│ Load transaction:                       │
│ - Header data (driver, car, etc)        │
│ - Detail data (all products)            │
└─────────────────────────────────────────┘
            ↓
┌─────────────────────────────────────────┐
│ Timbang keluar (actual weight out)     │
└─────────────────────────────────────────┘
            ↓
┌─────────────────────────────────────────┐
│ Calculate distribution:                 │
│                                         │
│ actual_netto = timbangin - timbangout   │
│                                         │
│ total_expected = SUM(qty × hist_avg)    │
│                                         │
│ adjustment_factor =                     │
│     actual_netto / total_expected       │
│                                         │
│ Untuk setiap produk:                    │
│   actual_weight =                       │
│     expected_weight × adjustment_factor │
│                                         │
│   avgKarung =                           │
│     actual_weight / qty_karung          │
└─────────────────────────────────────────┘
            ↓
┌─────────────────────────────────────────┐
│ Update trscale_details:                 │
│ - actual_weight                         │
│ - avgKarung                             │
│                                         │
│ Update product_avg_history:             │
│ - Rolling average per product           │
└─────────────────────────────────────────┘
```

### C. Contoh Perhitungan Numerik

```
INPUT DATA:
==========
Truk: B 1234 XYZ
Driver: Budi

Produk 1: Gula Pasir (itemCode: S001)
  - Qty: 100 karung
  - Historical avg: 40.5 kg/karung
  - Expected weight: 100 × 40.5 = 4,050 kg

Produk 2: Gula Kristal (itemCode: S002)
  - Qty: 50 karung
  - Historical avg: 35.2 kg/karung
  - Expected weight: 50 × 35.2 = 1,760 kg

Total Expected Weight: 4,050 + 1,760 = 5,810 kg

Timbang Masuk: 8,000 kg (truk + muatan)

PROSES TIMBANG KELUAR:
======================
Timbang Keluar: 3,000 kg (truk kosong)

Actual Netto: 8,000 - 3,000 = 5,000 kg

Adjustment Factor: 5,000 / 5,810 = 0.860586

DISTRIBUSI PER PRODUK:
=====================
Produk 1 (Gula Pasir):
  - Actual weight: 4,050 × 0.860586 = 3,485.37 kg
  - Avg per karung: 3,485.37 / 100 = 34.85 kg/karung
  
Produk 2 (Gula Kristal):
  - Actual weight: 1,760 × 0.860586 = 1,514.63 kg
  - Avg per karung: 1,514.63 / 50 = 30.29 kg/karung

VERIFIKASI:
==========
Total: 3,485.37 + 1,514.63 = 5,000 kg ✓ (match actual netto)
```

### D. Validation Rules

```php
// Saat timbang keluar, cek apakah adjustment factor masuk akal
if ($adjustment_factor < 0.85 || $adjustment_factor > 1.15) {
    // Selisih > 15% dari expected → warning
    return [
        'warning' => true,
        'message' => "Selisih berat actual vs expected > 15%. Mohon cek ulang.",
        'expected' => $total_expected,
        'actual' => $actual_netto,
        'difference' => abs($actual_netto - $total_expected)
    ];
}

// Cek per-produk avgKarung apakah masih dalam range normal
foreach ($products as $product) {
    $deviation = abs($product->avgKarung - $product->historical_avg) 
                 / $product->historical_avg * 100;
    
    if ($deviation > 20) {
        $warnings[] = [
            'product' => $product->itemName,
            'expected_avg' => $product->historical_avg,
            'calculated_avg' => $product->avgKarung,
            'deviation' => $deviation . '%'
        ];
    }
}
```

### E. Update Historical Average

```php
// Setelah timbang keluar selesai, update historical average
foreach ($details as $detail) {
    $history = ProductAvgHistory::where('itemCode', $detail->itemCode)->first();
    
    if ($history) {
        // Rolling average formula
        $new_total_records = $history->total_records + 1;
        $new_avg = (($history->avg_per_karung * $history->total_records) 
                    + $detail->avgKarung) / $new_total_records;
        
        $history->update([
            'avg_per_karung' => $new_avg,
            'total_records' => $new_total_records,
            'last_updated' => now()
        ]);
    } else {
        // First record for this product
        ProductAvgHistory::create([
            'itemCode' => $detail->itemCode,
            'avg_per_karung' => $detail->avgKarung,
            'total_records' => 1,
            'last_updated' => now()
        ]);
    }
}
```

---

## SOLUSI 2: Sequential Weighing with Shared Transaction

### Konsep
Timbang per-produk secara berurutan, tapi dalam 1 transaksi yang sama

### Flow
```
1. Create header transaction
2. Timbang kosong: 3,000 kg
3. Muat Produk A (100 karung) → timbang: 7,050 kg
   → Netto A: 7,050 - 3,000 = 4,050 kg
   → Avg A: 4,050 / 100 = 40.5 kg/karung
   
4. Muat Produk B (50 karung) → timbang: 8,810 kg
   → Netto B: 8,810 - 7,050 = 1,760 kg
   → Avg B: 1,760 / 50 = 35.2 kg/karung
   
5. Timbang keluar final: 3,100 kg
   → Verifikasi: expected 3,000 vs actual 3,100 = diff 100kg
```

### Database Structure
```sql
CREATE TABLE trscale_multi_weigh (
    id BIGINT PRIMARY KEY,
    header_id BIGINT,
    sequence INT,                    -- 1, 2, 3, dst
    itemCode VARCHAR(255),
    qty_karung INT,
    weight_before DECIMAL(10,2),     -- Berat sebelum muat produk ini
    weight_after DECIMAL(10,2),      -- Berat setelah muat produk ini
    netto DECIMAL(10,2),             -- weight_after - weight_before
    avgKarung DECIMAL(10,2)          -- netto / qty_karung
);
```

### Keunggulan:
- **Akurat 100%** - tidak pakai estimasi
- Data real per-produk

### Kelemahan:
- Lebih lama (multiple weighing)
- Butuh modifikasi proses loading
- Driver/operator harus disiplin

---

## SOLUSI 3: Hybrid Approach

### Konsep
Kombinasi: gunakan historical average untuk produk-produk yang stabil, 
tapi tetap allow manual override jika diperlukan

### Implementation
```php
class TrscaleDetail extends Model {
    // Flag untuk identifikasi metode perhitungan
    protected $fillable = [
        'calculation_method',  // 'historical', 'manual', 'sequential'
        'manual_override',     // true/false
        'override_reason',     // alasan jika di-override
    ];
}

// Saat timbang keluar
if ($detail->manual_override) {
    // User input manual weight untuk produk ini
    $detail->actual_weight = $manual_input;
    $detail->avgKarung = $manual_input / $detail->qty_karung;
    $detail->calculation_method = 'manual';
} else {
    // Gunakan historical method
    $detail->actual_weight = $expected_weight * $adjustment_factor;
    $detail->avgKarung = $detail->actual_weight / $detail->qty_karung;
    $detail->calculation_method = 'historical';
}
```

---

## PERBANDINGAN SOLUSI

| Aspek | Sol 1: Historical | Sol 2: Sequential | Sol 3: Hybrid |
|-------|-------------------|-------------------|---------------|
| **Akurasi** | 85-95% (estimasi) | 100% (exact) | 90-100% |
| **Kecepatan** | Cepat (1x timbang) | Lambat (Nx timbang) | Fleksibel |
| **Kompleksitas** | Medium | Low | High |
| **User Training** | Medium | High | High |
| **Maintenance** | Perlu update hist data | Simple | Complex |
| **Rekomendasi** | ⭐⭐⭐⭐⭐ | ⭐⭐⭐ | ⭐⭐⭐⭐ |

---

## KESIMPULAN & REKOMENDASI

### ✅ Gunakan SOLUSI 1 (Historical Average Method) jika:
- Produk relatif stabil/konsisten per batch
- Butuh proses cepat
- Volume transaksi tinggi
- Toleransi error 5-10% masih acceptable

### ✅ Gunakan SOLUSI 2 (Sequential Weighing) jika:
- Butuh akurasi 100%
- Volume transaksi rendah
- Ada waktu untuk multiple weighing
- Produk sangat bervariasi

### ✅ Gunakan SOLUSI 3 (Hybrid) jika:
- Mix antara produk stabil dan variabel
- Butuh fleksibilitas
- Ada resource untuk implement complex logic

---

## IMPLEMENTASI LANGKAH PERTAMA

Untuk mulai, saya sarankan:

1. **Buat migration untuk tabel baru**
2. **Modify Livewire component untuk support multiple products**
3. **Create service class untuk calculation logic**
4. **Update UI untuk input multiple products**
5. **Create seeder untuk initial historical data**

Apakah Anda ingin saya buatkan kode implementasi untuk Solusi 1?
