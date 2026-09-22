-- EndOut payment/subscription migration
-- Əgər endout.sql artıq yenilənmiş dump-dırsa, bu migration-ı
-- ayrıca icra etmək lazım deyil.

ALTER TABLE order_items
    ADD COLUMN IF NOT EXISTS months TINYINT UNSIGNED NOT NULL DEFAULT 1
    AFTER total;
