-- FindIt: sample data (MySQL / MariaDB)
-- Run after 02_create_tables.sql and 05_triggers.sql (or after tables alone):
--   mysql -u root findit < 03_insert_sample_data.sql

USE findit;

SET FOREIGN_KEY_CHECKS = 0;
TRUNCATE TABLE audit_logs;
TRUNCATE TABLE claims;
TRUNCATE TABLE items;
TRUNCATE TABLE locations;
TRUNCATE TABLE categories;
TRUNCATE TABLE admins;
TRUNCATE TABLE users;
SET FOREIGN_KEY_CHECKS = 1;

-- Users
INSERT INTO users (name, email, password, phone, address) VALUES
('John Smith', 'john.smith@university.edu', 'pass_john123', '555-0101', '123 Main St, Campus Apt 101'),
('Sarah Johnson', 'sarah.johnson@university.edu', 'pass_sarah456', '555-0102', '456 Oak Ave, Dorm Hall B'),
('Emily Davis', 'emily.davis@university.edu', 'pass_emily789', '555-0103', '789 Elm Rd, Student Housing'),
('Michael Chen', 'michael.chen@university.edu', 'pass_michael101', '555-0104', '321 Pine Lane, Apt 205'),
('Jessica Martinez', 'jessica.martinez@university.edu', 'pass_jessica202', '555-0105', '654 Maple Dr, Campus House'),
('David Wilson', 'david.wilson@university.edu', 'pass_david303', '555-0106', '987 Cedar St, Dorm Hall A'),
('Lisa Anderson', 'lisa.anderson@university.edu', 'pass_lisa404', '555-0107', '147 Birch Ave, Student Apt'),
('Robert Taylor', 'robert.taylor@university.edu', 'pass_robert505', '555-0108', '258 Walnut Blvd, Apartment 3'),
('Amanda Brown', 'amanda.brown@university.edu', 'pass_amanda606', '555-0109', '369 Ash Lane, Campus Housing'),
('Christopher Lee', 'christopher.lee@university.edu', 'pass_christopher707', '555-0110', '741 Spruce Rd, Dorm Room 102');

-- Admins
INSERT INTO admins (name, email, password) VALUES
('Admin User One', 'admin.one@university.edu', 'admin_pass_secure_001'),
('Admin User Two', 'admin.two@university.edu', 'admin_pass_secure_002');

-- Categories
INSERT INTO categories (category_name) VALUES
('Electronics'),
('Documents'),
('Bags'),
('Keys'),
('ID Cards'),
('Accessories'),
('Clothing'),
('Other');

-- Locations (KUET Khulna — real campus spots with map pins)
INSERT INTO locations (location_name, description, latitude, longitude) VALUES
('Central Library', 'KUET Central Library', 22.9005500, 89.5029500),
('Central Cafeteria', 'Student Welfare Center cafeteria', 22.8998500, 89.5017500),
('Central Computer Center', 'Central Computer Center (CCC)', 22.9009000, 89.5015500),
('Main Gate (Fulbarigate)', 'Main campus entrance area', 22.8987000, 89.5038000),
('Student Welfare Center', 'SWC — cafeteria, indoor games, open stage', 22.8999500, 89.5012500),
('Academic Building', 'Main academic / classroom buildings', 22.9011500, 89.5024500),
('Playground', 'Central playground and sports field', 22.8985500, 89.5021500),
('Fazlul Haque Hall', 'Residential hall', 22.9018500, 89.5018500),
('Central Mosque', 'KUET Central Mosque', 22.9002500, 89.5010500),
('Administrative Building', 'Admin / registrar offices', 22.9007000, 89.5031500),
('Medical Center', 'Campus medical center', 22.8994000, 89.5025500),
('Gymnasium & Swimming Pool', 'Gym and swimming pool complex', 22.8989500, 89.5015500),
('Open Stage (Mukto Mancha)', 'Open stage at Student Welfare Center', 22.8997000, 89.5014000),
('Lalan Shah Hall', 'Residential hall', 22.9021000, 89.5023500),
('Khan Jahan Ali Hall', 'Residential hall', 22.9023500, 89.5015500),
('Dr. M.A. Rashid Hall', 'Residential hall', 22.9015500, 89.5009500),
('Rokeya Hall', 'Female residential hall', 22.9019500, 89.5035500),
('Amar Ekushey Hall', 'Residential hall', 22.9024500, 89.5030500),
('Bangabandhu Hall', 'Bangabandhu Sheikh Mujibur Rahman Hall', 22.9027000, 89.5025500),
('Shaheed Smriti Hall', 'Residential hall', 22.9012500, 89.5040500),
('New Academic Building', 'Newer academic classroom block', 22.9014500, 89.5011500),
('EEE Building', 'Electrical & Electronic Engineering dept.', 22.9008500, 89.5026500),
('CSE Building', 'Computer Science & Engineering dept.', 22.9010500, 89.5020500),
('Civil Building', 'Civil Engineering department', 22.9003500, 89.5022500),
('Mechanical Building', 'Mechanical Engineering department', 22.9001500, 89.5030500),
('Transport / Bus Stand', 'Campus transport waiting area', 22.8988500, 89.5033500);

-- Lost items
INSERT INTO items (user_id, category_id, location_id, item_name, item_description, item_type, item_image, lost_or_found_date, status) VALUES
(1, 1, 1, 'Dell Laptop', 'Silver Dell Inspiron 15 laptop, model 5570', 'LOST', 'items/dell_laptop.png', '2026-06-15', 'PENDING'),
(2, 1, 2, 'Apple iPhone', 'iPhone 13 Pro, Space Grey color', 'LOST', 'items/iphone.png', '2026-06-14', 'FOUND'),
(3, 1, 3, 'Sony Headphones', 'Sony WH-1000XM4 wireless headphones', 'LOST', 'items/headphones.png', '2026-06-13', 'PENDING'),
(4, 2, 1, 'Student ID Card', 'Campus ID with photo, plastic card', 'LOST', 'items/id_card.png', '2026-06-12', 'CLAIMED'),
(5, 3, 2, 'Black Backpack', 'Nike Black backpack with red logo', 'LOST', 'items/backpack.png', '2026-06-11', 'RETURNED'),
(6, 4, 4, 'Set of Keys', 'House keys and car key on blue keychain', 'LOST', 'items/keys.png', '2026-06-10', 'PENDING'),
(7, 2, 5, 'Class Notes Folder', 'Red folder with biology class notes', 'LOST', 'items/notes.png', '2026-06-09', 'REJECTED'),
(8, 3, 6, 'Brown Leather Wallet', 'Leather bifold wallet with business cards', 'LOST', 'items/wallet.png', '2026-06-08', 'PENDING');

-- Found items
INSERT INTO items (user_id, category_id, location_id, item_name, item_description, item_type, item_image, lost_or_found_date, status) VALUES
(9, 1, 7, 'Samsung Galaxy Watch', 'Black smartwatch found in gym locker', 'FOUND', 'items/watch.png', '2026-06-17', 'PENDING'),
(10, 6, 8, 'Blue Glasses', 'Blue-framed reading glasses in case', 'FOUND', 'items/glasses.png', '2026-06-16', 'FOUND'),
(1, 2, 1, 'Driver License', 'License found in library study area', 'FOUND', 'items/license.png', '2026-06-18', 'CLAIMED'),
(2, 7, 2, 'Red Jacket', 'Red winter jacket found in cafeteria', 'FOUND', 'items/jacket.png', '2026-06-19', 'PENDING'),
(3, 1, 3, 'USB Flash Drive', 'Kingston 32GB USB drive found in lab', 'FOUND', 'items/usb.png', '2026-06-20', 'PENDING'),
(4, 6, 5, 'Silver Ring', 'Silver band ring found in student union', 'FOUND', 'items/ring.png', '2026-06-21', 'PENDING'),
(5, 8, 4, 'Unknown Item', 'Mystery item found in parking lot', 'FOUND', 'items/unknown.png', '2026-06-21', 'PENDING'),
(6, 3, 1, 'Green Canvas Bag', 'Green canvas messenger bag', 'FOUND', 'items/canvas_bag.png', '2026-06-21', 'RETURNED'),
(7, 1, 6, 'iPad Air', 'Apple iPad Air in silver found in lab', 'FOUND', 'items/ipad.png', '2026-06-20', 'CLAIMED'),
(8, 2, 8, 'Exam Papers', 'Stack of exam papers in folder', 'FOUND', 'items/papers.png', '2026-06-19', 'PENDING');

-- Claims
INSERT INTO claims (item_id, user_id, claim_message, proof_description, claim_status) VALUES
(2, 2, 'This is my iPhone 13 Pro', 'I can provide IMEI number and Apple ID', 'PENDING'),
(4, 4, 'My student ID, I lost it at the library', 'Photo ID in wallet can verify my appearance', 'APPROVED'),
(5, 5, 'My backpack from Nike, had my books', 'I have receipt showing purchase date', 'APPROVED'),
(11, 1, 'I lost my license', 'License photo matches my info', 'APPROVED'),
(18, 4, 'I found this iPad earlier', 'Can provide location and time found', 'APPROVED'),
(1, 1, 'I lost my Dell laptop', 'Serial number matches my records', 'PENDING'),
(3, 3, 'My Sony headphones', 'Original warranty papers as proof', 'REJECTED'),
(9, 9, 'I found this watch', 'Found in the gym locker room', 'PENDING'),
(13, 3, 'This is my USB drive with projects', 'I have backup records of files', 'PENDING'),
(16, 6, 'This is my green bag', 'I have receipt and can describe contents', 'APPROVED');
