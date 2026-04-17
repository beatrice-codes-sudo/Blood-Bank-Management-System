<?php
/**
 * Application Configuration
 */

// Application
define('APP_NAME', 'HemoLink');
define('APP_TAGLINE', 'Blood Bank Management System');
define('BASE_URL', '/BMS');

// Roles
define('ROLE_ADMIN', 1);
define('ROLE_HOSPITAL', 2);
define('ROLE_DONOR', 3);

// Session config
ini_set('session.cookie_httponly', 1);
ini_set('session.use_strict_mode', 1);

// Error reporting (disable in production)
error_reporting(E_ALL);
ini_set('display_errors', 1);

// Timezone
date_default_timezone_set('Africa/Nairobi');
