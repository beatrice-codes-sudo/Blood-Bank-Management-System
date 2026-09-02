<?php
/**
 * Donor Donation History Page
 */
$pageTitle = 'Donation History';
$donor = $data['donor'] ?? null;
$stats = $data['stats'] ?? ['total_donations' => 0, 'last_donation_date' => null, 'total_volume_ml' => 0];
$donations = $data['donations'] ?? [];

ob_start();
?>

<!-- Page Header -->
<div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4 mb-8">
    <div>
        <h1 class="font-display text-[32px] font-bold text-hemo-navy">Donation History</h1>
        <p class="text-hemo-charcoal mt-1">Your complete blood donation records.</p>
    </div>
    <a href="<?php echo BASE_URL; ?>/index.php?page=donor_appointments" class="inline-flex items-center gap-2 px-5 py-2.5 rounded-lg bg-hemo-red text-white font-semibold text-sm shadow-btn-primary hover:bg-hemo-deep-red hover:shadow-btn-hover hover:-translate-y-px transition-default btn-press">
        <i class="fas fa-calendar-plus"></i> Schedule Donation
    </a>
</div>

<!-- Stats -->
<div class="grid grid-cols-1 sm:grid-cols-3 gap-6 mb-8">
    <div class="stat-card bg-white rounded-xl shadow-card p-5 transition-default">
        <div class="flex items-center justify-between mb-4">
            <div class="w-12 h-12 rounded-xl bg-hemo-light-red flex items-center justify-center">
                <i class="fas fa-droplet text-hemo-red text-lg"></i>
            </div>
        </div>
        <p class="text-3xl font-bold text-hemo-navy"><?php echo number_format($stats['total_donations']); ?></p>
        <p class="text-sm text-hemo-gray mt-1">Total Donations</p>
    </div>
    <div class="stat-card bg-white rounded-xl shadow-card p-5 transition-default">
        <div class="flex items-center justify-between mb-4">
            <div class="w-12 h-12 rounded-xl bg-green-50 flex items-center justify-center">
                <i class="fas fa-flask text-hemo-success text-lg"></i>
            </div>
        </div>
        <p class="text-3xl font-bold text-hemo-navy"><?php echo number_format($stats['total_volume_ml']); ?><span class="text-lg font-normal text-hemo-gray"> ml</span></p>
        <p class="text-sm text-hemo-gray mt-1">Total Volume Donated</p>
    </div>
    <div class="stat-card bg-white rounded-xl shadow-card p-5 transition-default">
        <div class="flex items-center justify-between mb-4">
            <div class="w-12 h-12 rounded-xl bg-amber-50 flex items-center justify-center">
                <i class="fas fa-calendar-check text-hemo-amber text-lg"></i>
            </div>
        </div>
        <p class="text-xl font-bold text-hemo-navy">
            <?php echo $stats['last_donation_date'] ? date('M d, Y', strtotime($stats['last_donation_date'])) : 'No donations yet'; ?>
        </p>
        <p class="text-sm text-hemo-gray mt-1">Last Donation</p>
    </div>
</div>

<!-- Filters -->
<div class="bg-white rounded-xl shadow-card mb-6">
    <div class="px-6 py-4 border-b border-hemo-border flex flex-col sm:flex-row sm:items-center gap-3">
        <div class="relative flex-1">
            <i class="fas fa-search absolute left-3 top-1/2 -translate-y-1/2 text-hemo-gray text-sm"></i>
            <input type="text" id="search-input" placeholder="Search by center or notes..." class="w-full h-10 pl-10 pr-4 rounded-lg border border-hemo-border text-sm" oninput="filterTable()">
        </div>
        <select id="filter-status" class="h-10 px-4 rounded-lg border border-hemo-border text-sm" onchange="filterTable()">
            <option value="">All Statuses</option>
            <option value="Completed">Completed</option>
            <option value="Pending">Pending</option>
            <option value="Cancelled">Cancelled</option>
        </select>
    </div>

    <!-- Table -->
    <?php if (empty($donations)): ?>
    <div class="p-10 text-center">
        <div class="w-16 h-16 rounded-2xl bg-hemo-light-red flex items-center justify-center mx-auto mb-4">
            <i class="fas fa-droplet-slash text-hemo-red text-2xl"></i>
        </div>
        <p class="text-hemo-charcoal font-medium">No donations recorded yet</p>
        <p class="text-sm text-hemo-gray mt-1">Your donation history will appear here once you complete a donation.</p>
    </div>
    <?php else: ?>
    <div class="overflow-x-auto">
        <table class="w-full text-sm">
            <thead>
                <tr class="bg-hemo-light-gray text-hemo-gray text-xs uppercase tracking-wider">
                    <th class="px-6 py-3 text-left font-semibold">#</th>
                    <th class="px-6 py-3 text-left font-semibold">Date</th>
                    <th class="px-6 py-3 text-left font-semibold">Blood Type</th>
                    <th class="px-6 py-3 text-left font-semibold">Volume</th>
                    <th class="px-6 py-3 text-left font-semibold">Center</th>
                    <th class="px-6 py-3 text-left font-semibold">Status</th>
                    <th class="px-6 py-3 text-right font-semibold">Certificate</th>
                </tr>
            </thead>
            <tbody id="donations-body" class="divide-y divide-hemo-border">
                <?php foreach ($donations as $i => $don): ?>
                <tr class="table-row transition-fast donation-row"
                    data-status="<?php echo $don['status']; ?>"
                    data-search="<?php echo strtolower(($don['donation_center'] ?? '') . ' ' . ($don['notes'] ?? '')); ?>">
                    <td class="px-6 py-4 font-mono text-hemo-red font-semibold text-xs">#<?php echo $don['donation_id']; ?></td>
                    <td class="px-6 py-4 font-medium text-hemo-navy"><?php echo date('M d, Y', strtotime($don['donation_date'])); ?></td>
                    <td class="px-6 py-4">
                        <span class="inline-flex items-center gap-1.5 px-3 py-1 rounded-lg bg-hemo-red text-white text-xs font-bold">
                            <i class="fas fa-droplet text-[10px]"></i> <?php echo sanitize($don['blood_type'] ?? 'N/A'); ?>
                        </span>
                    </td>
                    <td class="px-6 py-4 text-hemo-navy font-semibold"><?php echo number_format($don['volume_ml']); ?> ml</td>
                    <td class="px-6 py-4 text-hemo-charcoal"><?php echo sanitize($don['donation_center'] ?? '—'); ?></td>
                    <td class="px-6 py-4">
                        <?php
                        $dsc = [
                            'Completed' => 'bg-green-50 text-hemo-success',
                            'Pending'   => 'bg-blue-50 text-blue-700',
                            'Cancelled' => 'bg-red-50 text-hemo-warning',
                        ];
                        $dc = $dsc[$don['status']] ?? 'bg-gray-50 text-gray-600';
                        ?>
                        <span class="text-[11px] font-semibold px-2.5 py-1 rounded-full <?php echo $dc; ?>"><?php echo $don['status']; ?></span>
                    </td>
                    <td class="px-6 py-4 text-right">
                        <?php if ($don['status'] === 'Completed'): ?>
                            <a href="<?php echo BASE_URL; ?>/index.php?page=donor_certificate&donation_id=<?php echo $don['donation_id']; ?>" 
                               class="inline-flex items-center gap-1.5 px-3 py-1.5 rounded-lg bg-red-50 text-hemo-red hover:bg-hemo-red hover:text-white transition-fast text-xs font-semibold" 
                               title="Download Certificate">
                                <i class="fas fa-certificate text-xs"></i> Certificate
                            </a>
                        <?php else: ?>
                            <span class="text-xs text-hemo-gray italic">—</span>
                        <?php endif; ?>
                    </td>
                </tr>
                <?php endforeach; ?>
            </tbody>
        </table>
    </div>
    <?php endif; ?>
</div>

<script>
function filterTable() {
    const search = document.getElementById('search-input').value.toLowerCase();
    const status = document.getElementById('filter-status').value;
    document.querySelectorAll('.donation-row').forEach(row => {
        const matchSearch = !search || row.dataset.search.includes(search);
        const matchStatus = !status || row.dataset.status === status;
        row.style.display = (matchSearch && matchStatus) ? '' : 'none';
    });
}
</script>

<?php
$content = ob_get_clean();
require_once __DIR__ . '/../layouts/dashboard_layout.php';
?>
