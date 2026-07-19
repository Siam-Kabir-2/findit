-- FindIt: create MySQL/MariaDB database (XAMPP)
-- Run as root (default XAMPP has empty password):
--   mysql -u root < 01_create_database.sql

CREATE DATABASE IF NOT EXISTS findit
  CHARACTER SET utf8mb4
  COLLATE utf8mb4_unicode_ci;

USE findit;
