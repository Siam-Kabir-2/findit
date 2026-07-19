-- FindIt: create tables (MySQL / MariaDB)
-- Run after 01_create_database.sql:
--   mysql -u root findit < 02_create_tables.sql

USE findit;

SET FOREIGN_KEY_CHECKS = 0;

DROP TABLE IF EXISTS audit_logs;
DROP TABLE IF EXISTS claims;
DROP TABLE IF EXISTS items;
DROP TABLE IF EXISTS locations;
DROP TABLE IF EXISTS categories;
DROP TABLE IF EXISTS admins;
DROP TABLE IF EXISTS users;

SET FOREIGN_KEY_CHECKS = 1;

-- Users
CREATE TABLE users (
  user_id       INT AUTO_INCREMENT,
  name          VARCHAR(100) NOT NULL,
  email         VARCHAR(100) NOT NULL,
  password      VARCHAR(255) NOT NULL,
  phone         VARCHAR(20),
  address       VARCHAR(255),
  created_at    TIMESTAMP DEFAULT CURRENT_TIMESTAMP NOT NULL,
  CONSTRAINT pk_users PRIMARY KEY (user_id),
  CONSTRAINT uk_users_email UNIQUE (email),
  CONSTRAINT chk_email_format CHECK (email LIKE '%@%')
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Admins
CREATE TABLE admins (
  admin_id      INT AUTO_INCREMENT,
  name          VARCHAR(100) NOT NULL,
  email         VARCHAR(100) NOT NULL,
  password      VARCHAR(255) NOT NULL,
  created_at    TIMESTAMP DEFAULT CURRENT_TIMESTAMP NOT NULL,
  CONSTRAINT pk_admins PRIMARY KEY (admin_id),
  CONSTRAINT uk_admins_email UNIQUE (email),
  CONSTRAINT chk_admin_email CHECK (email LIKE '%@%')
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Categories
CREATE TABLE categories (
  category_id   INT AUTO_INCREMENT,
  category_name VARCHAR(100) NOT NULL,
  created_at    TIMESTAMP DEFAULT CURRENT_TIMESTAMP NOT NULL,
  CONSTRAINT pk_categories PRIMARY KEY (category_id),
  CONSTRAINT uk_categories_name UNIQUE (category_name)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Locations
CREATE TABLE locations (
  location_id   INT AUTO_INCREMENT,
  location_name VARCHAR(100) NOT NULL,
  description   VARCHAR(500),
  created_at    TIMESTAMP DEFAULT CURRENT_TIMESTAMP NOT NULL,
  CONSTRAINT pk_locations PRIMARY KEY (location_id),
  CONSTRAINT uk_locations_name UNIQUE (location_name)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Items
CREATE TABLE items (
  item_id           INT AUTO_INCREMENT,
  user_id           INT NOT NULL,
  category_id       INT NOT NULL,
  location_id       INT NOT NULL,
  item_name         VARCHAR(100) NOT NULL,
  item_description  VARCHAR(500),
  item_type         VARCHAR(10) NOT NULL,
  item_image        VARCHAR(255),
  lost_or_found_date DATE NOT NULL,
  status            VARCHAR(20) DEFAULT 'PENDING' NOT NULL,
  created_at        TIMESTAMP DEFAULT CURRENT_TIMESTAMP NOT NULL,
  CONSTRAINT pk_items PRIMARY KEY (item_id),
  CONSTRAINT fk_items_user FOREIGN KEY (user_id) REFERENCES users(user_id) ON DELETE CASCADE,
  CONSTRAINT fk_items_category FOREIGN KEY (category_id) REFERENCES categories(category_id) ON DELETE CASCADE,
  CONSTRAINT fk_items_location FOREIGN KEY (location_id) REFERENCES locations(location_id) ON DELETE CASCADE,
  CONSTRAINT chk_item_type CHECK (item_type IN ('LOST', 'FOUND')),
  CONSTRAINT chk_item_status CHECK (status IN ('PENDING', 'FOUND', 'CLAIMED', 'RETURNED', 'REJECTED'))
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Claims
CREATE TABLE claims (
  claim_id          INT AUTO_INCREMENT,
  item_id           INT NOT NULL,
  user_id           INT NOT NULL,
  claim_message     VARCHAR(500),
  proof_description VARCHAR(500),
  claim_status      VARCHAR(20) DEFAULT 'PENDING' NOT NULL,
  created_at        TIMESTAMP DEFAULT CURRENT_TIMESTAMP NOT NULL,
  CONSTRAINT pk_claims PRIMARY KEY (claim_id),
  CONSTRAINT fk_claims_item FOREIGN KEY (item_id) REFERENCES items(item_id) ON DELETE CASCADE,
  CONSTRAINT fk_claims_user FOREIGN KEY (user_id) REFERENCES users(user_id) ON DELETE CASCADE,
  CONSTRAINT chk_claim_status CHECK (claim_status IN ('PENDING', 'APPROVED', 'REJECTED'))
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Audit logs
CREATE TABLE audit_logs (
  audit_id    INT AUTO_INCREMENT,
  table_name  VARCHAR(50) NOT NULL,
  record_id   INT NOT NULL,
  action_type VARCHAR(20) NOT NULL,
  old_status  VARCHAR(20),
  new_status  VARCHAR(20),
  action_date TIMESTAMP DEFAULT CURRENT_TIMESTAMP NOT NULL,
  action_by   VARCHAR(100),
  CONSTRAINT pk_audit_logs PRIMARY KEY (audit_id),
  CONSTRAINT chk_action_type CHECK (action_type IN ('INSERT', 'UPDATE', 'DELETE'))
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
