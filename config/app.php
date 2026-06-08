<?php
/**
 * Application Configuration
 * this file defines all the constants used in the application
 */

// Application
define('APP_NAME', 'HemoLink');
define('APP_TAGLINE', 'Blood Bank Management System');
define('BASE_URL', '/BMS');

// Roles (match ENUM values in users.role)
define('ROLE_ADMIN', 'Admin');
define('ROLE_HOSPITAL', 'Hospital');
define('ROLE_DONOR', 'Donor');

// Blood Types (replaces blood_types table)
define('BLOOD_TYPES', ['A+', 'A-', 'B+', 'B-', 'AB+', 'AB-', 'O+', 'O-']);

// Blood Components (replaces components table)
define('BLOOD_COMPONENTS', [
    'Whole Blood', 'Red Blood Cells', 'Platelets', 'Plasma', 'Cryoprecipitate'
]);

// Session config
ini_set('session.cookie_httponly', 1);
ini_set('session.use_strict_mode', 1);

// Error reporting (disable in production)
error_reporting(E_ALL);
ini_set('display_errors', 1);

// Timezone
date_default_timezone_set('Africa/Nairobi');
