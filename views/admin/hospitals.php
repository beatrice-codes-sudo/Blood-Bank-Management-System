<?php
/**
 * Admin: Hospitals Management View
 */
$pageTitle = 'Manage Hospitals';

ob_start();

$flash = getFlashMessage();
?>

<?php if ($flash): ?>
<div class="mb-6 px-4 py-3 rounded-lg text-sm font-semibold <?php echo $flash['type'] === 'success' ? 'bg-green-100 text-hemo-success border border-green-200' : 'bg-red-100 text-hemo-warning border border-red-200'; ?> flex items-center gap-2">
    <i class="fas <?php echo $flash['type'] === 'success' ? 'fa-check-circle' : 'fa-exclamation-circle'; ?>"></i>
    <?php echo sanitize($flash['message']); ?>
</div>
<?php endif; ?>

<!-- Page Header -->
<div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4 mb-8">
    <div>
        <h1 class="font-display text-[32px] font-bold text-hemo-navy">Hospitals</h1>
        <p class="text-sm text-hemo-gray mt-1">Manage registered hospitals and their blood requests</p>
    </div>
</div>

<!-- Stats Grid -->
<!-- <div class="grid grid-cols-1 sm:grid-cols-3 gap-6 mb-8"> -->
    <!-- Total Hospitals -->
    <!-- <div class="stat-card bg-white rounded-xl shadow-card p-5 border-l-4 border-hemo-red transition-default cursor-default">
        <div class="flex items-center justify-between mb-4">
            <div class="w-12 h-12 rounded-xl bg-hemo-light-red flex items-center justify-center">
                <i class="fas fa-hospital text-hemo-red text-lg"></i>
            </div>
        </div>
        <p class="text-3xl font-bold text-hemo-navy"><?php echo number_format($stats['total'] ?? 0); ?></p>
        <p class="text-sm text-hemo-gray mt-1">Total Hospitals</p>
    </div> -->

    <!-- Active Hospitals -->
    <!-- <div class="stat-card bg-white rounded-xl shadow-card p-5 border-l-4 border-hemo-success transition-default cursor-default">
        <div class="flex items-center justify-between mb-4">
            <div class="w-12 h-12 rounded-xl bg-green-50 flex items-center justify-center">
                <i class="fas fa-hospital-user text-hemo-success text-lg"></i>
            </div>
        </div>
        <p class="text-3xl font-bold text-hemo-navy"><?php echo number_format($stats['active'] ?? 0); ?></p>
        <p class="text-sm text-hemo-gray mt-1">Active Hospitals</p>
    </div> -->

    <!-- Inactive Hospitals -->
    <!-- <div class="stat-card bg-white rounded-xl shadow-card p-5 border-l-4 border-hemo-amber transition-default cursor-default">
        <div class="flex items-center justify-between mb-4">
            <div class="w-12 h-12 rounded-xl bg-amber-50 flex items-center justify-center">
                <i class="fas fa-hospital-alt text-hemo-amber text-lg"></i>
            </div>
        </div>
        <p class="text-3xl font-bold text-hemo-navy"><?php echo number_format($stats['inactive'] ?? 0); ?></p>
        <p class="text-sm text-hemo-gray mt-1">Inactive Hospitals</p>
    </div>
</div> -->

<!-- Hospital Directory -->
<div class="bg-white rounded-xl shadow-card overflow-hidden">
    <div class="p-6 border-b border-hemo-border flex flex-col lg:flex-row lg:items-center justify-between gap-4">
        <div class="flex items-center gap-3">
            <i class="fas fa-list text-hemo-red"></i>
            <h3 class="text-lg font-semibold text-hemo-navy">Hospital Directory</h3>
        </div>

        <!-- Filters -->
        <div class="flex flex-wrap items-center gap-3">
            <div class="relative">
                <i class="fas fa-search absolute left-3 top-1/2 -translate-y-1/2 text-hemo-gray text-xs"></i>
                <input type="text" id="searchHospital" placeholder="Search hospitals..." class="pl-9 pr-4 py-2 bg-hemo-light-gray border-none rounded-lg text-sm focus:ring-2 focus:ring-hemo-red/20 w-full sm:w-48 transition-fast">
            </div>

            <select id="filterStatus" class="px-4 py-2 bg-hemo-light-gray border-none rounded-lg text-sm focus:ring-2 focus:ring-hemo-red/20 text-hemo-charcoal">
                <option value="">All Statuses</option>
                <option value="active">Active</option>
                <option value="inactive">Inactive</option>
            </select>
        </div>
    </div>

    <div class="overflow-x-auto">
        <table class="w-full text-left border-collapse" id="hospitalsTable">
            <thead>
                <tr class="bg-hemo-light-gray">
                    <th class="px-6 py-4 text-xs font-semibold text-hemo-gray uppercase tracking-wider">Hospital</th>
                    <th class="px-6 py-4 text-xs font-semibold text-hemo-gray uppercase tracking-wider">Code</th>
                    <th class="px-6 py-4 text-xs font-semibold text-hemo-gray uppercase tracking-wider">Location</th>
                    <th class="px-6 py-4 text-xs font-semibold text-hemo-gray uppercase tracking-wider">Manager</th>
                    <th class="px-6 py-4 text-xs font-semibold text-hemo-gray uppercase tracking-wider">Contact</th>
                    <th class="px-6 py-4 text-xs font-semibold text-hemo-gray uppercase tracking-wider">Status</th>
                    <th class="px-6 py-4 text-xs font-semibold text-hemo-gray uppercase tracking-wider text-right">Actions</th>
                </tr>
            </thead>
            <tbody class="divide-y divide-hemo-border">
                <?php if (empty($hospitals)): ?>
                    <tr>
                        <td colspan="7" class="px-6 py-12 text-center text-hemo-gray">
                            <i class="fas fa-hospital text-4xl mb-3 block text-hemo-border"></i>
                            <p>No hospitals found in the system.</p>
                        </td>
                    </tr>
                <?php else: ?>
                    <?php foreach ($hospitals as $h): ?>
                    <tr class="hover:bg-hemo-off-white transition-fast hospital-row">
                        <td class="px-6 py-4">
                            <div class="flex items-center gap-3">
                                <div class="w-10 h-10 rounded-lg bg-blue-50 flex items-center justify-center text-hemo-info font-bold text-sm flex-shrink-0">
                                    <i class="fas fa-hospital"></i>
                                </div>
                                <div>
                                    <a href="<?php echo BASE_URL; ?>/index.php?page=admin_hospital_profile&hospital_id=<?php echo $h['hospital_id']; ?>" 
                                       class="text-sm font-bold text-hemo-navy hover:text-hemo-red transition-fast leading-none hospital-name no-underline">
                                        <?php echo sanitize($h['hospital_name']); ?>
                                    </a>
                                    <div class="flex items-center gap-2 mt-1">
                                        <span class="text-[10px] font-mono text-hemo-info bg-blue-50 px-1.5 py-0.5 rounded">ID: H-<?php echo str_pad($h['hospital_id'], 4, '0', STR_PAD_LEFT); ?></span>
                                    </div>
                                </div>
                            </div>
                        </td>
                        <td class="px-6 py-4">
                            <span class="text-sm font-mono text-hemo-red"><?php echo sanitize($h['hospital_code']); ?></span>
                        </td>
                        <td class="px-6 py-4">
                            <div class="space-y-0.5">
                                <p class="text-sm text-hemo-charcoal hospital-city"><?php echo sanitize($h['city']); ?></p>
                                <?php if ($h['region']): ?>
                                    <p class="text-xs text-hemo-gray"><?php echo sanitize($h['region']); ?></p>
                                <?php endif; ?>
                            </div>
                        </td>
                        <td class="px-6 py-4">
                            <p class="text-sm font-semibold text-hemo-navy"><?php echo sanitize($h['first_name'] . ' ' . $h['last_name']); ?></p>
                            <p class="text-xs text-hemo-gray"><?php echo sanitize($h['manager_email']); ?></p>
                        </td>
                        <td class="px-6 py-4">
                            <p class="text-sm text-hemo-charcoal"><i class="fas fa-phone text-hemo-gray text-xs mr-1"></i> <?php echo sanitize($h['phone']); ?></p>
                            <?php if ($h['email']): ?>
                                <p class="text-xs text-hemo-gray mt-0.5"><i class="fas fa-envelope text-hemo-gray text-xs mr-1"></i> <?php echo sanitize($h['email']); ?></p>
                            <?php endif; ?>
                        </td>
                        <td class="px-6 py-4">
                            <?php if ($h['is_active']): ?>
                                <span class="inline-flex items-center gap-1.5 px-2.5 py-1 rounded-full text-xs font-semibold bg-green-100 text-hemo-success hospital-status">
                                    <span class="w-1.5 h-1.5 rounded-full bg-hemo-success"></span> Active
                                </span>
                            <?php else: ?>
                                <span class="inline-flex items-center gap-1.5 px-2.5 py-1 rounded-full text-xs font-semibold bg-red-100 text-hemo-warning hospital-status">
                                    <span class="w-1.5 h-1.5 rounded-full bg-hemo-warning"></span> Inactive
                                </span>
                            <?php endif; ?>
                            <p class="text-[10px] text-hemo-gray mt-1">Joined: <?php echo date('M Y', strtotime($h['created_at'])); ?></p>
                        </td>
                        <td class="px-6 py-4 text-right whitespace-nowrap">
                            <a href="<?php echo BASE_URL; ?>/index.php?page=admin_hospital_profile&hospital_id=<?php echo $h['hospital_id']; ?>"
                               class="inline-flex p-2 rounded-lg bg-hemo-light-gray text-hemo-charcoal hover:bg-blue-50 hover:text-blue-600 transition-fast no-underline" title="View Profile">
                                <i class="fas fa-eye text-sm"></i>
                            </a>
                        </td>
                    </tr>
                    <?php endforeach; ?>
                <?php endif; ?>
            </tbody>
        </table>
    </div>
</div>

<!-- JavaScript for Search & Filter -->
<script>
    document.addEventListener('DOMContentLoaded', () => {
        const searchInput = document.getElementById('searchHospital');
        const statusFilter = document.getElementById('filterStatus');
        const rows = document.querySelectorAll('.hospital-row');

        function filterTable() {
            const searchTerm = searchInput.value.toLowerCase();
            const status = statusFilter.value.toLowerCase();

            rows.forEach(row => {
                const name = row.querySelector('.hospital-name').textContent.toLowerCase();
                const city = row.querySelector('.hospital-city')?.textContent.toLowerCase() || '';
                const statusText = row.querySelector('.hospital-status').textContent.toLowerCase().trim();

                const matchesSearch = name.includes(searchTerm) || city.includes(searchTerm);
                const matchesStatus = !status || statusText.includes(status);

                row.style.display = (matchesSearch && matchesStatus) ? '' : 'none';
            });
        }

        searchInput.addEventListener('input', filterTable);
        statusFilter.addEventListener('change', filterTable);
    });
</script>

<?php
$content = ob_get_clean();
require_once __DIR__ . '/../layouts/dashboard_layout.php';
?>
