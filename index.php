<?php
/**
 * HemoLink - Blood Bank Management System
 * Front Controller / Router
 */
require_once __DIR__ . '/config/init.php';

// Get the requested page
$page = $_GET['page'] ?? 'landing';

// Route map
switch ($page) {
    // === Public Pages ===
    case 'landing':
    case 'home':
        require_once __DIR__ . '/views/landing.php';
        break;

    // === Auth Pages ===
    case 'login':
        $auth = new AuthController();
        $auth->showLogin();
        break;

    case 'login_process':
        $auth = new AuthController();
        $auth->login();
        break;

    case 'register_donor':
        $auth = new AuthController();
        $auth->showRegisterDonor();
        break;

    case 'register_donor_process':
        $auth = new AuthController();
        $auth->registerDonor();
        break;

    case 'register_hospital':
        $auth = new AuthController();
        $auth->showRegisterHospital();
        break;

    case 'register_hospital_process':
        $auth = new AuthController();
        $auth->registerHospital();
        break;

    case 'logout':
        $auth = new AuthController();
        $auth->logout();
        break;

    // === Admin Dashboard ===
    case 'admin_dashboard':
        $admin = new AdminController();
        $admin->dashboard();
        break;

    case 'admin_users':
        $admin = new AdminController();
        $admin->manageUsers();
        break;

    case 'admin_toggle_user':
        $admin = new AdminController();
        $admin->toggleUserStatus();
        break;

    // Placeholder pages for admin (sidebar links)
    case 'admin_donors':
    case 'admin_hospitals':
    case 'admin_inventory':
    case 'admin_requests':
    case 'admin_audit':
        requireRole(ROLE_ADMIN);
        $pageTitle = ucwords(str_replace('admin_', '', $page));
        ob_start();
        echo '<div class="flex items-center justify-center min-h-[60vh]">
                <div class="text-center">
                    <div class="w-20 h-20 rounded-2xl bg-hemo-light-red flex items-center justify-center mx-auto mb-6">
                        <i class="fas fa-tools text-hemo-red text-3xl"></i>
                    </div>
                    <h2 class="font-display text-2xl font-bold text-hemo-navy mb-2">Coming Soon</h2>
                    <p class="text-hemo-charcoal">The <strong>' . sanitize($pageTitle) . '</strong> module is under development.</p>
                    <a href="' . BASE_URL . '/index.php?page=admin_dashboard" class="inline-flex items-center gap-2 mt-6 px-6 py-3 rounded-lg bg-hemo-red text-white font-semibold text-sm hover:bg-hemo-deep-red transition-default btn-press">
                        <i class="fas fa-arrow-left"></i> Back to Dashboard
                    </a>
                </div>
              </div>';
        $content = ob_get_clean();
        require_once __DIR__ . '/views/layouts/dashboard_layout.php';
        break;

    // === Hospital Dashboard ===
    case 'hospital_dashboard':
        $hospital = new HospitalController();
        $hospital->dashboard();
        break;

    // Placeholder pages for hospital
    case 'hospital_requests':
    case 'hospital_inventory':
    case 'hospital_profile':
        requireRole(ROLE_HOSPITAL);
        $pageTitle = ucwords(str_replace('hospital_', '', $page));
        ob_start();
        echo '<div class="flex items-center justify-center min-h-[60vh]">
                <div class="text-center">
                    <div class="w-20 h-20 rounded-2xl bg-blue-50 flex items-center justify-center mx-auto mb-6">
                        <i class="fas fa-tools text-hemo-info text-3xl"></i>
                    </div>
                    <h2 class="font-display text-2xl font-bold text-hemo-navy mb-2">Coming Soon</h2>
                    <p class="text-hemo-charcoal">The <strong>' . sanitize($pageTitle) . '</strong> module is under development.</p>
                    <a href="' . BASE_URL . '/index.php?page=hospital_dashboard" class="inline-flex items-center gap-2 mt-6 px-6 py-3 rounded-lg bg-hemo-navy text-white font-semibold text-sm hover:bg-slate-800 transition-default btn-press">
                        <i class="fas fa-arrow-left"></i> Back to Dashboard
                    </a>
                </div>
              </div>';
        $content = ob_get_clean();
        require_once __DIR__ . '/views/layouts/dashboard_layout.php';
        break;

    // === Donor Dashboard ===
    case 'donor_dashboard':
        $donorCtrl = new DonorController();
        $donorCtrl->dashboard();
        break;

    // Placeholder pages for donor
    case 'donor_history':
    case 'donor_appointments':
    case 'donor_profile':
        requireRole(ROLE_DONOR);
        $pageTitle = ucwords(str_replace('donor_', '', $page));
        ob_start();
        echo '<div class="flex items-center justify-center min-h-[60vh]">
                <div class="text-center">
                    <div class="w-20 h-20 rounded-2xl bg-hemo-light-red flex items-center justify-center mx-auto mb-6">
                        <i class="fas fa-tools text-hemo-red text-3xl"></i>
                    </div>
                    <h2 class="font-display text-2xl font-bold text-hemo-navy mb-2">Coming Soon</h2>
                    <p class="text-hemo-charcoal">The <strong>' . sanitize($pageTitle) . '</strong> module is under development.</p>
                    <a href="' . BASE_URL . '/index.php?page=donor_dashboard" class="inline-flex items-center gap-2 mt-6 px-6 py-3 rounded-lg bg-hemo-red text-white font-semibold text-sm hover:bg-hemo-deep-red transition-default btn-press">
                        <i class="fas fa-arrow-left"></i> Back to Dashboard
                    </a>
                </div>
              </div>';
        $content = ob_get_clean();
        require_once __DIR__ . '/views/layouts/dashboard_layout.php';
        break;

    // === Unauthorized ===
    case 'unauthorized':
        $pageTitle = 'Unauthorized';
        require_once __DIR__ . '/views/layouts/header.php';
        echo '<div class="min-h-screen flex items-center justify-center bg-hemo-off-white">
                <div class="text-center">
                    <div class="w-24 h-24 rounded-full bg-red-50 flex items-center justify-center mx-auto mb-6">
                        <i class="fas fa-shield-halved text-hemo-warning text-4xl"></i>
                    </div>
                    <h2 class="font-display text-3xl font-bold text-hemo-navy mb-3">Access Denied</h2>
                    <p class="text-hemo-charcoal mb-6">You don\'t have permission to access this page.</p>
                    <a href="' . BASE_URL . '/index.php?page=login" class="inline-flex items-center gap-2 px-6 py-3 rounded-lg bg-hemo-red text-white font-semibold text-sm hover:bg-hemo-deep-red transition-default btn-press">
                        <i class="fas fa-right-to-bracket"></i> Go to Login
                    </a>
                </div>
              </div>';
        require_once __DIR__ . '/views/layouts/footer.php';
        break;

    // === 404 ===
    default:
        $pageTitle = 'Page Not Found';
        require_once __DIR__ . '/views/layouts/header.php';
        echo '<div class="min-h-screen flex items-center justify-center bg-hemo-off-white">
                <div class="text-center">
                    <h2 class="font-display text-8xl font-bold text-hemo-red mb-4">404</h2>
                    <p class="text-xl text-hemo-navy font-semibold mb-2">Page Not Found</p>
                    <p class="text-hemo-charcoal mb-6">The page you\'re looking for doesn\'t exist.</p>
                    <a href="' . BASE_URL . '/index.php" class="inline-flex items-center gap-2 px-6 py-3 rounded-lg bg-hemo-red text-white font-semibold text-sm hover:bg-hemo-deep-red transition-default btn-press">
                        <i class="fas fa-home"></i> Go Home
                    </a>
                </div>
              </div>';
        require_once __DIR__ . '/views/layouts/footer.php';
        break;
}
