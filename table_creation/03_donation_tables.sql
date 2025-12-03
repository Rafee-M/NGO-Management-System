-- ============================================
-- File: 04_donation_tables.sql
-- Purpose: Create donation-related tables with foreign keys
-- Dependencies: Donor and Project tables must exist
-- ============================================

-- ====================== DONATION TABLE ======================
CREATE TABLE `Donation` (
  `donation_id` INT AUTO_INCREMENT,
  `donor_id` INT NOT NULL,
  `project_id` INT,
  `donation_type` VARCHAR(20) NOT NULL,
  `amount` INT NOT NULL,
  `payment_method` VARCHAR(30),
  `donation_date` DATE DEFAULT CURRENT_DATE,
  `receipt_issued` BOOLEAN DEFAULT FALSE,
  `status` VARCHAR(20) DEFAULT 'Received',
  `created_at` TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`donation_id`),
  FOREIGN KEY (`donor_id`) REFERENCES `Donor`(`donor_id`),
  FOREIGN KEY (`project_id`) REFERENCES `Project`(`project_id`)
);

-- ====================== INVENTORY TABLE ======================
CREATE TABLE `Inventory` (
  `item_id` INT AUTO_INCREMENT,
  `item_name` VARCHAR(30) NOT NULL,
  `quantity` INT NOT NULL DEFAULT 0,
  `unit` VARCHAR(10),
  `storage_location` VARCHAR(30) NOT NULL,
  `expiry_date` DATE,
  `donation_id` INT,
  `status` VARCHAR(15) DEFAULT 'In Stock',
  `created_at` TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`item_id`),
  FOREIGN KEY (`donation_id`) REFERENCES `Donation`(`donation_id`)
);
