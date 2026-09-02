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

    // === Admin Donors Management ===
    case 'admin_donors':
        $admin = new AdminController();
        $admin->manageDonors();
        break;

    case 'admin_donor_view':
        $admin = new AdminController();
        $admin->viewDonor();
        break;

    case 'admin_donor_add':
        $admin = new AdminController();
        $admin->addDonor();
        break;

    case 'admin_donor_edit':
        $admin = new AdminController();
        $admin->editDonor();
        break;

    case 'admin_donor_delete':
        $admin = new AdminController();
        $admin->deleteDonor();
        break;

    case 'admin_donor_history':
        $admin = new AdminController();
        $admin->donorHistory();
        break;

    // === Admin Hospital Management ===
    case 'admin_hospitals':
        $admin = new AdminController();
        $admin->manageHospitals();
        break;

    case 'admin_hospital_profile':
        $admin = new AdminController();
        $admin->viewHospitalProfile();
        break;

    case 'admin_fulfill_request':
        $admin = new AdminController();
        $admin->fulfillRequest();
        break;

    case 'admin_delete_appointment':
        $admin = new AdminController();
        $admin->deleteAppointment();
        break;

    case 'admin_delete_hospital_request':
        $admin = new AdminController();
        $admin->deleteHospitalRequest();
        break;

    case 'admin_hospital_request_view':
        $admin = new AdminController();
        $admin->viewHospitalRequest();
        break;

    case 'admin_dispatch_units':
        $admin = new AdminController();
        $admin->dispatchUnits();
        break;

    case 'admin_mark_ready_pickup':
        $admin = new AdminController();
        $admin->markReadyForPickup();
        break;

    case 'admin_verify_release_pin':
        $admin = new AdminController();
        $admin->verifyReleasePin();
        break;

    // === Admin Requests Management ===
    case 'admin_requests_donor':
        $admin = new AdminController();
        $admin->getRequestByUserId();
        break;
    case 'admin_requests_hospital':
        $admin = new AdminController();
        $admin->getRequestByHospitalId();
        break;

    case 'admin_requests':
        $admin = new AdminController();
        $admin->listRequests();
        break;

    // Placeholder pages for admin (sidebar links)
    case 'admin_inventory':
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

    // === Hospital Blood Requests ===
    case 'hospital_requests':
        $hospital = new HospitalController();
        $hospital->bloodRequests();
        break;

    case 'hospital_request_view':
        $hospital = new HospitalController();
        $hospital->viewRequest();
        break;

    case 'hospital_request_add':
        $hospital = new HospitalController();
        $hospital->addRequest();
        break;

    case 'hospital_request_edit':
        $hospital = new HospitalController();
        $hospital->editRequest();
        break;

    case 'hospital_request_delete':
        $hospital = new HospitalController();
        $hospital->deleteRequest();
        break;

    case 'hospital_request_history':
        $hospital = new HospitalController();
        $hospital->requestHistory();
        break;

    case 'hospital_profile_update':
        $hospital = new HospitalController();
        $hospital->updateProfile();
        break;

    case 'hospital_subscription':
        $hospital = new HospitalController();
        $hospital->subscription();
        break;

    case 'hospital_verify_subscription':
        $hospital = new HospitalController();
        $hospital->verifySubscription();
        break;

    // Placeholder pages for hospital (remaining)
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
        require_once __DIR__ . '/views/layouts/hospital_layout.php';
        break;

    // === Donor Dashboard ===
    case 'donor_dashboard':
        $donorCtrl = new DonorController();
        $donorCtrl->dashboard();
        break;

    // === Donor Appointments ===
    case 'donor_appointments':
        $donorCtrl = new DonorController();
        $donorCtrl->appointments();
        break;

    case 'donor_appointment_add':
        $donorCtrl = new DonorController();
        $donorCtrl->addAppointment();
        break;

    case 'donor_appointment_reschedule':
        $donorCtrl = new DonorController();
        $donorCtrl->rescheduleAppointment();
        break;

    case 'donor_appointment_cancel':
        $donorCtrl = new DonorController();
        $donorCtrl->cancelAppointment();
        break;

    // === Donor History ===
    case 'donor_history':
        $donorCtrl = new DonorController();
        $donorCtrl->donationHistory();
        break;

    // === Donor Profile ===
    case 'donor_profile':
        $donorCtrl = new DonorController();
        $donorCtrl->profile();
        break;

    case 'donor_profile_update':
        $donorCtrl = new DonorController();
        $donorCtrl->updateProfile();
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
