-- Alternatif manual SQL untuk menambahkan kolom pada tbl_log_rubah_tglMuat
-- Jika tidak menggunakan migration Laravel, jalankan script ini di SQL Server

USE [NamaDatabase]; -- Ganti dengan nama database Anda
GO

-- Tambah kolom jamMuat, jamMuat1, dan shift
ALTER TABLE tbl_log_rubah_tglMuat
ADD jamMuat TIME NULL,
    jamMuat1 TIME NULL,
    shift VARCHAR(50) NULL;
GO

-- Verifikasi kolom berhasil ditambahkan
SELECT TOP 1 * FROM tbl_log_rubah_tglMuat;
GO
