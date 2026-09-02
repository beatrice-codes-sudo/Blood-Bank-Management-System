<?php
/**
 * Admin Dashboard
 */
$pageTitle = 'Admin Dashboard';
$data = $data ?? [];

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

<!-- Critical Stock Alert Banner (Subtle & Clinical) -->
<?php if (!empty($data['critical_stock'])): ?>
<div class="mb-6 p-5 rounded-2xl bg-white border border-rose-200 shadow-sm flex flex-col lg:flex-row lg:items-center justify-between gap-4">
    <div class="flex items-start sm:items-center gap-3.5">
        <div class="w-10 h-10 rounded-xl bg-rose-50 border border-rose-200 flex items-center justify-center text-hemo-red flex-shrink-0">
            <i class="fas fa-triangle-exclamation text-base"></i>
        </div>
        <div>
            <div class="flex items-center gap-2 mb-0.5">
                <span class="text-xs font-bold text-rose-900 uppercase tracking-wider">Inventory Shortage Alert</span>
                <span class="text-xs text-hemo-gray">• <?php echo count($data['critical_stock']); ?> blood group(s) below safety threshold</span>
            </div>
            <div class="flex flex-wrap items-center gap-2 mt-1">
                <?php foreach ($data['critical_stock'] as $crit): ?>
                <span class="inline-flex items-center gap-1.5 px-2.5 py-1 rounded-lg bg-rose-50 border border-rose-200 text-xs">
                    <strong class="text-hemo-navy"><?php echo $crit['blood_type']; ?>:</strong>
                    <span class="text-rose-700 font-bold"><?php echo $crit['unit_count']; ?>/<?php echo $crit['min_threshold']; ?> units</span>
                    <span class="text-[10px] text-gray-500 font-medium">(-<?php echo $crit['deficit']; ?> deficit)</span>
                </span>
                <?php endforeach; ?>
            </div>
        </div>
    </div>
    
    <div class="flex items-center gap-2 flex-shrink-0">
        <button onclick="openEmergencyAppealModal('<?php echo $data['critical_stock'][0]['blood_type'] ?? 'O-'; ?>')" 
                class="px-4 py-2 rounded-xl bg-hemo-red hover:bg-hemo-deep-red text-white font-bold text-xs shadow-sm transition-fast flex items-center gap-1.5">
            <i class="fas fa-bullhorn text-xs"></i> Mobilize Donors
        </button>
        <button onclick="openAddUnitsModal('<?php echo $data['critical_stock'][0]['blood_type'] ?? 'O-'; ?>')" 
                class="px-3.5 py-2 rounded-xl bg-hemo-light-gray hover:bg-gray-200 text-hemo-charcoal font-semibold text-xs transition-fast flex items-center gap-1.5">
            <i class="fas fa-plus text-xs"></i> Intake
        </button>
        <button onclick="openThresholdModal()" 
                class="p-2 rounded-xl bg-hemo-light-gray hover:bg-gray-200 text-hemo-charcoal transition-fast flex items-center justify-center border border-hemo-border" title="Adjust Thresholds">
            <i class="fas fa-sliders text-xs"></i>
        </button>
    </div>
</div>
<?php endif; ?>

<!-- Active In-App Mobilization Notice (Shown when appeals are live on donor dashboards) -->
<?php if (!empty($data['active_appeals'])): ?>
<div class="mb-6 space-y-2">
    <?php foreach ($data['active_appeals'] as $app): ?>
    <div class="p-3.5 rounded-xl bg-amber-50/80 border border-amber-200 flex flex-col sm:flex-row sm:items-center justify-between gap-3 text-xs">
        <div class="flex items-center gap-2.5">
            <span class="w-2 h-2 rounded-full bg-amber-500 animate-pulse"></span>
            <span class="font-bold text-amber-950">Live Donor In-App Appeal: Group <?php echo sanitize($app['blood_type']); ?></span>
            <span class="text-amber-800 hidden md:inline">— Displaying callout banner on eligible donor dashboards</span>
        </div>
        <form action="<?php echo BASE_URL; ?>/index.php?page=admin_resolve_appeal" method="POST" class="flex-shrink-0">
            <input type="hidden" name="appeal_id" value="<?php echo $app['appeal_id']; ?>">
            <button type="submit" class="px-3 py-1 rounded-lg bg-white border border-amber-300 text-amber-900 font-semibold text-[11px] hover:bg-amber-100 transition-fast">
                <i class="fas fa-check text-[10px] mr-1"></i> End Appeal
            </button>
        </form>
    </div>
    <?php endforeach; ?>
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
    <div class="lg:col-span-2 bg-white rounded-2xl shadow-card border border-hemo-border overflow-hidden" id="inventory">
        <div class="p-6 border-b border-hemo-border flex flex-col sm:flex-row sm:items-center justify-between gap-4">
            <div>
                <div class="flex items-center gap-2.5">
                    <div class="w-8 h-8 rounded-lg bg-red-50 flex items-center justify-center text-hemo-red">
                        <i class="fas fa-droplet"></i>
                    </div>
                    <h3 class="text-lg font-bold text-hemo-navy">Blood Inventory</h3>
                </div>
                <p class="text-xs text-hemo-gray mt-0.5">Real-time reserves and safety threshold monitoring</p>
            </div>
            
            <div class="flex items-center gap-2">
                <button type="button" onclick="openAddUnitsModal()" 
                        class="px-3.5 py-2 rounded-xl bg-hemo-red hover:bg-hemo-deep-red text-white text-xs font-bold shadow-button transition-fast flex items-center gap-1.5">
                    <i class="fas fa-plus"></i> <span>Log Intake</span>
                </button>
                <button type="button" onclick="openThresholdModal()" 
                        class="px-3 py-2 rounded-xl bg-hemo-light-gray hover:bg-gray-200 text-hemo-navy text-xs font-semibold transition-fast flex items-center gap-1.5 border border-hemo-border" title="Adjust Thresholds">
                    <i class="fas fa-sliders"></i> <span class="hidden sm:inline">Thresholds</span>
                </button>
            </div>
        </div>
        
        <div class="p-6">
            <div class="grid grid-cols-2 sm:grid-cols-4 gap-4">
                <?php foreach ($data['inventory'] as $item): 
                    $count = (int)$item['unit_count'];
                    $min = (int)$item['min_threshold'];
                    $status = $item['stock_status']; // 'Empty', 'Critical', 'Adequate'
                    
                    if ($status === 'Empty') {
                        $cardBorder = 'border-red-200 bg-red-50/30';
                        $statusBadge = '<span class="text-[10px] font-bold text-red-600 bg-red-100 px-2 py-0.5 rounded">Empty</span>';
                        $numColor = 'text-red-600';
                    } elseif ($status === 'Critical') {
                        $cardBorder = 'border-amber-200 bg-amber-50/20';
                        $statusBadge = '<span class="text-[10px] font-bold text-amber-700 bg-amber-100 px-2 py-0.5 rounded">Low</span>';
                        $numColor = 'text-amber-600';
                    } else {
                        $cardBorder = 'border-hemo-border bg-white hover:border-gray-300';
                        $statusBadge = '<span class="text-[10px] font-bold text-emerald-700 bg-emerald-50 px-2 py-0.5 rounded">Optimal</span>';
                        $numColor = 'text-hemo-navy';
                    }
                ?>
                <div class="flex flex-col justify-between p-4 rounded-xl border <?php echo $cardBorder; ?> transition-fast">
                    <div class="flex items-center justify-between mb-2">
                        <span class="text-xl font-bold font-display text-hemo-navy"><?php echo sanitize($item['blood_type']); ?></span>
                        <?php echo $statusBadge; ?>
                    </div>

                    <div class="my-1.5">
                        <div class="flex items-baseline gap-1">
                            <span class="text-2xl font-bold <?php echo $numColor; ?>"><?php echo $count; ?></span>
                            <span class="text-xs text-hemo-gray">/ <?php echo $min; ?> min</span>
                        </div>
                        
                        <!-- Progress Bar -->
                        <div class="w-full h-1 bg-gray-100 rounded-full overflow-hidden mt-2">
                            <div class="h-full <?php echo $count === 0 ? 'bg-red-500' : ($count < $min ? 'bg-amber-500' : 'bg-emerald-500'); ?>" 
                                 style="width: <?php echo $item['fill_percent']; ?>%"></div>
                        </div>
                    </div>

                    <!-- Action Link -->
                    <div class="pt-2.5 border-t border-hemo-border/60 flex items-center justify-between text-[11px] mt-1">
                        <?php if ($count < $min): ?>
                        <button type="button" onclick="openEmergencyAppealModal('<?php echo $item['blood_type']; ?>')" 
                                class="text-hemo-red hover:underline font-semibold flex items-center gap-1">
                            <i class="fas fa-bullhorn text-[10px]"></i> Mobilize
                        </button>
                        <?php else: ?>
                        <a href="<?php echo BASE_URL; ?>/index.php?page=admin_donors&blood_type=<?php echo urlencode($item['blood_type']); ?>" 
                           class="text-hemo-gray hover:text-hemo-navy font-medium flex items-center gap-1">
                            <i class="fas fa-users text-[10px]"></i> Donors
                        </a>
                        <?php endif; ?>
                        
                        <button type="button" onclick="openAddUnitsModal('<?php echo $item['blood_type']; ?>')" 
                                class="text-hemo-gray hover:text-hemo-red font-semibold flex items-center gap-1" title="Log Intake">
                            <i class="fas fa-plus text-[10px]"></i> Intake
                        </button>
                    </div>
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
                                <span class="inline-flex items-center px-2.5 py-1 rounded-full text-xs font-semibold bg-green-100 text-hemo-success">Active</span>
                            <?php else: ?>
                                <span class="inline-flex items-center px-2.5 py-1 rounded-full text-xs font-semibold bg-red-100 text-hemo-warning">Inactive</span>
                            <?php endif; ?>
                        </td>
                    </tr>
                    <?php endforeach; ?>
                <?php endif; ?>
            </tbody>
        </table>
    </div>
</div>

<!-- Modal 1: Log / Intake Blood Units -->
<div id="addUnitsModal" class="fixed inset-0 bg-hemo-navy/50 backdrop-blur-sm z-[1001] hidden flex items-center justify-center p-4 opacity-0 transition-opacity duration-300">
    <div class="bg-white rounded-2xl shadow-2xl w-full max-w-lg flex flex-col transform scale-95 transition-transform duration-300 overflow-hidden">
        <div class="px-6 py-4 border-b border-hemo-border bg-red-50/70 flex items-center justify-between">
            <h3 class="text-base font-bold text-hemo-navy flex items-center gap-2">
                <i class="fas fa-boxes-stacked text-hemo-red"></i> Log Blood Units Intake
            </h3>
            <button type="button" onclick="closeModal('addUnitsModal')" class="text-hemo-charcoal hover:text-hemo-red"><i class="fas fa-times"></i></button>
        </div>
        <form action="<?php echo BASE_URL; ?>/index.php?page=admin_add_blood_units" method="POST" class="p-6 space-y-4">
            <p class="text-xs text-hemo-charcoal">
                Register verified incoming whole blood units from laboratory testing into active inventory.
            </p>

            <div class="grid grid-cols-2 gap-4">
                <div>
                    <label class="block text-xs font-semibold text-hemo-navy mb-1">Blood Group *</label>
                    <select name="blood_type" id="intake_blood_type" required class="w-full px-3 py-2 rounded-lg border border-hemo-border text-xs bg-white focus:border-hemo-red outline-none font-bold text-hemo-navy">
                        <?php foreach (BLOOD_TYPES as $bt): ?>
                            <option value="<?php echo $bt; ?>"><?php echo $bt; ?></option>
                        <?php endforeach; ?>
                    </select>
                </div>
                <div>
                    <label class="block text-xs font-semibold text-hemo-navy mb-1">Number of Units *</label>
                    <input type="number" name="units_count" id="intake_units_count" min="1" max="100" value="5" required
                           class="w-full px-3 py-2 rounded-lg border border-hemo-border text-xs focus:border-hemo-red outline-none font-bold">
                </div>
            </div>

            <div class="grid grid-cols-2 gap-4">
                <div>
                    <label class="block text-xs font-semibold text-hemo-navy mb-1">Volume per Unit (ml)</label>
                    <input type="number" name="volume_ml" value="450" min="300" max="600" step="10" required
                           class="w-full px-3 py-2 rounded-lg border border-hemo-border text-xs focus:border-hemo-red outline-none">
                </div>
                <div>
                    <label class="block text-xs font-semibold text-hemo-navy mb-1">Collection Date *</label>
                    <input type="date" name="collection_date" id="intake_collection_date" value="<?php echo date('Y-m-d'); ?>" required onchange="calculateIntakeExpiry(this.value)"
                           class="w-full px-3 py-2 rounded-lg border border-hemo-border text-xs focus:border-hemo-red outline-none">
                </div>
            </div>

            <div>
                <label class="block text-xs font-semibold text-hemo-navy mb-1">Computed Expiry Date (CPDA-1: 42 Days)</label>
                <input type="date" name="expiry_date" id="intake_expiry_date" value="<?php echo date('Y-m-d', strtotime('+42 days')); ?>" required
                       class="w-full px-3 py-2 rounded-lg border border-hemo-border text-xs bg-gray-50 focus:border-hemo-red outline-none text-hemo-charcoal">
            </div>

            <div class="flex gap-3 pt-3">
                <button type="button" onclick="closeModal('addUnitsModal')" class="flex-1 py-2.5 rounded-xl bg-hemo-light-gray text-hemo-charcoal font-semibold text-xs hover:bg-gray-200 transition-fast">Cancel</button>
                <button type="submit" class="flex-1 py-2.5 rounded-xl bg-hemo-red hover:bg-hemo-deep-red text-white font-bold text-xs shadow-md transition-fast flex items-center justify-center gap-1.5">
                    <i class="fas fa-plus-circle"></i> <span>Add to Inventory</span>
                </button>
            </div>
        </form>
    </div>
</div>

<!-- Modal 2: Configure Stock Thresholds -->
<div id="thresholdModal" class="fixed inset-0 bg-hemo-navy/50 backdrop-blur-sm z-[1001] hidden flex items-center justify-center p-4 opacity-0 transition-opacity duration-300">
    <div class="bg-white rounded-2xl shadow-2xl w-full max-w-lg flex flex-col transform scale-95 transition-transform duration-300 overflow-hidden">
        <div class="px-6 py-4 border-b border-hemo-border bg-slate-50 flex items-center justify-between">
            <h3 class="text-base font-bold text-hemo-navy flex items-center gap-2">
                <i class="fas fa-sliders text-hemo-navy"></i> Configure Safety Stock Thresholds
            </h3>
            <button type="button" onclick="closeModal('thresholdModal')" class="text-hemo-charcoal hover:text-hemo-red"><i class="fas fa-times"></i></button>
        </div>
        <form action="<?php echo BASE_URL; ?>/index.php?page=admin_update_thresholds" method="POST" class="p-6 space-y-4">
            <p class="text-xs text-hemo-charcoal">
                Set the minimum reserve units required before the system triggers critical shortage alerts for each blood group.
            </p>

            <div class="grid grid-cols-2 sm:grid-cols-4 gap-3">
                <?php 
                $thresholds = $data['thresholds'] ?? [];
                foreach (BLOOD_TYPES as $bt): 
                    $tVal = (int)($thresholds[$bt] ?? 5);
                ?>
                <div class="p-3 rounded-xl bg-hemo-light-gray border border-hemo-border text-center">
                    <label class="block font-black font-display text-sm text-hemo-navy mb-1"><?php echo $bt; ?></label>
                    <div class="flex items-center justify-center gap-1">
                        <input type="number" name="thresholds[<?php echo $bt; ?>]" value="<?php echo $tVal; ?>" min="1" max="100" required
                               class="w-16 px-2 py-1.5 text-center font-bold text-xs rounded-lg border border-gray-300 focus:border-hemo-red outline-none">
                        <span class="text-[10px] text-gray-500">units</span>
                    </div>
                </div>
                <?php endforeach; ?>
            </div>

            <div class="p-3 bg-blue-50/70 rounded-xl border border-blue-200 text-[11px] text-blue-900 leading-relaxed">
                <i class="fas fa-circle-info text-blue-600 mr-1"></i>
                <strong>Clinical Best Practice:</strong> Universal types (O- and O+) generally require higher minimum reserve thresholds ($\ge 8$ units) due to emergency cross-matching requirements.
            </div>

            <div class="flex gap-3 pt-2">
                <button type="button" onclick="closeModal('thresholdModal')" class="flex-1 py-2.5 rounded-xl bg-hemo-light-gray text-hemo-charcoal font-semibold text-xs hover:bg-gray-200 transition-fast">Cancel</button>
                <button type="submit" class="flex-1 py-2.5 rounded-xl bg-hemo-navy hover:bg-slate-800 text-white font-bold text-xs shadow-md transition-fast flex items-center justify-center gap-1.5">
                    <i class="fas fa-save"></i> <span>Save Thresholds</span>
                </button>
            </div>
        </form>
    </div>
</div>

<!-- Modal 3: Emergency Donor Mobilization & In-App Appeal -->
<div id="emergencyAppealModal" class="fixed inset-0 bg-hemo-navy/50 backdrop-blur-sm z-[1001] hidden flex items-center justify-center p-4 opacity-0 transition-opacity duration-300">
    <div class="bg-white rounded-2xl shadow-2xl w-full max-w-lg flex flex-col transform scale-95 transition-transform duration-300 overflow-hidden">
        <div class="px-6 py-4 border-b border-hemo-border bg-rose-50 flex items-center justify-between">
            <h3 class="text-base font-bold text-rose-950 flex items-center gap-2">
                <i class="fas fa-bullhorn text-hemo-red"></i> Emergency Donor Mobilization
            </h3>
            <button type="button" onclick="closeModal('emergencyAppealModal')" class="text-rose-800 hover:text-hemo-red"><i class="fas fa-times"></i></button>
        </div>
        <form action="<?php echo BASE_URL; ?>/index.php?page=admin_send_emergency_appeal" method="POST" class="p-6 space-y-4">
            <div class="grid grid-cols-2 gap-4">
                <div>
                    <label class="block text-xs font-semibold text-hemo-navy mb-1">Target Blood Group *</label>
                    <select name="blood_type" id="appeal_blood_type" required onchange="fetchEligibleDonors(this.value)"
                            class="w-full px-3 py-2 rounded-lg border border-hemo-border text-xs bg-white focus:border-hemo-red outline-none font-bold text-hemo-navy">
                        <?php foreach (BLOOD_TYPES as $bt): ?>
                            <option value="<?php echo $bt; ?>"><?php echo $bt; ?></option>
                        <?php endforeach; ?>
                    </select>
                </div>
                <div>
                    <label class="block text-xs font-semibold text-hemo-navy mb-1">Urgency Level</label>
                    <select name="urgency" class="w-full px-3 py-2 rounded-lg border border-hemo-border text-xs bg-white focus:border-hemo-red outline-none font-semibold">
                        <option value="Critical Shortage (Code Red)">Critical Shortage (Code Red)</option>
                        <option value="Urgent Replenishment (Code Yellow)">Urgent Replenishment (Code Yellow)</option>
                        <option value="Community Blood Drive">Community Blood Drive</option>
                    </select>
                </div>
            </div>

            <!-- Recipient Counter Badge -->
            <div id="appealDonorCountBox" class="p-3 bg-rose-50/70 rounded-xl border border-rose-200 flex items-center justify-between text-xs">
                <div class="flex items-center gap-2 text-rose-950">
                    <i class="fas fa-users text-hemo-red"></i>
                    <span>Eligible Donors in Registry:</span>
                </div>
                <span id="appealDonorCount" class="font-bold text-rose-900 bg-rose-200/80 px-2.5 py-0.5 rounded-full font-mono">Checking...</span>
            </div>

            <div class="p-3 bg-slate-50 rounded-xl border border-slate-200 text-[11px] text-hemo-charcoal flex items-start gap-2">
                <i class="fas fa-mobile-screen text-hemo-navy mt-0.5"></i>
                <span>Publishing this appeal displays a high-priority callout banner directly on the portal dashboards of all registered donors with matching blood groups.</span>
            </div>

            <div>
                <div class="flex items-center justify-between mb-1">
                    <label class="text-xs font-semibold text-hemo-navy">Donor Callout Message *</label>
                    <button type="button" onclick="regenerateAppealMessage()" class="text-[11px] text-hemo-red hover:underline">Reset Template</button>
                </div>
                <textarea name="appeal_message" id="appeal_message" rows="3" required
                          class="w-full px-3 py-2 rounded-lg border border-hemo-border text-xs bg-white focus:border-hemo-red outline-none leading-relaxed"></textarea>
            </div>

            <div class="flex gap-3 pt-2">
                <button type="button" onclick="closeModal('emergencyAppealModal')" class="flex-1 py-2.5 rounded-xl bg-hemo-light-gray text-hemo-charcoal font-semibold text-xs hover:bg-gray-200 transition-fast">Cancel</button>
                <button type="submit" class="flex-1 py-2.5 rounded-xl bg-hemo-red hover:bg-hemo-deep-red text-white font-bold text-xs shadow-md transition-fast flex items-center justify-center gap-1.5">
                    <i class="fas fa-bullhorn"></i> <span>Publish to Donor Dashboards</span>
                </button>
            </div>
        </form>
    </div>
</div>

<script>
    function openModal(id) {
        const modal = document.getElementById(id);
        if (!modal) return;
        modal.classList.remove('hidden');
        setTimeout(() => {
            modal.classList.remove('opacity-0');
            const inner = modal.querySelector('div');
            if (inner) inner.classList.remove('scale-95');
        }, 10);
    }

    function closeModal(id) {
        const modal = document.getElementById(id);
        if (!modal) return;
        modal.classList.add('opacity-0');
        const inner = modal.querySelector('div');
        if (inner) inner.classList.add('scale-95');
        setTimeout(() => {
            modal.classList.add('hidden');
        }, 300);
    }

    function openAddUnitsModal(bloodType = 'A+') {
        const select = document.getElementById('intake_blood_type');
        if (select && bloodType) {
            select.value = bloodType;
        }
        openModal('addUnitsModal');
    }

    function openThresholdModal() {
        openModal('thresholdModal');
    }

    function openEmergencyAppealModal(bloodType = 'O-') {
        const select = document.getElementById('appeal_blood_type');
        if (select && bloodType) {
            select.value = bloodType;
        }
        fetchEligibleDonors(bloodType);
        regenerateAppealMessage();
        openModal('emergencyAppealModal');
    }

    function calculateIntakeExpiry(collectionDateStr) {
        if (!collectionDateStr) return;
        const d = new Date(collectionDateStr);
        d.setDate(d.getDate() + 42);
        const yyyy = d.getFullYear();
        const mm = String(d.getMonth() + 1).padStart(2, '0');
        const dd = String(d.getDate()).padStart(2, '0');
        document.getElementById('intake_expiry_date').value = `${yyyy}-${mm}-${dd}`;
    }

    async function fetchEligibleDonors(bloodType) {
        const countSpan = document.getElementById('appealDonorCount');
        if (!countSpan) return;
        countSpan.textContent = 'Searching...';
        
        try {
            const res = await fetch(`<?php echo BASE_URL; ?>/index.php?page=admin_get_eligible_donors_ajax&blood_type=${encodeURIComponent(bloodType)}`);
            const data = await res.json();
            if (data.success) {
                countSpan.textContent = `${data.count} eligible donors`;
            } else {
                countSpan.textContent = '0 donors';
            }
        } catch (e) {
            countSpan.textContent = 'Ready';
        }
    }

    function regenerateAppealMessage() {
        const bt = document.getElementById('appeal_blood_type') ? document.getElementById('appeal_blood_type').value : 'O-';
        const msg = document.getElementById('appeal_message');
        if (msg) {
            msg.value = `URGENT BLOOD APPEAL: Central Blood Bank is currently experiencing a critical shortage of ${bt} blood reserves. Your donation can save lives today. Please visit your nearest donation centre or book an appointment online.`;
        }
    }
</script>

<?php
$content = ob_get_clean();
require_once __DIR__ . '/../layouts/dashboard_layout.php';
?>
