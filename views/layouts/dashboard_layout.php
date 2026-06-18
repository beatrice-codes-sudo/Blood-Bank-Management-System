<?php
/**
 * Dashboard Layout Wrapper for the admin dashboard
 * Sidebar + main content area
 */
require_once __DIR__ . '/header.php';
require_once __DIR__ . '/sidebar.php';
?>

<!-- Main Content Area -->
<main class="lg:ml-sidebar min-h-screen bg-hemo-off-white pt-16 lg:pt-0">
    <div class="p-6 lg:p-8 max-w-[1400px] mx-auto">
        <?php echo $content ?? ''; ?>
    </div>
</main>

<?php require_once __DIR__ . '/footer.php'; ?>
