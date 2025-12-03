-- ============================================
-- File: 05_event_table.sql
-- Purpose: Create Event table with foreign keys
-- Dependencies: Project and Staff table
-- ============================================

-- ====================== EVENT TABLE ======================
CREATE TABLE `Event` (
  `event_id` INT AUTO_INCREMENT,
  `event_name` VARCHAR(30) NOT NULL,
  `description` TEXT NOT NULL,
  `event_type` VARCHAR(50) NOT NULL,
  `date` DATE NOT NULL,
  `start_time` TIME NOT NULL,
  `end_time` TIME NOT NULL,
  `location` VARCHAR(40) NOT NULL,
  `cost` INT DEFAULT 0,
  `status` VARCHAR(15) DEFAULT 'Scheduled',
  `coordinator` INT NOT NULL,
  `project_id` INT,
  `created_at` TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`event_id`),
  FOREIGN KEY (`project_id`) REFERENCES `Project`(`project_id`),
  FOREIGN KEY (`coordinator`) REFERENCES `Staff`(`staff_id`)
);