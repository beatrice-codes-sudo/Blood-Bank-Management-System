-- ================================================================
-- HemoLink Blood Bank Management System - Consolidated Database Schema
-- Version: 2.0 (Post-Consolidation & Modernization)
-- Tables: 14 total (users, hospitals, stock_thresholds, emergency_appeals,
--                   donations, blood_units, requests, request_items,
--                   distributions, payments, appointments, blood_tests,
--                   donor_health_history, audit_logs)
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
DROP TABLE IF EXISTS `payments`;
DROP TABLE IF EXISTS `request_items`;
DROP TABLE IF EXISTS `requests`;
DROP TABLE IF EXISTS `emergency_appeals`;
DROP TABLE IF EXISTS `stock_thresholds`;
DROP TABLE IF EXISTS `blood_tests`;
DROP TABLE IF EXISTS `appointments`;
DROP TABLE IF EXISTS `donor_health_history`;
DROP TABLE IF EXISTS `blood_units`;
DROP TABLE IF EXISTS `donations`;
DROP TABLE IF EXISTS `hospitals`;
DROP TABLE IF EXISTS `users`;

-- Also drop legacy tables if upgrading from old schema
DROP TABLE IF EXISTS `hospital_staff`;
DROP TABLE IF EXISTS `staff`;
DROP TABLE IF EXISTS `donors`;
DROP TABLE IF EXISTS `blood_types`;
DROP TABLE IF EXISTS `components`;
DROP TABLE IF EXISTS `roles`;

-- --------------------------------------------------------
-- 1. Users (Central entity — absorbs roles, donors, staff)
-- --------------------------------------------------------

CREATE TABLE `users` (
    `user_id`                 INT UNSIGNED NOT NULL AUTO_INCREMENT PRIMARY KEY,
    `role`                    ENUM('Admin','Hospital','Donor') NOT NULL,
    `username`                VARCHAR(50) NOT NULL,
    `email`                   VARCHAR(100) NOT NULL,
    `password_hash`           VARCHAR(255) NOT NULL COMMENT 'Bcrypt hashed',
    `first_name`              VARCHAR(50) NOT NULL,
    `last_name`               VARCHAR(50) NOT NULL,
    `phone`                   VARCHAR(20) NULL,
    `is_active`               BOOLEAN NULL DEFAULT 1,
    `last_login`              TIMESTAMP NULL,

    -- Donor-specific (NULL for non-donors)
    `blood_type`              ENUM('A+','A-','B+','B-','AB+','AB-','O+','O-') NULL,
    `date_of_birth`           DATE NULL,
    `gender`                  ENUM('Male','Female','Other') NULL,
    `address`                 VARCHAR(255) NULL,
    `city`                    VARCHAR(50) NULL,
    `eligibility_status`      ENUM('Eligible','Deferred','Permanently Deferred') NULL DEFAULT 'Eligible',

    -- Staff-specific (NULL for non-staff)
    `employee_id`             VARCHAR(20) NULL,
    `department`              ENUM('Collection','Laboratory','Inventory','Administration','Nursing') NULL,
    `designation`             VARCHAR(50) NULL,
    `qualification`           VARCHAR(100) NULL,
    `date_joined`             DATE NULL,
    `is_verified`             BOOLEAN NULL,

    -- Hospital-staff-specific
    `hospital_id`             INT UNSIGNED NULL,
    `is_authorized_requester` BOOLEAN NULL,

    `created_at`              TIMESTAMP NULL DEFAULT CURRENT_TIMESTAMP(),
    `updated_at`              TIMESTAMP NULL DEFAULT CURRENT_TIMESTAMP() ON UPDATE CURRENT_TIMESTAMP(),

    UNIQUE KEY `users_username_unique` (`username`),
    UNIQUE KEY `users_email_unique` (`email`),
    UNIQUE KEY `users_employee_id_unique` (`employee_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------
-- 2. Hospitals (Partner hospitals and clinics)
-- --------------------------------------------------------

CREATE TABLE `hospitals` (
    `hospital_id`             INT UNSIGNED NOT NULL AUTO_INCREMENT PRIMARY KEY,
    `user_id`                 INT UNSIGNED NOT NULL COMMENT 'Hospital manager user account',
    `hospital_name`           VARCHAR(100) NOT NULL,
    `hospital_code`           VARCHAR(20) NOT NULL,
    `address`                 TEXT NOT NULL,
    `city`                    VARCHAR(50) NOT NULL,
    `region`                  VARCHAR(50) NULL,
    `postal_code`             VARCHAR(10) NULL,
    `phone`                   VARCHAR(20) NOT NULL,
    `email`                   VARCHAR(100) NULL,
    `license_number`          VARCHAR(50) NULL,
    `is_active`               BOOLEAN NULL DEFAULT 1,
    `subscription_plan`       ENUM('Starter','Professional','Enterprise') DEFAULT 'Starter',
    `billing_interval`        ENUM('Monthly','Annual') DEFAULT 'Monthly',
    `subscription_expires_at` DATETIME NULL DEFAULT NULL,
    `paystack_customer_code`  VARCHAR(100) NULL DEFAULT NULL,
    `created_at`              TIMESTAMP NULL DEFAULT CURRENT_TIMESTAMP(),
    `updated_at`              TIMESTAMP NULL DEFAULT CURRENT_TIMESTAMP() ON UPDATE CURRENT_TIMESTAMP(),

    UNIQUE KEY `hospitals_hospital_code_unique` (`hospital_code`),
    UNIQUE KEY `hospitals_user_id_unique` (`user_id`),
    CONSTRAINT `hospitals_user_id_foreign` FOREIGN KEY (`user_id`) REFERENCES `users` (`user_id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- Add hospital FK on users after hospitals table exists
ALTER TABLE `users`
    ADD CONSTRAINT `users_hospital_id_foreign`
    FOREIGN KEY (`hospital_id`) REFERENCES `hospitals` (`hospital_id`);

-- --------------------------------------------------------
-- 3. Stock Safety Thresholds (Configurable per blood type)
-- --------------------------------------------------------

CREATE TABLE `stock_thresholds` (
    `blood_type`              ENUM('A+','A-','B+','B-','AB+','AB-','O+','O-') NOT NULL PRIMARY KEY,
    `min_threshold`           INT UNSIGNED NOT NULL DEFAULT 5,
    `updated_at`              TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP() ON UPDATE CURRENT_TIMESTAMP()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------
-- 4. Emergency In-App Appeals (Donor Mobilization)
-- --------------------------------------------------------

CREATE TABLE `emergency_appeals` (
    `appeal_id`               INT UNSIGNED NOT NULL AUTO_INCREMENT PRIMARY KEY,
    `blood_type`              ENUM('A+','A-','B+','B-','AB+','AB-','O+','O-') NOT NULL,
    `urgency`                 VARCHAR(50) NULL DEFAULT 'Critical Shortage',
    `message`                 TEXT NOT NULL,
    `is_active`               BOOLEAN NULL DEFAULT 1,
    `created_by`              INT UNSIGNED NOT NULL,
    `created_at`              TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP(),
    `resolved_at`             TIMESTAMP NULL DEFAULT NULL,
    CONSTRAINT `emergency_appeals_created_by_foreign` FOREIGN KEY (`created_by`) REFERENCES `users` (`user_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------
-- 5. Donations
-- --------------------------------------------------------

CREATE TABLE `donations` (
    `donation_id`             INT UNSIGNED NOT NULL AUTO_INCREMENT PRIMARY KEY,
    `donor_id`                INT UNSIGNED NOT NULL COMMENT 'References users.user_id for donor',
    `blood_type`              ENUM('A+','A-','B+','B-','AB+','AB-','O+','O-') NULL,
    `donation_date`           DATE NOT NULL,
    `volume_ml`               INT UNSIGNED NULL DEFAULT 450,
    `status`                  ENUM('Pending', 'Completed', 'Cancelled') NULL DEFAULT 'Pending',
    `donation_center`         VARCHAR(100) NULL,
    `notes`                   TEXT NULL,
    `created_at`              TIMESTAMP NULL DEFAULT CURRENT_TIMESTAMP(),
    CONSTRAINT `donations_donor_id_foreign` FOREIGN KEY (`donor_id`) REFERENCES `users` (`user_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------
-- 6. Blood Units (Inventory tracking)
-- --------------------------------------------------------

CREATE TABLE `blood_units` (
    `unit_id`                 INT UNSIGNED NOT NULL AUTO_INCREMENT PRIMARY KEY,
    `blood_type`              ENUM('A+','A-','B+','B-','AB+','AB-','O+','O-') NOT NULL,
    `donation_id`             INT UNSIGNED NULL,
    `status`                  ENUM('Available', 'Reserved', 'Dispatched', 'Expired', 'Quarantined', 'Discarded') NULL DEFAULT 'Available',
    `collection_date`         DATE NULL,
    `expiry_date`             DATE NULL,
    `volume_ml`               INT UNSIGNED NULL DEFAULT 450,
    `created_at`              TIMESTAMP NULL DEFAULT CURRENT_TIMESTAMP(),
    CONSTRAINT `blood_units_donation_foreign` FOREIGN KEY (`donation_id`) REFERENCES `donations` (`donation_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------
-- 7. Blood Requests (Hospital Requisitions & Click & Collect PIN)
-- --------------------------------------------------------

CREATE TABLE `requests` (
    `request_id`              INT UNSIGNED NOT NULL AUTO_INCREMENT PRIMARY KEY,
    `hospital_id`             INT UNSIGNED NOT NULL,
    `blood_type`              ENUM('A+','A-','B+','B-','AB+','AB-','O+','O-') NULL,
    `units_requested`         INT UNSIGNED NOT NULL DEFAULT 1,
    `units_fulfilled`         INT UNSIGNED NULL DEFAULT 0,
    `urgency`                 ENUM('Normal', 'Urgent', 'Emergency') NULL DEFAULT 'Normal',
    `status`                  ENUM('Pending', 'Processing', 'Dispatched', 'Fulfilled', 'Partially Fulfilled', 'Rejected', 'Cancelled') NULL DEFAULT 'Pending',
    `collection_status`       ENUM('Pending', 'Ready for Pickup', 'Dispatched', 'Collected', 'Received', 'Cancelled') NULL DEFAULT 'Pending',
    `release_pin`             VARCHAR(10) NULL,
    `pin_generated_at`        DATETIME NULL,
    `collected_at`            DATETIME NULL,
    `collected_by_name`       VARCHAR(100) NULL,
    `collected_by_phone`      VARCHAR(30) NULL,
    `dispatched_at`           DATETIME NULL,
    `received_at`             DATETIME NULL,
    `temp_verified`           BOOLEAN NULL DEFAULT 1,
    `notes`                   TEXT NULL,
    `requested_by`            INT UNSIGNED NULL,
    `created_at`              TIMESTAMP NULL DEFAULT CURRENT_TIMESTAMP(),
    `updated_at`              TIMESTAMP NULL DEFAULT CURRENT_TIMESTAMP() ON UPDATE CURRENT_TIMESTAMP(),
    CONSTRAINT `requests_hospital_id_foreign` FOREIGN KEY (`hospital_id`) REFERENCES `hospitals` (`hospital_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------
-- 8. Request Items (Multi-item requisitions)
-- --------------------------------------------------------

CREATE TABLE `request_items` (
    `request_item_id`         INT UNSIGNED NOT NULL AUTO_INCREMENT PRIMARY KEY,
    `request_id`              INT UNSIGNED NOT NULL,
    `blood_type`              ENUM('A+','A-','B+','B-','AB+','AB-','O+','O-') NOT NULL,
    `component`               ENUM('Whole Blood','Red Blood Cells','Platelets','Plasma','Cryoprecipitate') NOT NULL,
    `units_requested`         INT UNSIGNED NOT NULL DEFAULT 1,
    `units_fulfilled`         INT UNSIGNED NULL,
    `special_requirements`    TEXT NULL COMMENT 'Irradiated, CMV negative, etc.',
    CONSTRAINT `request_items_request_foreign` FOREIGN KEY (`request_id`) REFERENCES `requests` (`request_id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------
-- 9. Distributions (Dispatch & transport logistics)
-- --------------------------------------------------------

CREATE TABLE `distributions` (
    `distribution_id`         INT UNSIGNED NOT NULL AUTO_INCREMENT PRIMARY KEY,
    `request_id`              INT UNSIGNED NULL,
    `hospital_id`             INT UNSIGNED NOT NULL,
    `dispatched_by`           INT UNSIGNED NOT NULL,
    `dispatched_date`         TIMESTAMP NULL DEFAULT CURRENT_TIMESTAMP(),
    `transport_method`        ENUM('Hospital Pickup', 'Ambulance', 'Courier', 'Emergency Transport') NOT NULL,
    `transport_temperature`   DECIMAL(4, 1) NULL,
    `receiver_name`           VARCHAR(100) NOT NULL,
    `receiver_id`             VARCHAR(50) NULL COMMENT 'ID of receiving staff',
    `receiver_signature`      INT NULL COMMENT 'Digital signature or scan',
    `delivery_notes`          TEXT NULL,
    `is_delivered`            BOOLEAN NULL,
    `delivered_at`            TIMESTAMP NULL,
    INDEX `distributions_dispatched_date_index` (`dispatched_date`),
    CONSTRAINT `distributions_request_foreign` FOREIGN KEY (`request_id`) REFERENCES `requests` (`request_id`),
    CONSTRAINT `distributions_hospital_foreign` FOREIGN KEY (`hospital_id`) REFERENCES `hospitals` (`hospital_id`),
    CONSTRAINT `distributions_dispatched_by_foreign` FOREIGN KEY (`dispatched_by`) REFERENCES `users` (`user_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------
-- 10. Payments (Paystack Subscriptions & Audit)
-- --------------------------------------------------------

CREATE TABLE `payments` (
    `payment_id`              INT UNSIGNED NOT NULL AUTO_INCREMENT PRIMARY KEY,
    `hospital_id`             INT UNSIGNED NOT NULL,
    `reference`               VARCHAR(100) NOT NULL,
    `amount`                  DECIMAL(10,2) NOT NULL,
    `currency`                VARCHAR(10) NOT NULL DEFAULT 'KES',
    `plan_name`               ENUM('Starter','Professional','Enterprise') NOT NULL,
    `billing_interval`        ENUM('Monthly','Annual') DEFAULT 'Monthly',
    `channel`                 VARCHAR(50) NULL COMMENT 'card, mobile_money, bank, etc.',
    `status`                  ENUM('Pending','Success','Failed') DEFAULT 'Pending',
    `paystack_response`       LONGTEXT NULL,
    `paid_at`                 DATETIME NULL,
    `created_at`              TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP(),
    UNIQUE KEY `payments_reference_unique` (`reference`),
    CONSTRAINT `payments_hospital_id_foreign` FOREIGN KEY (`hospital_id`) REFERENCES `hospitals` (`hospital_id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------
-- 11. Appointments (Donor appointments & screenings)
-- --------------------------------------------------------

CREATE TABLE `appointments` (
    `appointment_id`          INT UNSIGNED NOT NULL AUTO_INCREMENT PRIMARY KEY,
    `donor_id`                INT UNSIGNED NOT NULL COMMENT 'References users.user_id for donor',
    `hospital_id`             INT UNSIGNED NULL,
    `appointment_date`        DATE NOT NULL,
    `appointment_time`        TIME NOT NULL,
    `purpose`                 ENUM('Donation', 'Health Check', 'Consultation') NULL DEFAULT 'Donation',
    `status`                  ENUM('Scheduled', 'Completed', 'Cancelled', 'No Show') NULL DEFAULT 'Scheduled',
    `notes`                   TEXT NULL,
    `created_by`              INT UNSIGNED NULL,
    `created_at`              TIMESTAMP NULL DEFAULT CURRENT_TIMESTAMP(),
    INDEX `appointments_date_status_index` (`appointment_date`, `status`),
    CONSTRAINT `appointments_donor_foreign` FOREIGN KEY (`donor_id`) REFERENCES `users` (`user_id`),
    CONSTRAINT `appointments_hospital_foreign` FOREIGN KEY (`hospital_id`) REFERENCES `hospitals` (`hospital_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------
-- 12. Blood Tests (Lab screening for infectious markers)
-- --------------------------------------------------------

CREATE TABLE `blood_tests` (
    `test_id`                 INT UNSIGNED NOT NULL AUTO_INCREMENT PRIMARY KEY,
    `unit_id`                 INT UNSIGNED NOT NULL,
    `test_date`               DATE NOT NULL,
    `tested_by`               INT UNSIGNED NOT NULL,
    `hiv_result`              ENUM('Negative', 'Positive', 'Indeterminate') NOT NULL,
    `hepatitis_b`             ENUM('Negative', 'Positive', 'Indeterminate') NOT NULL,
    `hepatitis_c`             ENUM('Negative', 'Positive', 'Indeterminate') NOT NULL,
    `syphilis`                ENUM('Negative', 'Positive', 'Indeterminate') NOT NULL,
    `malaria`                 ENUM('Negative', 'Positive', 'Indeterminate') NULL DEFAULT 'Negative',
    `blood_group_verified`    BOOLEAN NULL,
    `notes`                   TEXT NULL,
    `is_approved`             BOOLEAN NULL,
    `approved_by`             INT UNSIGNED NULL,
    `created_at`              TIMESTAMP NULL DEFAULT CURRENT_TIMESTAMP(),
    INDEX `blood_tests_results_index` (`hiv_result`, `hepatitis_b`, `hepatitis_c`, `unit_id`),
    CONSTRAINT `blood_tests_unit_foreign` FOREIGN KEY (`unit_id`) REFERENCES `blood_units` (`unit_id`),
    CONSTRAINT `blood_tests_tested_by_foreign` FOREIGN KEY (`tested_by`) REFERENCES `users` (`user_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------
-- 13. Donor Health History (Vitals & deferrals)
-- --------------------------------------------------------

CREATE TABLE `donor_health_history` (
    `history_id`              INT UNSIGNED NOT NULL AUTO_INCREMENT PRIMARY KEY,
    `donor_id`                INT UNSIGNED NOT NULL COMMENT 'References users.user_id for donor',
    `donation_id`             INT UNSIGNED NULL,
    `checked_by`              INT UNSIGNED NOT NULL,
    `check_date`              DATE NOT NULL,
    `weight_kg`               DECIMAL(5, 2) NULL,
    `blood_pressure_systolic` INT UNSIGNED NULL,
    `blood_pressure_diastolic`INT UNSIGNED NULL,
    `pulse_rate`              INT UNSIGNED NULL,
    `temperature`             DECIMAL(4, 1) NULL,
    `hemoglobin_level`        DECIMAL(4, 1) NULL,
    `has_medical_condition`   BOOLEAN NULL,
    `medical_conditions`      TEXT NULL,
    `is_deferred`             BOOLEAN NULL,
    `deferral_period_days`    INT UNSIGNED NULL,
    `deferral_reason`         TEXT NULL,
    `doctor_clearance`        BOOLEAN NULL DEFAULT 1,
    `remarks`                 TEXT NULL,
    INDEX `donor_health_history_donor_id_check_date_index` (`donor_id`, `check_date`),
    CONSTRAINT `donor_health_donor_foreign` FOREIGN KEY (`donor_id`) REFERENCES `users` (`user_id`),
    CONSTRAINT `donor_health_checked_by_foreign` FOREIGN KEY (`checked_by`) REFERENCES `users` (`user_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------
-- 14. Audit Logs (System actions & changes)
-- --------------------------------------------------------

CREATE TABLE `audit_logs` (
    `log_id`                  BIGINT UNSIGNED NOT NULL AUTO_INCREMENT PRIMARY KEY,
    `table_name`              VARCHAR(50) NOT NULL,
    `record_id`               INT UNSIGNED NOT NULL,
    `action`                  ENUM('INSERT', 'UPDATE', 'DELETE') NOT NULL,
    `old_values`              JSON NULL,
    `new_values`              JSON NULL,
    `changed_by`              INT UNSIGNED NOT NULL,
    `changed_at`              TIMESTAMP NULL DEFAULT CURRENT_TIMESTAMP(),
    `ip_address`              VARCHAR(45) NULL,
    `user_agent`              TEXT NULL,
    INDEX `audit_logs_table_record_index` (`table_name`, `record_id`),
    INDEX `audit_logs_changed_at_index` (`changed_at`),
    CONSTRAINT `audit_logs_changed_by_foreign` FOREIGN KEY (`changed_by`) REFERENCES `users` (`user_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

SET foreign_key_checks = 1;