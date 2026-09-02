<?php
/**
 * Hospital Manager: Blood Requests Management View
 */
$pageTitle = 'Blood Requests';
$bloodTypes = $bloodTypes ?? BLOOD_TYPES;

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
                    <option value="<?php echo sanitize($bt); ?>"><?php echo sanitize($bt); ?></option>
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
                            <p class="text-sm font-bold text-hemo-navy leading-none request-id">REQ-<?php echo str_pad($req['request_id'], 4, '0', STR_PAD_LEFT); ?></p>
                            <div class="flex items-center gap-2 mt-1 hidden request-notes">
                                <?php echo sanitize($req['notes'] ?? ''); ?>
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
                            if ($req['status'] === 'Rejected' || $req['status'] === 'Cancelled') {
                                $statusClass = 'bg-red-100 text-hemo-warning border border-red-200';
                                $displayStatus = $req['status'];
                            } elseif ($req['status'] === 'Fulfilled' || (($req['collection_status'] ?? '') === 'Received' && (int)($req['temp_verified'] ?? 1) === 1)) {
                                $statusClass = 'bg-green-100 text-hemo-success border border-green-200';
                                $displayStatus = 'Fulfilled (Received)';
                            } elseif ($req['status'] === 'Dispatched' || ($req['collection_status'] ?? '') === 'Dispatched') {
                                $statusClass = 'bg-indigo-100 text-indigo-700 border border-indigo-200';
                                $displayStatus = 'In Transit';
                            } elseif (($req['collection_status'] ?? '') === 'Ready for Pickup') {
                                $statusClass = 'bg-amber-100 text-amber-800 border border-amber-300';
                                $displayStatus = 'Ready for Pickup';
                            } elseif ($req['status'] === 'Partially Fulfilled') {
                                $statusClass = 'bg-blue-100 text-blue-700 border border-blue-200';
                                $displayStatus = 'Partially Fulfilled';
                            } else {
                                $statusClass = 'bg-amber-100 text-hemo-amber';
                                $displayStatus = $req['status'];
                            }
                            ?>
                            <span class="inline-flex items-center px-2.5 py-1 rounded-full text-xs font-semibold <?php echo $statusClass; ?> request-status">
                                <?php echo sanitize($displayStatus); ?>
                            </span>
                        </td>
                        <td class="px-6 py-4">
                            <p class="text-sm text-hemo-charcoal"><?php echo date('M d, Y', strtotime($req['created_at'])); ?></p>
                        </td>
                        <td class="px-6 py-4 text-right space-x-1 whitespace-nowrap">
                            <button onclick="viewRequest(<?php echo $req['request_id']; ?>)" class="p-2 rounded-lg bg-hemo-light-gray text-hemo-charcoal hover:bg-blue-50 hover:text-blue-600 transition-fast" title="View Details">
                                <i class="fas fa-eye text-sm"></i>
                            </button>

                            <?php if ($req['status'] === 'Dispatched' || ($req['collection_status'] ?? '') === 'Dispatched'): ?>
                                <button onclick="openConfirmReceiptModal(<?php echo $req['request_id']; ?>)" class="p-2 rounded-lg bg-green-100 text-hemo-success hover:bg-green-200 transition-fast font-bold text-xs" title="Confirm Package Arrival">
                                    <i class="fas fa-check-double text-sm mr-1"></i> Confirm Received
                                </button>
                            <?php endif; ?>

                            <?php 
                            $canEdit = ($req['status'] === 'Partially Fulfilled') 
                                || (in_array($req['status'], ['Pending', 'Processing']) 
                                    && ($req['collection_status'] ?? '') !== 'Ready for Pickup' 
                                    && ($req['collection_status'] ?? '') !== 'Dispatched' 
                                    && ($req['collection_status'] ?? '') !== 'Received');
                            if ($canEdit): 
                            ?>
                            <button onclick="openEditModal(<?php echo htmlspecialchars(json_encode([
                                'id' => $req['request_id'],
                                'blood_type' => $req['blood_type'],
                                'units_requested' => $req['units_requested'],
                                'urgency' => $req['urgency'],
                                'status' => $req['status'],
                                'notes' => $req['notes']
                            ])); ?>)" class="p-2 rounded-lg bg-hemo-light-gray text-hemo-charcoal hover:bg-amber-50 hover:text-hemo-amber transition-fast" title="Edit Request">
                                <i class="fas fa-edit text-sm"></i>
                            </button>
                            <?php endif; ?>
                            <?php 
                            $canDelete = in_array($req['status'], ['Pending', 'Cancelled', 'Rejected']) 
                                && ($req['collection_status'] ?? '') !== 'Ready for Pickup'
                                && ($req['collection_status'] ?? '') !== 'Dispatched'
                                && ($req['collection_status'] ?? '') !== 'Received'
                                && (int)($req['units_fulfilled'] ?? 0) === 0;
                            if ($canDelete): 
                            ?>
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
                        <select name="blood_type" required class="w-full px-4 py-2 rounded-lg border-2 border-hemo-border focus:border-hemo-red focus:ring-0 transition-fast outline-none text-sm bg-white">
                            <option value="">Select Blood Type</option>
                            <?php foreach ($bloodTypes as $bt): ?>
                                <option value="<?php echo sanitize($bt); ?>"><?php echo sanitize($bt); ?></option>
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
                        <select name="blood_type" id="edit_blood_type" required class="w-full px-4 py-2 rounded-lg border-2 border-hemo-border focus:border-hemo-amber focus:ring-0 transition-fast outline-none text-sm bg-white">
                            <?php foreach ($bloodTypes as $bt): ?>
                                <option value="<?php echo sanitize($bt); ?>"><?php echo sanitize($bt); ?></option>
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
    <div class="bg-white rounded-2xl shadow-2xl w-full max-w-2xl max-h-[90vh] flex flex-col transform scale-95 transition-transform duration-300 overflow-hidden">
        <div class="px-6 py-4 border-b border-hemo-border flex items-center justify-between bg-hemo-off-white">
            <div class="flex items-center gap-2">
                <div class="w-8 h-8 rounded-lg bg-hemo-light-red flex items-center justify-center text-hemo-red">
                    <i class="fas fa-file-medical"></i>
                </div>
                <div>
                    <h3 class="text-base font-bold text-hemo-navy">Blood Requisition Details</h3>
                    <p class="text-xs text-hemo-gray">Click & Collect Handover & Custody Tracking</p>
                </div>
            </div>
            <button type="button" onclick="closeModal('viewRequestModal')" class="text-hemo-gray hover:text-hemo-red transition-fast"><i class="fas fa-times text-lg"></i></button>
        </div>
        <div class="p-6 overflow-y-auto flex-1">
            <div id="viewLoader" class="flex justify-center py-12">
                <i class="fas fa-spinner fa-spin text-3xl text-hemo-red"></i>
            </div>
            <div id="viewContent" class="hidden space-y-6">
                <!-- Dynamic Content with 4-Step Stepper & PIN Card -->
            </div>
        </div>
    </div>
</div>

<!-- Confirm Receipt Modal -->
<div id="confirmReceiptModal" class="fixed inset-0 bg-hemo-navy/50 backdrop-blur-sm z-[1001] hidden flex items-center justify-center p-4 opacity-0 transition-opacity duration-300">
    <div class="bg-white rounded-2xl shadow-2xl w-full max-w-lg flex flex-col transform scale-95 transition-transform duration-300 overflow-hidden">
        <div id="receiptModalHeader" class="px-6 py-4 border-b border-hemo-border bg-emerald-50 flex items-center justify-between transition-colors duration-300">
            <h3 id="receiptModalTitle" class="text-base font-bold text-emerald-950 flex items-center gap-2">
                <i class="fas fa-truck-ramp-box text-emerald-600"></i> Confirm Blood Delivery Arrival
            </h3>
            <button type="button" onclick="closeModal('confirmReceiptModal')" class="text-emerald-800 hover:text-hemo-red"><i class="fas fa-times"></i></button>
        </div>
        <form action="<?php echo BASE_URL; ?>/index.php?page=hospital_confirm_receipt" method="POST" class="p-6 space-y-4">
            <input type="hidden" name="request_id" id="receipt_request_id">
            
            <div class="text-center pb-1">
                <p class="text-xs text-hemo-charcoal">
                    Verify the physical blood consignment handed over by the courier / ambulance runner.
                </p>
            </div>

            <!-- Cold-Chain Checkbox Card -->
            <div id="tempCheckCard" class="bg-emerald-50/70 p-4 rounded-xl border-2 border-emerald-200 transition-all duration-300">
                <label class="flex items-start gap-3 cursor-pointer">
                    <input type="checkbox" name="temp_verified" id="temp_verified_checkbox" value="1" checked onchange="toggleDiscrepancySection(this)"
                           class="mt-0.5 w-5 h-5 text-emerald-600 rounded border-gray-300 focus:ring-emerald-500 cursor-pointer">
                    <div class="flex-1">
                        <span class="text-xs font-bold text-emerald-950 block">Cold-Chain & Security Seal Intact</span>
                        <span class="text-[11px] text-emerald-800 leading-relaxed block mt-0.5">
                            Temperature verified between 2°C - 6°C, cool box seal uncompromised, and units accepted for clinical transfusion.
                        </span>
                    </div>
                </label>
            </div>

            <!-- Discrepancy & Rejection Section (Revealed when unchecked) -->
            <div id="discrepancySection" class="hidden space-y-3 bg-red-50 p-4 rounded-xl border border-red-200">
                <div class="flex items-center gap-2 text-xs font-bold text-red-900 uppercase tracking-wider">
                    <i class="fas fa-triangle-exclamation text-hemo-warning"></i> Quality Incident / Discrepancy Report
                </div>
                <p class="text-[11px] text-red-800">
                    Unchecking cold-chain integrity will reject this delivery for transfusion safety and alert the Central Blood Bank Quality Team.
                </p>

                <div>
                    <label class="block text-xs font-semibold text-hemo-navy mb-1">Primary Reason for Discrepancy *</label>
                    <select name="discrepancy_reason" id="discrepancy_reason" class="w-full px-3 py-2 rounded-lg border border-hemo-border text-xs bg-white focus:border-hemo-red outline-none">
                        <option value="Broken / Tampered Security Seal">Broken / Tampered Security Seal</option>
                        <option value="Temperature Exceeded (>6°C / Warm Coolbox)">Temperature Exceeded (>6°C / Warm Coolbox)</option>
                        <option value="Damaged / Leaking Blood Bags">Damaged / Leaking Blood Bags</option>
                        <option value="Incorrect Blood Group / Label Mismatch">Incorrect Blood Group / Label Mismatch</option>
                        <option value="Haemolysis / Clot Observed in Unit">Haemolysis / Clot Observed in Unit</option>
                        <option value="Excessive Transit Delay (>4 Hours)">Excessive Transit Delay (>4 Hours)</option>
                        <option value="Other Discrepancy">Other Quality Discrepancy</option>
                    </select>
                </div>

                <div>
                    <label class="block text-xs font-semibold text-hemo-navy mb-1">Incident Details / Laboratory Notes *</label>
                    <textarea name="discrepancy_notes" id="discrepancy_notes" rows="2" placeholder="Describe the physical condition of the box, measured temp, or package defect..."
                              class="w-full px-3 py-2 rounded-lg border border-hemo-border text-xs bg-white focus:border-hemo-red outline-none"></textarea>
                </div>
            </div>

            <div class="flex gap-3 pt-2">
                <button type="button" onclick="closeModal('confirmReceiptModal')" class="flex-1 py-2.5 rounded-xl bg-hemo-light-gray text-hemo-charcoal font-semibold text-xs hover:bg-gray-200 transition-fast">Cancel</button>
                <button type="submit" id="btnConfirmReceiptSubmit" class="flex-1 py-2.5 rounded-xl bg-emerald-600 hover:bg-emerald-700 text-white font-bold text-xs shadow-md transition-all flex items-center justify-center gap-1.5">
                    <i class="fas fa-check-double"></i> <span>Confirm & Accept Delivery</span>
                </button>
            </div>
        </form>
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

<!-- JavaScript for Modals, Copy PIN & 4-Step Stepper -->
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

    function openConfirmReceiptModal(id) {
        document.getElementById('receipt_request_id').value = id;
        const checkbox = document.getElementById('temp_verified_checkbox');
        if (checkbox) {
            checkbox.checked = true;
            toggleDiscrepancySection(checkbox);
        }
        openModal('confirmReceiptModal');
    }

    function toggleDiscrepancySection(checkbox) {
        const section = document.getElementById('discrepancySection');
        const header = document.getElementById('receiptModalHeader');
        const card = document.getElementById('tempCheckCard');
        const btn = document.getElementById('btnConfirmReceiptSubmit');
        const reason = document.getElementById('discrepancy_reason');
        const notes = document.getElementById('discrepancy_notes');
        if (!section || !header || !card || !btn) return;

        if (checkbox.checked) {
            section.classList.add('hidden');
            header.className = 'px-6 py-4 border-b border-hemo-border bg-emerald-50 flex items-center justify-between transition-colors duration-300';
            card.className = 'bg-emerald-50/70 p-4 rounded-xl border-2 border-emerald-200 transition-all duration-300';
            btn.className = 'flex-1 py-2.5 rounded-xl bg-emerald-600 hover:bg-emerald-700 text-white font-bold text-xs shadow-md transition-all flex items-center justify-center gap-1.5';
            btn.innerHTML = '<i class="fas fa-check-double"></i> <span>Confirm & Accept Delivery</span>';
            if (reason) reason.required = false;
            if (notes) notes.required = false;
        } else {
            section.classList.remove('hidden');
            header.className = 'px-6 py-4 border-b border-hemo-border bg-red-50 flex items-center justify-between transition-colors duration-300';
            card.className = 'bg-red-50/70 p-4 rounded-xl border-2 border-red-300 transition-all duration-300';
            btn.className = 'flex-1 py-2.5 rounded-xl bg-red-600 hover:bg-red-700 text-white font-bold text-xs shadow-md transition-all flex items-center justify-center gap-1.5';
            btn.innerHTML = '<i class="fas fa-shield-xmark"></i> <span>Reject & Report Discrepancy</span>';
            if (reason) reason.required = true;
            if (notes) notes.required = true;
        }
    }

    function copyPinToClipboard(pin, btnId = 'btnCopyPin') {
        navigator.clipboard.writeText(pin).then(() => {
            const btn = document.getElementById(btnId);
            if (btn) {
                const originalHtml = btn.innerHTML;
                btn.innerHTML = '<i class="fas fa-check"></i> Copied!';
                btn.classList.add('bg-green-600', 'text-white');
                setTimeout(() => {
                    btn.innerHTML = originalHtml;
                    btn.classList.remove('bg-green-600', 'text-white');
                }, 2000);
            }
        });
    }

    // Edit Modal Populator
    function openEditModal(request) {
        document.getElementById('edit_request_id').value = request.id;
        document.getElementById('edit_blood_type').value = request.blood_type || '';
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

    // AJAX for View Request Details with 4-Step Stepper & PIN
    async function viewRequest(id) {
        openModal('viewRequestModal');
        document.getElementById('viewLoader').classList.remove('hidden');
        document.getElementById('viewContent').classList.add('hidden');

        try {
            const res = await fetch(`<?php echo BASE_URL; ?>/index.php?page=hospital_get_request&id=${id}`);
            const data = await res.json();
            
            if (data.success && data.data) {
                const r = data.data;
                const bType = r.blood_type || 'Unknown';
                
                // Determine Stepper Active States (1: Submitted, 2: Ready, 3: Dispatched, 4: Received)
                let step = 1;
                if (r.status === 'Fulfilled' || r.collection_status === 'Received') {
                    step = 4;
                } else if (r.status === 'Dispatched' || r.collection_status === 'Dispatched') {
                    step = 3;
                } else if (r.collection_status === 'Ready for Pickup' || r.release_pin) {
                    step = 2;
                }

                const isCompleted = (s) => step > s || (step === 4 && s === 4);
                const isCurrent = (s) => step === s && step !== 4;
                
                const getStepClasses = (s) => {
                    if (isCompleted(s) || (step === 4 && s === 4)) {
                        return 'bg-emerald-600 text-white shadow-sm ring-4 ring-emerald-50';
                    } else if (isCurrent(s)) {
                        return 'bg-blue-600 text-white shadow-md ring-4 ring-blue-50';
                    } else {
                        return 'bg-gray-100 text-gray-400 border border-gray-200';
                    }
                };

                const getLabelClasses = (s) => {
                    if (isCompleted(s) || (step === 4 && s === 4)) {
                        return 'text-emerald-700 font-bold';
                    } else if (isCurrent(s)) {
                        return 'text-blue-700 font-bold';
                    } else {
                        return 'text-gray-400 font-medium';
                    }
                };

                // Render 4-Step Stepper HTML
                const stepperHtml = `
                    <div class="py-4 px-3 bg-slate-50 rounded-2xl border border-slate-200 mb-6">
                        <div class="grid grid-cols-4 relative">
                            <!-- Connecting Line -->
                            <div class="absolute top-1/2 left-1/8 right-1/8 h-1.5 bg-gray-200 -translate-y-1/2 z-0 rounded-full overflow-hidden">
                                <div class="h-full bg-gradient-to-r from-emerald-500 via-teal-500 to-blue-600 transition-all duration-500" style="width: ${((step - 1) / 3) * 100}%"></div>
                            </div>

                            <!-- Step 1: Submitted -->
                            <div class="relative z-10 flex flex-col items-center text-center">
                                <div class="w-8 h-8 rounded-full flex items-center justify-center text-xs font-bold transition-all ${getStepClasses(1)}">
                                    <i class="fas fa-check"></i>
                                </div>
                                <span class="text-[11px] mt-1.5 ${getLabelClasses(1)}">1. Submitted</span>
                                <span class="text-[9px] text-gray-400">${r.created_at ? new Date(r.created_at).toLocaleTimeString([], {hour:'2-digit', minute:'2-digit'}) : ''}</span>
                            </div>

                            <!-- Step 2: Ready for Pickup -->
                            <div class="relative z-10 flex flex-col items-center text-center">
                                <div class="w-8 h-8 rounded-full flex items-center justify-center text-xs font-bold transition-all ${getStepClasses(2)}">
                                    ${isCompleted(2) ? '<i class="fas fa-check"></i>' : (isCurrent(2) ? '<i class="fas fa-box-archive"></i>' : '2')}
                                </div>
                                <span class="text-[11px] mt-1.5 ${getLabelClasses(2)}">2. Ready</span>
                                <span class="text-[9px] text-gray-400">${r.release_pin ? 'PIN Generated' : 'Pending'}</span>
                            </div>

                            <!-- Step 3: Dispatched -->
                            <div class="relative z-10 flex flex-col items-center text-center">
                                <div class="w-8 h-8 rounded-full flex items-center justify-center text-xs font-bold transition-all ${getStepClasses(3)}">
                                    ${isCompleted(3) ? '<i class="fas fa-check"></i>' : (isCurrent(3) ? '<i class="fas fa-truck-fast"></i>' : '3')}
                                </div>
                                <span class="text-[11px] mt-1.5 ${getLabelClasses(3)}">3. In Transit</span>
                                <span class="text-[9px] text-gray-400">${r.dispatched_at ? 'Driver Dispatched' : 'Pending Handover'}</span>
                            </div>

                            <!-- Step 4: Received / Rejected -->
                            <div class="relative z-10 flex flex-col items-center text-center">
                                <div class="w-8 h-8 rounded-full flex items-center justify-center text-xs font-bold transition-all ${r.status === 'Rejected' ? 'bg-red-600 text-white shadow-sm ring-4 ring-red-100' : getStepClasses(4)}">
                                    ${r.status === 'Rejected' ? '<i class="fas fa-triangle-exclamation"></i>' : (step >= 4 ? '<i class="fas fa-check-double"></i>' : '4')}
                                </div>
                                <span class="text-[11px] mt-1.5 ${r.status === 'Rejected' ? 'text-red-700 font-bold' : getLabelClasses(4)}">${r.status === 'Rejected' ? '4. Rejected' : '4. Received'}</span>
                                <span class="text-[9px] text-gray-400">${r.received_at ? (r.status === 'Rejected' ? 'Discrepancy' : 'Verified') : 'Awaiting Delivery'}</span>
                            </div>
                        </div>
                    </div>
                `;

                // Release PIN Card (Shown when PIN is generated)
                let pinCardHtml = '';
                if (r.release_pin) {
                    pinCardHtml = `
                        <div class="p-5 rounded-2xl bg-amber-50 border-2 border-amber-300 shadow-sm relative overflow-hidden">
                            <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-4">
                                <div>
                                    <div class="flex items-center gap-1.5 text-xs font-bold text-amber-900 uppercase tracking-wider mb-1">
                                        <i class="fas fa-key text-amber-600"></i> Click & Collect Handover Token
                                    </div>
                                    <p class="text-xs text-amber-800">
                                        Share this 6-digit PIN with your ambulance driver / courier to authenticate handover at Central Blood Bank.
                                    </p>
                                </div>
                                <div class="flex items-center gap-2">
                                    <div class="bg-white px-4 py-2.5 rounded-xl border border-amber-300 shadow-sm">
                                        <span class="font-mono font-black text-2xl tracking-widest text-amber-950">${r.release_pin}</span>
                                    </div>
                                    <button type="button" id="btnCopyPinModal" onclick="copyPinToClipboard('${r.release_pin}', 'btnCopyPinModal')"
                                            class="px-3.5 py-2.5 rounded-xl bg-amber-600 hover:bg-amber-700 text-white font-bold text-xs shadow-sm transition-fast flex items-center gap-1.5" title="Copy to Clipboard">
                                        <i class="fas fa-copy"></i> Copy PIN
                                    </button>
                                </div>
                            </div>
                        </div>
                    `;
                }

                // Driver Handover Info (Shown when Dispatched or Received)
                let driverInfoHtml = '';
                if (r.collected_by_name || r.dispatched_at) {
                    driverInfoHtml = `
                        <div class="p-4 rounded-xl bg-indigo-50 border border-indigo-200 text-xs space-y-2">
                            <div class="flex items-center justify-between font-bold text-indigo-950">
                                <span><i class="fas fa-id-badge text-indigo-600 mr-1.5"></i> Courier / Runner Handover Details</span>
                                <span class="px-2 py-0.5 rounded bg-indigo-100 text-indigo-800 uppercase text-[10px]">In Custody</span>
                            </div>
                            <div class="grid grid-cols-2 gap-2 text-hemo-charcoal pt-1">
                                <div><span class="text-hemo-gray block">Driver / Runner:</span> <strong>${r.collected_by_name || 'N/A'}</strong></div>
                                <div><span class="text-hemo-gray block">Phone / Reg:</span> <strong>${r.collected_by_phone || 'N/A'}</strong></div>
                                <div><span class="text-hemo-gray block">Dispatched At:</span> <strong>${r.dispatched_at ? new Date(r.dispatched_at).toLocaleString() : 'N/A'}</strong></div>
                                <div><span class="text-hemo-gray block">Cold-Chain Seal:</span> ${r.received_at ? (r.temp_verified == 1 || r.temp_verified === true ? '<strong class="text-emerald-700 font-bold"><i class="fas fa-shield-check"></i> Intact (2°C - 6°C)</strong>' : '<strong class="text-red-600 font-bold"><i class="fas fa-triangle-exclamation"></i> Compromised / Rejected</strong>') : '<strong class="text-blue-700 font-bold"><i class="fas fa-lock"></i> Sealed in Transit</strong>'}</div>
                            </div>
                        </div>
                    `;
                }

                // Hospital Confirm Receipt Action Button
                let confirmReceiptActionHtml = '';
                if (r.status === 'Dispatched' || r.collection_status === 'Dispatched') {
                    confirmReceiptActionHtml = `
                        <div class="p-4 rounded-xl bg-green-50 border border-green-200 flex flex-col sm:flex-row sm:items-center justify-between gap-3">
                            <div>
                                <p class="text-xs font-bold text-green-950">Package Arrived at Hospital?</p>
                                <p class="text-[11px] text-green-800">Confirm receipt to verify cold-chain temperature and close this requisition.</p>
                            </div>
                            <button onclick="closeModal('viewRequestModal'); openConfirmReceiptModal(${r.request_id})"
                                    class="px-5 py-2.5 rounded-xl bg-hemo-success hover:bg-green-700 text-white font-bold text-xs shadow-md transition-fast flex items-center gap-2 whitespace-nowrap">
                                <i class="fas fa-check-double"></i> Confirm Package Received
                            </button>
                        </div>
                    `;
                }

                document.getElementById('viewContent').innerHTML = `
                    <div class="flex items-center justify-between pb-4 border-b border-hemo-border">
                        <div class="flex items-center gap-3">
                            <span class="text-base font-mono font-bold text-hemo-red bg-hemo-light-red px-2.5 py-1 rounded-lg">REQ-${String(r.request_id).padStart(4, '0')}</span>
                            <span class="inline-flex items-center gap-1 px-3 py-1 rounded-lg text-xs font-bold bg-hemo-red text-white">
                                <i class="fas fa-droplet text-[9px]"></i> ${bType}
                            </span>
                            <span class="px-2.5 py-1 rounded-full text-xs font-bold uppercase tracking-tight ${
                                r.status === 'Rejected' || r.status === 'Cancelled' ? 'bg-red-100 text-hemo-warning border border-red-200' :
                                (r.status === 'Fulfilled' ? 'bg-green-100 text-hemo-success border border-green-200' :
                                (r.collection_status === 'Ready for Pickup' ? 'bg-amber-100 text-amber-800 border border-amber-300' :
                                (r.status === 'Dispatched' ? 'bg-indigo-100 text-indigo-700 border border-indigo-200' : 'bg-gray-100 text-gray-700')))
                            }">
                                ${r.status}
                            </span>
                        </div>
                        <div>
                            <span class="text-xs font-semibold px-2.5 py-1 rounded-full ${r.urgency === 'Emergency' ? 'bg-red-100 text-hemo-warning' : (r.urgency === 'Urgent' ? 'bg-amber-100 text-hemo-amber' : 'bg-blue-100 text-blue-700')}">
                                ${r.urgency} Urgency
                            </span>
                        </div>
                    </div>

                    ${stepperHtml}
                    ${pinCardHtml}
                    ${driverInfoHtml}
                    ${confirmReceiptActionHtml}

                    <div class="grid grid-cols-2 gap-4 p-4 rounded-xl bg-hemo-off-white border border-hemo-border text-xs">
                        <div><span class="text-hemo-gray block mb-0.5">Units Requested</span><span class="text-sm font-bold text-hemo-navy">${r.units_requested} units</span></div>
                        <div><span class="text-hemo-gray block mb-0.5">Units Fulfilled</span><span class="text-sm font-bold text-hemo-navy">${r.units_fulfilled || 0} units</span></div>
                        <div><span class="text-hemo-gray block mb-0.5">Hospital</span><span class="font-semibold text-hemo-navy">${r.hospital_name || 'N/A'}</span></div>
                        <div><span class="text-hemo-gray block mb-0.5">Requested By</span><span class="font-semibold text-hemo-navy">${r.requester_first || ''} ${r.requester_last || ''}</span></div>
                        ${r.notes ? `<div class="col-span-2 pt-2 border-t border-hemo-border"><span class="text-hemo-gray block mb-0.5">Clinical Notes:</span><p class="text-hemo-charcoal italic">${r.notes}</p></div>` : ''}
                    </div>
                `;
            }
        } catch (e) {
            document.getElementById('viewContent').innerHTML = `<div class="text-center text-hemo-warning py-4">Failed to load request data.</div>`;
        }
        
        document.getElementById('viewLoader').classList.add('hidden');
        document.getElementById('viewContent').classList.remove('hidden');
    }
</script>

<?php
$content = ob_get_clean();
require_once __DIR__ . '/../layouts/hospital_layout.php';
?>

