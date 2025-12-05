-- Drop database if exists and create new
DROP DATABASE IF EXISTS ngo_management;
CREATE DATABASE ngo_management;
USE ngo_management;

-- Staff table
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
);

-- Donor table
CREATE TABLE `Donor` (
  `donor_id` INT AUTO_INCREMENT,
  `first_name` VARCHAR(30),
  `last_name` VARCHAR(30),
  `email` VARCHAR(30) UNIQUE,
  `phone` VARCHAR(16),
  `address` VARCHAR(40),
  `city` VARCHAR(25),
  PRIMARY KEY (`donor_id`)
);

-- Project table
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
  FOREIGN KEY (`project_manager`) REFERENCES `Staff`(`staff_id`)
);

-- Donation table
CREATE TABLE `Donation` (
  `donation_id` INT AUTO_INCREMENT,
  `donor_id` INT NOT NULL,
  `donation_type` ENUM('Money', 'Goods') NOT NULL,
  `amount` INT NOT NULL,
  `description` TEXT,
  `payment_method` VARCHAR(30),
  `item_name` VARCHAR(50),
  `quantity` INT,
  `donation_date` DATE DEFAULT (CURRENT_DATE),
  PRIMARY KEY (`donation_id`),
  FOREIGN KEY (`donor_id`) REFERENCES `Donor`(`donor_id`)
);

-- CashInventory table
CREATE TABLE `CashInventory` (
  `transaction_id` INT AUTO_INCREMENT,
  `amount` INT NOT NULL,
  `transaction_type` ENUM('Donation', 'BudgetAllocation', 'Adjustment') NOT NULL,
  `donation_id` INT,
  `project_id` INT,
  `transaction_date` DATE DEFAULT (CURRENT_DATE),
  `description` VARCHAR(255),
  PRIMARY KEY (`transaction_id`),
  FOREIGN KEY (`donation_id`) REFERENCES `Donation`(`donation_id`),
  FOREIGN KEY (`project_id`) REFERENCES `Project`(`project_id`)
);

-- GoodsInventory table
CREATE TABLE `GoodsInventory` (
  `item_id` INT AUTO_INCREMENT,
  `item_name` VARCHAR(30) NOT NULL,
  `quantity` INT NOT NULL DEFAULT 0,
  `available_quantity` INT NOT NULL DEFAULT 0,
  `storage_location` VARCHAR(30) NOT NULL,
  `description` TEXT,
  `donation_id` INT,
  `status` VARCHAR(15) DEFAULT 'In Stock',
  PRIMARY KEY (`item_id`),
  FOREIGN KEY (`donation_id`) REFERENCES `Donation`(`donation_id`)
);

-- Beneficiary table
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
  PRIMARY KEY (`beneficiary_id`)
);

-- Beneficiary_Allocation table
CREATE TABLE `Beneficiary_Allocation` (
  `allocation_id` INT AUTO_INCREMENT,
  `project_id` INT NOT NULL,
  `beneficiary_id` INT NOT NULL,
  `allocation_type` ENUM('Cash', 'Goods') NOT NULL, 
  `item_id` INT,
  `quantity` INT,
  `cash_amount` INT,
  `allocation_date` DATE DEFAULT (CURRENT_DATE),
  `status` VARCHAR(20) DEFAULT 'Pending',
  PRIMARY KEY (`allocation_id`),
  FOREIGN KEY (`project_id`) REFERENCES `Project`(`project_id`),
  FOREIGN KEY (`beneficiary_id`) REFERENCES `Beneficiary`(`beneficiary_id`),
  FOREIGN KEY (`item_id`) REFERENCES `GoodsInventory`(`item_id`)
);

-- Volunteer table
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
  PRIMARY KEY (`volunteer_id`)
);

-- Volunteer_Project table
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
  FOREIGN KEY (`volunteer_id`) REFERENCES `Volunteer`(`volunteer_id`),
  FOREIGN KEY (`project_id`) REFERENCES `Project`(`project_id`)
);

-- Event table
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
  FOREIGN KEY (`project_id`) REFERENCES `Project`(`project_id`),
  FOREIGN KEY (`coordinator`) REFERENCES `Staff`(`staff_id`)
);

-- Budget_Allocation table
CREATE TABLE `Budget_Allocation` (
  `allocation_id` INT AUTO_INCREMENT,
  `project_id` INT NOT NULL,
  `amount` INT NOT NULL,
  `allocation_date` DATE DEFAULT (CURRENT_DATE),
  `description` TEXT,
  PRIMARY KEY (`allocation_id`),
  FOREIGN KEY (`project_id`) REFERENCES `Project`(`project_id`)
);

-- Insert sample data
-- Staff
INSERT INTO `Staff` VALUES 
(1, 'John', 'Doe', 'ceo@ngo.org', 100001, '123 CEO Street', '1975-05-15', 'Male', '555-0101', 'CEO', 'Active', NULL, '2020-01-15'),
(2, 'Jane', 'Smith', 'manager@ngo.org', 100002, '456 Manager Ave', '1985-08-22', 'Female', '555-0102', 'Manager', 'Active', 1, '2021-03-10'),
(3, 'Bob', 'Johnson', 'employee@ngo.org', 100003, '789 Employee Rd', '1990-11-30', 'Male', '555-0103', 'Field Officer', 'Active', 2, '2022-06-01');

-- Donors
INSERT INTO `Donor` VALUES 
(1, 'Michael', 'Johnson', 'michael@email.com', '555-0201', '789 Pine Rd', 'New York'),
(2, 'Sarah', 'Williams', 'sarah@email.com', '555-0202', '321 Maple Dr', 'Chicago'),
(3, 'Tech', 'Corp', 'tech@corp.com', '555-0203', '555 Tech Blvd', 'Silicon Valley');

-- Projects
INSERT INTO `Project` VALUES 
(1, 'Food Relief', 'Provide food packages to needy families affected by pandemic', '2024-01-01', '2024-06-30', 50000, 25000, 'Active', 2),
(2, 'Education Aid', 'Support children education with books and supplies', '2024-02-01', '2024-12-31', 30000, 10000, 'Active', 2),
(3, 'Disaster Response', 'Emergency aid for flood victims', '2024-03-15', NULL, 75000, 40000, 'Planning', 1);

-- Donations
INSERT INTO `Donation` VALUES 
(1, 1, 'Money', 10000, 'Monthly donation', 'Bank Transfer', NULL, NULL, '2024-01-15'),
(2, 2, 'Money', 5000, 'One-time donation', 'Online', NULL, NULL, '2024-02-01'),
(3, 3, 'Goods', 5000, 'Food supplies', 'In-kind', 'Rice', 1000, '2024-02-10'),
(4, 1, 'Goods', 3000, 'Winter clothes', 'In-kind', 'Blankets', 500, '2024-03-05');

-- CashInventory
INSERT INTO `CashInventory` VALUES 
(1, 10000, 'Donation', 1, NULL, '2024-01-15', 'Money donation'),
(2, 5000, 'Donation', 2, NULL, '2024-02-01', 'Money donation'),
(3, -30000, 'BudgetAllocation', NULL, 1, '2024-01-20', 'Project budget allocation'),
(4, -15000, 'BudgetAllocation', NULL, 2, '2024-02-05', 'Project budget allocation');

-- GoodsInventory
INSERT INTO `GoodsInventory` VALUES 
(1, 'Rice', 1000, 800, 'Warehouse A', 'Food grain', 3, 'In Stock'),
(2, 'Blankets', 500, 300, 'Warehouse B', 'Winter blankets', 4, 'In Stock');

-- Beneficiaries
INSERT INTO `Beneficiary` VALUES 
(1, 'Rahul', 'Sharma', '1985-03-15', 'Male', 'rahul@email.com', '555-0301', '123 Village Rd', 'Mumbai', 'Farmer', 200001, 4, 20000, NULL, 'Active'),
(2, 'Priya', 'Patel', '1990-07-22', 'Female', NULL, '555-0302', '456 Slum Area', 'Delhi', 'Housewife', 200002, 3, 15000, 'Single mother', 'Active'),
(3, 'Amit', 'Kumar', '1978-11-30', 'Male', 'amit@email.com', '555-0303', '789 Flood Area', 'Kolkata', 'Laborer', 200003, 5, 18000, NULL, 'Active');

-- Beneficiary Allocations
INSERT INTO `Beneficiary_Allocation` VALUES 
(1, 1, 1, 'Cash', NULL, NULL, 5000, '2024-01-25', 'Completed'),
(2, 1, 2, 'Goods', 1, 100, NULL, '2024-01-26', 'Completed'),
(3, 2, 3, 'Cash', NULL, NULL, 3000, '2024-02-15', 'Completed');

-- Volunteers
INSERT INTO `Volunteer` VALUES 
(1, 'David', 'Wilson', '1995-04-18', 'david@email.com', '555-0401', '123 Volunteer St', 'Miami', 300001, '2024-01-10', 'Current'),
(2, 'Emily', 'Taylor', '1992-12-05', 'emily@email.com', '555-0402', '456 Helper Ave', 'Austin', 300002, '2024-02-15', 'Current'),
(3, 'Chris', 'Miller', '1988-06-25', 'chris@email.com', '555-0403', '789 Service Rd', 'Denver', 300003, '2023-11-20', 'On-Leave');

-- Volunteer Projects
INSERT INTO `Volunteer_Project` VALUES 
(1, 1, 1, 'Distribution Coordinator', 40, '2024-01-20', NULL, 'Active'),
(2, 2, 1, 'Field Worker', 30, '2024-01-22', NULL, 'Active'),
(3, 2, 2, 'Teacher Assistant', 25, '2024-02-10', NULL, 'Active');

-- Events
INSERT INTO `Event` VALUES 
(1, 'Food Distribution Camp', 'Weekly food distribution for 500 families', 'Distribution', '2024-03-10', '09:00:00', '17:00:00', 'Community Hall', 2000, 'Completed', 2, 1),
(2, 'Medical Camp', 'Free health checkup for beneficiaries', 'Medical', '2024-04-15', '08:00:00', '16:00:00', 'Health Center', 5000, 'Scheduled', 2, 1);

-- Budget Allocations
INSERT INTO `Budget_Allocation` VALUES 
(1, 1, 30000, '2024-01-20', 'Initial budget for food relief'),
(2, 2, 15000, '2024-02-05', 'Education materials budget');