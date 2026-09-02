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
                                    <form action="<?php echo BASE_URL; ?>/index.php?page=admin_mark_ready_pickup" method="POST" class="inline" title="Mark Ready for Pickup (Generates 6-Digit PIN)">
                                        <input type="hidden" name="request_id" value="<?php echo $request['request_id']; ?>">
                                        <input type="hidden" name="hospital_id" value="<?php echo $request['hospital_id'] ?? 0; ?>">
                                        <button type="submit" class="p-2 rounded-lg bg-hemo-light-gray text-hemo-charcoal hover:bg-amber-50 hover:text-amber-700 transition-fast" title="Mark Ready for Pickup">
                                            <i class="fas fa-box-archive text-sm"></i>
                                        </button>
                                    </form>

                                    <form action="<?php echo BASE_URL; ?>/index.php?page=admin_dispatch_units" method="POST" class="inline">
                                        <input type="hidden" name="request_id" value="<?php echo $request['request_id']; ?>">
                                        <input type="hidden" name="hospital_id" value="<?php echo $request['hospital_id'] ?? 0; ?>">
                                        <input type="hidden" name="status" value="Fulfilled">
                                        <input type="hidden" name="units_to_dispatch" value="<?php echo (int)($request['units_requested'] ?? $request['units'] ?? 0); ?>">
                                        <button type="submit" class="p-2 rounded-lg bg-hemo-light-gray text-hemo-charcoal hover:bg-green-50 hover:text-hemo-success transition-fast" title="Direct Fulfill" onclick="return confirm('Mark this request as fulfilled?');">
                                            <i class="fas fa-check text-sm"></i>
                                        </button>
                                    </form>
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
            btn.classList.add('bg-green-600', 'text-white');
            setTimeout(() => {
                btn.innerHTML = originalHtml;
                btn.classList.remove('bg-green-600', 'text-white');
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
        
        if (data.success && data.data) {
            const r = data.data;
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

            const stepperHtml = `
                <div class="py-3 px-2 bg-hemo-off-white rounded-xl border border-hemo-border mb-4">
                    <div class="grid grid-cols-4 relative">
                        <div class="absolute top-1/2 left-1/8 right-1/8 h-1 bg-gray-200 -translate-y-1/2 z-0">
                            <div class="h-full bg-hemo-red transition-all duration-500" style="width: ${((step - 1) / 3) * 100}%"></div>
                        </div>

                        <div class="relative z-10 flex flex-col items-center text-center">
                            <div class="w-8 h-8 rounded-full flex items-center justify-center text-xs font-bold ${step >= 1 ? 'bg-hemo-red text-white shadow-md' : 'bg-gray-200 text-gray-500'}">
                                <i class="fas fa-check"></i>
                            </div>
                            <span class="text-[11px] font-bold mt-1 text-hemo-navy">1. Submitted</span>
                            <span class="text-[9px] text-hemo-gray">${r.created_at ? new Date(r.created_at).toLocaleTimeString([], {hour:'2-digit', minute:'2-digit'}) : ''}</span>
                        </div>

                        <div class="relative z-10 flex flex-col items-center text-center">
                            <div class="w-8 h-8 rounded-full flex items-center justify-center text-xs font-bold ${step >= 2 ? 'bg-hemo-red text-white shadow-md' : 'bg-gray-200 text-gray-500'}">
                                ${step >= 2 ? '<i class="fas fa-box-archive"></i>' : '2'}
                            </div>
                            <span class="text-[11px] font-bold mt-1 ${step >= 2 ? 'text-hemo-navy' : 'text-gray-400'}">2. Ready</span>
                            <span class="text-[9px] text-hemo-gray">${r.release_pin ? 'PIN Generated' : 'Pending'}</span>
                        </div>

                        <div class="relative z-10 flex flex-col items-center text-center">
                            <div class="w-8 h-8 rounded-full flex items-center justify-center text-xs font-bold ${step >= 3 ? 'bg-hemo-red text-white shadow-md' : 'bg-gray-200 text-gray-500'}">
                                ${step >= 3 ? '<i class="fas fa-truck-fast"></i>' : '3'}
                            </div>
                            <span class="text-[11px] font-bold mt-1 ${step >= 3 ? 'text-hemo-navy' : 'text-gray-400'}">3. Dispatched</span>
                            <span class="text-[9px] text-hemo-gray">${r.dispatched_at ? 'In Transit' : 'Pending'}</span>
                        </div>

                        <div class="relative z-10 flex flex-col items-center text-center">
                            <div class="w-8 h-8 rounded-full flex items-center justify-center text-xs font-bold ${step >= 4 ? 'bg-hemo-success text-white shadow-md' : 'bg-gray-200 text-gray-500'}">
                                ${step >= 4 ? '<i class="fas fa-check-double"></i>' : '4'}
                            </div>
                            <span class="text-[11px] font-bold mt-1 ${step >= 4 ? 'text-hemo-success' : 'text-gray-400'}">4. Delivered</span>
                            <span class="text-[9px] text-hemo-gray">${r.received_at ? 'Received' : 'Pending'}</span>
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


