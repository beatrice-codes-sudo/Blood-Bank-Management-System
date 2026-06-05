<?php
/**
 * Donor Profile Page
 * View and edit donor profile information
 */
$pageTitle = 'My Profile';
$donor = $data['donor'] ?? null;
$stats = $data['stats'] ?? ['total_donations' => 0, 'last_donation_date' => null, 'total_volume_ml' => 0];
$bloodTypes = $data['bloodTypes'] ?? [];
$flash = getFlashMessage();
$initials = strtoupper(substr($donor['first_name'] ?? 'U', 0, 1) . substr($donor['last_name'] ?? '', 0, 1));

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
        <h1 class="font-display text-[32px] font-bold text-hemo-navy">My Profile</h1>
        <p class="text-hemo-charcoal mt-1">Manage your personal information.</p>
    </div>
</div>

<!-- Profile Header Card -->
<div class="bg-white rounded-xl shadow-card p-6 sm:p-8 mb-8">
    <div class="flex flex-col sm:flex-row items-start gap-6">
        <!-- Avatar -->
        <div class="w-20 h-20 rounded-full gradient-red flex items-center justify-center text-white font-bold text-2xl flex-shrink-0">
            <?php echo $initials; ?>
        </div>
        <!-- Info -->
        <div class="flex-1 min-w-0">
            <h2 class="text-xl font-bold text-hemo-navy"><?php echo sanitize(($donor['first_name'] ?? '') . ' ' . ($donor['last_name'] ?? '')); ?></h2>
            <p class="text-sm font-mono text-hemo-red mt-1">DONOR-<?php echo str_pad($donor['user_id'] ?? 0, 4, '0', STR_PAD_LEFT); ?></p>
            <div class="flex flex-wrap items-center gap-4 mt-3">
                <?php if (!empty($donor['blood_type'])): ?>
                <span class="inline-flex items-center gap-1.5 px-3 py-1.5 rounded-lg bg-hemo-red text-white text-sm font-bold">
                    <i class="fas fa-droplet text-xs"></i> <?php echo sanitize($donor['blood_type']); ?>
                </span>
                <?php endif; ?>
                <span class="inline-flex items-center gap-1.5 text-sm <?php echo ($donor['eligibility_status'] ?? '') === 'Eligible' ? 'text-hemo-success' : 'text-hemo-warning'; ?>">
                    <i class="fas fa-<?php echo ($donor['eligibility_status'] ?? '') === 'Eligible' ? 'check-circle' : 'exclamation-circle'; ?>"></i>
                    <?php echo sanitize($donor['eligibility_status'] ?? 'Unknown'); ?>
                </span>
                <?php if (!empty($donor['email'])): ?>
                <span class="text-sm text-hemo-charcoal"><i class="fas fa-envelope text-hemo-gray mr-1"></i><?php echo sanitize($donor['email']); ?></span>
                <?php endif; ?>
                <?php if (!empty($donor['phone'])): ?>
                <span class="text-sm text-hemo-charcoal"><i class="fas fa-phone text-hemo-gray mr-1"></i><?php echo sanitize($donor['phone']); ?></span>
                <?php endif; ?>
            </div>
        </div>
        <!-- Quick Stats -->
        <div class="flex gap-4 flex-shrink-0">
            <div class="text-center px-4 py-3 rounded-xl bg-hemo-light-red">
                <p class="text-2xl font-bold text-hemo-red"><?php echo $stats['total_donations']; ?></p>
                <p class="text-[10px] text-hemo-gray font-semibold uppercase tracking-wider">Donations</p>
            </div>
            <div class="text-center px-4 py-3 rounded-xl bg-green-50">
                <p class="text-2xl font-bold text-hemo-success"><?php echo number_format($stats['total_volume_ml']); ?></p>
                <p class="text-[10px] text-hemo-gray font-semibold uppercase tracking-wider">ml Total</p>
            </div>
        </div>
    </div>
</div>

<!-- Edit Form Card -->
<div class="bg-white rounded-xl shadow-card overflow-hidden">
    <div class="px-6 py-4 border-b border-hemo-border flex items-center gap-3">
        <i class="fas fa-user-pen text-hemo-red"></i>
        <h3 class="text-lg font-semibold text-hemo-navy">Edit Profile</h3>
    </div>

    <form method="POST" action="<?php echo BASE_URL; ?>/index.php?page=donor_profile_update" class="p-6 sm:p-8">
        <div class="grid grid-cols-1 sm:grid-cols-2 gap-6">
            <!-- First Name -->
            <div>
                <label class="block text-sm font-semibold text-hemo-navy mb-1.5">First Name <span class="text-hemo-red">*</span></label>
                <input type="text" name="first_name" required value="<?php echo sanitize($donor['first_name'] ?? ''); ?>"
                    class="w-full h-11 px-4 rounded-lg border-2 border-hemo-border text-sm">
            </div>
            <!-- Last Name -->
            <div>
                <label class="block text-sm font-semibold text-hemo-navy mb-1.5">Last Name <span class="text-hemo-red">*</span></label>
                <input type="text" name="last_name" required value="<?php echo sanitize($donor['last_name'] ?? ''); ?>"
                    class="w-full h-11 px-4 rounded-lg border-2 border-hemo-border text-sm">
            </div>
            <!-- Email -->
            <div>
                <label class="block text-sm font-semibold text-hemo-navy mb-1.5">Email <span class="text-hemo-red">*</span></label>
                <input type="email" name="email" required value="<?php echo sanitize($donor['email'] ?? ''); ?>"
                    class="w-full h-11 px-4 rounded-lg border-2 border-hemo-border text-sm">
            </div>
            <!-- Phone -->
            <div>
                <label class="block text-sm font-semibold text-hemo-navy mb-1.5">Phone</label>
                <input type="tel" name="phone" value="<?php echo sanitize($donor['phone'] ?? ''); ?>"
                    class="w-full h-11 px-4 rounded-lg border-2 border-hemo-border text-sm">
            </div>
            <!-- Blood Type -->
            <div>
                <label class="block text-sm font-semibold text-hemo-navy mb-1.5">Blood Type</label>
                <select name="blood_type" class="w-full h-11 px-4 rounded-lg border-2 border-hemo-border text-sm">
                    <option value="">Select blood type</option>
                    <?php foreach ($bloodTypes as $bt): ?>
                    <option value="<?php echo sanitize($bt); ?>" <?php echo ($donor['blood_type'] ?? '') === $bt ? 'selected' : ''; ?>>
                        <?php echo sanitize($bt); ?>
                    </option>
                    <?php endforeach; ?>
                </select>
            </div>
            <!-- Date of Birth -->
            <div>
                <label class="block text-sm font-semibold text-hemo-navy mb-1.5">Date of Birth</label>
                <input type="date" name="date_of_birth" value="<?php echo sanitize($donor['date_of_birth'] ?? ''); ?>"
                    class="w-full h-11 px-4 rounded-lg border-2 border-hemo-border text-sm">
            </div>
            <!-- Gender -->
            <div>
                <label class="block text-sm font-semibold text-hemo-navy mb-1.5">Gender</label>
                <select name="gender" class="w-full h-11 px-4 rounded-lg border-2 border-hemo-border text-sm">
                    <option value="">Select gender</option>
                    <option value="Male" <?php echo ($donor['gender'] ?? '') === 'Male' ? 'selected' : ''; ?>>Male</option>
                    <option value="Female" <?php echo ($donor['gender'] ?? '') === 'Female' ? 'selected' : ''; ?>>Female</option>
                    <option value="Other" <?php echo ($donor['gender'] ?? '') === 'Other' ? 'selected' : ''; ?>>Other</option>
                </select>
            </div>
            <!-- City -->
            <div>
                <label class="block text-sm font-semibold text-hemo-navy mb-1.5">City</label>
                <input type="text" name="city" value="<?php echo sanitize($donor['city'] ?? ''); ?>"
                    class="w-full h-11 px-4 rounded-lg border-2 border-hemo-border text-sm">
            </div>
            <!-- Address (full width) -->
            <div class="sm:col-span-2">
                <label class="block text-sm font-semibold text-hemo-navy mb-1.5">Address</label>
                <input type="text" name="address" value="<?php echo sanitize($donor['address'] ?? ''); ?>"
                    class="w-full h-11 px-4 rounded-lg border-2 border-hemo-border text-sm">
            </div>
        </div>

        <!-- Save Button -->
        <div class="flex justify-end mt-8 pt-6 border-t border-hemo-border">
            <button type="submit" class="inline-flex items-center gap-2 px-6 py-3 rounded-lg bg-hemo-red text-white font-semibold text-sm shadow-btn-primary hover:bg-hemo-deep-red hover:shadow-btn-hover hover:-translate-y-px transition-default btn-press">
                <i class="fas fa-check"></i> Save Changes
            </button>
        </div>
    </form>
</div>

<?php
$content = ob_get_clean();
require_once __DIR__ . '/../layouts/dashboard_layout.php';
?>
