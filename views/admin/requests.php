<?php
/**
 * Admin: Requests Management View
 */
$pageTitle = 'Manage Blood Requests';
$page = $page ?? 1;
$limit = $limit ?? 10;
$requests = $requests ?? [];
$totalPages = $totalPages ?? 1;
$totalRequests = $totalRequests ?? 0;
ob_start();
?>

<!-- Page Header -->
<div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4 mb-8">
    <div>
        <h1 class="font-display text-[32px] font-bold text-hemo-navy">Blood Requests</h1>
        <p class="text-hemo-charcoal mt-1">Manage all blood requests from hospitals and donors.</p>
    </div>
    <div class="flex items-center gap-3">
        <a href="<?php echo BASE_URL; ?>/index.php?page=admin_dashboard" class="inline-flex items-center gap-2 px-4 py-2 rounded-lg bg-hemo-light-red text-hemo-red font-semibold text-sm hover:bg-hemo-red hover:text-white transition-default">
            <i class="fas fa-arrow-left"></i> Dashboard
        </a>
    </div>
</div>

<!-- Requests Table -->
<div class="bg-white rounded-xl shadow-card overflow-hidden">
    <div class="p-6 border-b border-hemo-border flex flex-col sm:flex-row sm:items-center justify-between gap-4">
        <div class="flex items-center gap-3">
            <i class="fas fa-tint text-hemo-red"></i>
            <h3 class="text-lg font-semibold text-hemo-navy">All System Requests</h3>
        </div>
        
        <!-- Filters -->
        <form action="<?php echo BASE_URL; ?>/index.php" method="get" class="flex flex-wrap items-center gap-2">
            <input type="hidden" name="page" value="admin_requests">
            <div class="relative">
                <i class="fas fa-search absolute left-3 top-1/2 -translate-y-1/2 text-hemo-gray text-xs"></i>
                <input type="text" name="search" placeholder="Search requests..." value="<?php echo htmlspecialchars($_GET['search'] ?? ''); ?>" class="pl-9 pr-4 py-2 bg-hemo-light-gray border-none rounded-lg text-sm focus:ring-2 focus:ring-hemo-red/20 w-full sm:w-48 transition-fast">
            </div>
            <select name="status" class="py-2 px-3 bg-hemo-light-gray border-none rounded-lg text-sm focus:ring-2 focus:ring-hemo-red/20 transition-fast">
                <option value="">All Statuses</option>
                <option value="Pending" <?php echo ($_GET['status'] ?? '') == 'Pending' ? 'selected' : ''; ?>>Pending</option>
                <option value="Processing" <?php echo ($_GET['status'] ?? '') == 'Processing' ? 'selected' : ''; ?>>Processing</option>
                <option value="Fulfilled" <?php echo ($_GET['status'] ?? '') == 'Fulfilled' ? 'selected' : ''; ?>>Fulfilled</option>
                <option value="Rejected" <?php echo ($_GET['status'] ?? '') == 'Rejected' ? 'selected' : ''; ?>>Rejected</option>
            </select>
            <button type="submit" class="px-4 py-2 rounded-lg bg-hemo-navy text-white text-sm font-semibold hover:bg-slate-800 transition-fast">Filter</button>
        </form>
    </div>
    
    <div class="overflow-x-auto">
        <table class="w-full text-left border-collapse">
            <thead>
                <tr class="bg-hemo-light-gray">
                    <th class="px-6 py-4 text-xs font-semibold text-hemo-gray uppercase tracking-wider">Requester</th>
                    <th class="px-6 py-4 text-xs font-semibold text-hemo-gray uppercase tracking-wider">Blood Type</th>
                    <th class="px-6 py-4 text-xs font-semibold text-hemo-gray uppercase tracking-wider">Units</th>
                    <th class="px-6 py-4 text-xs font-semibold text-hemo-gray uppercase tracking-wider">Urgency</th>
                    <th class="px-6 py-4 text-xs font-semibold text-hemo-gray uppercase tracking-wider">Status</th>
                    <th class="px-6 py-4 text-xs font-semibold text-hemo-gray uppercase tracking-wider">Date</th>
                    <th class="px-6 py-4 text-xs font-semibold text-hemo-gray uppercase tracking-wider text-right">Actions</th>
                </tr>
            </thead>
            <tbody class="divide-y divide-hemo-border">
                <?php if (empty($requests)): ?>
                    <tr>
                        <td colspan="7" class="px-6 py-12 text-center text-hemo-gray">
                            <i class="fas fa-inbox text-4xl mb-3 block text-hemo-border"></i>
                            <p>No requests found in the system.</p>
                        </td>
                    </tr>
                <?php else: ?>
                    <?php foreach ($requests as $request): ?>
                    <tr class="hover:bg-hemo-off-white transition-fast">
                        <td class="px-6 py-4">
                            <div class="flex items-center gap-3">
                                <?php if (!empty($request['hospital_id'])): ?>
                                    <div class="w-10 h-10 rounded-full bg-blue-100 flex items-center justify-center text-blue-700 shadow-sm">
                                        <i class="fas fa-hospital text-sm"></i>
                                    </div>
                                    <div>
                                        <p class="text-sm font-bold text-hemo-navy leading-none"><?php echo sanitize($request['hospital_name'] ?? 'Hospital ID: ' . $request['hospital_id']); ?></p>
                                        <p class="text-xs text-hemo-gray mt-1">Hospital Request</p>
                                    </div>
                                <?php else: ?>
                                    <div class="w-10 h-10 rounded-full bg-purple-100 flex items-center justify-center text-purple-700 shadow-sm">
                                        <i class="fas fa-user text-sm"></i>
                                    </div>
                                    <div>
                                        <p class="text-sm font-bold text-hemo-navy leading-none"><?php echo sanitize(($request['first_name'] ?? '') . ' ' . ($request['last_name'] ?? 'Unknown User')); ?></p>
                                        <p class="text-xs text-hemo-gray mt-1">Individual Request</p>
                                    </div>
                                <?php endif; ?>
                            </div>
                        </td>
                        <td class="px-6 py-4">
                            <span class="inline-flex items-center justify-center w-10 h-10 rounded-full text-sm font-bold border <?php echo getBloodTypeColorClass($request['blood_type'] ?? ''); ?>">
                                <?php echo sanitize($request['blood_type'] ?? '?'); ?>
                            </span>
                        </td>
                        <td class="px-6 py-4">
                            <div class="flex flex-col">
                                <span class="text-sm font-bold text-hemo-navy"><?php echo (int)($request['units_requested'] ?? $request['units'] ?? 0); ?> Requested</span>
                                <?php if (isset($request['units_fulfilled']) && $request['units_fulfilled'] > 0): ?>
                                    <span class="text-xs text-hemo-success"><?php echo (int)$request['units_fulfilled']; ?> Fulfilled</span>
                                <?php endif; ?>
                            </div>
                        </td>
                        <td class="px-6 py-4">
                            <?php 
                            $urgency = $request['urgency'] ?? 'Normal';
                            if ($urgency === 'Emergency') {
                                echo '<span class="inline-flex items-center gap-1 text-xs font-bold text-hemo-red bg-red-100 px-2 py-1 rounded-md"><i class="fas fa-exclamation-circle"></i> Emergency</span>';
                            } else {
                                echo '<span class="text-xs font-semibold text-hemo-gray bg-hemo-light-gray px-2 py-1 rounded-md">Normal</span>';
                            }
                            ?>
                        </td>
                        <td class="px-6 py-4">
                            <?php
                            $status = $request['status'] ?? 'Pending';
                            $statusClass = 'bg-hemo-light-gray text-hemo-gray';
                            
                            switch($status) {
                                case 'Pending': $statusClass = 'bg-yellow-100 text-yellow-700'; break;
                                case 'Processing': $statusClass = 'bg-blue-100 text-blue-700'; break;
                                case 'Fulfilled': $statusClass = 'bg-green-100 text-hemo-success'; break;
                                case 'Rejected': $statusClass = 'bg-red-100 text-hemo-red'; break;
                            }
                            ?>
                            <span class="inline-flex items-center px-2.5 py-1 rounded-full text-[11px] font-bold uppercase tracking-tight <?php echo $statusClass; ?>">
                                <?php echo sanitize($status); ?>
                            </span>
                        </td>
                        <td class="px-6 py-4 text-xs text-hemo-gray">
                            <?php echo date('M d, Y H:i', strtotime($request['created_at'])); ?>
                        </td>
                        <td class="px-6 py-4 text-right">
                            <div class="flex items-center justify-end gap-2">
                                <?php if ($status === 'Pending' || $status === 'Processing'): ?>
                                    <form action="<?php echo BASE_URL; ?>/index.php?page=admin_dispatch_units" method="POST" class="inline">
                                        <input type="hidden" name="request_id" value="<?php echo $request['request_id']; ?>">
                                        <input type="hidden" name="hospital_id" value="<?php echo $request['hospital_id'] ?? 0; ?>">
                                        <input type="hidden" name="status" value="Fulfilled">
                                        <input type="hidden" name="units_to_dispatch" value="<?php echo (int)($request['units_requested'] ?? $request['units'] ?? 0); ?>">
                                        <button type="submit" class="p-2 rounded-lg hover:bg-green-50 text-hemo-success transition-fast" title="Fulfill Request" onclick="return confirm('Mark this request as fulfilled?');">
                                            <i class="fas fa-check text-sm"></i>
                                        </button>
                                    </form>
                                <?php endif; ?>
                                
                                <button class="p-2 rounded-lg hover:bg-hemo-light-red text-hemo-gray hover:text-hemo-red transition-fast" title="View Details">
                                    <i class="fas fa-eye text-sm"></i>
                                </button>
                                
                                <form action="<?php echo BASE_URL; ?>/index.php?page=admin_delete_request" method="POST" class="inline">
                                    <input type="hidden" name="request_id" value="<?php echo $request['request_id']; ?>">
                                    <button type="submit" class="p-2 rounded-lg hover:bg-red-50 text-hemo-warning transition-fast" title="Delete Request" onclick="return confirm('Are you sure you want to delete this request?');">
                                        <i class="fas fa-trash text-sm"></i>
                                    </button>
                                </form>
                            </div>
                        </td>
                    </tr>
                    <?php endforeach; ?>
                <?php endif; ?>
            </tbody>
        </table>
    </div>
    
    <div class="p-4 bg-hemo-off-white border-t border-hemo-border flex flex-col sm:flex-row sm:items-center justify-between gap-4">
        <p class="text-xs text-hemo-gray">
            Showing <?php echo empty($requests) ? 0 : (($page - 1) * $limit + 1); ?> to 
            <?php echo (($page - 1) * $limit + count($requests)); ?> of 
            <?php echo $totalRequests; ?> requests.
        </p>
        
        <?php if ($totalPages > 1): ?>
        <div class="flex items-center gap-1">
            <?php if ($page > 1): ?>
                <a href="<?php echo BASE_URL; ?>/index.php?page=admin_requests&p=<?php echo $page - 1; ?><?php echo !empty($_GET['status']) ? '&status='.urlencode($_GET['status']) : ''; ?><?php echo !empty($_GET['search']) ? '&search='.urlencode($_GET['search']) : ''; ?>" class="w-8 h-8 flex items-center justify-center rounded-lg border border-hemo-border bg-white text-hemo-charcoal hover:bg-hemo-light-gray transition-fast">
                    <i class="fas fa-chevron-left text-xs"></i>
                </a>
            <?php endif; ?>
            
            <?php for ($i = max(1, $page - 2); $i <= min($totalPages, $page + 2); $i++): ?>
                <a href="<?php echo BASE_URL; ?>/index.php?page=admin_requests&p=<?php echo $i; ?><?php echo !empty($_GET['status']) ? '&status='.urlencode($_GET['status']) : ''; ?><?php echo !empty($_GET['search']) ? '&search='.urlencode($_GET['search']) : ''; ?>" class="w-8 h-8 flex items-center justify-center rounded-lg border <?php echo $i === $page ? 'border-hemo-red bg-hemo-red text-white' : 'border-hemo-border bg-white text-hemo-charcoal hover:bg-hemo-light-gray'; ?> text-xs font-semibold transition-fast">
                    <?php echo $i; ?>
                </a>
            <?php endfor; ?>
            
            <?php if ($page < $totalPages): ?>
                <a href="<?php echo BASE_URL; ?>/index.php?page=admin_requests&p=<?php echo $page + 1; ?><?php echo !empty($_GET['status']) ? '&status='.urlencode($_GET['status']) : ''; ?><?php echo !empty($_GET['search']) ? '&search='.urlencode($_GET['search']) : ''; ?>" class="w-8 h-8 flex items-center justify-center rounded-lg border border-hemo-border bg-white text-hemo-charcoal hover:bg-hemo-light-gray transition-fast">
                    <i class="fas fa-chevron-right text-xs"></i>
                </a>
            <?php endif; ?>
        </div>
        <?php endif; ?>
    </div>
</div>

<?php
$content = ob_get_clean();
require_once __DIR__ . '/../layouts/dashboard_layout.php';
?>
