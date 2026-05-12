<?php
/**
 * Hospital Manager: Blood Requests Management View
 */
$pageTitle = 'Blood Requests';

ob_start();
?>

<!-- Page Header -->
<div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4 mb-8">
    <div>
        <h1 class="font-display text-[32px] font-bold text-hemo-navy">Blood Requests</h1>
    </div>
    <div class="flex items-center gap-3">
        <button onclick="openModal('addRequestModal')" class="inline-flex items-center gap-2 px-4 py-2 rounded-lg bg-hemo-red text-white font-semibold text-sm hover:bg-hemo-deep-red transition-default shadow-button">
            <i class="fas fa-plus"></i> New Blood Request
        </button>
    </div>
</div>

<!-- Stats Grid -->
<div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-6 mb-8">
    <!-- Total Requests -->
    <div class="stat-card bg-white rounded-xl shadow-card p-5 border-l-4 border-hemo-red transition-default cursor-default">
        <div class="flex items-center justify-between mb-4">
            <div class="w-12 h-12 rounded-xl bg-hemo-light-red flex items-center justify-center">
                <i class="fas fa-clipboard-list text-hemo-red text-lg"></i>
            </div>
        </div>
        <p class="text-3xl font-bold text-hemo-navy"><?php echo number_format($stats['total'] ?? 0); ?></p>
        <p class="text-sm text-hemo-gray mt-1">Total Requests</p>
    </div>

    <!-- Pending Requests -->
    <div class="stat-card bg-white rounded-xl shadow-card p-5 border-l-4 border-amber-500 transition-default cursor-default">
        <div class="flex items-center justify-between mb-4">
            <div class="w-12 h-12 rounded-xl bg-amber-50 flex items-center justify-center">
                <i class="fas fa-clock text-amber-500 text-lg"></i>
            </div>
        </div>
        <p class="text-3xl font-bold text-hemo-navy"><?php echo number_format($stats['pending'] ?? 0); ?></p>
        <p class="text-sm text-hemo-gray mt-1">Pending Requests</p>
    </div>

    <!-- Fulfilled Requests -->
    <div class="stat-card bg-white rounded-xl shadow-card p-5 border-l-4 border-hemo-success transition-default cursor-default">
        <div class="flex items-center justify-between mb-4">
            <div class="w-12 h-12 rounded-xl bg-green-50 flex items-center justify-center">
                <i class="fas fa-check-circle text-hemo-success text-lg"></i>
            </div>
        </div>
        <p class="text-3xl font-bold text-hemo-navy"><?php echo number_format($stats['fulfilled'] ?? 0); ?></p>
        <p class="text-sm text-hemo-gray mt-1">Fulfilled Requests</p>
    </div>

    <!-- Emergency Requests -->
    <div class="stat-card bg-white rounded-xl shadow-card p-5 border-l-4 border-hemo-warning transition-default cursor-default">
        <div class="flex items-center justify-between mb-4">
            <div class="w-12 h-12 rounded-xl bg-red-50 flex items-center justify-center">
                <i class="fas fa-ambulance text-hemo-warning text-lg"></i>
            </div>
        </div>
        <p class="text-3xl font-bold text-hemo-navy"><?php echo number_format($stats['emergency'] ?? 0); ?></p>
        <p class="text-sm text-hemo-gray mt-1">Emergency Urgency</p>
    </div>
</div>

<!-- Filters & Table -->
<div class="bg-white rounded-xl shadow-card overflow-hidden">
    <div class="p-6 border-b border-hemo-border flex flex-col lg:flex-row lg:items-center justify-between gap-4">
        <div class="flex items-center gap-3">
            <i class="fas fa-list text-hemo-red"></i>
            <h3 class="text-lg font-semibold text-hemo-navy">Request Directory</h3>
        </div>
        
        <!-- Filters -->
        <div class="flex flex-wrap items-center gap-3">
            <div class="relative">
                <i class="fas fa-search absolute left-3 top-1/2 -translate-y-1/2 text-hemo-gray text-xs"></i>
                <input type="text" id="searchRequest" placeholder="Search requests..." class="pl-9 pr-4 py-2 bg-hemo-light-gray border-none rounded-lg text-sm focus:ring-2 focus:ring-hemo-red/20 w-full sm:w-48 transition-fast">
            </div>
            
            <select id="filterBloodType" class="px-4 py-2 bg-hemo-light-gray border-none rounded-lg text-sm focus:ring-2 focus:ring-hemo-red/20 text-hemo-charcoal">
                <option value="">All Blood Types</option>
                <?php foreach ($bloodTypes as $bt): ?>
                    <option value="<?php echo sanitize($bt['type_name']); ?>"><?php echo sanitize($bt['type_name']); ?></option>
                <?php endforeach; ?>
            </select>
            
            <select id="filterStatus" class="px-4 py-2 bg-hemo-light-gray border-none rounded-lg text-sm focus:ring-2 focus:ring-hemo-red/20 text-hemo-charcoal">
                <option value="">All Statuses</option>
                <option value="Pending">Pending</option>
                <option value="Processing">Processing</option>
                <option value="Fulfilled">Fulfilled</option>
                <option value="Partially Fulfilled">Partially Fulfilled</option>
                <option value="Rejected">Rejected</option>
                <option value="Cancelled">Cancelled</option>
            </select>

            <select id="filterUrgency" class="px-4 py-2 bg-hemo-light-gray border-none rounded-lg text-sm focus:ring-2 focus:ring-hemo-red/20 text-hemo-charcoal">
                <option value="">All Urgencies</option>
                <option value="Normal">Normal</option>
                <option value="Urgent">Urgent</option>
                <option value="Emergency">Emergency</option>
            </select>
        </div>
    </div>
    
    <div class="overflow-x-auto">
        <table class="w-full text-left border-collapse" id="requestsTable">
            <thead>
                <tr class="bg-hemo-light-gray">
                    <th class="px-6 py-4 text-xs font-semibold text-hemo-gray uppercase tracking-wider">Request ID</th>
                    <th class="px-6 py-4 text-xs font-semibold text-hemo-gray uppercase tracking-wider">Blood Type</th>
                    <th class="px-6 py-4 text-xs font-semibold text-hemo-gray uppercase tracking-wider">Units (Fulfilled/Req)</th>
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
                            <i class="fas fa-clipboard text-4xl mb-3 block text-hemo-border"></i>
                            <p>No blood requests found.</p>
                        </td>
                    </tr>
                <?php else: ?>
                    <?php foreach ($requests as $req): ?>
                    <tr class="hover:bg-hemo-off-white transition-fast request-row">
                        <td class="px-6 py-4">
                            <div class="flex items-center gap-3">
                                <div>
                                    <p class="text-sm font-bold text-hemo-navy leading-none request-id">REQ-<?php echo str_pad($req['request_id'], 4, '0', STR_PAD_LEFT); ?></p>
                                    <div class="flex items-center gap-2 mt-1 hidden request-notes">
                                        <?php echo sanitize($req['notes'] ?? ''); ?>
                                    </div>
                                </div>
                            </div>
                        </td>
                        <td class="px-6 py-4">
                            <?php if ($req['blood_type']): ?>
                                <span class="inline-flex items-center gap-1.5 px-3 py-1 rounded-lg text-sm font-bold bg-hemo-red text-white request-blood">
                                    <i class="fas fa-droplet text-[10px]"></i>
                                    <?php echo sanitize($req['blood_type']); ?>
                                </span>
                            <?php else: ?>
                                <span class="text-xs text-hemo-gray italic request-blood">Any</span>
                            <?php endif; ?>
                        </td>
                        <td class="px-6 py-4">
                            <div class="space-y-1">
                                <p class="text-sm font-semibold text-hemo-navy">
                                    <?php echo (int)($req['units_fulfilled']); ?> / <?php echo (int)($req['units_requested']); ?>
                                </p>
                            </div>
                        </td>
                        <td class="px-6 py-4">
                            <?php 
                            $urgClass = 'bg-gray-100 text-gray-700';
                            if ($req['urgency'] === 'Normal') {
                                $urgClass = 'bg-blue-100 text-blue-700';
                            } elseif ($req['urgency'] === 'Urgent') {
                                $urgClass = 'bg-amber-100 text-hemo-amber';
                            } elseif ($req['urgency'] === 'Emergency') {
                                $urgClass = 'bg-red-100 text-hemo-warning';
                            }
                            ?>
                            <span class="inline-flex items-center px-2.5 py-1 rounded-lg text-xs font-semibold <?php echo $urgClass; ?> request-urgency">
                                <?php echo sanitize($req['urgency']); ?>
                            </span>
                        </td>
                        <td class="px-6 py-4">
                            <?php 
                            $statusClass = 'bg-gray-100 text-gray-700';
                            $statusDot = 'bg-gray-500';
                            if ($req['status'] === 'Pending' || $req['status'] === 'Processing') {
                                $statusClass = 'bg-amber-100 text-hemo-amber';
                                $statusDot = 'bg-hemo-amber';
                            } elseif ($req['status'] === 'Fulfilled') {
                                $statusClass = 'bg-green-100 text-hemo-success';
                                $statusDot = 'bg-hemo-success';
                            } elseif ($req['status'] === 'Partially Fulfilled') {
                                $statusClass = 'bg-blue-100 text-blue-500';
                                $statusDot = 'bg-blue-500';
                            } elseif ($req['status'] === 'Rejected' || $req['status'] === 'Cancelled') {
                                $statusClass = 'bg-red-100 text-hemo-warning';
                                $statusDot = 'bg-hemo-warning';
                            }
                            ?>
                            <span class="inline-flex items-center gap-1.5 px-2.5 py-1 rounded-full text-xs font-semibold <?php echo $statusClass; ?> request-status">
                                <span class="w-1.5 h-1.5 rounded-full <?php echo $statusDot; ?>"></span> 
                                <?php echo sanitize($req['status']); ?>
                            </span>
                        </td>
                        <td class="px-6 py-4">
                            <p class="text-sm text-hemo-charcoal"><?php echo date('M d, Y', strtotime($req['created_at'])); ?></p>
                        </td>
                        <td class="px-6 py-4 text-right space-x-1 whitespace-nowrap">
                            <button onclick="viewRequest(<?php echo $req['request_id']; ?>)" class="p-2 rounded-lg bg-hemo-light-gray text-hemo-charcoal hover:bg-blue-50 hover:text-blue-600 transition-fast" title="View Details">
                                <i class="fas fa-eye text-sm"></i>
                            </button>
                            <?php if (in_array($req['status'], ['Pending', 'Processing', 'Partially Fulfilled'])): ?>
                            <button onclick="openEditModal(<?php echo htmlspecialchars(json_encode([
                                'id' => $req['request_id'],
                                'blood_type_id' => $req['blood_type_id'],
                                'units_requested' => $req['units_requested'],
                                'urgency' => $req['urgency'],
                                'status' => $req['status'],
                                'notes' => $req['notes']
                            ])); ?>)" class="p-2 rounded-lg bg-hemo-light-gray text-hemo-charcoal hover:bg-amber-50 hover:text-hemo-amber transition-fast" title="Edit Request">
                                <i class="fas fa-edit text-sm"></i>
                            </button>
                            <?php endif; ?>
                            <button onclick="viewHistory(<?php echo $req['request_id']; ?>)" class="p-2 rounded-lg bg-hemo-light-gray text-hemo-charcoal hover:bg-green-50 hover:text-hemo-success transition-fast" title="Fulfillment History">
                                <i class="fas fa-clock-rotate-left text-sm"></i>
                            </button>
                            <?php if (in_array($req['status'], ['Pending', 'Cancelled'])): ?>
                            <button onclick="openDeleteModal(<?php echo $req['request_id']; ?>)" class="p-2 rounded-lg bg-hemo-light-gray text-hemo-charcoal hover:bg-red-50 hover:text-hemo-warning transition-fast" title="Delete Request">
                                <i class="fas fa-trash text-sm"></i>
                            </button>
                            <?php endif; ?>
                        </td>
                    </tr>
                    <?php endforeach; ?>
                <?php endif; ?>
            </tbody>
        </table>
    </div>
</div>

<!-- ================= MODALS ================= -->

<!-- Add Request Modal -->
<div id="addRequestModal" class="fixed inset-0 bg-hemo-navy/50 backdrop-blur-sm z-[1001] hidden flex items-center justify-center p-4 opacity-0 transition-opacity duration-300">
    <div class="bg-white rounded-xl shadow-2xl w-full max-w-lg max-h-[90vh] flex flex-col transform scale-95 transition-transform duration-300">
        <div class="px-6 py-4 border-b border-hemo-border flex items-center justify-between bg-hemo-off-white rounded-t-xl">
            <h3 class="text-lg font-semibold text-hemo-navy"><i class="fas fa-plus text-hemo-red mr-2"></i> New Blood Request</h3>
            <button type="button" onclick="closeModal('addRequestModal')" class="text-hemo-gray hover:text-hemo-red transition-fast"><i class="fas fa-times text-lg"></i></button>
        </div>
        <div class="p-6 overflow-y-auto flex-1">
            <form id="addRequestForm" action="<?php echo BASE_URL; ?>/index.php?page=hospital_request_add" method="POST">
                <div class="grid grid-cols-1 gap-4 mb-6">
                    <div>
                        <label class="block text-sm font-semibold text-hemo-charcoal mb-1">Blood Type *</label>
                        <select name="blood_type_id" required class="w-full px-4 py-2 rounded-lg border-2 border-hemo-border focus:border-hemo-red focus:ring-0 transition-fast outline-none text-sm bg-white">
                            <option value="">Select Blood Type</option>
                            <?php foreach ($bloodTypes as $bt): ?>
                                <option value="<?php echo $bt['blood_type_id']; ?>"><?php echo sanitize($bt['type_name']); ?></option>
                            <?php endforeach; ?>
                        </select>
                    </div>
                    <div>
                        <label class="block text-sm font-semibold text-hemo-charcoal mb-1">Units Requested *</label>
                        <input type="number" name="units_requested" min="1" value="1" required class="w-full px-4 py-2 rounded-lg border-2 border-hemo-border focus:border-hemo-red focus:ring-0 transition-fast outline-none text-sm">
                    </div>
                    <div>
                        <label class="block text-sm font-semibold text-hemo-charcoal mb-1">Urgency</label>
                        <select name="urgency" class="w-full px-4 py-2 rounded-lg border-2 border-hemo-border focus:border-hemo-red focus:ring-0 transition-fast outline-none text-sm bg-white">
                            <option value="Normal">Normal</option>
                            <option value="Urgent">Urgent</option>
                            <option value="Emergency">Emergency</option>
                        </select>
                    </div>
                    <div>
                        <label class="block text-sm font-semibold text-hemo-charcoal mb-1">Notes</label>
                        <textarea name="notes" rows="3" placeholder="Special requirements, patient details, etc." class="w-full px-4 py-2 rounded-lg border-2 border-hemo-border focus:border-hemo-red focus:ring-0 transition-fast outline-none text-sm"></textarea>
                    </div>
                </div>
            </form>
        </div>
        <div class="px-6 py-4 border-t border-hemo-border bg-hemo-off-white flex justify-end gap-3 rounded-b-xl">
            <button type="button" onclick="closeModal('addRequestModal')" class="px-4 py-2 rounded-lg bg-white border border-hemo-border text-hemo-charcoal font-semibold text-sm hover:bg-gray-50 transition-fast">Cancel</button>
            <button type="submit" form="addRequestForm" class="px-6 py-2 rounded-lg bg-hemo-red text-white font-semibold text-sm hover:bg-hemo-deep-red transition-fast shadow-button">Submit Request</button>
        </div>
    </div>
</div>

<!-- Edit Request Modal -->
<div id="editRequestModal" class="fixed inset-0 bg-hemo-navy/50 backdrop-blur-sm z-[1001] hidden flex items-center justify-center p-4 opacity-0 transition-opacity duration-300">
    <div class="bg-white rounded-xl shadow-2xl w-full max-w-lg max-h-[90vh] flex flex-col transform scale-95 transition-transform duration-300">
        <div class="px-6 py-4 border-b border-hemo-border flex items-center justify-between bg-hemo-off-white rounded-t-xl">
            <h3 class="text-lg font-semibold text-hemo-navy"><i class="fas fa-edit text-hemo-amber mr-2"></i> Edit Request</h3>
            <button type="button" onclick="closeModal('editRequestModal')" class="text-hemo-gray hover:text-hemo-red transition-fast"><i class="fas fa-times text-lg"></i></button>
        </div>
        <div class="p-6 overflow-y-auto flex-1">
            <form id="editRequestForm" action="<?php echo BASE_URL; ?>/index.php?page=hospital_request_edit" method="POST">
                <input type="hidden" name="request_id" id="edit_request_id">
                
                <div class="grid grid-cols-1 gap-4 mb-6">
                    <div>
                        <label class="block text-sm font-semibold text-hemo-charcoal mb-1">Blood Type *</label>
                        <select name="blood_type_id" id="edit_blood_type_id" required class="w-full px-4 py-2 rounded-lg border-2 border-hemo-border focus:border-hemo-amber focus:ring-0 transition-fast outline-none text-sm bg-white">
                            <?php foreach ($bloodTypes as $bt): ?>
                                <option value="<?php echo $bt['blood_type_id']; ?>"><?php echo sanitize($bt['type_name']); ?></option>
                            <?php endforeach; ?>
                        </select>
                    </div>
                    <div>
                        <label class="block text-sm font-semibold text-hemo-charcoal mb-1">Units Requested *</label>
                        <input type="number" name="units_requested" id="edit_units_requested" min="1" required class="w-full px-4 py-2 rounded-lg border-2 border-hemo-border focus:border-hemo-amber focus:ring-0 transition-fast outline-none text-sm">
                    </div>
                    <div>
                        <label class="block text-sm font-semibold text-hemo-charcoal mb-1">Urgency</label>
                        <select name="urgency" id="edit_urgency" class="w-full px-4 py-2 rounded-lg border-2 border-hemo-border focus:border-hemo-amber focus:ring-0 transition-fast outline-none text-sm bg-white">
                            <option value="Normal">Normal</option>
                            <option value="Urgent">Urgent</option>
                            <option value="Emergency">Emergency</option>
                        </select>
                    </div>
                    <div>
                        <label class="block text-sm font-semibold text-hemo-charcoal mb-1">Status</label>
                        <select name="status" id="edit_status" class="w-full px-4 py-2 rounded-lg border-2 border-hemo-border focus:border-hemo-amber focus:ring-0 transition-fast outline-none text-sm bg-white">
                            <option value="Pending">Pending</option>
                            <option value="Cancelled">Cancelled</option>
                        </select>
                        <p class="text-xs text-hemo-gray mt-1">Note: Hospitals can only cancel or keep requests pending.</p>
                    </div>
                    <div>
                        <label class="block text-sm font-semibold text-hemo-charcoal mb-1">Notes</label>
                        <textarea name="notes" id="edit_notes" rows="3" class="w-full px-4 py-2 rounded-lg border-2 border-hemo-border focus:border-hemo-amber focus:ring-0 transition-fast outline-none text-sm"></textarea>
                    </div>
                </div>
            </form>
        </div>
        <div class="px-6 py-4 border-t border-hemo-border bg-hemo-off-white flex justify-end gap-3 rounded-b-xl">
            <button type="button" onclick="closeModal('editRequestModal')" class="px-4 py-2 rounded-lg bg-white border border-hemo-border text-hemo-charcoal font-semibold text-sm hover:bg-gray-50 transition-fast">Cancel</button>
            <button type="submit" form="editRequestForm" class="px-6 py-2 rounded-lg bg-hemo-amber text-white font-semibold text-sm hover:bg-amber-600 transition-fast shadow-sm">Save Changes</button>
        </div>
    </div>
</div>

<!-- View Request Modal -->
<div id="viewRequestModal" class="fixed inset-0 bg-hemo-navy/50 backdrop-blur-sm z-[1001] hidden flex items-center justify-center p-4 opacity-0 transition-opacity duration-300">
    <div class="bg-white rounded-xl shadow-2xl w-full max-w-lg flex flex-col transform scale-95 transition-transform duration-300">
        <div class="px-6 py-4 border-b border-hemo-border flex items-center justify-between bg-hemo-off-white rounded-t-xl">
            <h3 class="text-lg font-semibold text-hemo-navy">Request Details</h3>
            <button type="button" onclick="closeModal('viewRequestModal')" class="text-hemo-gray hover:text-hemo-red transition-fast"><i class="fas fa-times text-lg"></i></button>
        </div>
        <div class="p-6">
            <div id="viewLoader" class="flex justify-center py-8">
                <i class="fas fa-spinner fa-spin text-3xl text-hemo-red"></i>
            </div>
            <div id="viewContent" class="hidden">
                <!-- Content populated via JS -->
            </div>
        </div>
    </div>
</div>

<!-- View History Modal -->
<div id="historyRequestModal" class="fixed inset-0 bg-hemo-navy/50 backdrop-blur-sm z-[1001] hidden flex items-center justify-center p-4 opacity-0 transition-opacity duration-300">
    <div class="bg-white rounded-xl shadow-2xl w-full max-w-3xl max-h-[80vh] flex flex-col transform scale-95 transition-transform duration-300">
        <div class="px-6 py-4 border-b border-hemo-border flex items-center justify-between bg-hemo-off-white rounded-t-xl">
            <h3 class="text-lg font-semibold text-hemo-navy"><i class="fas fa-clock-rotate-left text-hemo-success mr-2"></i> Fulfillment History</h3>
            <button type="button" onclick="closeModal('historyRequestModal')" class="text-hemo-gray hover:text-hemo-red transition-fast"><i class="fas fa-times text-lg"></i></button>
        </div>
        <div class="p-0 overflow-y-auto flex-1">
            <div id="historyLoader" class="flex justify-center py-12">
                <i class="fas fa-spinner fa-spin text-3xl text-hemo-red"></i>
            </div>
            <div id="historyContent" class="hidden">
                <table class="w-full text-left border-collapse">
                    <thead class="sticky top-0 bg-hemo-light-gray">
                        <tr>
                            <th class="px-6 py-3 text-xs font-semibold text-hemo-gray uppercase">Date</th>
                            <th class="px-6 py-3 text-xs font-semibold text-hemo-gray uppercase">Method</th>
                            <th class="px-6 py-3 text-xs font-semibold text-hemo-gray uppercase">Receiver</th>
                            <th class="px-6 py-3 text-xs font-semibold text-hemo-gray uppercase">Status</th>
                        </tr>
                    </thead>
                    <tbody id="historyTableBody" class="divide-y divide-hemo-border">
                        <!-- Populated via JS -->
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</div>

<!-- Delete Confirm Modal -->
<div id="deleteRequestModal" class="fixed inset-0 bg-hemo-navy/50 backdrop-blur-sm z-[1001] hidden flex items-center justify-center p-4 opacity-0 transition-opacity duration-300">
    <div class="bg-white rounded-xl shadow-2xl w-full max-w-sm flex flex-col transform scale-95 transition-transform duration-300">
        <div class="p-6 text-center">
            <div class="w-16 h-16 rounded-full bg-red-100 flex items-center justify-center mx-auto mb-4">
                <i class="fas fa-exclamation-triangle text-2xl text-hemo-warning"></i>
            </div>
            <h3 class="text-xl font-bold text-hemo-navy mb-2">Delete Request?</h3>
            <p class="text-sm text-hemo-charcoal mb-6">Are you sure you want to delete this blood request? This action cannot be undone.</p>
            
            <form action="<?php echo BASE_URL; ?>/index.php?page=hospital_request_delete" method="POST" class="flex gap-3">
                <input type="hidden" name="request_id" id="delete_request_id">
                <button type="button" onclick="closeModal('deleteRequestModal')" class="flex-1 py-2.5 rounded-lg bg-hemo-light-gray text-hemo-charcoal font-semibold hover:bg-gray-200 transition-fast">Cancel</button>
                <button type="submit" class="flex-1 py-2.5 rounded-lg bg-hemo-warning text-white font-semibold hover:bg-red-700 transition-fast">Yes, Delete</button>
            </form>
        </div>
    </div>
</div>

<!-- JavaScript for Modals & Filtering -->
<script>
    // Search and Filter Logic
    document.addEventListener('DOMContentLoaded', () => {
        const searchInput = document.getElementById('searchRequest');
        const bloodTypeFilter = document.getElementById('filterBloodType');
        const statusFilter = document.getElementById('filterStatus');
        const urgencyFilter = document.getElementById('filterUrgency');
        const rows = document.querySelectorAll('.request-row');

        function filterTable() {
            const searchTerm = searchInput.value.toLowerCase();
            const bloodType = bloodTypeFilter.value.toLowerCase();
            const status = statusFilter.value.toLowerCase();
            const urgency = urgencyFilter.value.toLowerCase();

            rows.forEach(row => {
                const reqId = row.querySelector('.request-id').textContent.toLowerCase();
                const notes = row.querySelector('.request-notes').textContent.toLowerCase();
                const bType = row.querySelector('.request-blood').textContent.toLowerCase();
                const stat = row.querySelector('.request-status').textContent.toLowerCase();
                const urg = row.querySelector('.request-urgency').textContent.toLowerCase();

                const matchesSearch = reqId.includes(searchTerm) || notes.includes(searchTerm);
                const matchesBlood = !bloodType || bType.includes(bloodType);
                const matchesStatus = !status || stat.includes(status);
                const matchesUrgency = !urgency || urg.includes(urgency);

                if (matchesSearch && matchesBlood && matchesStatus && matchesUrgency) {
                    row.style.display = '';
                } else {
                    row.style.display = 'none';
                }
            });
        }

        searchInput.addEventListener('input', filterTable);
        bloodTypeFilter.addEventListener('change', filterTable);
        statusFilter.addEventListener('change', filterTable);
        urgencyFilter.addEventListener('change', filterTable);
    });

    // Modal Helpers
    function openModal(id) {
        const modal = document.getElementById(id);
        modal.classList.remove('hidden');
        // trigger reflow
        void modal.offsetWidth;
        modal.classList.remove('opacity-0');
        modal.querySelector('div').classList.remove('scale-95');
    }

    function closeModal(id) {
        const modal = document.getElementById(id);
        modal.classList.add('opacity-0');
        modal.querySelector('div').classList.add('scale-95');
        setTimeout(() => {
            modal.classList.add('hidden');
        }, 300);
    }

    // Edit Modal Populator
    function openEditModal(request) {
        document.getElementById('edit_request_id').value = request.id;
        document.getElementById('edit_blood_type_id').value = request.blood_type_id || '';
        document.getElementById('edit_units_requested').value = request.units_requested || 1;
        document.getElementById('edit_urgency').value = request.urgency || 'Normal';
        document.getElementById('edit_status').value = request.status || 'Pending';
        document.getElementById('edit_notes').value = request.notes || '';
        openModal('editRequestModal');
    }

    function openDeleteModal(id) {
        document.getElementById('delete_request_id').value = id;
        openModal('deleteRequestModal');
    }

    // AJAX for View Request Details
    async function viewRequest(id) {
        openModal('viewRequestModal');
        document.getElementById('viewLoader').classList.remove('hidden');
        document.getElementById('viewContent').classList.add('hidden');

        try {
            const res = await fetch(`<?php echo BASE_URL; ?>/index.php?page=hospital_request_view&request_id=${id}`);
            const data = await res.json();
            
            if (data.request) {
                const r = data.request;
                const bType = r.blood_type ? r.blood_type : 'Unknown';
                
                document.getElementById('viewContent').innerHTML = `
                    <div class="flex items-center gap-6 mb-6">
                        <div class="w-16 h-16 rounded-xl bg-hemo-light-red flex items-center justify-center flex-shrink-0">
                            <i class="fas fa-clipboard-list text-hemo-red text-2xl"></i>
                        </div>
                        <div>
                            <h4 class="text-xl font-bold text-hemo-navy">REQ-${String(r.request_id).padStart(4, '0')}</h4>
                            <span class="inline-flex items-center gap-1.5 px-3 py-1 rounded-lg text-sm font-bold bg-hemo-red text-white mt-2">
                                <i class="fas fa-droplet"></i> ${bType}
                            </span>
                        </div>
                    </div>
                    
                    <div class="grid grid-cols-2 gap-y-4 gap-x-6 mb-6">
                        <div><span class="text-xs text-hemo-gray block mb-1">Status</span><span class="text-sm font-semibold">${r.status}</span></div>
                        <div><span class="text-xs text-hemo-gray block mb-1">Urgency</span><span class="text-sm font-semibold">${r.urgency}</span></div>
                        <div><span class="text-xs text-hemo-gray block mb-1">Units Requested</span><span class="text-sm font-semibold">${r.units_requested}</span></div>
                        <div><span class="text-xs text-hemo-gray block mb-1">Units Fulfilled</span><span class="text-sm font-semibold">${r.units_fulfilled || 0}</span></div>
                        <div class="col-span-2"><span class="text-xs text-hemo-gray block mb-1">Requested By</span><span class="text-sm font-semibold">${r.requester_first || ''} ${r.requester_last || ''}</span></div>
                        <div class="col-span-2"><span class="text-xs text-hemo-gray block mb-1">Notes</span><span class="text-sm font-semibold">${r.notes || 'None'}</span></div>
                    </div>
                `;
            }
        } catch (e) {
            document.getElementById('viewContent').innerHTML = `<div class="text-center text-hemo-warning py-4">Failed to load request data.</div>`;
        }
        
        document.getElementById('viewLoader').classList.add('hidden');
        document.getElementById('viewContent').classList.remove('hidden');
    }

    // AJAX for History
    async function viewHistory(id) {
        openModal('historyRequestModal');
        document.getElementById('historyLoader').classList.remove('hidden');
        document.getElementById('historyContent').classList.add('hidden');

        try {
            const res = await fetch(`<?php echo BASE_URL; ?>/index.php?page=hospital_request_history&request_id=${id}`);
            const data = await res.json();
            
            const tbody = document.getElementById('historyTableBody');
            tbody.innerHTML = '';
            
            if (data.distributions && data.distributions.length > 0) {
                data.distributions.forEach(d => {
                    let statusClass = d.is_delivered ? 'bg-green-100 text-hemo-success' : 'bg-amber-100 text-hemo-amber';
                    let statusText = d.is_delivered ? 'Delivered' : 'Dispatched / En Route';
                    
                    tbody.innerHTML += `
                        <tr class="hover:bg-hemo-off-white">
                            <td class="px-6 py-4 text-sm font-semibold text-hemo-navy">${d.dispatched_date}</td>
                            <td class="px-6 py-4 text-sm text-hemo-charcoal">${d.transport_method}</td>
                            <td class="px-6 py-4 text-sm text-hemo-charcoal">${d.receiver_name || 'Pending'}</td>
                            <td class="px-6 py-4">
                                <span class="inline-flex items-center px-2.5 py-1 rounded-full text-xs font-semibold ${statusClass}">
                                    ${statusText}
                                </span>
                            </td>
                        </tr>
                    `;
                });
            } else {
                tbody.innerHTML = `<tr><td colspan="4" class="px-6 py-12 text-center text-hemo-gray italic">No distribution history found for this request.</td></tr>`;
            }
        } catch (e) {
            document.getElementById('historyTableBody').innerHTML = `<tr><td colspan="4" class="px-6 py-8 text-center text-hemo-warning">Failed to load history.</td></tr>`;
        }
        
        document.getElementById('historyLoader').classList.add('hidden');
        document.getElementById('historyContent').classList.remove('hidden');
    }
</script>

<?php
$content = ob_get_clean();
require_once __DIR__ . '/../layouts/dashboard_layout.php';
?>
