<?php
/**
 * Hospital Manager Dashboard
 */
$pageTitle = 'Hospital Dashboard';
$hospital = $data['hospital'] ?? null;
$stats = $data['stats'] ?? ['total_requests' => 0, 'fulfilled' => 0, 'pending' => 0, 'rejected' => 0];
$requests = $data['requests'] ?? [];
$inventory = $data['inventory'] ?? [];

ob_start();
?>

<!-- Page Header -->
<div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4 mb-8">
    <div>
        <h1 class="font-display text-[32px] font-bold text-hemo-navy">Hospital Dashboard</h1>
        <p class="text-hemo-charcoal mt-1">
            <?php if ($hospital): ?>
                Managing <span class="font-semibold text-hemo-red"><?php echo sanitize($hospital['hospital_name']); ?></span>
            <?php else: ?>
                Welcome, <?php echo sanitize($_SESSION['first_name'] ?? 'Manager'); ?>
            <?php endif; ?>
        </p>
    </div>
    <!-- <div class="flex items-center gap-3">
        <button onclick="openModal('addRequestModal')" class="inline-flex items-center gap-2 px-4 py-2 rounded-lg bg-hemo-red text-white font-semibold text-sm hover:bg-hemo-deep-red transition-default shadow-button">
            <i class="fas fa-plus"></i> New Blood Request
        </button>
    </div> -->
</div>

<!-- Stats Grid -->
<div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-6 mb-8">
    <div class="stat-card bg-white rounded-xl shadow-card p-5 card-accent-red transition-default">
        <div class="flex items-center justify-between mb-4">
            <div class="w-12 h-12 rounded-xl bg-hemo-light-red flex items-center justify-center">
                <i class="fas fa-clipboard-list text-hemo-red text-lg"></i>
            </div>
        </div>
        <p class="text-3xl font-bold text-hemo-navy"><?php echo number_format($stats['total_requests']); ?></p>
        <p class="text-sm text-hemo-gray mt-1">Total Requests</p>
    </div>

    <div class="stat-card bg-white rounded-xl shadow-card p-5 card-accent-green transition-default">
        <div class="flex items-center justify-between mb-4">
            <div class="w-12 h-12 rounded-xl bg-green-50 flex items-center justify-center">
                <i class="fas fa-check-circle text-hemo-success text-lg"></i>
            </div>
        </div>
        <p class="text-3xl font-bold text-hemo-navy"><?php echo number_format($stats['fulfilled']); ?></p>
        <p class="text-sm text-hemo-gray mt-1">Fulfilled</p>
    </div>

    <div class="stat-card bg-white rounded-xl shadow-card p-5 card-accent-amber transition-default">
        <div class="flex items-center justify-between mb-4">
            <div class="w-12 h-12 rounded-xl bg-amber-50 flex items-center justify-center">
                <i class="fas fa-clock text-hemo-amber text-lg"></i>
            </div>
        </div>
        <p class="text-3xl font-bold text-hemo-navy"><?php echo number_format($stats['pending']); ?></p>
        <p class="text-sm text-hemo-gray mt-1">Pending</p>
    </div>

    <div class="stat-card bg-white rounded-xl shadow-card p-5 card-accent-deep-red transition-default">
        <div class="flex items-center justify-between mb-4">
            <div class="w-12 h-12 rounded-xl bg-red-50 flex items-center justify-center">
                <i class="fas fa-times-circle text-hemo-warning text-lg"></i>
            </div>
        </div>
        <p class="text-3xl font-bold text-hemo-navy"><?php echo number_format($stats['rejected']); ?></p>
        <p class="text-sm text-hemo-gray mt-1">Rejected</p>
    </div>
</div>

<!-- Content Grid -->
<div class="grid lg:grid-cols-3 gap-6 mb-8">
    <!-- Hospital Profile Card -->
    <div class="bg-white rounded-xl shadow-card p-6 card-accent-blue">
        <div class="flex items-center gap-3 mb-6 pb-4 border-b border-hemo-border">
            <i class="fas fa-hospital text-hemo-info"></i>
            <h3 class="text-lg font-semibold text-hemo-navy">Hospital Profile</h3>
        </div>
        <?php if ($hospital): ?>
        <div class="flex items-start gap-4 mb-6">
            <div class="w-16 h-16 rounded-2xl bg-blue-50 flex items-center justify-center flex-shrink-0">
                <i class="fas fa-hospital text-hemo-info text-2xl"></i>
            </div>
            <div>
                <h4 class="text-lg font-bold text-hemo-navy"><?php echo sanitize($hospital['hospital_name']); ?></h4>
                <p class="text-xs text-hemo-red font-mono mt-1"><?php echo sanitize($hospital['hospital_code']); ?></p>
            </div>
        </div>
        <div class="space-y-3">
            <div class="flex items-center gap-3 text-sm">
                <i class="fas fa-location-dot w-5 text-center text-hemo-red"></i>
                <span class="text-hemo-charcoal"><?php echo sanitize($hospital['address'] . ', ' . $hospital['city']); ?></span>
            </div>
            <div class="flex items-center gap-3 text-sm">
                <i class="fas fa-phone w-5 text-center text-hemo-red"></i>
                <span class="text-hemo-charcoal"><?php echo sanitize($hospital['phone']); ?></span>
            </div>
            <div class="flex items-center gap-3 text-sm">
                <i class="fas fa-envelope w-5 text-center text-hemo-red"></i>
                <span class="text-hemo-charcoal"><?php echo sanitize($hospital['email'] ?? $hospital['manager_email']); ?></span>
            </div>
            <?php if ($hospital['license_number']): ?>
            <div class="flex items-center gap-3 text-sm">
                <i class="fas fa-id-card w-5 text-center text-hemo-red"></i>
                <span class="text-hemo-charcoal"><?php echo sanitize($hospital['license_number']); ?></span>
            </div>
            <?php endif; ?>
        </div>
        <?php else: ?>
        <div class="text-center py-8 text-hemo-gray">
            <i class="fas fa-hospital text-4xl mb-3 text-hemo-border"></i>
            <p>Hospital profile not found</p>
        </div>
        <?php endif; ?>
    </div>

    <!-- Blood Availability -->
    <div class="lg:col-span-2 bg-white rounded-xl shadow-card">
        <div class="p-6 border-b border-hemo-border flex items-center justify-between">
            <div class="flex items-center gap-3">
                <i class="fas fa-droplet text-hemo-red"></i>
                <h3 class="text-lg font-semibold text-hemo-navy">Blood Availability</h3>
            </div>
            <span class="text-xs text-hemo-gray">Current stock levels</span>
        </div>
        <div class="p-6">
            <div class="grid grid-cols-2 sm:grid-cols-4 gap-4">
                <?php foreach ($inventory as $item):
                    $count = (int)$item['unit_count'];
                    $bgClass = $count === 0 ? 'bg-red-50 border-red-200' : ($count < 5 ? 'bg-amber-50 border-amber-200' : 'bg-hemo-light-gray border-transparent');
                    $textClass = $count === 0 ? 'text-hemo-warning' : ($count < 5 ? 'text-hemo-amber' : 'text-hemo-red');
                ?>
                <div class="blood-card flex flex-col items-center justify-center p-5 rounded-xl border-2 <?php echo $bgClass; ?> transition-default">
                    <span class="text-2xl font-bold text-hemo-navy"><?php echo sanitize($item['type_name']); ?></span>
                    <span class="text-xl font-bold <?php echo $textClass; ?> mt-2"><?php echo $count; ?></span>
                    <span class="text-xs text-hemo-gray mt-1">units</span>
                </div>
                <?php endforeach; ?>
            </div>
        </div>
    </div>
</div>

<!-- Recent Requests Table -->
<div class="bg-white rounded-xl shadow-card">
    <div class="p-6 border-b border-hemo-border flex items-center justify-between">
        <div class="flex items-center gap-3">
            <i class="fas fa-clipboard-list text-hemo-red"></i>
            <h3 class="text-lg font-semibold text-hemo-navy">Recent Blood Requests</h3>
        </div>
        <a href="<?php echo BASE_URL; ?>/index.php?page=hospital_requests" class="text-sm text-hemo-red font-semibold hover:text-hemo-deep-red transition-fast">View All →</a>
    </div>
    <div class="overflow-x-auto">
        <table class="w-full">
            <thead>
                <tr class="bg-hemo-light-gray">
                    <th class="text-left px-6 py-3.5 text-xs font-semibold text-hemo-gray uppercase tracking-wider">Request ID</th>
                    <th class="text-left px-6 py-3.5 text-xs font-semibold text-hemo-gray uppercase tracking-wider">Blood Type</th>
                    <th class="text-left px-6 py-3.5 text-xs font-semibold text-hemo-gray uppercase tracking-wider">Units</th>
                    <th class="text-left px-6 py-3.5 text-xs font-semibold text-hemo-gray uppercase tracking-wider">Date</th>
                    <th class="text-left px-6 py-3.5 text-xs font-semibold text-hemo-gray uppercase tracking-wider">Status</th>
                </tr>
            </thead>
            <tbody>
                <?php if (empty($requests)): ?>
                    <tr>
                        <td colspan="5" class="px-6 py-12 text-center text-hemo-gray">
                            <i class="fas fa-clipboard text-3xl mb-3 block text-hemo-border"></i>
                            <p>No blood requests yet</p>
                            <p class="text-xs mt-1">Click "New Blood Request" to create one</p>
                        </td>
                    </tr>
                <?php else: ?>
                    <?php foreach ($requests as $req): ?>
                    <tr class="table-row border-b border-hemo-border last:border-0 transition-fast">
                        <td class="px-6 py-4 text-sm font-mono text-hemo-red font-semibold">#REQ-<?php echo str_pad($req['request_id'], 4, '0', STR_PAD_LEFT); ?></td>
                        <td class="px-6 py-4">
                            <span class="inline-flex items-center gap-1.5 px-3 py-1 rounded-lg bg-hemo-red text-white text-xs font-bold">
                                <i class="fas fa-droplet text-[9px]"></i> <?php echo sanitize($req['blood_type'] ?? 'N/A'); ?>
                            </span>
                        </td>
                        <td class="px-6 py-4 text-sm font-semibold text-hemo-navy"><?php echo $req['units_requested'] ?? '—'; ?></td>
                        <td class="px-6 py-4 text-sm text-hemo-gray"><?php echo date('M d, Y', strtotime($req['created_at'])); ?></td>
                        <td class="px-6 py-4">
                            <?php 
                            $statusColors = [
                                'Pending' => 'bg-amber-100 text-amber-700',
                                'Fulfilled' => 'bg-green-100 text-hemo-success',
                                'Rejected' => 'bg-red-100 text-hemo-warning',
                            ];
                            $sc = $statusColors[$req['status']] ?? 'bg-gray-100 text-hemo-gray';
                            ?>
                            <span class="inline-flex items-center gap-1.5 px-3 py-1 rounded-full text-xs font-semibold <?php echo $sc; ?>">
                                <span class="w-1.5 h-1.5 rounded-full bg-current"></span>
                                <?php echo sanitize($req['status']); ?>
                            </span>
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
require_once __DIR__ . '/../layouts/hospital_layout.php';
?>
