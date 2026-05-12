<?php
/**
 * Hospital Layout Wrapper
 * Top Navbar + main content area
 */
require_once __DIR__ . '/header.php';
require_once __DIR__ . '/hospital_navbar.php';
?>

<!-- Main Content Area -->
<main class="min-h-screen bg-hemo-off-white pt-[88px] pb-12">
    <!-- pt-[88px] accounts for the 64px (h-16) fixed navbar + 24px extra padding -->
    <div class="px-4 sm:px-6 lg:px-8 max-w-[1400px] mx-auto">
        <?php echo $content ?? ''; ?>
    </div>
</main>

<?php require_once __DIR__ . '/footer.php'; ?>
