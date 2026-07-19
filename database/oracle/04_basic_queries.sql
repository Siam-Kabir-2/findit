-- FindIt: basic queries

-- All users
SELECT * FROM users ORDER BY user_id;

-- All categories
SELECT * FROM categories ORDER BY category_id;

-- All locations
SELECT * FROM locations ORDER BY location_id;

-- Items with user, category, location
SELECT
  i.item_id, u.name AS reporter_name, i.item_name, c.category_name,
  l.location_name, i.item_type, i.status, i.lost_or_found_date
FROM items i
JOIN users u ON i.user_id = u.user_id
JOIN categories c ON i.category_id = c.category_id
JOIN locations l ON i.location_id = l.location_id
ORDER BY i.item_id;

-- Search by item name
SELECT item_id, item_name, item_type, status, lost_or_found_date
FROM items
WHERE LOWER(item_name) LIKE LOWER('%phone%')
ORDER BY item_id;

-- Lost items
SELECT i.item_id, u.name AS reported_by, i.item_name, c.category_name, i.status, i.lost_or_found_date
FROM items i
JOIN users u ON i.user_id = u.user_id
JOIN categories c ON i.category_id = c.category_id
WHERE i.item_type = 'LOST'
ORDER BY i.lost_or_found_date DESC;

-- Found items
SELECT i.item_id, u.name AS reported_by, i.item_name, c.category_name, i.status, i.lost_or_found_date
FROM items i
JOIN users u ON i.user_id = u.user_id
JOIN categories c ON i.category_id = c.category_id
WHERE i.item_type = 'FOUND'
ORDER BY i.lost_or_found_date DESC;

-- Filter by category
SELECT i.item_id, i.item_name, c.category_name, i.item_type, i.status
FROM items i
JOIN categories c ON i.category_id = c.category_id
WHERE c.category_name = 'Electronics'
ORDER BY i.item_id;

-- Filter by location
SELECT i.item_id, i.item_name, i.item_type, i.status, l.location_name, l.description
FROM items i
JOIN locations l ON i.location_id = l.location_id
WHERE l.location_name = 'Main Library'
ORDER BY i.item_id;

-- Pending claims
SELECT c.claim_id, c.claim_message, c.proof_description, c.claim_status, c.created_at
FROM claims c
WHERE c.claim_status = 'PENDING'
ORDER BY c.created_at DESC;

-- Approved claims
SELECT c.claim_id, u.name AS claimant_name, i.item_name, c.proof_description, c.claim_status, c.created_at
FROM claims c
JOIN users u ON c.user_id = u.user_id
JOIN items i ON c.item_id = i.item_id
WHERE c.claim_status = 'APPROVED'
ORDER BY c.created_at DESC;

-- Claims with user and item
SELECT c.claim_id, u.name AS claimant_name, u.email, i.item_id, i.item_name,
  c.claim_message, c.proof_description, c.claim_status, c.created_at
FROM claims c
JOIN users u ON c.user_id = u.user_id
JOIN items i ON c.item_id = i.item_id
ORDER BY c.created_at DESC;

-- Count users
SELECT COUNT(*) AS total_users FROM users;

-- Count items
SELECT COUNT(*) AS total_items FROM items;

-- Items by status
SELECT status, COUNT(*) AS item_count
FROM items
GROUP BY status
ORDER BY status;

-- Claims by status
SELECT claim_status, COUNT(*) AS claim_count
FROM claims
GROUP BY claim_status
ORDER BY claim_status;
