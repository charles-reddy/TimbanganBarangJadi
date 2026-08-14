# Update: Koreksi B10 dengan Approval Tanpa Perubahan Qty

**Tanggal Update:** 14 Agustus 2026  
**Versi:** 1.1.0  
**Jenis Update:** Feature Enhancement - B10 Correction & Approval

---

## 📋 Overview

Update ini menambahkan kemampuan untuk submit transaksi out of range ke approval **tanpa harus mengubah qty karung**, cukup dengan upload foto bukti dan memberikan alasan. Ini memberikan fleksibilitas bagi operator untuk menyerahkan keputusan ke approver ketika koreksi qty tidak memungkinkan atau tidak diinginkan.

---

## 🎯 Tujuan Update

1. **Fleksibilitas Workflow**: Tidak semua kasus out of range harus diselesaikan dengan koreksi qty
2. **Transparansi**: Semua foto bukti dan alasan tetap terdokumentasi
3. **Audit Trail**: History lengkap dari setiap keputusan tetap tersimpan
4. **Efisiensi**: Mengurangi iterasi koreksi yang tidak perlu

---

## 🔄 Alur Kerja Baru

### Skenario 1: Koreksi dengan Perubahan Qty (Existing Flow)

```
Timbang Out → Out of Range → Koreksi B10 Page
    ↓
Ubah Qty Karung + Upload Foto + Isi Alasan
    ↓
Simpan Koreksi & Recalculate
    ↓
[Jika masih out of range] → b10_correction_count++ → Tetap di Koreksi B10 Page
[Jika dalam range] → Status: READY_FOR_WEIGH_OUT → Timbang Out Ulang
```

### Skenario 2: Submit ke Approval Tanpa Ubah Qty (NEW)

```
Timbang Out → Out of Range → Koreksi B10 Page
    ↓
TIDAK ubah Qty Karung + Upload Foto Bukti + Isi Alasan
    ↓
Simpan Koreksi (tanpa perubahan qty)
    ↓
Otomatis submit → Status: PENDING_APPROVAL
    ↓
Muncul di Multi-Product Approval dengan badge "📸 Submitted dgn foto bukti"
    ↓
Approver bisa:
    • Approve → Status: APPROVED
    • Reject → Status: REJECTED
    • Re-Weigh → Kembali timbang out ulang
```

---

## 💾 Perubahan Database

Tidak ada perubahan struktur database. Update ini menggunakan kolom yang sudah ada:

- `trscale_headers.correction_submitted` - Flag untuk menandai transaksi yang disubmit tanpa koreksi qty
- `trscale_headers.remarks` - Menyimpan alasan submit
- `trscale_details.buktiKoreksi1` - Foto bukti 1 (WAJIB)
- `trscale_details.buktiKoreksi2` - Foto bukti 2 (Opsional)
- `trscale_details.buktiKoreksi3` - Foto bukti 3 (Opsional)
- `trscale_details.b10_correction_count` - Tetap 0 jika tidak ada perubahan qty

---

## 📝 Perubahan Kode

### 1. File: `app/Livewire/MultiProductKoreksiB10.php`

**Method: `saveKoreksi()` - Line ~320-343**

```php
// Update status
if ($isInRange) {
    // Koreksi berhasil, sekarang in range!
    foreach ($this->selectedHeader->details as $detail) {
        $detail->update(['is_in_range' => true]);
    }

    // Kembali ke status READY_FOR_WEIGH_OUT untuk timbang out lagi
    $this->selectedHeader->update([
        'status' => 'READY_FOR_WEIGH_OUT',
        'need_approval' => false,
        'needs_b10_correction' => false,
        'correction_submitted' => false,
    ]);

    session()->flash('success', "Koreksi B10 berhasil! Trans No: {$this->selectedHeader->trans_no} sekarang dalam range...");
} else {
    // Masih out of range
    foreach ($this->selectedHeader->details as $detail) {
        $avgInRange = ($detail->avg_per_karung >= $detail->gross_min) &&
            ($detail->avg_per_karung <= $detail->gross_max);
        $detail->update(['is_in_range' => $avgInRange]);
    }

    // ⭐ NEW: Jika tidak ada perubahan qty tapi ada foto bukti + alasan → submit untuk approval
    if (!$hasQtyChanges) {
        $this->selectedHeader->update([
            'status' => 'PENDING_APPROVAL',
            'correction_submitted' => true,
            'need_approval' => true,
            'remarks' => ($this->selectedHeader->remarks ? $this->selectedHeader->remarks . "\n\n" : '') .
                "[" . now()->format('Y-m-d H:i:s') . "] Submitted for approval tanpa perubahan qty (foto bukti + alasan): " . $this->correctionReason,
        ]);

        session()->flash('success', "Trans No: {$this->selectedHeader->trans_no} telah disubmit untuk approval dengan {$uploadedCount} foto bukti...");
    } else {
        session()->flash('warning', "Koreksi B10 berhasil disimpan ({$uploadedCount} foto bukti terupload), namun masih out of range...");
    }
}
```

**Logika:**

- Cek variable `$hasQtyChanges` untuk mendeteksi apakah ada perubahan qty
- Jika tidak ada perubahan qty (`!$hasQtyChanges`), otomatis submit ke approval
- Set `correction_submitted = true` untuk tracking
- Simpan alasan ke `remarks` dengan timestamp

---

### 2. File: `resources/views/livewire/multi-product-koreksi-b10.blade.php`

**Perubahan UI Informasi:**

```blade
<!-- Preview Message -->
<small>Anda bisa koreksi lagi atau klik simpan untuk submit approval<br>
(tanpa ubah qty pun bisa, asal ada foto bukti + alasan)</small>

<!-- Alert Box -->
<div class="alert alert-warning">
    <strong><i class="bi bi-exclamation-triangle"></i> Perhatian:</strong><br>
    • Ubah <strong>B10 Qty Baru</strong> untuk product yang perlu dikoreksi<br>
    • Preview akan menampilkan perhitungan average yang baru<br>
    • <strong>Foto Bukti 1 WAJIB</strong> untuk setiap product yang dikoreksi<br>
    • Foto Bukti 2 dan 3 bersifat opsional (bisa diupload jika perlu)<br>
    • Jika setelah koreksi masih out of range, Anda bisa koreksi lagi atau submit untuk approval<br>
    • <strong>⭐ Bisa submit ke approval tanpa ubah qty</strong> (cukup upload foto bukti + isi alasan)<br>
    • Semua history koreksi akan tersimpan untuk audit
</div>
```

---

### 3. File: `resources/views/livewire/multi-product-approval.blade.php`

**Perubahan Badge Status:**

```blade
<td class="text-center align-middle">
    @if ($trans->status === 'PENDING_APPROVAL')
        <span class="badge bg-warning text-dark">⏳ Pending Approval</span>
        @php
            $correctionCount = $trans->details->max('b10_correction_count') ?? 0;
        @endphp
        @if ($correctionCount == 0 && $trans->correction_submitted)
            <!-- ⭐ NEW: Badge khusus untuk submit tanpa perubahan qty -->
            <br><small class="badge bg-info mt-1">📸 Submitted dgn foto bukti</small>
        @elseif ($correctionCount > 0)
            <br><small class="badge bg-secondary mt-1">{{ $correctionCount }}x koreksi</small>
        @endif
    @elseif ($trans->status === 'PENDING_B10_CORRECTION')
        <!-- ... existing code ... -->
    @endif
</td>
```

**Perubahan Alert Box di Modal Approve:**

```blade
{{-- ⭐ NEW: Show submission remarks if status is PENDING_APPROVAL with correction_submitted --}}
@if ($selectedTransaction->status === 'PENDING_APPROVAL' && $selectedTransaction->correction_submitted)
    <div class="alert alert-warning mb-3">
        <i class="bi bi-file-earmark-text"></i> <strong>Alasan Submit:</strong><br>
        @if ($selectedTransaction->remarks)
            {!! nl2br(e($selectedTransaction->remarks)) !!}
        @else
            <em class="text-muted">Tidak ada catatan</em>
        @endif
        @php
            $hasCorrectionCount = $selectedTransaction->details->some(function ($detail) {
                return $detail->b10_correction_count >= 1;
            });
        @endphp
        @if (!$hasCorrectionCount)
            <hr class="my-2">
            <small class="text-info">
                <i class="bi bi-info-circle"></i> Transaksi ini disubmit dengan foto bukti tanpa perubahan qty karung
            </small>
        @endif
    </div>
@endif
```

**Kolom Bukti Foto di Tabel Detail:**

```blade
<th rowspan="2" class="text-center" style="width: 100px;">Bukti Foto</th>
<!-- ... -->
<td class="text-center">
    @php
        $hasFotoBukti = $detail->buktiKoreksi1 || $detail->buktiKoreksi2 || $detail->buktiKoreksi3;
    @endphp
    @if ($hasFotoBukti)
        <div class="d-flex gap-1 justify-content-center">
            @if ($detail->buktiKoreksi1)
                <a href="{{ asset('storage/' . $detail->buktiKoreksi1) }}"
                   target="_blank"
                   class="btn btn-sm btn-outline-primary"
                   title="Bukti Foto 1">
                    <i class="bi bi-image"></i> 1
                </a>
            @endif
            <!-- Similar for foto 2 & 3 -->
        </div>
    @else
        <span class="text-muted">-</span>
    @endif
</td>
```

---

## 🎬 User Guide / Cara Penggunaan

### Untuk Operator B10

1. **Buka halaman "Koreksi B10"**
    - Menu: Koreksi B10 - Transaksi Out of Range

2. **Pilih transaksi yang out of range**
    - Klik tombol **"Koreksi B10"**

3. **Upload Foto Bukti (WAJIB minimal 1 foto)**
    - Foto Bukti 1: **WAJIB**
    - Foto Bukti 2 & 3: Opsional

4. **Isi Alasan Koreksi (WAJIB)**
    - Minimal 10 karakter
    - Jelaskan kondisi/situasi yang menyebabkan out of range

5. **PILIHAN A: Koreksi dengan ubah qty**
    - Ubah nilai di kolom "B10 Qty Baru"
    - Preview akan menampilkan perhitungan baru
    - Klik **"Simpan Koreksi & Recalculate"**

6. **PILIHAN B: Submit ke approval tanpa ubah qty (NEW)**
    - **JANGAN ubah** nilai di kolom "B10 Qty Baru" (biarkan default)
    - Pastikan foto bukti sudah terupload
    - Pastikan alasan sudah terisi
    - Klik **"Simpan Koreksi & Recalculate"**
    - Sistem akan otomatis submit ke approval

7. **Hasil**
    - Jika submit tanpa ubah qty → Sukses message: "Trans No: XXX telah disubmit untuk approval dengan N foto bukti"
    - Transaksi otomatis pindah ke halaman approval

### Untuk Approver

1. **Buka halaman "Approval Multi Product"**
    - Menu: Approval Multi Product

2. **Identifikasi transaksi yang disubmit tanpa koreksi qty**
    - Lihat badge: **📸 Submitted dgn foto bukti**

3. **Klik tombol "View" untuk melihat detail**
    - Alert box kuning akan menampilkan alasan submit
    - Info: "Transaksi ini disubmit dengan foto bukti tanpa perubahan qty karung"
    - Tabel detail menampilkan kolom "Bukti Foto" dengan button untuk lihat foto

4. **Lihat Foto Bukti**
    - Klik button **🖼️ 1, 2, atau 3** untuk membuka foto di tab baru

5. **Buat Keputusan**
    - **Approve**: Terima transaksi meskipun out of range
    - **Reject**: Tolak transaksi (butuh penjelasan lebih lanjut)
    - **Re-Weigh**: Reset transaksi untuk timbang keluar ulang

---

## ✅ Validasi & Business Rules

### Validasi Upload Foto

1. **Minimal 1 foto wajib terupload** (Foto Bukti 1)
2. **Format file**: JPG, JPEG, PNG
3. **Ukuran maksimal**: 2MB per file
4. **Total maksimal**: 3 foto per product

### Validasi Alasan

1. **Wajib diisi**
2. **Minimal 10 karakter**
3. **Disimpan ke database** dengan timestamp

### Kondisi Submit ke Approval Tanpa Ubah Qty

```php
// Syarat untuk otomatis submit ke approval:
if (!$hasQtyChanges && $hasBukti && $correctionReason) {
    // Submit ke PENDING_APPROVAL
}
```

Harus memenuhi SEMUA kondisi:

- ✅ Tidak ada perubahan qty karung (`!$hasQtyChanges`)
- ✅ Ada minimal 1 foto bukti terupload (`$hasBukti`)
- ✅ Alasan sudah diisi (`$correctionReason` valid)

### Status Transaksi di Approval

Transaksi akan muncul di approval page jika:

1. **Status = PENDING_APPROVAL** (termasuk yang disubmit tanpa ubah qty)
2. **Status = PENDING_B10_CORRECTION** dengan `b10_correction_count >= 1`

---

## 📊 Contoh Kasus Penggunaan

### Case 1: Driver Salah Input B10, Tapi Tidak Tahu Nilai yang Benar

**Situasi:**

- Timbang out: Net = 1950 kg
- Range: 1900-2000 kg (DALAM RANGE ✓)
- Driver input B10: 40 karung → Avg = 48.75 kg/karung
- Range per karung: 50-52 kg → **OUT OF RANGE** ✗

**Masalah:**

- Net weight sebenarnya dalam range total
- Tapi average per karung out of range karena input B10 salah
- Operator B10 tidak tahu nilai yang benar karena truk sudah pergi

**Solusi dengan Fitur Baru:**

1. Operator foto kondisi muatan (misal: karung tidak penuh, ada kerusakan, dll)
2. Upload foto + isi alasan: "Driver input 40 karung, tapi truk sudah pergi. Foto menunjukkan kondisi karung tidak standar."
3. **TIDAK ubah qty**, klik simpan
4. Sistem otomatis submit ke approval
5. Supervisor review foto dan approve

### Case 2: Kondisi Khusus (Hujan, Basah, dll)

**Situasi:**

- Produk basah karena hujan
- Average per karung lebih berat dari normal
- Secara visual, qty karung sudah benar

**Solusi dengan Fitur Baru:**

1. Foto kondisi karung basah
2. Alasan: "Karung basah karena hujan, berat bertambah sekitar 5%. Qty fisik sudah sesuai."
3. Submit tanpa ubah qty
4. Manager approve dengan catatan

---

## 🔍 Monitoring & Reporting

### Query untuk Monitoring

```sql
-- Transaksi yang disubmit tanpa perubahan qty
SELECT
    h.trans_no,
    h.driver,
    h.carID,
    h.status,
    h.correction_submitted,
    h.remarks,
    h.created_at
FROM trscale_headers h
WHERE h.status = 'PENDING_APPROVAL'
  AND h.correction_submitted = TRUE
  AND NOT EXISTS (
      SELECT 1 FROM trscale_details d
      WHERE d.header_id = h.id
      AND d.b10_correction_count > 0
  )
ORDER BY h.created_at DESC;
```

### Dashboard Metrics

Tambahan metrik yang bisa dimonitor:

- **Submit tanpa koreksi qty per hari/minggu/bulan**
- **Approval rate** untuk transaksi tanpa koreksi qty
- **Reject rate** untuk transaksi tanpa koreksi qty
- **Time to approval** untuk transaksi tanpa koreksi qty

---

## 🧪 Testing Checklist

### Functional Testing

- [ ] Upload foto bukti 1 (wajib) berhasil
- [ ] Upload foto bukti 2 & 3 (opsional) berhasil
- [ ] Validasi format file (hanya JPG/PNG)
- [ ] Validasi ukuran file (max 2MB)
- [ ] Validasi alasan (minimal 10 karakter)
- [ ] Submit tanpa ubah qty → Status = PENDING_APPROVAL
- [ ] Badge "📸 Submitted dgn foto bukti" muncul
- [ ] Alasan tampil di modal approve
- [ ] Foto bisa dibuka di tab baru
- [ ] Approve berhasil → Status = APPROVED
- [ ] Reject berhasil → Status = REJECTED
- [ ] Re-weigh berhasil → Status = READY_FOR_WEIGH_OUT

### Edge Cases

- [ ] Upload foto tanpa isi alasan → Error
- [ ] Isi alasan tanpa upload foto → Error
- [ ] Upload foto + isi alasan + ubah qty → Normal koreksi (bukan submit ke approval)
- [ ] Submit tanpa foto & alasan → Error

### Integration Testing

- [ ] Approval workflow lengkap (submit → approve → completed)
- [ ] Approval workflow lengkap (submit → reject → re-weigh)
- [ ] History transaksi tersimpan dengan benar
- [ ] Foto tersimpan di storage dengan naming yang benar

---

## 📌 Notes & Best Practices

### Untuk Operator

1. **Selalu upload foto yang jelas** - Foto harus menunjukkan kondisi yang menyebabkan out of range
2. **Alasan harus spesifik** - Hindari alasan generik seperti "out of range"
3. **Gunakan fitur ini hanya jika perlu** - Jika tahu nilai qty yang benar, lakukan koreksi qty

### Untuk Approver

1. **Review foto dengan teliti** - Pastikan foto mendukung alasan yang diberikan
2. **Cross-check dengan SOP** - Pastikan kondisi sesuai dengan SOP yang berlaku
3. **Dokumentasi keputusan** - Isi catatan approval dengan jelas

### Untuk Developer/Maintenance

1. **Storage management** - Monitor ukuran folder uploads/koreksi
2. **Cleanup policy** - Pertimbangkan kebijakan cleanup foto lama (misal: > 6 bulan)
3. **Backup** - Pastikan folder uploads/koreksi masuk dalam backup schedule

---

## 🔄 Rollback Plan

Jika terjadi masalah dan perlu rollback:

### 1. Rollback Kode

```bash
# Revert file yang diubah
git checkout HEAD~1 -- app/Livewire/MultiProductKoreksiB10.php
git checkout HEAD~1 -- resources/views/livewire/multi-product-koreksi-b10.blade.php
git checkout HEAD~1 -- resources/views/livewire/multi-product-approval.blade.php
```

### 2. Database - Tidak Perlu Rollback

Tidak ada perubahan struktur database, hanya penggunaan kolom existing.

### 3. Update Status Transaksi yang Pending

```sql
-- Jika ada transaksi yang stuck di PENDING_APPROVAL setelah rollback
UPDATE trscale_headers
SET status = 'PENDING_B10_CORRECTION',
    correction_submitted = FALSE,
    need_approval = FALSE
WHERE status = 'PENDING_APPROVAL'
  AND correction_submitted = TRUE
  AND NOT EXISTS (
      SELECT 1 FROM trscale_details d
      WHERE d.header_id = trscale_headers.id
      AND d.b10_correction_count > 0
  );
```

---

## 📞 Support & Contact

Jika ada pertanyaan atau masalah terkait fitur ini:

1. **Technical Issues**: Contact Development Team
2. **Business Process**: Contact Manager Logistik
3. **Training Request**: Contact HR/Training Department

---

## 📅 Change Log

| Tanggal     | Versi | Perubahan                                        | Author           |
| ----------- | ----- | ------------------------------------------------ | ---------------- |
| 14 Aug 2026 | 1.1.0 | Initial release - Submit approval tanpa ubah qty | Development Team |

---

## ✨ Future Enhancements (Roadmap)

Fitur-fitur yang bisa dikembangkan di masa depan:

1. **Notifikasi Real-time** - Push notification ke approver saat ada transaksi baru
2. **Mobile App Support** - Upload foto langsung dari mobile app
3. **OCR Integration** - Deteksi qty karung otomatis dari foto
4. **Analytics Dashboard** - Dashboard khusus untuk monitoring pattern out of range
5. **Auto Approval Rules** - Rule-based auto approval untuk kondisi tertentu (misal: deviasi < 5%)

---

**End of Documentation**
