<?php
/**
 * Donor Appointments Page
 * Interactive calendar with booking, reschedule, and cancel
 */
$pageTitle = 'My Appointments';
$donor = $data['donor'] ?? null;
$appointments = $data['appointments'] ?? [];
$stats = $data['stats'] ?? ['total' => 0, 'scheduled' => 0, 'completed' => 0, 'cancelled' => 0, 'no_show' => 0];
$hospitals = $data['hospitals'] ?? [];
$flash = getFlashMessage();

ob_start();
?>

<!-- Flash Message -->
<?php if ($flash): ?>
<div id="flash-msg" class="mb-6 px-5 py-4 rounded-xl text-sm font-semibold flex items-center gap-3
    <?php echo $flash['type'] === 'success' ? 'bg-green-50 text-hemo-success border border-green-200' : 'bg-red-50 text-hemo-warning border border-red-200'; ?>">
    <i class="fas <?php echo $flash['type'] === 'success' ? 'fa-check-circle' : 'fa-exclamation-circle'; ?>"></i>
    <?php echo sanitize($flash['message']); ?>
    <button onclick="document.getElementById('flash-msg').remove()" class="ml-auto text-lg leading-none opacity-60 hover:opacity-100">&times;</button>
</div>
<?php endif; ?>

<!-- Page Header -->
<div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4 mb-8">
    <div>
        <h1 class="font-display text-[32px] font-bold text-hemo-navy">My Appointments</h1>
        <p class="text-hemo-charcoal mt-1">Schedule and manage your donation appointments.</p>
    </div>
    <button onclick="openBookModal()" class="inline-flex items-center gap-2 px-5 py-2.5 rounded-lg bg-hemo-red text-white font-semibold text-sm shadow-btn-primary hover:bg-hemo-deep-red hover:shadow-btn-hover hover:-translate-y-px transition-default btn-press">
        <i class="fas fa-calendar-plus"></i> Book Appointment
    </button>
</div>

<!-- Stats -->
<div class="grid grid-cols-2 sm:grid-cols-4 gap-4 mb-8">
    <div class="stat-card bg-white rounded-xl shadow-card p-5 transition-default">
        <div class="w-10 h-10 rounded-xl bg-hemo-light-red flex items-center justify-center mb-3">
            <i class="fas fa-calendar text-hemo-red"></i>
        </div>
        <p class="text-2xl font-bold text-hemo-navy"><?php echo $stats['total']; ?></p>
        <p class="text-xs text-hemo-gray mt-1">Total</p>
    </div>
    <div class="stat-card bg-white rounded-xl shadow-card p-5 transition-default">
        <div class="w-10 h-10 rounded-xl bg-blue-50 flex items-center justify-center mb-3">
            <i class="fas fa-clock text-hemo-info"></i>
        </div>
        <p class="text-2xl font-bold text-hemo-navy"><?php echo $stats['scheduled']; ?></p>
        <p class="text-xs text-hemo-gray mt-1">Scheduled</p>
    </div>
    <div class="stat-card bg-white rounded-xl shadow-card p-5 transition-default">
        <div class="w-10 h-10 rounded-xl bg-green-50 flex items-center justify-center mb-3">
            <i class="fas fa-check-circle text-hemo-success"></i>
        </div>
        <p class="text-2xl font-bold text-hemo-navy"><?php echo $stats['completed']; ?></p>
        <p class="text-xs text-hemo-gray mt-1">Completed</p>
    </div>
    <div class="stat-card bg-white rounded-xl shadow-card p-5 transition-default">
        <div class="w-10 h-10 rounded-xl bg-red-50 flex items-center justify-center mb-3">
            <i class="fas fa-times-circle text-hemo-warning"></i>
        </div>
        <p class="text-2xl font-bold text-hemo-navy"><?php echo $stats['cancelled']; ?></p>
        <p class="text-xs text-hemo-gray mt-1">Cancelled</p>
    </div>
</div>

<!-- Calendar + Appointments Grid -->
<div class="grid grid-cols-1 lg:grid-cols-5 gap-6 mb-8">

    <!-- Calendar Card -->
    <div class="lg:col-span-2 bg-white rounded-xl shadow-card p-6">
        <div class="flex items-center justify-between mb-5">
            <h3 class="text-lg font-semibold text-hemo-navy" id="cal-title"></h3>
            <div class="flex items-center gap-1">
                <button onclick="calNav(-1)" class="w-8 h-8 rounded-lg flex items-center justify-center hover:bg-hemo-light-red text-hemo-charcoal hover:text-hemo-red transition-fast"><i class="fas fa-chevron-left text-xs"></i></button>
                <button onclick="calNav(0)" class="px-3 py-1 rounded-lg text-xs font-semibold hover:bg-hemo-light-red text-hemo-charcoal hover:text-hemo-red transition-fast">Today</button>
                <button onclick="calNav(1)" class="w-8 h-8 rounded-lg flex items-center justify-center hover:bg-hemo-light-red text-hemo-charcoal hover:text-hemo-red transition-fast"><i class="fas fa-chevron-right text-xs"></i></button>
            </div>
        </div>
        <div class="grid grid-cols-7 gap-0 text-center text-xs font-semibold text-hemo-gray uppercase tracking-wider mb-2">
            <div class="py-2">Sun</div><div class="py-2">Mon</div><div class="py-2">Tue</div><div class="py-2">Wed</div><div class="py-2">Thu</div><div class="py-2">Fri</div><div class="py-2">Sat</div>
        </div>
        <div id="cal-grid" class="grid grid-cols-7 gap-0"></div>
    </div>

    <!-- Upcoming Appointments -->
    <div class="lg:col-span-3 bg-white rounded-xl shadow-card overflow-hidden">
        <div class="px-6 py-4 border-b border-hemo-border flex items-center gap-3">
            <i class="fas fa-list-check text-hemo-red"></i>
            <h3 class="text-lg font-semibold text-hemo-navy">All Appointments</h3>
            <span class="ml-auto text-xs font-semibold text-hemo-gray bg-hemo-light-gray px-3 py-1 rounded-full"><?php echo count($appointments); ?></span>
        </div>

        <?php if (empty($appointments)): ?>
        <div class="p-10 text-center">
            <div class="w-16 h-16 rounded-2xl bg-hemo-light-red flex items-center justify-center mx-auto mb-4">
                <i class="fas fa-calendar-xmark text-hemo-red text-2xl"></i>
            </div>
            <p class="text-hemo-charcoal font-medium">No appointments yet</p>
            <p class="text-sm text-hemo-gray mt-1">Click "Book Appointment" to schedule your first one.</p>
        </div>
        <?php else: ?>
        <div class="divide-y divide-hemo-border max-h-[500px] overflow-y-auto">
            <?php foreach ($appointments as $appt): ?>
            <?php
                $statusColors = [
                    'Scheduled' => 'bg-blue-50 text-blue-700',
                    'Completed' => 'bg-green-50 text-hemo-success',
                    'Cancelled' => 'bg-red-50 text-hemo-warning',
                    'No Show'   => 'bg-amber-50 text-amber-700',
                ];
                $sc = $statusColors[$appt['status']] ?? 'bg-gray-50 text-gray-600';
            ?>
            <div class="px-6 py-4 hover:bg-hemo-hover transition-fast">
                <div class="flex items-start justify-between gap-4">
                    <div class="flex items-start gap-4 min-w-0">
                        <div class="w-11 h-11 rounded-xl bg-hemo-light-red flex flex-col items-center justify-center flex-shrink-0">
                            <span class="text-[10px] font-bold text-hemo-red leading-none"><?php echo date('M', strtotime($appt['appointment_date'])); ?></span>
                            <span class="text-sm font-bold text-hemo-red leading-tight"><?php echo date('d', strtotime($appt['appointment_date'])); ?></span>
                        </div>
                        <div class="min-w-0">
                            <p class="text-sm font-semibold text-hemo-navy truncate">
                                <?php echo sanitize($appt['hospital_name'] ?? 'No hospital'); ?>
                            </p>
                            <p class="text-xs text-hemo-gray mt-0.5">
                                <i class="fas fa-clock mr-1"></i><?php echo date('g:i A', strtotime($appt['appointment_time'])); ?>
                                &middot; <?php echo sanitize($appt['purpose']); ?>
                            </p>
                            <?php if (!empty($appt['notes'])): ?>
                            <p class="text-xs text-hemo-gray mt-1 truncate"><i class="fas fa-sticky-note mr-1"></i><?php echo sanitize($appt['notes']); ?></p>
                            <?php endif; ?>
                        </div>
                    </div>
                    <div class="flex items-center gap-2 flex-shrink-0">
                        <span class="text-[11px] font-semibold px-2.5 py-1 rounded-full <?php echo $sc; ?>"><?php echo $appt['status']; ?></span>
                        <?php if ($appt['status'] === 'Scheduled'): ?>
                        <button onclick='openRescheduleModal(<?php echo json_encode($appt); ?>)' class="w-8 h-8 rounded-lg flex items-center justify-center text-hemo-gray hover:text-hemo-info hover:bg-blue-50 transition-fast" title="Reschedule">
                            <i class="fas fa-pen-to-square text-xs"></i>
                        </button>
                        <form method="POST" action="<?php echo BASE_URL; ?>/index.php?page=donor_appointment_cancel" onsubmit="return confirm('Cancel this appointment?')" class="inline">
                            <input type="hidden" name="appointment_id" value="<?php echo $appt['appointment_id']; ?>">
                            <button type="submit" class="w-8 h-8 rounded-lg flex items-center justify-center text-hemo-gray hover:text-hemo-warning hover:bg-red-50 transition-fast" title="Cancel">
                                <i class="fas fa-xmark text-xs"></i>
                            </button>
                        </form>
                        <?php endif; ?>
                    </div>
                </div>
            </div>
            <?php endforeach; ?>
        </div>
        <?php endif; ?>
    </div>
</div>

<!-- ==================== BOOK MODAL ==================== -->
<div id="book-modal" class="fixed inset-0 z-[1000] hidden">
    <div class="absolute inset-0 bg-black/50 backdrop-blur-sm" onclick="closeModals()"></div>
    <div class="absolute inset-0 flex items-center justify-center p-4">
        <div class="bg-white rounded-xl shadow-modal w-full max-w-lg max-h-[90vh] overflow-hidden relative">
            <div class="px-6 py-4 bg-hemo-light-gray border-b border-hemo-border flex items-center justify-between">
                <h3 class="text-lg font-semibold text-hemo-navy"><i class="fas fa-calendar-plus text-hemo-red mr-2"></i>Book Appointment</h3>
                <button onclick="closeModals()" class="w-8 h-8 rounded-lg flex items-center justify-center text-hemo-gray hover:text-hemo-red hover:bg-hemo-light-red transition-fast">&times;</button>
            </div>
            <form method="POST" action="<?php echo BASE_URL; ?>/index.php?page=donor_appointment_add" class="p-6 space-y-5">
                <div>
                    <label class="block text-sm font-semibold text-hemo-navy mb-1.5">Hospital <span class="text-hemo-red">*</span></label>
                    <select name="hospital_id" required class="w-full h-11 px-4 rounded-lg border-2 border-hemo-border text-sm">
                        <option value="">Select a hospital</option>
                        <?php foreach ($hospitals as $h): ?>
                        <option value="<?php echo $h['hospital_id']; ?>"><?php echo sanitize($h['hospital_name']); ?> — <?php echo sanitize($h['city']); ?></option>
                        <?php endforeach; ?>
                    </select>
                </div>
                <div class="grid grid-cols-2 gap-4">
                    <div>
                        <label class="block text-sm font-semibold text-hemo-navy mb-1.5">Date <span class="text-hemo-red">*</span></label>
                        <input type="date" name="appointment_date" id="book-date" required min="<?php echo date('Y-m-d'); ?>" class="w-full h-11 px-4 rounded-lg border-2 border-hemo-border text-sm">
                    </div>
                    <div>
                        <label class="block text-sm font-semibold text-hemo-navy mb-1.5">Time <span class="text-hemo-red">*</span></label>
                        <select name="appointment_time" required class="w-full h-11 px-4 rounded-lg border-2 border-hemo-border text-sm">
                            <option value="">Select time</option>
                            <?php for ($h = 7; $h <= 17; $h++): ?>
                            <?php foreach (['00', '30'] as $m): ?>
                            <?php $t = sprintf('%02d:%s', $h, $m); $label = date('g:i A', strtotime($t)); ?>
                            <option value="<?php echo $t; ?>"><?php echo $label; ?></option>
                            <?php endforeach; endfor; ?>
                        </select>
                    </div>
                </div>
                <div>
                    <label class="block text-sm font-semibold text-hemo-navy mb-1.5">Purpose</label>
                    <select name="purpose" class="w-full h-11 px-4 rounded-lg border-2 border-hemo-border text-sm">
                        <option value="Donation">Donation</option>
                        <option value="Health Check">Health Check</option>
                        <option value="Consultation">Consultation</option>
                    </select>
                </div>
                <div>
                    <label class="block text-sm font-semibold text-hemo-navy mb-1.5">Notes</label>
                    <textarea name="notes" rows="2" class="w-full px-4 py-3 rounded-lg border-2 border-hemo-border text-sm resize-none" placeholder="Optional notes..."></textarea>
                </div>
                <div class="flex justify-end gap-3 pt-2">
                    <button type="button" onclick="closeModals()" class="px-5 py-2.5 rounded-lg border-2 border-hemo-border text-sm font-semibold text-hemo-charcoal hover:bg-hemo-light-gray transition-fast">Cancel</button>
                    <button type="submit" class="px-5 py-2.5 rounded-lg bg-hemo-red text-white text-sm font-semibold shadow-btn-primary hover:bg-hemo-deep-red transition-default btn-press">
                        <i class="fas fa-check mr-1"></i> Book Appointment
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>

<!-- ==================== RESCHEDULE MODAL ==================== -->
<div id="reschedule-modal" class="fixed inset-0 z-[1000] hidden">
    <div class="absolute inset-0 bg-black/50 backdrop-blur-sm" onclick="closeModals()"></div>
    <div class="absolute inset-0 flex items-center justify-center p-4">
        <div class="bg-white rounded-xl shadow-modal w-full max-w-lg max-h-[90vh] overflow-hidden relative">
            <div class="px-6 py-4 bg-hemo-light-gray border-b border-hemo-border flex items-center justify-between">
                <h3 class="text-lg font-semibold text-hemo-navy"><i class="fas fa-pen-to-square text-hemo-info mr-2"></i>Reschedule Appointment</h3>
                <button onclick="closeModals()" class="w-8 h-8 rounded-lg flex items-center justify-center text-hemo-gray hover:text-hemo-red hover:bg-hemo-light-red transition-fast">&times;</button>
            </div>
            <form method="POST" action="<?php echo BASE_URL; ?>/index.php?page=donor_appointment_reschedule" class="p-6 space-y-5">
                <input type="hidden" name="appointment_id" id="rs-id">
                <div>
                    <label class="block text-sm font-semibold text-hemo-navy mb-1.5">Hospital <span class="text-hemo-red">*</span></label>
                    <select name="hospital_id" id="rs-hospital" required class="w-full h-11 px-4 rounded-lg border-2 border-hemo-border text-sm">
                        <option value="">Select a hospital</option>
                        <?php foreach ($hospitals as $h): ?>
                        <option value="<?php echo $h['hospital_id']; ?>"><?php echo sanitize($h['hospital_name']); ?> — <?php echo sanitize($h['city']); ?></option>
                        <?php endforeach; ?>
                    </select>
                </div>
                <div class="grid grid-cols-2 gap-4">
                    <div>
                        <label class="block text-sm font-semibold text-hemo-navy mb-1.5">New Date <span class="text-hemo-red">*</span></label>
                        <input type="date" name="appointment_date" id="rs-date" required min="<?php echo date('Y-m-d'); ?>" class="w-full h-11 px-4 rounded-lg border-2 border-hemo-border text-sm">
                    </div>
                    <div>
                        <label class="block text-sm font-semibold text-hemo-navy mb-1.5">New Time <span class="text-hemo-red">*</span></label>
                        <select name="appointment_time" id="rs-time" required class="w-full h-11 px-4 rounded-lg border-2 border-hemo-border text-sm">
                            <option value="">Select time</option>
                            <?php for ($h = 7; $h <= 17; $h++): ?>
                            <?php foreach (['00', '30'] as $m): ?>
                            <?php $t = sprintf('%02d:%s', $h, $m); $label = date('g:i A', strtotime($t)); ?>
                            <option value="<?php echo $t; ?>"><?php echo $label; ?></option>
                            <?php endforeach; endfor; ?>
                        </select>
                    </div>
                </div>
                <div>
                    <label class="block text-sm font-semibold text-hemo-navy mb-1.5">Purpose</label>
                    <select name="purpose" id="rs-purpose" class="w-full h-11 px-4 rounded-lg border-2 border-hemo-border text-sm">
                        <option value="Donation">Donation</option>
                        <option value="Health Check">Health Check</option>
                        <option value="Consultation">Consultation</option>
                    </select>
                </div>
                <div>
                    <label class="block text-sm font-semibold text-hemo-navy mb-1.5">Notes</label>
                    <textarea name="notes" id="rs-notes" rows="2" class="w-full px-4 py-3 rounded-lg border-2 border-hemo-border text-sm resize-none"></textarea>
                </div>
                <div class="flex justify-end gap-3 pt-2">
                    <button type="button" onclick="closeModals()" class="px-5 py-2.5 rounded-lg border-2 border-hemo-border text-sm font-semibold text-hemo-charcoal hover:bg-hemo-light-gray transition-fast">Cancel</button>
                    <button type="submit" class="px-5 py-2.5 rounded-lg bg-hemo-info text-white text-sm font-semibold hover:bg-blue-700 transition-default btn-press">
                        <i class="fas fa-calendar-check mr-1"></i> Reschedule
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>

<!-- ==================== JAVASCRIPT ==================== -->
<script>
// Appointment data for calendar dots
const apptData = <?php echo json_encode(array_map(function($a) {
    return ['date' => $a['appointment_date'], 'status' => $a['status']];
}, $appointments)); ?>;

let calYear, calMonth;
(function initCal() {
    const now = new Date();
    calYear = now.getFullYear();
    calMonth = now.getMonth();
    renderCal();
})();

function calNav(dir) {
    if (dir === 0) { const n = new Date(); calYear = n.getFullYear(); calMonth = n.getMonth(); }
    else { calMonth += dir; if (calMonth < 0) { calMonth = 11; calYear--; } if (calMonth > 11) { calMonth = 0; calYear++; } }
    renderCal();
}

function renderCal() {
    const months = ['January','February','March','April','May','June','July','August','September','October','November','December'];
    document.getElementById('cal-title').textContent = months[calMonth] + ' ' + calYear;
    const grid = document.getElementById('cal-grid');
    grid.innerHTML = '';
    const first = new Date(calYear, calMonth, 1).getDay();
    const days = new Date(calYear, calMonth + 1, 0).getDate();
    const today = new Date(); today.setHours(0,0,0,0);

    for (let i = 0; i < first; i++) {
        grid.innerHTML += '<div class="h-10"></div>';
    }
    for (let d = 1; d <= days; d++) {
        const dateStr = calYear + '-' + String(calMonth+1).padStart(2,'0') + '-' + String(d).padStart(2,'0');
        const isToday = (new Date(calYear, calMonth, d)).getTime() === today.getTime();
        const appts = apptData.filter(a => a.date === dateStr);
        const hasScheduled = appts.some(a => a.status === 'Scheduled');
        const hasCompleted = appts.some(a => a.status === 'Completed');

        let cls = 'h-10 rounded-lg flex flex-col items-center justify-center text-sm cursor-pointer transition-fast hover:bg-hemo-light-red relative ';
        if (isToday) cls += 'bg-hemo-red text-white font-bold hover:bg-hemo-deep-red ';
        else cls += 'text-hemo-navy ';

        let dots = '';
        if (hasScheduled) dots += '<span class="w-1.5 h-1.5 rounded-full bg-blue-500 inline-block mx-px"></span>';
        if (hasCompleted) dots += '<span class="w-1.5 h-1.5 rounded-full bg-green-500 inline-block mx-px"></span>';

        grid.innerHTML += '<div class="' + cls + '" onclick="selectCalDate(\'' + dateStr + '\')">' + d + (dots ? '<div class="flex absolute bottom-0.5">' + dots + '</div>' : '') + '</div>';
    }
}

function selectCalDate(dateStr) {
    document.getElementById('book-date').value = dateStr;
    openBookModal();
}

function openBookModal() { document.getElementById('book-modal').classList.remove('hidden'); }
function closeModals() {
    document.getElementById('book-modal').classList.add('hidden');
    document.getElementById('reschedule-modal').classList.add('hidden');
}

function openRescheduleModal(appt) {
    document.getElementById('rs-id').value = appt.appointment_id;
    document.getElementById('rs-date').value = appt.appointment_date;
    document.getElementById('rs-notes').value = appt.notes || '';
    // Set time - match HH:MM
    const timeSel = document.getElementById('rs-time');
    const timeVal = appt.appointment_time.substring(0, 5);
    for (let o of timeSel.options) { if (o.value === timeVal) { o.selected = true; break; } }
    // Set hospital
    const hSel = document.getElementById('rs-hospital');
    for (let o of hSel.options) { if (o.value == appt.hospital_id) { o.selected = true; break; } }
    // Set purpose
    const pSel = document.getElementById('rs-purpose');
    for (let o of pSel.options) { if (o.value === appt.purpose) { o.selected = true; break; } }
    document.getElementById('reschedule-modal').classList.remove('hidden');
}

document.addEventListener('keydown', e => { if (e.key === 'Escape') closeModals(); });
</script>

<?php
$content = ob_get_clean();
require_once __DIR__ . '/../layouts/dashboard_layout.php';
?>
