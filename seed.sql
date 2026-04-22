-- ================================================================
-- HemoLink BMS - Master Seed Data (Reset & Populate)
-- Passwords: admin=admin123 | hospitals=hospital123 | donors=donor123
-- ================================================================

USE `bms_db`;
SET foreign_key_checks = 0;



-- --------------------------------------------------------
-- 1. Roles
-- --------------------------------------------------------
INSERT INTO `roles` (`role_id`, `role_name`, `role_description`) VALUES
(1, 'admin', 'System Administrator - Full access to all modules'),
(2, 'hospital_manager', 'Hospital Manager - Manages hospital blood requests'),
(3, 'donor', 'Donor - Manages personal donation records');

-- --------------------------------------------------------
-- 2. Blood Types
-- --------------------------------------------------------
INSERT INTO `blood_types` (`blood_type_id`, `type_name`) VALUES
(1, 'A+'), (2, 'A-'), (3, 'B+'), (4, 'B-'),
(5, 'AB+'), (6, 'AB-'), (7, 'O+'), (8, 'O-');

-- --------------------------------------------------------
-- 3. Components
-- --------------------------------------------------------
INSERT INTO `components` (`component_id`, `component_name`) VALUES
(1, 'Whole Blood'), (2, 'Red Blood Cells'), (3, 'Platelets'),
(4, 'Plasma'), (5, 'Cryoprecipitate');

-- --------------------------------------------------------
-- 4. Users (1 admin + 3 hospital managers + 6 donors)
-- --------------------------------------------------------
INSERT INTO `users` (`user_id`, `role_id`, `username`, `email`, `password_hash`, `first_name`, `last_name`, `phone`, `is_active`) VALUES
-- Admin
(1,  1, 'admin',         'admin@hemolink.com',        '$2y$10$VeFyOBuHxJ205g8VAlqiUuSC9XuqzLVY5BvYKe07vSKmD00i7VgQa', 'System',   'Admin',    '+254700000001', 1),
-- Hospital Managers
(2,  2, 'knh.manager',   'manager@knh.go.ke',         '$2y$10$OO3f.S2SkcE/hbshbcB0MO00vxWsu6.iDQ48hZfewGECuTqbeddjy', 'James',    'Mwangi',   '+254711000001', 1),
(3,  2, 'aga.manager',   'manager@agakhan.org',       '$2y$10$OO3f.S2SkcE/hbshbcB0MO00vxWsu6.iDQ48hZfewGECuTqbeddjy', 'Fatuma',   'Hassan',   '+254711000002', 1),
(4,  2, 'mp.manager',    'manager@mpshahospital.com', '$2y$10$OO3f.S2SkcE/hbshbcB0MO00vxWsu6.iDQ48hZfewGECuTqbeddjy', 'Peter',    'Kimani',   '+254711000003', 1),
-- Donors
(5,  3, 'brian.otieno',  'brian.otieno@gmail.com',    '$2y$10$iCCHeVlNqVKJV3v0rFmVE.q5YRROrduqnsQNyX7YCGBFrdiOdjYfy', 'Brian',    'Otieno',   '+254722001001', 1),
(6,  3, 'grace.wanjiku', 'grace.wanjiku@gmail.com',   '$2y$10$iCCHeVlNqVKJV3v0rFmVE.q5YRROrduqnsQNyX7YCGBFrdiOdjYfy', 'Grace',    'Wanjiku',  '+254722001002', 1),
(7,  3, 'john.kamau',    'john.kamau@gmail.com',      '$2y$10$iCCHeVlNqVKJV3v0rFmVE.q5YRROrduqnsQNyX7YCGBFrdiOdjYfy', 'John',     'Kamau',    '+254722001003', 1),
(8,  3, 'alice.njeri',   'alice.njeri@gmail.com',     '$2y$10$iCCHeVlNqVKJV3v0rFmVE.q5YRROrduqnsQNyX7YCGBFrdiOdjYfy', 'Alice',    'Njeri',    '+254722001004', 1),
(9,  3, 'david.odhiambo','david.odhiambo@gmail.com',  '$2y$10$iCCHeVlNqVKJV3v0rFmVE.q5YRROrduqnsQNyX7YCGBFrdiOdjYfy', 'David',    'Odhiambo', '+254722001005', 1),
(10, 3, 'mary.akinyi',   'mary.akinyi@gmail.com',     '$2y$10$iCCHeVlNqVKJV3v0rFmVE.q5YRROrduqnsQNyX7YCGBFrdiOdjYfy', 'Mary',     'Akinyi',   '+254722001006', 1);

-- --------------------------------------------------------
-- 5. Hospitals
-- --------------------------------------------------------
INSERT INTO `hospitals` (`hospital_id`, `user_id`, `hospital_name`, `hospital_code`, `address`, `city`, `region`, `postal_code`, `phone`, `email`, `license_number`, `is_active`) VALUES
(1, 2, 'Kenyatta National Hospital',  'KNH-001', 'Hospital Road, Upper Hill',    'Nairobi',  'Nairobi', '00202', '+254202726300', 'info@knh.go.ke',            'KNH/LIC/2020/001', 1),
(2, 3, 'Aga Khan University Hospital','AKH-002', 'Third Parklands Avenue',        'Nairobi',  'Nairobi', '00610', '+254203662000', 'info@agakhan.org',          'AKH/LIC/2020/002', 1),
(3, 4, 'M.P. Shah Hospital',          'MPS-003', 'Shivachi Road, Parklands',      'Nairobi',  'Nairobi', '00600', '+254203748000', 'info@mpshahospital.com',    'MPS/LIC/2020/003', 1);

-- --------------------------------------------------------
-- 6. Donors
-- --------------------------------------------------------
INSERT INTO `donors` (`donor_id`, `user_id`, `first_name`, `last_name`, `blood_type_id`, `date_of_birth`, `gender`, `address`, `city`, `eligibility_status`, `last_donation_date`, `total_donations`) VALUES
(1, 5,  'Brian',  'Otieno',   7, '1990-03-15', 'Male',   'Tom Mboya Street, CBD',    'Nairobi', 'Eligible',           '2024-12-10', 5),
(2, 6,  'Grace',  'Wanjiku',  1, '1995-07-22', 'Female', 'Westlands, Ring Road',     'Nairobi', 'Eligible',           '2025-01-20', 3),
(3, 7,  'John',   'Kamau',    3, '1988-11-08', 'Male',   'Kiambu Road, Ridgeways',   'Nairobi', 'Eligible',           '2025-02-14', 8),
(4, 8,  'Alice',  'Njeri',    5, '1992-05-30', 'Female', 'Ngong Road, Karen',        'Nairobi', 'Deferred',           '2025-01-05', 2),
(5, 9,  'David',  'Odhiambo', 8, '1985-09-18', 'Male',   'Mombasa Road, Industrial', 'Nairobi', 'Eligible',           '2025-03-01', 12),
(6, 10, 'Mary',   'Akinyi',   2, '1998-01-25', 'Female', 'Thika Road, Roysambu',     'Nairobi', 'Eligible',           NULL,         0);

-- --------------------------------------------------------
-- 7. Donations
-- --------------------------------------------------------
INSERT INTO `donations` (`donation_id`, `donor_id`, `blood_type_id`, `donation_date`, `volume_ml`, `status`, `donation_center`) VALUES
(1,  1, 7, '2024-03-10', 450, 'Completed', 'Nairobi Blood Bank Centre'),
(2,  1, 7, '2024-06-15', 450, 'Completed', 'Nairobi Blood Bank Centre'),
(3,  1, 7, '2024-09-20', 450, 'Completed', 'Kenyatta National Hospital'),
(4,  1, 7, '2024-12-10', 450, 'Completed', 'Kenyatta National Hospital'),
(5,  2, 1, '2024-05-18', 450, 'Completed', 'Nairobi Blood Bank Centre'),
(6,  2, 1, '2024-09-22', 450, 'Completed', 'Nairobi Blood Bank Centre'),
(7,  2, 1, '2025-01-20', 450, 'Completed', 'Aga Khan Hospital'),
(8,  3, 3, '2024-02-14', 450, 'Completed', 'Nairobi Blood Bank Centre'),
(9,  3, 3, '2024-05-20', 450, 'Completed', 'Nairobi Blood Bank Centre'),
(10, 3, 3, '2024-08-28', 450, 'Completed', 'Kenyatta National Hospital'),
(11, 3, 3, '2024-11-15', 450, 'Completed', 'Nairobi Blood Bank Centre'),
(12, 3, 3, '2025-02-14', 450, 'Completed', 'Nairobi Blood Bank Centre'),
(13, 4, 5, '2024-08-10', 450, 'Completed', 'Nairobi Blood Bank Centre'),
(14, 4, 5, '2025-01-05', 450, 'Completed', 'Nairobi Blood Bank Centre'),
(15, 5, 8, '2024-01-15', 450, 'Completed', 'Nairobi Blood Bank Centre'),
(16, 5, 8, '2024-04-20', 450, 'Completed', 'Kenyatta National Hospital'),
(17, 5, 8, '2024-07-10', 450, 'Completed', 'Nairobi Blood Bank Centre'),
(18, 5, 8, '2024-10-05', 450, 'Completed', 'Aga Khan Hospital'),
(19, 5, 8, '2025-01-08', 450, 'Completed', 'Nairobi Blood Bank Centre'),
(20, 5, 8, '2025-03-01', 450, 'Completed', 'Kenyatta National Hospital');

-- --------------------------------------------------------
-- 8. Blood Units
-- --------------------------------------------------------
INSERT INTO `blood_units` (`unit_id`, `blood_type_id`, `donation_id`, `status`, `collection_date`, `expiry_date`, `volume_ml`) VALUES
(1,  1, 5,    'Available', '2024-05-18', '2024-07-17', 450),
(2,  1, 6,    'Available', '2024-09-22', '2024-11-21', 450),
(3,  1, 7,    'Available', '2025-01-20', '2025-03-21', 450),
(4,  1, NULL, 'Available', '2025-02-01', '2025-04-02', 450),
(5,  1, NULL, 'Available', '2025-02-10', '2025-04-11', 450),
(6,  1, NULL, 'Reserved',  '2025-03-01', '2025-04-30', 450),
(7,  1, NULL, 'Available', '2025-03-12', '2025-05-11', 450),
(8,  1, NULL, 'Available', '2025-03-20', '2025-05-19', 450),
(9,  2, NULL, 'Available', '2025-01-10', '2025-03-11', 450),
(10, 2, NULL, 'Available', '2025-02-15', '2025-04-16', 450),
(11, 2, NULL, 'Reserved',  '2025-03-05', '2025-05-04', 450),
(12, 3, 8,    'Available', '2024-02-14', '2024-04-14', 450),
(13, 3, 9,    'Available', '2024-05-20', '2024-07-19', 450),
(14, 3, 10,   'Available', '2024-08-28', '2024-10-27', 450),
(15, 3, 11,   'Available', '2024-11-15', '2025-01-14', 450),
(16, 3, 12,   'Available', '2025-02-14', '2025-04-15', 450),
(17, 3, NULL, 'Available', '2025-02-20', '2025-04-21', 450),
(18, 3, NULL, 'Available', '2025-03-01', '2025-04-30', 450),
(19, 3, NULL, 'Dispatched','2025-03-10', '2025-05-09', 450),
(20, 3, NULL, 'Available', '2025-03-15', '2025-05-14', 450),
(21, 3, NULL, 'Available', '2025-03-22', '2025-05-21', 450),
(22, 4, NULL, 'Available', '2025-02-28', '2025-04-29', 450),
(23, 4, NULL, 'Available', '2025-03-18', '2025-05-17', 450),
(24, 5, 13,   'Available', '2024-08-10', '2024-10-09', 450),
(25, 5, 14,   'Available', '2025-01-05', '2025-03-06', 450),
(26, 5, NULL, 'Available', '2025-02-08', '2025-04-09', 450),
(27, 5, NULL, 'Available', '2025-02-25', '2025-04-26', 450),
(28, 5, NULL, 'Reserved',  '2025-03-10', '2025-05-09', 450),
(29, 5, NULL, 'Available', '2025-03-20', '2025-05-19', 450),
(30, 6, NULL, 'Available', '2025-03-01', '2025-04-30', 450),
(31, 7, 1,    'Available', '2024-03-10', '2024-05-09', 450),
(32, 7, 2,    'Available', '2024-06-15', '2024-08-14', 450),
(33, 7, 3,    'Available', '2024-09-20', '2024-11-19', 450),
(34, 7, 4,    'Available', '2024-12-10', '2025-02-08', 450),
(35, 7, NULL, 'Available', '2025-01-05', '2025-03-06', 450),
(36, 7, NULL, 'Available', '2025-01-20', '2025-03-21', 450),
(37, 7, NULL, 'Available', '2025-02-02', '2025-04-03', 450),
(38, 7, NULL, 'Reserved',  '2025-02-14', '2025-04-15', 450),
(39, 7, NULL, 'Available', '2025-02-22', '2025-04-23', 450),
(40, 7, NULL, 'Available', '2025-03-01', '2025-04-30', 450),
(41, 7, NULL, 'Available', '2025-03-08', '2025-05-07', 450),
(42, 7, NULL, 'Available', '2025-03-12', '2025-05-11', 450),
(43, 7, NULL, 'Dispatched','2025-03-15', '2025-05-14', 450),
(44, 7, NULL, 'Available', '2025-03-19', '2025-05-18', 450),
(45, 7, NULL, 'Available', '2025-03-22', '2025-05-21', 450),
(46, 8, 15,   'Available', '2024-01-15', '2024-03-15', 450),
(47, 8, 16,   'Available', '2024-04-20', '2024-06-19', 450),
(48, 8, 17,   'Available', '2024-07-10', '2024-09-08', 450),
(49, 8, 18,   'Available', '2024-10-05', '2024-12-04', 450),
(50, 8, 19,   'Available', '2025-01-08', '2025-03-09', 450),
(51, 8, 20,   'Available', '2025-03-01', '2025-04-30', 450),
(52, 8, NULL, 'Available', '2025-03-10', '2025-05-09', 450),
(53, 8, NULL, 'Reserved',  '2025-03-15', '2025-05-14', 450),
(54, 8, NULL, 'Available', '2025-03-20', '2025-05-19', 450);

-- --------------------------------------------------------
-- 9. Requests
-- --------------------------------------------------------
INSERT INTO `requests` (`request_id`, `hospital_id`, `blood_type_id`, `units_requested`, `units_fulfilled`, `urgency`, `status`, `notes`, `requested_by`) VALUES
(1,  1, 7, 5, 5, 'Normal',    'Fulfilled',           'Routine surgical stock replenishment',          2),
(2,  1, 8, 3, 3, 'Urgent',    'Fulfilled',           'Emergency trauma unit - O neg needed urgently', 2),
(3,  1, 1, 4, 2, 'Normal',    'Partially Fulfilled', 'Maternity ward top-up',                         2),
(4,  1, 3, 6, 0, 'Normal',    'Pending',             'Elective surgery scheduled next week',          2),
(5,  1, 5, 2, 0, 'Emergency', 'Pending',             'ICU patient - rare type needed immediately',    2),
(6,  2, 7, 8, 8, 'Normal',    'Fulfilled',           'General ward stock',                            3),
(7,  2, 1, 3, 0, 'Urgent',    'Pending',             'Paediatric surgery tomorrow morning',           3),
(8,  2, 8, 2, 2, 'Emergency', 'Fulfilled',           'Emergency caesarean section',                   3),
(9,  2, 2, 1, 0, 'Normal',    'Rejected',            'Stock not available at time of request',        3),
(10, 3, 3, 4, 4, 'Normal',    'Fulfilled',           'Dialysis unit monthly requirement',             4),
(11, 3, 7, 5, 0, 'Urgent',    'Pending',             'Oncology ward - chemotherapy support',          4),
(12, 3, 6, 2, 0, 'Emergency', 'Pending',             'Rare blood type for scheduled transplant',      4);

-- --------------------------------------------------------
-- 10. Appointments
-- --------------------------------------------------------
INSERT INTO `appointments` (`appointment_id`, `donor_id`, `appointment_date`, `appointment_time`, `purpose`, `status`, `notes`, `created_by`) VALUES
(1, 1, '2025-04-25', '09:00:00', 'Donation',     'Scheduled',  'Regular quarterly donation',        1),
(2, 2, '2025-04-26', '10:30:00', 'Donation',     'Scheduled',  'Follow up from last visit',         1),
(3, 3, '2025-04-28', '08:00:00', 'Donation',     'Scheduled',  'High frequency donor - priority',   1),
(4, 5, '2025-04-24', '11:00:00', 'Donation',     'Scheduled',  'Universal donor - urgent needed',   1),
(5, 6, '2025-04-30', '14:00:00', 'Health Check', 'Scheduled',  'First time donor screening',        1),
(6, 4, '2025-04-22', '09:30:00', 'Consultation', 'Completed',  'Deferral review - BP elevated',     1),
(7, 1, '2025-01-15', '09:00:00', 'Donation',     'Completed',  '',                                  1),
(8, 3, '2025-01-20', '08:00:00', 'Donation',     'Completed',  '',                                  1),
(9, 5, '2025-02-10', '10:00:00', 'Donation',     'Completed',  'O-neg emergency donation processed',1),
(10,2, '2025-02-18', '11:30:00', 'Donation',     'Cancelled',  'Donor called to cancel - flu',      1);

-- --------------------------------------------------------
-- 11. Donor Health History
-- --------------------------------------------------------
INSERT INTO `donor_health_history` (`donor_id`, `donation_id`, `checked_by`, `check_date`, `weight_kg`, `blood_pressure_systolic`, `blood_pressure_diastolic`, `pulse_rate`, `temperature`, `hemoglobin_level`, `has_medical_condition`, `is_deferred`, `doctor_clearance`, `remarks`) VALUES
(1, 4,  1, '2024-12-10', 75.5, 118, 76, 68, 36.6, 14.2, 0, 0, 1, 'Healthy. Cleared for donation.'),
(2, 7,  1, '2025-01-20', 62.0, 112, 70, 72, 36.5, 13.8, 0, 0, 1, 'All vitals normal.'),
(3, 12, 1, '2025-02-14', 80.0, 120, 78, 65, 36.7, 15.0, 0, 0, 1, 'Experienced donor. No issues.'),
(4, 14, 1, '2025-01-05', 58.0, 145, 92, 88, 37.1, 12.5, 1, 1, 0, 'Elevated BP. Deferred for 56 days. Advised to monitor blood pressure.'),
(5, 20, 1, '2025-03-01', 85.0, 115, 74, 62, 36.4, 16.1, 0, 0, 1, 'Universal donor. Excellent health.'),
(6, NULL,1,'2025-03-22', 55.0, 108, 68, 78, 36.8, 13.2, 0, 0, 1, 'Pre-screening for first donation. Approved.');

SET foreign_key_checks = 1;


-- User Type	Username / Email Example	Default Password
-- System Admin	admin / admin@hemolink.com	admin123
-- Hospitals	knh.manager, aga.manager	hospital123
-- Donors	brian.otieno, grace.wanjiku	donor123