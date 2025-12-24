-- =====================================================
-- Database Changes for December 24, 2025 (Today)
-- =====================================================
-- Run these queries in your database to apply all changes
-- =====================================================

-- =====================================================
-- 1. Add tax_type column to suppliers table
-- =====================================================
-- Migration: 2025_12_24_065028_add_tax_type_to_suppliers_table.php
ALTER TABLE `suppliers` 
ADD COLUMN `tax_type` ENUM('Intra-State', 'Inter-State') NULL 
AFTER `gst_number`;

-- =====================================================
-- 2. Add mode_of_order and buyer_order_number to sales_invoices table
--    Add description to sales_invoice_items table
-- =====================================================
-- Migration: 2025_12_24_054311_add_mode_of_order_and_buyer_order_number_to_sales_invoices_and_description_to_sales_invoice_items.php

-- Add mode_of_order column to sales_invoices
ALTER TABLE `sales_invoices` 
ADD COLUMN `mode_of_order` VARCHAR(191) NOT NULL DEFAULT 'IMMEDIATE' 
AFTER `customer_id`;

-- Add buyer_order_number column to sales_invoices
ALTER TABLE `sales_invoices` 
ADD COLUMN `buyer_order_number` VARCHAR(191) NULL 
AFTER `mode_of_order`;

-- Add description column to sales_invoice_items
ALTER TABLE `sales_invoice_items` 
ADD COLUMN `description` TEXT NULL 
AFTER `product_id`;

-- =====================================================
-- Note: The following migrations were found but appear to be empty:
-- - 2025_12_24_045558_rename_petty_cashes_table_to_daily_expenses.php
-- - 2025_12_24_051705_fix_daily_expenses_table_structure.php
-- - 2025_12_24_065723_add_tax_type_to_customers_table.php
-- =====================================================

-- =====================================================
-- Verification Queries (Optional - to check if changes were applied)
-- =====================================================

-- Check if tax_type column exists in suppliers table
-- SELECT COLUMN_NAME, DATA_TYPE, IS_NULLABLE, COLUMN_DEFAULT 
-- FROM INFORMATION_SCHEMA.COLUMNS 
-- WHERE TABLE_SCHEMA = DATABASE() 
-- AND TABLE_NAME = 'suppliers' 
-- AND COLUMN_NAME = 'tax_type';

-- Check if mode_of_order and buyer_order_number exist in sales_invoices table
-- SELECT COLUMN_NAME, DATA_TYPE, IS_NULLABLE, COLUMN_DEFAULT 
-- FROM INFORMATION_SCHEMA.COLUMNS 
-- WHERE TABLE_SCHEMA = DATABASE() 
-- AND TABLE_NAME = 'sales_invoices' 
-- AND COLUMN_NAME IN ('mode_of_order', 'buyer_order_number');

-- Check if description exists in sales_invoice_items table
-- SELECT COLUMN_NAME, DATA_TYPE, IS_NULLABLE 
-- FROM INFORMATION_SCHEMA.COLUMNS 
-- WHERE TABLE_SCHEMA = DATABASE() 
-- AND TABLE_NAME = 'sales_invoice_items' 
-- AND COLUMN_NAME = 'description';

-- =====================================================
-- Database Changes for December 25, 2025 (Tomorrow)
-- =====================================================
-- No migrations found for tomorrow yet.
-- Add any new changes here when they are created.
-- =====================================================

-- =====================================================
-- End of Database Changes
-- =====================================================

