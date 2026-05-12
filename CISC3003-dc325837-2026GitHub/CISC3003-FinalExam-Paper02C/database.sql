CREATE DATABASE IF NOT EXISTS cisc3003_scenario_c
  CHARACTER SET utf8mb4
  COLLATE utf8mb4_unicode_ci;

USE cisc3003_scenario_c;

CREATE TABLE IF NOT EXISTS users (
  id INT UNSIGNED NOT NULL AUTO_INCREMENT,
  email VARCHAR(255) NOT NULL,
  password_hash VARCHAR(255) NOT NULL,
  full_name VARCHAR(120) NOT NULL,
  email_verified TINYINT(1) NOT NULL DEFAULT 0,
  verify_token VARCHAR(64) DEFAULT NULL,
  verify_expires DATETIME DEFAULT NULL,
  reset_token VARCHAR(64) DEFAULT NULL,
  reset_expires DATETIME DEFAULT NULL,
  created_at TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (id),
  UNIQUE KEY uq_users_email (email),
  KEY idx_verify_token (verify_token),
  KEY idx_reset_token (reset_token)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
