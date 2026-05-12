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
<div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4 mb-8">
    <div>
        <h1 class="font-display text-[32px] font-bold text-hemo-navy">My Dashboard</h1>
        <p class="text-hemo-charcoal mt-1">Welcome back, <span class="font-semibold text-hemo-red"><?php echo sanitize($_SESSION['first_name'] ?? 'Donor'); ?></span>. Track your contributions.</p>
    </div>
    <div class="flex items-center gap-3">
        <button class="inline-flex items-center gap-2 px-5 py-2.5 rounded-lg bg-hemo-red text-white font-semibold text-sm shadow-btn-primary hover:bg-hemo-deep-red hover:shadow-btn-hover hover:-translate-y-px transition-default btn-press">
            <i class="fas fa-calendar-plus"></i> Schedule Donation
        </button>
    </div>
</div>

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

<!-- Content Grid -->
<div class="grid lg:grid-cols-2 gap-6 mb-8">
    <!-- Donor Profile Card -->
    <!-- <div class="bg-white rounded-xl shadow-card p-6 card-accent-red">
        <div class="flex items-center gap-3 mb-6 pb-4 border-b border-hemo-border">
            <i class="fas fa-user text-hemo-red"></i>
            <h3 class="text-lg font-semibold text-hemo-navy">My Profile</h3>
        </div>
        <?php if ($donor): ?>
        <div class="text-center mb-6">
            <div class="w-20 h-20 rounded-full gradient-red flex items-center justify-center mx-auto mb-4">
                <span class="text-2xl font-bold text-white">
                    <?php echo strtoupper(substr($donor['first_name'], 0, 1) . substr($donor['last_name'], 0, 1)); ?>
                </span>
            </div>
            <h4 class="text-lg font-bold text-hemo-navy"><?php echo sanitize($donor['first_name'] . ' ' . $donor['last_name']); ?></h4>
            <p class="text-xs text-hemo-red font-mono mt-1">DNR-<?php echo str_pad($donor['donor_id'], 5, '0', STR_PAD_LEFT); ?></p>
        </div> -->

        <!-- Blood Type Badge -->
        <!-- <?php if ($donor['blood_type']): ?>
        <div class="flex justify-center mb-6">
            <div class="inline-flex items-center gap-2 px-6 py-3 rounded-xl bg-hemo-red text-white">
                <i class="fas fa-droplet"></i>
                <span class="text-2xl font-bold"><?php echo sanitize($donor['blood_type']); ?></span>
            </div>
        </div>
        <?php endif; ?>

        <div class="space-y-3">
            <div class="flex items-center gap-3 text-sm">
                <i class="fas fa-envelope w-5 text-center text-hemo-red"></i>
                <span class="text-hemo-charcoal"><?php echo sanitize($donor['email']); ?></span>
            </div>
            <?php if ($donor['phone']): ?>
            <div class="flex items-center gap-3 text-sm">
                <i class="fas fa-phone w-5 text-center text-hemo-red"></i>
                <span class="text-hemo-charcoal"><?php echo sanitize($donor['phone']); ?></span>
            </div>
            <?php endif; ?>
            <?php if ($donor['date_of_birth']): ?>
            <div class="flex items-center gap-3 text-sm">
                <i class="fas fa-cake-candles w-5 text-center text-hemo-red"></i>
                <span class="text-hemo-charcoal"><?php echo date('M d, Y', strtotime($donor['date_of_birth'])); ?></span>
            </div>
            <?php endif; ?>
            <?php if ($donor['gender']): ?>
            <div class="flex items-center gap-3 text-sm">
                <i class="fas fa-venus-mars w-5 text-center text-hemo-red"></i>
                <span class="text-hemo-charcoal"><?php echo sanitize($donor['gender']); ?></span>
            </div>
            <?php endif; ?>
            <?php if ($donor['address'] || $donor['city']): ?>
            <div class="flex items-center gap-3 text-sm">
                <i class="fas fa-location-dot w-5 text-center text-hemo-red"></i>
                <span class="text-hemo-charcoal"><?php echo sanitize(trim($donor['address'] . ', ' . $donor['city'], ', ')); ?></span>
            </div>
            <?php endif; ?>
        </div>
        <?php else: ?>
        <div class="text-center py-8 text-hemo-gray">
            <i class="fas fa-user text-4xl mb-3 text-hemo-border"></i>
            <p>Profile not found</p>
        </div>
        <?php endif; ?>
    </div> -->

    <!-- Impact & Next Steps -->
    
        <!-- Impact Card -->
        <div class="bg-white rounded-xl shadow-card p-6 card-accent-gold">
            <div class="flex items-center gap-3 mb-6 pb-4 border-b border-hemo-border">
                <i class="fas fa-award text-hemo-gold"></i>
                <h3 class="text-lg font-semibold text-hemo-navy">Your Impact</h3>
            </div>
    
                <div class="text-center p-6 bg-hemo-off-white rounded-xl">
                    <div class="w-14 h-14 rounded-xl bg-hemo-light-red flex items-center justify-center mx-auto mb-3">
                        <i class="fas fa-heart text-hemo-red text-xl"></i>
                    </div>
                    <p class="text-2xl font-bold text-hemo-navy"><?php echo $stats['total_donations'] * 3; ?></p>
                    <p class="text-xs text-hemo-gray mt-1">Potential Lives Saved</p>
                </div>
                <div class="text-center p-6 bg-hemo-off-white rounded-xl">
                    <div class="w-14 h-14 rounded-xl bg-amber-50 flex items-center justify-center mx-auto mb-3">
                        <i class="fas fa-droplet text-hemo-amber text-xl"></i>
                    </div>
                    <p class="text-2xl font-bold text-hemo-navy"><?php echo number_format($stats['total_volume_ml']); ?></p>
                    <p class="text-xs text-hemo-gray mt-1">ml Donated</p>
                </div>
                <div class="text-center p-6 bg-hemo-off-white rounded-xl">
                    <div class="w-14 h-14 rounded-xl bg-green-50 flex items-center justify-center mx-auto mb-3">
                        <i class="fas fa-trophy text-hemo-success text-xl"></i>
                    </div>
                    <p class="text-2xl font-bold text-hemo-navy"><?php echo $stats['total_donations']; ?></p>
                    <p class="text-xs text-hemo-gray mt-1">Donations Complete</p>
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
                <a href="#" class="flex items-center gap-4 p-4 rounded-xl hover:bg-hemo-light-red transition-fast group">
                    <div class="w-10 h-10 rounded-lg bg-hemo-light-red flex items-center justify-center flex-shrink-0 group-hover:bg-hemo-red group-hover:text-white transition-fast">
                        <i class="fas fa-file-download text-hemo-red group-hover:text-white"></i>
                    </div>
                    <div>
                        <p class="text-sm font-semibold text-hemo-navy">Download Certificate</p>
                        <p class="text-xs text-hemo-gray">Get donation certificate</p>
                    </div>
                </a>
            
        </div>

</div>

<!-- Donation History Table -->
<!-- <div class="bg-white rounded-xl shadow-card">
    <div class="p-6 border-b border-hemo-border flex items-center justify-between">
        <div class="flex items-center gap-3">
            <i class="fas fa-clock-rotate-left text-hemo-red"></i>
            <h3 class="text-lg font-semibold text-hemo-navy">Donation History</h3>
        </div>
        <a href="<?php echo BASE_URL; ?>/index.php?page=donor_history" class="text-sm text-hemo-red font-semibold hover:text-hemo-deep-red transition-fast">View All →</a>
    </div>
    <div class="overflow-x-auto">
        <table class="w-full">
            <thead>
                <tr class="bg-hemo-light-gray">
                    <th class="text-left px-6 py-3.5 text-xs font-semibold text-hemo-gray uppercase tracking-wider">Donation ID</th>
                    <th class="text-left px-6 py-3.5 text-xs font-semibold text-hemo-gray uppercase tracking-wider">Date</th>
                    <th class="text-left px-6 py-3.5 text-xs font-semibold text-hemo-gray uppercase tracking-wider">Blood Type</th>
                    <th class="text-left px-6 py-3.5 text-xs font-semibold text-hemo-gray uppercase tracking-wider">Volume</th>
                    <th class="text-left px-6 py-3.5 text-xs font-semibold text-hemo-gray uppercase tracking-wider">Status</th>
                </tr>
            </thead>
            <tbody>
                <?php if (empty($donations)): ?>
                    <tr>
                        <td colspan="5" class="px-6 py-12 text-center text-hemo-gray">
                            <i class="fas fa-droplet text-3xl mb-3 block text-hemo-border"></i>
                            <p>No donations recorded yet</p>
                            <p class="text-xs mt-1">Schedule your first donation to get started</p>
                        </td>
                    </tr>
                <?php else: ?>
                    <?php foreach ($donations as $don): ?>
                    <tr class="table-row border-b border-hemo-border last:border-0 transition-fast">
                        <td class="px-6 py-4 text-sm font-mono text-hemo-red font-semibold">#DON-<?php echo str_pad($don['donation_id'], 5, '0', STR_PAD_LEFT); ?></td>
                        <td class="px-6 py-4 text-sm text-hemo-charcoal"><?php echo date('M d, Y', strtotime($don['donation_date'])); ?></td>
                        <td class="px-6 py-4">
                            <span class="inline-flex items-center gap-1.5 px-3 py-1 rounded-lg bg-hemo-red text-white text-xs font-bold">
                                <i class="fas fa-droplet text-[9px]"></i> <?php echo sanitize($don['blood_type'] ?? 'N/A'); ?>
                            </span>
                        </td>
                        <td class="px-6 py-4 text-sm font-semibold text-hemo-navy"><?php echo $don['volume_ml'] ?? '—'; ?> ml</td>
                        <td class="px-6 py-4">
                            <?php 
                            $dStatusColors = [
                                'Completed' => 'bg-green-100 text-hemo-success',
                                'Pending' => 'bg-amber-100 text-amber-700',
                                'Cancelled' => 'bg-red-100 text-hemo-warning',
                            ];
                            $dsc = $dStatusColors[$don['status'] ?? 'Pending'] ?? 'bg-gray-100 text-hemo-gray';
                            ?>
                            <span class="inline-flex items-center gap-1.5 px-3 py-1 rounded-full text-xs font-semibold <?php echo $dsc; ?>">
                                <span class="w-1.5 h-1.5 rounded-full bg-current"></span>
                                <?php echo sanitize($don['status'] ?? 'Pending'); ?>
                            </span>
                        </td>
                    </tr>
                    <?php endforeach; ?>
                <?php endif; ?>
            </tbody>
        </table>
    </div>
</div> -->

<?php
$content = ob_get_clean();
require_once __DIR__ . '/../layouts/dashboard_layout.php';
?>
