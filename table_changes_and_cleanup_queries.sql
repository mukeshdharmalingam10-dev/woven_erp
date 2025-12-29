-- =====================================================
-- TABLE CHANGES AND DATA CLEANUP QUERIES
-- Date: Today
-- Tables: Suppliers, Debit Notes, Credit Notes, Daily Expenses
-- =====================================================
-- IMPORTANT: Review all queries before executing on live database
-- =====================================================

-- =====================================================
-- 1. DEBIT NOTE ITEMS - DATA CLEANUP
-- =====================================================
-- Fix invalid product_id values (empty strings, '0', 0, or 'ref_*' strings)
-- These should be NULL for reference document items

UPDATE debit_note_items
SET product_id = NULL
WHERE product_id = '' 
   OR product_id = '0' 
   OR product_id = 0
   OR CAST(product_id AS CHAR) LIKE 'ref_%';

-- =====================================================
-- 2. CREDIT NOTE ITEMS - DATA CLEANUP
-- =====================================================
-- Fix invalid product_id values (empty strings, '0', 0, or 'ref_*' strings)
-- These should be NULL for reference document items

UPDATE credit_note_items
SET product_id = NULL
WHERE product_id = '' 
   OR product_id = '0' 
   OR product_id = 0
   OR CAST(product_id AS CHAR) LIKE 'ref_%';

-- =====================================================
-- 3. VERIFY COLUMN STRUCTURE - DEBIT NOTE ITEMS
-- =====================================================
-- Ensure product_id is nullable (should already be correct from migration)

-- Check current structure
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

-- If product_id is NOT nullable, run this ALTER statement:
-- ALTER TABLE debit_note_items MODIFY COLUMN product_id BIGINT UNSIGNED NULL;

-- =====================================================
-- 4. VERIFY COLUMN STRUCTURE - CREDIT NOTE ITEMS
-- =====================================================
-- Ensure product_id is nullable (should already be correct from migration)

-- Check current structure
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

-- If product_id is NOT nullable, run this ALTER statement:
-- ALTER TABLE credit_note_items MODIFY COLUMN product_id BIGINT UNSIGNED NULL;

-- =====================================================
-- 5. VERIFY DAILY EXPENSES TABLE STRUCTURE
-- =====================================================
-- Check if table is named 'daily_expenses' or 'petty_cashes'

SELECT 
    TABLE_NAME,
    TABLE_TYPE
FROM INFORMATION_SCHEMA.TABLES
WHERE TABLE_SCHEMA = DATABASE()
AND (TABLE_NAME = 'daily_expenses' OR TABLE_NAME = 'petty_cashes');

-- If table is still named 'petty_cashes', rename it:
-- RENAME TABLE petty_cashes TO daily_expenses;

-- Verify receipt_path column exists and is nullable
SELECT 
    'daily_expenses' as table_name,
    COLUMN_NAME,
    IS_NULLABLE,
    DATA_TYPE,
    COLUMN_TYPE
FROM INFORMATION_SCHEMA.COLUMNS
WHERE TABLE_SCHEMA = DATABASE()
AND TABLE_NAME = 'daily_expenses'
AND COLUMN_NAME = 'receipt_path';

-- If receipt_path doesn't exist, add it:
-- ALTER TABLE daily_expenses ADD COLUMN receipt_path VARCHAR(255) NULL AFTER paid_to;

-- =====================================================
-- 6. VERIFY SUPPLIERS TABLE STRUCTURE
-- =====================================================
-- Check suppliers table structure (no changes expected today)

SELECT 
    'suppliers' as table_name,
    COLUMN_NAME,
    IS_NULLABLE,
    DATA_TYPE,
    COLUMN_TYPE
FROM INFORMATION_SCHEMA.COLUMNS
WHERE TABLE_SCHEMA = DATABASE()
AND TABLE_NAME = 'suppliers'
ORDER BY ORDINAL_POSITION;

-- =====================================================
-- 7. DATA VERIFICATION QUERIES
-- =====================================================

-- Check for any remaining invalid product_id values in debit_note_items
SELECT 
    'debit_note_items' as table_name,
    COUNT(*) as invalid_product_id_count
FROM debit_note_items
WHERE product_id = '' 
   OR product_id = '0' 
   OR product_id = 0
   OR CAST(product_id AS CHAR) LIKE 'ref_%';

-- Check for any remaining invalid product_id values in credit_note_items
SELECT 
    'credit_note_items' as table_name,
    COUNT(*) as invalid_product_id_count
FROM credit_note_items
WHERE product_id = '' 
   OR product_id = '0' 
   OR product_id = 0
   OR CAST(product_id AS CHAR) LIKE 'ref_%';

-- =====================================================
-- 8. DATA SUMMARY QUERIES
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

-- Summary for daily_expenses
SELECT 
    'daily_expenses' as table_name,
    COUNT(*) as total_records,
    COUNT(receipt_path) as records_with_receipt,
    COUNT(*) - COUNT(receipt_path) as records_without_receipt
FROM daily_expenses;

-- Summary for suppliers
SELECT 
    'suppliers' as table_name,
    COUNT(*) as total_suppliers,
    COUNT(CASE WHEN is_active = 1 THEN 1 END) as active_suppliers,
    COUNT(CASE WHEN is_active = 0 THEN 1 END) as inactive_suppliers
FROM suppliers;

-- =====================================================
-- 9. FOREIGN KEY VERIFICATION
-- =====================================================

-- Verify foreign key constraints exist
SELECT 
    TABLE_NAME,
    CONSTRAINT_NAME,
    COLUMN_NAME,
    REFERENCED_TABLE_NAME,
    REFERENCED_COLUMN_NAME
FROM INFORMATION_SCHEMA.KEY_COLUMN_USAGE
WHERE TABLE_SCHEMA = DATABASE()
AND TABLE_NAME IN ('debit_note_items', 'credit_note_items', 'daily_expenses', 'suppliers')
AND REFERENCED_TABLE_NAME IS NOT NULL
ORDER BY TABLE_NAME, CONSTRAINT_NAME;

-- =====================================================
-- NOTES:
-- =====================================================
-- 1. The UPDATE queries (sections 1 and 2) clean up invalid data
--    and are safe to run multiple times.
--
-- 2. The ALTER TABLE statements (sections 3, 4, 5) are commented out
--    and should only be run if the verification queries show they are needed.
--
-- 3. Run queries in this order:
--    a. Verification queries (sections 3-6) to check current state
--    b. Cleanup queries (sections 1-2) to fix data
--    c. Verification queries again (section 7) to confirm cleanup
--    d. Summary queries (section 8) to see final state
--
-- 4. Always backup your database before running ALTER TABLE statements.
--
-- 5. No structural changes were made to the suppliers table today.
--    Only data cleanup was needed for debit/credit note items.
-- =====================================================

