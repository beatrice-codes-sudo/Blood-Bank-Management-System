<?php
/**
 * Hospital Manager: SaaS Subscription Plans View
 */
$pageTitle = 'SaaS Subscription Plans';
$hospital = $hospital ?? [];
$plans = $plans ?? (defined('PLANS') ? PLANS : []);
$payments = $payments ?? [];

$subStatus = $hospital['subscription_status'] ?? 'Trial';
$currentPlan = $hospital['subscription_plan'] ?? 'Starter';
$expiresAt = $hospital['subscription_expires_at'] ?? null;
$billingInterval = $hospital['billing_interval'] ?? 'Monthly';

ob_start();
?>

<!-- Include Paystack Inline JS -->
<script src="https://js.paystack.co/v1/inline.js"></script>

<!-- Header & Current Plan Status Banner -->
<div class="mb-8">
    <div class="flex flex-col md:flex-row md:items-center md:justify-between gap-4">
        <div>
            <div class="flex items-center gap-2">
                <span class="px-3 py-1 rounded-full text-[11px] font-bold uppercase tracking-wider bg-hemo-light-red text-hemo-red">
                    <i class="fas fa-shield-halved mr-1"></i> B2B Healthcare SaaS
                </span>
                <?php if ($subStatus === 'Active'): ?>
                    <span class="px-3 py-1 rounded-full text-[11px] font-bold uppercase tracking-wider bg-green-100 text-hemo-success">
                        <i class="fas fa-check-circle mr-1"></i> Subscription Active
                    </span>
                <?php elseif ($subStatus === 'Trial'): ?>
                    <span class="px-3 py-1 rounded-full text-[11px] font-bold uppercase tracking-wider bg-blue-100 text-blue-700">
                        <i class="fas fa-hourglass-half mr-1"></i> Trial Period
                    </span>
                <?php else: ?>
                    <span class="px-3 py-1 rounded-full text-[11px] font-bold uppercase tracking-wider bg-red-100 text-hemo-warning">
                        <i class="fas fa-exclamation-triangle mr-1"></i> <?php echo sanitize($subStatus); ?>
                    </span>
                <?php endif; ?>
            </div>
            <h1 class="font-display text-3xl font-bold text-hemo-navy mt-2">Hospital Subscription Plans</h1>
            <p class="text-sm text-hemo-charcoal mt-1">Unlock real-time central stock visibility, emergency fast-track queues, and secure Click & Collect digital release PINs.</p>
        </div>

        <!-- Current Status Pill -->
        <div class="bg-white p-4 rounded-xl shadow-card border border-hemo-border flex items-center gap-4">
            <div class="w-12 h-12 rounded-xl bg-amber-50 flex items-center justify-center text-hemo-gold text-2xl">
                <i class="fas fa-crown"></i>
            </div>
            <div>
                <p class="text-xs text-hemo-gray uppercase font-bold tracking-wider">Current Plan</p>
                <p class="text-base font-bold text-hemo-navy"><?php echo sanitize($currentPlan); ?> (<?php echo sanitize($subStatus); ?>)</p>
                <?php if ($expiresAt): ?>
                    <p class="text-[11px] text-hemo-gray">Renews/Expires: <span class="font-semibold text-hemo-navy"><?php echo date('M d, Y', strtotime($expiresAt)); ?></span></p>
                <?php endif; ?>
            </div>
        </div>
    </div>
</div>

<!-- Billing Cycle Toggle (Monthly vs Annual) -->
<div class="flex items-center justify-center mb-10">
    <div class="bg-gray-100 p-1 rounded-xl flex items-center gap-1 border border-hemo-border">
        <button type="button" id="btnMonthly" onclick="toggleBilling('Monthly')" 
                class="px-5 py-2 rounded-lg text-xs font-bold transition-fast bg-white text-hemo-navy shadow-sm">
            Monthly Billing
        </button>
        <button type="button" id="btnAnnual" onclick="toggleBilling('Annual')" 
                class="px-5 py-2 rounded-lg text-xs font-bold transition-fast text-hemo-gray hover:text-hemo-navy flex items-center gap-1.5">
            Annual Billing
            <span class="px-2 py-0.5 rounded-full text-[10px] font-bold bg-green-100 text-hemo-success uppercase tracking-tight">Save 15%</span>
        </button>
    </div>
</div>

<!-- Pricing Cards Grid -->
<div class="grid grid-cols-1 md:grid-cols-3 gap-8 mb-12">
    <?php foreach ($plans as $planKey => $plan): 
        $isPro = ($planKey === 'Professional');
        $isCurrent = ($currentPlan === $planKey && $subStatus === 'Active');
    ?>
    <div class="bg-white rounded-2xl p-7 flex flex-col justify-between transition-default relative <?php echo $isPro ? 'border-2 border-hemo-red shadow-xl ring-4 ring-hemo-red/5' : 'border border-hemo-border shadow-card'; ?>">
        <?php if ($isPro): ?>
            <div class="absolute -top-3.5 left-1/2 -translate-x-1/2 bg-gradient-to-r from-hemo-red to-hemo-deep-red text-white text-[11px] font-bold px-4 py-1 rounded-full uppercase tracking-wider shadow-md">
                <i class="fas fa-star mr-1 text-hemo-gold"></i> Recommended for Hospitals
            </div>
        <?php endif; ?>

        <div>
            <!-- Header -->
            <div class="flex items-center justify-between mb-2">
                <h3 class="font-display text-2xl font-bold text-hemo-navy"><?php echo sanitize($plan['name']); ?></h3>
                <?php if ($isCurrent): ?>
                    <span class="px-2.5 py-0.5 rounded-full text-[10px] font-bold bg-green-100 text-hemo-success uppercase">Active</span>
                <?php endif; ?>
            </div>
            <p class="text-xs text-hemo-gray mb-6"><?php echo sanitize($plan['desc']); ?></p>

            <!-- Price Display -->
            <div class="mb-6 p-4 rounded-xl bg-hemo-off-white border border-hemo-border">
                <div class="price-monthly flex items-baseline gap-1">
                    <span class="text-xs text-hemo-gray font-semibold">KES</span>
                    <span class="text-3xl font-extrabold text-hemo-navy"><?php echo number_format($plan['monthly']); ?></span>
                    <span class="text-xs text-hemo-gray font-medium">/ month</span>
                </div>
                <div class="price-annual hidden flex items-baseline gap-1">
                    <span class="text-xs text-hemo-gray font-semibold">KES</span>
                    <span class="text-3xl font-extrabold text-hemo-navy"><?php echo number_format($plan['annual']); ?></span>
                    <span class="text-xs text-hemo-gray font-medium">/ year</span>
                </div>
                <p class="text-[11px] text-hemo-gray mt-1 price-annual hidden">Billed annually (equivalent to KES <?php echo number_format($plan['annual'] / 12); ?>/mo)</p>
            </div>

            <!-- Features Checklist -->
            <ul class="space-y-3.5 text-xs text-hemo-charcoal mb-8">
                <li class="flex items-start gap-2.5">
                    <i class="fas fa-check-circle text-hemo-success mt-0.5 text-sm"></i>
                    <span><strong>Live Blood Inventory:</strong> Real-time central bank availability</span>
                </li>
                <li class="flex items-start gap-2.5">
                    <i class="fas fa-check-circle text-hemo-success mt-0.5 text-sm"></i>
                    <span><strong>Click & Collect:</strong> 6-Digit Dynamic Digital Release PIN</span>
                </li>
                <li class="flex items-start gap-2.5">
                    <i class="fas fa-check-circle text-hemo-success mt-0.5 text-sm"></i>
                    <span><strong>Requisitions:</strong> <?php echo $plan['max_req']; ?> requests per month</span>
                </li>
                <li class="flex items-start gap-2.5">
                    <i class="fas <?php echo $plan['emergency'] ? 'fa-check-circle text-hemo-success' : 'fa-times-circle text-gray-300'; ?> mt-0.5 text-sm"></i>
                    <span class="<?php echo $plan['emergency'] ? 'font-semibold text-hemo-navy' : 'text-hemo-gray line-through'; ?>">
                        Emergency Fast-Track Triage Queue
                    </span>
                </li>
                <li class="flex items-start gap-2.5">
                    <i class="fas fa-check-circle text-hemo-success mt-0.5 text-sm"></i>
                    <span><strong>Audit Logs:</strong> Transfusion & compliance records export</span>
                </li>
            </ul>
        </div>

        <!-- Action Button -->
        <div>
            <button onclick="initiateSubscription('<?php echo $planKey; ?>', <?php echo $plan['monthly']; ?>, <?php echo $plan['annual']; ?>)"
                    class="w-full py-3.5 rounded-xl font-bold text-xs tracking-wider uppercase transition-all shadow-sm btn-press flex items-center justify-center gap-2 <?php echo $isPro ? 'bg-hemo-red hover:bg-hemo-deep-red text-white shadow-btn-primary' : 'bg-hemo-navy hover:bg-slate-800 text-white'; ?>">
                <i class="fas fa-credit-card"></i>
                <span><?php echo $isCurrent ? 'Renew / Extend Plan' : 'Subscribe with Paystack'; ?></span>
            </button>
            <p class="text-center text-[10px] text-hemo-gray mt-2 flex items-center justify-center gap-2">
                <i class="fas fa-lock text-[9px]"></i> Supports M-Pesa & Visa / Mastercard
            </p>
        </div>
    </div>
    <?php endforeach; ?>
</div>

<!-- Transaction History Table -->
<div class="bg-white rounded-2xl shadow-card overflow-hidden border border-hemo-border">
    <div class="p-5 border-b border-hemo-border flex items-center justify-between">
        <div>
            <h2 class="font-display text-lg font-bold text-hemo-navy">Subscription & Payment History</h2>
            <p class="text-xs text-hemo-gray">Verified transaction receipts processed via Paystack</p>
        </div>
    </div>

    <div class="overflow-x-auto">
        <table class="w-full text-left border-collapse text-xs">
            <thead>
                <tr class="bg-hemo-off-white border-b border-hemo-border text-hemo-gray font-semibold uppercase tracking-wider text-[11px]">
                    <th class="py-3 px-4">Date</th>
                    <th class="py-3 px-4">Reference</th>
                    <th class="py-3 px-4">Plan</th>
                    <th class="py-3 px-4">Interval</th>
                    <th class="py-3 px-4">Amount</th>
                    <th class="py-3 px-4">Channel</th>
                    <th class="py-3 px-4">Status</th>
                </tr>
            </thead>
            <tbody class="divide-y divide-hemo-border">
                <?php if (empty($payments)): ?>
                    <tr>
                        <td colspan="7" class="py-8 text-center text-hemo-gray">
                            <i class="fas fa-receipt text-3xl mb-2 text-gray-300 block"></i>
                            No subscription payment records found yet.
                        </td>
                    </tr>
                <?php else: ?>
                    <?php foreach ($payments as $p): ?>
                        <tr class="hover:bg-hemo-off-white transition-fast">
                            <td class="py-3 px-4 font-medium text-hemo-navy"><?php echo date('M d, Y H:i', strtotime($p['created_at'])); ?></td>
                            <td class="py-3 px-4 font-mono text-hemo-charcoal"><?php echo sanitize($p['reference']); ?></td>
                            <td class="py-3 px-4 font-semibold text-hemo-navy"><?php echo sanitize($p['plan_name']); ?></td>
                            <td class="py-3 px-4"><?php echo sanitize($p['billing_interval']); ?></td>
                            <td class="py-3 px-4 font-bold text-hemo-navy">KES <?php echo number_format($p['amount'], 2); ?></td>
                            <td class="py-3 px-4 uppercase text-[11px]"><?php echo sanitize($p['channel'] ?? 'M-Pesa/Card'); ?></td>
                            <td class="py-3 px-4">
                                <span class="px-2 py-0.5 rounded-full text-[10px] font-bold <?php echo $p['status'] === 'Success' ? 'bg-green-100 text-hemo-success' : 'bg-red-100 text-hemo-warning'; ?>">
                                    <?php echo sanitize($p['status']); ?>
                                </span>
                            </td>
                        </tr>
                    <?php endforeach; ?>
                <?php endif; ?>
            </tbody>
        </table>
    </div>
</div>

<!-- Paystack Popup Script & Modal Logic -->
<script>
let currentInterval = 'Monthly';

function toggleBilling(interval) {
    currentInterval = interval;
    const btnMonthly = document.getElementById('btnMonthly');
    const btnAnnual = document.getElementById('btnAnnual');
    const monthlyPrices = document.querySelectorAll('.price-monthly');
    const annualPrices = document.querySelectorAll('.price-annual');

    if (interval === 'Annual') {
        btnAnnual.classList.add('bg-white', 'text-hemo-navy', 'shadow-sm');
        btnAnnual.classList.remove('text-hemo-gray');
        btnMonthly.classList.remove('bg-white', 'text-hemo-navy', 'shadow-sm');
        btnMonthly.classList.add('text-hemo-gray');

        monthlyPrices.forEach(el => el.classList.add('hidden'));
        annualPrices.forEach(el => el.classList.remove('hidden'));
    } else {
        btnMonthly.classList.add('bg-white', 'text-hemo-navy', 'shadow-sm');
        btnMonthly.classList.remove('text-hemo-gray');
        btnAnnual.classList.remove('bg-white', 'text-hemo-navy', 'shadow-sm');
        btnAnnual.classList.add('text-hemo-gray');

        annualPrices.forEach(el => el.classList.add('hidden'));
        monthlyPrices.forEach(el => el.classList.remove('hidden'));
    }
}

function initiateSubscription(planKey, monthlyPrice, annualPrice) {
    const amount = (currentInterval === 'Annual') ? annualPrice : monthlyPrice;
    const userEmail = "<?php echo sanitize($_SESSION['email'] ?? 'hospital@hemolink.com'); ?>";
    const hospitalName = "<?php echo sanitize($hospital['hospital_name'] ?? 'Hospital'); ?>";
    const hospitalId = "<?php echo (int)($hospital['hospital_id'] ?? 0); ?>";
    const publicKey = "<?php echo defined('PAYSTACK_PUBLIC_KEY') ? PAYSTACK_PUBLIC_KEY : ''; ?>";

    if (!publicKey || publicKey.indexOf('pk_') !== 0) {
        alert('Paystack Public Key is not configured. Please add PAYSTACK_PUBLIC_KEY to your .env file.');
        return;
    }

    const handler = PaystackPop.setup({
        key: publicKey,
        email: userEmail,
        amount: Math.round(amount * 100), // Amount in cents / subunits
        currency: '<?php echo defined('PAYSTACK_CURRENCY') ? PAYSTACK_CURRENCY : 'KES'; ?>',
        ref: 'HL-SUB-' + planKey.substring(0, 3).toUpperCase() + '-' + Date.now(),
        metadata: {
            custom_fields: [
                { display_name: "Hospital Name", variable_name: "hospital_name", value: hospitalName },
                { display_name: "Hospital ID", variable_name: "hospital_id", value: hospitalId },
                { display_name: "Plan Name", variable_name: "plan_name", value: planKey },
                { display_name: "Billing Interval", variable_name: "billing_interval", value: currentInterval }
            ]
        },
        callback: function(response) {
            // Verify reference server-side with PHP
            fetch('<?php echo BASE_URL; ?>/index.php?page=hospital_verify_subscription', {
                method: 'POST',
                headers: { 'Content-Type': 'application/x-www-form-urlencoded' },
                body: new URLSearchParams({
                    reference: response.reference,
                    plan_name: planKey,
                    interval: currentInterval
                })
            })
            .then(res => res.json())
            .then(data => {
                if (data.success) {
                    alert('🎉 Subscription activated successfully! Your hospital account is now active.');
                    window.location.reload();
                } else {
                    alert('Verification error: ' + data.message);
                }
            })
            .catch(err => {
                console.error('Payment verification error:', err);
                alert('A network error occurred while verifying the payment. Please contact support.');
            });
        },
        onClose: function() {
            console.log('Paystack checkout popup closed.');
        }
    });

    handler.openIframe();
}
</script>

<?php
$content = ob_get_clean();
require_once __DIR__ . '/../layouts/hospital_layout.php';
?>
