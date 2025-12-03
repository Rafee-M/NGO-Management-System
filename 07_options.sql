-- ====================== Constraint Checks ======================

-- Ensure positive amounts
ALTER TABLE `Donation`
ADD CONSTRAINT `chk_amount_positive` CHECK (`amount` > 0);

-- Ensure valid dates
ALTER TABLE `Project`
ADD CONSTRAINT `chk_project_dates` CHECK (`start_date` <= `estimated_end_date`);

-- Change varchar to enum. Restricted and easy to read values
ALTER TABLE `Donor` 
MODIFY `status` ENUM('Active', 'Inactive', 'Blacklisted', 'Deceased') DEFAULT 'Active';

ALTER TABLE `Donation`
MODIFY `donation_type` ENUM('Cash', 'Check', 'Online', 'Goods', 'Services') NOT NULL;

ALTER TABLE `Event`
MODIFY `status` ENUM('Scheduled', 'Ongoing', 'Completed', 'Cancelled') DEFAULT 'Scheduled';