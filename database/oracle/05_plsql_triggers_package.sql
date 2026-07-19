-- FindIt: triggers and PL/SQL package
-- Run as findit user after tables are created.

-- Auto ID on insert

CREATE OR REPLACE TRIGGER trg_users_bi
BEFORE INSERT ON users
FOR EACH ROW
BEGIN
  IF :NEW.user_id IS NULL THEN
    SELECT seq_users.NEXTVAL INTO :NEW.user_id FROM dual;
  END IF;
END;
/

CREATE OR REPLACE TRIGGER trg_admins_bi
BEFORE INSERT ON admins
FOR EACH ROW
BEGIN
  IF :NEW.admin_id IS NULL THEN
    SELECT seq_admins.NEXTVAL INTO :NEW.admin_id FROM dual;
  END IF;
END;
/

CREATE OR REPLACE TRIGGER trg_categories_bi
BEFORE INSERT ON categories
FOR EACH ROW
BEGIN
  IF :NEW.category_id IS NULL THEN
    SELECT seq_categories.NEXTVAL INTO :NEW.category_id FROM dual;
  END IF;
END;
/

CREATE OR REPLACE TRIGGER trg_locations_bi
BEFORE INSERT ON locations
FOR EACH ROW
BEGIN
  IF :NEW.location_id IS NULL THEN
    SELECT seq_locations.NEXTVAL INTO :NEW.location_id FROM dual;
  END IF;
END;
/

CREATE OR REPLACE TRIGGER trg_items_bi
BEFORE INSERT ON items
FOR EACH ROW
BEGIN
  IF :NEW.item_id IS NULL THEN
    SELECT seq_items.NEXTVAL INTO :NEW.item_id FROM dual;
  END IF;
  IF :NEW.status IS NULL THEN
    :NEW.status := 'PENDING';
  END IF;
END;
/

CREATE OR REPLACE TRIGGER trg_claims_bi
BEFORE INSERT ON claims
FOR EACH ROW
BEGIN
  IF :NEW.claim_id IS NULL THEN
    SELECT seq_claims.NEXTVAL INTO :NEW.claim_id FROM dual;
  END IF;
  IF :NEW.claim_status IS NULL THEN
    :NEW.claim_status := 'PENDING';
  END IF;
END;
/

CREATE OR REPLACE TRIGGER trg_audit_logs_bi
BEFORE INSERT ON audit_logs
FOR EACH ROW
BEGIN
  IF :NEW.audit_id IS NULL THEN
    SELECT seq_audit_logs.NEXTVAL INTO :NEW.audit_id FROM dual;
  END IF;
END;
/

-- Write changes to audit_logs

CREATE OR REPLACE TRIGGER trg_items_audit
AFTER INSERT OR UPDATE OR DELETE ON items
FOR EACH ROW
BEGIN
  IF INSERTING THEN
    INSERT INTO audit_logs (table_name, record_id, action_type, old_status, new_status, action_by)
    VALUES ('ITEMS', :NEW.item_id, 'INSERT', NULL, :NEW.status, USER);
  ELSIF UPDATING THEN
    INSERT INTO audit_logs (table_name, record_id, action_type, old_status, new_status, action_by)
    VALUES ('ITEMS', :NEW.item_id, 'UPDATE', :OLD.status, :NEW.status, USER);
  ELSIF DELETING THEN
    INSERT INTO audit_logs (table_name, record_id, action_type, old_status, new_status, action_by)
    VALUES ('ITEMS', :OLD.item_id, 'DELETE', :OLD.status, NULL, USER);
  END IF;
END;
/

CREATE OR REPLACE TRIGGER trg_claims_audit
AFTER INSERT OR UPDATE OR DELETE ON claims
FOR EACH ROW
BEGIN
  IF INSERTING THEN
    INSERT INTO audit_logs (table_name, record_id, action_type, old_status, new_status, action_by)
    VALUES ('CLAIMS', :NEW.claim_id, 'INSERT', NULL, :NEW.claim_status, USER);
  ELSIF UPDATING THEN
    INSERT INTO audit_logs (table_name, record_id, action_type, old_status, new_status, action_by)
    VALUES ('CLAIMS', :NEW.claim_id, 'UPDATE', :OLD.claim_status, :NEW.claim_status, USER);
  ELSIF DELETING THEN
    INSERT INTO audit_logs (table_name, record_id, action_type, old_status, new_status, action_by)
    VALUES ('CLAIMS', :OLD.claim_id, 'DELETE', :OLD.claim_status, NULL, USER);
  END IF;
END;
/

-- Package header

CREATE OR REPLACE PACKAGE findit_pkg AS
  PROCEDURE register_user(
    p_name IN VARCHAR2,
    p_email IN VARCHAR2,
    p_password IN VARCHAR2,
    p_phone IN VARCHAR2,
    p_address IN VARCHAR2,
    p_user_id OUT NUMBER
  );

  PROCEDURE add_item(
    p_user_id IN NUMBER,
    p_category_id IN NUMBER,
    p_location_id IN NUMBER,
    p_item_name IN VARCHAR2,
    p_item_description IN VARCHAR2,
    p_item_type IN VARCHAR2,
    p_item_image IN VARCHAR2,
    p_lost_or_found_date IN DATE,
    p_item_id OUT NUMBER
  );

  PROCEDURE update_item_status(
    p_item_id IN NUMBER,
    p_status IN VARCHAR2
  );

  PROCEDURE submit_claim(
    p_item_id IN NUMBER,
    p_user_id IN NUMBER,
    p_claim_message IN VARCHAR2,
    p_proof_description IN VARCHAR2,
    p_claim_id OUT NUMBER
  );

  PROCEDURE approve_claim(
    p_claim_id IN NUMBER,
    p_admin_name IN VARCHAR2
  );

  PROCEDURE reject_claim(
    p_claim_id IN NUMBER,
    p_admin_name IN VARCHAR2
  );

  PROCEDURE add_category(
    p_category_name IN VARCHAR2,
    p_category_id OUT NUMBER
  );

  PROCEDURE add_location(
    p_location_name IN VARCHAR2,
    p_description IN VARCHAR2,
    p_location_id OUT NUMBER
  );

  PROCEDURE delete_category(p_category_id IN NUMBER);
  PROCEDURE delete_location(p_location_id IN NUMBER);
  PROCEDURE delete_user(p_user_id IN NUMBER);
  PROCEDURE delete_item(p_item_id IN NUMBER);

  FUNCTION get_total_users RETURN NUMBER;
  FUNCTION get_total_items RETURN NUMBER;
  FUNCTION get_pending_claims RETURN NUMBER;
  FUNCTION get_approved_claims RETURN NUMBER;
  FUNCTION get_lost_items RETURN NUMBER;
  FUNCTION get_found_items RETURN NUMBER;
END findit_pkg;
/

-- Package body

CREATE OR REPLACE PACKAGE BODY findit_pkg AS

  PROCEDURE register_user(
    p_name IN VARCHAR2,
    p_email IN VARCHAR2,
    p_password IN VARCHAR2,
    p_phone IN VARCHAR2,
    p_address IN VARCHAR2,
    p_user_id OUT NUMBER
  ) IS
    v_count NUMBER;
  BEGIN
    SELECT COUNT(*) INTO v_count FROM users WHERE LOWER(email) = LOWER(p_email);
    IF v_count > 0 THEN
      RAISE_APPLICATION_ERROR(-20001, 'Email already registered');
    END IF;

    INSERT INTO users (name, email, password, phone, address)
    VALUES (p_name, p_email, p_password, p_phone, p_address)
    RETURNING user_id INTO p_user_id;
  END register_user;

  PROCEDURE add_item(
    p_user_id IN NUMBER,
    p_category_id IN NUMBER,
    p_location_id IN NUMBER,
    p_item_name IN VARCHAR2,
    p_item_description IN VARCHAR2,
    p_item_type IN VARCHAR2,
    p_item_image IN VARCHAR2,
    p_lost_or_found_date IN DATE,
    p_item_id OUT NUMBER
  ) IS
  BEGIN
    IF UPPER(p_item_type) NOT IN ('LOST', 'FOUND') THEN
      RAISE_APPLICATION_ERROR(-20002, 'Item type must be LOST or FOUND');
    END IF;

    INSERT INTO items (
      user_id, category_id, location_id, item_name, item_description,
      item_type, item_image, lost_or_found_date, status
    ) VALUES (
      p_user_id, p_category_id, p_location_id, p_item_name, p_item_description,
      UPPER(p_item_type), p_item_image, p_lost_or_found_date, 'PENDING'
    ) RETURNING item_id INTO p_item_id;
  END add_item;

  PROCEDURE update_item_status(
    p_item_id IN NUMBER,
    p_status IN VARCHAR2
  ) IS
  BEGIN
    IF UPPER(p_status) NOT IN ('PENDING', 'FOUND', 'CLAIMED', 'RETURNED', 'REJECTED') THEN
      RAISE_APPLICATION_ERROR(-20003, 'Invalid item status');
    END IF;

    UPDATE items SET status = UPPER(p_status) WHERE item_id = p_item_id;

    IF SQL%ROWCOUNT = 0 THEN
      RAISE_APPLICATION_ERROR(-20004, 'Item not found');
    END IF;
  END update_item_status;

  PROCEDURE submit_claim(
    p_item_id IN NUMBER,
    p_user_id IN NUMBER,
    p_claim_message IN VARCHAR2,
    p_proof_description IN VARCHAR2,
    p_claim_id OUT NUMBER
  ) IS
    v_item_user NUMBER;
    v_status VARCHAR2(20);
    v_exists NUMBER;
  BEGIN
    BEGIN
      SELECT user_id, status INTO v_item_user, v_status
      FROM items WHERE item_id = p_item_id;
    EXCEPTION
      WHEN NO_DATA_FOUND THEN
        RAISE_APPLICATION_ERROR(-20005, 'Item not found');
    END;

    IF v_item_user = p_user_id THEN
      RAISE_APPLICATION_ERROR(-20006, 'Cannot claim your own item');
    END IF;

    IF v_status IN ('CLAIMED', 'RETURNED', 'REJECTED') THEN
      RAISE_APPLICATION_ERROR(-20007, 'Item is not available for claims');
    END IF;

    SELECT COUNT(*) INTO v_exists
    FROM claims
    WHERE item_id = p_item_id AND user_id = p_user_id AND claim_status = 'PENDING';

    IF v_exists > 0 THEN
      RAISE_APPLICATION_ERROR(-20008, 'You already have a pending claim for this item');
    END IF;

    INSERT INTO claims (item_id, user_id, claim_message, proof_description, claim_status)
    VALUES (p_item_id, p_user_id, p_claim_message, p_proof_description, 'PENDING')
    RETURNING claim_id INTO p_claim_id;
  END submit_claim;

  PROCEDURE approve_claim(
    p_claim_id IN NUMBER,
    p_admin_name IN VARCHAR2
  ) IS
    v_item_id NUMBER;
    v_status VARCHAR2(20);
  BEGIN
    BEGIN
      SELECT item_id, claim_status INTO v_item_id, v_status
      FROM claims WHERE claim_id = p_claim_id;
    EXCEPTION
      WHEN NO_DATA_FOUND THEN
        RAISE_APPLICATION_ERROR(-20009, 'Claim not found');
    END;

    IF v_status <> 'PENDING' THEN
      RAISE_APPLICATION_ERROR(-20010, 'Only pending claims can be approved');
    END IF;

    UPDATE claims SET claim_status = 'APPROVED' WHERE claim_id = p_claim_id;

    -- Reject other pending claims for this item
    UPDATE claims
    SET claim_status = 'REJECTED'
    WHERE item_id = v_item_id
      AND claim_id <> p_claim_id
      AND claim_status = 'PENDING';

    UPDATE items SET status = 'CLAIMED' WHERE item_id = v_item_id;

    INSERT INTO audit_logs (table_name, record_id, action_type, old_status, new_status, action_by)
    VALUES ('CLAIMS', p_claim_id, 'UPDATE', 'PENDING', 'APPROVED', NVL(p_admin_name, USER));
  END approve_claim;

  PROCEDURE reject_claim(
    p_claim_id IN NUMBER,
    p_admin_name IN VARCHAR2
  ) IS
    v_status VARCHAR2(20);
  BEGIN
    BEGIN
      SELECT claim_status INTO v_status FROM claims WHERE claim_id = p_claim_id;
    EXCEPTION
      WHEN NO_DATA_FOUND THEN
        RAISE_APPLICATION_ERROR(-20009, 'Claim not found');
    END;

    IF v_status <> 'PENDING' THEN
      RAISE_APPLICATION_ERROR(-20011, 'Only pending claims can be rejected');
    END IF;

    UPDATE claims SET claim_status = 'REJECTED' WHERE claim_id = p_claim_id;

    INSERT INTO audit_logs (table_name, record_id, action_type, old_status, new_status, action_by)
    VALUES ('CLAIMS', p_claim_id, 'UPDATE', 'PENDING', 'REJECTED', NVL(p_admin_name, USER));
  END reject_claim;

  PROCEDURE add_category(
    p_category_name IN VARCHAR2,
    p_category_id OUT NUMBER
  ) IS
  BEGIN
    INSERT INTO categories (category_name)
    VALUES (p_category_name)
    RETURNING category_id INTO p_category_id;
  EXCEPTION
    WHEN DUP_VAL_ON_INDEX THEN
      RAISE_APPLICATION_ERROR(-20012, 'Category already exists');
  END add_category;

  PROCEDURE add_location(
    p_location_name IN VARCHAR2,
    p_description IN VARCHAR2,
    p_location_id OUT NUMBER
  ) IS
  BEGIN
    INSERT INTO locations (location_name, description)
    VALUES (p_location_name, p_description)
    RETURNING location_id INTO p_location_id;
  EXCEPTION
    WHEN DUP_VAL_ON_INDEX THEN
      RAISE_APPLICATION_ERROR(-20013, 'Location already exists');
  END add_location;

  PROCEDURE delete_category(p_category_id IN NUMBER) IS
    v_count NUMBER;
  BEGIN
    SELECT COUNT(*) INTO v_count FROM items WHERE category_id = p_category_id;
    IF v_count > 0 THEN
      RAISE_APPLICATION_ERROR(-20014, 'Cannot delete category with linked items');
    END IF;
    DELETE FROM categories WHERE category_id = p_category_id;
  END delete_category;

  PROCEDURE delete_location(p_location_id IN NUMBER) IS
    v_count NUMBER;
  BEGIN
    SELECT COUNT(*) INTO v_count FROM items WHERE location_id = p_location_id;
    IF v_count > 0 THEN
      RAISE_APPLICATION_ERROR(-20015, 'Cannot delete location with linked items');
    END IF;
    DELETE FROM locations WHERE location_id = p_location_id;
  END delete_location;

  PROCEDURE delete_user(p_user_id IN NUMBER) IS
  BEGIN
    DELETE FROM users WHERE user_id = p_user_id;
    IF SQL%ROWCOUNT = 0 THEN
      RAISE_APPLICATION_ERROR(-20016, 'User not found');
    END IF;
  END delete_user;

  PROCEDURE delete_item(p_item_id IN NUMBER) IS
  BEGIN
    DELETE FROM items WHERE item_id = p_item_id;
    IF SQL%ROWCOUNT = 0 THEN
      RAISE_APPLICATION_ERROR(-20004, 'Item not found');
    END IF;
  END delete_item;

  FUNCTION get_total_users RETURN NUMBER IS
    v NUMBER;
  BEGIN
    SELECT COUNT(*) INTO v FROM users;
    RETURN v;
  END get_total_users;

  FUNCTION get_total_items RETURN NUMBER IS
    v NUMBER;
  BEGIN
    SELECT COUNT(*) INTO v FROM items;
    RETURN v;
  END get_total_items;

  FUNCTION get_pending_claims RETURN NUMBER IS
    v NUMBER;
  BEGIN
    SELECT COUNT(*) INTO v FROM claims WHERE claim_status = 'PENDING';
    RETURN v;
  END get_pending_claims;

  FUNCTION get_approved_claims RETURN NUMBER IS
    v NUMBER;
  BEGIN
    SELECT COUNT(*) INTO v FROM claims WHERE claim_status = 'APPROVED';
    RETURN v;
  END get_approved_claims;

  FUNCTION get_lost_items RETURN NUMBER IS
    v NUMBER;
  BEGIN
    SELECT COUNT(*) INTO v FROM items WHERE item_type = 'LOST';
    RETURN v;
  END get_lost_items;

  FUNCTION get_found_items RETURN NUMBER IS
    v NUMBER;
  BEGIN
    SELECT COUNT(*) INTO v FROM items WHERE item_type = 'FOUND';
    RETURN v;
  END get_found_items;

END findit_pkg;
/

COMMIT;
