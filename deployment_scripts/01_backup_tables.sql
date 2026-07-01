-- ============================================================
-- BACKUP DATABASE TABLES
-- File: 01_backup_tables.sql
-- Purpose: Backup semua tabel yang akan terpengaruh deployment
-- ============================================================

-- ⚠️ GANTI [nama_database] dengan nama database production Anda!
USE [nama_database];
GO

PRINT '========================================';
PRINT 'STARTING DATABASE BACKUP...';
PRINT '========================================';
PRINT '';

-- 1. Backup tabel trscale_headers (tabel utama yang akan berubah)
PRINT '1. Backing up trscale_headers...';
SELECT * 
INTO trscale_headers_backup_20260701
FROM trscale_headers;
PRINT '   ✓ trscale_headers backed up';
PRINT '';

-- 2. Backup tabel trscale_details (untuk safety)
PRINT '2. Backing up trscale_details...';
SELECT * 
INTO trscale_details_backup_20260701
FROM trscale_details;
PRINT '   ✓ trscale_details backed up';
PRINT '';

-- 3. Backup tabel trscale_approvals (untuk safety)
PRINT '3. Backing up trscale_approvals...';
SELECT * 
INTO trscale_approvals_backup_20260701
FROM trscale_approvals;
PRINT '   ✓ trscale_approvals backed up';
PRINT '';

-- ============================================================
-- VERIFICATION
-- ============================================================

PRINT '========================================';
PRINT 'VERIFYING BACKUPS...';
PRINT '========================================';
PRINT '';

DECLARE @HeadersCount INT, @DetailsCount INT, @ApprovalsCount INT;

SELECT @HeadersCount = COUNT(*) FROM trscale_headers_backup_20260701;
SELECT @DetailsCount = COUNT(*) FROM trscale_details_backup_20260701;
SELECT @ApprovalsCount = COUNT(*) FROM trscale_approvals_backup_20260701;

PRINT 'Backup Results:';
PRINT '  - Headers   : ' + CAST(@HeadersCount AS VARCHAR(20)) + ' rows';
PRINT '  - Details   : ' + CAST(@DetailsCount AS VARCHAR(20)) + ' rows';
PRINT '  - Approvals : ' + CAST(@ApprovalsCount AS VARCHAR(20)) + ' rows';
PRINT '';

-- Detail verification query
SELECT 
    'trscale_headers' as TableName,
    COUNT(*) as BackupRowCount,
    MIN(created_at) as OldestRecord,
    MAX(created_at) as NewestRecord
FROM trscale_headers_backup_20260701
UNION ALL
SELECT 
    'trscale_details' as TableName,
    COUNT(*) as BackupRowCount,
    MIN(created_at) as OldestRecord,
    MAX(created_at) as NewestRecord
FROM trscale_details_backup_20260701
UNION ALL
SELECT 
    'trscale_approvals' as TableName,
    COUNT(*) as BackupRowCount,
    MIN(created_at) as OldestRecord,
    MAX(created_at) as NewestRecord
FROM trscale_approvals_backup_20260701;

PRINT '';
PRINT '========================================';
PRINT '✅ BACKUP COMPLETED SUCCESSFULLY!';
PRINT '========================================';
PRINT '';
PRINT '📋 CATAT HASIL INI:';
PRINT '   Headers backup  : ___________ rows';
PRINT '   Details backup  : ___________ rows';
PRINT '   Approvals backup: ___________ rows';
PRINT '   Backup time     : ' + CONVERT(VARCHAR(30), GETDATE(), 120);
PRINT '';

GO
