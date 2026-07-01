-- ============================================================
-- POST-DEPLOYMENT VERIFICATION QUERIES
-- File: 03_verification_queries.sql
-- Purpose: Verify deployment success and data integrity
-- ============================================================

-- ⚠️ GANTI [nama_database] dengan nama database production Anda!
USE [nama_database];
GO

PRINT '========================================';
PRINT 'POST-DEPLOYMENT VERIFICATION';
PRINT '========================================';
PRINT '';

-- ============================================================
-- CHECK 1: Verify Schema Changes
-- ============================================================

PRINT '1. SCHEMA VERIFICATION';
PRINT '   Checking if new columns exist...';
PRINT '';

SELECT 
    COLUMN_NAME as [Column],
    DATA_TYPE as [Type],
    NUMERIC_PRECISION as [Precision],
    NUMERIC_SCALE as [Scale],
    IS_NULLABLE as [Nullable]
FROM INFORMATION_SCHEMA.COLUMNS
WHERE TABLE_NAME = 'trscale_headers'
AND COLUMN_NAME IN ('total_range_min', 'total_range_max')
ORDER BY ORDINAL_POSITION;

PRINT '';

-- Count check
DECLARE @ColumnCount INT;
SELECT @ColumnCount = COUNT(*)
FROM INFORMATION_SCHEMA.COLUMNS
WHERE TABLE_NAME = 'trscale_headers'
AND COLUMN_NAME IN ('total_range_min', 'total_range_max');

IF @ColumnCount = 2
BEGIN
    PRINT '   ✓ Both columns exist!';
END
ELSE
BEGIN
    PRINT '   ✗ ERROR: Expected 2 columns, found ' + CAST(@ColumnCount AS VARCHAR(10));
END

PRINT '';
PRINT '';

-- ============================================================
-- CHECK 2: Data Integrity - Old Records
-- ============================================================

PRINT '2. DATA INTEGRITY CHECK - OLD RECORDS';
PRINT '   Checking old records (before deployment)...';
PRINT '';

DECLARE @OldRecordsCount INT, @OldRecordsWithNull INT;

-- Count old records (before deployment date)
SELECT @OldRecordsCount = COUNT(*)
FROM trscale_headers
WHERE created_at < '2026-07-01';  -- Adjust date as needed

-- Count old records with NULL in new columns (expected)
SELECT @OldRecordsWithNull = COUNT(*)
FROM trscale_headers
WHERE created_at < '2026-07-01'
AND total_range_min IS NULL
AND total_range_max IS NULL;

PRINT '   Old records (before 2026-07-01): ' + CAST(@OldRecordsCount AS VARCHAR(20));
PRINT '   Old records with NULL (expected): ' + CAST(@OldRecordsWithNull AS VARCHAR(20));

IF @OldRecordsCount = @OldRecordsWithNull
BEGIN
    PRINT '   ✓ All old records have NULL in new columns (correct!)';
END
ELSE
BEGIN
    PRINT '   ⚠ WARNING: Some old records have values in new columns';
END

-- Sample old records
PRINT '';
PRINT '   Sample old records (top 5):';
SELECT TOP 5
    hdrID,
    netto,
    total_range_min,
    total_range_max,
    created_at
FROM trscale_headers
WHERE created_at < '2026-07-01'
ORDER BY created_at DESC;

PRINT '';
PRINT '';

-- ============================================================
-- CHECK 3: New Transactions After Deployment
-- ============================================================

PRINT '3. NEW TRANSACTIONS CHECK';
PRINT '   Checking new transactions after deployment...';
PRINT '';

DECLARE @NewRecordsCount INT, @NewRecordsWithRange INT;

-- Count new records (after deployment)
SELECT @NewRecordsCount = COUNT(*)
FROM trscale_headers
WHERE created_at >= '2026-07-01';  -- Adjust date as needed

-- Count new records with range values (expected after weighing out)
SELECT @NewRecordsWithRange = COUNT(*)
FROM trscale_headers
WHERE created_at >= '2026-07-01'
AND total_range_min IS NOT NULL
AND total_range_max IS NOT NULL;

PRINT '   New records (after 2026-07-01): ' + CAST(@NewRecordsCount AS VARCHAR(20));
PRINT '   New records with range values: ' + CAST(@NewRecordsWithRange AS VARCHAR(20));

IF @NewRecordsCount > 0
BEGIN
    -- Sample new records
    PRINT '';
    PRINT '   Sample new records with range (top 5):';
    SELECT TOP 5
        hdrID,
        netto,
        total_range_min,
        total_range_max,
        CASE 
            WHEN netto >= total_range_min AND netto <= total_range_max 
            THEN 'In Range' 
            ELSE 'Out of Range' 
        END as [Status],
        created_at
    FROM trscale_headers
    WHERE created_at >= '2026-07-01'
    AND total_range_min IS NOT NULL
    ORDER BY created_at DESC;
END
ELSE
BEGIN
    PRINT '   ℹ No new transactions yet (normal if just deployed)';
END

PRINT '';
PRINT '';

-- ============================================================
-- CHECK 4: Range Validation Logic
-- ============================================================

PRINT '4. RANGE VALIDATION LOGIC CHECK';
PRINT '   Verifying total range calculation...';
PRINT '';

-- Sample transaction with details
IF EXISTS (SELECT 1 FROM trscale_headers WHERE total_range_min IS NOT NULL)
BEGIN
    DECLARE @SampleHdrID INT;
    
    SELECT TOP 1 @SampleHdrID = hdrID
    FROM trscale_headers
    WHERE total_range_min IS NOT NULL
    ORDER BY created_at DESC;
    
    PRINT '   Sample Transaction ID: ' + CAST(@SampleHdrID AS VARCHAR(20));
    PRINT '';
    
    -- Show header
    PRINT '   Header Data:';
    SELECT 
        hdrID,
        netto as [Net Weight],
        total_range_min as [Total Min],
        total_range_max as [Total Max],
        CASE 
            WHEN netto >= total_range_min AND netto <= total_range_max 
            THEN 'In Range' 
            ELSE 'Out of Range' 
        END as [Calculated Status]
    FROM trscale_headers
    WHERE hdrID = @SampleHdrID;
    
    PRINT '';
    PRINT '   Detail Products (if available):';
    
    -- Show details (if details table exists and has data)
    IF EXISTS (SELECT 1 FROM trscale_details WHERE hdrID = @SampleHdrID)
    BEGIN
        SELECT 
            dtlID,
            itemCode,
            theoretical,
            actual,
            rangeMin as [Range Min (per product)],
            rangeMax as [Range Max (per product)]
        FROM trscale_details
        WHERE hdrID = @SampleHdrID;
        
        -- Calculate sum of ranges
        DECLARE @SumRangeMin DECIMAL(10,2), @SumRangeMax DECIMAL(10,2);
        
        SELECT 
            @SumRangeMin = SUM(rangeMin),
            @SumRangeMax = SUM(rangeMax)
        FROM trscale_details
        WHERE hdrID = @SampleHdrID;
        
        PRINT '';
        PRINT '   Calculated Total Range (from details):';
        PRINT '     Sum Range Min: ' + CAST(@SumRangeMin AS VARCHAR(20));
        PRINT '     Sum Range Max: ' + CAST(@SumRangeMax AS VARCHAR(20));
        
        -- Compare with header
        DECLARE @HeaderRangeMin DECIMAL(10,2), @HeaderRangeMax DECIMAL(10,2);
        
        SELECT 
            @HeaderRangeMin = total_range_min,
            @HeaderRangeMax = total_range_max
        FROM trscale_headers
        WHERE hdrID = @SampleHdrID;
        
        IF @SumRangeMin = @HeaderRangeMin AND @SumRangeMax = @HeaderRangeMax
        BEGIN
            PRINT '   ✓ Total range calculation correct!';
        END
        ELSE
        BEGIN
            PRINT '   ⚠ WARNING: Total range mismatch!';
            PRINT '     Expected: ' + CAST(@SumRangeMin AS VARCHAR(20)) + ' - ' + CAST(@SumRangeMax AS VARCHAR(20));
            PRINT '     Actual:   ' + CAST(@HeaderRangeMin AS VARCHAR(20)) + ' - ' + CAST(@HeaderRangeMax AS VARCHAR(20));
        END
    END
    ELSE
    BEGIN
        PRINT '   ℹ No details found for this transaction';
    END
END
ELSE
BEGIN
    PRINT '   ℹ No transactions with range data yet';
    PRINT '   (Normal if no weighing out has been done after deployment)';
END

PRINT '';
PRINT '';

-- ============================================================
-- CHECK 5: Approval Status Distribution
-- ============================================================

PRINT '5. APPROVAL STATUS DISTRIBUTION';
PRINT '   Analyzing approval patterns...';
PRINT '';

SELECT 
    CASE 
        WHEN total_range_min IS NULL THEN 'Not Weighed Out Yet'
        WHEN netto >= total_range_min AND netto <= total_range_max THEN 'In Range (No Approval Needed)'
        ELSE 'Out of Range (Needs Approval)'
    END as [Status Category],
    COUNT(*) as [Count]
FROM trscale_headers
WHERE created_at >= '2026-07-01'
GROUP BY 
    CASE 
        WHEN total_range_min IS NULL THEN 'Not Weighed Out Yet'
        WHEN netto >= total_range_min AND netto <= total_range_max THEN 'In Range (No Approval Needed)'
        ELSE 'Out of Range (Needs Approval)'
    END
ORDER BY [Count] DESC;

PRINT '';

-- ============================================================
-- CHECK 6: Performance Check
-- ============================================================

PRINT '6. PERFORMANCE CHECK';
PRINT '   Checking query performance...';
PRINT '';

DECLARE @StartTime DATETIME, @EndTime DATETIME, @Duration INT;

SET @StartTime = GETDATE();

-- Sample query that will be used frequently
SELECT TOP 100
    hdrID,
    netto,
    total_range_min,
    total_range_max,
    CASE 
        WHEN netto >= total_range_min AND netto <= total_range_max 
        THEN 'In Range' 
        ELSE 'Out of Range' 
    END as status
FROM trscale_headers
WHERE total_range_min IS NOT NULL
ORDER BY created_at DESC;

SET @EndTime = GETDATE();
SET @Duration = DATEDIFF(MILLISECOND, @StartTime, @EndTime);

PRINT '   Query execution time: ' + CAST(@Duration AS VARCHAR(20)) + ' ms';

IF @Duration < 100
BEGIN
    PRINT '   ✓ Performance excellent (< 100ms)';
END
ELSE IF @Duration < 500
BEGIN
    PRINT '   ✓ Performance good (< 500ms)';
END
ELSE IF @Duration < 1000
BEGIN
    PRINT '   ⚠ Performance acceptable (< 1s)';
END
ELSE
BEGIN
    PRINT '   ⚠ Performance slow (> 1s) - consider adding index';
END

PRINT '';

-- ============================================================
-- SUMMARY
-- ============================================================

PRINT '';
PRINT '========================================';
PRINT 'VERIFICATION SUMMARY';
PRINT '========================================';
PRINT '';

-- Generate summary report
DECLARE @TotalRecords INT, @OldRecords INT, @NewRecords INT, @InRange INT, @OutOfRange INT;

SELECT @TotalRecords = COUNT(*) FROM trscale_headers;
SELECT @OldRecords = COUNT(*) FROM trscale_headers WHERE created_at < '2026-07-01';
SELECT @NewRecords = COUNT(*) FROM trscale_headers WHERE created_at >= '2026-07-01';
SELECT @InRange = COUNT(*) FROM trscale_headers 
WHERE total_range_min IS NOT NULL 
AND netto >= total_range_min 
AND netto <= total_range_max;
SELECT @OutOfRange = COUNT(*) FROM trscale_headers 
WHERE total_range_min IS NOT NULL 
AND (netto < total_range_min OR netto > total_range_max);

PRINT 'Database Statistics:';
PRINT '  Total records         : ' + CAST(@TotalRecords AS VARCHAR(20));
PRINT '  Old records (< today) : ' + CAST(@OldRecords AS VARCHAR(20));
PRINT '  New records (>= today): ' + CAST(@NewRecords AS VARCHAR(20));
PRINT '';
PRINT 'Range Validation:';
PRINT '  In Range              : ' + CAST(@InRange AS VARCHAR(20));
PRINT '  Out of Range          : ' + CAST(@OutOfRange AS VARCHAR(20));
PRINT '';

-- Final checklist
PRINT '✓ FINAL CHECKLIST:';
PRINT '  [ ] Schema changes applied successfully';
PRINT '  [ ] Old data integrity maintained (NULL in new columns)';
PRINT '  [ ] New transactions have range data populated';
PRINT '  [ ] Range calculation logic working correctly';
PRINT '  [ ] Performance acceptable';
PRINT '';

PRINT '========================================';
PRINT '✅ VERIFICATION COMPLETED!';
PRINT '========================================';
PRINT '';
PRINT 'Verification Time: ' + CONVERT(VARCHAR(30), GETDATE(), 120);
PRINT '';

GO
