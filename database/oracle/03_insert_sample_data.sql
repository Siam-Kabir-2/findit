-- FINDIT - Insert Sample Data

-- USERS (10 records)
INSERT INTO users (user_id, name, email, password, phone, address, created_at)
VALUES (seq_users.NEXTVAL, 'John Smith', 'john.smith@university.edu', 'pass_john123', '555-0101', '123 Main St, Campus Apt 101', CURRENT_TIMESTAMP);

INSERT INTO users (user_id, name, email, password, phone, address, created_at)
VALUES (seq_users.NEXTVAL, 'Sarah Johnson', 'sarah.johnson@university.edu', 'pass_sarah456', '555-0102', '456 Oak Ave, Dorm Hall B', CURRENT_TIMESTAMP);

INSERT INTO users (user_id, name, email, password, phone, address, created_at)
VALUES (seq_users.NEXTVAL, 'Emily Davis', 'emily.davis@university.edu', 'pass_emily789', '555-0103', '789 Elm Rd, Student Housing', CURRENT_TIMESTAMP);

INSERT INTO users (user_id, name, email, password, phone, address, created_at)
VALUES (seq_users.NEXTVAL, 'Michael Chen', 'michael.chen@university.edu', 'pass_michael101', '555-0104', '321 Pine Lane, Apt 205', CURRENT_TIMESTAMP);

INSERT INTO users (user_id, name, email, password, phone, address, created_at)
VALUES (seq_users.NEXTVAL, 'Jessica Martinez', 'jessica.martinez@university.edu', 'pass_jessica202', '555-0105', '654 Maple Dr, Campus House', CURRENT_TIMESTAMP);

INSERT INTO users (user_id, name, email, password, phone, address, created_at)
VALUES (seq_users.NEXTVAL, 'David Wilson', 'david.wilson@university.edu', 'pass_david303', '555-0106', '987 Cedar St, Dorm Hall A', CURRENT_TIMESTAMP);

INSERT INTO users (user_id, name, email, password, phone, address, created_at)
VALUES (seq_users.NEXTVAL, 'Lisa Anderson', 'lisa.anderson@university.edu', 'pass_lisa404', '555-0107', '147 Birch Ave, Student Apt', CURRENT_TIMESTAMP);

INSERT INTO users (user_id, name, email, password, phone, address, created_at)
VALUES (seq_users.NEXTVAL, 'Robert Taylor', 'robert.taylor@university.edu', 'pass_robert505', '555-0108', '258 Walnut Blvd, Apartment 3', CURRENT_TIMESTAMP);

INSERT INTO users (user_id, name, email, password, phone, address, created_at)
VALUES (seq_users.NEXTVAL, 'Amanda Brown', 'amanda.brown@university.edu', 'pass_amanda606', '555-0109', '369 Ash Lane, Campus Housing', CURRENT_TIMESTAMP);

INSERT INTO users (user_id, name, email, password, phone, address, created_at)
VALUES (seq_users.NEXTVAL, 'Christopher Lee', 'christopher.lee@university.edu', 'pass_christopher707', '555-0110', '741 Spruce Rd, Dorm Room 102', CURRENT_TIMESTAMP);

-- ADMINS (2 records)
INSERT INTO admins (admin_id, name, email, password, created_at)
VALUES (seq_admins.NEXTVAL, 'Admin User One', 'admin.one@university.edu', 'admin_pass_secure_001', CURRENT_TIMESTAMP);

INSERT INTO admins (admin_id, name, email, password, created_at)
VALUES (seq_admins.NEXTVAL, 'Admin User Two', 'admin.two@university.edu', 'admin_pass_secure_002', CURRENT_TIMESTAMP);

-- CATEGORIES (8 records)
INSERT INTO categories (category_id, category_name, created_at)
VALUES (seq_categories.NEXTVAL, 'Electronics', CURRENT_TIMESTAMP);

INSERT INTO categories (category_id, category_name, created_at)
VALUES (seq_categories.NEXTVAL, 'Documents', CURRENT_TIMESTAMP);

INSERT INTO categories (category_id, category_name, created_at)
VALUES (seq_categories.NEXTVAL, 'Bags', CURRENT_TIMESTAMP);

INSERT INTO categories (category_id, category_name, created_at)
VALUES (seq_categories.NEXTVAL, 'Keys', CURRENT_TIMESTAMP);

INSERT INTO categories (category_id, category_name, created_at)
VALUES (seq_categories.NEXTVAL, 'ID Cards', CURRENT_TIMESTAMP);

INSERT INTO categories (category_id, category_name, created_at)
VALUES (seq_categories.NEXTVAL, 'Accessories', CURRENT_TIMESTAMP);

INSERT INTO categories (category_id, category_name, created_at)
VALUES (seq_categories.NEXTVAL, 'Clothing', CURRENT_TIMESTAMP);

INSERT INTO categories (category_id, category_name, created_at)
VALUES (seq_categories.NEXTVAL, 'Other', CURRENT_TIMESTAMP);

-- LOCATIONS (8 records)
INSERT INTO locations (location_id, location_name, description, created_at)
VALUES (seq_locations.NEXTVAL, 'Main Library', 'Central campus library building', CURRENT_TIMESTAMP);

INSERT INTO locations (location_id, location_name, description, created_at)
VALUES (seq_locations.NEXTVAL, 'Student Cafeteria', 'Main dining hall in Student Center', CURRENT_TIMESTAMP);

INSERT INTO locations (location_id, location_name, description, created_at)
VALUES (seq_locations.NEXTVAL, 'Computer Lab A', 'Technology center, Building 5', CURRENT_TIMESTAMP);

INSERT INTO locations (location_id, location_name, description, created_at)
VALUES (seq_locations.NEXTVAL, 'Parking Lot B', 'North campus parking area', CURRENT_TIMESTAMP);

INSERT INTO locations (location_id, location_name, description, created_at)
VALUES (seq_locations.NEXTVAL, 'Student Union', 'Main social hub and event center', CURRENT_TIMESTAMP);

INSERT INTO locations (location_id, location_name, description, created_at)
VALUES (seq_locations.NEXTVAL, 'Science Building', 'Physics and Chemistry labs', CURRENT_TIMESTAMP);

INSERT INTO locations (location_id, location_name, description, created_at)
VALUES (seq_locations.NEXTVAL, 'Sports Complex', 'Gym and athletic facilities', CURRENT_TIMESTAMP);

INSERT INTO locations (location_id, location_name, description, created_at)
VALUES (seq_locations.NEXTVAL, 'Dorm Hall A', 'Residential building near campus entrance', CURRENT_TIMESTAMP);

-- ITEMS (20 records - LOST)
INSERT INTO items (item_id, user_id, category_id, location_id, item_name, item_description, item_type, item_image, lost_or_found_date, status, created_at)
VALUES (seq_items.NEXTVAL, 1, 1, 1, 'Dell Laptop', 'Silver Dell Inspiron 15 laptop, model 5570', 'LOST', 'laptop_001.jpg', TO_DATE('2026-06-15', 'YYYY-MM-DD'), 'PENDING', CURRENT_TIMESTAMP);

INSERT INTO items (item_id, user_id, category_id, location_id, item_name, item_description, item_type, item_image, lost_or_found_date, status, created_at)
VALUES (seq_items.NEXTVAL, 2, 1, 2, 'Apple iPhone', 'iPhone 13 Pro, Space Grey color', 'LOST', 'iphone_001.jpg', TO_DATE('2026-06-14', 'YYYY-MM-DD'), 'FOUND', CURRENT_TIMESTAMP);

INSERT INTO items (item_id, user_id, category_id, location_id, item_name, item_description, item_type, item_image, lost_or_found_date, status, created_at)
VALUES (seq_items.NEXTVAL, 3, 1, 3, 'Sony Headphones', 'Sony WH-1000XM4 wireless headphones', 'LOST', 'headphones_001.jpg', TO_DATE('2026-06-13', 'YYYY-MM-DD'), 'PENDING', CURRENT_TIMESTAMP);

INSERT INTO items (item_id, user_id, category_id, location_id, item_name, item_description, item_type, item_image, lost_or_found_date, status, created_at)
VALUES (seq_items.NEXTVAL, 4, 2, 1, 'Student ID Card', 'Campus ID with photo, plastic card', 'LOST', 'idcard_001.jpg', TO_DATE('2026-06-12', 'YYYY-MM-DD'), 'CLAIMED', CURRENT_TIMESTAMP);

INSERT INTO items (item_id, user_id, category_id, location_id, item_name, item_description, item_type, item_image, lost_or_found_date, status, created_at)
VALUES (seq_items.NEXTVAL, 5, 3, 2, 'Black Backpack', 'Nike Black backpack with red logo', 'LOST', 'backpack_001.jpg', TO_DATE('2026-06-11', 'YYYY-MM-DD'), 'RETURNED', CURRENT_TIMESTAMP);

INSERT INTO items (item_id, user_id, category_id, location_id, item_name, item_description, item_type, item_image, lost_or_found_date, status, created_at)
VALUES (seq_items.NEXTVAL, 6, 4, 4, 'Set of Keys', 'House keys and car key on blue keychain', 'LOST', 'keys_001.jpg', TO_DATE('2026-06-10', 'YYYY-MM-DD'), 'PENDING', CURRENT_TIMESTAMP);

INSERT INTO items (item_id, user_id, category_id, location_id, item_name, item_description, item_type, item_image, lost_or_found_date, status, created_at)
VALUES (seq_items.NEXTVAL, 7, 2, 5, 'Class Notes Folder', 'Red folder with biology class notes', 'LOST', 'notes_001.jpg', TO_DATE('2026-06-09', 'YYYY-MM-DD'), 'REJECTED', CURRENT_TIMESTAMP);

INSERT INTO items (item_id, user_id, category_id, location_id, item_name, item_description, item_type, item_image, lost_or_found_date, status, created_at)
VALUES (seq_items.NEXTVAL, 8, 3, 6, 'Brown Leather Wallet', 'Leather bifold wallet with business cards', 'LOST', 'wallet_001.jpg', TO_DATE('2026-06-08', 'YYYY-MM-DD'), 'PENDING', CURRENT_TIMESTAMP);

-- ITEMS (FOUND)
INSERT INTO items (item_id, user_id, category_id, location_id, item_name, item_description, item_type, item_image, lost_or_found_date, status, created_at)
VALUES (seq_items.NEXTVAL, 9, 1, 7, 'Samsung Galaxy Watch', 'Black smartwatch found in gym locker', 'FOUND', 'watch_001.jpg', TO_DATE('2026-06-17', 'YYYY-MM-DD'), 'PENDING', CURRENT_TIMESTAMP);

INSERT INTO items (item_id, user_id, category_id, location_id, item_name, item_description, item_type, item_image, lost_or_found_date, status, created_at)
VALUES (seq_items.NEXTVAL, 10, 6, 8, 'Blue Glasses', 'Blue-framed reading glasses in case', 'FOUND', 'glasses_001.jpg', TO_DATE('2026-06-16', 'YYYY-MM-DD'), 'FOUND', CURRENT_TIMESTAMP);

INSERT INTO items (item_id, user_id, category_id, location_id, item_name, item_description, item_type, item_image, lost_or_found_date, status, created_at)
VALUES (seq_items.NEXTVAL, 1, 2, 1, 'Driver License', 'License found in library study area', 'FOUND', 'license_001.jpg', TO_DATE('2026-06-18', 'YYYY-MM-DD'), 'CLAIMED', CURRENT_TIMESTAMP);

INSERT INTO items (item_id, user_id, category_id, location_id, item_name, item_description, item_type, item_image, lost_or_found_date, status, created_at)
VALUES (seq_items.NEXTVAL, 2, 7, 2, 'Red Jacket', 'Red winter jacket found in cafeteria', 'FOUND', 'jacket_001.jpg', TO_DATE('2026-06-19', 'YYYY-MM-DD'), 'PENDING', CURRENT_TIMESTAMP);

INSERT INTO items (item_id, user_id, category_id, location_id, item_name, item_description, item_type, item_image, lost_or_found_date, status, created_at)
VALUES (seq_items.NEXTVAL, 3, 1, 3, 'USB Flash Drive', 'Kingston 32GB USB drive found in lab', 'FOUND', 'usb_001.jpg', TO_DATE('2026-06-20', 'YYYY-MM-DD'), 'PENDING', CURRENT_TIMESTAMP);

INSERT INTO items (item_id, user_id, category_id, location_id, item_name, item_description, item_type, item_image, lost_or_found_date, status, created_at)
VALUES (seq_items.NEXTVAL, 4, 6, 5, 'Silver Ring', 'Silver band ring found in student union', 'FOUND', 'ring_001.jpg', TO_DATE('2026-06-21', 'YYYY-MM-DD'), 'PENDING', CURRENT_TIMESTAMP);

INSERT INTO items (item_id, user_id, category_id, location_id, item_name, item_description, item_type, item_image, lost_or_found_date, status, created_at)
VALUES (seq_items.NEXTVAL, 5, 8, 4, 'Unknown Item', 'Mystery item found in parking lot', 'FOUND', 'unknown_001.jpg', TO_DATE('2026-06-21', 'YYYY-MM-DD'), 'PENDING', CURRENT_TIMESTAMP);

INSERT INTO items (item_id, user_id, category_id, location_id, item_name, item_description, item_type, item_image, lost_or_found_date, status, created_at)
VALUES (seq_items.NEXTVAL, 6, 3, 1, 'Green Canvas Bag', 'Green canvas messenger bag', 'FOUND', 'bag_001.jpg', TO_DATE('2026-06-21', 'YYYY-MM-DD'), 'RETURNED', CURRENT_TIMESTAMP);

INSERT INTO items (item_id, user_id, category_id, location_id, item_name, item_description, item_type, item_image, lost_or_found_date, status, created_at)
VALUES (seq_items.NEXTVAL, 7, 1, 6, 'iPad Air', 'Apple iPad Air in silver found in lab', 'FOUND', 'ipad_001.jpg', TO_DATE('2026-06-20', 'YYYY-MM-DD'), 'CLAIMED', CURRENT_TIMESTAMP);

INSERT INTO items (item_id, user_id, category_id, location_id, item_name, item_description, item_type, item_image, lost_or_found_date, status, created_at)
VALUES (seq_items.NEXTVAL, 8, 2, 8, 'Exam Papers', 'Stack of exam papers in folder', 'FOUND', 'papers_001.jpg', TO_DATE('2026-06-19', 'YYYY-MM-DD'), 'PENDING', CURRENT_TIMESTAMP);

-- CLAIMS (10 records)
INSERT INTO claims (claim_id, item_id, user_id, claim_message, proof_description, claim_status, created_at)
VALUES (seq_claims.NEXTVAL, 2, 2, 'This is my iPhone 13 Pro', 'I can provide IMEI number and Apple ID', 'PENDING', CURRENT_TIMESTAMP);

INSERT INTO claims (claim_id, item_id, user_id, claim_message, proof_description, claim_status, created_at)
VALUES (seq_claims.NEXTVAL, 4, 4, 'My student ID, I lost it at the library', 'Photo ID in wallet can verify my appearance', 'APPROVED', CURRENT_TIMESTAMP);

INSERT INTO claims (claim_id, item_id, user_id, claim_message, proof_description, claim_status, created_at)
VALUES (seq_claims.NEXTVAL, 5, 5, 'My backpack from Nike, had my books', 'I have receipt showing purchase date', 'APPROVED', CURRENT_TIMESTAMP);

INSERT INTO claims (claim_id, item_id, user_id, claim_message, proof_description, claim_status, created_at)
VALUES (seq_claims.NEXTVAL, 11, 1, 'I lost my license', 'License photo matches my info', 'APPROVED', CURRENT_TIMESTAMP);

INSERT INTO claims (claim_id, item_id, user_id, claim_message, proof_description, claim_status, created_at)
VALUES (seq_claims.NEXTVAL, 18, 4, 'I found this iPad earlier', 'Can provide location and time found', 'APPROVED', CURRENT_TIMESTAMP);

INSERT INTO claims (claim_id, item_id, user_id, claim_message, proof_description, claim_status, created_at)
VALUES (seq_claims.NEXTVAL, 1, 1, 'I lost my Dell laptop', 'Serial number matches my records', 'PENDING', CURRENT_TIMESTAMP);

INSERT INTO claims (claim_id, item_id, user_id, claim_message, proof_description, claim_status, created_at)
VALUES (seq_claims.NEXTVAL, 3, 3, 'My Sony headphones', 'Original warranty papers as proof', 'REJECTED', CURRENT_TIMESTAMP);

INSERT INTO claims (claim_id, item_id, user_id, claim_message, proof_description, claim_status, created_at)
VALUES (seq_claims.NEXTVAL, 9, 9, 'I found this watch', 'Found in the gym locker room', 'PENDING', CURRENT_TIMESTAMP);

INSERT INTO claims (claim_id, item_id, user_id, claim_message, proof_description, claim_status, created_at)
VALUES (seq_claims.NEXTVAL, 13, 3, 'This is my USB drive with projects', 'I have backup records of files', 'PENDING', CURRENT_TIMESTAMP);

INSERT INTO claims (claim_id, item_id, user_id, claim_message, proof_description, claim_status, created_at)
VALUES (seq_claims.NEXTVAL, 16, 6, 'This is my green bag', 'I have receipt and can describe contents', 'APPROVED', CURRENT_TIMESTAMP);

COMMIT;
