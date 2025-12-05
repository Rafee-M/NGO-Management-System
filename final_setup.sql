-- NGO Donation and Volunteer Management System
-- Created by: [Your Name]
-- Date: [Current Date]
-- Description: Complete database schema for NGO management with triggers, procedures, and views

-- ==================== DATABASE CREATION ====================
CREATE DATABASE IF NOT EXISTS ngo_management;
USE ngo_management;

-- ==================== CORE TABLES ====================

-- Beneficiaries: Individuals receiving aid from NGO
CREATE TABLE `Beneficiary` (
  `beneficiary_id` INT AUTO_INCREMENT,
  `first_name` VARCHAR(30) NOT NULL,
  `last_name` VARCHAR(30) NOT NULL,
  `dob` DATE,
  `gender` VARCHAR(10),
  `email` VARCHAR(30),
  `phone` VARCHAR(16) NOT NULL,
  `address` VARCHAR(50),
  `city` VARCHAR(20) NOT NULL,
  `occupation` VARCHAR(40),
  `nid` INT,
  `family_size` INT DEFAULT 1,
  `income` INT,
  `special_needs` TEXT,
  `status` VARCHAR(20) DEFAULT 'Active',
  PRIMARY KEY (`beneficiary_id`),
  
  -- Indexes for faster queries
  INDEX `idx_city` (`city`),
  INDEX `idx_status` (`status`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- Projects: NGO initiatives and programs
CREATE TABLE `Project` (
  `project_id` INT AUTO_INCREMENT,
  `project_name` VARCHAR(30) NOT NULL,
  `description` TEXT NOT NULL,
  `start_date` DATE NOT NULL,
  `end_date` DATE,
  `allocated_budget` INT DEFAULT 0,
  `spent_budget` INT DEFAULT 0,
  `status` VARCHAR(15) NOT NULL DEFAULT 'Planning',
  `project_manager` INT,
  PRIMARY KEY (`project_id`),
  
  -- Indexes
  INDEX `idx_status` (`status`),
  INDEX `idx_start_date` (`start_date`),
  
  -- Calculated column for remaining budget
  CONSTRAINT `chk_budget` CHECK (`spent_budget` <= `allocated_budget`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- Goods Inventory: Physical items donated and stored
CREATE TABLE `GoodsInventory` (
  `item_id` INT AUTO_INCREMENT,
  `item_name` VARCHAR(30) NOT NULL,
  `available_quantity` INT NOT NULL DEFAULT 0,
  `storage_location` VARCHAR(30) NOT NULL,
  `description` TEXT,
  `donation_id` INT,
  `status` VARCHAR(15) DEFAULT 'In Stock',
  PRIMARY KEY (`item_id`),
  
  -- Indexes
  INDEX `idx_status` (`status`),
  INDEX `idx_storage` (`storage_location`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- Beneficiary Allocations: Records of aid distribution to beneficiaries
CREATE TABLE `Beneficiary_Allocation` (
  `allocation_id` INT AUTO_INCREMENT,
  `project_id` INT NOT NULL,
  `beneficiary_id` INT NOT NULL,
  `allocation_type` ENUM('Cash', 'Goods') NOT NULL, 
  `item_id` INT,  -- NULL for cash allocations
  `quantity` INT,  -- For goods, NULL for cash
  `cash_amount` INT,  -- For cash, NULL for goods
  `allocation_date` DATE DEFAULT (CURRENT_DATE),
  `status` VARCHAR(20) DEFAULT 'Pending',
  PRIMARY KEY (`allocation_id`),
  
  -- Foreign Keys with proper referential integrity
  FOREIGN KEY (`project_id`) REFERENCES `Project`(`project_id`) 
    ON DELETE CASCADE 
    ON UPDATE CASCADE,
  FOREIGN KEY (`beneficiary_id`) REFERENCES `Beneficiary`(`beneficiary_id`) 
    ON DELETE CASCADE 
    ON UPDATE CASCADE,
  FOREIGN KEY (`item_id`) REFERENCES `GoodsInventory`(`item_id`) 
    ON DELETE SET NULL 
    ON UPDATE CASCADE,
  
  -- Business Logic Constraints
  CONSTRAINT `chk_allocation_type` CHECK (
    (allocation_type = 'Goods' AND item_id IS NOT NULL AND quantity IS NOT NULL AND cash_amount IS NULL) OR
    (allocation_type = 'Cash' AND cash_amount IS NOT NULL AND item_id IS NULL AND quantity IS NULL)
  ),
  CONSTRAINT `chk_positive_values` CHECK (
    (allocation_type = 'Cash' AND cash_amount > 0) OR
    (allocation_type = 'Goods' AND quantity > 0)
  ),
  
  -- Indexes for performance
  INDEX `idx_project` (`project_id`),
  INDEX `idx_beneficiary` (`beneficiary_id`),
  INDEX `idx_allocation_date` (`allocation_date`),
  INDEX `idx_status` (`status`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- Events: NGO-organized activities
CREATE TABLE `Event` (
  `event_id` INT AUTO_INCREMENT,
  `event_name` VARCHAR(30) NOT NULL,
  `description` TEXT NOT NULL,
  `event_type` VARCHAR(50) NOT NULL,
  `date` DATE NOT NULL,
  `start_time` TIME NOT NULL,
  `end_time` TIME NOT NULL,
  `location` VARCHAR(40) NOT NULL,
  `cost` INT NOT NULL DEFAULT 0,
  `status` ENUM('Scheduled', 'Completed', 'Cancelled') DEFAULT 'Scheduled',
  `coordinator` INT NOT NULL,
  `project_id` INT NOT NULL,
  PRIMARY KEY (`event_id`),
  FOREIGN KEY (`project_id`) REFERENCES `Project`(`project_id`) 
    ON DELETE CASCADE 
    ON UPDATE CASCADE,
  
  -- Indexes
  INDEX `idx_project` (`project_id`),
  INDEX `idx_date` (`date`),
  INDEX `idx_status` (`status`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- Budget Allocations: Money assigned from cash pool to projects
CREATE TABLE `Budget_Allocation` (
  `allocation_id` INT AUTO_INCREMENT,
  `project_id` INT NOT NULL,
  `amount` INT NOT NULL,
  `allocation_date` DATE DEFAULT (CURRENT_DATE),
  `description` TEXT,
  PRIMARY KEY (`allocation_id`),
  FOREIGN KEY (`project_id`) REFERENCES `Project`(`project_id`) 
    ON DELETE CASCADE 
    ON UPDATE CASCADE,
  CONSTRAINT `chk_positive_amount` CHECK (`amount` > 0),
  
  -- Indexes
  INDEX `idx_project` (`project_id`),
  INDEX `idx_date` (`allocation_date`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- Donations: Records of all donations (money and goods)
CREATE TABLE `Donation` (
  `donation_id` INT AUTO_INCREMENT,
  `donor_id` INT NOT NULL,
  `donation_type` ENUM('Money', 'Goods') NOT NULL,
  `amount` INT NOT NULL,
  `description` TEXT,
  `payment_method` VARCHAR(30),
  `item_name` VARCHAR(50),  -- For goods
  `quantity` INT,           -- For goods
  `donation_date` DATE DEFAULT (CURRENT_DATE),
  PRIMARY KEY (`donation_id`),
  
  -- Indexes
  INDEX `idx_donor` (`donor_id`),
  INDEX `idx_type` (`donation_type`),
  INDEX `idx_date` (`donation_date`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- Cash Inventory: Tracks cash pool and budget allocations
CREATE TABLE `CashInventory` (
  `transaction_id` INT AUTO_INCREMENT,
  `amount` INT NOT NULL,
  `transaction_type` ENUM('Donation', 'BudgetAllocation', 'Adjustment') NOT NULL,
  `donation_id` INT,
  `project_id` INT,  -- For budget allocations
  `transaction_date` DATE DEFAULT (CURRENT_DATE),
  `description` VARCHAR(255),
  PRIMARY KEY (`transaction_id`),
  FOREIGN KEY (`donation_id`) REFERENCES `Donation`(`donation_id`) 
    ON DELETE CASCADE 
    ON UPDATE CASCADE,
  FOREIGN KEY (`project_id`) REFERENCES `Project`(`project_id`) 
    ON DELETE CASCADE 
    ON UPDATE CASCADE,
  CONSTRAINT `chk_amount_not_zero` CHECK (`amount` != 0),
  
  -- Indexes
  INDEX `idx_donation` (`donation_id`),
  INDEX `idx_project` (`project_id`),
  INDEX `idx_date` (`transaction_date`),
  INDEX `idx_type` (`transaction_type`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- Staff: NGO employees
CREATE TABLE `Staff` (
  `staff_id` INT AUTO_INCREMENT,
  `first_name` VARCHAR(30) NOT NULL,
  `last_name` VARCHAR(30) NOT NULL,
  `email` VARCHAR(30) NOT NULL UNIQUE,
  `nid` INT NOT NULL UNIQUE,
  `address` VARCHAR(50) NOT NULL,
  `dob` DATE NOT NULL,
  `gender` VARCHAR(10),
  `phone` VARCHAR(16) NOT NULL,
  `position` VARCHAR(40) NOT NULL,
  `status` ENUM('Active', 'Fired', 'Quit') DEFAULT 'Active',
  `supervisor_id` INT,
  `hire_date` DATE,
  PRIMARY KEY (`staff_id`),
  FOREIGN KEY (`supervisor_id`) REFERENCES `Staff`(`staff_id`) 
    ON DELETE SET NULL 
    ON UPDATE CASCADE,
  
  -- Indexes
  INDEX `idx_status` (`status`),
  INDEX `idx_supervisor` (`supervisor_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- Donors: Individuals/organizations donating to NGO
CREATE TABLE `Donor` (
  `donor_id` INT AUTO_INCREMENT,
  `first_name` VARCHAR(30),
  `last_name` VARCHAR(30),
  `email` VARCHAR(30) UNIQUE,
  `phone` VARCHAR(16),
  `address` VARCHAR(40),
  `city` VARCHAR(25),
  PRIMARY KEY (`donor_id`),
  
  -- Indexes
  INDEX `idx_city` (`city`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- Volunteers: Individuals volunteering for NGO
CREATE TABLE `Volunteer` (
  `volunteer_id` INT AUTO_INCREMENT,
  `first_name` VARCHAR(30) NOT NULL,
  `last_name` VARCHAR(30) NOT NULL,
  `dob` DATE,
  `email` VARCHAR(30) NOT NULL UNIQUE,
  `phone` VARCHAR(16) NOT NULL,
  `address` VARCHAR(50),
  `city` VARCHAR(20),
  `nid` INT UNIQUE,
  `date_joined` DATE DEFAULT (CURRENT_DATE),
  `status` ENUM('Current', 'On-Leave', 'Quit') DEFAULT 'Current',
  PRIMARY KEY (`volunteer_id`),
  
  -- Indexes
  INDEX `idx_status` (`status`),
  INDEX `idx_join_date` (`date_joined`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- Volunteer Assignments: Links volunteers to projects
CREATE TABLE `Volunteer_Project` (
  `assignment_id` INT AUTO_INCREMENT,
  `volunteer_id` INT NOT NULL,
  `project_id` INT NOT NULL,
  `role` VARCHAR(30) NOT NULL,
  `hours` INT NOT NULL,
  `start_date` DATE NOT NULL,
  `end_date` DATE,
  `status` VARCHAR(20) DEFAULT 'Active',
  PRIMARY KEY (`assignment_id`),
  FOREIGN KEY (`volunteer_id`) REFERENCES `Volunteer`(`volunteer_id`) 
    ON DELETE CASCADE 
    ON UPDATE CASCADE,
  FOREIGN KEY (`project_id`) REFERENCES `Project`(`project_id`) 
    ON DELETE CASCADE 
    ON UPDATE CASCADE,
  
  -- Indexes
  INDEX `idx_volunteer` (`volunteer_id`),
  INDEX `idx_project` (`project_id`),
  INDEX `idx_status` (`status`),
  INDEX `idx_start_date` (`start_date`),
  
  -- Validation
  CONSTRAINT `chk_hours_positive` CHECK (`hours` >= 0)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- ==================== ADD MISSING FOREIGN KEYS ====================

-- Project manager must be a staff member
ALTER TABLE `Project` 
ADD FOREIGN KEY (`project_manager`) REFERENCES `Staff`(`staff_id`)
  ON DELETE SET NULL 
  ON UPDATE CASCADE;

-- Event coordinator must be a staff member
ALTER TABLE `Event`
ADD FOREIGN KEY (`coordinator`) REFERENCES `Staff`(`staff_id`)
  ON DELETE RESTRICT 
  ON UPDATE CASCADE;

-- Donation must have a valid donor
ALTER TABLE `Donation`
ADD FOREIGN KEY (`donor_id`) REFERENCES `Donor`(`donor_id`)
  ON DELETE CASCADE 
  ON UPDATE CASCADE;

-- Goods must come from a donation
ALTER TABLE `GoodsInventory`
ADD FOREIGN KEY (`donation_id`) REFERENCES `Donation`(`donation_id`)
  ON DELETE CASCADE 
  ON UPDATE CASCADE;

-- ==================== VIEWS ====================

-- Current Cash Balance: Shows available cash in pool
CREATE VIEW `CurrentCashBalance` AS
SELECT COALESCE(SUM(amount), 0) as available_balance
FROM CashInventory;

-- Volunteer Total Hours: Aggregates hours per volunteer
CREATE VIEW `VolunteerTotalHours` AS
SELECT 
    v.volunteer_id,
    v.first_name,
    v.last_name,
    COALESCE(SUM(vp.hours), 0) as total_hours,
    COUNT(vp.assignment_id) as total_assignments
FROM Volunteer v
LEFT JOIN Volunteer_Project vp ON v.volunteer_id = vp.volunteer_id
GROUP BY v.volunteer_id, v.first_name, v.last_name;

-- Project Financial Summary: Overview of project finances
CREATE VIEW `ProjectFinancialSummary` AS
SELECT 
    p.project_id,
    p.project_name,
    p.allocated_budget,
    p.spent_budget,
    (p.allocated_budget - p.spent_budget) as remaining_budget,
    ROUND((p.spent_budget / NULLIF(p.allocated_budget, 0)) * 100, 2) as utilization_percentage,
    p.status
FROM Project p;

-- ==================== TRIGGERS ====================

-- Trigger 1: Automatically add money donations to CashInventory
DELIMITER $$

CREATE TRIGGER `after_money_donation`
AFTER INSERT ON `Donation`
FOR EACH ROW
BEGIN
    IF NEW.donation_type = 'Money' THEN
        INSERT INTO CashInventory (
            amount,
            transaction_type,
            donation_id,
            description,
            transaction_date
        ) VALUES (
            NEW.amount,
            'Donation',
            NEW.donation_id,
            CONCAT('Money donation from Donor #', NEW.donor_id),
            NEW.donation_date
        );
    END IF;
END$$

DELIMITER ;

-- Trigger 2: Automatically add goods donations to GoodsInventory
DELIMITER $$

CREATE TRIGGER `after_goods_donation`
AFTER INSERT ON `Donation`
FOR EACH ROW
BEGIN
    IF NEW.donation_type = 'Goods' THEN
        INSERT INTO GoodsInventory (
            item_name,
            available_quantity,
            donation_id,
            description,
            storage_location
        ) VALUES (
            NEW.item_name,
            NEW.quantity,  -- Initially all available
            NEW.donation_id,
            NEW.description,
            'Main Warehouse'  -- Default storage location
        );
    END IF;
END$$

DELIMITER ;