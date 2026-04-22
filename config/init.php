<?php
/**
 * Application Bootstrap
 * Include this file at the top of index.php to load everything
 */

// Load configuration (must be before session_start for session ini settings)
require_once __DIR__ . '/app.php';

session_start();

require_once __DIR__ . '/database.php';

// Load models
require_once __DIR__ . '/../models/User.php';
require_once __DIR__ . '/../models/Donor.php';
require_once __DIR__ . '/../models/Hospital.php';
require_once __DIR__ . '/../models/BloodInventory.php';

// Load controllers
require_once __DIR__ . '/../controllers/AuthController.php';
require_once __DIR__ . '/../controllers/AdminController.php';
require_once __DIR__ . '/../controllers/HospitalController.php';
require_once __DIR__ . '/../controllers/DonorController.php';

/**
 * Helper: check if user is logged in
 */
function isLoggedIn() {
    return isset($_SESSION['user_id']);
}

/**
 * Helper: get current user role
 */
function getUserRole() {
    return $_SESSION['role_id'] ?? null;
}

/**
 * Helper: require authentication
 */
function requireAuth() {
    if (!isLoggedIn()) {
        header('Location: ' . BASE_URL . '/index.php?page=login');
        exit;
    }
}

/**
 * Helper: require specific role
 */
function requireRole($roleId) {
    requireAuth();
    if (getUserRole() != $roleId) {
        header('Location: ' . BASE_URL . '/index.php?page=unauthorized');
        exit;
    }
}

/**
 * Helper: redirect with flash message
 */
function redirect($page, $message = '', $type = 'success') {
    if ($message) {
        $_SESSION['flash_message'] = $message;
        $_SESSION['flash_type'] = $type;
    }
    header('Location: ' . BASE_URL . '/index.php?page=' . $page);
    exit;
}

/**
 * Helper: get and clear flash message
 */
function getFlashMessage() {
    if (isset($_SESSION['flash_message'])) {
        $msg = [
            'message' => $_SESSION['flash_message'],
            'type' => $_SESSION['flash_type'] ?? 'success'
        ];
        unset($_SESSION['flash_message'], $_SESSION['flash_type']);
        return $msg;
    }
    return null;
}

/**
 * Helper: sanitize input
 */
function sanitize($input) {
    return htmlspecialchars(trim($input), ENT_QUOTES, 'UTF-8');
}
