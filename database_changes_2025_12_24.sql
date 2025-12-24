-- =====================================================
-- Database Changes - December 24, 2025
-- =====================================================
-- Description: Add purchase_order_id column to material_inwards table
-- =====================================================

-- Add purchase_order_id column to material_inwards table
-- This column links material inwards to purchase orders
ALTER TABLE `material_inwards` 
ADD COLUMN `purchase_order_id` BIGINT UNSIGNED NULL 
AFTER `supplier_id`;

-- Add foreign key constraint
ALTER TABLE `material_inwards` 
ADD CONSTRAINT `material_inwards_purchase_order_id_foreign` 
FOREIGN KEY (`purchase_order_id`) 
REFERENCES `purchase_orders` (`id`) 
ON DELETE SET NULL;

-- =====================================================
-- Rollback Query (if needed)
-- =====================================================
-- To remove the column and foreign key:
-- ALTER TABLE `material_inwards` 
-- DROP FOREIGN KEY `material_inwards_purchase_order_id_foreign`;
-- 
-- ALTER TABLE `material_inwards` 
-- DROP COLUMN `purchase_order_id`;
-- =====================================================

