# Update Fitur Ganti Tanggal Muat - Validasi Kuota Harian dan Shift

## Tanggal: 3 Juli 2026

## Deskripsi Perubahan

Fitur **Ganti Tanggal Muat (gantitgltm)** telah diupdate untuk mengikuti **kuota harian** dan **shift operasional**.

### Fitur Baru:

1. **Validasi Kuota Harian**
    - Sistem akan mengecek ketersediaan kuota pada tanggal tujuan
    - Menampilkan sisa kuota real-time saat memilih tanggal
    - Otomatis update kuota (kembalikan kuota lama, kurangi kuota baru)
    - Mencegah perubahan jika kuota tidak mencukupi

2. **Validasi Shift Operasional**
    - Shift 1: 08:00 - 12:00
    - Shift 2: 12:00 - 16:00
    - Shift 3: 16:00 - 20:00
    - Menampilkan shift otomatis berdasarkan jam muat yang dipilih
    - Mencegah perubahan di luar jam operasional

3. **Tampilan yang Ditingkatkan**
    - Input jam muat dengan time picker
    - Indikator shift real-time
    - Informasi kuota real-time
    - Tabel menampilkan jam muat dan shift

## File yang Diubah

### 1. Backend (PHP)

- **app/Livewire/Gantitgltm.php**
    - Tambah validasi kuota harian
    - Tambah validasi shift
    - Logika update kuota (kembalikan & kurangi)
    - Method `updateShift()` untuk calculate shift
    - Method `updateQuotaInfo()` untuk tampilkan info kuota

### 2. Frontend (Blade)

- **resources/views/livewire/gantitgltm.blade.php**
    - Tambah input jam muat (time picker)
    - Tambah display shift dan kuota info
    - Tambah kolom jam muat dan shift di tabel
    - Display real-time quota dan shift

### 3. Database

- **Migration**: `2026_07_03_000001_add_jam_muat_shift_to_log_rubah_tglmuat.php`
    - Tambah kolom `jamMuat` (TIME)
    - Tambah kolom `jamMuat1` (TIME)
    - Tambah kolom `shift` (VARCHAR 50)

## Cara Deploy

### Opsi 1: Menggunakan Laravel Migration (Recommended)

```bash
php artisan migrate
```

### Opsi 2: Manual SQL Script

Jalankan file SQL:

```bash
deployment_scripts/add_shift_quota_to_gantitgltm.sql
```

## Cara Penggunaan

1. **Buka halaman Ganti Tanggal Muat**
2. **Pilih tiket muat** dari tabel dengan klik tombol "Pilih"
3. **Ubah tanggal muat** - sistem akan menampilkan:
    - Kuota harian tersedia
    - Sisa kuota
4. **Pilih jam muat** - sistem akan menampilkan:
    - Shift yang sesuai
    - Peringatan jika di luar jam operasional
5. **Klik SIMPAN**

## Validasi Error Messages

- **"Kuota harian untuk tanggal XX-XX-XXXX belum dibuat"**
  → Buat kuota harian terlebih dahulu di menu Quota Harian

- **"Kuota harian tidak mencukupi. Sisa kuota: XXX Kg"**
  → Pilih tanggal lain atau tingkatkan kuota harian

- **"Jam muat di luar shift operasional (08:00-20:00)"**
  → Pilih jam antara 08:00 - 20:00

## Testing Checklist

- [ ] Pastikan tbl_QuotaHarian sudah ada data
- [ ] Test ubah tanggal dengan kuota mencukupi → Harus berhasil
- [ ] Test ubah tanggal dengan kuota tidak cukup → Harus ditolak
- [ ] Test jam muat di luar shift → Harus ditolak
- [ ] Test jam muat dalam shift → Harus berhasil
- [ ] Verifikasi kuota berkurang di tanggal baru
- [ ] Verifikasi kuota kembali di tanggal lama
- [ ] Verifikasi log tercatat dengan lengkap (jamMuat, shift)

## Dependencies

### Tabel Database yang Dibutuhkan:

- `create_t_m_s` (harus ada kolom: jamMuat)
- `tbl_QuotaHarian` (harus ada data kuota)
- `tbl_log_rubah_tglMuat` (kolom baru: jamMuat, jamMuat1, shift)

### Referensi Sistem Shift

Logic shift mengikuti standar yang digunakan di:

- `app/Livewire/Cardpgi.php`
- `app/Livewire/Cardantrianhariini.php`
- `app/Livewire/Fgdashboard.php`

## Catatan Penting

1. **Kuota Harian harus dibuat terlebih dahulu** di menu `/quotaharian` sebelum mengubah tanggal muat
2. **Jam Muat otomatis set ke 08:00** jika data lama tidak ada jamMuat
3. **Update kuota bersifat transactional** - jika gagal simpan, kuota tidak berubah
4. **Log perubahan** menyimpan: tanggal lama/baru, jam lama/baru, shift, dan user

## Support & Troubleshooting

Jika ada error:

1. Cek apakah migration sudah dijalankan
2. Pastikan tabel tbl_QuotaHarian ada dan terisi
3. Cek error log di `storage/logs/laravel.log`
4. Verifikasi koneksi database 'sqlsrv'

## Rollback

Jika perlu rollback:

```bash
php artisan migrate:rollback --step=1
```

Atau manual SQL:

```sql
ALTER TABLE tbl_log_rubah_tglMuat
DROP COLUMN jamMuat, jamMuat1, shift;
```
