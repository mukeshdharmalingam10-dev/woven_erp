-- =====================================================
-- TODAY'S DATA CLEANUP QUERIES
-- Date: Today
-- =====================================================
-- IMPORTANT: No table structure changes were made today.
-- All changes were in application code (controllers & views).
-- These queries are to clean up any existing invalid data.
-- =====================================================

-- =====================================================
-- 1. CLEAN UP INVALID product_id VALUES
-- =====================================================
-- Fix empty strings, '0', or 0 values in product_id
-- These should be NULL for reference document items

-- For debit_note_items table
UPDATE debit_note_items
SET product_id = NULL
WHERE product_id = '' 
   OR product_id = '0' 
   OR product_id = 0
   OR CAST(product_id AS CHAR) LIKE 'ref_%';

-- For credit_note_items table
UPDATE credit_note_items
SET product_id = NULL
WHERE product_id = '' 
   OR product_id = '0' 
   OR product_id = 0
   OR CAST(product_id AS CHAR) LIKE 'ref_%';

-- =====================================================
-- 2. VERIFY THE CLEANUP
-- =====================================================

-- Check debit_note_items - should show 0 invalid records
SELECT 
    'debit_note_items' as table_name,
    COUNT(*) as invalid_product_id_count
FROM debit_note_items
WHERE product_id = '' 
   OR product_id = '0' 
   OR product_id = 0
   OR CAST(product_id AS CHAR) LIKE 'ref_%';

-- Check credit_note_items - should show 0 invalid records
SELECT 
    'credit_note_items' as table_name,
    COUNT(*) as invalid_product_id_count
FROM credit_note_items
WHERE product_id = '' 
   OR product_id = '0' 
   OR product_id = 0
   OR CAST(product_id AS CHAR) LIKE 'ref_%';

-- =====================================================
-- 3. VERIFY TABLE STRUCTURE (Should already be correct)
-- =====================================================

-- Verify product_id is nullable in debit_note_items
SELECT 
    'debit_note_items' as table_name,
    COLUMN_NAME,
    IS_NULLABLE,
    DATA_TYPE,
    COLUMN_TYPE
FROM INFORMATION_SCHEMA.COLUMNS
WHERE TABLE_SCHEMA = DATABASE()
AND TABLE_NAME = 'debit_note_items'
AND COLUMN_NAME = 'product_id';

-- Verify product_id is nullable in credit_note_items
SELECT 
    'credit_note_items' as table_name,
    COLUMN_NAME,
    IS_NULLABLE,
    DATA_TYPE,
    COLUMN_TYPE
FROM INFORMATION_SCHEMA.COLUMNS
WHERE TABLE_SCHEMA = DATABASE()
AND TABLE_NAME = 'credit_note_items'
AND COLUMN_NAME = 'product_id';

-- =====================================================
-- 4. DATA SUMMARY (After cleanup)
-- =====================================================

-- Summary for debit_note_items
SELECT 
    'debit_note_items' as table_name,
    COUNT(*) as total_items,
    COUNT(product_id) as items_with_product_id,
    COUNT(*) - COUNT(product_id) as items_without_product_id,
    COUNT(DISTINCT product_id) as unique_products
FROM debit_note_items;

-- Summary for credit_note_items
SELECT 
    'credit_note_items' as table_name,
    COUNT(*) as total_items,
    COUNT(product_id) as items_with_product_id,
    COUNT(*) - COUNT(product_id) as items_without_product_id,
    COUNT(DISTINCT product_id) as unique_products
FROM credit_note_items;

-- =====================================================
-- NOTES:
-- =====================================================
-- 1. No ALTER TABLE statements needed - tables already
--    have product_id as nullable.
--
-- 2. The UPDATE queries will fix any existing invalid
--    data (empty strings, '0', or 'ref_*' values).
--
-- 3. Run queries in order: 1 (cleanup), 2 (verify),
--    3 (structure check), 4 (summary).
--
-- 4. These queries are safe to run multiple times.
-- =====================================================

