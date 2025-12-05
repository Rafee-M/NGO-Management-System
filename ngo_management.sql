-- phpMyAdmin SQL Dump
-- version 5.2.1
-- https://www.phpmyadmin.net/
--
-- Host: 127.0.0.1
-- Generation Time: Dec 06, 2025 at 12:24 AM
-- Server version: 10.4.32-MariaDB
-- PHP Version: 8.0.30

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
START TRANSACTION;
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;

--
-- Database: `ngo_management`
--

-- --------------------------------------------------------

--
-- Table structure for table `beneficiary`
--

CREATE TABLE `beneficiary` (
  `beneficiary_id` int(11) NOT NULL,
  `first_name` varchar(30) NOT NULL,
  `last_name` varchar(30) NOT NULL,
  `dob` date DEFAULT NULL,
  `gender` varchar(10) DEFAULT NULL,
  `email` varchar(30) DEFAULT NULL,
  `phone` varchar(16) NOT NULL,
  `address` varchar(50) DEFAULT NULL,
  `city` varchar(20) NOT NULL,
  `occupation` varchar(40) DEFAULT NULL,
  `nid` int(11) DEFAULT NULL,
  `family_size` int(11) DEFAULT 1,
  `income` int(11) DEFAULT NULL,
  `special_needs` text DEFAULT NULL,
  `status` varchar(20) DEFAULT 'Active'
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `beneficiary`
--

INSERT INTO `beneficiary` (`beneficiary_id`, `first_name`, `last_name`, `dob`, `gender`, `email`, `phone`, `address`, `city`, `occupation`, `nid`, `family_size`, `income`, `special_needs`, `status`) VALUES
(1, 'Fatima', 'Begum', '1990-05-15', 'Female', 'fatima@email.com', '555-0301', '123 Slum Area, Road 1', 'Dhaka', 'Housewife', 100000001, 5, 8000, 'None', 'Active'),
(2, 'Abdul', 'Rahman', '1975-11-22', 'Male', 'abdul@email.com', '555-0302', '456 Village Road', 'Chittagong', 'Day Laborer', 100000002, 4, 12000, 'Asthma', 'Active'),
(3, 'Sunita', 'Devi', '1988-03-08', 'Female', 'sunita@email.com', '555-0303', '789 Rural Colony', 'Rajshahi', 'Farm Worker', 100000003, 6, 10000, 'Visual impairment', 'Active'),
(4, 'Rahim', 'Islam', '1965-12-10', 'Male', 'rahim@email.com', '555-0304', '321 Urban Slum', 'Khulna', 'Rickshaw Puller', 100000004, 3, 9000, 'Diabetes', 'Active'),
(5, 'Anjali', 'Sharma', '1995-07-30', 'Female', 'anjali@email.com', '555-0305', '654 Temporary Settlement', 'Sylhet', 'Student', 100000005, 4, 7000, 'None', 'Active'),
(6, 'Kamal', 'Hossain', '1982-09-14', 'Male', 'kamal@email.com', '555-0306', '987 Flood Area', 'Barisal', 'Fisherman', 100000006, 5, 11000, 'None', 'Active'),
(7, 'Bina', 'Akter', '1978-04-25', 'Female', 'bina@email.com', '555-0307', '147 Hill Tracts', 'Rangpur', 'Weaver', 100000007, 3, 8500, 'Hearing difficulty', 'Active'),
(8, 'Rajesh', 'Kumar', '1970-08-18', 'Male', 'rajesh@email.com', '555-0308', '258 Displaced Camp', 'Mymensingh', 'Construction Worker', 100000008, 6, 13000, 'Back pain', 'Active'),
(9, 'Taslima', 'Khatun', '1992-01-05', 'Female', 'taslima@email.com', '555-0309', '369 Riverbank Area', 'Comilla', 'Domestic Worker', 100000009, 2, 6000, 'None', 'Active'),
(10, 'Sanjay', 'Das', '1968-06-20', 'Male', 'sanjay@email.com', '555-0310', '741 Coastal Village', 'Noakhali', 'Farmer', 100000010, 7, 14000, 'Arthritis', 'Active');

-- --------------------------------------------------------

--
-- Table structure for table `beneficiary_allocation`
--

CREATE TABLE `beneficiary_allocation` (
  `allocation_id` int(11) NOT NULL,
  `project_id` int(11) NOT NULL,
  `beneficiary_id` int(11) NOT NULL,
  `allocation_type` enum('Cash','Goods') NOT NULL,
  `item_id` int(11) DEFAULT NULL,
  `quantity` int(11) DEFAULT NULL,
  `cash_amount` int(11) DEFAULT NULL,
  `allocation_date` date DEFAULT curdate(),
  `status` varchar(20) DEFAULT 'Pending'
) ;

-- --------------------------------------------------------

--
-- Table structure for table `budget_allocation`
--

CREATE TABLE `budget_allocation` (
  `allocation_id` int(11) NOT NULL,
  `project_id` int(11) NOT NULL,
  `amount` int(11) NOT NULL,
  `allocation_date` date DEFAULT curdate(),
  `description` text DEFAULT NULL
) ;

--
-- Dumping data for table `budget_allocation`
--

INSERT INTO `budget_allocation` (`allocation_id`, `project_id`, `amount`, `allocation_date`, `description`) VALUES
(1, 1, 20000, '2023-01-20', 'Initial funding for Education for All'),
(2, 2, 30000, '2023-03-05', 'First quarter budget for Food Security'),
(3, 1, 15000, '2023-06-15', 'Additional funds for school supplies'),
(4, 3, 50000, '2023-02-15', 'Emergency response allocation'),
(5, 4, 20000, '2023-04-25', 'Vocational training materials'),
(6, 5, 35000, '2023-05-10', 'Medical supplies purchase'),
(7, 6, 60000, '2023-06-20', 'Water purification equipment'),
(8, 7, 15000, '2023-07-05', 'Sports equipment and facilities'),
(9, 8, 20000, '2023-01-10', 'Quarterly stipends for elderly'),
(10, 9, 25000, '2023-08-25', 'Disability equipment purchase');

-- --------------------------------------------------------

--
-- Table structure for table `cashinventory`
--

CREATE TABLE `cashinventory` (
  `transaction_id` int(11) NOT NULL,
  `amount` int(11) NOT NULL,
  `transaction_type` enum('Donation','BudgetAllocation','Adjustment') NOT NULL,
  `donation_id` int(11) DEFAULT NULL,
  `project_id` int(11) DEFAULT NULL,
  `transaction_date` date DEFAULT curdate(),
  `description` varchar(255) DEFAULT NULL
) ;

--
-- Dumping data for table `cashinventory`
--

INSERT INTO `cashinventory` (`transaction_id`, `amount`, `transaction_type`, `donation_id`, `project_id`, `transaction_date`, `description`) VALUES
(1, 50000, 'Donation', 1, NULL, '2023-10-01', 'Money donation from Donor #1'),
(2, 100000, 'Donation', 2, NULL, '2023-10-05', 'Money donation from Donor #2'),
(3, 2500, 'Donation', 4, NULL, '2023-10-12', 'Money donation from Donor #4'),
(4, 750000, 'Donation', 6, NULL, '2023-10-18', 'Money donation from Donor #6'),
(5, 150000, 'Donation', 7, NULL, '2023-10-20', 'Money donation from Donor #3'),
(6, 3000, 'Donation', 9, NULL, '2023-10-25', 'Money donation from Donor #8');

-- --------------------------------------------------------

--
-- Stand-in structure for view `currentcashbalance`
-- (See below for the actual view)
--
CREATE TABLE `currentcashbalance` (
`available_balance` decimal(32,0)
);

-- --------------------------------------------------------

--
-- Table structure for table `donation`
--

CREATE TABLE `donation` (
  `donation_id` int(11) NOT NULL,
  `donor_id` int(11) NOT NULL,
  `donation_type` enum('Money','Goods') NOT NULL,
  `amount` int(11) NOT NULL,
  `description` text DEFAULT NULL,
  `payment_method` varchar(30) DEFAULT NULL,
  `item_name` varchar(50) DEFAULT NULL,
  `quantity` int(11) DEFAULT NULL,
  `donation_date` date DEFAULT curdate()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `donation`
--

INSERT INTO `donation` (`donation_id`, `donor_id`, `donation_type`, `amount`, `description`, `payment_method`, `item_name`, `quantity`, `donation_date`) VALUES
(1, 1, 'Money', 50000, 'Monthly donation for education project', 'Bank Transfer', NULL, NULL, '2023-10-01'),
(2, 2, 'Money', 100000, 'Annual contribution', 'Check', NULL, NULL, '2023-10-05'),
(3, 3, 'Goods', 0, '100 laptops for students', NULL, 'Laptops', 100, '2023-10-10'),
(4, 4, 'Money', 2500, 'Food security program support', 'Credit Card', NULL, NULL, '2023-10-12'),
(5, 5, 'Goods', 0, 'Winter clothing for distribution', NULL, 'Winter Jackets', 200, '2023-10-15'),
(6, 6, 'Money', 750000, 'Emergency relief fund', 'Bank Transfer', NULL, NULL, '2023-10-18'),
(7, 3, 'Money', 150000, 'Healthcare initiative sponsorship', 'Corporate Transfer', NULL, NULL, '2023-10-20'),
(8, 7, 'Goods', 0, 'Medical supplies and medicines', NULL, 'Medical Kits', 50, '2023-10-22'),
(9, 8, 'Money', 3000, 'Youth development program', 'PayPal', NULL, NULL, '2023-10-25'),
(10, 9, 'Goods', 0, 'School supplies package', NULL, 'School Kits', 300, '2023-10-28');

--
-- Triggers `donation`
--
DELIMITER $$
CREATE TRIGGER `after_goods_donation` AFTER INSERT ON `donation` FOR EACH ROW BEGIN
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
END
$$
DELIMITER ;
DELIMITER $$
CREATE TRIGGER `after_money_donation` AFTER INSERT ON `donation` FOR EACH ROW BEGIN
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
END
$$
DELIMITER ;

-- --------------------------------------------------------

--
-- Table structure for table `donor`
--

CREATE TABLE `donor` (
  `donor_id` int(11) NOT NULL,
  `first_name` varchar(30) DEFAULT NULL,
  `last_name` varchar(30) DEFAULT NULL,
  `email` varchar(30) DEFAULT NULL,
  `phone` varchar(16) DEFAULT NULL,
  `address` varchar(40) DEFAULT NULL,
  `city` varchar(25) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `donor`
--

INSERT INTO `donor` (`donor_id`, `first_name`, `last_name`, `email`, `phone`, `address`, `city`) VALUES
(1, 'Michael', 'Thompson', 'michael@corp.com', '555-0201', '1000 Business Blvd', 'Metro City'),
(2, 'Sarah', 'Williams', 'sarah@williamsfamily.org', '555-0202', '200 Philanthropy Ave', 'Capital City'),
(3, 'TechGiant Inc', NULL, 'donations@techgiant.com', '555-0203', '300 Innovation Park', 'Silicon Valley'),
(4, 'James', 'Wilson', 'james.w@email.com', '555-0204', '400 Charity Lane', 'Springfield'),
(5, 'Community Bank', NULL, 'csr@commbank.com', '555-0205', '500 Finance Street', 'Downtown'),
(6, 'Patricia', 'Miller', 'patricia.m@email.com', '555-0206', '600 Giving Circle', 'Riverside'),
(7, 'Global Pharma', NULL, 'giving@globalpharma.com', '555-0207', '700 Health Way', 'Medical City'),
(8, 'Thomas', 'Anderson', 'thomas.a@email.com', '555-0208', '800 Hope Drive', 'Hillside'),
(9, 'Local Rotary Club', NULL, 'president@rotarylocal.org', '555-0209', '900 Service Road', 'Centerville'),
(10, 'Jennifer', 'Clark', 'jennifer.c@email.com', '555-0210', '1000 Compassion Court', 'Greenville');

-- --------------------------------------------------------

--
-- Table structure for table `event`
--

CREATE TABLE `event` (
  `event_id` int(11) NOT NULL,
  `event_name` varchar(30) NOT NULL,
  `description` text NOT NULL,
  `event_type` varchar(50) NOT NULL,
  `date` date NOT NULL,
  `start_time` time NOT NULL,
  `end_time` time NOT NULL,
  `location` varchar(40) NOT NULL,
  `cost` int(11) NOT NULL DEFAULT 0,
  `status` enum('Scheduled','Completed','Cancelled') DEFAULT 'Scheduled',
  `coordinator` int(11) NOT NULL,
  `project_id` int(11) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Table structure for table `goodsinventory`
--

CREATE TABLE `goodsinventory` (
  `item_id` int(11) NOT NULL,
  `item_name` varchar(30) NOT NULL,
  `available_quantity` int(11) NOT NULL DEFAULT 0,
  `storage_location` varchar(30) NOT NULL,
  `description` text DEFAULT NULL,
  `donation_id` int(11) DEFAULT NULL,
  `status` varchar(15) DEFAULT 'In Stock'
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `goodsinventory`
--

INSERT INTO `goodsinventory` (`item_id`, `item_name`, `available_quantity`, `storage_location`, `description`, `donation_id`, `status`) VALUES
(1, 'Laptops', 100, 'Main Warehouse', '100 laptops for students', 3, 'In Stock'),
(2, 'Winter Jackets', 200, 'Main Warehouse', 'Winter clothing for distribution', 5, 'In Stock'),
(3, 'Medical Kits', 50, 'Main Warehouse', 'Medical supplies and medicines', 8, 'In Stock'),
(4, 'School Kits', 300, 'Main Warehouse', 'School supplies package', 10, 'In Stock');

-- --------------------------------------------------------

--
-- Table structure for table `project`
--

CREATE TABLE `project` (
  `project_id` int(11) NOT NULL,
  `project_name` varchar(30) NOT NULL,
  `description` text NOT NULL,
  `start_date` date NOT NULL,
  `end_date` date DEFAULT NULL,
  `allocated_budget` int(11) DEFAULT 0,
  `spent_budget` int(11) DEFAULT 0,
  `status` varchar(15) NOT NULL DEFAULT 'Planning',
  `project_manager` int(11) DEFAULT NULL
) ;

--
-- Dumping data for table `project`
--

INSERT INTO `project` (`project_id`, `project_name`, `description`, `start_date`, `end_date`, `allocated_budget`, `spent_budget`, `status`, `project_manager`) VALUES
(1, 'Education for All', 'Providing school supplies and tuition support for underprivileged children', '2023-01-15', '2023-12-31', 50000, 32000, 'Active', 2),
(2, 'Food Security', 'Distributing food packages to low-income families', '2023-03-01', NULL, 75000, 45000, 'Active', 5),
(3, 'Emergency Relief', 'Immediate aid for disaster-affected communities', '2023-02-10', '2023-08-10', 10000, 9800, 'Completed', 5),
(4, 'Women Empowerment', 'Vocational training and business grants for women', '2023-04-20', '2024-04-20', 6000, 1500, 'Active', 4),
(5, 'Healthcare Access', 'Free medical camps and medicine distribution', '2023-05-05', NULL, 80000, 42000, 'Active', 2),
(6, 'Clean Water Initiative', 'Installing water purification systems in rural areas', '2023-06-15', '2024-06-15', 120000, 35000, 'Planning', 5),
(7, 'Youth Development', 'Sports and leadership programs for teenagers', '2023-07-01', '2023-12-31', 30000, 22000, 'Active', 4),
(8, 'Elderly Support', 'Monthly stipends and healthcare for seniors', '2023-01-01', NULL, 40000, 38000, 'Active', 2),
(9, 'Disability Inclusion', 'Special equipment and accessibility modifications', '2023-08-20', '2024-08-20', 55000, 18000, 'Active', 5),
(10, 'Environmental Cleanup', 'Community trash collection and recycling program', '2023-09-10', '2023-11-10', 25000, 24500, 'Completed', 4);

-- --------------------------------------------------------

--
-- Stand-in structure for view `projectfinancialsummary`
-- (See below for the actual view)
--
CREATE TABLE `projectfinancialsummary` (
`project_id` int(11)
,`project_name` varchar(30)
,`allocated_budget` int(11)
,`spent_budget` int(11)
,`remaining_budget` bigint(12)
,`utilization_percentage` decimal(16,2)
,`status` varchar(15)
);

-- --------------------------------------------------------

--
-- Table structure for table `staff`
--

CREATE TABLE `staff` (
  `staff_id` int(11) NOT NULL,
  `first_name` varchar(30) NOT NULL,
  `last_name` varchar(30) NOT NULL,
  `email` varchar(30) NOT NULL,
  `nid` int(11) NOT NULL,
  `address` varchar(50) NOT NULL,
  `dob` date NOT NULL,
  `gender` varchar(10) DEFAULT NULL,
  `phone` varchar(16) NOT NULL,
  `position` varchar(40) NOT NULL,
  `status` enum('Active','Fired','Quit') DEFAULT 'Active',
  `supervisor_id` int(11) DEFAULT NULL,
  `hire_date` date DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `staff`
--

INSERT INTO `staff` (`staff_id`, `first_name`, `last_name`, `email`, `nid`, `address`, `dob`, `gender`, `phone`, `position`, `status`, `supervisor_id`, `hire_date`) VALUES
(1, 'John', 'Smith', 'john.smith@ngo.org', 123456789, '123 Main St, Cityville', '1985-03-15', 'Male', '555-0101', 'Executive Director', 'Active', NULL, '2020-01-15'),
(2, 'Maria', 'Garcia', 'maria.g@ngo.org', 987654321, '456 Oak Ave, Townsville', '1990-07-22', 'Female', '555-0102', 'Project Manager', 'Active', 1, '2021-03-10'),
(3, 'David', 'Chen', 'david.c@ngo.org', 456789123, '789 Pine Rd, Villageton', '1988-11-05', 'Male', '555-0103', 'Finance Officer', 'Active', 1, '2020-08-20'),
(4, 'Sarah', 'Johnson', 'sarah.j@ngo.org', 321654987, '321 Elm St, Hamlet City', '1992-04-18', 'Female', '555-0104', 'Volunteer Coordinator', 'Active', 2, '2022-01-12'),
(5, 'Ahmed', 'Khan', 'ahmed.k@ngo.org', 654987321, '654 Maple Dr, Borough Town', '1987-09-30', 'Male', '555-0105', 'Field Officer', 'Active', 2, '2021-11-05'),
(6, 'Lisa', 'Wang', 'lisa.w@ngo.org', 789123456, '987 Cedar Ln, Countyville', '1993-12-25', 'Female', '555-0106', 'Inventory Manager', 'Active', 1, '2022-06-15'),
(7, 'Robert', 'Brown', 'robert.b@ngo.org', 159753486, '159 Birch St, District City', '1984-06-08', 'Male', '555-0107', 'IT Specialist', 'Active', 1, '2020-05-30'),
(8, 'Emma', 'Davis', 'emma.d@ngo.org', 357159486, '357 Walnut Ave, Metroville', '1991-02-14', 'Female', '555-0108', 'HR Manager', 'Active', 1, '2021-09-22'),
(9, 'Carlos', 'Rodriguez', 'carlos.r@ngo.org', 258369147, '258 Spruce Rd, Urban Town', '1989-08-17', 'Male', '555-0109', 'Monitoring Officer', 'Active', 2, '2022-03-18'),
(10, 'Aisha', 'Mohammed', 'aisha.m@ngo.org', 147258369, '147 Fir Dr, Capital City', '1994-05-03', 'Female', '555-0110', 'Communications Officer', 'Active', 1, '2023-01-08');

-- --------------------------------------------------------

--
-- Table structure for table `volunteer`
--

CREATE TABLE `volunteer` (
  `volunteer_id` int(11) NOT NULL,
  `first_name` varchar(30) NOT NULL,
  `last_name` varchar(30) NOT NULL,
  `dob` date DEFAULT NULL,
  `email` varchar(30) NOT NULL,
  `phone` varchar(16) NOT NULL,
  `address` varchar(50) DEFAULT NULL,
  `city` varchar(20) DEFAULT NULL,
  `nid` int(11) DEFAULT NULL,
  `date_joined` date DEFAULT curdate(),
  `status` enum('Current','On-Leave','Quit') DEFAULT 'Current'
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `volunteer`
--

INSERT INTO `volunteer` (`volunteer_id`, `first_name`, `last_name`, `dob`, `email`, `phone`, `address`, `city`, `nid`, `date_joined`, `status`) VALUES
(1, 'Alex', 'Turner', '1998-04-12', 'alex.t@email.com', '555-0401', '101 Student Hostel', 'Dhaka', 200000001, '2023-01-10', 'Current'),
(2, 'Sofia', 'Martinez', '1996-09-25', 'sofia.m@email.com', '555-0402', '202 University Dorm', 'Dhaka', 200000002, '2023-02-15', 'Current'),
(3, 'Kenji', 'Tanaka', '1995-11-30', 'kenji.t@email.com', '555-0403', '303 Expat Residence', 'Chittagong', 200000003, '2023-03-05', 'Current'),
(4, 'Lena', 'Schmidt', '1997-07-18', 'lena.s@email.com', '555-0404', '404 Volunteer House', 'Rajshahi', 200000004, '2023-01-20', 'On-Leave'),
(5, 'Omar', 'Farooq', '1994-02-28', 'omar.f@email.com', '555-0405', '505 Community Center', 'Khulna', 200000005, '2023-04-10', 'Current'),
(6, 'Priya', 'Patel', '1999-12-05', 'priya.p@email.com', '555-0406', '606 Youth Hostel', 'Sylhet', 200000006, '2023-05-15', 'Current'),
(7, 'Mark', 'Johnson', '1993-08-22', 'mark.j@email.com', '555-0407', '707 Temporary Stay', 'Barisal', 200000007, '2023-02-28', 'Quit'),
(8, 'Amina', 'Hassan', '1996-03-14', 'amina.h@email.com', '555-0408', '808 Local Residence', 'Rangpur', 200000008, '2023-06-01', 'Current'),
(9, 'Diego', 'Silva', '1992-10-08', 'diego.s@email.com', '555-0409', '909 Shared Apartment', 'Mymensingh', 200000009, '2023-03-20', 'Current'),
(10, 'Yuki', 'Nakamura', '1998-06-17', 'yuki.n@email.com', '555-0410', '1010 Volunteer Lodge', 'Comilla', 200000010, '2023-07-05', 'Current');

-- --------------------------------------------------------

--
-- Stand-in structure for view `volunteertotalhours`
-- (See below for the actual view)
--
CREATE TABLE `volunteertotalhours` (
`volunteer_id` int(11)
,`first_name` varchar(30)
,`last_name` varchar(30)
,`total_hours` decimal(32,0)
,`total_assignments` bigint(21)
);

-- --------------------------------------------------------

--
-- Table structure for table `volunteer_project`
--

CREATE TABLE `volunteer_project` (
  `assignment_id` int(11) NOT NULL,
  `volunteer_id` int(11) NOT NULL,
  `project_id` int(11) NOT NULL,
  `role` varchar(30) NOT NULL,
  `hours` int(11) NOT NULL,
  `start_date` date NOT NULL,
  `end_date` date DEFAULT NULL,
  `status` varchar(20) DEFAULT 'Active'
) ;

--
-- Dumping data for table `volunteer_project`
--

INSERT INTO `volunteer_project` (`assignment_id`, `volunteer_id`, `project_id`, `role`, `hours`, `start_date`, `end_date`, `status`) VALUES
(1, 1, 1, 'Tutor', 40, '2023-02-01', NULL, 'Active'),
(2, 2, 1, 'Assistant', 35, '2023-02-15', NULL, 'Active'),
(3, 3, 2, 'Distribution Helper', 50, '2023-03-10', NULL, 'Active'),
(4, 4, 3, 'Field Coordinator', 120, '2023-02-15', '2023-08-10', 'Completed'),
(5, 5, 4, 'Workshop Facilitator', 60, '2023-05-01', NULL, 'Active'),
(6, 6, 5, 'Medical Assistant', 45, '2023-05-10', NULL, 'Active'),
(7, 7, 6, 'Technical Assistant', 80, '2023-06-20', NULL, 'Active'),
(8, 8, 7, 'Sports Coach', 30, '2023-07-05', NULL, 'Active'),
(9, 9, 8, 'Companion', 25, '2023-01-15', NULL, 'Active'),
(10, 10, 9, 'Accessibility Assistant', 40, '2023-08-25', NULL, 'Active');

-- --------------------------------------------------------

--
-- Structure for view `currentcashbalance`
--
DROP TABLE IF EXISTS `currentcashbalance`;

CREATE ALGORITHM=UNDEFINED DEFINER=`root`@`localhost` SQL SECURITY DEFINER VIEW `currentcashbalance`  AS SELECT coalesce(sum(`cashinventory`.`amount`),0) AS `available_balance` FROM `cashinventory` ;

-- --------------------------------------------------------

--
-- Structure for view `projectfinancialsummary`
--
DROP TABLE IF EXISTS `projectfinancialsummary`;

CREATE ALGORITHM=UNDEFINED DEFINER=`root`@`localhost` SQL SECURITY DEFINER VIEW `projectfinancialsummary`  AS SELECT `p`.`project_id` AS `project_id`, `p`.`project_name` AS `project_name`, `p`.`allocated_budget` AS `allocated_budget`, `p`.`spent_budget` AS `spent_budget`, `p`.`allocated_budget`- `p`.`spent_budget` AS `remaining_budget`, round(`p`.`spent_budget` / nullif(`p`.`allocated_budget`,0) * 100,2) AS `utilization_percentage`, `p`.`status` AS `status` FROM `project` AS `p` ;

-- --------------------------------------------------------

--
-- Structure for view `volunteertotalhours`
--
DROP TABLE IF EXISTS `volunteertotalhours`;

CREATE ALGORITHM=UNDEFINED DEFINER=`root`@`localhost` SQL SECURITY DEFINER VIEW `volunteertotalhours`  AS SELECT `v`.`volunteer_id` AS `volunteer_id`, `v`.`first_name` AS `first_name`, `v`.`last_name` AS `last_name`, coalesce(sum(`vp`.`hours`),0) AS `total_hours`, count(`vp`.`assignment_id`) AS `total_assignments` FROM (`volunteer` `v` left join `volunteer_project` `vp` on(`v`.`volunteer_id` = `vp`.`volunteer_id`)) GROUP BY `v`.`volunteer_id`, `v`.`first_name`, `v`.`last_name` ;

--
-- Indexes for dumped tables
--

--
-- Indexes for table `beneficiary`
--
ALTER TABLE `beneficiary`
  ADD PRIMARY KEY (`beneficiary_id`),
  ADD KEY `idx_city` (`city`),
  ADD KEY `idx_status` (`status`);

--
-- Indexes for table `beneficiary_allocation`
--
ALTER TABLE `beneficiary_allocation`
  ADD PRIMARY KEY (`allocation_id`),
  ADD KEY `item_id` (`item_id`),
  ADD KEY `idx_project` (`project_id`),
  ADD KEY `idx_beneficiary` (`beneficiary_id`),
  ADD KEY `idx_allocation_date` (`allocation_date`),
  ADD KEY `idx_status` (`status`);

--
-- Indexes for table `budget_allocation`
--
ALTER TABLE `budget_allocation`
  ADD PRIMARY KEY (`allocation_id`),
  ADD KEY `idx_project` (`project_id`),
  ADD KEY `idx_date` (`allocation_date`);

--
-- Indexes for table `cashinventory`
--
ALTER TABLE `cashinventory`
  ADD PRIMARY KEY (`transaction_id`),
  ADD KEY `idx_donation` (`donation_id`),
  ADD KEY `idx_project` (`project_id`),
  ADD KEY `idx_date` (`transaction_date`),
  ADD KEY `idx_type` (`transaction_type`);

--
-- Indexes for table `donation`
--
ALTER TABLE `donation`
  ADD PRIMARY KEY (`donation_id`),
  ADD KEY `idx_donor` (`donor_id`),
  ADD KEY `idx_type` (`donation_type`),
  ADD KEY `idx_date` (`donation_date`);

--
-- Indexes for table `donor`
--
ALTER TABLE `donor`
  ADD PRIMARY KEY (`donor_id`),
  ADD UNIQUE KEY `email` (`email`),
  ADD KEY `idx_city` (`city`);

--
-- Indexes for table `event`
--
ALTER TABLE `event`
  ADD PRIMARY KEY (`event_id`),
  ADD KEY `idx_project` (`project_id`),
  ADD KEY `idx_date` (`date`),
  ADD KEY `idx_status` (`status`),
  ADD KEY `coordinator` (`coordinator`);

--
-- Indexes for table `goodsinventory`
--
ALTER TABLE `goodsinventory`
  ADD PRIMARY KEY (`item_id`),
  ADD KEY `idx_status` (`status`),
  ADD KEY `idx_storage` (`storage_location`),
  ADD KEY `donation_id` (`donation_id`);

--
-- Indexes for table `project`
--
ALTER TABLE `project`
  ADD PRIMARY KEY (`project_id`),
  ADD KEY `idx_status` (`status`),
  ADD KEY `idx_start_date` (`start_date`),
  ADD KEY `project_manager` (`project_manager`);

--
-- Indexes for table `staff`
--
ALTER TABLE `staff`
  ADD PRIMARY KEY (`staff_id`),
  ADD UNIQUE KEY `email` (`email`),
  ADD UNIQUE KEY `nid` (`nid`),
  ADD KEY `idx_status` (`status`),
  ADD KEY `idx_supervisor` (`supervisor_id`);

--
-- Indexes for table `volunteer`
--
ALTER TABLE `volunteer`
  ADD PRIMARY KEY (`volunteer_id`),
  ADD UNIQUE KEY `email` (`email`),
  ADD UNIQUE KEY `nid` (`nid`),
  ADD KEY `idx_status` (`status`),
  ADD KEY `idx_join_date` (`date_joined`);

--
-- Indexes for table `volunteer_project`
--
ALTER TABLE `volunteer_project`
  ADD PRIMARY KEY (`assignment_id`),
  ADD KEY `idx_volunteer` (`volunteer_id`),
  ADD KEY `idx_project` (`project_id`),
  ADD KEY `idx_status` (`status`),
  ADD KEY `idx_start_date` (`start_date`);

--
-- AUTO_INCREMENT for dumped tables
--

--
-- AUTO_INCREMENT for table `beneficiary`
--
ALTER TABLE `beneficiary`
  MODIFY `beneficiary_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=11;

--
-- AUTO_INCREMENT for table `beneficiary_allocation`
--
ALTER TABLE `beneficiary_allocation`
  MODIFY `allocation_id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `budget_allocation`
--
ALTER TABLE `budget_allocation`
  MODIFY `allocation_id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `cashinventory`
--
ALTER TABLE `cashinventory`
  MODIFY `transaction_id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `donation`
--
ALTER TABLE `donation`
  MODIFY `donation_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=11;

--
-- AUTO_INCREMENT for table `donor`
--
ALTER TABLE `donor`
  MODIFY `donor_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=11;

--
-- AUTO_INCREMENT for table `event`
--
ALTER TABLE `event`
  MODIFY `event_id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `goodsinventory`
--
ALTER TABLE `goodsinventory`
  MODIFY `item_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=5;

--
-- AUTO_INCREMENT for table `project`
--
ALTER TABLE `project`
  MODIFY `project_id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `staff`
--
ALTER TABLE `staff`
  MODIFY `staff_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=11;

--
-- AUTO_INCREMENT for table `volunteer`
--
ALTER TABLE `volunteer`
  MODIFY `volunteer_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=11;

--
-- AUTO_INCREMENT for table `volunteer_project`
--
ALTER TABLE `volunteer_project`
  MODIFY `assignment_id` int(11) NOT NULL AUTO_INCREMENT;

--
-- Constraints for dumped tables
--

--
-- Constraints for table `beneficiary_allocation`
--
ALTER TABLE `beneficiary_allocation`
  ADD CONSTRAINT `beneficiary_allocation_ibfk_1` FOREIGN KEY (`project_id`) REFERENCES `project` (`project_id`) ON DELETE CASCADE ON UPDATE CASCADE,
  ADD CONSTRAINT `beneficiary_allocation_ibfk_2` FOREIGN KEY (`beneficiary_id`) REFERENCES `beneficiary` (`beneficiary_id`) ON DELETE CASCADE ON UPDATE CASCADE,
  ADD CONSTRAINT `beneficiary_allocation_ibfk_3` FOREIGN KEY (`item_id`) REFERENCES `goodsinventory` (`item_id`) ON DELETE SET NULL ON UPDATE CASCADE;

--
-- Constraints for table `budget_allocation`
--
ALTER TABLE `budget_allocation`
  ADD CONSTRAINT `budget_allocation_ibfk_1` FOREIGN KEY (`project_id`) REFERENCES `project` (`project_id`) ON DELETE CASCADE ON UPDATE CASCADE;

--
-- Constraints for table `cashinventory`
--
ALTER TABLE `cashinventory`
  ADD CONSTRAINT `cashinventory_ibfk_1` FOREIGN KEY (`donation_id`) REFERENCES `donation` (`donation_id`) ON DELETE CASCADE ON UPDATE CASCADE,
  ADD CONSTRAINT `cashinventory_ibfk_2` FOREIGN KEY (`project_id`) REFERENCES `project` (`project_id`) ON DELETE CASCADE ON UPDATE CASCADE;

--
-- Constraints for table `donation`
--
ALTER TABLE `donation`
  ADD CONSTRAINT `donation_ibfk_1` FOREIGN KEY (`donor_id`) REFERENCES `donor` (`donor_id`) ON DELETE CASCADE ON UPDATE CASCADE;

--
-- Constraints for table `event`
--
ALTER TABLE `event`
  ADD CONSTRAINT `event_ibfk_1` FOREIGN KEY (`project_id`) REFERENCES `project` (`project_id`) ON DELETE CASCADE ON UPDATE CASCADE,
  ADD CONSTRAINT `event_ibfk_2` FOREIGN KEY (`coordinator`) REFERENCES `staff` (`staff_id`) ON UPDATE CASCADE;

--
-- Constraints for table `goodsinventory`
--
ALTER TABLE `goodsinventory`
  ADD CONSTRAINT `goodsinventory_ibfk_1` FOREIGN KEY (`donation_id`) REFERENCES `donation` (`donation_id`) ON DELETE CASCADE ON UPDATE CASCADE;

--
-- Constraints for table `project`
--
ALTER TABLE `project`
  ADD CONSTRAINT `project_ibfk_1` FOREIGN KEY (`project_manager`) REFERENCES `staff` (`staff_id`) ON DELETE SET NULL ON UPDATE CASCADE;

--
-- Constraints for table `staff`
--
ALTER TABLE `staff`
  ADD CONSTRAINT `staff_ibfk_1` FOREIGN KEY (`supervisor_id`) REFERENCES `staff` (`staff_id`) ON DELETE SET NULL ON UPDATE CASCADE;

--
-- Constraints for table `volunteer_project`
--
ALTER TABLE `volunteer_project`
  ADD CONSTRAINT `volunteer_project_ibfk_1` FOREIGN KEY (`volunteer_id`) REFERENCES `volunteer` (`volunteer_id`) ON DELETE CASCADE ON UPDATE CASCADE,
  ADD CONSTRAINT `volunteer_project_ibfk_2` FOREIGN KEY (`project_id`) REFERENCES `project` (`project_id`) ON DELETE CASCADE ON UPDATE CASCADE;
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
