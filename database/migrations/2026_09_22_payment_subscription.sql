-- EndOut payment/subscription migration
-- Bir dəfə icra edin.

ALTER TABLE order_items
    ADD COLUMN months TINYINT UNSIGNED NOT NULL DEFAULT 1
    AFTER total;

ALTER TABLE payments
    ADD KEY idx_provider_payment_id (provider_payment_id);
