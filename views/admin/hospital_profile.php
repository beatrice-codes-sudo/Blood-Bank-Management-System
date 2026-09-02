<?php
$pageTitle = 'Hospital Profile';
$hospital = $hospital ?? [];
$requests = $requests ?? [];
$appointments = $appointments ?? [];
$hName = sanitize($hospital['hospital_name']);
$manageName = sanitize($hospital['first_name'].' '.$hospital['last_name']);
$managerEmail = sanitize($hospital['manager_email']);
$hId = $hospital['hospital_id'];
ob_start();
$flash = getFlashMessage();
$urgencyMap = ['Normal'=>['Low','bg-blue-100 text-blue-700'],'Urgent'=>['High','bg-amber-100 text-amber-700'],'Emergency'=>['Critical','bg-red-100 text-hemo-warning']];
$statusColors = ['Pending'=>'bg-amber-100 text-amber-700','Processing'=>'bg-blue-100 text-blue-700','Dispatched'=>'bg-indigo-100 text-indigo-700','Fulfilled'=>'bg-green-100 text-hemo-success','Partially Fulfilled'=>'bg-indigo-100 text-indigo-700','Rejected'=>'bg-red-100 text-hemo-warning','Cancelled'=>'bg-gray-100 text-gray-600'];

?>

<?php if ($flash): ?>
<div class="mb-6 px-4 py-3 rounded-lg text-sm font-semibold <?php echo $flash['type']==='success'?'bg-green-100 text-hemo-success border border-green-200':'bg-red-100 text-hemo-warning border border-red-200'; ?> flex items-center gap-2">
    <i class="fas <?php echo $flash['type']==='success'?'fa-check-circle':'fa-exclamation-circle'; ?>"></i>
    <?php echo sanitize($flash['message']); ?>
</div>
<?php endif; ?>

<!-- Back Link -->
<a href="<?php echo BASE_URL; ?>/index.php?page=admin_hospitals" class="inline-flex items-center gap-2 text-sm text-hemo-gray hover:text-hemo-red transition-fast mb-6 no-underline">
    <i class="fas fa-arrow-left"></i> Back to Hospitals
</a>

<!-- Hospital Info Card -->
<div class="bg-white rounded-xl shadow-card p-6 lg:p-8 mb-8 ">
    <div class="flex flex-col lg:flex-row lg:items-start gap-6">
        <div class="w-20 h-20 rounded-2xl bg-blue-50 flex items-center justify-center text-hemo-info text-3xl flex-shrink-0">
            <i class="fas fa-hospital"></i>
        </div>
        <div class="flex-1 min-w-0">
            <div class="flex flex-col sm:flex-row sm:items-center gap-2 mb-1">
                <h1 class="font-display text-2xl font-bold text-hemo-navy"><?php echo $hName; ?></h1>
                <?php if ($hospital['is_active']): ?>
                    <span class="inline-flex items-center px-2.5 py-1 rounded-full text-xs font-semibold bg-green-100 text-hemo-success w-fit">Active</span>
                <?php else: ?>
                    <span class="inline-flex items-center px-2.5 py-1 rounded-full text-xs font-semibold bg-red-100 text-hemo-warning w-fit">Inactive</span>
                <?php endif; ?>
            </div>
            <p class="text-xs font-mono text-hemo-info mb-4">Code: <?php echo sanitize($hospital['hospital_code']); ?> <?php if($hospital['license_number']): ?> &bull; License: <?php echo sanitize($hospital['license_number']); ?><?php endif; ?></p>
            <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 gap-x-8 gap-y-3">
                <div><span class="text-xs text-hemo-gray block"><i class="fas fa-map-marker-alt w-4 text-hemo-red"></i> Address</span><span class="text-sm font-medium"><?php echo sanitize($hospital['address']); ?>, <?php echo sanitize($hospital['city']); ?><?php if($hospital['region']): ?>, <?php echo sanitize($hospital['region']); ?><?php endif; ?></span></div>
                <div><span class="text-xs text-hemo-gray block"><i class="fas fa-phone w-4 text-hemo-red"></i> Phone</span><span class="text-sm font-medium"><?php echo sanitize($hospital['phone']); ?></span></div>
                <div><span class="text-xs text-hemo-gray block"><i class="fas fa-envelope w-4 text-hemo-red"></i> Email</span><span class="text-sm font-medium"><?php echo sanitize($hospital['email'] ?? 'N/A'); ?></span></div>
                <div><span class="text-xs text-hemo-gray block"><i class="fas fa-user-tie w-4 text-hemo-red"></i> Contact Person</span><span class="text-sm font-medium"><?php echo $manageName; ?></span></div>
                <div><span class="text-xs text-hemo-gray block"><i class="fas fa-at w-4 text-hemo-red"></i> Manager Email</span><span class="text-sm font-medium"><?php echo $managerEmail; ?></span></div>
                <div><span class="text-xs text-hemo-gray block"><i class="fas fa-calendar w-4 text-hemo-red"></i> Registered</span><span class="text-sm font-medium"><?php echo date('M d, Y', strtotime($hospital['created_at'])); ?></span></div>
            </div>
        </div>
    </div>
</div>

<!-- Request Stats -->
<div class="grid grid-cols-2 lg:grid-cols-4 gap-4 mb-8">
    <div class="stat-card bg-white rounded-xl shadow-card p-5 cursor-default">
        <p class="text-2xl font-bold text-hemo-navy"><?php echo $requestStats['total']??0; ?></p> 
        <p class="text-xs text-hemo-gray mt-1">Total Requests</p>
    </div>
    <div class="stat-card bg-white rounded-xl shadow-card p-5 cursor-default">
        <p class="text-2xl font-bold text-hemo-navy"><?php echo ($requestStats['pending']??0)+($requestStats['processing']??0); ?></p>
        <p class="text-xs text-hemo-gray mt-1">Active Requests</p>
    </div>
    <div class="stat-card bg-white rounded-xl shadow-card p-5 cursor-default">
        <p class="text-2xl font-bold text-hemo-navy"><?php echo $requestStats['fulfilled']??0; ?></p>
        <p class="text-xs text-hemo-gray mt-1">Fulfilled</p>
    </div>
    <div class="stat-card bg-white rounded-xl shadow-card p-5 cursor-default">
        <p class="text-2xl font-bold text-hemo-navy"><?php echo ($requestStats['rejected']??0)+($requestStats['cancelled']??0); ?></p>
        <p class="text-xs text-hemo-gray mt-1">Rejected / Cancelled</p>
    </div>
</div>

<!-- Blood Requests Table -->
<div class="bg-white rounded-xl shadow-card overflow-hidden mb-8">
    <div class="p-6 border-b border-hemo-border flex items-center gap-3">
        <i class="fas fa-clipboard-list text-hemo-red"></i>
        <h3 class="text-lg font-semibold text-hemo-navy">Blood Requests</h3>
        <span class="ml-auto text-xs text-hemo-gray"><?php echo count($requests); ?> total</span>
    </div>
    <div class="overflow-x-auto">
        <table class="w-full text-left border-collapse">
            <thead><tr class="bg-hemo-light-gray">
                <th class="px-6 py-3 text-xs font-semibold text-hemo-gray uppercase">ID</th>
                <th class="px-6 py-3 text-xs font-semibold text-hemo-gray uppercase">Blood Type</th>
                <th class="px-6 py-3 text-xs font-semibold text-hemo-gray uppercase">Units</th>
                <th class="px-6 py-3 text-xs font-semibold text-hemo-gray uppercase">Urgency</th>
                <th class="px-6 py-3 text-xs font-semibold text-hemo-gray uppercase">Status</th>
                <th class="px-6 py-3 text-xs font-semibold text-hemo-gray uppercase">Requested</th>
                <th class="px-6 py-3 text-xs font-semibold text-hemo-gray uppercase">By</th>
                <th class="px-6 py-3 text-xs font-semibold text-hemo-gray uppercase text-right">Actions</th>
            </tr></thead>
            <tbody class="divide-y divide-hemo-border">
            <?php if(empty($requests)): ?>
                <tr><td colspan="8" class="px-6 py-12 text-center text-hemo-gray"><i class="fas fa-inbox text-3xl mb-2 block text-hemo-border"></i>No blood requests found.</td></tr>
            <?php else: foreach($requests as $r):
                $urg = $urgencyMap[$r['urgency']] ?? ['Medium','bg-gray-100 text-gray-700'];
                $stColor = $statusColors[$r['status']] ?? 'bg-gray-100 text-gray-600';
            ?>
                <tr class="hover:bg-hemo-off-white transition-fast">
                    <td class="px-6 py-4"><button onclick="viewRequest(<?php echo $r['request_id']; ?>)" class="text-sm font-mono text-hemo-red hover:underline cursor-pointer bg-transparent border-none p-0">R-<?php echo str_pad($r['request_id'],4,'0',STR_PAD_LEFT); ?></button></td>
                    <td class="px-6 py-4"><span class="inline-flex items-center gap-1 px-2.5 py-1 rounded-lg text-xs font-bold bg-hemo-red text-white"><i class="fas fa-droplet text-[9px]"></i> <?php echo sanitize($r['blood_type']??'N/A'); ?></span></td>
                    <td class="px-6 py-4 text-sm"><?php echo $r['units_fulfilled']??0; ?>/<?php echo $r['units_requested']; ?></td>
                    <td class="px-6 py-4"><span class="inline-flex items-center px-2.5 py-1 rounded-full text-xs font-semibold <?php echo $urg[1]; ?>"><?php echo $urg[0]; ?></span></td>
                    <td class="px-6 py-4"><span class="inline-flex items-center px-2.5 py-1 rounded-full text-xs font-semibold <?php echo $stColor; ?>"><?php echo sanitize($r['status']); ?></span></td>
                    <td class="px-6 py-4 text-xs text-hemo-charcoal"><?php echo date('M d, Y',strtotime($r['created_at'])); ?></td>
                    <td class="px-6 py-4 text-xs text-hemo-charcoal"><?php echo sanitize(($r['requester_first']??'').' '.($r['requester_last']??'')); ?></td>
                    <td class="px-6 py-4 text-right whitespace-nowrap space-x-1">
                        <button onclick="viewRequest(<?php echo $r['request_id']; ?>)" class="p-2 rounded-lg bg-hemo-light-gray text-hemo-charcoal hover:bg-blue-50 hover:text-blue-600 transition-fast" title="View"><i class="fas fa-eye text-sm"></i></button>
                        
                        <?php if(($r['collection_status'] ?? '') === 'Ready for Pickup'): ?>
                            <button onclick="openVerifyPinModal(<?php echo $r['request_id']; ?>, '<?php echo sanitize($r['hospital_name'] ?? $hName); ?>')" class="p-2 rounded-lg bg-amber-100 text-amber-800 hover:bg-amber-200 transition-fast font-bold text-xs" title="Verify Runner Release PIN">
                                <i class="fas fa-key text-sm mr-1"></i> Verify PIN
                            </button>
                        <?php elseif($r['status']!=='Fulfilled' && $r['status']!=='Dispatched' && $r['status']!=='Cancelled' && $r['status']!=='Rejected'): ?>
                            <form method="POST" action="<?php echo BASE_URL; ?>/index.php?page=admin_mark_ready_pickup" class="inline" title="Mark Ready for Pickup (Generates 6-Digit PIN)">
                                <input type="hidden" name="request_id" value="<?php echo $r['request_id']; ?>">
                                <input type="hidden" name="hospital_id" value="<?php echo $hId; ?>">
                                <button type="submit" class="p-2 rounded-lg bg-hemo-light-gray text-hemo-charcoal hover:bg-amber-50 hover:text-amber-700 transition-fast" title="Mark Ready for Collection">
                                    <i class="fas fa-box-archive text-sm"></i>
                                </button>
                            </form>
                            <form method="POST" action="<?php echo BASE_URL; ?>/index.php?page=admin_fulfill_request" class="inline">
                                <input type="hidden" name="request_id" value="<?php echo $r['request_id']; ?>">
                                <input type="hidden" name="hospital_id" value="<?php echo $hId; ?>">
                                <button type="submit" class="p-2 rounded-lg bg-hemo-light-gray text-hemo-charcoal hover:bg-green-50 hover:text-hemo-success transition-fast" title="Quick Direct Fulfill">
                                    <i class="fas fa-check text-sm"></i>
                                </button>
                            </form>
                        <?php endif; ?>

                        <button onclick="openDeleteRequestModal(<?php echo $r['request_id']; ?>)" class="p-2 rounded-lg bg-hemo-light-gray text-hemo-charcoal hover:bg-red-50 hover:text-hemo-warning transition-fast" title="Delete"><i class="fas fa-trash text-sm"></i></button>
                    </td>
                </tr>
            <?php endforeach; endif; ?>
            </tbody>
        </table>
    </div>
</div>

<!-- View Request Modal -->
<div id="viewRequestModal" class="fixed inset-0 bg-hemo-navy/50 backdrop-blur-sm z-[1001] hidden flex items-center justify-center p-4 opacity-0 transition-opacity duration-300">
    <div class="bg-white rounded-xl shadow-2xl w-full max-w-lg flex flex-col transform scale-95 transition-transform duration-300">
        <div class="px-6 py-4 border-b border-hemo-border flex items-center justify-between bg-hemo-off-white rounded-t-xl">
            <h3 class="text-lg font-semibold text-hemo-navy"><i class="fas fa-clipboard-list text-hemo-red mr-2"></i>Request Details</h3>
            <button onclick="closeModal('viewRequestModal')" class="text-hemo-gray hover:text-hemo-red transition-fast"><i class="fas fa-times text-lg"></i></button>
        </div>
        <div class="p-6">
            <div id="reqLoader" class="flex justify-center py-8"><i class="fas fa-spinner fa-spin text-3xl text-hemo-red"></i></div>
            <div id="reqContent" class="hidden"></div>
        </div>
    </div>
</div>

<!-- Delete Request Modal -->
<div id="deleteRequestModal" class="fixed inset-0 bg-hemo-navy/50 backdrop-blur-sm z-[1001] hidden flex items-center justify-center p-4 opacity-0 transition-opacity duration-300">
    <div class="bg-white rounded-xl shadow-2xl w-full max-w-sm flex flex-col transform scale-95 transition-transform duration-300">
        <div class="p-6 text-center">
            <div class="w-16 h-16 rounded-full bg-red-100 flex items-center justify-center mx-auto mb-4"><i class="fas fa-exclamation-triangle text-2xl text-hemo-warning"></i></div>
            <h3 class="text-xl font-bold text-hemo-navy mb-2">Delete Request?</h3>
            <p class="text-sm text-hemo-charcoal mb-6">This will permanently delete this blood request.</p>
            <form action="<?php echo BASE_URL; ?>/index.php?page=admin_delete_hospital_request" method="POST" class="flex gap-3">
                <input type="hidden" name="request_id" id="del_req_id">
                <input type="hidden" name="hospital_id" value="<?php echo $hId; ?>">
                <button type="button" onclick="closeModal('deleteRequestModal')" class="flex-1 py-2.5 rounded-lg bg-hemo-light-gray text-hemo-charcoal font-semibold hover:bg-gray-200 transition-fast">Cancel</button>
                <button type="submit" class="flex-1 py-2.5 rounded-lg bg-hemo-warning text-white font-semibold hover:bg-red-700 transition-fast">Delete</button>
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
                Ask the hospital ambulance driver or lab runner for the <strong>6-digit Release PIN</strong> generated on their portal.
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
function openDeleteRequestModal(id){document.getElementById('del_req_id').value=id;openModal('deleteRequestModal');}

function openVerifyPinModal(requestId, hospitalName) {
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

function copyPinToClipboard(pin, btnId = 'btnCopyPinAdmin') {
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

const urgencyLabels={'Normal':['Low','text-blue-700'],'Urgent':['High','text-amber-700'],'Emergency':['Critical','text-hemo-warning']};
const statusLabels={'Pending':'bg-amber-100 text-amber-700','Processing':'bg-blue-100 text-blue-700','Dispatched':'bg-indigo-100 text-indigo-700','Fulfilled':'bg-green-100 text-hemo-success','Partially Fulfilled':'bg-indigo-100 text-indigo-700','Rejected':'bg-red-100 text-hemo-warning','Cancelled':'bg-gray-100 text-gray-600'};

async function viewRequest(id){
    openModal('viewRequestModal');
    document.getElementById('reqLoader').classList.remove('hidden');
    document.getElementById('reqContent').classList.add('hidden');
    try{
        const res=await fetch(`<?php echo BASE_URL; ?>/index.php?page=admin_get_request_json&id=${id}`);
        const data=await res.json();
        if(data.success && data.data){
            const r=data.data;
            const urg=urgencyLabels[r.urgency]||['Medium','text-gray-700'];
            const sc=statusLabels[r.status]||'bg-gray-100 text-gray-600';
            
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
                            <button type="button" id="btnCopyPinAdmin" onclick="copyPinToClipboard('${r.release_pin}', 'btnCopyPinAdmin')"
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
                        <button onclick="closeModal('viewRequestModal'); openVerifyPinModal(${r.request_id}, '${r.hospital_name || ''}')"
                                class="w-full py-2.5 bg-amber-600 hover:bg-amber-700 text-white font-bold text-xs rounded-xl shadow-md transition-fast flex items-center justify-center gap-2">
                            <i class="fas fa-key"></i> Verify Runner Release PIN & Dispatch Units
                        </button>
                    </div>
                `;
            }

            document.getElementById('reqContent').innerHTML=`
                <div class="space-y-4">
                    <div class="flex items-center justify-between pb-3 border-b border-hemo-border">
                        <div class="flex items-center gap-3">
                            <span class="text-sm font-mono font-bold text-hemo-red bg-hemo-light-red px-2.5 py-1 rounded">R-${String(r.request_id).padStart(4,'0')}</span>
                            <span class="inline-flex items-center px-2.5 py-1 rounded-full text-xs font-semibold ${sc}">${r.status}</span>
                        </div>
                        <span class="text-xs ${urg[1]} font-semibold">${urg[0]} Urgency</span>
                    </div>

                    ${stepperHtml}
                    ${pinCardHtml}
                    ${driverInfoHtml}
                    ${actionButtonsHtml}

                    <div class="grid grid-cols-2 gap-4 p-4 rounded-xl bg-hemo-off-white border border-hemo-border text-xs">
                        <div><span class="text-hemo-gray block">Blood Type</span><span class="inline-flex items-center gap-1 px-2 py-0.5 rounded-lg text-xs font-bold bg-hemo-red text-white"><i class="fas fa-droplet text-[9px]"></i> ${r.blood_type||'N/A'}</span></div>
                        <div><span class="text-hemo-gray block">Units</span><span class="text-sm font-bold text-hemo-navy">${r.units_fulfilled||0} / ${r.units_requested} units</span></div>
                        <div><span class="text-hemo-gray block">Hospital</span><span class="font-semibold text-hemo-navy">${r.hospital_name||'N/A'}</span></div>
                        <div><span class="text-hemo-gray block">Requested On</span><span class="font-semibold text-hemo-navy">${r.created_at?new Date(r.created_at).toLocaleDateString('en-US',{month:'short',day:'numeric',year:'numeric'}):'N/A'}</span></div>
                    </div>

                    ${r.requester_first?`<div class="bg-hemo-light-gray rounded-xl p-3 text-xs">
                        <p class="text-hemo-gray font-bold uppercase mb-1">Requested By</p>
                        <p class="font-semibold text-hemo-navy">${r.requester_first} ${r.requester_last||''}</p>
                        <p class="text-hemo-charcoal mt-0.5"><i class="fas fa-envelope text-hemo-gray mr-1"></i>${r.requester_email||'N/A'} &nbsp;|&nbsp; <i class="fas fa-phone text-hemo-gray mr-1"></i>${r.requester_phone||'N/A'}</p>
                    </div>`:''}
                </div>`;
        }
    }catch(e){document.getElementById('reqContent').innerHTML='<p class="text-center text-hemo-warning">Failed to load request.</p>';}
    document.getElementById('reqLoader').classList.add('hidden');
    document.getElementById('reqContent').classList.remove('hidden');
}
</script>

<?php
$content = ob_get_clean();
require_once __DIR__ . '/../layouts/dashboard_layout.php';
?>
