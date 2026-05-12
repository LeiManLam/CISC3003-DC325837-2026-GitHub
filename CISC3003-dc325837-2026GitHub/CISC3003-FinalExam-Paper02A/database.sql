-- Scenario A: create database and users table (import via phpMyAdmin or mysql CLI)
CREATE DATABASE IF NOT EXISTS cisc3003_scenario_a
  CHARACTER SET utf8mb4
  COLLATE utf8mb4_unicode_ci;

USE cisc3003_scenario_a;

CREATE TABLE IF NOT EXISTS users (
  id INT UNSIGNED NOT NULL AUTO_INCREMENT,
  full_name VARCHAR(120) NOT NULL,
  email VARCHAR(255) NOT NULL,
  phone VARCHAR(40) NOT NULL DEFAULT '',
  country VARCHAR(60) NOT NULL DEFAULT '',
  gender VARCHAR(20) NOT NULL DEFAULT '',
  interests VARCHAR(255) NOT NULL DEFAULT '',
  comments TEXT,
  password_hash VARCHAR(255) NOT NULL,
  created_at TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (id),
  UNIQUE KEY uq_users_email (email)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Example INSERT (optional); prefer registering via register.php + prepared statement
-- INSERT INTO users (full_name, email, phone, country, gender, interests, comments, password_hash)
-- VALUES ('Demo User', 'demo@example.com', '12345678', 'HK', 'x', 'web,mobile', 'Hello', '$2y$10$...');
