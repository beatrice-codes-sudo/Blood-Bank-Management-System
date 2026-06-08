<?php
/**
 * Sidebar Navigation
 * Dynamic menu based on user role
 */
$currentPage = $_GET['page'] ?? 'dashboard';
$roleId = getUserRole();
$userName = ($_SESSION['first_name'] ?? 'User') . ' ' . ($_SESSION['last_name'] ?? '');
$roleName = $_SESSION['role_name'] ?? 'User';
$userInitials = strtoupper(substr($_SESSION['first_name'] ?? 'U', 0, 1) . substr($_SESSION['last_name'] ?? '', 0, 1));
?>

<!-- Mobile Sidebar Overlay -->
<div id="sidebar-overlay" class="fixed inset-0 bg-black/50 z-[99] hidden lg:hidden" onclick="toggleSidebar()"></div>

<!-- Sidebar -->
<aside id="sidebar" class="fixed left-0 top-0 h-screen w-sidebar bg-white border-r border-hemo-border z-[100] flex flex-col transition-transform duration-300 
    lg:translate-x-0 -translate-x-full">
    
    <!-- Logo Header -->
    <div class="h-[72px] flex items-center px-6 border-b border-hemo-border flex-shrink-0">
        <?php
        $dashboardPage = 'home';
        if (isset($_SESSION['role_name'])) {
            $dashboardPage = $_SESSION['role_name'] === 'admin' ? 'admin_dashboard' : ($_SESSION['role_name'] === 'hospital_manager' ? 'hospital_dashboard' : 'donor_dashboard');
        }
        ?>
        <a href="<?php echo BASE_URL; ?>/index.php?page=<?php echo $dashboardPage; ?>" class="flex items-center gap-3 no-underline">
            <div class="w-10 h-10 rounded-lg gradient-red flex items-center justify-center">
                <i class="fas fa-droplet text-white text-lg"></i>
            </div>
            <div>
                <h1 class="font-display text-xl font-bold text-hemo-navy tracking-tight leading-none">Hemo<span class="text-hemo-gold">Link</span></h1>
                <p class="text-[10px] text-hemo-gray font-interface tracking-wider uppercase">Blood Bank System</p>
            </div>
        </a>
    </div>

    <!-- Navigation Menu -->
    <nav class="flex-1 overflow-y-auto py-4 px-3">
        <?php if ($roleId == ROLE_ADMIN): ?>
            <!-- Admin Menu -->
            <p class="text-xs font-semibold text-hemo-gray uppercase tracking-widest px-4 mb-3">Main Menu</p>
            <a href="<?php echo BASE_URL; ?>/index.php?page=admin_dashboard" 
               class="sidebar-item flex items-center gap-3 px-4 py-3 rounded-lg text-sm font-medium transition-fast <?php echo $currentPage === 'admin_dashboard' ? 'active' : 'text-hemo-charcoal'; ?>">
                <i class="fas fa-chart-pie w-5 text-center"></i>
                <span>Dashboard</span>
            </a>

            <p class="text-xs font-semibold text-hemo-gray uppercase tracking-widest px-4 mb-3 mt-6">Management</p>
            <a href="<?php echo BASE_URL; ?>/index.php?page=admin_donors" 
               class="sidebar-item flex items-center gap-3 px-4 py-3 rounded-lg text-sm font-medium transition-fast <?php echo $currentPage === 'admin_donors' ? 'active' : 'text-hemo-charcoal'; ?>">
                <i class="fas fa-users w-5 text-center"></i>
                <span>Donors</span>
            </a>
            <a href="<?php echo BASE_URL; ?>/index.php?page=admin_hospitals" 
               class="sidebar-item flex items-center gap-3 px-4 py-3 rounded-lg text-sm font-medium transition-fast <?php echo in_array($currentPage, ['admin_hospitals', 'admin_hospital_profile']) ? 'active' : 'text-hemo-charcoal'; ?>">
                <i class="fas fa-hospital w-5 text-center"></i>
                <span>Hospitals</span>
            </a>
            <a href="<?php echo BASE_URL; ?>/index.php?page=admin_inventory" 
               class="sidebar-item flex items-center gap-3 px-4 py-3 rounded-lg text-sm font-medium transition-fast <?php echo $currentPage === 'admin_inventory' ? 'active' : 'text-hemo-charcoal'; ?>">
                <i class="fas fa-droplet w-5 text-center"></i>
                <span>Blood Inventory</span>
            </a>
            <a href="<?php echo BASE_URL; ?>/index.php?page=admin_requests" 
               class="sidebar-item flex items-center gap-3 px-4 py-3 rounded-lg text-sm font-medium transition-fast <?php echo $currentPage === 'admin_requests' ? 'active' : 'text-hemo-charcoal'; ?>">
                <i class="fas fa-clipboard-list w-5 text-center"></i>
                <span>Requests</span>
            </a>

            <p class="text-xs font-semibold text-hemo-gray uppercase tracking-widest px-4 mb-3 mt-6">System</p>
            <a href="<?php echo BASE_URL; ?>/index.php?page=admin_users" 
               class="sidebar-item flex items-center gap-3 px-4 py-3 rounded-lg text-sm font-medium transition-fast <?php echo $currentPage === 'admin_users' ? 'active' : 'text-hemo-charcoal'; ?>">
                <i class="fas fa-user-shield w-5 text-center"></i>
                <span>User Accounts</span>
            </a>
            <a href="<?php echo BASE_URL; ?>/index.php?page=admin_audit" 
               class="sidebar-item flex items-center gap-3 px-4 py-3 rounded-lg text-sm font-medium transition-fast <?php echo $currentPage === 'admin_audit' ? 'active' : 'text-hemo-charcoal'; ?>">
                <i class="fas fa-file-lines w-5 text-center"></i>
                <span>Audit Logs</span>
            </a>

        <?php elseif ($roleId == ROLE_DONOR): ?>
            <!-- Donor Menu -->
            <!-- <p class="text-xs font-semibold text-hemo-gray uppercase tracking-widest px-4 mb-3">Main Menu</p> -->
            <a href="<?php echo BASE_URL; ?>/index.php?page=donor_dashboard" 
               class="sidebar-item flex items-center gap-3 px-4 py-3 rounded-lg text-sm font-medium transition-fast <?php echo $currentPage === 'donor_dashboard' ? 'active' : 'text-hemo-charcoal'; ?>">
                <i class="fas fa-chart-pie w-5 text-center"></i>
                <span>Dashboard</span>
            </a>

            <p class="text-xs font-semibold text-hemo-gray uppercase tracking-widest px-4 mb-3 mt-6">Donations</p>
            <a href="<?php echo BASE_URL; ?>/index.php?page=donor_history" 
               class="sidebar-item flex items-center gap-3 px-4 py-3 rounded-lg text-sm font-medium transition-fast <?php echo $currentPage === 'donor_history' ? 'active' : 'text-hemo-charcoal'; ?>">
                <i class="fas fa-clock-rotate-left w-5 text-center"></i>
                <span>Donation History</span>
            </a>
            <a href="<?php echo BASE_URL; ?>/index.php?page=donor_appointments" 
               class="sidebar-item flex items-center gap-3 px-4 py-3 rounded-lg text-sm font-medium transition-fast <?php echo $currentPage === 'donor_appointments' ? 'active' : 'text-hemo-charcoal'; ?>">
                <i class="fas fa-calendar-check w-5 text-center"></i>
                <span>Appointments</span>
            </a>

            <p class="text-xs font-semibold text-hemo-gray uppercase tracking-widest px-4 mb-3 mt-6">Account</p>
            <a href="<?php echo BASE_URL; ?>/index.php?page=donor_profile" 
               class="sidebar-item flex items-center gap-3 px-4 py-3 rounded-lg text-sm font-medium transition-fast <?php echo $currentPage === 'donor_profile' ? 'active' : 'text-hemo-charcoal'; ?>">
                <i class="fas fa-user w-5 text-center"></i>
                <span>My Profile</span>
            </a>
        <?php endif; ?>
    </nav>

    <!-- User Profile Footer -->
    <div class="h-[80px] border-t border-hemo-border px-4 flex items-center gap-3 flex-shrink-0">
        <div class="w-10 h-10 rounded-full gradient-red flex items-center justify-center text-white font-semibold text-sm flex-shrink-0">
            <?php echo $userInitials; ?>
        </div>
        <div class="flex-1 min-w-0">
            <p class="text-sm font-semibold text-hemo-navy truncate"><?php echo sanitize($userName); ?></p>
            <p class="text-xs text-hemo-gray truncate"><?php echo sanitize($roleName); ?></p>
        </div>
        <a href="<?php echo BASE_URL; ?>/index.php?page=logout" class="w-8 h-8 flex items-center justify-center rounded-lg text-hemo-gray hover:text-hemo-red hover:bg-hemo-light-red transition-fast" title="Logout">
            <i class="fas fa-right-from-bracket"></i>
        </a>
    </div>
</aside>

<!-- Mobile Top Bar -->
<div class="lg:hidden fixed top-0 left-0 right-0 h-16 bg-white border-b border-hemo-border z-[98] flex items-center px-4 gap-4">
    <button onclick="toggleSidebar()" class="w-10 h-10 flex items-center justify-center rounded-lg text-hemo-navy hover:bg-hemo-light-red transition-fast">
        <i class="fas fa-bars text-lg"></i>
    </button>
    <div class="flex items-center gap-2">
        <div class="w-8 h-8 rounded-lg gradient-red flex items-center justify-center">
            <i class="fas fa-droplet text-white text-sm"></i>
        </div>
        <span class="font-display text-lg font-bold text-hemo-navy">Hemo<span class="text-hemo-gold">Link</span></span>
    </div>
</div>
