<?php
/**
 * Donor Registration Page
 */
$pageTitle = 'Donor Registration';
$errors = $_SESSION['errors'] ?? [];
$oldInput = $_SESSION['old_input'] ?? [];
unset($_SESSION['errors'], $_SESSION['old_input']);
require_once __DIR__ . '/../layouts/header.php';
?>

<div class="min-h-screen flex">
    <!-- Left Panel - Branding -->
    <div class="hidden lg:flex lg:w-[55%] gradient-red-radial relative overflow-hidden items-center justify-center">
        <div class="absolute top-10 left-10 w-72 h-72 bg-white/5 rounded-full blur-3xl"></div>
        <div class="absolute bottom-20 right-20 w-96 h-96 bg-white/3 rounded-full blur-3xl"></div>

        <div class="relative z-10 text-center px-12 max-w-md">
            <div class="w-20 h-20 rounded-2xl bg-white/10 backdrop-blur-sm flex items-center justify-center mx-auto mb-8 border border-white/20">
                <i class="fas fa-heart-pulse text-hemo-gold text-4xl"></i>
            </div>
            <h2 class="font-display text-4xl font-bold text-white mb-4">Become a<br>Life Saver</h2>
            <p class="text-lg text-white/70 font-light leading-relaxed mb-10">Register as a donor and track your contributions. Every donation can save up to 3 lives.</p>
            
            <div class="space-y-4 text-left">
                <div class="glass-card p-4 flex items-center gap-4">
                    <div class="w-10 h-10 rounded-lg bg-white/10 flex items-center justify-center flex-shrink-0">
                        <i class="fas fa-droplet text-hemo-gold"></i>
                    </div>
                    <div>
                        <p class="text-sm font-semibold text-white">Track Your Donations</p>
                        <p class="text-xs text-white/60">View your complete donation history</p>
                    </div>
                </div>
                <div class="glass-card p-4 flex items-center gap-4">
                    <div class="w-10 h-10 rounded-lg bg-white/10 flex items-center justify-center flex-shrink-0">
                        <i class="fas fa-calendar-check text-hemo-gold"></i>
                    </div>
                    <div>
                        <p class="text-sm font-semibold text-white">Schedule Appointments</p>
                        <p class="text-xs text-white/60">Book your next donation session</p>
                    </div>
                </div>
                <div class="glass-card p-4 flex items-center gap-4">
                    <div class="w-10 h-10 rounded-lg bg-white/10 flex items-center justify-center flex-shrink-0">
                        <i class="fas fa-certificate text-hemo-gold"></i>
                    </div>
                    <div>
                        <p class="text-sm font-semibold text-white">Get Certified</p>
                        <p class="text-xs text-white/60">Receive digital donation certificates</p>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Right Panel - Registration Form -->
    <div class="w-full lg:w-[45%] bg-white flex items-start justify-center p-8 overflow-y-auto relative">
        <!-- Back to Home Button -->
        <a href="<?php echo BASE_URL; ?>/index.php" class="absolute top-6 right-8 text-hemo-gray hover:text-hemo-red transition-fast flex items-center gap-2 text-sm font-semibold bg-hemo-light-gray px-3 py-1.5 rounded-lg z-10">
            <i class="fas fa-home"></i> Home
        </a>
        <div class="w-full max-w-[480px] py-6">
            <!-- Mobile Logo -->
            <div class="lg:hidden flex items-center gap-3 mb-6">
                <div class="w-10 h-10 rounded-lg gradient-red flex items-center justify-center">
                    <i class="fas fa-droplet text-white text-lg"></i>
                </div>
                <h1 class="font-display text-2xl font-bold text-hemo-navy">Hemo<span class="text-hemo-gold">Link</span></h1>
            </div>

            <div class="mb-6">
                <h2 class="font-display text-[28px] font-bold text-hemo-navy mb-2">Donor Registration</h2>
                <p class="text-hemo-charcoal text-sm">Create your account and start saving lives</p>
            </div>

            <!-- Errors -->
            <?php if (!empty($errors)): ?>
                <div class="mb-6 p-4 rounded-xl bg-red-50 border border-red-200">
                    <?php foreach ($errors as $error): ?>
                        <p class="text-sm text-hemo-warning flex items-center gap-2 mb-1 last:mb-0">
                            <i class="fas fa-circle text-[5px]"></i> <?php echo sanitize($error); ?>
                        </p>
                    <?php endforeach; ?>
                </div>
            <?php endif; ?>

            <form action="<?php echo BASE_URL; ?>/index.php?page=register_donor_process" method="POST" class="space-y-5" id="register-donor-form">
                <!-- Name Row -->
                <div class="grid grid-cols-2 gap-4">
                    <div>
                        <label class="block text-sm font-semibold text-hemo-navy mb-1.5">First Name <span class="text-hemo-red">*</span></label>
                        <input type="text" name="first_name" id="reg-first-name" required
                               value="<?php echo sanitize($oldInput['first_name'] ?? ''); ?>"
                               class="w-full h-[44px] border-2 border-hemo-border rounded-lg px-4 text-sm text-hemo-navy placeholder:text-gray-400 transition-fast"
                               placeholder="John">
                    </div>
                    <div>
                        <label class="block text-sm font-semibold text-hemo-navy mb-1.5">Last Name <span class="text-hemo-red">*</span></label>
                        <input type="text" name="last_name" id="reg-last-name" required
                               value="<?php echo sanitize($oldInput['last_name'] ?? ''); ?>"
                               class="w-full h-[44px] border-2 border-hemo-border rounded-lg px-4 text-sm text-hemo-navy placeholder:text-gray-400 transition-fast"
                               placeholder="Doe">
                    </div>
                </div>

                <!-- Email -->
                <div>
                    <label class="block text-sm font-semibold text-hemo-navy mb-1.5">Email Address <span class="text-hemo-red">*</span></label>
                    <div class="relative">
                        <i class="fas fa-envelope absolute left-4 top-1/2 -translate-y-1/2 text-hemo-gray text-sm"></i>
                        <input type="email" name="email" id="reg-email" required
                               value="<?php echo sanitize($oldInput['email'] ?? ''); ?>"
                               class="w-full h-[44px] border-2 border-hemo-border rounded-lg pl-11 pr-4 text-sm text-hemo-navy placeholder:text-gray-400 transition-fast"
                               placeholder="john@example.com">
                    </div>
                </div>

                <!-- Phone -->
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

                <!-- Blood Type & DOB -->
                <div class="grid grid-cols-2 gap-4">
                    <div>
                        <label class="block text-sm font-semibold text-hemo-navy mb-1.5">Blood Type <span class="text-hemo-red">*</span></label>
                        <select name="blood_type_id" id="reg-blood-type" required
                                class="w-full h-[44px] border-2 border-hemo-border rounded-lg px-4 text-sm text-hemo-navy appearance-none bg-white transition-fast">
                            <option value="">Select type</option>
                            <?php if (isset($bloodTypes)): foreach ($bloodTypes as $bt): ?>
                                <option value="<?php echo $bt['blood_type_id']; ?>" <?php echo ($oldInput['blood_type_id'] ?? '') == $bt['blood_type_id'] ? 'selected' : ''; ?>>
                                    <?php echo sanitize($bt['type_name']); ?>
                                </option>
                            <?php endforeach; endif; ?>
                        </select>
                    </div>
                    <div>
                        <label class="block text-sm font-semibold text-hemo-navy mb-1.5">Date of Birth</label>
                        <input type="date" name="date_of_birth" id="reg-dob"
                               value="<?php echo sanitize($oldInput['date_of_birth'] ?? ''); ?>"
                               class="w-full h-[44px] border-2 border-hemo-border rounded-lg px-4 text-sm text-hemo-navy transition-fast">
                    </div>
                </div>

                <!-- Gender -->
                <div>
                    <label class="block text-sm font-semibold text-hemo-navy mb-1.5">Gender</label>
                    <select name="gender" id="reg-gender"
                            class="w-full h-[44px] border-2 border-hemo-border rounded-lg px-4 text-sm text-hemo-navy appearance-none bg-white transition-fast">
                        <option value="">Select gender</option>
                        <option value="Male" <?php echo ($oldInput['gender'] ?? '') === 'Male' ? 'selected' : ''; ?>>Male</option>
                        <option value="Female" <?php echo ($oldInput['gender'] ?? '') === 'Female' ? 'selected' : ''; ?>>Female</option>
                        <option value="Other" <?php echo ($oldInput['gender'] ?? '') === 'Other' ? 'selected' : ''; ?>>Other</option>
                    </select>
                </div>

                <!-- Address -->
                <div class="grid grid-cols-2 gap-4">
                    <div>
                        <label class="block text-sm font-semibold text-hemo-navy mb-1.5">Address</label>
                        <input type="text" name="address" id="reg-address"
                               value="<?php echo sanitize($oldInput['address'] ?? ''); ?>"
                               class="w-full h-[44px] border-2 border-hemo-border rounded-lg px-4 text-sm text-hemo-navy placeholder:text-gray-400 transition-fast"
                               placeholder="Street address">
                    </div>
                    <div>
                        <label class="block text-sm font-semibold text-hemo-navy mb-1.5">City</label>
                        <input type="text" name="city" id="reg-city"
                               value="<?php echo sanitize($oldInput['city'] ?? ''); ?>"
                               class="w-full h-[44px] border-2 border-hemo-border rounded-lg px-4 text-sm text-hemo-navy placeholder:text-gray-400 transition-fast"
                               placeholder="City">
                    </div>
                </div>

                <!-- Password -->
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

                <button type="submit" id="register-donor-submit"
                        class="w-full h-12 rounded-lg bg-hemo-red text-white font-semibold text-base shadow-btn-primary hover:bg-hemo-deep-red hover:shadow-btn-hover hover:-translate-y-px transition-default btn-press flex items-center justify-center gap-2">
                    <i class="fas fa-user-plus"></i> Create Account
                </button>
            </form>

            <p class="text-center text-sm text-hemo-charcoal mt-6">
                Already have an account? 
                <a href="<?php echo BASE_URL; ?>/index.php?page=login" class="text-hemo-red font-semibold hover:text-hemo-deep-red transition-fast">Sign In</a>
            </p>
        </div>
    </div>
</div>

<?php require_once __DIR__ . '/../layouts/footer.php'; ?>
