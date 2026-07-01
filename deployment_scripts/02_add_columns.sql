-- ============================================================
-- ADD NEW COLUMNS TO trscale_headers
-- File: 02_add_columns.sql
-- Purpose: Menambahkan kolom total_range_min dan total_range_max
-- ============================================================

-- ⚠️ GANTI [nama_database] dengan nama database production Anda!
USE [nama_database];
GO

PRINT '========================================';
PRINT 'ADDING NEW COLUMNS TO trscale_headers';
PRINT '========================================';
PRINT '';

-- ============================================================
-- STEP 1: Add total_range_min column
-- ============================================================

PRINT '1. Checking if total_range_min exists...';

IF NOT EXISTS (
    SELECT * 
    FROM INFORMATION_SCHEMA.COLUMNS 
    WHERE TABLE_NAME = 'trscale_headers' 
    AND COLUMN_NAME = 'total_range_min'
)
BEGIN
    PRINT '   Column does not exist. Adding...';
    
    ALTER TABLE trscale_headers 
    ADD total_range_min DECIMAL(10,2) NULL;
    
    PRINT '   ✓ Column total_range_min successfully added!';
END
ELSE
BEGIN
    PRINT '   ⚠ Column total_range_min already exists. Skipping...';
END

PRINT '';

-- ============================================================
-- STEP 2: Add total_range_max column
-- ============================================================

PRINT '2. Checking if total_range_max exists...';

IF NOT EXISTS (
    SELECT * 
    FROM INFORMATION_SCHEMA.COLUMNS 
    WHERE TABLE_NAME = 'trscale_headers' 
    AND COLUMN_NAME = 'total_range_max'
)
BEGIN
    PRINT '   Column does not exist. Adding...';
    
    ALTER TABLE trscale_headers 
    ADD total_range_max DECIMAL(10,2) NULL;
    
    PRINT '   ✓ Column total_range_max successfully added!';
END
ELSE
BEGIN
    PRINT '   ⚠ Column total_range_max already exists. Skipping...';
END

PRINT '';

-- ============================================================
-- STEP 3: VERIFICATION
-- ============================================================

PRINT '========================================';
PRINT 'VERIFYING NEW COLUMNS...';
PRINT '========================================';
PRINT '';

SELECT 
    COLUMN_NAME as [Column Name],
    DATA_TYPE as [Data Type],
    CHARACTER_MAXIMUM_LENGTH as [Max Length],
    NUMERIC_PRECISION as [Precision],
    NUMERIC_SCALE as [Scale],
    IS_NULLABLE as [Nullable],
    COLUMN_DEFAULT as [Default Value]
FROM INFORMATION_SCHEMA.COLUMNS
WHERE TABLE_NAME = 'trscale_headers'
AND COLUMN_NAME IN ('total_range_min', 'total_range_max')
ORDER BY ORDINAL_POSITION;

PRINT '';
PRINT 'Expected Result:';
PRINT '┌─────────────────┬───────────┬────────────┬───────────┬───────┬──────────┬─────────┐';
PRINT '│ Column Name     │ Data Type │ Max Length │ Precision │ Scale │ Nullable │ Default │';
PRINT '├─────────────────┼───────────┼────────────┼───────────┼───────┼──────────┼─────────┤';
PRINT '│ total_range_min │ decimal   │ NULL       │ 10        │ 2     │ YES      │ NULL    │';
PRINT '│ total_range_max │ decimal   │ NULL       │ 10        │ 2     │ YES      │ NULL    │';
PRINT '└─────────────────┴───────────┴────────────┴───────────┴───────┴──────────┴─────────┘';
PRINT '';

-- Check row count before and after (should be same)
DECLARE @RowCount INT;
SELECT @RowCount = COUNT(*) FROM trscale_headers;

PRINT 'Table Statistics:';
PRINT '  Total rows in trscale_headers: ' + CAST(@RowCount AS VARCHAR(20));
PRINT '';

-- Sample data check (old data should have NULL in new columns)
PRINT 'Sample Data Check (latest 5 records):';
SELECT TOP 5
    hdrID,
    netto,
    total_range_min,
    total_range_max,
    created_at
FROM trscale_headers
ORDER BY created_at DESC;

PRINT '';
PRINT '========================================';
PRINT '✅ SCHEMA UPDATE COMPLETED!';
PRINT '========================================';
PRINT '';
PRINT '✓ Checklist:';
PRINT '  [ ] Column total_range_min exists with type DECIMAL(10,2)';
PRINT '  [ ] Column total_range_max exists with type DECIMAL(10,2)';
PRINT '  [ ] Both columns are NULLABLE';
PRINT '  [ ] Row count unchanged (no data loss)';
PRINT '  [ ] Old records have NULL in new columns';
PRINT '';

GO
