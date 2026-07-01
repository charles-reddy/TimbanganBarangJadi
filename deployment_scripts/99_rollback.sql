-- ============================================================
-- ROLLBACK DATABASE CHANGES
-- File: 99_rollback.sql
-- Purpose: Rollback database schema changes if needed
-- ⚠️ WARNING: This will remove total_range_min and total_range_max columns!
-- ⚠️ WARNING: Any data in these columns will be LOST!
-- ============================================================

-- ⚠️⚠️⚠️ READ THIS BEFORE EXECUTING! ⚠️⚠️⚠️
-- 
-- This script will:
-- 1. Backup current state (with new columns)
-- 2. Remove total_range_min and total_range_max columns
-- 3. Verify rollback completed
--
-- ONLY execute this if:
-- - Deployment caused critical issues
-- - Application rollback is not enough
-- - You have approval from team lead
--
-- BACKUP IS MANDATORY before rollback!
-- ============================================================

-- ⚠️ GANTI [nama_database] dengan nama database production Anda!
USE [nama_database];
GO

PRINT '========================================';
PRINT '⚠️  DATABASE ROLLBACK PROCEDURE';
PRINT '========================================';
PRINT '';
PRINT '⚠️  WARNING: This will remove columns and data!';
PRINT '';
PRINT 'Press Ctrl+C now to cancel if you are not sure!';
PRINT 'Waiting 5 seconds before proceeding...';
PRINT '';

-- Wait 5 seconds (simulate, actual wait depends on execution method)
WAITFOR DELAY '00:00:05';

PRINT 'Proceeding with rollback...';
PRINT '';

-- ============================================================
-- STEP 1: Backup Current State (MANDATORY!)
-- ============================================================

PRINT '========================================';
PRINT 'STEP 1: BACKING UP CURRENT STATE';
PRINT '========================================';
PRINT '';

DECLARE @BackupTableName NVARCHAR(255);
SET @BackupTableName = 'trscale_headers_before_rollback_' + FORMAT(GETDATE(), 'yyyyMMdd_HHmmss');

PRINT 'Creating backup table: ' + @BackupTableName;

DECLARE @BackupSQL NVARCHAR(MAX);
SET @BackupSQL = N'SELECT * INTO ' + @BackupTableName + N' FROM trscale_headers;';

EXEC sp_executesql @BackupSQL;

PRINT '✓ Backup created: ' + @BackupTableName;
PRINT '';

-- Verify backup
DECLARE @BackupCount INT;
SET @BackupCount = (SELECT COUNT(*) FROM trscale_headers);

PRINT 'Backup contains ' + CAST(@BackupCount AS VARCHAR(20)) + ' rows';
PRINT '';

-- Show sample of data that will be lost
PRINT 'Sample of data that will be LOST (top 10 with range data):';
SELECT TOP 10
    hdrID,
    netto,
    total_range_min,
    total_range_max,
    created_at
FROM trscale_headers
WHERE total_range_min IS NOT NULL
ORDER BY created_at DESC;

PRINT '';
PRINT '';

-- ============================================================
-- STEP 2: Remove Columns
-- ============================================================

PRINT '========================================';
PRINT 'STEP 2: REMOVING COLUMNS';
PRINT '========================================';
PRINT '';

-- Check if columns exist before dropping
IF EXISTS (
    SELECT * 
    FROM INFORMATION_SCHEMA.COLUMNS 
    WHERE TABLE_NAME = 'trscale_headers' 
    AND COLUMN_NAME = 'total_range_min'
)
BEGIN
    PRINT 'Dropping column: total_range_min...';
    ALTER TABLE trscale_headers DROP COLUMN total_range_min;
    PRINT '✓ Column total_range_min dropped';
END
ELSE
BEGIN
    PRINT '⚠ Column total_range_min not found (already removed?)';
END

PRINT '';

IF EXISTS (
    SELECT * 
    FROM INFORMATION_SCHEMA.COLUMNS 
    WHERE TABLE_NAME = 'trscale_headers' 
    AND COLUMN_NAME = 'total_range_max'
)
BEGIN
    PRINT 'Dropping column: total_range_max...';
    ALTER TABLE trscale_headers DROP COLUMN total_range_max;
    PRINT '✓ Column total_range_max dropped';
END
ELSE
BEGIN
    PRINT '⚠ Column total_range_max not found (already removed?)';
END

PRINT '';
PRINT '';

-- ============================================================
-- STEP 3: Verification
-- ============================================================

PRINT '========================================';
PRINT 'STEP 3: VERIFICATION';
PRINT '========================================';
PRINT '';

-- Check if columns are really gone
DECLARE @RemainingColumns INT;
SELECT @RemainingColumns = COUNT(*)
FROM INFORMATION_SCHEMA.COLUMNS
WHERE TABLE_NAME = 'trscale_headers'
AND COLUMN_NAME IN ('total_range_min', 'total_range_max');

IF @RemainingColumns = 0
BEGIN
    PRINT '✓ Columns successfully removed';
END
ELSE
BEGIN
    PRINT '✗ ERROR: ' + CAST(@RemainingColumns AS VARCHAR(10)) + ' column(s) still exist!';
    
    -- Show remaining columns
    SELECT COLUMN_NAME
    FROM INFORMATION_SCHEMA.COLUMNS
    WHERE TABLE_NAME = 'trscale_headers'
    AND COLUMN_NAME IN ('total_range_min', 'total_range_max');
END

PRINT '';

-- Verify row count unchanged
DECLARE @CurrentCount INT;
SELECT @CurrentCount = COUNT(*) FROM trscale_headers;

PRINT 'Row count verification:';
PRINT '  Before rollback: ' + CAST(@BackupCount AS VARCHAR(20));
PRINT '  After rollback : ' + CAST(@CurrentCount AS VARCHAR(20));

IF @BackupCount = @CurrentCount
BEGIN
    PRINT '  ✓ Row count unchanged (no data loss in other columns)';
END
ELSE
BEGIN
    PRINT '  ✗ WARNING: Row count mismatch!';
END

PRINT '';

-- Show current schema
PRINT 'Current schema of trscale_headers (first 10 columns):';
SELECT TOP 10
    COLUMN_NAME as [Column],
    DATA_TYPE as [Type],
    IS_NULLABLE as [Nullable]
FROM INFORMATION_SCHEMA.COLUMNS
WHERE TABLE_NAME = 'trscale_headers'
ORDER BY ORDINAL_POSITION;

PRINT '';
PRINT '';

-- ============================================================
-- STEP 4: Summary
-- ============================================================

PRINT '========================================';
PRINT 'ROLLBACK SUMMARY';
PRINT '========================================';
PRINT '';

PRINT 'Rollback completed at: ' + CONVERT(VARCHAR(30), GETDATE(), 120);
PRINT '';
PRINT 'Backup table created: ' + @BackupTableName;
PRINT '  (Contains data WITH total_range columns)';
PRINT '  (Keep this table until you are sure rollback is permanent)';
PRINT '';
PRINT 'Columns removed:';
PRINT '  - total_range_min';
PRINT '  - total_range_max';
PRINT '';
PRINT 'Next steps:';
PRINT '  1. Rollback application files (use PowerShell script)';
PRINT '  2. Clear Laravel cache: php artisan optimize:clear';
PRINT '  3. Test application functionality';
PRINT '  4. Monitor logs for errors';
PRINT '  5. If stable, can drop backup table after 7 days';
PRINT '';

-- Generate drop backup command (for future reference)
PRINT 'To drop backup table later (after confirming stable):';
PRINT '  DROP TABLE ' + @BackupTableName + ';';
PRINT '';

PRINT '========================================';
PRINT '✅ ROLLBACK COMPLETED!';
PRINT '========================================';
PRINT '';

-- ============================================================
-- OPTIONAL: Restore from Backup (EXTREME CASE)
-- ============================================================

PRINT '';
PRINT '========================================';
PRINT 'FULL RESTORE FROM BACKUP (IF NEEDED)';
PRINT '========================================';
PRINT '';
PRINT 'If you need to restore ENTIRE table from backup:';
PRINT '';
PRINT '-- ⚠️⚠️⚠️ DANGER ZONE ⚠️⚠️⚠️';
PRINT '-- This will DELETE ALL current data!';
PRINT '-- Only use if database is completely corrupted!';
PRINT '';
PRINT '-- DROP TABLE trscale_headers;';
PRINT '-- SELECT * INTO trscale_headers FROM trscale_headers_backup_20260701;';
PRINT '';
PRINT '⚠️  DO NOT execute above unless absolutely necessary!';
PRINT '';

GO

-- ============================================================
-- RESTORE VERIFICATION QUERY
-- ============================================================

-- Use this query to verify rollback was successful
PRINT '';
PRINT 'Run this query to verify rollback:';
PRINT '';
PRINT 'SELECT COLUMN_NAME, DATA_TYPE, IS_NULLABLE';
PRINT 'FROM INFORMATION_SCHEMA.COLUMNS';
PRINT 'WHERE TABLE_NAME = ''trscale_headers''';
PRINT 'ORDER BY ORDINAL_POSITION;';
PRINT '';
PRINT '-- Should NOT show total_range_min or total_range_max';
PRINT '';

GO
