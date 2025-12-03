-- ============================================
-- File: 02_core_tables.sql
-- Purpose: Create core independent tables
-- Execution Order: Run second
-- Dependencies: None (independent tables first) as no foreign keys
-- ============================================

-- ====================== DONOR TABLE ======================
CREATE TABLE `Donor` (
  `donor_id` INT AUTO_INCREMENT,
  `first_name` VARCHAR(30) NOT NULL,
  `last_name` VARCHAR(30) NOT NULL,
  `dob` DATE,
  `gender` VARCHAR(10),
  `email` VARCHAR(30) NOT NULL,
  `phone` VARCHAR(16) NOT NULL,
  `address` VARCHAR(50),
  `city` VARCHAR(20),
  `zip_code` VARCHAR(8),
  `occupation` VARCHAR(40),
  `nid` INT,
  `donor_since` DATE DEFAULT CURRENT_DATE,
  `status` VARCHAR(20) DEFAULT 'Active',
  `created_at` TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`donor_id`),
  UNIQUE KEY (`email`),
  UNIQUE KEY (`phone`)
);

-- ====================== STAFF TABLE ======================
CREATE TABLE `Staff` (
  `staff_id` INT AUTO_INCREMENT,
  `first_name` VARCHAR(30) NOT NULL,
  `last_name` VARCHAR(30) NOT NULL,
  `dob` DATE,
  `gender` VARCHAR(10),
  `email` VARCHAR(30) NOT NULL,
  `phone` VARCHAR(16) NOT NULL,
  `address` VARCHAR(50),
  `city` VARCHAR(20),
  `zip_code` VARCHAR(8),
  `position` VARCHAR(40),
  `nid` INT,
  `salary` INT,
  `supervisor_id` INT,
  `hire_date` DATE,
  `status` VARCHAR(20) DEFAULT 'Active',
  `created_at` TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`staff_id`),
  UNIQUE KEY (`email`),
  UNIQUE KEY (`phone`),
  FOREIGN KEY (`supervisor_id`) REFERENCES `Staff`(`staff_id`)
);

-- ====================== PROJECT TABLE ======================
-- Note: project_manager foreign key will be added in file 03
CREATE TABLE `Project` (
  `project_id` INT AUTO_INCREMENT,
  `project_name` VARCHAR(30) NOT NULL,
  `description` TEXT NOT NULL,
  `project_type` VARCHAR(50) NOT NULL,
  `start_date` DATE NOT NULL,
  `estimated_end_date` DATE,
  `actual_end_date` DATE,
  `budget` INT NOT NULL,
  `beneficiary_count` INT NOT NULL DEFAULT 0,
  `status` VARCHAR(15) NOT NULL DEFAULT 'Planning',
  `project_manager` INT,
  `created_at` TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`project_id`)
);

-- ====================== VOLUNTEER TABLE ======================
CREATE TABLE `Volunteer` (
  `volunteer_id` INT AUTO_INCREMENT,
  `first_name` VARCHAR(30) NOT NULL,
  `last_name` VARCHAR(30) NOT NULL,
  `dob` DATE,
  `gender` VARCHAR(10),
  `email` VARCHAR(30) NOT NULL,
  `phone` VARCHAR(16) NOT NULL,
  `address` VARCHAR(50),
  `city` VARCHAR(20),
  `zip_code` VARCHAR(8),
  `occupation` VARCHAR(40),
  `nid` INT,
  `emergency_contact` VARCHAR(16),
  `date_joined` DATE DEFAULT CURRENT_DATE,
  `total_hours` INT DEFAULT 0,
  `status` VARCHAR(20) DEFAULT 'Active',
  `created_at` TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`volunteer_id`),
  UNIQUE KEY (`email`),
  UNIQUE KEY (`phone`)
);

-- ====================== BENEFICIARY TABLE ======================
CREATE TABLE `Beneficiary` (
  `beneficiary_id` INT AUTO_INCREMENT,
  `first_name` VARCHAR(30) NOT NULL,
  `last_name` VARCHAR(30) NOT NULL,
  `dob` DATE,
  `gender` VARCHAR(10),
  `email` VARCHAR(30),
  `phone` VARCHAR(16),
  `address` VARCHAR(50) NOT NULL,
  `city` VARCHAR(20) NOT NULL,
  `zip_code` VARCHAR(8) NOT NULL,
  `occupation` VARCHAR(40),
  `nid` INT,
  `family_size` INT DEFAULT 1,
  `income` INT,
  `special_needs` TEXT,
  `status` VARCHAR(20) DEFAULT 'Active',
  `created_at` TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`beneficiary_id`)
);

-- Add foreign key from Project to Staff (project_manager)
ALTER TABLE `Project`
ADD FOREIGN KEY (`project_manager`) REFERENCES `Staff`(`staff_id`);