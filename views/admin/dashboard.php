<?php
/**
 * Admin Dashboard
 */
$pageTitle = 'Admin Dashboard';

// Start output buffering for content injection
ob_start();
?>

<!-- Page Header -->
<div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4 mb-8">
    <div>
        <h1 class="font-display text-[32px] font-bold text-hemo-navy">Dashboard</h1>
        <p class="text-hemo-charcoal mt-1">Welcome back, <?php echo sanitize($_SESSION['first_name'] ?? 'Admin'); ?>. Here's your system overview.</p>
    </div>
    <div class="flex items-center gap-3">
        <span class="text-sm text-hemo-gray"><i class="fas fa-calendar-day mr-1"></i> <?php echo date('l, F j, Y'); ?></span>
    </div>
</div>

<!-- Critical Stock Alert -->
<?php if (!empty($data['critical_stock'])): ?>
<div class="mb-8 p-5 rounded-xl gradient-red text-white flex items-center gap-4 relative overflow-hidden">
    <div class="absolute inset-0 bg-[radial-gradient(circle_at_right,rgba(255,255,255,0.1),transparent)]"></div>
    <div class="relative z-10 flex items-center gap-4 flex-1">
        <div class="w-12 h-12 rounded-xl bg-white/20 flex items-center justify-center flex-shrink-0">
            <i class="fas fa-exclamation-triangle text-xl"></i>
        </div>
        <div>
            <p class="font-bold text-lg">Critical Blood Stock Alert</p>
            <p class="text-white/80 text-sm">
                <?php echo count($data['critical_stock']); ?> blood type(s) are below minimum threshold: 
                <strong><?php echo implode(', ', array_column($data['critical_stock'], 'blood_type')); ?></strong>
            </p>
        </div>
    </div>
    <a href="#inventory" class="relative z-10 px-5 py-2.5 rounded-lg bg-hemo-gold text-hemo-navy font-bold text-sm hover:bg-yellow-400 transition-fast btn-press flex-shrink-0">
        View Details
    </a>
</div>
<?php endif; ?>

<!-- Stats Grid -->
<div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-6 mb-8">
    <!-- Total Donors -->
    <div class="stat-card bg-white rounded-xl shadow-card p-5 card-accent-red transition-default cursor-default">
        <div class="flex items-center justify-between mb-4">
            <div class="w-12 h-12 rounded-xl bg-hemo-light-red flex items-center justify-center">
                <i class="fas fa-users text-hemo-red text-lg"></i>
            </div>
            <span class="text-xs font-bold text-hemo-success bg-green-50 px-2.5 py-1 rounded-full flex items-center gap-1">
                <i class="fas fa-arrow-up text-[8px]"></i> Active
            </span>
        </div>
        <p class="text-3xl font-bold text-hemo-navy"><?php echo number_format($data['total_donors']); ?></p>
        <p class="text-sm text-hemo-gray mt-1">Registered Donors</p>
    </div>

    <!-- Total Hospitals -->
    <div class="stat-card bg-white rounded-xl shadow-card p-5 card-accent-blue transition-default cursor-default">
        <div class="flex items-center justify-between mb-4">
            <div class="w-12 h-12 rounded-xl bg-blue-50 flex items-center justify-center">
                <i class="fas fa-hospital text-hemo-info text-lg"></i>
            </div>
            <span class="text-xs font-bold text-hemo-info bg-blue-50 px-2.5 py-1 rounded-full flex items-center gap-1">
                <i class="fas fa-link text-[8px]"></i> Partners
            </span>
        </div>
        <p class="text-3xl font-bold text-hemo-navy"><?php echo number_format($data['total_hospitals']); ?></p>
        <p class="text-sm text-hemo-gray mt-1">Partner Hospitals</p>
    </div>

    <!-- Blood Units -->
    <div class="stat-card bg-white rounded-xl shadow-card p-5 card-accent-green transition-default cursor-default">
        <div class="flex items-center justify-between mb-4">
            <div class="w-12 h-12 rounded-xl bg-green-50 flex items-center justify-center">
                <i class="fas fa-droplet text-hemo-success text-lg"></i>
            </div>
            <span class="text-xs font-bold text-hemo-success bg-green-50 px-2.5 py-1 rounded-full flex items-center gap-1">
                <i class="fas fa-box text-[8px]"></i> In Stock
            </span>
        </div>
        <p class="text-3xl font-bold text-hemo-navy"><?php echo number_format($data['total_units']); ?></p>
        <p class="text-sm text-hemo-gray mt-1">Blood Units Available</p>
    </div>

    <!-- Pending Requests -->
    <div class="stat-card bg-white rounded-xl shadow-card p-5 card-accent-amber transition-default cursor-default">
        <div class="flex items-center justify-between mb-4">
            <div class="w-12 h-12 rounded-xl bg-amber-50 flex items-center justify-center">
                <i class="fas fa-clipboard-list text-hemo-amber text-lg"></i>
            </div>
            <span class="text-xs font-bold text-hemo-amber bg-amber-50 px-2.5 py-1 rounded-full flex items-center gap-1">
                <i class="fas fa-clock text-[8px]"></i> Pending
            </span>
        </div>
        <p class="text-3xl font-bold text-hemo-navy"><?php echo number_format($data['pending_requests']); ?></p>
        <p class="text-sm text-hemo-gray mt-1">Pending Requests</p>
    </div>
</div>

<!-- Content Grid -->
<div class="grid lg:grid-cols-3 gap-6 mb-8">
    <!-- Blood Inventory Grid -->
    <div class="lg:col-span-2 bg-white rounded-xl shadow-card" id="inventory">
        <div class="p-6 border-b border-hemo-border flex items-center justify-between">
            <div class="flex items-center gap-3">
                <i class="fas fa-droplet text-hemo-red"></i>
                <h3 class="text-lg font-semibold text-hemo-navy">Blood Inventory</h3>
            </div>
            <span class="text-xs text-hemo-gray">Real-time stock levels</span>
        </div>
        <div class="p-6">
            <div class="grid grid-cols-2 sm:grid-cols-4 gap-4">
                <?php foreach ($data['inventory'] as $item): 
                    $count = (int)$item['unit_count'];
                    $bgClass = $count === 0 ? 'bg-red-50 border-red-200' : ($count < 5 ? 'bg-amber-50 border-amber-200' : 'bg-hemo-light-gray border-transparent');
                    $textClass = $count === 0 ? 'text-hemo-warning' : ($count < 5 ? 'text-hemo-amber' : 'text-hemo-red');
                ?>
                <div class="blood-card flex flex-col items-center justify-center p-5 rounded-xl border-2 <?php echo $bgClass; ?> transition-default cursor-default hover:bg-white">
                    <span class="text-2xl font-bold text-hemo-navy"><?php echo sanitize($item['blood_type']); ?></span>
                    <span class="text-xl font-bold <?php echo $textClass; ?> mt-2"><?php echo $count; ?></span>
                    <span class="text-xs text-hemo-gray mt-1">units</span>
                </div>
                <?php endforeach; ?>
            </div>
        </div>
    </div>

    <!-- Quick Stats Sidebar -->
    <div class="space-y-6">
        <!-- System Health -->
        <div class="bg-white rounded-xl shadow-card p-6 card-accent-green">
            <div class="flex items-center gap-3 mb-4">
                <i class="fas fa-heart-pulse text-hemo-success"></i>
                <h3 class="text-lg font-semibold text-hemo-navy">System Status</h3>
            </div>
            <div class="space-y-3">
                <div class="flex items-center justify-between py-2">
                    <span class="text-sm text-hemo-charcoal">Database</span>
                    <span class="text-xs font-bold text-hemo-success bg-green-50 px-3 py-1 rounded-full">Online</span>
                </div>
                <div class="flex items-center justify-between py-2 border-t border-hemo-border">
                    <span class="text-sm text-hemo-charcoal">Active Sessions</span>
                    <span class="text-sm font-bold text-hemo-navy">1</span>
                </div>
                <div class="flex items-center justify-between py-2 border-t border-hemo-border">
                    <span class="text-sm text-hemo-charcoal">Last Backup</span>
                    <span class="text-sm font-bold text-hemo-navy">Today</span>
                </div>
            </div>
        </div>

        <!-- Quick Actions -->
        <div class="bg-white rounded-xl shadow-card p-6 card-accent-gold">
            <div class="flex items-center gap-3 mb-4">
                <i class="fas fa-bolt text-hemo-gold"></i>
                <h3 class="text-lg font-semibold text-hemo-navy">Quick Actions</h3>
            </div>
            <div class="space-y-2">
                <a href="<?php echo BASE_URL; ?>/index.php?page=admin_donors" class="flex items-center gap-3 px-4 py-3 rounded-lg hover:bg-hemo-light-red text-sm text-hemo-charcoal hover:text-hemo-red transition-fast">
                    <i class="fas fa-user-plus w-5 text-center text-hemo-red"></i> View Donors
                </a>
                <a href="<?php echo BASE_URL; ?>/index.php?page=admin_hospitals" class="flex items-center gap-3 px-4 py-3 rounded-lg hover:bg-hemo-light-red text-sm text-hemo-charcoal hover:text-hemo-red transition-fast">
                    <i class="fas fa-hospital w-5 text-center text-hemo-red"></i> View Hospitals
                </a>
                <a href="<?php echo BASE_URL; ?>/index.php?page=admin_users" class="flex items-center gap-3 px-4 py-3 rounded-lg hover:bg-hemo-light-red text-sm text-hemo-charcoal hover:text-hemo-red transition-fast">
                    <i class="fas fa-user-shield w-5 text-center text-hemo-red"></i> Manage Users
                </a>
            </div>
        </div>
    </div>
</div>

<!-- Recent Registrations Table -->
<div class="bg-white rounded-xl shadow-card">
    <div class="p-6 border-b border-hemo-border flex items-center justify-between">
        <div class="flex items-center gap-3">
            <i class="fas fa-user-plus text-hemo-red"></i>
            <h3 class="text-lg font-semibold text-hemo-navy">Recent Registrations</h3>
        </div>
        <a href="<?php echo BASE_URL; ?>/index.php?page=admin_users" class="text-sm text-hemo-red font-semibold hover:text-hemo-deep-red transition-fast">View All →</a>
    </div>
    <div class="overflow-x-auto">
        <table class="w-full">
            <thead>
                <tr class="bg-hemo-light-gray">
                    <th class="text-left px-6 py-3.5 text-xs font-semibold text-hemo-gray uppercase tracking-wider">User</th>
                    <th class="text-left px-6 py-3.5 text-xs font-semibold text-hemo-gray uppercase tracking-wider">Role</th>
                    <th class="text-left px-6 py-3.5 text-xs font-semibold text-hemo-gray uppercase tracking-wider">Email</th>
                    <th class="text-left px-6 py-3.5 text-xs font-semibold text-hemo-gray uppercase tracking-wider">Registered</th>
                    <th class="text-left px-6 py-3.5 text-xs font-semibold text-hemo-gray uppercase tracking-wider">Status</th>
                </tr>
            </thead>
            <tbody>
                <?php if (empty($data['recent_users'])): ?>
                    <tr>
                        <td colspan="5" class="px-6 py-12 text-center text-hemo-gray">
                            <i class="fas fa-users text-3xl mb-3 block text-hemo-border"></i>
                            No users registered yet
                        </td>
                    </tr>
                <?php else: ?>
                    <?php foreach ($data['recent_users'] as $user): ?>
                    <tr class="table-row border-b border-hemo-border last:border-0 transition-fast">
                        <td class="px-6 py-4">
                            <div class="flex items-center gap-3">
                                <div class="w-9 h-9 rounded-full gradient-red flex items-center justify-center text-white font-semibold text-xs flex-shrink-0">
                                    <?php echo strtoupper(substr($user['first_name'], 0, 1) . substr($user['last_name'], 0, 1)); ?>
                                </div>
                                <div>
                                    <p class="text-sm font-semibold text-hemo-navy"><?php echo sanitize($user['first_name'] . ' ' . $user['last_name']); ?></p>
                                    <p class="text-xs text-hemo-gray font-mono">@<?php echo sanitize($user['username']); ?></p>
                                </div>
                            </div>
                        </td>
                        <td class="px-6 py-4">
                            <?php 
                            $roleBg = $user['role'] === 'Admin' ? 'bg-purple-100 text-purple-700' : 
                                     ($user['role'] === 'Hospital' ? 'bg-blue-100 text-blue-700' : 'bg-red-100 text-hemo-red');
                            $roleIcon = $user['role'] === 'Admin' ? 'fa-shield' : 
                                       ($user['role'] === 'Hospital' ? 'fa-hospital' : 'fa-heart');
                            ?>
                            <span class="inline-flex items-center gap-1.5 px-3 py-1 rounded-full text-xs font-semibold <?php echo $roleBg; ?>">
                                <i class="fas <?php echo $roleIcon; ?> text-[9px]"></i>
                                <?php echo sanitize($user['role']); ?>
                            </span>
                        </td>
                        <td class="px-6 py-4 text-sm text-hemo-charcoal"><?php echo sanitize($user['email']); ?></td>
                        <td class="px-6 py-4 text-sm text-hemo-gray"><?php echo date('M d, Y', strtotime($user['created_at'])); ?></td>
                        <td class="px-6 py-4">
                            <?php if ($user['is_active']): ?>
                                <span class="inline-flex items-center gap-1.5 px-3 py-1 rounded-full text-xs font-semibold bg-green-100 text-hemo-success">
                                    <span class="w-1.5 h-1.5 rounded-full bg-hemo-success"></span> Active
                                </span>
                            <?php else: ?>
                                <span class="inline-flex items-center gap-1.5 px-3 py-1 rounded-full text-xs font-semibold bg-red-100 text-hemo-warning">
                                    <span class="w-1.5 h-1.5 rounded-full bg-hemo-warning"></span> Inactive
                                </span>
                            <?php endif; ?>
                        </td>
                    </tr>
                    <?php endforeach; ?>
                <?php endif; ?>
            </tbody>
        </table>
    </div>
</div>

<?php
$content = ob_get_clean();
require_once __DIR__ . '/../layouts/dashboard_layout.php';
?>
