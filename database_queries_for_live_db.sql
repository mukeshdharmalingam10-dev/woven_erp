-- =====================================================
-- Database Queries for Live Database
-- Date: Today's Changes Verification and Cleanup
-- =====================================================

-- =====================================================
-- 1. VERIFY TABLE STRUCTURE
-- =====================================================

-- Check debit_note_items table structure
DESCRIBE debit_note_items;

-- Check credit_note_items table structure
DESCRIBE credit_note_items;

-- Verify product_id column is nullable
SELECT 
    COLUMN_NAME, 
    IS_NULLABLE, 
    DATA_TYPE, 
    COLUMN_TYPE
FROM INFORMATION_SCHEMA.COLUMNS
WHERE TABLE_SCHEMA = DATABASE()
AND TABLE_NAME IN ('debit_note_items', 'credit_note_items')
AND COLUMN_NAME = 'product_id';

-- =====================================================
-- 2. CLEAN UP INVALID DATA
-- =====================================================

-- Check for any empty strings in product_id (should be NULL)
SELECT 
    'debit_note_items' as table_name,
    COUNT(*) as empty_string_count
FROM debit_note_items
WHERE product_id = '' OR product_id = '0'
UNION ALL
SELECT 
    'credit_note_items' as table_name,
    COUNT(*) as empty_string_count
FROM credit_note_items
WHERE product_id = '' OR product_id = '0';

-- Fix empty strings in product_id for debit_note_items
-- (Convert empty strings and '0' to NULL)
UPDATE debit_note_items
SET product_id = NULL
WHERE product_id = '' OR product_id = '0' OR product_id = 0;

-- Fix empty strings in product_id for credit_note_items
-- (Convert empty strings and '0' to NULL)
UPDATE credit_note_items
SET product_id = NULL
WHERE product_id = '' OR product_id = '0' OR product_id = 0;

-- =====================================================
-- 3. VERIFY FOREIGN KEY CONSTRAINTS
-- =====================================================

-- Check foreign key constraints for debit_note_items
SELECT 
    CONSTRAINT_NAME,
    TABLE_NAME,
    COLUMN_NAME,
    REFERENCED_TABLE_NAME,
    REFERENCED_COLUMN_NAME
FROM INFORMATION_SCHEMA.KEY_COLUMN_USAGE
WHERE TABLE_SCHEMA = DATABASE()
AND TABLE_NAME = 'debit_note_items'
AND REFERENCED_TABLE_NAME IS NOT NULL;

-- Check foreign key constraints for credit_note_items
SELECT 
    CONSTRAINT_NAME,
    TABLE_NAME,
    COLUMN_NAME,
    REFERENCED_TABLE_NAME,
    REFERENCED_COLUMN_NAME
FROM INFORMATION_SCHEMA.KEY_COLUMN_USAGE
WHERE TABLE_SCHEMA = DATABASE()
AND TABLE_NAME = 'credit_note_items'
AND REFERENCED_TABLE_NAME IS NOT NULL;

-- =====================================================
-- 4. CHECK FOR ORPHANED RECORDS
-- =====================================================

-- Check for debit_note_items with invalid product_id references
SELECT 
    dni.id,
    dni.debit_note_id,
    dni.product_id,
    dni.item_name
FROM debit_note_items dni
LEFT JOIN products p ON dni.product_id = p.id
WHERE dni.product_id IS NOT NULL
AND p.id IS NULL;

-- Check for credit_note_items with invalid product_id references
SELECT 
    cni.id,
    cni.credit_note_id,
    cni.product_id,
    cni.item_name
FROM credit_note_items cni
LEFT JOIN products p ON cni.product_id = p.id
WHERE cni.product_id IS NOT NULL
AND p.id IS NULL;

-- =====================================================
-- 5. DATA INTEGRITY CHECK
-- =====================================================

-- Check debit_note_items data summary
SELECT 
    COUNT(*) as total_items,
    COUNT(product_id) as items_with_product_id,
    COUNT(*) - COUNT(product_id) as items_without_product_id,
    COUNT(DISTINCT product_id) as unique_products
FROM debit_note_items;

-- Check credit_note_items data summary
SELECT 
    COUNT(*) as total_items,
    COUNT(product_id) as items_with_product_id,
    COUNT(*) - COUNT(product_id) as items_without_product_id,
    COUNT(DISTINCT product_id) as unique_products
FROM credit_note_items;

-- =====================================================
-- 6. VERIFY RECENT CHANGES (Optional - if you have timestamps)
-- =====================================================

-- Check recent debit_note_items (last 24 hours)
SELECT 
    id,
    debit_note_id,
    product_id,
    item_name,
    rate,
    quantity,
    created_at,
    updated_at
FROM debit_note_items
WHERE updated_at >= DATE_SUB(NOW(), INTERVAL 24 HOUR)
ORDER BY updated_at DESC;

-- Check recent credit_note_items (last 24 hours)
SELECT 
    id,
    credit_note_id,
    product_id,
    item_name,
    rate,
    quantity,
    created_at,
    updated_at
FROM credit_note_items
WHERE updated_at >= DATE_SUB(NOW(), INTERVAL 24 HOUR)
ORDER BY updated_at DESC;

-- =====================================================
-- NOTES:
-- =====================================================
-- 1. No schema changes were made today - tables already have
--    product_id as nullable which is correct.
--
-- 2. The main fix was in application code to convert
--    reference item values ("ref_0", "ref_1") to NULL
--    before saving to database.
--
-- 3. Run queries 1-4 to verify and clean up any existing
--    invalid data (empty strings in product_id).
--
-- 4. Query 5 provides a summary of data integrity.
--
-- 5. Query 6 is optional to check recent changes.
-- =====================================================

