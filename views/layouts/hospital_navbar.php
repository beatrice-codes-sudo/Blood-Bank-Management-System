<?php
/**
 * Hospital Manager Top Navbar
 */
$currentPage = $_GET['page'] ?? 'hospital_dashboard';
$userName = ($_SESSION['first_name'] ?? 'User') . ' ' . ($_SESSION['last_name'] ?? '');
$userInitials = strtoupper(substr($_SESSION['first_name'] ?? 'H', 0, 1) . substr($_SESSION['last_name'] ?? '', 0, 1));
?>

<!-- Top Navbar (Sticky) -->
<nav class="fixed top-0 left-0 right-0 h-16 bg-white border-b border-hemo-border z-[100] flex items-center justify-between px-4 lg:px-8 shadow-sm">
    
    <!-- Left: Logo & Brand -->
    <div class="flex items-center gap-6">
        <a href="<?php echo BASE_URL; ?>/index.php?page=hospital_dashboard" class="flex items-center gap-3 no-underline">
            <div class="w-8 h-8 lg:w-10 lg:h-10 rounded-lg gradient-red flex items-center justify-center">
                <i class="fas fa-droplet text-white text-sm lg:text-lg"></i>
            </div>
            <div class="hidden sm:block">
                <h1 class="font-display text-lg lg:text-xl font-bold text-hemo-navy tracking-tight leading-none">Hemo<span class="text-hemo-gold">Link</span></h1>
            </div>
        </a>
    </div>

    <!-- Center: Desktop Navigation Links -->
    <div class="hidden lg:flex items-center h-full space-x-1">
        <a href="<?php echo BASE_URL; ?>/index.php?page=hospital_dashboard" 
           class="h-full flex items-center px-4 text-sm font-semibold transition-fast border-b-2 <?php echo $currentPage === 'hospital_dashboard' ? 'border-hemo-red text-hemo-red' : 'border-transparent text-hemo-charcoal hover:text-hemo-red hover:border-hemo-light-red'; ?>">
            <i class="fas fa-chart-pie mr-2"></i> Dashboard
        </a>
        
        <a href="<?php echo BASE_URL; ?>/index.php?page=hospital_requests" 
           class="h-full flex items-center px-4 text-sm font-semibold transition-fast border-b-2 <?php echo $currentPage === 'hospital_requests' ? 'border-hemo-red text-hemo-red' : 'border-transparent text-hemo-charcoal hover:text-hemo-red hover:border-hemo-light-red'; ?>">
            <i class="fas fa-clipboard-list mr-2"></i> Blood Requests
        </a>
        
        <a href="<?php echo BASE_URL; ?>/index.php?page=hospital_subscription" 
           class="h-full flex items-center px-4 text-sm font-semibold transition-fast border-b-2 <?php echo $currentPage === 'hospital_subscription' ? 'border-hemo-red text-hemo-red' : 'border-transparent text-hemo-charcoal hover:text-hemo-red hover:border-hemo-light-red'; ?>">
            <i class="fas fa-crown mr-2 text-hemo-gold"></i> Subscription Plans
        </a>
    </div>

    <!-- Right: User Profile & Mobile Toggle -->
    <div class="flex items-center gap-4">
        
        <!-- User Profile -->
        <div class="hidden sm:flex items-center gap-3 border-l border-hemo-border pl-4">
            <div class="text-right">
                <p class="text-sm font-bold text-hemo-navy leading-tight"><?php echo sanitize($userName); ?></p>
                <p class="text-xs text-hemo-gray">Hospital Manager</p>
            </div>
            <div class="w-9 h-9 rounded-full gradient-red flex items-center justify-center text-white font-semibold text-sm shadow-sm">
                <?php echo $userInitials; ?>
            </div>
            <a href="<?php echo BASE_URL; ?>/index.php?page=logout" class="ml-2 w-8 h-8 flex items-center justify-center rounded-lg text-hemo-gray hover:text-hemo-red hover:bg-hemo-light-red transition-fast" title="Logout">
                <i class="fas fa-right-from-bracket"></i>
            </a>
        </div>

        <!-- Mobile Menu Toggle Button -->
        <button id="mobileMenuBtn" class="lg:hidden w-10 h-10 flex items-center justify-center rounded-lg text-hemo-navy hover:bg-hemo-light-red transition-fast">
            <i class="fas fa-bars text-lg"></i>
        </button>
    </div>
</nav>

<!-- Mobile Navigation Menu (Hidden by default) -->
<div id="mobileMenu" class="fixed inset-0 top-16 bg-white z-[90] hidden flex-col overflow-y-auto lg:hidden border-t border-hemo-border shadow-lg">
    <div class="flex flex-col p-4 space-y-2">
        <a href="<?php echo BASE_URL; ?>/index.php?page=hospital_dashboard" 
           class="flex items-center gap-3 px-4 py-3 rounded-lg text-sm font-semibold transition-fast <?php echo $currentPage === 'hospital_dashboard' ? 'bg-hemo-light-red text-hemo-red' : 'text-hemo-charcoal hover:bg-gray-50'; ?>">
            <i class="fas fa-chart-pie w-5 text-center"></i>
            <span>Dashboard</span>
        </a>
        
        <a href="<?php echo BASE_URL; ?>/index.php?page=hospital_requests" 
           class="flex items-center gap-3 px-4 py-3 rounded-lg text-sm font-semibold transition-fast <?php echo $currentPage === 'hospital_requests' ? 'bg-hemo-light-red text-hemo-red' : 'text-hemo-charcoal hover:bg-gray-50'; ?>">
            <i class="fas fa-clipboard-list w-5 text-center"></i>
            <span>Blood Requests</span>
        </a>
        
        <a href="<?php echo BASE_URL; ?>/index.php?page=hospital_subscription" 
           class="flex items-center gap-3 px-4 py-3 rounded-lg text-sm font-semibold transition-fast <?php echo $currentPage === 'hospital_subscription' ? 'bg-hemo-light-red text-hemo-red' : 'text-hemo-charcoal hover:bg-gray-50'; ?>">
            <i class="fas fa-crown w-5 text-center text-hemo-gold"></i>
            <span>Subscription Plans</span>
        </a>

        <div class="h-px bg-hemo-border my-2"></div>
        
        <div class="flex items-center gap-3 px-4 py-3">
            <div class="w-8 h-8 rounded-full gradient-red flex items-center justify-center text-white font-semibold text-xs shadow-sm">
                <?php echo $userInitials; ?>
            </div>
            <div class="flex-1">
                <p class="text-sm font-bold text-hemo-navy leading-tight"><?php echo sanitize($userName); ?></p>
            </div>
            <a href="<?php echo BASE_URL; ?>/index.php?page=logout" class="flex items-center gap-2 text-hemo-gray hover:text-hemo-red text-sm font-semibold transition-fast">
                <i class="fas fa-right-from-bracket"></i> Logout
            </a>
        </div>
    </div>
</div>

<script>
    document.addEventListener('DOMContentLoaded', () => {
        const btn = document.getElementById('mobileMenuBtn');
        const menu = document.getElementById('mobileMenu');
        const icon = btn.querySelector('i');

        btn.addEventListener('click', () => {
            menu.classList.toggle('hidden');
            menu.classList.toggle('flex');
            
            if (menu.classList.contains('hidden')) {
                icon.classList.remove('fa-times');
                icon.classList.add('fa-bars');
            } else {
                icon.classList.remove('fa-bars');
                icon.classList.add('fa-times');
            }
        });
    });
</script>
