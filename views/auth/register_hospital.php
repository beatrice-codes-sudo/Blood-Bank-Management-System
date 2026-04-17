<?php
/**
 * Hospital Registration Page
 */
$pageTitle = 'Hospital Registration';
$errors = $_SESSION['errors'] ?? [];
$oldInput = $_SESSION['old_input'] ?? [];
unset($_SESSION['errors'], $_SESSION['old_input']);
require_once __DIR__ . '/../layouts/header.php';
?>

<div class="min-h-screen flex">
    <!-- Left Panel -->
    <div class="hidden lg:flex lg:w-[55%] gradient-red-radial relative overflow-hidden items-center justify-center">
        <div class="absolute top-10 left-10 w-72 h-72 bg-white/5 rounded-full blur-3xl"></div>
        <div class="absolute bottom-20 right-20 w-96 h-96 bg-white/3 rounded-full blur-3xl"></div>

        <div class="relative z-10 text-center px-12 max-w-md">
            <div class="w-20 h-20 rounded-2xl bg-white/10 backdrop-blur-sm flex items-center justify-center mx-auto mb-8 border border-white/20">
                <i class="fas fa-hospital text-hemo-gold text-4xl"></i>
            </div>
            <h2 class="font-display text-4xl font-bold text-white mb-4">Partner With<br>HemoLink</h2>
            <p class="text-lg text-white/70 font-light leading-relaxed mb-10">Register your hospital to request blood units, track deliveries, and manage your needs efficiently.</p>
            
            <div class="space-y-4 text-left">
                <div class="glass-card p-4 flex items-center gap-4">
                    <div class="w-10 h-10 rounded-lg bg-white/10 flex items-center justify-center flex-shrink-0">
                        <i class="fas fa-bolt text-hemo-gold"></i>
                    </div>
                    <div>
                        <p class="text-sm font-semibold text-white">Instant Requests</p>
                        <p class="text-xs text-white/60">Request blood units in seconds</p>
                    </div>
                </div>
                <div class="glass-card p-4 flex items-center gap-4">
                    <div class="w-10 h-10 rounded-lg bg-white/10 flex items-center justify-center flex-shrink-0">
                        <i class="fas fa-truck-medical text-hemo-gold"></i>
                    </div>
                    <div>
                        <p class="text-sm font-semibold text-white">Track Deliveries</p>
                        <p class="text-xs text-white/60">Real-time delivery status updates</p>
                    </div>
                </div>
                <div class="glass-card p-4 flex items-center gap-4">
                    <div class="w-10 h-10 rounded-lg bg-white/10 flex items-center justify-center flex-shrink-0">
                        <i class="fas fa-chart-line text-hemo-gold"></i>
                    </div>
                    <div>
                        <p class="text-sm font-semibold text-white">Analytics Dashboard</p>
                        <p class="text-xs text-white/60">Monitor usage patterns and trends</p>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Right Panel -->
    <div class="w-full lg:w-[45%] bg-white flex items-start justify-center p-8 overflow-y-auto">
        <div class="w-full max-w-[480px] py-6">
            <div class="lg:hidden flex items-center gap-3 mb-6">
                <div class="w-10 h-10 rounded-lg gradient-red flex items-center justify-center">
                    <i class="fas fa-droplet text-white text-lg"></i>
                </div>
                <h1 class="font-display text-2xl font-bold text-hemo-navy">Hemo<span class="text-hemo-gold">Link</span></h1>
            </div>

            <div class="mb-6">
                <h2 class="font-display text-[28px] font-bold text-hemo-navy mb-2">Hospital Registration</h2>
                <p class="text-hemo-charcoal text-sm">Register your hospital to start requesting blood units</p>
            </div>

            <?php if (!empty($errors)): ?>
                <div class="mb-6 p-4 rounded-xl bg-red-50 border border-red-200">
                    <?php foreach ($errors as $error): ?>
                        <p class="text-sm text-hemo-warning flex items-center gap-2 mb-1 last:mb-0">
                            <i class="fas fa-circle text-[5px]"></i> <?php echo sanitize($error); ?>
                        </p>
                    <?php endforeach; ?>
                </div>
            <?php endif; ?>

            <form action="<?php echo BASE_URL; ?>/index.php?page=register_hospital_process" method="POST" class="space-y-5" id="register-hospital-form">
                <!-- Section: Hospital Info -->
                <div class="pb-2">
                    <h3 class="text-xs font-bold text-hemo-gray uppercase tracking-widest flex items-center gap-2">
                        <i class="fas fa-hospital text-hemo-red"></i> Hospital Information
                    </h3>
                </div>

                <div>
                    <label class="block text-sm font-semibold text-hemo-navy mb-1.5">Hospital Name <span class="text-hemo-red">*</span></label>
                    <input type="text" name="hospital_name" id="reg-hospital-name" required
                           value="<?php echo sanitize($oldInput['hospital_name'] ?? ''); ?>"
                           class="w-full h-[44px] border-2 border-hemo-border rounded-lg px-4 text-sm text-hemo-navy placeholder:text-gray-400 transition-fast"
                           placeholder="Kenyatta National Hospital">
                </div>

                <div class="grid grid-cols-2 gap-4">
                    <div>
                        <label class="block text-sm font-semibold text-hemo-navy mb-1.5">Hospital Code <span class="text-hemo-red">*</span></label>
                        <input type="text" name="hospital_code" id="reg-hospital-code" required
                               value="<?php echo sanitize($oldInput['hospital_code'] ?? ''); ?>"
                               class="w-full h-[44px] border-2 border-hemo-border rounded-lg px-4 text-sm text-hemo-navy placeholder:text-gray-400 transition-fast"
                               placeholder="KNH-001">
                    </div>
                    <div>
                        <label class="block text-sm font-semibold text-hemo-navy mb-1.5">License Number</label>
                        <input type="text" name="license_number" id="reg-license"
                               value="<?php echo sanitize($oldInput['license_number'] ?? ''); ?>"
                               class="w-full h-[44px] border-2 border-hemo-border rounded-lg px-4 text-sm text-hemo-navy placeholder:text-gray-400 transition-fast"
                               placeholder="LIC-XXXX">
                    </div>
                </div>

                <div>
                    <label class="block text-sm font-semibold text-hemo-navy mb-1.5">Address <span class="text-hemo-red">*</span></label>
                    <input type="text" name="address" id="reg-address" required
                           value="<?php echo sanitize($oldInput['address'] ?? ''); ?>"
                           class="w-full h-[44px] border-2 border-hemo-border rounded-lg px-4 text-sm text-hemo-navy placeholder:text-gray-400 transition-fast"
                           placeholder="Hospital Road, P.O. Box XXX">
                </div>

                <div class="grid grid-cols-3 gap-4">
                    <div>
                        <label class="block text-sm font-semibold text-hemo-navy mb-1.5">City <span class="text-hemo-red">*</span></label>
                        <input type="text" name="city" id="reg-city" required
                               value="<?php echo sanitize($oldInput['city'] ?? ''); ?>"
                               class="w-full h-[44px] border-2 border-hemo-border rounded-lg px-4 text-sm text-hemo-navy placeholder:text-gray-400 transition-fast"
                               placeholder="Nairobi">
                    </div>
                    <div>
                        <label class="block text-sm font-semibold text-hemo-navy mb-1.5">Region</label>
                        <input type="text" name="region" id="reg-region"
                               value="<?php echo sanitize($oldInput['region'] ?? ''); ?>"
                               class="w-full h-[44px] border-2 border-hemo-border rounded-lg px-4 text-sm text-hemo-navy placeholder:text-gray-400 transition-fast"
                               placeholder="Nairobi">
                    </div>
                    <div>
                        <label class="block text-sm font-semibold text-hemo-navy mb-1.5">Postal Code</label>
                        <input type="text" name="postal_code" id="reg-postal"
                               value="<?php echo sanitize($oldInput['postal_code'] ?? ''); ?>"
                               class="w-full h-[44px] border-2 border-hemo-border rounded-lg px-4 text-sm text-hemo-navy placeholder:text-gray-400 transition-fast"
                               placeholder="00100">
                    </div>
                </div>

                <!-- Section: Manager Info -->
                <div class="pt-4 pb-2 border-t border-hemo-border">
                    <h3 class="text-xs font-bold text-hemo-gray uppercase tracking-widest flex items-center gap-2">
                        <i class="fas fa-user-tie text-hemo-red"></i> Manager Account
                    </h3>
                </div>

                <div class="grid grid-cols-2 gap-4">
                    <div>
                        <label class="block text-sm font-semibold text-hemo-navy mb-1.5">First Name <span class="text-hemo-red">*</span></label>
                        <input type="text" name="first_name" id="reg-first-name" required
                               value="<?php echo sanitize($oldInput['first_name'] ?? ''); ?>"
                               class="w-full h-[44px] border-2 border-hemo-border rounded-lg px-4 text-sm text-hemo-navy placeholder:text-gray-400 transition-fast"
                               placeholder="Jane">
                    </div>
                    <div>
                        <label class="block text-sm font-semibold text-hemo-navy mb-1.5">Last Name <span class="text-hemo-red">*</span></label>
                        <input type="text" name="last_name" id="reg-last-name" required
                               value="<?php echo sanitize($oldInput['last_name'] ?? ''); ?>"
                               class="w-full h-[44px] border-2 border-hemo-border rounded-lg px-4 text-sm text-hemo-navy placeholder:text-gray-400 transition-fast"
                               placeholder="Manager">
                    </div>
                </div>

                <div>
                    <label class="block text-sm font-semibold text-hemo-navy mb-1.5">Email Address <span class="text-hemo-red">*</span></label>
                    <div class="relative">
                        <i class="fas fa-envelope absolute left-4 top-1/2 -translate-y-1/2 text-hemo-gray text-sm"></i>
                        <input type="email" name="email" id="reg-email" required
                               value="<?php echo sanitize($oldInput['email'] ?? ''); ?>"
                               class="w-full h-[44px] border-2 border-hemo-border rounded-lg pl-11 pr-4 text-sm text-hemo-navy placeholder:text-gray-400 transition-fast"
                               placeholder="manager@hospital.com">
                    </div>
                </div>

                <div>
                    <label class="block text-sm font-semibold text-hemo-navy mb-1.5">Phone Number</label>
                    <div class="relative">
                        <i class="fas fa-phone absolute left-4 top-1/2 -translate-y-1/2 text-hemo-gray text-sm"></i>
                        <input type="tel" name="phone" id="reg-phone"
                               value="<?php echo sanitize($oldInput['phone'] ?? ''); ?>"
                               class="w-full h-[44px] border-2 border-hemo-border rounded-lg pl-11 pr-4 text-sm text-hemo-navy placeholder:text-gray-400 transition-fast"
                               placeholder="+254 700 000 000">
                    </div>
                </div>

                <div class="grid grid-cols-2 gap-4">
                    <div>
                        <label class="block text-sm font-semibold text-hemo-navy mb-1.5">Password <span class="text-hemo-red">*</span></label>
                        <div class="relative">
                            <i class="fas fa-lock absolute left-4 top-1/2 -translate-y-1/2 text-hemo-gray text-sm"></i>
                            <input type="password" name="password" id="reg-password" required minlength="6"
                                   class="w-full h-[44px] border-2 border-hemo-border rounded-lg pl-11 pr-4 text-sm text-hemo-navy placeholder:text-gray-400 transition-fast"
                                   placeholder="Min 6 chars">
                        </div>
                    </div>
                    <div>
                        <label class="block text-sm font-semibold text-hemo-navy mb-1.5">Confirm <span class="text-hemo-red">*</span></label>
                        <div class="relative">
                            <i class="fas fa-lock absolute left-4 top-1/2 -translate-y-1/2 text-hemo-gray text-sm"></i>
                            <input type="password" name="confirm_password" id="reg-confirm-password" required
                                   class="w-full h-[44px] border-2 border-hemo-border rounded-lg pl-11 pr-4 text-sm text-hemo-navy placeholder:text-gray-400 transition-fast"
                                   placeholder="Confirm">
                        </div>
                    </div>
                </div>

                <button type="submit" id="register-hospital-submit"
                        class="w-full h-12 rounded-lg bg-hemo-navy text-white font-semibold text-base hover:bg-slate-800 hover:-translate-y-px transition-default btn-press flex items-center justify-center gap-2 shadow-lg">
                    <i class="fas fa-hospital"></i> Register Hospital
                </button>
            </form>

            <p class="text-center text-sm text-hemo-charcoal mt-6">
                Already registered? 
                <a href="<?php echo BASE_URL; ?>/index.php?page=login" class="text-hemo-red font-semibold hover:text-hemo-deep-red transition-fast">Sign In</a>
            </p>
        </div>
    </div>
</div>

<?php require_once __DIR__ . '/../layouts/footer.php'; ?>
