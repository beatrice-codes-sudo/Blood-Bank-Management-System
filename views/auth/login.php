<?php
/**
 * Login Page
 * Split-screen layout per DesignLayout.md §6.1
 */
$pageTitle = 'Sign In';
$errors = $_SESSION['errors'] ?? [];
$oldInput = $_SESSION['old_input'] ?? [];
unset($_SESSION['errors'], $_SESSION['old_input']);
require_once __DIR__ . '/../layouts/header.php';
?>

<div class="min-h-screen flex">
    <!-- Left Panel - Branding -->
    <div class="hidden lg:flex lg:w-[60%] gradient-red-radial relative overflow-hidden items-center justify-center">
        <!-- Decorative blobs -->
        <div class="absolute top-10 left-10 w-72 h-72 bg-white/5 rounded-full blur-3xl"></div>
        <div class="absolute bottom-20 right-20 w-96 h-96 bg-white/3 rounded-full blur-3xl"></div>
        <div class="absolute top-1/3 right-1/3 w-3 h-3 bg-hemo-gold rounded-full pulse-dot"></div>
        <div class="absolute bottom-1/3 left-1/4 w-2 h-2 bg-white/40 rounded-full pulse-dot" style="animation-delay: 0.7s"></div>

        <div class="relative z-10 text-center px-12 max-w-lg">
            <!-- Logo -->
            <div class="flex items-center justify-center gap-4 mb-8">
                <div class="w-14 h-14 rounded-xl bg-white/10 backdrop-blur-sm flex items-center justify-center border border-white/20">
                    <i class="fas fa-droplet text-hemo-gold text-2xl"></i>
                </div>
                <h1 class="font-display text-5xl font-bold text-white tracking-tight">Hemo<span class="text-hemo-gold">Link</span></h1>
            </div>
            <p class="text-xl font-light text-white/80 mb-12">Blood Bank Management System</p>

            <!-- Stats -->
            <div class="grid grid-cols-3 gap-5">
                <div class="glass-card p-5 text-center">
                    <p class="text-2xl font-bold text-white mb-1">500+</p>
                    <p class="text-xs text-white/60 font-medium uppercase tracking-wider">Active Donors</p>
                </div>
                <div class="glass-card p-5 text-center">
                    <p class="text-2xl font-bold text-white mb-1">50+</p>
                    <p class="text-xs text-white/60 font-medium uppercase tracking-wider">Hospitals</p>
                </div>
                <div class="glass-card p-5 text-center">
                    <p class="text-2xl font-bold text-white mb-1">2K+</p>
                    <p class="text-xs text-white/60 font-medium uppercase tracking-wider">Units Saved</p>
                </div>
            </div>
        </div>
    </div>

    <!-- Right Panel - Login Form -->
    <div class="w-full lg:w-[40%] min-w-0 lg:min-w-[450px] bg-white flex items-center justify-center p-8">
        <div class="w-full max-w-[400px]">
            <!-- Mobile Logo -->
            <div class="lg:hidden flex items-center gap-3 mb-8">
                <div class="w-10 h-10 rounded-lg gradient-red flex items-center justify-center">
                    <i class="fas fa-droplet text-white text-lg"></i>
                </div>
                <h1 class="font-display text-2xl font-bold text-hemo-navy">Hemo<span class="text-hemo-gold">Link</span></h1>
            </div>

            <!-- Header -->
            <div class="mb-8">
                <h2 class="font-display text-[32px] font-bold text-hemo-navy mb-2">Welcome Back</h2>
                <p class="text-hemo-charcoal">Sign in to access your dashboard</p>
            </div>

            <!-- Flash Message -->
            <?php $flash = getFlashMessage(); if ($flash): ?>
                <div class="mb-6 p-4 rounded-xl text-sm font-medium flex items-center gap-3
                    <?php echo $flash['type'] === 'success' ? 'bg-green-50 text-hemo-success border border-green-200' : 'bg-red-50 text-hemo-warning border border-red-200'; ?>">
                    <i class="fas <?php echo $flash['type'] === 'success' ? 'fa-check-circle' : 'fa-exclamation-circle'; ?>"></i>
                    <?php echo sanitize($flash['message']); ?>
                </div>
            <?php endif; ?>

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

            <!-- Login Form -->
            <form action="<?php echo BASE_URL; ?>/index.php?page=login_process" method="POST" class="space-y-6" id="login-form">
                <div>
                    <label class="block text-sm font-semibold text-hemo-navy mb-2">Email Address</label>
                    <div class="relative">
                        <i class="fas fa-envelope absolute left-4 top-1/2 -translate-y-1/2 text-hemo-gray text-sm"></i>
                        <input type="email" name="email" id="login-email" required
                               value="<?php echo sanitize($oldInput['email'] ?? ''); ?>"
                               class="w-full h-[44px] border-2 border-hemo-border rounded-lg pl-11 pr-4 text-sm text-hemo-navy font-interface placeholder:text-gray-400 transition-fast"
                               placeholder="Enter your email">
                    </div>
                </div>

                <div>
                    <label class="block text-sm font-semibold text-hemo-navy mb-2">Password</label>
                    <div class="relative">
                        <i class="fas fa-lock absolute left-4 top-1/2 -translate-y-1/2 text-hemo-gray text-sm"></i>
                        <input type="password" name="password" id="login-password" required
                               class="w-full h-[44px] border-2 border-hemo-border rounded-lg pl-11 pr-4 text-sm text-hemo-navy font-interface placeholder:text-gray-400 transition-fast"
                               placeholder="Enter your password">
                    </div>
                </div>

                <div class="flex items-center justify-between">
                    <label class="flex items-center gap-2 cursor-pointer">
                        <input type="checkbox" class="w-4 h-4 rounded border-hemo-border text-hemo-red focus:ring-hemo-red">
                        <span class="text-sm text-hemo-charcoal">Remember me</span>
                    </label>
                    <a href="#" class="text-sm text-hemo-red font-medium hover:text-hemo-deep-red transition-fast">Forgot password?</a>
                </div>

                <button type="submit" id="login-submit"
                        class="w-full h-12 rounded-lg bg-hemo-red text-white font-semibold text-base shadow-btn-primary hover:bg-hemo-deep-red hover:shadow-btn-hover hover:-translate-y-px transition-default btn-press flex items-center justify-center gap-2">
                    <i class="fas fa-right-to-bracket"></i> Sign In
                </button>
            </form>

            <!-- Registration Links -->
            <div class="mt-8 pt-8 border-t border-hemo-border">
                <p class="text-sm text-hemo-charcoal text-center mb-4">Don't have an account?</p>
                <div class="grid grid-cols-2 gap-3">
                    <a href="<?php echo BASE_URL; ?>/index.php?page=register_donor" 
                       class="flex items-center justify-center gap-2 px-4 py-3 rounded-lg border-2 border-hemo-red text-hemo-red text-sm font-semibold hover:bg-hemo-red hover:text-white transition-default btn-press">
                        <i class="fas fa-heart-pulse"></i> As Donor
                    </a>
                    <a href="<?php echo BASE_URL; ?>/index.php?page=register_hospital" 
                       class="flex items-center justify-center gap-2 px-4 py-3 rounded-lg border-2 border-hemo-navy text-hemo-navy text-sm font-semibold hover:bg-hemo-navy hover:text-white transition-default btn-press">
                        <i class="fas fa-hospital"></i> As Hospital
                    </a>
                </div>
            </div>
        </div>
    </div>
</div>

<?php require_once __DIR__ . '/../layouts/footer.php'; ?>
