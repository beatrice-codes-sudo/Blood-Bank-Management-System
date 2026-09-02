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
                            $colStatus = $request['collection_status'] ?? 'Pending';
                            $statusClass = 'bg-hemo-light-gray text-hemo-gray';
                            $displayStatus = $status;
                            
                            if ($colStatus === 'Ready for Pickup') {
                                $statusClass = 'bg-amber-100 text-amber-800 border border-amber-300';
                                $displayStatus = 'Ready for Pickup';
                            } elseif ($status === 'Dispatched' || $colStatus === 'Dispatched') {
                                $statusClass = 'bg-indigo-100 text-indigo-700 border border-indigo-200';
                                $displayStatus = 'In Transit';
                            } elseif ($status === 'Pending') {
                                $statusClass = 'bg-yellow-100 text-yellow-700';
                            } elseif ($status === 'Processing') {
                                $statusClass = 'bg-blue-100 text-blue-700';
                            } elseif ($status === 'Fulfilled' || $colStatus === 'Received') {
                                $statusClass = 'bg-green-100 text-hemo-success';
                                $displayStatus = 'Fulfilled (Received)';
                            } elseif ($status === 'Rejected') {
                                $statusClass = 'bg-red-100 text-hemo-red';
                            }
                            ?>
                            <span class="inline-flex items-center px-2.5 py-1 rounded-full text-[11px] font-bold uppercase tracking-tight <?php echo $statusClass; ?>">
                                <?php echo sanitize($displayStatus); ?>
                            </span>
                        </td>
                        <td class="px-6 py-4 text-xs text-hemo-gray">
                            <?php echo date('M d, Y H:i', strtotime($request['created_at'])); ?>
                        </td>
                        <td class="px-6 py-4 text-right">
                            <div class="flex items-center justify-end gap-1.5 whitespace-nowrap">
                                <button onclick="viewRequestDetailsAdmin(<?php echo $request['request_id']; ?>)" class="p-2 rounded-lg bg-hemo-light-gray text-hemo-charcoal hover:bg-blue-50 hover:text-blue-600 transition-fast" title="View Details">
                                    <i class="fas fa-eye text-sm"></i>
                                </button>

                                <?php if (($request['collection_status'] ?? '') === 'Ready for Pickup'): ?>
                                    <button onclick="openVerifyPinModal(<?php echo $request['request_id']; ?>)" class="px-2.5 py-1.5 rounded-lg bg-amber-100 text-amber-800 hover:bg-amber-200 transition-fast font-bold text-xs flex items-center gap-1" title="Verify Runner Release PIN">
                                        <i class="fas fa-key text-xs"></i> Verify PIN
                                    </button>
                                <?php elseif ($status !== 'Fulfilled' && $status !== 'Dispatched' && $status !== 'Rejected' && $status !== 'Cancelled'): ?>
                                    <button type="button" onclick="openPrepareModal(<?php echo htmlspecialchars(json_encode([
                                        'request_id' => $request['request_id'],
                                        'hospital_id' => $request['hospital_id'] ?? 0,
                                        'hospital_name' => $request['hospital_name'] ?? 'Hospital Partner',
                                        'blood_type' => $request['blood_type'] ?? 'N/A',
                                        'units_requested' => (int)($request['units_requested'] ?? $request['units'] ?? 0),
                                        'units_fulfilled' => (int)($request['units_fulfilled'] ?? 0)
                                    ])); ?>)" class="p-2 rounded-lg bg-hemo-light-gray text-hemo-charcoal hover:bg-amber-50 hover:text-amber-700 transition-fast" title="Fulfill / Prepare Request">
                                        <i class="fas fa-boxes-packing text-sm"></i>
                                    </button>
                                <?php endif; ?>
                                
                                <form action="<?php echo BASE_URL; ?>/index.php?page=admin_delete_request" method="POST" class="inline">
                                    <input type="hidden" name="request_id" value="<?php echo $request['request_id']; ?>">
                                    <button type="submit" class="p-2 rounded-lg bg-hemo-light-gray text-hemo-charcoal hover:bg-red-50 hover:text-hemo-warning transition-fast" title="Delete Request" onclick="return confirm('Are you sure you want to delete this request?');">
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

<!-- View Details Modal (Admin) -->
<div id="viewRequestModalAdmin" class="fixed inset-0 bg-hemo-navy/50 backdrop-blur-sm z-[1001] hidden flex items-center justify-center p-4 opacity-0 transition-opacity duration-300">
    <div class="bg-white rounded-2xl shadow-2xl w-full max-w-2xl max-h-[90vh] flex flex-col transform scale-95 transition-transform duration-300 overflow-hidden">
        <div class="px-6 py-4 border-b border-hemo-border flex items-center justify-between bg-hemo-off-white">
            <div class="flex items-center gap-2">
                <div class="w-8 h-8 rounded-lg bg-hemo-light-red flex items-center justify-center text-hemo-red">
                    <i class="fas fa-clipboard-list"></i>
                </div>
                <div>
                    <h3 class="text-base font-bold text-hemo-navy">Request Audit & Handover Details</h3>
                    <p class="text-xs text-hemo-gray">Click & Collect Handover & Tracking</p>
                </div>
            </div>
            <button type="button" onclick="closeModal('viewRequestModalAdmin')" class="text-hemo-gray hover:text-hemo-red transition-fast"><i class="fas fa-times text-lg"></i></button>
        </div>
        <div class="p-6 overflow-y-auto flex-1">
            <div id="viewLoaderAdmin" class="flex justify-center py-12">
                <i class="fas fa-spinner fa-spin text-3xl text-hemo-red"></i>
            </div>
            <div id="viewContentAdmin" class="hidden space-y-4">
                <!-- Dynamic Content with 4-Step Stepper & PIN Card -->
            </div>
        </div>
    </div>
</div>

<!-- Prepare & Fulfill Request Modal (Supports Partial & Full Fulfillment) -->
<div id="prepareRequestModal" class="fixed inset-0 bg-hemo-navy/50 backdrop-blur-sm z-[1001] hidden flex items-center justify-center p-4 opacity-0 transition-opacity duration-300">
    <div class="bg-white rounded-2xl shadow-2xl w-full max-w-lg overflow-hidden transform scale-95 transition-transform duration-300">
        <div class="px-6 py-4 border-b border-hemo-border bg-gradient-to-r from-hemo-navy to-slate-800 text-white flex items-center justify-between">
            <h3 class="text-base font-bold flex items-center gap-2">
                <i class="fas fa-boxes-packing text-hemo-gold"></i> Fulfill / Dispatch Blood Request
            </h3>
            <button type="button" onclick="closeModal('prepareRequestModal')" class="text-white/70 hover:text-white"><i class="fas fa-times"></i></button>
        </div>
        <div class="p-6 space-y-5">
            <!-- Summary Info -->
            <div class="p-4 rounded-xl bg-hemo-off-white border border-hemo-border grid grid-cols-2 gap-3 text-xs">
                <div>
                    <span class="text-hemo-gray block font-semibold">Hospital</span>
                    <strong id="prepModalHospital" class="text-sm text-hemo-navy">Hospital Name</strong>
                </div>
                <div>
                    <span class="text-hemo-gray block font-semibold">Blood Group</span>
                    <span id="prepModalBloodType" class="inline-flex items-center gap-1 px-2.5 py-0.5 rounded font-bold text-white bg-hemo-red">O+</span>
                </div>
                <div>
                    <span class="text-hemo-gray block font-semibold">Units Requested</span>
                    <strong id="prepModalRequested" class="text-hemo-navy text-sm">0 units</strong>
                </div>
                <div>
                    <span class="text-hemo-gray block font-semibold">Already Fulfilled</span>
                    <strong id="prepModalFulfilled" class="text-emerald-700 text-sm">0 units</strong>
                </div>
            </div>

            <!-- Form -->
            <form id="prepareActionForm" method="POST" action="<?php echo BASE_URL; ?>/index.php?page=admin_mark_ready_pickup" class="space-y-4">
                <input type="hidden" name="request_id" id="prep_request_id">
                <input type="hidden" name="hospital_id" id="prep_hospital_id">
                
                <div>
                    <div class="flex items-center justify-between mb-1.5">
                        <label class="block text-xs font-bold text-hemo-navy uppercase tracking-wider">
                            Units to Allocate / Fulfill *
                        </label>
                        <span id="prepRemainingHint" class="text-xs text-hemo-gray">Remaining: 0 units</span>
                    </div>
                    <div class="flex items-center gap-3">
                        <input type="number" name="units_allocated" id="prep_units_input" required min="1" max="100"
                               class="w-full px-4 py-2.5 rounded-xl border border-hemo-border font-bold text-lg text-hemo-navy focus:border-hemo-red focus:ring-2 focus:ring-hemo-red/10 outline-none transition-fast">
                        <span class="text-sm font-semibold text-hemo-charcoal">Units</span>
                    </div>
                    <p class="text-[11px] text-hemo-gray mt-1.5">
                        Allocate requested units for full fulfillment, or enter a smaller amount to <strong>partially fulfill</strong> this requisition.
                    </p>
                </div>

                <!-- Live Stock Depletion Warning Box -->
                <div id="prepDepletionWarning" class="hidden"></div>

                <div class="pt-3 border-t border-hemo-border grid grid-cols-2 gap-3">
                    <button type="submit" onclick="document.getElementById('prepareActionForm').action='<?php echo BASE_URL; ?>/index.php?page=admin_mark_ready_pickup';"
                            class="py-3 px-4 rounded-xl bg-amber-600 hover:bg-amber-700 text-white font-bold text-xs shadow-md transition-fast flex items-center justify-center gap-1.5 text-center">
                        <i class="fas fa-box-archive"></i> Ready for Pickup (PIN)
                    </button>
                    
                    <button type="submit" onclick="document.getElementById('prep_units_dispatch').value=document.getElementById('prep_units_input').value; document.getElementById('prepareActionForm').action='<?php echo BASE_URL; ?>/index.php?page=admin_dispatch_units';"
                            class="py-3 px-4 rounded-xl bg-emerald-600 hover:bg-emerald-700 text-white font-bold text-xs shadow-md transition-fast flex items-center justify-center gap-1.5 text-center">
                        <i class="fas fa-check"></i> Direct Dispatch
                    </button>
                </div>
                <input type="hidden" name="units_to_dispatch" id="prep_units_dispatch">
            </form>
        </div>
    </div>
</div>

<!-- Verify Release PIN Modal -->
<div id="verifyPinModal" class="fixed inset-0 bg-hemo-navy/50 backdrop-blur-sm z-[1001] hidden flex items-center justify-center p-4 opacity-0 transition-opacity duration-300">
    <div class="bg-white rounded-xl shadow-2xl w-full max-w-md flex flex-col transform scale-95 transition-transform duration-300 overflow-hidden">
        <div class="px-6 py-4 border-b border-hemo-border bg-amber-50 flex items-center justify-between">
            <h3 class="text-base font-bold text-amber-900 flex items-center gap-2">
                <i class="fas fa-key text-amber-600"></i> Verify Click & Collect Handover
            </h3>
            <button type="button" onclick="closeModal('verifyPinModal')" class="text-amber-800 hover:text-hemo-red"><i class="fas fa-times"></i></button>
        </div>
        <form id="verifyPinForm" onsubmit="submitVerifyPin(event)" class="p-6">
            <input type="hidden" name="request_id" id="pin_request_id">
            
            <p class="text-xs text-hemo-charcoal mb-4">
                Ask the hospital ambulance driver or lab runner for the <strong>6-digit Release PIN</strong>.
            </p>

            <div class="space-y-4 mb-6">
                <div>
                    <label class="block text-xs font-bold text-hemo-charcoal mb-1 uppercase tracking-wider">6-Digit Release PIN *</label>
                    <input type="text" name="release_pin" id="pin_input" required maxlength="10" placeholder="e.g. 748291" 
                           class="w-full px-4 py-3 rounded-lg border-2 border-amber-300 text-center font-mono font-bold text-2xl tracking-widest text-hemo-navy focus:border-amber-500 focus:ring-0 outline-none">
                </div>

                <div>
                    <label class="block text-xs font-semibold text-hemo-charcoal mb-1">Runner / Driver Full Name *</label>
                    <input type="text" name="collected_by_name" id="runner_name_input" required placeholder="e.g. John Mwangi (Driver)" 
                           class="w-full px-3 py-2 rounded-lg border border-hemo-border text-sm focus:border-hemo-red outline-none">
                </div>

                <div>
                    <label class="block text-xs font-semibold text-hemo-charcoal mb-1">Runner Phone / Ambulance Reg</label>
                    <input type="text" name="collected_by_phone" id="runner_phone_input" placeholder="e.g. +254711... or KCD 123A" 
                           class="w-full px-3 py-2 rounded-lg border border-hemo-border text-sm focus:border-hemo-red outline-none">
                </div>
            </div>

            <div id="pinErrorMessage" class="hidden mb-4 p-3 rounded-lg bg-red-100 text-hemo-warning text-xs font-semibold"></div>

            <div class="flex gap-3">
                <button type="button" onclick="closeModal('verifyPinModal')" class="flex-1 py-2.5 rounded-lg bg-hemo-light-gray text-hemo-charcoal font-semibold text-sm hover:bg-gray-200 transition-fast">Cancel</button>
                <button type="submit" id="btnSubmitPin" class="flex-1 py-2.5 rounded-lg bg-amber-600 hover:bg-amber-700 text-white font-bold text-sm shadow-md transition-fast">
                    Verify & Release Units
                </button>
            </div>
        </form>
    </div>
</div>

<script>
function openModal(id){const m=document.getElementById(id);m.classList.remove('hidden');void m.offsetWidth;m.classList.remove('opacity-0');m.querySelector('div').classList.remove('scale-95');}
function closeModal(id){const m=document.getElementById(id);m.classList.add('opacity-0');m.querySelector('div').classList.add('scale-95');setTimeout(()=>m.classList.add('hidden'),300);}

const stockInventoryMap = <?php echo json_encode(array_column($stockSummary ?? [], null, 'blood_type')); ?>;

function checkDepletionWarning() {
    const input = document.getElementById('prep_units_input');
    const warningBox = document.getElementById('prepDepletionWarning');
    if (!input || !warningBox) return;

    const allocated = parseInt(input.value) || 0;
    const currentAvailable = parseInt(input.dataset.availableStock) || 0;
    const threshold = parseInt(input.dataset.threshold) || 5;
    const remaining = currentAvailable - allocated;
    const bType = input.dataset.bloodType || 'Blood';

    if (allocated > currentAvailable) {
        warningBox.className = 'p-3 rounded-xl bg-red-50 border border-red-200 text-xs text-red-900 flex items-start gap-2';
        warningBox.innerHTML = `<i class="fas fa-circle-exclamation text-red-600 mt-0.5"></i> <div><strong>Insufficient Stock:</strong> Only ${currentAvailable} unit(s) of ${bType} available in central blood bank. Allocation exceeds inventory.</div>`;
        warningBox.classList.remove('hidden');
    } else if (remaining < threshold) {
        warningBox.className = 'p-3 rounded-xl bg-amber-50 border border-amber-300 text-xs text-amber-900 flex items-start gap-2';
        warningBox.innerHTML = `<i class="fas fa-triangle-exclamation text-amber-600 mt-0.5"></i> <div><strong>Critical Depletion Warning:</strong> Fulfilling ${allocated} unit(s) will drop reserve ${bType} stock to <strong>${remaining} unit(s)</strong> (below safety threshold of ${threshold}).</div>`;
        warningBox.classList.remove('hidden');
    } else {
        warningBox.className = 'p-2.5 rounded-xl bg-emerald-50 border border-emerald-200 text-xs text-emerald-900 flex items-center gap-2';
        warningBox.innerHTML = `<i class="fas fa-shield-check text-emerald-600"></i> <span>Adequate Stock: ${remaining} unit(s) will remain in reserve after fulfillment.</span>`;
        warningBox.classList.remove('hidden');
    }
}

function openPrepareModal(req) {
    document.getElementById('prep_request_id').value = req.request_id;
    document.getElementById('prep_hospital_id').value = req.hospital_id;
    document.getElementById('prepModalHospital').textContent = req.hospital_name;
    document.getElementById('prepModalBloodType').textContent = req.blood_type;
    document.getElementById('prepModalRequested').textContent = req.units_requested + ' units';
    document.getElementById('prepModalFulfilled').textContent = req.units_fulfilled + ' units';

    const remaining = Math.max(1, req.units_requested - req.units_fulfilled);
    document.getElementById('prepRemainingHint').textContent = 'Remaining: ' + remaining + ' units';
    
    const stockInfo = stockInventoryMap[req.blood_type] || { unit_count: 0, min_threshold: 5 };
    const input = document.getElementById('prep_units_input');
    input.max = req.units_requested;
    input.value = remaining;
    input.dataset.availableStock = stockInfo.unit_count || 0;
    input.dataset.threshold = stockInfo.min_threshold || 5;
    input.dataset.bloodType = req.blood_type;
    input.oninput = checkDepletionWarning;

    checkDepletionWarning();
    openModal('prepareRequestModal');
}

function openVerifyPinModal(requestId) {
    document.getElementById('pin_request_id').value = requestId;
    document.getElementById('pin_input').value = '';
    document.getElementById('runner_name_input').value = '';
    document.getElementById('runner_phone_input').value = '';
    document.getElementById('pinErrorMessage').classList.add('hidden');
    openModal('verifyPinModal');
}

async function submitVerifyPin(e) {
    e.preventDefault();
    const btn = document.getElementById('btnSubmitPin');
    const errBox = document.getElementById('pinErrorMessage');
    errBox.classList.add('hidden');
    btn.disabled = true;
    btn.innerHTML = '<i class="fas fa-spinner fa-spin"></i> Verifying...';

    const formData = new FormData(document.getElementById('verifyPinForm'));

    try {
        const response = await fetch('<?php echo BASE_URL; ?>/index.php?page=admin_verify_release_pin', {
            method: 'POST',
            body: formData
        });
        const data = await response.json();

        if (data.success) {
            alert(data.message);
            window.location.reload();
        } else {
            errBox.textContent = data.message || 'Invalid PIN entered.';
            errBox.classList.remove('hidden');
            btn.disabled = false;
            btn.innerHTML = 'Verify & Release Units';
        }
    } catch (err) {
        errBox.textContent = 'Server connection error. Please try again.';
        errBox.classList.remove('hidden');
        btn.disabled = false;
        btn.innerHTML = 'Verify & Release Units';
    }
}

function copyPinToClipboard(pin, btnId = 'btnCopyPinAdminReq') {
    navigator.clipboard.writeText(pin).then(() => {
        const btn = document.getElementById(btnId);
        if (btn) {
            const originalHtml = btn.innerHTML;
            btn.innerHTML = '<i class="fas fa-check"></i> Copied!';
            btn.classList.add('bg-green-600');
            setTimeout(() => {
                btn.innerHTML = originalHtml;
                btn.classList.remove('bg-green-600');
            }, 2000);
        }
    });
}

const urgencyLabels = {'Normal':['Low','text-blue-700'],'Urgent':['High','text-amber-700'],'Emergency':['Critical','text-hemo-warning']};
const statusLabels = {'Pending':'bg-amber-100 text-amber-700','Processing':'bg-blue-100 text-blue-700','Dispatched':'bg-indigo-100 text-indigo-700','Fulfilled':'bg-green-100 text-hemo-success','Partially Fulfilled':'bg-indigo-100 text-indigo-700','Rejected':'bg-red-100 text-hemo-warning','Cancelled':'bg-gray-100 text-gray-600'};

async function viewRequestDetailsAdmin(id) {
    openModal('viewRequestModalAdmin');
    document.getElementById('viewLoaderAdmin').classList.remove('hidden');
    document.getElementById('viewContentAdmin').classList.add('hidden');
    
    try {
        const res = await fetch(`<?php echo BASE_URL; ?>/index.php?page=admin_get_request_json&id=${id}`);
        const data = await res.json();
        
        if (data.error) {
            document.getElementById('viewContentAdmin').innerHTML = `<div class="text-center text-hemo-warning py-4">${data.error}</div>`;
        } else {
            const r = data.request;
            const urg = urgencyLabels[r.urgency] || ['Medium','text-gray-700'];
            const sc = statusLabels[r.status] || 'bg-gray-100 text-gray-600';
            
            // Stepper Step Number (1: Submitted, 2: Ready, 3: Dispatched, 4: Received)
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

            const stepperHtml = `
                <div class="py-4 px-3 bg-slate-50 rounded-2xl border border-slate-200 mb-5">
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

                        <!-- Step 3: In Transit -->
                        <div class="relative z-10 flex flex-col items-center text-center">
                            <div class="w-8 h-8 rounded-full flex items-center justify-center text-xs font-bold transition-all ${getStepClasses(3)}">
                                ${isCompleted(3) ? '<i class="fas fa-check"></i>' : (isCurrent(3) ? '<i class="fas fa-truck-fast"></i>' : '3')}
                            </div>
                            <span class="text-[11px] mt-1.5 ${getLabelClasses(3)}">3. In Transit</span>
                            <span class="text-[9px] text-gray-400">${r.dispatched_at ? 'Driver Dispatched' : 'Pending'}</span>
                        </div>

                        <!-- Step 4: Received / Rejected -->
                        <div class="relative z-10 flex flex-col items-center text-center">
                            <div class="w-8 h-8 rounded-full flex items-center justify-center text-xs font-bold transition-all ${r.status === 'Rejected' ? 'bg-red-600 text-white shadow-sm ring-4 ring-red-100' : getStepClasses(4)}">
                                ${r.status === 'Rejected' ? '<i class="fas fa-triangle-exclamation"></i>' : (step >= 4 ? '<i class="fas fa-check-double"></i>' : '4')}
                            </div>
                            <span class="text-[11px] mt-1.5 ${r.status === 'Rejected' ? 'text-red-700 font-bold' : getLabelClasses(4)}">${r.status === 'Rejected' ? '4. Rejected' : '4. Delivered'}</span>
                            <span class="text-[9px] text-gray-400">${r.received_at ? (r.status === 'Rejected' ? 'Discrepancy' : 'Received') : 'Pending'}</span>
                        </div>
                    </div>
                </div>
            `;

            let pinCardHtml = '';
            if (r.release_pin) {
                pinCardHtml = `
                    <div class="p-4 rounded-xl bg-amber-50 border border-amber-300 flex flex-col sm:flex-row sm:items-center justify-between gap-3">
                        <div>
                            <div class="flex items-center gap-1.5 text-xs font-bold text-amber-900 uppercase">
                                <i class="fas fa-key text-amber-600"></i> Click & Collect Release PIN
                            </div>
                            <p class="text-[11px] text-amber-800">Runner must present this code at counter.</p>
                        </div>
                        <div class="flex items-center gap-2">
                            <div class="bg-white px-3 py-1.5 rounded-lg border border-amber-300 font-mono font-bold text-xl tracking-widest text-amber-950">
                                ${r.release_pin}
                            </div>
                            <button type="button" id="btnCopyPinAdminReq" onclick="copyPinToClipboard('${r.release_pin}', 'btnCopyPinAdminReq')"
                                    class="px-3 py-2 rounded-lg bg-amber-600 hover:bg-amber-700 text-white font-bold text-xs shadow-sm transition-fast flex items-center gap-1">
                                <i class="fas fa-copy"></i> Copy
                            </button>
                        </div>
                    </div>
                `;
            }

            let driverInfoHtml = '';
            if (r.collected_by_name || r.dispatched_at) {
                driverInfoHtml = `
                    <div class="p-3.5 rounded-xl bg-indigo-50 border border-indigo-200 text-xs">
                        <p class="font-bold text-indigo-950 mb-1.5"><i class="fas fa-id-badge text-indigo-600 mr-1"></i> Courier Handover Audit</p>
                        <div class="grid grid-cols-2 gap-2 text-hemo-charcoal">
                            <div><span class="text-hemo-gray block">Driver:</span> <strong>${r.collected_by_name || 'N/A'}</strong></div>
                            <div><span class="text-hemo-gray block">Phone / Reg:</span> <strong>${r.collected_by_phone || 'N/A'}</strong></div>
                            <div><span class="text-hemo-gray block">Dispatched:</span> <strong>${r.dispatched_at ? new Date(r.dispatched_at).toLocaleString() : 'N/A'}</strong></div>
                            <div><span class="text-hemo-gray block">Delivery Received:</span> <strong>${r.received_at ? new Date(r.received_at).toLocaleString() : 'In Transit'}</strong></div>
                            <div class="col-span-2 pt-1 border-t border-indigo-100"><span class="text-hemo-gray block">Cold-Chain Quality Status:</span> ${r.received_at ? (r.temp_verified == 1 || r.temp_verified === true ? '<strong class="text-emerald-700 font-bold"><i class="fas fa-shield-check"></i> Intact (Verified 2°C - 6°C)</strong>' : '<strong class="text-red-600 font-bold"><i class="fas fa-triangle-exclamation"></i> Compromised / Discrepancy Reported</strong>') : '<strong class="text-blue-700 font-bold"><i class="fas fa-lock"></i> Sealed in Transit</strong>'}</div>
                        </div>
                    </div>
                `;
            }

            let actionButtonsHtml = '';
            if (r.collection_status === 'Ready for Pickup') {
                actionButtonsHtml = `
                    <div class="pt-2">
                        <button onclick="closeModal('viewRequestModalAdmin'); openVerifyPinModal(${r.request_id})"
                                class="w-full py-2.5 bg-amber-600 hover:bg-amber-700 text-white font-bold text-xs rounded-xl shadow-md transition-fast flex items-center justify-center gap-2">
                            <i class="fas fa-key"></i> Verify Runner Release PIN & Dispatch Units
                        </button>
                    </div>
                `;
            }

            document.getElementById('viewContentAdmin').innerHTML = `
                <div class="space-y-4">
                    <div class="flex items-center justify-between pb-3 border-b border-hemo-border">
                        <div class="flex items-center gap-3">
                            <span class="text-sm font-mono font-bold text-hemo-red bg-hemo-light-red px-2.5 py-1 rounded">REQ-${String(r.request_id).padStart(4,'0')}</span>
                            <span class="inline-flex items-center px-2.5 py-1 rounded-full text-xs font-semibold ${sc}">${r.status}</span>
                        </div>
                        <span class="text-xs ${urg[1]} font-semibold">${urg[0]} Urgency</span>
                    </div>

                    ${stepperHtml}
                    ${pinCardHtml}
                    ${driverInfoHtml}
                    ${actionButtonsHtml}

                    <div class="grid grid-cols-2 gap-4 p-4 rounded-xl bg-hemo-off-white border border-hemo-border text-xs">
                        <div><span class="text-hemo-gray block">Blood Type</span><span class="inline-flex items-center gap-1 px-2 py-0.5 rounded-lg text-xs font-bold bg-hemo-red text-white"><i class="fas fa-droplet text-[9px]"></i> ${r.blood_type || 'N/A'}</span></div>
                        <div><span class="text-hemo-gray block">Units</span><span class="text-sm font-bold text-hemo-navy">${r.units_fulfilled || 0} / ${r.units_requested} units</span></div>
                        <div><span class="text-hemo-gray block">Hospital</span><span class="font-semibold text-hemo-navy">${r.hospital_name || 'N/A'}</span></div>
                        <div><span class="text-hemo-gray block">Requested On</span><span class="font-semibold text-hemo-navy">${r.created_at ? new Date(r.created_at).toLocaleDateString('en-US',{month:'short',day:'numeric',year:'numeric'}) : 'N/A'}</span></div>
                    </div>

                    ${r.requester_first ? `<div class="bg-hemo-light-gray rounded-xl p-3 text-xs">
                        <p class="text-hemo-gray font-bold uppercase mb-1">Requested By</p>
                        <p class="font-semibold text-hemo-navy">${r.requester_first} ${r.requester_last || ''}</p>
                        <p class="text-hemo-charcoal mt-0.5"><i class="fas fa-envelope text-hemo-gray mr-1"></i>${r.requester_email || 'N/A'} &nbsp;|&nbsp; <i class="fas fa-phone text-hemo-gray mr-1"></i>${r.requester_phone || 'N/A'}</p>
                    </div>` : ''}
                </div>
            `;
        }
    } catch(e) {
        document.getElementById('viewContentAdmin').innerHTML = '<p class="text-center text-hemo-warning py-4">Failed to load request.</p>';
    }
    document.getElementById('viewLoaderAdmin').classList.add('hidden');
    document.getElementById('viewContentAdmin').classList.remove('hidden');
}
</script>

<?php
$content = ob_get_clean();
require_once __DIR__ . '/../layouts/dashboard_layout.php';
?>


