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

/**
 * Helper to safely parse .env file without INI syntax errors
 *
 * @param string $filePath
 * @return array
 */
if (!function_exists('loadEnvFile')) {
    function loadEnvFile(string $filePath): array {
        if (!file_exists($filePath)) return [];
        $lines = file($filePath, FILE_IGNORE_NEW_LINES | FILE_SKIP_EMPTY_LINES);
        $data = [];
        foreach ($lines as $line) {
            $line = trim($line);
            if ($line === '' || $line[0] === '#' || $line[0] === ';') continue;
            $parts = explode('=', $line, 2);
            if (count($parts) === 2) {
                $key = trim($parts[0]);
                $val = trim($parts[1]);
                $val = trim($val, "\"'");
                $data[$key] = $val;
            }
        }
        return $data;
    }
}

// Load environment variables for Paystack & SaaS Plans
$envPath = __DIR__ . '/../.env';
if (file_exists($envPath)) {
    $env = loadEnvFile($envPath);
    define('PAYSTACK_PUBLIC_KEY', $env['PAYSTACK_PUBLIC_KEY'] ?? '');
    define('PAYSTACK_SECRET_KEY', $env['PAYSTACK_SECRET_KEY'] ?? '');
    define('PAYSTACK_CURRENCY', $env['PAYSTACK_CURRENCY'] ?? 'KES');

    // SaaS Plan Prices
    define('PLANS', [
        'Starter' => [
            'name'        => 'Starter (Clinic)',
            'monthly'     => (float)($env['PLAN_STARTER_MONTHLY'] ?? 6000),
            'annual'      => (float)($env['PLAN_STARTER_MONTHLY'] ?? 6000) * 12 * 0.85,
            'max_req'     => 15,
            'emergency'   => false,
            'desc'        => 'Ideal for small clinics and maternal care centers.',
        ],
        'Professional' => [
            'name'        => 'Professional (Hospital)',
            'monthly'     => (float)($env['PLAN_PRO_MONTHLY'] ?? 18000),
            'annual'      => (float)($env['PLAN_PRO_MONTHLY'] ?? 18000) * 12 * 0.85,
            'max_req'     => 'Unlimited',
            'emergency'   => true,
            'desc'        => 'For private & county hospitals requiring emergency priority.',
        ],
        'Enterprise' => [
            'name'        => 'Enterprise (Network)',
            'monthly'     => (float)($env['PLAN_ENTERPRISE_MONTHLY'] ?? 45000),
            'annual'      => (float)($env['PLAN_ENTERPRISE_MONTHLY'] ?? 45000) * 12 * 0.85,
            'max_req'     => 'Unlimited',
            'emergency'   => true,
            'desc'        => 'For major referral hospitals & regional healthcare networks.',
        ],
    ]);
}
