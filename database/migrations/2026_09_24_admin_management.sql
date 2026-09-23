-- EndOut admin management foundation
CREATE TABLE IF NOT EXISTS admin_audit_logs (
  id BIGINT UNSIGNED NOT NULL AUTO_INCREMENT,
  admin_id INT UNSIGNED NOT NULL,
  action VARCHAR(80) NOT NULL,
  entity_type VARCHAR(80) DEFAULT NULL,
  entity_id BIGINT UNSIGNED DEFAULT NULL,
  old_values LONGTEXT DEFAULT NULL,
  new_values LONGTEXT DEFAULT NULL,
  ip_address VARCHAR(45) DEFAULT NULL,
  user_agent VARCHAR(255) DEFAULT NULL,
  created_at DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (id),
  KEY idx_admin_audit_admin_created (admin_id, created_at),
  KEY idx_admin_audit_entity (entity_type, entity_id),
  KEY idx_admin_audit_action (action)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

CREATE TABLE IF NOT EXISTS admin_login_attempts (
  id BIGINT UNSIGNED NOT NULL AUTO_INCREMENT,
  email VARCHAR(255) NOT NULL,
  ip_address VARCHAR(45) DEFAULT NULL,
  success TINYINT(1) NOT NULL DEFAULT 0,
  created_at DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (id),
  KEY idx_admin_login_email_created (email, created_at),
  KEY idx_admin_login_ip_created (ip_address, created_at)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

CREATE TABLE IF NOT EXISTS admin_sessions (
  id BIGINT UNSIGNED NOT NULL AUTO_INCREMENT,
  admin_id INT UNSIGNED NOT NULL,
  session_id VARCHAR(128) NOT NULL,
  ip_address VARCHAR(45) DEFAULT NULL,
  user_agent VARCHAR(255) DEFAULT NULL,
  last_seen_at DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,
  created_at DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,
  revoked_at DATETIME DEFAULT NULL,
  PRIMARY KEY (id),
  UNIQUE KEY uq_admin_session (session_id),
  KEY idx_admin_sessions_admin (admin_id, revoked_at)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

INSERT INTO tbl_permissions (name, permission, permission_is_active)
SELECT 'Dashboard', 'dashboard', 'Aktiv'
WHERE NOT EXISTS (SELECT 1 FROM tbl_permissions WHERE permission='dashboard');

INSERT INTO tbl_permissions (name, permission, permission_is_active)
SELECT 'Müştərilər', 'customers', 'Aktiv'
WHERE NOT EXISTS (SELECT 1 FROM tbl_permissions WHERE permission='customers');

INSERT INTO tbl_permissions (name, permission, permission_is_active)
SELECT 'Vendorlar', 'vendors', 'Aktiv'
WHERE NOT EXISTS (SELECT 1 FROM tbl_permissions WHERE permission='vendors');

INSERT INTO tbl_permissions (name, permission, permission_is_active)
SELECT 'Moderasiya', 'moderation', 'Aktiv'
WHERE NOT EXISTS (SELECT 1 FROM tbl_permissions WHERE permission='moderation');

INSERT INTO tbl_permissions (name, permission, permission_is_active)
SELECT 'Ödənişlər', 'payments', 'Aktiv'
WHERE NOT EXISTS (SELECT 1 FROM tbl_permissions WHERE permission='payments');

INSERT INTO tbl_permissions (name, permission, permission_is_active)
SELECT 'Abunəliklər', 'subscriptions', 'Aktiv'
WHERE NOT EXISTS (SELECT 1 FROM tbl_permissions WHERE permission='subscriptions');

INSERT INTO tbl_permissions (name, permission, permission_is_active)
SELECT 'Şikayətlər', 'complaints', 'Aktiv'
WHERE NOT EXISTS (SELECT 1 FROM tbl_permissions WHERE permission='complaints');

INSERT INTO tbl_permissions (name, permission, permission_is_active)
SELECT 'Audit', 'audit', 'Aktiv'
WHERE NOT EXISTS (SELECT 1 FROM tbl_permissions WHERE permission='audit');

INSERT INTO tbl_permissions (name, permission, permission_is_active)
SELECT 'Hesabatlar', 'reports', 'Aktiv'
WHERE NOT EXISTS (SELECT 1 FROM tbl_permissions WHERE permission='reports');

INSERT INTO tbl_permissions (name, permission, permission_is_active)
SELECT 'Təhlükəsizlik', 'security', 'Aktiv'
WHERE NOT EXISTS (SELECT 1 FROM tbl_permissions WHERE permission='security');

INSERT INTO tbl_permissions (name, permission, permission_is_active)
SELECT 'Parametrlər', 'settings', 'Aktiv'
WHERE NOT EXISTS (SELECT 1 FROM tbl_permissions WHERE permission='settings');
