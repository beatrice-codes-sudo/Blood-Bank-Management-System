<?php
/**
 * Donor Dashboard
 */
$pageTitle = 'Donor Dashboard';
$donor = $data['donor'] ?? null;
$stats = $data['stats'] ?? ['total_donations' => 0, 'last_donation_date' => null, 'total_volume_ml' => 0];
$donations = $data['donations'] ?? [];

ob_start();
?>

<!-- Page Header -->
<div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4 mb-6">
    <div>
        <h1 class="font-display text-[32px] font-bold text-hemo-navy">My Dashboard</h1>
        <p class="text-hemo-charcoal mt-1">Welcome back, <span class="font-semibold text-hemo-red"><?php echo sanitize($_SESSION['first_name'] ?? 'Donor'); ?></span>. Track your contributions.</p>
    </div>
    <div class="flex items-center gap-3">
        <a href="<?php echo BASE_URL; ?>/index.php?page=donor_appointments" class="inline-flex items-center gap-2 px-5 py-2.5 rounded-lg bg-hemo-red text-white font-semibold text-sm shadow-btn-primary hover:bg-hemo-deep-red hover:shadow-btn-hover hover:-translate-y-px transition-default btn-press">
            <i class="fas fa-calendar-plus"></i> Schedule Donation
        </a>
    </div>
</div>

<!-- Active Emergency In-App Appeal Banner -->
<?php if (!empty($data['active_appeal'])): 
    $appeal = $data['active_appeal'];
?>
<div class="mb-8 p-5 rounded-2xl bg-gradient-to-r from-red-600 to-rose-700 text-white flex flex-col md:flex-row md:items-center justify-between gap-4 shadow-lg border border-red-500 relative overflow-hidden">
    <div class="flex items-center gap-4">
        <div class="w-12 h-12 rounded-xl bg-white/20 flex items-center justify-center flex-shrink-0 backdrop-blur-sm">
            <i class="fas fa-bullhorn text-xl text-yellow-300"></i>
        </div>
        <div>
            <div class="flex items-center gap-2 mb-0.5">
                <span class="px-2.5 py-0.5 rounded-full bg-white/20 text-yellow-300 text-[10px] font-bold uppercase tracking-wider">
                    Emergency Callout: <?php echo sanitize($appeal['blood_type']); ?>
                </span>
                <span class="text-xs text-white/80"><?php echo sanitize($appeal['urgency']); ?></span>
            </div>
            <h3 class="font-bold text-base text-white">Your Blood Group is in Critical Shortage</h3>
            <p class="text-white/90 text-xs mt-0.5 max-w-2xl leading-relaxed">
                <?php echo sanitize($appeal['message']); ?>
            </p>
        </div>
    </div>
    <a href="<?php echo BASE_URL; ?>/index.php?page=donor_appointments" 
       class="px-5 py-2.5 rounded-xl bg-yellow-400 hover:bg-yellow-300 text-hemo-navy font-bold text-xs shadow-md transition-fast flex items-center justify-center gap-2 whitespace-nowrap">
        <i class="fas fa-calendar-plus"></i> Book Urgent Donation
    </a>
</div>
<?php endif; ?>

<!-- Stats Grid -->
<div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-6 mb-8">
    <div class="stat-card bg-white rounded-xl shadow-card p-5 card-accent-red transition-default">
        <div class="flex items-center justify-between mb-4">
            <div class="w-12 h-12 rounded-xl bg-hemo-light-red flex items-center justify-center">
                <i class="fas fa-droplet text-hemo-red text-lg"></i>
            </div>
        </div>
        <p class="text-3xl font-bold text-hemo-navy"><?php echo number_format($stats['total_donations']); ?></p>
        <p class="text-sm text-hemo-gray mt-1">Total Donations</p>
    </div>

    <div class="stat-card bg-white rounded-xl shadow-card p-5 card-accent-amber transition-default">
        <div class="flex items-center justify-between mb-4">
            <div class="w-12 h-12 rounded-xl bg-amber-50 flex items-center justify-center">
                <i class="fas fa-calendar-check text-hemo-amber text-lg"></i>
            </div>
        </div>
        <p class="text-xl font-bold text-hemo-navy">
            <?php echo $stats['last_donation_date'] ? date('M d, Y', strtotime($stats['last_donation_date'])) : 'No donations'; ?>
        </p>
        <p class="text-sm text-hemo-gray mt-1">Last Donation</p>
    </div>

    <div class="stat-card bg-white rounded-xl shadow-card p-5 card-accent-green transition-default">
        <div class="flex items-center justify-between mb-4">
            <div class="w-12 h-12 rounded-xl bg-green-50 flex items-center justify-center">
                <i class="fas fa-flask text-hemo-success text-lg"></i>
            </div>
        </div>
        <p class="text-3xl font-bold text-hemo-navy"><?php echo number_format($stats['total_volume_ml']); ?><span class="text-lg font-normal text-hemo-gray"> ml</span></p>
        <p class="text-sm text-hemo-gray mt-1">Total Volume Donated</p>
    </div>

    <div class="stat-card bg-white rounded-xl shadow-card p-5 card-accent-green transition-default">
        <div class="flex items-center justify-between mb-4">
            <div class="w-12 h-12 rounded-xl bg-green-50 flex items-center justify-center">
                <i class="fas fa-heart-pulse text-hemo-success text-lg"></i>
            </div>
        </div>
        <p class="text-xl font-bold text-hemo-success">
            <?php echo sanitize($donor['eligibility_status'] ?? 'Eligible'); ?>
        </p>
        <p class="text-sm text-hemo-gray mt-1">Eligibility Status</p>
    </div>
</div>    

        <!-- Next Steps -->
        <div class="bg-white rounded-xl shadow-card p-6 card-accent-green">
            <div class="flex items-center gap-3 mb-4 pb-4 border-b border-hemo-border">
                <i class="fas fa-lightbulb text-hemo-success"></i>
                <h3 class="text-lg font-semibold text-hemo-navy">Quick Actions</h3>
            </div>
            
                <a href="<?php echo BASE_URL; ?>/index.php?page=donor_appointments" 
                   class="flex items-center gap-4 p-4 rounded-xl hover:bg-hemo-light-red transition-fast group">
                    <div class="w-10 h-10 rounded-lg bg-hemo-light-red flex items-center justify-center flex-shrink-0 group-hover:bg-hemo-red group-hover:text-white transition-fast">
                        <i class="fas fa-calendar-plus text-hemo-red group-hover:text-white"></i>
                    </div>
                    <div>
                        <p class="text-sm font-semibold text-hemo-navy">Schedule Appointment</p>
                        <p class="text-xs text-hemo-gray">Book your next donation</p>
                    </div>
                </a>
                <a href="<?php echo BASE_URL; ?>/index.php?page=donor_history" 
                   class="flex items-center gap-4 p-4 rounded-xl hover:bg-hemo-light-red transition-fast group">
                    <div class="w-10 h-10 rounded-lg bg-hemo-light-red flex items-center justify-center flex-shrink-0 group-hover:bg-hemo-red group-hover:text-white transition-fast">
                        <i class="fas fa-clock-rotate-left text-hemo-red group-hover:text-white"></i>
                    </div>
                    <div>
                        <p class="text-sm font-semibold text-hemo-navy">Donation History</p>
                        <p class="text-xs text-hemo-gray">View past donations</p>
                    </div>
                </a>
                <a href="<?php echo BASE_URL; ?>/index.php?page=donor_profile" 
                   class="flex items-center gap-4 p-4 rounded-xl hover:bg-hemo-light-red transition-fast group">
                    <div class="w-10 h-10 rounded-lg bg-hemo-light-red flex items-center justify-center flex-shrink-0 group-hover:bg-hemo-red group-hover:text-white transition-fast">
                        <i class="fas fa-user-pen text-hemo-red group-hover:text-white"></i>
                    </div>
                    <div>
                        <p class="text-sm font-semibold text-hemo-navy">Edit Profile</p>
                        <p class="text-xs text-hemo-gray">Update your information</p>
                    </div>
                </a>
                <a href="<?php echo BASE_URL; ?>/index.php?page=donor_certificate" class="flex items-center gap-4 p-4 rounded-xl hover:bg-hemo-light-red transition-fast group">
                    <div class="w-10 h-10 rounded-lg bg-hemo-light-red flex items-center justify-center flex-shrink-0 group-hover:bg-hemo-red group-hover:text-white transition-fast">
                        <i class="fas fa-certificate text-hemo-red group-hover:text-white"></i>
                    </div>
                    <div>
                        <p class="text-sm font-semibold text-hemo-navy">Download Certificate</p>
                        <p class="text-xs text-hemo-gray">Official recognition certificate</p>
                    </div>
                </a>
            
        </div>

</div>



<?php
$content = ob_get_clean();
require_once __DIR__ . '/../layouts/dashboard_layout.php';
?>
