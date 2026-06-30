CREATE DATABASE IF NOT EXISTS nishchal CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;
USE nishchal;

CREATE TABLE IF NOT EXISTS classes (
  id INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
  name VARCHAR(190) NOT NULL,
  description TEXT NULL,
  start_date DATE NULL,
  price DECIMAL(10,2) NULL,
  instructor VARCHAR(190) NULL,
  image_path VARCHAR(255) NULL,
  created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
  updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
);

-- Add booking related columns if not present (safe on re-run)
ALTER TABLE classes
  ADD COLUMN IF NOT EXISTS booking_enabled TINYINT(1) DEFAULT 1,
  ADD COLUMN IF NOT EXISTS slot_capacity INT NULL,
  ADD COLUMN IF NOT EXISTS timeslots TEXT NULL,
  ADD COLUMN IF NOT EXISTS tiers TEXT NULL,
  ADD COLUMN IF NOT EXISTS booking_type VARCHAR(50) NULL DEFAULT 'Group';

CREATE TABLE IF NOT EXISTS events (
  id INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
  title VARCHAR(190) NOT NULL,
  event_date DATE NOT NULL,
  event_time TIME NULL,
  location VARCHAR(190) NULL,
  description TEXT NULL,
  image_path VARCHAR(255) NULL,
  created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
  updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
);

CREATE TABLE IF NOT EXISTS gallery (
  id INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
  title VARCHAR(190) NOT NULL,
  category VARCHAR(100) NULL,
  description TEXT NULL,
  image_path VARCHAR(255) NOT NULL,
  created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
  updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
);

CREATE TABLE IF NOT EXISTS blogs (
  id INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
  title VARCHAR(190) NOT NULL,
  author VARCHAR(190) NOT NULL,
  category VARCHAR(120) NOT NULL,
  publish_date DATE NOT NULL,
  content LONGTEXT NOT NULL,
  image_path VARCHAR(255) NULL,
  created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
  updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
);

CREATE TABLE IF NOT EXISTS bookings (
  id INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
  class_id INT UNSIGNED NOT NULL,
  customer_name VARCHAR(190) NOT NULL,
  customer_email VARCHAR(190) NOT NULL,
  customer_phone VARCHAR(50) NULL,
  program_tier VARCHAR(100) NULL,
  booking_date DATE NOT NULL,
  time_slot VARCHAR(50) NOT NULL,
  quantity INT NOT NULL DEFAULT 1,
  personal_or_group VARCHAR(20) DEFAULT 'personal',
  payment_status VARCHAR(50) DEFAULT 'pending',
  transaction_id VARCHAR(255) NULL,
  created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
  updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  FOREIGN KEY (class_id) REFERENCES classes(id) ON DELETE CASCADE
);

CREATE TABLE IF NOT EXISTS enquiries (
  id INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
  name VARCHAR(190) NOT NULL,
  email VARCHAR(190) NOT NULL,
  phone VARCHAR(50) NULL,
  subject VARCHAR(255) NOT NULL,
  message TEXT NOT NULL,
  status VARCHAR(50) DEFAULT 'New',
  created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
  updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
);


CREATE TABLE IF NOT EXISTS instructors (
  id INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
  name VARCHAR(190) NOT NULL,
  bio TEXT NULL,
  specialty VARCHAR(190) NULL,
  image_path VARCHAR(255) NULL,
  created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
  updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
);

-- ======================================================
-- Extended class fields for dynamic frontend card rendering
-- ======================================================
ALTER TABLE classes
  ADD COLUMN IF NOT EXISTS category VARCHAR(50) NULL DEFAULT 'in-person' COMMENT 'Filter category: in-person|online|retreats|sacred-trails',
  ADD COLUMN IF NOT EXISTS location VARCHAR(50) NULL DEFAULT 'none' COMMENT 'Location section: none|mangaluru|udupi',
  ADD COLUMN IF NOT EXISTS highlights TEXT NULL COMMENT 'One benefit per line (shown as bullet points)',
  ADD COLUMN IF NOT EXISTS upcoming_batches TEXT NULL COMMENT 'JSON array: [{date,time,price,note}]',
  ADD COLUMN IF NOT EXISTS video_url VARCHAR(255) NULL COMMENT 'YouTube or external video URL',
  ADD COLUMN IF NOT EXISTS badge_label VARCHAR(100) NULL COMMENT 'Small badge label on card image e.g. Hatha Yoga',
  ADD COLUMN IF NOT EXISTS registration_required TINYINT(1) NOT NULL DEFAULT 0 COMMENT '1 = show Registration Mandatory badge',
  ADD COLUMN IF NOT EXISTS age_requirement VARCHAR(50) NULL DEFAULT '14+ years' COMMENT 'e.g. 14+ years',
  ADD COLUMN IF NOT EXISTS special_instruction TEXT NULL COMMENT 'Italic note shown at card bottom e.g. No prior experience required',
  ADD COLUMN IF NOT EXISTS schedule TEXT NULL COMMENT 'Free-text schedule description',
  ADD COLUMN IF NOT EXISTS price_note VARCHAR(255) NULL COMMENT 'e.g. Incl. Jala Neti Pot · No prior exp. needed';

