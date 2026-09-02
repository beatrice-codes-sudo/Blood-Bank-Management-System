-- ================================================================
-- HemoLink BMS - Consolidated Seed Data (Reset & Populate)
-- Version: 2.0
-- Matches the consolidated schema (all 14 tables)
-- Default Passwords:
--   Admin:     admin123
--   Hospitals: hospital123
--   Donors:    donor123
-- ================================================================

USE `bms_db`;
SET foreign_key_checks = 0;

-- Clean existing data
DELETE FROM `audit_logs`;
DELETE FROM `distributions`;
DELETE FROM `payments`;
DELETE FROM `request_items`;
DELETE FROM `requests`;
DELETE FROM `emergency_appeals`;
DELETE FROM `stock_thresholds`;
DELETE FROM `blood_tests`;
DELETE FROM `appointments`;
DELETE FROM `donor_health_history`;
DELETE FROM `blood_units`;
DELETE FROM `donations`;
DELETE FROM `hospitals`;
DELETE FROM `users`;

-- Reset AUTO_INCREMENT counters
ALTER TABLE `audit_logs` AUTO_INCREMENT = 1;
ALTER TABLE `distributions` AUTO_INCREMENT = 1;
ALTER TABLE `payments` AUTO_INCREMENT = 1;
ALTER TABLE `request_items` AUTO_INCREMENT = 1;
ALTER TABLE `requests` AUTO_INCREMENT = 1;
ALTER TABLE `emergency_appeals` AUTO_INCREMENT = 1;
ALTER TABLE `stock_thresholds` AUTO_INCREMENT = 1;
ALTER TABLE `blood_tests` AUTO_INCREMENT = 1;
ALTER TABLE `appointments` AUTO_INCREMENT = 1;
ALTER TABLE `donor_health_history` AUTO_INCREMENT = 1;
ALTER TABLE `blood_units` AUTO_INCREMENT = 1;
ALTER TABLE `donations` AUTO_INCREMENT = 1;
ALTER TABLE `hospitals` AUTO_INCREMENT = 1;
ALTER TABLE `users` AUTO_INCREMENT = 1;

-- --------------------------------------------------------
-- 1. Users (1 admin + 3 hospital managers + 6 donors)
-- --------------------------------------------------------
INSERT INTO `users` (`user_id`, `role`, `username`, `email`, `password_hash`, `first_name`, `last_name`, `phone`, `is_active`,
                     `blood_type`, `date_of_birth`, `gender`, `address`, `city`, `eligibility_status`) VALUES
-- Admin
(1,  'Admin',    'admin',          'admin@hemolink.com',        '$2y$10$VeFyOBuHxJ205g8VAlqiUuSC9XuqzLVY5BvYKe07vSKmD00i7VgQa', 'System',  'Admin',    '+254700000001', 1,
     NULL, NULL, NULL, NULL, NULL, NULL),
-- Hospital Managers
(2,  'Hospital', 'knh.manager',    'manager@knh.go.ke',         '$2y$10$OO3f.S2SkcE/hbshbcB0MO00vxWsu6.iDQ48hZfewGECuTqbeddjy', 'James',   'Mwangi',   '+254711000001', 1,
     NULL, NULL, NULL, NULL, NULL, NULL),
(3,  'Hospital', 'aga.manager',    'manager@agakhan.org',       '$2y$10$OO3f.S2SkcE/hbshbcB0MO00vxWsu6.iDQ48hZfewGECuTqbeddjy', 'Fatuma',  'Hassan',   '+254711000002', 1,
     NULL, NULL, NULL, NULL, NULL, NULL),
(4,  'Hospital', 'mp.manager',     'manager@mpshahospital.com', '$2y$10$OO3f.S2SkcE/hbshbcB0MO00vxWsu6.iDQ48hZfewGECuTqbeddjy', 'Peter',   'Kimani',   '+254711000003', 1,
     NULL, NULL, NULL, NULL, NULL, NULL),
-- Donors (with inline donor-specific fields)
(5,  'Donor',    'brian.otieno',   'brian.otieno@gmail.com',     '$2y$10$iCCHeVlNqVKJV3v0rFmVE.q5YRROrduqnsQNyX7YCGBFrdiOdjYfy', 'Brian',   'Otieno',   '+254722001001', 1,
     'O+',  '1990-03-15', 'Male',   'Tom Mboya Street, CBD',    'Nairobi', 'Eligible'),
(6,  'Donor',    'grace.wanjiku',  'grace.wanjiku@gmail.com',   '$2y$10$iCCHeVlNqVKJV3v0rFmVE.q5YRROrduqnsQNyX7YCGBFrdiOdjYfy', 'Grace',   'Wanjiku',  '+254722001002', 1,
     'A+',  '1995-07-22', 'Female', 'Westlands, Ring Road',     'Nairobi', 'Eligible'),
(7,  'Donor',    'john.kamau',     'john.kamau@gmail.com',      '$2y$10$iCCHeVlNqVKJV3v0rFmVE.q5YRROrduqnsQNyX7YCGBFrdiOdjYfy', 'John',    'Kamau',    '+254722001003', 1,
     'B+',  '1988-11-08', 'Male',   'Kiambu Road, Ridgeways',   'Nairobi', 'Eligible'),
(8,  'Donor',    'alice.njeri',    'alice.njeri@gmail.com',     '$2y$10$iCCHeVlNqVKJV3v0rFmVE.q5YRROrduqnsQNyX7YCGBFrdiOdjYfy', 'Alice',   'Njeri',    '+254722001004', 1,
     'AB+', '1992-05-30', 'Female', 'Ngong Road, Karen',        'Nairobi', 'Deferred'),
(9,  'Donor',    'david.odhiambo', 'david.odhiambo@gmail.com',  '$2y$10$iCCHeVlNqVKJV3v0rFmVE.q5YRROrduqnsQNyX7YCGBFrdiOdjYfy', 'David',   'Odhiambo', '+254722001005', 1,
     'O-',  '1985-09-18', 'Male',   'Mombasa Road, Industrial', 'Nairobi', 'Eligible'),
(10, 'Donor',    'mary.akinyi',    'mary.akinyi@gmail.com',     '$2y$10$iCCHeVlNqVKJV3v0rFmVE.q5YRROrduqnsQNyX7YCGBFrdiOdjYfy', 'Mary',    'Akinyi',   '+254722001006', 1,
     'A-',  '1998-01-25', 'Female', 'Thika Road, Roysambu',     'Nairobi', 'Eligible');

-- --------------------------------------------------------
-- 2. Hospitals
-- --------------------------------------------------------
INSERT INTO `hospitals` (`hospital_id`, `user_id`, `hospital_name`, `hospital_code`, `address`, `city`, `region`, `postal_code`, `phone`, `email`, `license_number`, `is_active`, `subscription_plan`, `billing_interval`, `subscription_expires_at`) VALUES
(1, 2, 'Kenyatta National Hospital',   'KNH-001', 'Hospital Road, Upper Hill',    'Nairobi', 'Nairobi', '00202', '+254202726300', 'info@knh.go.ke',         'KNH/LIC/2020/001', 1, 'Enterprise',   'Annual',  DATE_ADD(NOW(), INTERVAL 365 DAY)),
(2, 3, 'Aga Khan University Hospital', 'AKH-002', 'Third Parklands Avenue',        'Nairobi', 'Nairobi', '00610', '+254203662000', 'info@agakhan.org',       'AKH/LIC/2020/002', 1, 'Professional', 'Monthly', DATE_ADD(NOW(), INTERVAL 30 DAY)),
(3, 4, 'M.P. Shah Hospital',           'MPS-003', 'Shivachi Road, Parklands',      'Nairobi', 'Nairobi', '00600', '+254203748000', 'info@mpshahospital.com', 'MPS/LIC/2020/003', 1, 'Starter',      'Monthly', NULL);

-- Update users hospital_id links
UPDATE `users` SET `hospital_id` = 1 WHERE `user_id` = 2;
UPDATE `users` SET `hospital_id` = 2 WHERE `user_id` = 3;
UPDATE `users` SET `hospital_id` = 3 WHERE `user_id` = 4;

-- --------------------------------------------------------
-- 3. Stock Safety Thresholds
-- --------------------------------------------------------
INSERT INTO `stock_thresholds` (`blood_type`, `min_threshold`) VALUES
('O-',  8),
('O+',  8),
('A+',  5),
('B+',  5),
('A-',  4),
('B-',  4),
('AB+', 3),
('AB-', 3);

-- --------------------------------------------------------
-- 4. Emergency Appeals (Live Mobilization Sample)
-- --------------------------------------------------------
INSERT INTO `emergency_appeals` (`appeal_id`, `blood_type`, `urgency`, `message`, `is_active`, `created_by`, `created_at`) VALUES
(1, 'A-', 'Critical Shortage (Code Red)', 'URGENT BLOOD APPEAL: Central Blood Bank is experiencing a critical shortage of A- reserves. Your donation can save lives today. Please book an appointment or visit a donation centre.', 1, 1, NOW());

-- --------------------------------------------------------
-- 5. Donations
-- --------------------------------------------------------
INSERT INTO `donations` (`donation_id`, `donor_id`, `blood_type`, `donation_date`, `volume_ml`, `status`, `donation_center`) VALUES
(1,  5,  'O+',  '2024-03-10', 450, 'Completed', 'Nairobi Blood Bank Centre'),
(2,  5,  'O+',  '2024-06-15', 450, 'Completed', 'Nairobi Blood Bank Centre'),
(3,  5,  'O+',  '2024-09-20', 450, 'Completed', 'Kenyatta National Hospital'),
(4,  5,  'O+',  '2024-12-10', 450, 'Completed', 'Kenyatta National Hospital'),
(5,  6,  'A+',  '2024-05-18', 450, 'Completed', 'Nairobi Blood Bank Centre'),
(6,  6,  'A+',  '2024-09-22', 450, 'Completed', 'Nairobi Blood Bank Centre'),
(7,  6,  'A+',  '2025-01-20', 450, 'Completed', 'Aga Khan Hospital'),
(8,  7,  'B+',  '2024-02-14', 450, 'Completed', 'Nairobi Blood Bank Centre'),
(9,  7,  'B+',  '2024-05-20', 450, 'Completed', 'Nairobi Blood Bank Centre'),
(10, 7,  'B+',  '2024-08-28', 450, 'Completed', 'Kenyatta National Hospital'),
(11, 7,  'B+',  '2024-11-15', 450, 'Completed', 'Nairobi Blood Bank Centre'),
(12, 7,  'B+',  '2025-02-14', 450, 'Completed', 'Nairobi Blood Bank Centre'),
(13, 8,  'AB+', '2024-08-10', 450, 'Completed', 'Nairobi Blood Bank Centre'),
(14, 8,  'AB+', '2025-01-05', 450, 'Completed', 'Nairobi Blood Bank Centre'),
(15, 9,  'O-',  '2024-01-15', 450, 'Completed', 'Nairobi Blood Bank Centre'),
(16, 9,  'O-',  '2024-04-20', 450, 'Completed', 'Kenyatta National Hospital'),
(17, 9,  'O-',  '2024-07-10', 450, 'Completed', 'Nairobi Blood Bank Centre'),
(18, 9,  'O-',  '2024-10-05', 450, 'Completed', 'Aga Khan Hospital'),
(19, 9,  'O-',  '2025-01-08', 450, 'Completed', 'Nairobi Blood Bank Centre'),
(20, 9,  'O-',  '2025-03-01', 450, 'Completed', 'Kenyatta National Hospital');

-- --------------------------------------------------------
-- 6. Blood Units (Inventory)
-- --------------------------------------------------------
INSERT INTO `blood_units` (`unit_id`, `blood_type`, `donation_id`, `status`, `collection_date`, `expiry_date`, `volume_ml`) VALUES
(1,  'A+',  5,    'Available', '2025-02-18', '2025-04-01', 450),
(2,  'A+',  6,    'Available', '2025-02-22', '2025-04-05', 450),
(3,  'A+',  7,    'Available', '2025-03-01', '2025-04-12', 450),
(4,  'A+',  NULL, 'Available', '2025-03-10', '2025-04-21', 450),
(5,  'A+',  NULL, 'Reserved',  '2025-03-15', '2025-04-26', 450),
(6,  'A+',  NULL, 'Dispatched','2025-02-10', '2025-03-24', 450),
(7,  'A-',  NULL, 'Available', '2025-03-01', '2025-04-12', 450),
(8,  'A-',  NULL, 'Available', '2025-03-05', '2025-04-16', 450),
(9,  'A-',  NULL, 'Reserved',  '2025-03-12', '2025-04-23', 450),
(10, 'B+',  8,    'Available', '2025-02-14', '2025-03-28', 450),
(11, 'B+',  9,    'Available', '2025-02-20', '2025-04-03', 450),
(12, 'B+',  10,   'Available', '2025-03-01', '2025-04-12', 450),
(13, 'B+',  11,   'Available', '2025-03-10', '2025-04-21', 450),
(14, 'B+',  12,   'Available', '2025-03-15', '2025-04-26', 450),
(15, 'B+',  NULL, 'Dispatched','2025-02-20', '2025-04-03', 450),
(16, 'B-',  NULL, 'Available', '2025-02-28', '2025-04-11', 450),
(17, 'B-',  NULL, 'Available', '2025-03-18', '2025-04-29', 450),
(18, 'AB+', 13,   'Available', '2025-02-10', '2025-03-24', 450),
(19, 'AB+', 14,   'Available', '2025-02-25', '2025-04-08', 450),
(20, 'AB+', NULL, 'Available', '2025-03-05', '2025-04-16', 450),
(21, 'AB+', NULL, 'Reserved',  '2025-03-10', '2025-04-21', 450),
(22, 'AB-', NULL, 'Available', '2025-03-01', '2025-04-12', 450),
(23, 'O+',  1,    'Available', '2025-02-10', '2025-03-24', 450),
(24, 'O+',  2,    'Available', '2025-02-15', '2025-03-29', 450),
(25, 'O+',  3,    'Available', '2025-02-20', '2025-04-03', 450),
(26, 'O+',  4,    'Available', '2025-02-25', '2025-04-08', 450),
(27, 'O+',  NULL, 'Available', '2025-03-01', '2025-04-12', 450),
(28, 'O+',  NULL, 'Available', '2025-03-05', '2025-04-16', 450),
(29, 'O+',  NULL, 'Available', '2025-03-10', '2025-04-21', 450),
(30, 'O+',  NULL, 'Available', '2025-03-15', '2025-04-26', 450),
(31, 'O+',  NULL, 'Reserved',  '2025-03-18', '2025-04-29', 450),
(32, 'O-',  15,   'Available', '2025-02-15', '2025-03-29', 450),
(33, 'O-',  16,   'Available', '2025-02-20', '2025-04-03', 450),
(34, 'O-',  17,   'Available', '2025-02-25', '2025-04-08', 450),
(35, 'O-',  18,   'Available', '2025-03-01', '2025-04-12', 450),
(36, 'O-',  19,   'Available', '2025-03-05', '2025-04-16', 450),
(37, 'O-',  20,   'Available', '2025-03-10', '2025-04-21', 450),
(38, 'O-',  NULL, 'Available', '2025-03-12', '2025-04-23', 450),
(39, 'O-',  NULL, 'Available', '2025-03-15', '2025-04-26', 450),
(40, 'O-',  NULL, 'Reserved',  '2025-03-20', '2025-05-01', 450);

-- --------------------------------------------------------
-- 7. Requests (Requisitions with Click & Collect Handover)
-- --------------------------------------------------------
INSERT INTO `requests` (`request_id`, `hospital_id`, `blood_type`, `units_requested`, `units_fulfilled`, `urgency`, `status`, `collection_status`, `release_pin`, `pin_generated_at`, `collected_at`, `collected_by_name`, `collected_by_phone`, `dispatched_at`, `received_at`, `temp_verified`, `notes`, `requested_by`) VALUES
(1,  1, 'O+',  5, 5, 'Normal',    'Fulfilled',           'Received',         '748291', DATE_SUB(NOW(), INTERVAL 3 DAY), DATE_SUB(NOW(), INTERVAL 2 DAY), 'John Mwangi (Driver)', '+254711223344', DATE_SUB(NOW(), INTERVAL 2 DAY), DATE_SUB(NOW(), INTERVAL 2 DAY), 1, 'Routine surgical stock replenishment',          2),
(2,  1, 'O-',  3, 3, 'Urgent',    'Fulfilled',           'Received',         '392014', DATE_SUB(NOW(), INTERVAL 2 DAY), DATE_SUB(NOW(), INTERVAL 1 DAY), 'Peter Omondi (Lab)',   '+254722334455', DATE_SUB(NOW(), INTERVAL 1 DAY), DATE_SUB(NOW(), INTERVAL 1 DAY), 1, 'Emergency trauma unit - O neg needed urgently', 2),
(3,  1, 'A+',  4, 2, 'Normal',    'Partially Fulfilled', 'Dispatched',       '883920', DATE_SUB(NOW(), INTERVAL 1 DAY), NULL,                             NULL,                  NULL,            DATE_SUB(NOW(), INTERVAL 4 HOUR),NULL,                             1, 'Maternity ward top-up',                         2),
(4,  1, 'B+',  6, 0, 'Normal',    'Pending',             'Pending',          NULL,     NULL,                             NULL,                             NULL,                  NULL,            NULL,                              NULL,                             1, 'Elective surgery scheduled next week',          2),
(5,  1, 'AB+', 2, 0, 'Emergency', 'Pending',             'Pending',          NULL,     NULL,                             NULL,                             NULL,                  NULL,            NULL,                              NULL,                             1, 'ICU patient - rare type needed immediately',    2),
(6,  2, 'O+',  8, 8, 'Normal',    'Fulfilled',           'Received',         '502914', DATE_SUB(NOW(), INTERVAL 5 DAY), DATE_SUB(NOW(), INTERVAL 4 DAY), 'Samuel Kiprono',      '+254733445566', DATE_SUB(NOW(), INTERVAL 4 DAY), DATE_SUB(NOW(), INTERVAL 4 DAY), 1, 'General ward stock',                            3),
(7,  2, 'A+',  3, 3, 'Urgent',    'Processing',          'Ready for Pickup', '491028', DATE_SUB(NOW(), INTERVAL 2 HOUR),NULL,                             NULL,                  NULL,            NULL,                              NULL,                             1, 'Paediatric surgery preparation',                3),
(8,  2, 'O-',  2, 2, 'Emergency', 'Fulfilled',           'Received',         '672819', DATE_SUB(NOW(), INTERVAL 1 DAY), DATE_SUB(NOW(), INTERVAL 8 HOUR),'Hassan Ali (Ambulance)','+254711889900', DATE_SUB(NOW(), INTERVAL 8 HOUR),DATE_SUB(NOW(), INTERVAL 8 HOUR),1, 'Emergency caesarean section',                   3),
(9,  2, 'A-',  1, 0, 'Normal',    'Rejected',            'Cancelled',        NULL,     NULL,                             NULL,                             NULL,                  NULL,            NULL,                              NULL,                             1, 'Stock depleted at time of requisition',          3),
(10, 3, 'B+',  4, 4, 'Normal',    'Fulfilled',           'Received',         '192840', DATE_SUB(NOW(), INTERVAL 6 DAY), DATE_SUB(NOW(), INTERVAL 5 DAY), 'Daniel Wekesa',        '+254700112233', DATE_SUB(NOW(), INTERVAL 5 DAY), DATE_SUB(NOW(), INTERVAL 5 DAY), 1, 'Dialysis unit monthly requirement',             4),
(11, 3, 'O+',  5, 0, 'Urgent',    'Pending',             'Pending',          NULL,     NULL,                             NULL,                             NULL,                  NULL,            NULL,                              NULL,                             1, 'Oncology ward - chemotherapy support',          4),
(12, 3, 'AB-', 2, 0, 'Emergency', 'Pending',             'Pending',          NULL,     NULL,                             NULL,                             NULL,                  NULL,            NULL,                              NULL,                             1, 'Rare blood type for scheduled transplant',      4);

-- --------------------------------------------------------
-- 8. Payments (Paystack Subscriptions)
-- --------------------------------------------------------
INSERT INTO `payments` (`payment_id`, `hospital_id`, `reference`, `amount`, `currency`, `plan_name`, `billing_interval`, `channel`, `status`, `paystack_response`, `paid_at`, `created_at`) VALUES
(1, 1, 'HLK_SUB_1_1710000001', 150000.00, 'KES', 'Enterprise',   'Annual',  'card', 'Success', '{"status":true,"message":"Verification successful","data":{"status":"success","reference":"HLK_SUB_1_1710000001","amount":15000000,"currency":"KES","channel":"card"}}', NOW(), NOW()),
(2, 2, 'HLK_SUB_2_1710000002', 15000.00,  'KES', 'Professional', 'Monthly', 'card', 'Success', '{"status":true,"message":"Verification successful","data":{"status":"success","reference":"HLK_SUB_2_1710000002","amount":1500000,"currency":"KES","channel":"card"}}',  NOW(), NOW());

-- --------------------------------------------------------
-- 9. Appointments
-- --------------------------------------------------------
INSERT INTO `appointments` (`appointment_id`, `donor_id`, `hospital_id`, `appointment_date`, `appointment_time`, `purpose`, `status`, `notes`, `created_by`) VALUES
(1, 5,  NULL, DATE_ADD(CURRENT_DATE, INTERVAL 2 DAY), '09:00:00', 'Donation',     'Scheduled',  'Regular quarterly donation',        1),
(2, 6,  NULL, DATE_ADD(CURRENT_DATE, INTERVAL 3 DAY), '10:30:00', 'Donation',     'Scheduled',  'Follow up from emergency appeal',   1),
(3, 7,  NULL, DATE_ADD(CURRENT_DATE, INTERVAL 5 DAY), '08:00:00', 'Donation',     'Scheduled',  'High frequency donor - priority',   1),
(4, 9,  NULL, DATE_ADD(CURRENT_DATE, INTERVAL 1 DAY), '11:00:00', 'Donation',     'Scheduled',  'Universal donor - urgent needed',   1),
(5, 10, NULL, DATE_ADD(CURRENT_DATE, INTERVAL 4 DAY), '14:00:00', 'Health Check', 'Scheduled',  'First time donor screening',        1),
(6, 8,  NULL, DATE_SUB(CURRENT_DATE, INTERVAL 10 DAY),'09:30:00', 'Consultation', 'Completed',  'Deferral review - BP elevated',     1),
(7, 5,  NULL, DATE_SUB(CURRENT_DATE, INTERVAL 90 DAY),'09:00:00', 'Donation',     'Completed',  '',                                  1),
(8, 7,  NULL, DATE_SUB(CURRENT_DATE, INTERVAL 85 DAY),'08:00:00', 'Donation',     'Completed',  '',                                  1),
(9, 9,  NULL, DATE_SUB(CURRENT_DATE, INTERVAL 60 DAY),'10:00:00', 'Donation',     'Completed',  'O-neg emergency donation processed',1),
(10,6,  NULL, DATE_SUB(CURRENT_DATE, INTERVAL 15 DAY),'11:30:00', 'Donation',     'Cancelled',  'Donor called to cancel - flu',      1);

-- --------------------------------------------------------
-- 10. Donor Health History
-- --------------------------------------------------------
INSERT INTO `donor_health_history` (`donor_id`, `donation_id`, `checked_by`, `check_date`, `weight_kg`, `blood_pressure_systolic`, `blood_pressure_diastolic`, `pulse_rate`, `temperature`, `hemoglobin_level`, `has_medical_condition`, `is_deferred`, `doctor_clearance`, `remarks`) VALUES
(5,  4,  1, '2024-12-10', 75.5, 118, 76, 68, 36.6, 14.2, 0, 0, 1, 'Healthy. Cleared for donation.'),
(6,  7,  1, '2025-01-20', 62.0, 112, 70, 72, 36.5, 13.8, 0, 0, 1, 'All vitals normal.'),
(7,  12, 1, '2025-02-14', 80.0, 120, 78, 65, 36.7, 15.0, 0, 0, 1, 'Experienced donor. No issues.'),
(8,  14, 1, '2025-01-05', 58.0, 145, 92, 88, 37.1, 12.5, 1, 1, 0, 'Elevated BP. Deferred for 56 days. Advised to monitor blood pressure.'),
(9,  20, 1, '2025-03-01', 85.0, 115, 74, 62, 36.4, 16.1, 0, 0, 1, 'Universal donor. Excellent health.'),
(10, NULL,1,'2025-03-22', 55.0, 108, 68, 78, 36.8, 13.2, 0, 0, 1, 'Pre-screening for first donation. Approved.');

-- --------------------------------------------------------
-- 11. Request Items
-- --------------------------------------------------------
INSERT INTO `request_items` (`request_item_id`, `request_id`, `blood_type`, `component`, `units_requested`, `units_fulfilled`, `special_requirements`) VALUES
(1, 1, 'O+',  'Whole Blood', 5, 5, 'Standard storage temp (2-6°C)'),
(2, 2, 'O-',  'Whole Blood', 3, 3, 'Emergency trauma crossmatch ready'),
(3, 3, 'A+',  'Whole Blood', 4, 2, 'Leukoreduced preferred');

-- --------------------------------------------------------
-- 12. Distributions
-- --------------------------------------------------------
INSERT INTO `distributions` (`distribution_id`, `request_id`, `hospital_id`, `dispatched_by`, `dispatched_date`, `transport_method`, `transport_temperature`, `receiver_name`, `receiver_id`, `delivery_notes`, `is_delivered`, `delivered_at`) VALUES
(1, 1, 1, 1, DATE_SUB(NOW(), INTERVAL 2 DAY), 'Hospital Pickup', 4.2, 'John Mwangi',   'EMP-KNH-882', 'Intact cold chain verified. Handover via PIN 748291.', 1, DATE_SUB(NOW(), INTERVAL 2 DAY)),
(2, 2, 1, 1, DATE_SUB(NOW(), INTERVAL 1 DAY), 'Ambulance',       3.8, 'Peter Omondi',  'EMP-KNH-910', 'Emergency dispatch. Verified intact handover.',         1, DATE_SUB(NOW(), INTERVAL 1 DAY)),
(3, 6, 2, 1, DATE_SUB(NOW(), INTERVAL 4 DAY), 'Courier',         4.0, 'Samuel Kiprono','EMP-AKH-402', 'Routine batch delivery.',                               1, DATE_SUB(NOW(), INTERVAL 4 DAY));

SET foreign_key_checks = 1;

-- ================================================================
-- Credentials Reference
-- ----------------------------------------------------------------
-- User Type       Username / Email Example         Password
-- System Admin    admin / admin@hemolink.com        admin123
-- Hospitals       knh.manager, aga.manager          hospital123
-- Donors          brian.otieno, grace.wanjiku       donor123
-- ================================================================