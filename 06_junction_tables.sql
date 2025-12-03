-- ============================================
-- File: 06_junction_tables.sql
-- Purpose: Create junction tables for many-to-many relationships
-- Dependencies: All referenced tables must exist
-- ============================================

-- ====================== JUNCTION TABLES ======================

-- Volunteer-Project Assignment (Many-to-Many)
CREATE TABLE `Volunteer_Project` (
  `assignment_id` INT AUTO_INCREMENT,
  `volunteer_id` INT NOT NULL,
  `project_id` INT NOT NULL,
  `role` VARCHAR(30) NOT NULL,
  `total_hours` INT NOT NULL DEFAULT 0,
  `start_date` DATE NOT NULL,
  `end_date` DATE,
  `feedback` TEXT,
  `status` VARCHAR(20) DEFAULT 'Active',
  `created_at` TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`assignment_id`),
  UNIQUE KEY `unique_volunteer_project` (`volunteer_id`, `project_id`),
  FOREIGN KEY (`volunteer_id`) REFERENCES `Volunteer`(`volunteer_id`),
  FOREIGN KEY (`project_id`) REFERENCES `Project`(`project_id`)
);

-- Volunteer-Event Assignment (Many-to-Many)
CREATE TABLE `Volunteer_Event` (
  `volunteer_id` INT NOT NULL,
  `event_id` INT NOT NULL,
  `assignment_date` DATE DEFAULT CURRENT_DATE,
  `hours_volunteered` INT NOT NULL DEFAULT 0,
  `feedback` TEXT,
  `status` VARCHAR(20) DEFAULT 'Confirmed',
  `created_at` TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`volunteer_id`, `event_id`),
  FOREIGN KEY (`volunteer_id`) REFERENCES `Volunteer`(`volunteer_id`),
  FOREIGN KEY (`event_id`) REFERENCES `Event`(`event_id`)
);

-- Beneficiary-Project Allocation (Many-to-Many)
CREATE TABLE `Beneficiary_Allocation` (
  `allocation_id` INT AUTO_INCREMENT,
  `project_id` INT NOT NULL,
  `beneficiary_id` INT NOT NULL,
  `item_type` VARCHAR(20),
  `amount` INT NOT NULL DEFAULT 0,
  `allocation_date` DATE DEFAULT CURRENT_DATE,
  `status` VARCHAR(20) DEFAULT 'Allocated',
  `created_at` TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`allocation_id`),
  UNIQUE KEY `unique_beneficiary_project` (`project_id`, `beneficiary_id`, `allocation_date`),
  FOREIGN KEY (`project_id`) REFERENCES `Project`(`project_id`),
  FOREIGN KEY (`beneficiary_id`) REFERENCES `Beneficiary`(`beneficiary_id`)
);