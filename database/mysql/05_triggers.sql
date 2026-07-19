-- FindIt: audit triggers (MySQL / MariaDB)
-- Run after 02_create_tables.sql:
--   mysql -u root findit < 05_triggers.sql
-- Mirrors database/oracle/05_plsql_triggers_package.sql audit triggers.
-- Business rules live in App\Services\FinditPlsqlService (PHP transactions).

USE findit;

DROP TRIGGER IF EXISTS trg_items_audit_ai;
DROP TRIGGER IF EXISTS trg_items_audit_au;
DROP TRIGGER IF EXISTS trg_items_audit_ad;
DROP TRIGGER IF EXISTS trg_claims_audit_ai;
DROP TRIGGER IF EXISTS trg_claims_audit_au;
DROP TRIGGER IF EXISTS trg_claims_audit_ad;

DELIMITER $$

CREATE TRIGGER trg_items_audit_ai
AFTER INSERT ON items
FOR EACH ROW
BEGIN
  INSERT INTO audit_logs (table_name, record_id, action_type, old_status, new_status, action_by)
  VALUES ('ITEMS', NEW.item_id, 'INSERT', NULL, NEW.status, COALESCE(@findit_actor, CURRENT_USER()));
END$$

CREATE TRIGGER trg_items_audit_au
AFTER UPDATE ON items
FOR EACH ROW
BEGIN
  INSERT INTO audit_logs (table_name, record_id, action_type, old_status, new_status, action_by)
  VALUES ('ITEMS', NEW.item_id, 'UPDATE', OLD.status, NEW.status, COALESCE(@findit_actor, CURRENT_USER()));
END$$

CREATE TRIGGER trg_items_audit_ad
AFTER DELETE ON items
FOR EACH ROW
BEGIN
  INSERT INTO audit_logs (table_name, record_id, action_type, old_status, new_status, action_by)
  VALUES ('ITEMS', OLD.item_id, 'DELETE', OLD.status, NULL, COALESCE(@findit_actor, CURRENT_USER()));
END$$

CREATE TRIGGER trg_claims_audit_ai
AFTER INSERT ON claims
FOR EACH ROW
BEGIN
  INSERT INTO audit_logs (table_name, record_id, action_type, old_status, new_status, action_by)
  VALUES ('CLAIMS', NEW.claim_id, 'INSERT', NULL, NEW.claim_status, COALESCE(@findit_actor, CURRENT_USER()));
END$$

CREATE TRIGGER trg_claims_audit_au
AFTER UPDATE ON claims
FOR EACH ROW
BEGIN
  INSERT INTO audit_logs (table_name, record_id, action_type, old_status, new_status, action_by)
  VALUES ('CLAIMS', NEW.claim_id, 'UPDATE', OLD.claim_status, NEW.claim_status, COALESCE(@findit_actor, CURRENT_USER()));
END$$

CREATE TRIGGER trg_claims_audit_ad
AFTER DELETE ON claims
FOR EACH ROW
BEGIN
  INSERT INTO audit_logs (table_name, record_id, action_type, old_status, new_status, action_by)
  VALUES ('CLAIMS', OLD.claim_id, 'DELETE', OLD.claim_status, NULL, COALESCE(@findit_actor, CURRENT_USER()));
END$$

DELIMITER ;
