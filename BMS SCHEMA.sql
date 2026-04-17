-- ================================================================
-- HemoLink Blood Bank Management System - Database Schema
-- ================================================================

CREATE DATABASE IF NOT EXISTS `bms_db`;
USE `bms_db`;

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
SET time_zone = "+00:00";
SET foreign_key_checks = 0;
SET NAMES utf8mb4;

-- --------------------------------------------------------
-- Drop existing tables (clean install)
-- --------------------------------------------------------
DROP TABLE IF EXISTS `audit_logs`;
DROP TABLE IF EXISTS `distributions`;
DROP TABLE IF EXISTS `request_items`;
DROP TABLE IF EXISTS `blood_tests`;
DROP TABLE IF EXISTS `appointments`;
DROP TABLE IF EXISTS `donor_health_history`;
DROP TABLE IF EXISTS `hospital_staff`;
DROP TABLE IF EXISTS `staff`;
DROP TABLE IF EXISTS `donations`;
DROP TABLE IF EXISTS `requests`;
DROP TABLE IF EXISTS `blood_units`;
DROP TABLE IF EXISTS `donors`;
DROP TABLE IF EXISTS `hospitals`;
DROP TABLE IF EXISTS `users`;
DROP TABLE IF EXISTS `blood_types`;
DROP TABLE IF EXISTS `components`;
DROP TABLE IF EXISTS `roles`;

-- --------------------------------------------------------
-- Core Tables
-- --------------------------------------------------------

CREATE TABLE `roles` (
    `role_id` INT UNSIGNED NOT NULL AUTO_INCREMENT PRIMARY KEY,
    `role_name` VARCHAR(50) NOT NULL,
    `role_description` VARCHAR(255) NULL,
    `created_at` TIMESTAMP NULL DEFAULT CURRENT_TIMESTAMP(),
    `updated_at` TIMESTAMP NULL DEFAULT CURRENT_TIMESTAMP() ON UPDATE CURRENT_TIMESTAMP(),
    UNIQUE KEY `roles_role_name_unique` (`role_name`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

CREATE TABLE `blood_types` (
    `blood_type_id` INT UNSIGNED NOT NULL AUTO_INCREMENT PRIMARY KEY,
    `type_name` VARCHAR(5) NOT NULL,
    UNIQUE KEY `blood_types_type_name_unique` (`type_name`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

CREATE TABLE `components` (
    `component_id` INT UNSIGNED NOT NULL AUTO_INCREMENT PRIMARY KEY,
    `component_name` VARCHAR(50) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

CREATE TABLE `users` (
    `user_id` INT UNSIGNED NOT NULL AUTO_INCREMENT PRIMARY KEY,
    `role_id` INT UNSIGNED NOT NULL,
    `username` VARCHAR(50) NOT NULL,
    `email` VARCHAR(100) NOT NULL,
    `password_hash` VARCHAR(255) NOT NULL COMMENT 'Bcrypt hashed',
    `first_name` VARCHAR(50) NOT NULL,
    `last_name` VARCHAR(50) NOT NULL,
    `phone` VARCHAR(20) NULL,
    `is_active` BOOLEAN NULL DEFAULT 1,
    `last_login` TIMESTAMP NULL,
    `created_at` TIMESTAMP NULL DEFAULT CURRENT_TIMESTAMP(),
    `updated_at` TIMESTAMP NULL DEFAULT CURRENT_TIMESTAMP() ON UPDATE CURRENT_TIMESTAMP(),
    UNIQUE KEY `users_username_unique` (`username`),
    UNIQUE KEY `users_email_unique` (`email`),
    CONSTRAINT `users_role_id_foreign` FOREIGN KEY (`role_id`) REFERENCES `roles` (`role_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- --------------------------------------------------------
-- Donors (linked to users)
-- --------------------------------------------------------

CREATE TABLE `donors` (
    `donor_id` INT UNSIGNED NOT NULL AUTO_INCREMENT PRIMARY KEY,
    `user_id` INT UNSIGNED NOT NULL,
    `first_name` VARCHAR(50) NOT NULL,
    `last_name` VARCHAR(50) NOT NULL,
    `blood_type_id` INT UNSIGNED NULL,
    `date_of_birth` DATE NULL,
    `gender` ENUM('Male', 'Female', 'Other') NULL,
    `address` VARCHAR(255) NULL,
    `city` VARCHAR(50) NULL,
    `eligibility_status` ENUM('Eligible', 'Deferred', 'Permanently Deferred') NULL DEFAULT 'Eligible',
    `last_donation_date` DATE NULL,
    `total_donations` INT UNSIGNED NULL DEFAULT 0,
    `created_at` TIMESTAMP NULL DEFAULT CURRENT_TIMESTAMP(),
    `updated_at` TIMESTAMP NULL DEFAULT CURRENT_TIMESTAMP() ON UPDATE CURRENT_TIMESTAMP(),
    UNIQUE KEY `donors_user_id_unique` (`user_id`),
    CONSTRAINT `donors_user_id_foreign` FOREIGN KEY (`user_id`) REFERENCES `users` (`user_id`) ON DELETE CASCADE,
    CONSTRAINT `donors_blood_type_foreign` FOREIGN KEY (`blood_type_id`) REFERENCES `blood_types` (`blood_type_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- --------------------------------------------------------
-- Hospitals (linked to manager user)
-- --------------------------------------------------------

CREATE TABLE `hospitals` (
    `hospital_id` INT UNSIGNED NOT NULL AUTO_INCREMENT PRIMARY KEY,
    `user_id` INT UNSIGNED NOT NULL COMMENT 'Hospital manager user account',
    `hospital_name` VARCHAR(100) NOT NULL,
    `hospital_code` VARCHAR(20) NOT NULL,
    `address` TEXT NOT NULL,
    `city` VARCHAR(50) NOT NULL,
    `region` VARCHAR(50) NULL,
    `postal_code` VARCHAR(10) NULL,
    `phone` VARCHAR(20) NOT NULL,
    `email` VARCHAR(100) NULL,
    `license_number` VARCHAR(50) NULL,
    `is_active` BOOLEAN NULL DEFAULT 1,
    `created_at` TIMESTAMP NULL DEFAULT CURRENT_TIMESTAMP(),
    `updated_at` TIMESTAMP NULL DEFAULT CURRENT_TIMESTAMP() ON UPDATE CURRENT_TIMESTAMP(),
    UNIQUE KEY `hospitals_hospital_code_unique` (`hospital_code`),
    UNIQUE KEY `hospitals_user_id_unique` (`user_id`),
    CONSTRAINT `hospitals_user_id_foreign` FOREIGN KEY (`user_id`) REFERENCES `users` (`user_id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- --------------------------------------------------------
-- Donations
-- --------------------------------------------------------

CREATE TABLE `donations` (
    `donation_id` INT UNSIGNED NOT NULL AUTO_INCREMENT PRIMARY KEY,
    `donor_id` INT UNSIGNED NOT NULL,
    `blood_type_id` INT UNSIGNED NULL,
    `donation_date` DATE NOT NULL,
    `volume_ml` INT UNSIGNED NULL DEFAULT 450,
    `status` ENUM('Pending', 'Completed', 'Cancelled') NULL DEFAULT 'Pending',
    `donation_center` VARCHAR(100) NULL,
    `notes` TEXT NULL,
    `created_at` TIMESTAMP NULL DEFAULT CURRENT_TIMESTAMP(),
    CONSTRAINT `donations_donor_id_foreign` FOREIGN KEY (`donor_id`) REFERENCES `donors` (`donor_id`),
    CONSTRAINT `donations_blood_type_foreign` FOREIGN KEY (`blood_type_id`) REFERENCES `blood_types` (`blood_type_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- --------------------------------------------------------
-- Blood Units (inventory)
-- --------------------------------------------------------

CREATE TABLE `blood_units` (
    `unit_id` INT UNSIGNED NOT NULL AUTO_INCREMENT PRIMARY KEY,
    `blood_type_id` INT UNSIGNED NOT NULL,
    `donation_id` INT UNSIGNED NULL,
    `status` ENUM('Available', 'Reserved', 'Dispatched', 'Expired', 'Quarantined', 'Discarded') NULL DEFAULT 'Available',
    `collection_date` DATE NULL,
    `expiry_date` DATE NULL,
    `volume_ml` INT UNSIGNED NULL DEFAULT 450,
    `created_at` TIMESTAMP NULL DEFAULT CURRENT_TIMESTAMP(),
    CONSTRAINT `blood_units_blood_type_foreign` FOREIGN KEY (`blood_type_id`) REFERENCES `blood_types` (`blood_type_id`),
    CONSTRAINT `blood_units_donation_foreign` FOREIGN KEY (`donation_id`) REFERENCES `donations` (`donation_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- --------------------------------------------------------
-- Blood Requests (from hospitals)
-- --------------------------------------------------------

CREATE TABLE `requests` (
    `request_id` INT UNSIGNED NOT NULL AUTO_INCREMENT PRIMARY KEY,
    `hospital_id` INT UNSIGNED NOT NULL,
    `blood_type_id` INT UNSIGNED NULL,
    `units_requested` INT UNSIGNED NOT NULL DEFAULT 1,
    `units_fulfilled` INT UNSIGNED NULL DEFAULT 0,
    `urgency` ENUM('Normal', 'Urgent', 'Emergency') NULL DEFAULT 'Normal',
    `status` ENUM('Pending', 'Processing', 'Fulfilled', 'Partially Fulfilled', 'Rejected', 'Cancelled') NULL DEFAULT 'Pending',
    `notes` TEXT NULL,
    `requested_by` INT UNSIGNED NULL,
    `created_at` TIMESTAMP NULL DEFAULT CURRENT_TIMESTAMP(),
    `updated_at` TIMESTAMP NULL DEFAULT CURRENT_TIMESTAMP() ON UPDATE CURRENT_TIMESTAMP(),
    CONSTRAINT `requests_hospital_id_foreign` FOREIGN KEY (`hospital_id`) REFERENCES `hospitals` (`hospital_id`),
    CONSTRAINT `requests_blood_type_foreign` FOREIGN KEY (`blood_type_id`) REFERENCES `blood_types` (`blood_type_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- --------------------------------------------------------
-- Staff (blood bank internal staff)
-- --------------------------------------------------------

CREATE TABLE `staff` (
    `staff_id` INT UNSIGNED NOT NULL AUTO_INCREMENT PRIMARY KEY,
    `user_id` INT UNSIGNED NOT NULL,
    `employee_id` VARCHAR(20) NOT NULL,
    `department` ENUM('Collection', 'Laboratory', 'Inventory', 'Administration', 'Nursing') NOT NULL,
    `designation` VARCHAR(50) NOT NULL,
    `qualification` VARCHAR(100) NULL,
    `date_joined` DATE NOT NULL,
    `is_verified` BOOLEAN NULL,
    UNIQUE KEY `staff_user_id_unique` (`user_id`),
    UNIQUE KEY `staff_employee_id_unique` (`employee_id`),
    CONSTRAINT `staff_user_id_foreign` FOREIGN KEY (`user_id`) REFERENCES `users` (`user_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- --------------------------------------------------------
-- Hospital Staff
-- --------------------------------------------------------

CREATE TABLE `hospital_staff` (
    `hospital_staff_id` INT UNSIGNED NOT NULL AUTO_INCREMENT PRIMARY KEY,
    `user_id` INT UNSIGNED NOT NULL,
    `hospital_id` INT UNSIGNED NOT NULL,
    `department` VARCHAR(50) NOT NULL,
    `designation` VARCHAR(50) NOT NULL,
    `is_authorized_requester` BOOLEAN NULL COMMENT 'Can place blood requests',
    UNIQUE KEY `hospital_staff_user_id_unique` (`user_id`),
    CONSTRAINT `hospital_staff_user_foreign` FOREIGN KEY (`user_id`) REFERENCES `users` (`user_id`),
    CONSTRAINT `hospital_staff_hospital_foreign` FOREIGN KEY (`hospital_id`) REFERENCES `hospitals` (`hospital_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- --------------------------------------------------------
-- Donor Health History
-- --------------------------------------------------------

CREATE TABLE `donor_health_history` (
    `history_id` INT UNSIGNED NOT NULL AUTO_INCREMENT PRIMARY KEY,
    `donor_id` INT UNSIGNED NOT NULL,
    `donation_id` INT UNSIGNED NULL,
    `checked_by` INT UNSIGNED NOT NULL,
    `check_date` DATE NOT NULL,
    `weight_kg` DECIMAL(5, 2) NULL,
    `blood_pressure_systolic` INT UNSIGNED NULL,
    `blood_pressure_diastolic` INT UNSIGNED NULL,
    `pulse_rate` INT UNSIGNED NULL,
    `temperature` DECIMAL(4, 1) NULL,
    `hemoglobin_level` DECIMAL(4, 1) NULL,
    `has_medical_condition` BOOLEAN NULL,
    `medical_conditions` TEXT NULL,
    `is_deferred` BOOLEAN NULL,
    `deferral_period_days` INT UNSIGNED NULL,
    `deferral_reason` TEXT NULL,
    `doctor_clearance` BOOLEAN NULL DEFAULT 1,
    `remarks` TEXT NULL,
    INDEX `donor_health_history_donor_id_check_date_index` (`donor_id`, `check_date`),
    CONSTRAINT `donor_health_donor_foreign` FOREIGN KEY (`donor_id`) REFERENCES `donors` (`donor_id`),
    CONSTRAINT `donor_health_checked_by_foreign` FOREIGN KEY (`checked_by`) REFERENCES `users` (`user_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- --------------------------------------------------------
-- Appointments
-- --------------------------------------------------------

CREATE TABLE `appointments` (
    `appointment_id` INT UNSIGNED NOT NULL AUTO_INCREMENT PRIMARY KEY,
    `donor_id` INT UNSIGNED NOT NULL,
    `appointment_date` DATE NOT NULL,
    `appointment_time` TIME NOT NULL,
    `purpose` ENUM('Donation', 'Health Check', 'Consultation') NULL DEFAULT 'Donation',
    `status` ENUM('Scheduled', 'Completed', 'Cancelled', 'No Show') NULL DEFAULT 'Scheduled',
    `notes` TEXT NULL,
    `created_by` INT UNSIGNED NULL,
    `created_at` TIMESTAMP NULL DEFAULT CURRENT_TIMESTAMP(),
    INDEX `appointments_date_status_index` (`appointment_date`, `status`),
    CONSTRAINT `appointments_donor_foreign` FOREIGN KEY (`donor_id`) REFERENCES `donors` (`donor_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- --------------------------------------------------------
-- Blood Tests
-- --------------------------------------------------------

CREATE TABLE `blood_tests` (
    `test_id` INT UNSIGNED NOT NULL AUTO_INCREMENT PRIMARY KEY,
    `unit_id` INT UNSIGNED NOT NULL,
    `test_date` DATE NOT NULL,
    `tested_by` INT UNSIGNED NOT NULL,
    `hiv_result` ENUM('Negative', 'Positive', 'Indeterminate') NOT NULL,
    `hepatitis_b` ENUM('Negative', 'Positive', 'Indeterminate') NOT NULL,
    `hepatitis_c` ENUM('Negative', 'Positive', 'Indeterminate') NOT NULL,
    `syphilis` ENUM('Negative', 'Positive', 'Indeterminate') NOT NULL,
    `malaria` ENUM('Negative', 'Positive', 'Indeterminate') NULL DEFAULT 'Negative',
    `blood_group_verified` BOOLEAN NULL,
    `notes` TEXT NULL,
    `is_approved` BOOLEAN NULL,
    `approved_by` INT UNSIGNED NULL,
    `created_at` TIMESTAMP NULL DEFAULT CURRENT_TIMESTAMP(),
    INDEX `blood_tests_results_index` (`hiv_result`, `hepatitis_b`, `hepatitis_c`, `unit_id`),
    CONSTRAINT `blood_tests_unit_foreign` FOREIGN KEY (`unit_id`) REFERENCES `blood_units` (`unit_id`),
    CONSTRAINT `blood_tests_tested_by_foreign` FOREIGN KEY (`tested_by`) REFERENCES `users` (`user_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- --------------------------------------------------------
-- Request Items
-- --------------------------------------------------------

CREATE TABLE `request_items` (
    `request_item_id` INT UNSIGNED NOT NULL AUTO_INCREMENT PRIMARY KEY,
    `request_id` INT UNSIGNED NOT NULL,
    `blood_type_id` INT UNSIGNED NOT NULL,
    `component_id` INT UNSIGNED NOT NULL,
    `units_requested` INT UNSIGNED NOT NULL DEFAULT 1,
    `units_fulfilled` INT UNSIGNED NULL,
    `special_requirements` TEXT NULL COMMENT 'Irradiated, CMV negative, etc.',
    CONSTRAINT `request_items_request_foreign` FOREIGN KEY (`request_id`) REFERENCES `requests` (`request_id`),
    CONSTRAINT `request_items_blood_type_foreign` FOREIGN KEY (`blood_type_id`) REFERENCES `blood_types` (`blood_type_id`),
    CONSTRAINT `request_items_component_foreign` FOREIGN KEY (`component_id`) REFERENCES `components` (`component_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- --------------------------------------------------------
-- Distributions
-- --------------------------------------------------------

CREATE TABLE `distributions` (
    `distribution_id` INT UNSIGNED NOT NULL AUTO_INCREMENT PRIMARY KEY,
    `request_id` INT UNSIGNED NULL,
    `hospital_id` INT UNSIGNED NOT NULL,
    `dispatched_by` INT UNSIGNED NOT NULL,
    `dispatched_date` TIMESTAMP NULL DEFAULT CURRENT_TIMESTAMP(),
    `transport_method` ENUM('Hospital Pickup', 'Ambulance', 'Courier', 'Emergency Transport') NOT NULL,
    `transport_temperature` DECIMAL(4, 1) NULL,
    `receiver_name` VARCHAR(100) NOT NULL,
    `receiver_id` VARCHAR(50) NULL COMMENT 'ID of receiving staff',
    `receiver_signature` INT NULL COMMENT 'Digital signature or scan',
    `delivery_notes` TEXT NULL,
    `is_delivered` BOOLEAN NULL,
    `delivered_at` TIMESTAMP NULL,
    INDEX `distributions_dispatched_date_index` (`dispatched_date`),
    CONSTRAINT `distributions_request_foreign` FOREIGN KEY (`request_id`) REFERENCES `requests` (`request_id`),
    CONSTRAINT `distributions_hospital_foreign` FOREIGN KEY (`hospital_id`) REFERENCES `hospitals` (`hospital_id`),
    CONSTRAINT `distributions_dispatched_by_foreign` FOREIGN KEY (`dispatched_by`) REFERENCES `users` (`user_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- --------------------------------------------------------
-- Audit Logs
-- --------------------------------------------------------

CREATE TABLE `audit_logs` (
    `log_id` BIGINT UNSIGNED NOT NULL AUTO_INCREMENT PRIMARY KEY,
    `table_name` VARCHAR(50) NOT NULL,
    `record_id` INT UNSIGNED NOT NULL,
    `action` ENUM('INSERT', 'UPDATE', 'DELETE') NOT NULL,
    `old_values` JSON NULL,
    `new_values` JSON NULL,
    `changed_by` INT UNSIGNED NOT NULL,
    `changed_at` TIMESTAMP NULL DEFAULT CURRENT_TIMESTAMP(),
    `ip_address` VARCHAR(45) NULL,
    `user_agent` TEXT NULL,
    INDEX `audit_logs_table_record_index` (`table_name`, `record_id`),
    INDEX `audit_logs_changed_at_index` (`changed_at`),
    CONSTRAINT `audit_logs_changed_by_foreign` FOREIGN KEY (`changed_by`) REFERENCES `users` (`user_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- --------------------------------------------------------
-- Seed Data
-- --------------------------------------------------------

-- Roles
INSERT INTO `roles` (`role_id`, `role_name`, `role_description`) VALUES
(1, 'admin', 'System Administrator - Full access to all modules'),
(2, 'hospital_manager', 'Hospital Manager - Manages hospital blood requests'),
(3, 'donor', 'Donor - Manages personal donation records');

-- Blood Types
INSERT INTO `blood_types` (`blood_type_id`, `type_name`) VALUES
(1, 'A+'), (2, 'A-'), (3, 'B+'), (4, 'B-'),
(5, 'AB+'), (6, 'AB-'), (7, 'O+'), (8, 'O-');

-- Components
INSERT INTO `components` (`component_id`, `component_name`) VALUES
(1, 'Whole Blood'), (2, 'Red Blood Cells'), (3, 'Platelets'),
(4, 'Plasma'), (5, 'Cryoprecipitate');

-- Default Admin User (password: admin123)
INSERT INTO `users` (`user_id`, `role_id`, `username`, `email`, `password_hash`, `first_name`, `last_name`, `phone`, `is_active`) VALUES
(1, 1, 'admin', 'admin@hemolink.com', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', 'System', 'Admin', '+254700000000', 1);

SET foreign_key_checks = 1;