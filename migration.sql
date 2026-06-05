-- ================================================================
-- HemoLink BMS - Schema Consolidation Migration
-- Ref: consolidation.md
--
-- Removes: roles, blood_types, components, donors, staff, hospital_staff
-- Merges their data into: users (+ inline ENUMs on child tables)
--
-- Run against an EXISTING bms_db with seeded data.
-- ================================================================

USE `bms_db`;
SET foreign_key_checks = 0;
SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";


-- ================================================================
-- PHASE 1: ADD NEW COLUMNS TO `users`
-- ================================================================

-- 1a. Replace role_id FK with role ENUM
ALTER TABLE `users`
    ADD COLUMN `role` ENUM('Admin','Hospital','Donor') NULL AFTER `user_id`;

-- 1b. Add donor-specific columns
ALTER TABLE `users`
    ADD COLUMN `blood_type`         ENUM('A+','A-','B+','B-','AB+','AB-','O+','O-') NULL AFTER `last_login`,
    ADD COLUMN `date_of_birth`      DATE NULL AFTER `blood_type`,
    ADD COLUMN `gender`             ENUM('Male','Female','Other') NULL AFTER `date_of_birth`,
    ADD COLUMN `address`            VARCHAR(255) NULL AFTER `gender`,
    ADD COLUMN `city`               VARCHAR(50) NULL AFTER `address`,
    ADD COLUMN `eligibility_status` ENUM('Eligible','Deferred','Permanently Deferred') NULL DEFAULT 'Eligible' AFTER `city`;

-- 1c. Add staff-specific columns
ALTER TABLE `users`
    ADD COLUMN `employee_id`    VARCHAR(20) NULL AFTER `eligibility_status`,
    ADD COLUMN `department`     ENUM('Collection','Laboratory','Inventory','Administration','Nursing') NULL AFTER `employee_id`,
    ADD COLUMN `designation`    VARCHAR(50) NULL AFTER `department`,
    ADD COLUMN `qualification`  VARCHAR(100) NULL AFTER `designation`,
    ADD COLUMN `date_joined`    DATE NULL AFTER `qualification`,
    ADD COLUMN `is_verified`    BOOLEAN NULL AFTER `date_joined`;

-- 1d. Add hospital-staff-specific columns
ALTER TABLE `users`
    ADD COLUMN `hospital_id`             INT UNSIGNED NULL AFTER `is_verified`,
    ADD COLUMN `is_authorized_requester` BOOLEAN NULL AFTER `hospital_id`;


-- ================================================================
-- PHASE 2: MIGRATE DATA INTO `users` NEW COLUMNS
-- ================================================================

-- 2a. Populate role ENUM from roles table
UPDATE `users` u
    JOIN `roles` r ON u.role_id = r.role_id
SET u.role = CASE r.role_name
    WHEN 'admin'            THEN 'Admin'
    WHEN 'hospital_manager' THEN 'Hospital'
    WHEN 'donor'            THEN 'Donor'
END;

-- 2b. Populate donor fields from donors table
UPDATE `users` u
    JOIN `donors` d ON u.user_id = d.user_id
    LEFT JOIN `blood_types` bt ON d.blood_type_id = bt.blood_type_id
SET u.blood_type         = bt.type_name,
    u.date_of_birth      = d.date_of_birth,
    u.gender             = d.gender,
    u.address            = d.address,
    u.city               = d.city,
    u.eligibility_status = d.eligibility_status;

-- 2c. Populate staff fields from staff table
UPDATE `users` u
    JOIN `staff` s ON u.user_id = s.user_id
SET u.employee_id   = s.employee_id,
    u.department     = s.department,
    u.designation    = s.designation,
    u.qualification  = s.qualification,
    u.date_joined    = s.date_joined,
    u.is_verified    = s.is_verified;

-- 2d. Populate hospital-staff fields from hospital_staff table
UPDATE `users` u
    JOIN `hospital_staff` hs ON u.user_id = hs.user_id
SET u.hospital_id             = hs.hospital_id,
    u.department               = hs.department,
    u.designation              = hs.designation,
    u.is_authorized_requester  = hs.is_authorized_requester;


-- ================================================================
-- PHASE 3: MIGRATE blood_type_id → blood_type ENUM ON CHILD TABLES
-- ================================================================

-- 3a. donations: add blood_type ENUM, populate, drop old FK + column
ALTER TABLE `donations`
    ADD COLUMN `blood_type` ENUM('A+','A-','B+','B-','AB+','AB-','O+','O-') NULL AFTER `donor_id`;

UPDATE `donations` dn
    LEFT JOIN `blood_types` bt ON dn.blood_type_id = bt.blood_type_id
SET dn.blood_type = bt.type_name;

ALTER TABLE `donations` DROP FOREIGN KEY `donations_blood_type_foreign`;
ALTER TABLE `donations` DROP COLUMN `blood_type_id`;


-- 3b. blood_units: add blood_type ENUM, populate, drop old FK + column
ALTER TABLE `blood_units`
    ADD COLUMN `blood_type` ENUM('A+','A-','B+','B-','AB+','AB-','O+','O-') NOT NULL DEFAULT 'O+' AFTER `unit_id`;

UPDATE `blood_units` bu
    JOIN `blood_types` bt ON bu.blood_type_id = bt.blood_type_id
SET bu.blood_type = bt.type_name;

ALTER TABLE `blood_units` DROP FOREIGN KEY `blood_units_blood_type_foreign`;
ALTER TABLE `blood_units` DROP COLUMN `blood_type_id`;

-- Remove the temporary default now that data is populated
ALTER TABLE `blood_units` MODIFY COLUMN `blood_type` ENUM('A+','A-','B+','B-','AB+','AB-','O+','O-') NOT NULL;


-- 3c. requests: add blood_type ENUM, populate, drop old FK + column
ALTER TABLE `requests`
    ADD COLUMN `blood_type` ENUM('A+','A-','B+','B-','AB+','AB-','O+','O-') NULL AFTER `hospital_id`;

UPDATE `requests` r
    LEFT JOIN `blood_types` bt ON r.blood_type_id = bt.blood_type_id
SET r.blood_type = bt.type_name;

ALTER TABLE `requests` DROP FOREIGN KEY `requests_blood_type_foreign`;
ALTER TABLE `requests` DROP COLUMN `blood_type_id`;


-- 3d. request_items: add blood_type ENUM + component ENUM, populate, drop old FKs + columns
ALTER TABLE `request_items`
    ADD COLUMN `blood_type` ENUM('A+','A-','B+','B-','AB+','AB-','O+','O-') NOT NULL DEFAULT 'O+' AFTER `request_id`,
    ADD COLUMN `component`  ENUM('Whole Blood','Red Blood Cells','Platelets','Plasma','Cryoprecipitate') NOT NULL DEFAULT 'Whole Blood' AFTER `blood_type`;

UPDATE `request_items` ri
    JOIN `blood_types` bt ON ri.blood_type_id = bt.blood_type_id
    JOIN `components` c ON ri.component_id = c.component_id
SET ri.blood_type = bt.type_name,
    ri.component  = c.component_name;

ALTER TABLE `request_items` DROP FOREIGN KEY `request_items_blood_type_foreign`;
ALTER TABLE `request_items` DROP FOREIGN KEY `request_items_component_foreign`;
ALTER TABLE `request_items` DROP COLUMN `blood_type_id`;
ALTER TABLE `request_items` DROP COLUMN `component_id`;

-- Remove temporary defaults
ALTER TABLE `request_items`
    MODIFY COLUMN `blood_type` ENUM('A+','A-','B+','B-','AB+','AB-','O+','O-') NOT NULL,
    MODIFY COLUMN `component`  ENUM('Whole Blood','Red Blood Cells','Platelets','Plasma','Cryoprecipitate') NOT NULL;


-- ================================================================
-- PHASE 4: REMAP donor_id FKs (donors.donor_id → users.user_id)
-- ================================================================
-- Child tables that reference donors(donor_id) need their values
-- remapped to the corresponding users(user_id).

-- 4a. donations.donor_id → users.user_id
ALTER TABLE `donations` DROP FOREIGN KEY `donations_donor_id_foreign`;

UPDATE `donations` dn
    JOIN `donors` d ON dn.donor_id = d.donor_id
SET dn.donor_id = d.user_id;

ALTER TABLE `donations`
    ADD CONSTRAINT `donations_donor_id_foreign`
    FOREIGN KEY (`donor_id`) REFERENCES `users` (`user_id`);


-- 4b. donor_health_history.donor_id → users.user_id
ALTER TABLE `donor_health_history` DROP FOREIGN KEY `donor_health_donor_foreign`;

UPDATE `donor_health_history` dhh
    JOIN `donors` d ON dhh.donor_id = d.donor_id
SET dhh.donor_id = d.user_id;

ALTER TABLE `donor_health_history`
    ADD CONSTRAINT `donor_health_donor_foreign`
    FOREIGN KEY (`donor_id`) REFERENCES `users` (`user_id`);


-- 4c. appointments.donor_id → users.user_id
ALTER TABLE `appointments` DROP FOREIGN KEY `appointments_donor_foreign`;

UPDATE `appointments` a
    JOIN `donors` d ON a.donor_id = d.donor_id
SET a.donor_id = d.user_id;

ALTER TABLE `appointments`
    ADD CONSTRAINT `appointments_donor_foreign`
    FOREIGN KEY (`donor_id`) REFERENCES `users` (`user_id`);


-- ================================================================
-- PHASE 5: FINALIZE `users` TABLE — DROP OLD role_id, ADD CONSTRAINTS
-- ================================================================

-- 5a. Make role NOT NULL now that data is populated
ALTER TABLE `users` MODIFY COLUMN `role` ENUM('Admin','Hospital','Donor') NOT NULL;

-- 5b. Drop the old role_id FK and column
ALTER TABLE `users` DROP FOREIGN KEY `users_role_id_foreign`;
ALTER TABLE `users` DROP COLUMN `role_id`;

-- 5c. Add unique key on employee_id (nullable, only staff have it)
ALTER TABLE `users` ADD UNIQUE KEY `users_employee_id_unique` (`employee_id`);

-- 5d. Add hospital FK for hospital-staff users
ALTER TABLE `users`
    ADD CONSTRAINT `users_hospital_id_foreign`
    FOREIGN KEY (`hospital_id`) REFERENCES `hospitals` (`hospital_id`);


-- ================================================================
-- PHASE 6: DROP REMOVED TABLES
-- ================================================================

DROP TABLE `hospital_staff`;
DROP TABLE `staff`;
DROP TABLE `donors`;
DROP TABLE `blood_types`;
DROP TABLE `components`;
DROP TABLE `roles`;


-- ================================================================
-- DONE
-- ================================================================

SET foreign_key_checks = 1;

SELECT '✅ Migration complete. Tables reduced from 14 to 11.' AS result;
SELECT CONCAT('   Users: ', COUNT(*), ' rows') AS info FROM users;
SELECT CONCAT('   Roles column sample: ', GROUP_CONCAT(DISTINCT role ORDER BY role)) AS info FROM users;
