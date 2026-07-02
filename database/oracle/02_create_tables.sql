
-- FINDIT - Create Tables

-- Drop existing tables
BEGIN
  EXECUTE IMMEDIATE 'DROP TABLE audit_logs';
EXCEPTION WHEN OTHERS THEN NULL;
END;
/

BEGIN
  EXECUTE IMMEDIATE 'DROP TABLE claims';
EXCEPTION WHEN OTHERS THEN NULL;
END;
/

BEGIN
  EXECUTE IMMEDIATE 'DROP TABLE items';
EXCEPTION WHEN OTHERS THEN NULL;
END;
/

BEGIN
  EXECUTE IMMEDIATE 'DROP TABLE locations';
EXCEPTION WHEN OTHERS THEN NULL;
END;
/

BEGIN
  EXECUTE IMMEDIATE 'DROP TABLE categories';
EXCEPTION WHEN OTHERS THEN NULL;
END;
/

BEGIN
  EXECUTE IMMEDIATE 'DROP TABLE admins';
EXCEPTION WHEN OTHERS THEN NULL;
END;
/

BEGIN
  EXECUTE IMMEDIATE 'DROP TABLE users';
EXCEPTION WHEN OTHERS THEN NULL;
END;
/

-- Drop existing sequences
BEGIN
  EXECUTE IMMEDIATE 'DROP SEQUENCE seq_users';
EXCEPTION WHEN OTHERS THEN NULL;
END;
/

BEGIN
  EXECUTE IMMEDIATE 'DROP SEQUENCE seq_admins';
EXCEPTION WHEN OTHERS THEN NULL;
END;
/

BEGIN
  EXECUTE IMMEDIATE 'DROP SEQUENCE seq_categories';
EXCEPTION WHEN OTHERS THEN NULL;
END;
/

BEGIN
  EXECUTE IMMEDIATE 'DROP SEQUENCE seq_locations';
EXCEPTION WHEN OTHERS THEN NULL;
END;
/

BEGIN
  EXECUTE IMMEDIATE 'DROP SEQUENCE seq_items';
EXCEPTION WHEN OTHERS THEN NULL;
END;
/

BEGIN
  EXECUTE IMMEDIATE 'DROP SEQUENCE seq_claims';
EXCEPTION WHEN OTHERS THEN NULL;
END;
/

BEGIN
  EXECUTE IMMEDIATE 'DROP SEQUENCE seq_audit_logs';
EXCEPTION WHEN OTHERS THEN NULL;
END;
/

-- USERS table
CREATE TABLE users (
  user_id       NUMBER,
  name          VARCHAR2(100) NOT NULL,
  email         VARCHAR2(100) NOT NULL,
  password      VARCHAR2(255) NOT NULL,
  phone         VARCHAR2(20),
  address       VARCHAR2(255),
  created_at    TIMESTAMP DEFAULT CURRENT_TIMESTAMP NOT NULL,
  CONSTRAINT pk_users PRIMARY KEY (user_id),
  CONSTRAINT uk_users_email UNIQUE (email),
  CONSTRAINT chk_email_format CHECK (email LIKE '%@%')
);

CREATE SEQUENCE seq_users START WITH 1 INCREMENT BY 1;

-- ADMINS table
CREATE TABLE admins (
  admin_id      NUMBER,
  name          VARCHAR2(100) NOT NULL,
  email         VARCHAR2(100) NOT NULL,
  password      VARCHAR2(255) NOT NULL,
  created_at    TIMESTAMP DEFAULT CURRENT_TIMESTAMP NOT NULL,
  CONSTRAINT pk_admins PRIMARY KEY (admin_id),
  CONSTRAINT uk_admins_email UNIQUE (email),
  CONSTRAINT chk_admin_email CHECK (email LIKE '%@%')
);

CREATE SEQUENCE seq_admins START WITH 1 INCREMENT BY 1;

-- CATEGORIES table
CREATE TABLE categories (
  category_id   NUMBER,
  category_name VARCHAR2(100) NOT NULL,
  created_at    TIMESTAMP DEFAULT CURRENT_TIMESTAMP NOT NULL,
  CONSTRAINT pk_categories PRIMARY KEY (category_id),
  CONSTRAINT uk_categories_name UNIQUE (category_name)
);

CREATE SEQUENCE seq_categories START WITH 1 INCREMENT BY 1;

-- LOCATIONS table
CREATE TABLE locations (
  location_id   NUMBER,
  location_name VARCHAR2(100) NOT NULL,
  description   VARCHAR2(500),
  created_at    TIMESTAMP DEFAULT CURRENT_TIMESTAMP NOT NULL,
  CONSTRAINT pk_locations PRIMARY KEY (location_id),
  CONSTRAINT uk_locations_name UNIQUE (location_name)
);

CREATE SEQUENCE seq_locations START WITH 1 INCREMENT BY 1;

-- ITEMS table
CREATE TABLE items (
  item_id           NUMBER,
  user_id           NUMBER NOT NULL,
  category_id       NUMBER NOT NULL,
  location_id       NUMBER NOT NULL,
  item_name         VARCHAR2(100) NOT NULL,
  item_description  VARCHAR2(500),
  item_type         VARCHAR2(10) NOT NULL,
  item_image        VARCHAR2(255),
  lost_or_found_date DATE NOT NULL,
  status            VARCHAR2(20) DEFAULT 'PENDING' NOT NULL,
  created_at        TIMESTAMP DEFAULT CURRENT_TIMESTAMP NOT NULL,
  CONSTRAINT pk_items PRIMARY KEY (item_id),
  CONSTRAINT fk_items_user FOREIGN KEY (user_id) REFERENCES users(user_id) ON DELETE CASCADE,
  CONSTRAINT fk_items_category FOREIGN KEY (category_id) REFERENCES categories(category_id) ON DELETE CASCADE,
  CONSTRAINT fk_items_location FOREIGN KEY (location_id) REFERENCES locations(location_id) ON DELETE CASCADE,
  CONSTRAINT chk_item_type CHECK (item_type IN ('LOST', 'FOUND')),
  CONSTRAINT chk_item_status CHECK (status IN ('PENDING', 'FOUND', 'CLAIMED', 'RETURNED', 'REJECTED'))
);

CREATE SEQUENCE seq_items START WITH 1 INCREMENT BY 1;

-- CLAIMS table
CREATE TABLE claims (
  claim_id          NUMBER,
  item_id           NUMBER NOT NULL,
  user_id           NUMBER NOT NULL,
  claim_message     VARCHAR2(500),
  proof_description VARCHAR2(500),
  claim_status      VARCHAR2(20) DEFAULT 'PENDING' NOT NULL,
  created_at        TIMESTAMP DEFAULT CURRENT_TIMESTAMP NOT NULL,
  CONSTRAINT pk_claims PRIMARY KEY (claim_id),
  CONSTRAINT fk_claims_item FOREIGN KEY (item_id) REFERENCES items(item_id) ON DELETE CASCADE,
  CONSTRAINT fk_claims_user FOREIGN KEY (user_id) REFERENCES users(user_id) ON DELETE CASCADE,
  CONSTRAINT chk_claim_status CHECK (claim_status IN ('PENDING', 'APPROVED', 'REJECTED'))
);

CREATE SEQUENCE seq_claims START WITH 1 INCREMENT BY 1;

-- AUDIT_LOGS table
CREATE TABLE audit_logs (
  audit_id   NUMBER,
  table_name VARCHAR2(50) NOT NULL,
  record_id  NUMBER NOT NULL,
  action_type VARCHAR2(20) NOT NULL,
  old_status VARCHAR2(20),
  new_status VARCHAR2(20),
  action_date TIMESTAMP DEFAULT CURRENT_TIMESTAMP NOT NULL,
  action_by  VARCHAR2(100),
  CONSTRAINT pk_audit_logs PRIMARY KEY (audit_id),
  CONSTRAINT chk_action_type CHECK (action_type IN ('INSERT', 'UPDATE', 'DELETE'))
);

CREATE SEQUENCE seq_audit_logs START WITH 1 INCREMENT BY 1;
