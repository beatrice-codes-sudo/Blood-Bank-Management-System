-- ================================================================
-- HemoLink BMS - Migration: Add hospital_id to appointments
-- Run this script against bms_db to link appointments to hospitals
-- ================================================================

USE `bms_db`;

-- Add hospital_id column to appointments table
ALTER TABLE `appointments`
    ADD COLUMN `hospital_id` INT UNSIGNED NULL AFTER `donor_id`,
    ADD CONSTRAINT `appointments_hospital_foreign`
        FOREIGN KEY (`hospital_id`) REFERENCES `hospitals` (`hospital_id`);

-- Optionally seed existing appointments with a hospital
UPDATE `appointments` SET `hospital_id` = 1 WHERE `appointment_id` IN (1, 3, 4, 7, 8, 9);
UPDATE `appointments` SET `hospital_id` = 2 WHERE `appointment_id` IN (2, 5, 10);
UPDATE `appointments` SET `hospital_id` = 3 WHERE `appointment_id` = 6;
