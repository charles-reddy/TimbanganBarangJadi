# Saran Implementasi Opsi B: Input B10 Multi Product dengan Koreksi

## 📋 Overview

Implementasi input B10 untuk Multi Product Weighing dengan flow terpisah dan kemampuan koreksi jika average out of range.

---

## 🔄 Flow Diagram

```
┌─────────────────────────────────────────────────────────────────────────┐
│ 1. TIMBANG MASUK (Multi Product Weighing In)                           │
│    - Pilih multiple SPM (truk sama, driver boleh beda)                  │
│    - Input: Driver, CarID, Customer, Transporter, DO/PO                 │
│    - Input: Tare Weight, Remarks                                        │
│    - TANPA Input B10 (belum perlu input qty karung actual)             │
│    - Status: WEIGHING_IN                                                │
│    - TIDAK redirect kemana-mana (tetap di halaman weighing in)         │
└────────────────────────┬────────────────────────────────────────────────┘
                         │
                         ▼
┌─────────────────────────────────────────────────────────────────────────┐
│ 2. INPUT B10 MULTI PRODUCT (Menu Terpisah)                             │
│    - Role: operator-b10, supervisor-b10                                 │
│    - Tampilkan transaksi dengan status WEIGHING_IN                     │
│    - Per transaksi: tampilkan list products (dari header_id)           │
│    - Input per product:                                                 │
│      • B10 Qty Karung (actual) *required                               │
│      • Batch No *required                                               │
│      • Container No                                                     │
│      • Krani *required                                                  │
│      • Upload Foto Form Loading *required                              │
│    - Validasi: Semua product harus diisi sebelum bisa submit           │
│    - Setelah submit:                                                    │
│      • Status berubah: WEIGHING_IN → READY_FOR_WEIGH_OUT              │
│      • isLoadingDone = true, isLoadingDoneDate = now()                 │
└────────────────────────┬────────────────────────────────────────────────┘
                         │
                         ▼
┌─────────────────────────────────────────────────────────────────────────┐
│ 3. TIMBANG KELUAR (Multi Product Weighing Out)                         │
│    - Hanya tampilkan transaksi dengan status: READY_FOR_WEIGH_OUT      │
│    - Input: Gross Weight                                                │
│    - Perhitungan:                                                       │
│      • Net Weight = Gross - Tare                                        │
│      • Correction Factor K = Net / Theoretical Weight                   │
│      • Actual Weight (per product) = Theoretical × K                    │
│      • **Avg Per Karung = Actual Weight / B10QtyKarung**               │
│        (BUKAN qty_karung dari SPM, TAPI dari input B10!)              │
│    - Validasi Range:                                                    │
│      • Total Range Min = Σ(b10QtyKarung × gross_min)                   │
│      • Total Range Max = Σ(b10QtyKarung × gross_max)                   │
│      • In Range: Net Weight between [Range Min, Range Max]             │
│    - Status:                                                            │
│      • In Range → COMPLETED                                             │
│      • Out of Range → PENDING_B10_CORRECTION                           │
└────────────────────────┬────────────────────────────────────────────────┘
                         │
                ┌────────┴────────┐
                │                 │
         In Range              Out of Range
                │                 │
                ▼                 ▼
┌────────────────────┐  ┌─────────────────────────────────────────────────┐
│ 4a. COMPLETED      │  │ 4b. KOREKSI B10 (Out of Range)                 │
│                    │  │     - Role: operator-b10, supervisor-b10        │
│ Selesai            │  │     - Status: PENDING_B10_CORRECTION            │
│                    │  │     - Tampilkan:                                │
└────────────────────┘  │       • Net Weight, Correction Factor           │
                        │       • List products dengan avg_per_karung     │
                        │       • Highlight yang out of range             │
                        │     - User B10 bisa:                            │
                        │       OPSI 1: Edit B10 Qty Karung               │
                        │         • Input ulang qty karung yang benar     │
                        │         • System recalculate avg_per_karung     │
                        │         • Cek range lagi                        │
                        │         • Upload bukti koreksi (3 foto)         │
                        │         • Loop sampai in range                  │
                        │         • Status → COMPLETED                    │
                        │                                                 │
                        │       OPSI 2: Submit untuk Approval             │
                        │         • Tidak koreksi qty karung              │
                        │         • Upload bukti (3 foto)                 │
                        │         • Submit ke approval                    │
                        │         • Status → PENDING_APPROVAL             │
                        └──────────────────┬──────────────────────────────┘
                                           │
                                           ▼
                        ┌──────────────────────────────────────────────────┐
                        │ 5. APPROVAL (Multi Product Approval)             │
                        │    - Role: manager-logistik, administrator       │
                        │    - Status: PENDING_APPROVAL                    │
                        │    - Review:                                     │
                        │      • Transaksi out of range                    │
                        │      • Product details dengan deviasi            │
                        │      • Bukti foto koreksi                        │
                        │    - Action:                                     │
                        │      • APPROVE → Status: APPROVED                │
                        │      • REJECT → Status: REJECTED                 │
                        │        (bisa dikembalikan ke B10 untuk koreksi)  │
                        └──────────────────────────────────────────────────┘
```

---

## 🗄️ Database Structure Changes

### 1. Update `trscale_details` Table

```sql
-- Tambah kolom untuk input B10
ALTER TABLE trscale_details ADD COLUMN b10QtyKarung INT NULL COMMENT 'Qty karung actual dari B10';
ALTER TABLE trscale_details ADD COLUMN b10BatchNo VARCHAR(50) NULL COMMENT 'Batch number dari B10';
ALTER TABLE trscale_details ADD COLUMN kontainerNo VARCHAR(50) NULL COMMENT 'Container number';
ALTER TABLE trscale_details ADD COLUMN krani VARCHAR(100) NULL COMMENT 'Nama krani yang input';
ALTER TABLE trscale_details ADD COLUMN imgFormLoading VARCHAR(255) NULL COMMENT 'Path foto form loading';

-- Kolom untuk koreksi B10 (jika out of range)
ALTER TABLE trscale_details ADD COLUMN b10QtyKarung_original INT NULL COMMENT 'Qty karung original sebelum koreksi';
ALTER TABLE trscale_details ADD COLUMN b10_correction_count INT DEFAULT 0 COMMENT 'Jumlah koreksi yang dilakukan';
ALTER TABLE trscale_details ADD COLUMN b10_corrected_by INT NULL COMMENT 'User ID yang koreksi';
ALTER TABLE trscale_details ADD COLUMN b10_corrected_at DATETIME NULL COMMENT 'Tanggal koreksi';
ALTER TABLE trscale_details ADD COLUMN buktiKoreksi1 VARCHAR(255) NULL COMMENT 'Foto bukti koreksi 1';
ALTER TABLE trscale_details ADD COLUMN buktiKoreksi2 VARCHAR(255) NULL COMMENT 'Foto bukti koreksi 2';
ALTER TABLE trscale_details ADD COLUMN buktiKoreksi3 VARCHAR(255) NULL COMMENT 'Foto bukti koreksi 3';

-- Index untuk performance
CREATE INDEX idx_trscale_details_isLoadingDone ON trscale_details(isLoadingDone);
```

### 2. Update `trscale_headers` Table

```sql
-- Tambah status baru
ALTER TABLE trscale_headers MODIFY COLUMN status ENUM(
    'WEIGHING_IN',           -- Setelah timbang masuk
    'READY_FOR_WEIGH_OUT',   -- Setelah input B10 selesai
    'WEIGHING_OUT',          -- Setelah timbang keluar
    'PENDING_B10_CORRECTION',-- Out of range, menunggu koreksi B10
    'PENDING_APPROVAL',      -- Submitted untuk approval (jika tidak dikoreksi)
    'APPROVED',              -- Approved oleh manager
    'REJECTED',              -- Rejected, perlu koreksi ulang
    'COMPLETED'              -- In range dan selesai
) DEFAULT 'WEIGHING_IN';

-- Tambah kolom tracking koreksi
ALTER TABLE trscale_headers ADD COLUMN b10_input_by INT NULL COMMENT 'User ID yang input B10';
ALTER TABLE trscale_headers ADD COLUMN b10_input_at DATETIME NULL COMMENT 'Tanggal input B10';
ALTER TABLE trscale_headers ADD COLUMN needs_b10_correction BOOLEAN DEFAULT FALSE COMMENT 'Flag jika perlu koreksi B10';
ALTER TABLE trscale_headers ADD COLUMN correction_submitted BOOLEAN DEFAULT FALSE COMMENT 'Flag jika sudah submit koreksi untuk approval';
```

### 3. Create `trscale_b10_corrections` Table (History Koreksi)

```sql
CREATE TABLE trscale_b10_corrections (
    id INT AUTO_INCREMENT PRIMARY KEY,
    header_id INT NOT NULL,
    detail_id INT NOT NULL,
    correction_number INT NOT NULL COMMENT 'Koreksi ke-n',
    old_b10_qty_karung INT NOT NULL,
    new_b10_qty_karung INT NOT NULL,
    old_avg_per_karung DECIMAL(10,2),
    new_avg_per_karung DECIMAL(10,2),
    reason TEXT COMMENT 'Alasan koreksi',
    corrected_by INT NOT NULL,
    corrected_at DATETIME NOT NULL,
    bukti_foto_1 VARCHAR(255),
    bukti_foto_2 VARCHAR(255),
    bukti_foto_3 VARCHAR(255),
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,

    FOREIGN KEY (header_id) REFERENCES trscale_headers(id) ON DELETE CASCADE,
    FOREIGN KEY (detail_id) REFERENCES trscale_details(id) ON DELETE CASCADE,
    FOREIGN KEY (corrected_by) REFERENCES users(id),

    INDEX idx_header_id (header_id),
    INDEX idx_detail_id (detail_id)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
```

---

## 📁 File Structure

```
app/
├── Livewire/
│   ├── MultiProductWeighingIn.php       # Existing (no major changes)
│   ├── MultiProductInputB10.php         # NEW - Input B10 per product
│   ├── MultiProductKoreksiB10.php       # NEW - Koreksi B10 jika out of range
│   ├── MultiProductWeighingOut.php      # UPDATE - Gunakan b10QtyKarung untuk average
│   └── MultiProductApproval.php         # UPDATE - Handle approval out of range
│
├── Models/
│   └── TrscaleB10Correction.php         # NEW - Model untuk history koreksi
│
└── Services/
    └── MultiProductWeighingService.php  # UPDATE - Logic perhitungan dengan B10

resources/views/livewire/
├── multi-product-weighing-in.blade.php  # Existing (no major changes)
├── multi-product-input-b10.blade.php    # NEW
├── multi-product-koreksi-b10.blade.php  # NEW
├── multi-product-weighing-out.blade.php # UPDATE
└── multi-product-approval.blade.php     # UPDATE

routes/web.php                            # Add new routes
```

---

## 🎨 UI Components (Saran Layout)

### 1. Menu Navigation (Update)

```blade
<!-- Untuk Role: operator-b10, supervisor-b10 -->
<x-dropdown-link :href="route('multi-product-input-b10')">
    <i class="bi bi-pencil-square"></i> Input B10 Multi Product
</x-dropdown-link>

<x-dropdown-link :href="route('multi-product-koreksi-b10')">
    <i class="bi bi-arrow-repeat"></i> Koreksi B10 (Out of Range)
</x-dropdown-link>
```

### 2. Halaman Input B10 Multi Product (NEW)

**Layout:**

```
┌─────────────────────────────────────────────────────────────────┐
│ 🟦 Header: Input B10 Multi Product                             │
└─────────────────────────────────────────────────────────────────┘

┌─────────────────────────────────────────────────────────────────┐
│ 🔍 Filter:                                                      │
│   [Search Trans No/Car ID] [Filter Date: Today ▾]              │
└─────────────────────────────────────────────────────────────────┘

┌─────────────────────────────────────────────────────────────────┐
│ 📋 List Transaksi (Status: WEIGHING_IN)                        │
│                                                                 │
│ ┌───────────────────────────────────────────────────────────┐ │
│ │ Trans No: TRX/2026/07/0001                                │ │
│ │ Driver: BUDI | Car ID: B 1234 CD                          │ │
│ │ Customer: PT. ABC | Tare Weight: 5000 kg                  │ │
│ │ Weigh In: 13/07/2026 08:30                                │ │
│ │ Products: 3 items                                          │ │
│ │                                                            │ │
│ │ Status: ⏳ MENUNGGU INPUT B10 (0/3 completed)             │ │
│ │                                                            │ │
│ │ [📝 Input B10]                                            │ │
│ └───────────────────────────────────────────────────────────┘ │
│                                                                 │
│ ┌───────────────────────────────────────────────────────────┐ │
│ │ Trans No: TRX/2026/07/0002                                │ │
│ │ ...                                                        │ │
│ └───────────────────────────────────────────────────────────┘ │
└─────────────────────────────────────────────────────────────────┘
```

**Modal Input B10:**

```
┌─────────────────────────────────────────────────────────────────┐
│ 📝 Input B10 - Trans No: TRX/2026/07/0001                      │
│ Driver: BUDI | Car ID: B 1234 CD                                │
└─────────────────────────────────────────────────────────────────┘

Table: Input Data per Product
┌────────┬──────────────┬─────────┬──────────┬──────────┬────────┬──────┬──────────┐
│ No     │ SPM No       │ Product │ Qty SPM  │ B10 Qty* │ Batch* │Kontn │ Krani*   │
│        │              │         │ (info)   │          │  No    │ No   │          │
├────────┼──────────────┼─────────┼──────────┼──────────┼────────┼──────┼──────────┤
│ 1      │ SPM-001      │ FLOUR   │ 500      │ [____]   │ [___]  │[___] │ [_____]  │
│        │              │         │          │          │        │      │ Upload   │
│        │              │         │          │          │        │      │ [📷]     │
├────────┼──────────────┼─────────┼──────────┼──────────┼────────┼──────┼──────────┤
│ 2      │ SPM-002      │ SUGAR   │ 300      │ [____]   │ [___]  │[___] │ [_____]  │
│        │              │         │          │          │        │      │ [📷]     │
├────────┼──────────────┼─────────┼──────────┼──────────┼────────┼──────┼──────────┤
│ 3      │ SPM-003      │ SALT    │ 200      │ [____]   │ [___]  │[___] │ [_____]  │
│        │              │         │          │          │        │      │ [📷]     │
└────────┴──────────────┴─────────┴──────────┴──────────┴────────┴──────┴──────────┘

⚠️ Info:
• Qty SPM adalah referensi dari create SPM
• B10 Qty adalah jumlah karung ACTUAL yang di-load (wajib diisi)
• Semua field dengan (*) wajib diisi untuk semua product
• Upload foto form loading untuk setiap product

[❌ Batal]  [💾 Simpan Semua]
```

### 3. Halaman Koreksi B10 (NEW)

**Layout:**

```
┌─────────────────────────────────────────────────────────────────┐
│ 🔄 Koreksi B10 - Transaksi Out of Range                        │
└─────────────────────────────────────────────────────────────────┘

┌─────────────────────────────────────────────────────────────────┐
│ 📋 List Transaksi (Status: PENDING_B10_CORRECTION)             │
│                                                                 │
│ ┌───────────────────────────────────────────────────────────┐ │
│ │ Trans No: TRX/2026/07/0005                                │ │
│ │ Driver: BUDI | Car ID: B 1234 CD                          │ │
│ │ Gross: 25500 kg | Tare: 5000 kg | Net: 20500 kg          │ │
│ │ Correction Factor K: 0.98                                 │ │
│ │                                                            │ │
│ │ ❌ OUT OF RANGE                                           │ │
│ │ Range: 20800 - 21200 kg | Net: 20500 kg (❌ Too Low)     │ │
│ │                                                            │ │
│ │ Products:                                                  │ │
│ │ • FLOUR: Avg 41.2 kg/karung (Range: 42-43) ❌            │ │
│ │ • SUGAR: Avg 42.5 kg/karung (Range: 42-43) ✅            │ │
│ │                                                            │ │
│ │ [🔧 Koreksi B10]  [✓ Submit untuk Approval]              │ │
│ └───────────────────────────────────────────────────────────┘ │
└─────────────────────────────────────────────────────────────────┘
```

**Modal Koreksi:**

```
┌─────────────────────────────────────────────────────────────────┐
│ 🔧 Koreksi B10 - Trans No: TRX/2026/07/0005                    │
└─────────────────────────────────────────────────────────────────┘

Table: Koreksi Qty Karung
┌────┬─────────┬──────────┬──────────┬──────────┬─────────┬─────────────┐
│ No │ Product │ B10 Qty  │ Avg Now  │ Range    │ Status  │ B10 Qty Baru│
│    │         │ Sekarang │          │          │         │ (Koreksi)   │
├────┼─────────┼──────────┼──────────┼──────────┼─────────┼─────────────┤
│ 1  │ FLOUR   │ 500      │ 41.2 kg  │ 42-43 kg │ ❌ Low  │ [_______]   │
│    │         │          │          │          │         │ (editable)  │
├────┼─────────┼──────────┼──────────┼──────────┼─────────┼─────────────┤
│ 2  │ SUGAR   │ 300      │ 42.5 kg  │ 42-43 kg │ ✅ OK   │ 300         │
│    │         │          │          │          │         │ (readonly)  │
└────┴─────────┴──────────┴──────────┴──────────┴─────────┴─────────────┘

💡 Live Calculation Preview:
• Net Weight: 20500 kg
• Total Range Min: 20800 kg (❌ Net < Range Min)
• Total Range Max: 21200 kg

Jika B10 Qty FLOUR diubah ke 495:
• New Avg: 41.6 kg/karung
• New Total Range Min: 20700 kg (✅ Net dalam range!)

Alasan Koreksi:
[_____________________________________________________________]
[_____________________________________________________________]

Upload Bukti Koreksi (3 Foto):
[📷 Bukti 1*] [📷 Bukti 2*] [📷 Bukti 3]

[❌ Batal]  [💾 Simpan Koreksi & Recalculate]
```

---

## 💻 Implementation Code

### 1. Model: TrscaleB10Correction.php (NEW)

```php
<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class TrscaleB10Correction extends Model
{
    protected $table = 'trscale_b10_corrections';

    protected $fillable = [
        'header_id',
        'detail_id',
        'correction_number',
        'old_b10_qty_karung',
        'new_b10_qty_karung',
        'old_avg_per_karung',
        'new_avg_per_karung',
        'reason',
        'corrected_by',
        'corrected_at',
        'bukti_foto_1',
        'bukti_foto_2',
        'bukti_foto_3',
    ];

    protected $casts = [
        'corrected_at' => 'datetime',
        'old_avg_per_karung' => 'decimal:2',
        'new_avg_per_karung' => 'decimal:2',
    ];

    public function header(): BelongsTo
    {
        return $this->belongsTo(TrscaleHeader::class, 'header_id');
    }

    public function detail(): BelongsTo
    {
        return $this->belongsTo(TrscaleDetail::class, 'detail_id');
    }

    public function corrector(): BelongsTo
    {
        return $this->belongsTo(User::class, 'corrected_by');
    }
}
```

### 2. Update Model: TrscaleDetail.php

```php
// Add to fillable array
protected $fillable = [
    // ... existing fields ...
    'b10QtyKarung',
    'b10BatchNo',
    'kontainerNo',
    'krani',
    'imgFormLoading',
    'b10QtyKarung_original',
    'b10_correction_count',
    'b10_corrected_by',
    'b10_corrected_at',
    'buktiKoreksi1',
    'buktiKoreksi2',
    'buktiKoreksi3',
];

// Add relationship
public function corrections()
{
    return $this->hasMany(TrscaleB10Correction::class, 'detail_id');
}

public function corrector()
{
    return $this->belongsTo(User::class, 'b10_corrected_by');
}
```

### 3. Livewire Component: MultiProductInputB10.php (NEW)

```php
<?php

namespace App\Livewire;

use Livewire\Component;
use Livewire\WithPagination;
use Livewire\WithFileUploads;
use App\Models\TrscaleHeader;
use App\Models\TrscaleDetail;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;

class MultiProductInputB10 extends Component
{
    use WithPagination, WithFileUploads;

    protected $paginationTheme = 'bootstrap';

    public $search = '';
    public $filterDate = '';
    public $showModal = false;
    public $selectedHeader = null;

    // Form data per product (array)
    public $b10Data = [];
    public $uploadedFiles = [];

    public function mount()
    {
        $this->filterDate = date('Y-m-d');
    }

    public function openInputModal($headerId)
    {
        $this->selectedHeader = TrscaleHeader::with(['details.spm.product'])->findOrFail($headerId);

        // Initialize b10Data array
        $this->b10Data = [];
        foreach ($this->selectedHeader->details as $detail) {
            $this->b10Data[$detail->id] = [
                'b10QtyKarung' => $detail->b10QtyKarung ?? '',
                'b10BatchNo' => $detail->b10BatchNo ?? '',
                'kontainerNo' => $detail->kontainerNo ?? '',
                'krani' => $detail->krani ?? '',
                'imgFormLoading' => null,
            ];
        }

        $this->showModal = true;
    }

    public function saveB10Data()
    {
        // Validation
        $rules = [];
        foreach ($this->selectedHeader->details as $detail) {
            $rules["b10Data.{$detail->id}.b10QtyKarung"] = 'required|integer|min:1';
            $rules["b10Data.{$detail->id}.b10BatchNo"] = 'required|string|max:50';
            $rules["b10Data.{$detail->id}.krani"] = 'required|string|max:100';
            $rules["uploadedFiles.{$detail->id}"] = 'required|image|max:1024'; // Max 1MB
        }

        $this->validate($rules, [
            'b10Data.*.b10QtyKarung.required' => 'Qty Karung B10 wajib diisi',
            'b10Data.*.b10QtyKarung.integer' => 'Qty Karung harus angka',
            'b10Data.*.b10BatchNo.required' => 'Batch No wajib diisi',
            'b10Data.*.krani.required' => 'Nama Krani wajib diisi',
            'uploadedFiles.*.required' => 'Foto Form Loading wajib diupload',
            'uploadedFiles.*.image' => 'File harus berupa gambar',
            'uploadedFiles.*.max' => 'Ukuran foto maksimal 1 MB',
        ]);

        DB::beginTransaction();

        try {
            foreach ($this->selectedHeader->details as $detail) {
                $data = $this->b10Data[$detail->id];

                // Upload photo
                $photoPath = null;
                if (isset($this->uploadedFiles[$detail->id])) {
                    $spmNo = str_replace("/", "-", $detail->spm->spmNo);
                    $fileName = $spmNo . '.jpg';
                    $photoPath = 'uploads/formloading/' . $fileName;
                    $this->uploadedFiles[$detail->id]->storeAs('uploads/formloading', $fileName, 'public');
                }

                // Update detail
                $detail->update([
                    'b10QtyKarung' => $data['b10QtyKarung'],
                    'b10BatchNo' => $data['b10BatchNo'],
                    'kontainerNo' => $data['kontainerNo'] ?? null,
                    'krani' => $data['krani'],
                    'imgFormLoading' => $photoPath,
                    'isLoadingDone' => true,
                    'isLoadingDoneDate' => Carbon::now(),
                ]);
            }

            // Update header status
            $this->selectedHeader->update([
                'status' => 'READY_FOR_WEIGH_OUT',
                'b10_input_by' => Auth::id(),
                'b10_input_at' => Carbon::now(),
            ]);

            DB::commit();

            session()->flash('success', "Input B10 berhasil untuk Trans No: {$this->selectedHeader->trans_no}");
            $this->closeModal();

        } catch (\Exception $e) {
            DB::rollBack();
            session()->flash('error', 'Error: ' . $e->getMessage());
        }
    }

    public function closeModal()
    {
        $this->showModal = false;
        $this->reset(['selectedHeader', 'b10Data', 'uploadedFiles']);
    }

    public function render()
    {
        $headers = TrscaleHeader::with(['details'])
            ->where('status', 'WEIGHING_IN')
            ->when($this->search, function ($query) {
                $query->where(function ($q) {
                    $q->where('trans_no', 'like', '%' . $this->search . '%')
                      ->orWhere('carID', 'like', '%' . $this->search . '%')
                      ->orWhere('driver', 'like', '%' . $this->search . '%');
                });
            })
            ->when($this->filterDate, function ($query) {
                $query->whereDate('weigh_in_time', $this->filterDate);
            })
            ->orderBy('weigh_in_time', 'desc')
            ->paginate(10);

        return view('livewire.multi-product-input-b10', [
            'headers' => $headers,
        ]);
    }
}
```

### 4. Update Service: MultiProductWeighingService.php

```php
/**
 * Process weigh-out transaction dengan B10 qty
 * Calculate correction factor menggunakan B10QtyKarung untuk average
 */
public function processWeighOut(int $headerId, float $grossWeight)
{
    DB::beginTransaction();

    try {
        $header = TrscaleHeader::with('details')->findOrFail($headerId);

        // Validate status
        if ($header->status !== 'READY_FOR_WEIGH_OUT') {
            throw new \Exception('Status transaksi harus READY_FOR_WEIGH_OUT (Input B10 sudah selesai) untuk melakukan timbang keluar');
        }

        // Validate all details have B10 data
        foreach ($header->details as $detail) {
            if (empty($detail->b10QtyKarung)) {
                throw new \Exception("Product {$detail->itemName} belum ada input B10. Silakan lengkapi input B10 terlebih dahulu.");
            }
        }

        // Calculate net weight
        $netWeight = $grossWeight - $header->tare_weight;

        if ($netWeight <= 0) {
            throw new \Exception('Net weight harus lebih besar dari 0');
        }

        // Calculate correction factor: K = net_weight / theoretical_weight
        $correctionFactor = $header->theoretical_weight > 0
            ? $netWeight / $header->theoretical_weight
            : 0;

        // Update header
        $header->update([
            'gross_weight' => $grossWeight,
            'net_weight' => $netWeight,
            'correction_factor' => $correctionFactor,
            'weigh_out_time' => now(),
            'user_out_id' => Auth::id(),
            'status' => 'WEIGHING_OUT',
        ]);

        // Calculate total range min dan max MENGGUNAKAN B10QtyKarung
        $totalRangeMin = 0;
        $totalRangeMax = 0;

        foreach ($header->details as $detail) {
            // ** KEY CHANGE: Gunakan b10QtyKarung bukan qty_karung **
            $totalRangeMin += $detail->b10QtyKarung * $detail->gross_min;
            $totalRangeMax += $detail->b10QtyKarung * $detail->gross_max;
        }

        // Check if net_weight is within total range
        $isInRange = ($netWeight >= $totalRangeMin) && ($netWeight <= $totalRangeMax);
        $needCorrection = !$isInRange;

        // Calculate actual weight untuk setiap detail
        foreach ($header->details as $detail) {
            // actual_weight = theoretical_weight × correction_factor
            $actualWeight = $detail->theoretical_weight * $correctionFactor;

            // ** KEY CHANGE: avg_per_karung = actual_weight / B10QtyKarung **
            $avgPerKarung = $detail->b10QtyKarung > 0
                ? $actualWeight / $detail->b10QtyKarung
                : 0;

            // Update detail
            $detail->update([
                'actual_weight' => $actualWeight,
                'avg_per_karung' => $avgPerKarung,
                'is_in_range' => $isInRange,
            ]);
        }

        // Update header dengan range info
        $header->update([
            'total_range_min' => $totalRangeMin,
            'total_range_max' => $totalRangeMax,
        ]);

        // Tentukan status final
        if ($needCorrection) {
            $header->update([
                'need_approval' => true,
                'needs_b10_correction' => true,
                'status' => 'PENDING_B10_CORRECTION', // Status baru: menunggu koreksi B10
            ]);
        } else {
            $header->update([
                'need_approval' => false,
                'needs_b10_correction' => false,
                'status' => 'COMPLETED',
            ]);
        }

        DB::commit();

        return $header->fresh(['details']);
    } catch (\Exception $e) {
        DB::rollBack();
        throw $e;
    }
}

/**
 * Koreksi B10 qty karung (recalculate average)
 */
public function correctB10(int $headerId, array $corrections, array $buktiFiles = [])
{
    DB::beginTransaction();

    try {
        $header = TrscaleHeader::with('details')->findOrFail($headerId);

        if ($header->status !== 'PENDING_B10_CORRECTION') {
            throw new \Exception('Status harus PENDING_B10_CORRECTION untuk melakukan koreksi');
        }

        foreach ($corrections as $detailId => $newQty) {
            $detail = $header->details->firstWhere('id', $detailId);

            if (!$detail) continue;

            $oldQty = $detail->b10QtyKarung;
            $oldAvg = $detail->avg_per_karung;

            // Save history sebelum koreksi
            if (!$detail->b10QtyKarung_original) {
                $detail->b10QtyKarung_original = $oldQty;
            }

            // Update qty karung
            $detail->b10QtyKarung = $newQty;
            $detail->b10_correction_count = ($detail->b10_correction_count ?? 0) + 1;
            $detail->b10_corrected_by = Auth::id();
            $detail->b10_corrected_at = now();

            // Recalculate avg_per_karung
            $newAvg = $newQty > 0 ? $detail->actual_weight / $newQty : 0;
            $detail->avg_per_karung = $newAvg;

            // Save bukti foto
            if (isset($buktiFiles[$detailId])) {
                $spmNo = str_replace("/", "-", $detail->spm->spmNo);
                $detail->buktiKoreksi1 = 'uploads/koreksi/' . $spmNo . '-1.jpg';
                $detail->buktiKoreksi2 = 'uploads/koreksi/' . $spmNo . '-2.jpg';
                $detail->buktiKoreksi3 = 'uploads/koreksi/' . $spmNo . '-3.jpg';
            }

            $detail->save();

            // Create correction history
            TrscaleB10Correction::create([
                'header_id' => $header->id,
                'detail_id' => $detail->id,
                'correction_number' => $detail->b10_correction_count,
                'old_b10_qty_karung' => $oldQty,
                'new_b10_qty_karung' => $newQty,
                'old_avg_per_karung' => $oldAvg,
                'new_avg_per_karung' => $newAvg,
                'corrected_by' => Auth::id(),
                'corrected_at' => now(),
                'bukti_foto_1' => $detail->buktiKoreksi1,
                'bukti_foto_2' => $detail->buktiKoreksi2,
                'bukti_foto_3' => $detail->buktiKoreksi3,
            ]);
        }

        // Recalculate total range dengan qty baru
        $totalRangeMin = 0;
        $totalRangeMax = 0;

        foreach ($header->details->fresh() as $detail) {
            $totalRangeMin += $detail->b10QtyKarung * $detail->gross_min;
            $totalRangeMax += $detail->b10QtyKarung * $detail->gross_max;
        }

        // Check range lagi
        $isInRange = ($header->net_weight >= $totalRangeMin) && ($header->net_weight <= $totalRangeMax);

        $header->update([
            'total_range_min' => $totalRangeMin,
            'total_range_max' => $totalRangeMax,
        ]);

        // Update status
        if ($isInRange) {
            // Koreksi berhasil, sekarang in range!
            foreach ($header->details as $detail) {
                $detail->update(['is_in_range' => true]);
            }

            $header->update([
                'status' => 'COMPLETED',
                'need_approval' => false,
                'needs_b10_correction' => false,
            ]);
        } else {
            // Masih out of range
            foreach ($header->details as $detail) {
                $avgInRange = ($detail->avg_per_karung >= $detail->gross_min) &&
                              ($detail->avg_per_karung <= $detail->gross_max);
                $detail->update(['is_in_range' => $avgInRange]);
            }

            // Status tetap PENDING_B10_CORRECTION
            // User bisa koreksi lagi atau submit untuk approval
        }

        DB::commit();

        return $header->fresh(['details']);
    } catch (\Exception $e) {
        DB::rollBack();
        throw $e;
    }
}

/**
 * Submit untuk approval (jika user B10 tidak mau koreksi lagi)
 */
public function submitForApproval(int $headerId, array $buktiFiles = [])
{
    DB::beginTransaction();

    try {
        $header = TrscaleHeader::with('details')->findOrFail($headerId);

        if ($header->status !== 'PENDING_B10_CORRECTION') {
            throw new \Exception('Status harus PENDING_B10_CORRECTION untuk submit approval');
        }

        // Upload bukti jika belum ada koreksi sebelumnya
        foreach ($header->details as $detail) {
            if (empty($detail->buktiKoreksi1) && isset($buktiFiles[$detail->id])) {
                $spmNo = str_replace("/", "-", $detail->spm->spmNo);
                $detail->buktiKoreksi1 = 'uploads/approval/' . $spmNo . '-1.jpg';
                $detail->buktiKoreksi2 = 'uploads/approval/' . $spmNo . '-2.jpg';
                $detail->buktiKoreksi3 = 'uploads/approval/' . $spmNo . '-3.jpg';
                $detail->save();
            }
        }

        $header->update([
            'status' => 'PENDING_APPROVAL',
            'correction_submitted' => true,
        ]);

        DB::commit();

        return $header->fresh(['details']);
    } catch (\Exception $e) {
        DB::rollBack();
        throw $e;
    }
}
```

---

## 🔐 Permissions & Roles

```php
// routes/web.php

// Role: operator-b10, supervisor-b10
Route::middleware(['auth', 'role:operator-b10|supervisor-b10|administrator'])->group(function () {
    Route::get('/multi-product-input-b10', function () {
        return view('multi-product-input-b10');
    })->name('multi-product-input-b10');

    Route::get('/multi-product-koreksi-b10', function () {
        return view('multi-product-koreksi-b10');
    })->name('multi-product-koreksi-b10');
});

// Role: manager-logistik, administrator (approval)
Route::middleware(['auth', 'role:manager-logistik|administrator'])->group(function () {
    Route::get('/multi-product-approval', function () {
        return view('multi-product-approval');
    })->name('multi-product-approval');
});
```

---

## 🎯 Key Benefits

✅ **Separation of Concerns:**

- Timbang masuk tidak terganggu dengan input B10
- User B10 punya menu dedicated untuk input data

✅ **Flexible Correction:**

- Jika out of range, bisa koreksi qty karung sampai benar
- Atau langsung submit ke approval jika yakin data sudah benar

✅ **Audit Trail:**

- History semua koreksi tersimpan di `trscale_b10_corrections`
- Bisa tracking siapa yang koreksi, kapan, dan alasan

✅ **Multiple Options:**

- Path 1: Koreksi B10 → In Range → Auto Completed
- Path 2: Submit Approval → Manager Review → Approved/Rejected

---

## 📊 Status Flow Summary

```
WEIGHING_IN
    ↓ (Input B10 selesai)
READY_FOR_WEIGH_OUT
    ↓ (Timbang keluar)
    ├─ In Range → COMPLETED ✅
    └─ Out of Range → PENDING_B10_CORRECTION
        ├─ Koreksi B10 → In Range → COMPLETED ✅
        ├─ Koreksi B10 → Still Out of Range → PENDING_B10_CORRECTION (loop)
        └─ Submit Approval → PENDING_APPROVAL
            ├─ Approve → APPROVED ✅
            └─ Reject → REJECTED ❌ (bisa dikembalikan ke B10 untuk koreksi ulang)
```

---

## ⚡ Next Steps

1. **Create Migration Files:**
    - `add_b10_fields_to_trscale_details`
    - `add_status_fields_to_trscale_headers`
    - `create_trscale_b10_corrections_table`

2. **Create Model:**
    - `TrscaleB10Correction.php`

3. **Create Livewire Components:**
    - `MultiProductInputB10.php`
    - `MultiProductKoreksiB10.php`

4. **Create Blade Views:**
    - `multi-product-input-b10.blade.php`
    - `multi-product-koreksi-b10.blade.php`

5. **Update Existing:**
    - `MultiProductWeighingService.php` (processWeighOut logic)
    - `MultiProductApproval.php` (handle PENDING_APPROVAL dari koreksi B10)

6. **Testing:**
    - Test flow input B10
    - Test koreksi B10 sampai in range
    - Test submit approval jika out of range

---

## 📝 Notes

- **Average Calculation:** Selalu menggunakan `b10QtyKarung` (bukan `qty_karung` dari SPM)
- **Validation:** Total range dihitung dengan `Σ(b10QtyKarung × gross_min/max)`
- **History:** Semua perubahan B10 tercatat untuk audit
- **Flexibility:** User bisa pilih koreksi atau langsung approve

---

**Apakah Anda ingin saya implementasikan kode lengkapnya sekarang?**
