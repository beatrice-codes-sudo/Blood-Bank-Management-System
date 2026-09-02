<?php
/**
 * Landing Page
 */
$pageTitle = 'Welcome';
require_once __DIR__ . '/layouts/header.php';
?>

<!-- Navigation Bar -->
<nav class="fixed top-0 left-0 right-0 z-50 bg-white/90 backdrop-blur-md border-b border-hemo-border">
    <div class="max-w-7xl mx-auto px-6 h-[72px] flex items-center justify-between">
        <a href="<?php echo BASE_URL; ?>" class="flex items-center gap-3 no-underline">
            <div class="w-10 h-10 rounded-lg gradient-red flex items-center justify-center">
                <i class="fas fa-droplet text-white text-lg"></i>
            </div>
            <h1 class="font-display text-2xl font-bold text-hemo-navy tracking-tight">Hemo<span class="text-hemo-gold">Link</span></h1>
        </a>
        <div class="flex items-center gap-4">
            <a href="<?php echo BASE_URL; ?>/index.php?page=login" 
               class="hidden sm:inline-flex items-center gap-2 px-5 py-2.5 rounded-lg border-2 border-hemo-red text-hemo-red font-semibold text-sm hover:bg-hemo-red hover:text-white transition-default btn-press">
                <i class="fas fa-right-to-bracket"></i> Sign In
            </a>
            <a href="<?php echo BASE_URL; ?>/index.php?page=register_donor" 
               class="inline-flex items-center gap-2 px-5 py-2.5 rounded-lg bg-hemo-red text-white font-semibold text-sm shadow-btn-primary hover:bg-hemo-deep-red hover:shadow-btn-hover hover:-translate-y-px transition-default btn-press">
                <i class="fas fa-heart-pulse"></i> Become a Donor
            </a>
        </div>
    </div>
</nav>

<!-- Hero Section -->
<section class="min-h-screen gradient-red-radial flex items-center pt-[72px] relative overflow-hidden">
    <!-- Decorative elements -->
    <div class="absolute top-20 right-10 w-64 h-64 bg-white/5 rounded-full blur-3xl"></div>
    <div class="absolute bottom-10 left-10 w-96 h-96 bg-white/3 rounded-full blur-3xl"></div>

    <div class="max-w-7xl mx-auto px-6 py-20 w-full relative z-10">
        <div class="grid lg:grid-cols-2 gap-16 items-center">
            <!-- Left: Content -->
            <div class="text-white">
                <h2 class="font-display text-5xl lg:text-6xl font-bold leading-tight mb-6">
                    Every Drop<br>
                    <span class="text-hemo-gold">Counts</span>
                </h2>
                <p class="text-xl font-light text-white/80 mb-10 max-w-lg leading-relaxed">
                    HemoLink connects donors, hospitals, and blood banks through a seamless, modern platform. Join our mission to ensure no patient goes without.
                </p>
                <div class="flex flex-wrap gap-4">
                    <a href="<?php echo BASE_URL; ?>/index.php?page=register_donor" 
                       class="inline-flex items-center gap-3 px-8 py-4 rounded-xl bg-white text-hemo-red font-bold text-base hover:bg-hemo-gold hover:text-hemo-navy transition-default btn-press shadow-lg">
                        <i class="fas fa-heart-pulse"></i> Register as Donor
                    </a>
                    <a href="<?php echo BASE_URL; ?>/index.php?page=register_hospital" 
                       class="inline-flex items-center gap-3 px-8 py-4 rounded-xl glass-card text-white font-bold text-base hover:bg-white/20 transition-default btn-press">
                        <i class="fas fa-hospital"></i> Register Hospital
                    </a>
                </div>
            </div>

            <!-- Right: Stats Cards -->
            <div class="grid grid-cols-2 gap-5">
                <div class="glass-card p-6 text-center text-white hover:bg-white/15 transition-default">
                    <div class="w-14 h-14 rounded-xl bg-white/10 flex items-center justify-center mx-auto mb-4">
                        <i class="fas fa-droplet text-2xl text-hemo-gold"></i>
                    </div>
                    <p class="text-3xl font-bold mb-1">8</p>
                    <p class="text-sm text-white/70 font-medium">Blood Types</p>
                </div>
                <div class="glass-card p-6 text-center text-white hover:bg-white/15 transition-default">
                    <div class="w-14 h-14 rounded-xl bg-white/10 flex items-center justify-center mx-auto mb-4">
                        <i class="fas fa-users text-2xl text-hemo-gold"></i>
                    </div>
                    <p class="text-3xl font-bold mb-1">24/7</p>
                    <p class="text-sm text-white/70 font-medium">Available</p>
                </div>
                <div class="glass-card p-6 text-center text-white hover:bg-white/15 transition-default">
                    <div class="w-14 h-14 rounded-xl bg-white/10 flex items-center justify-center mx-auto mb-4">
                        <i class="fas fa-hospital text-2xl text-hemo-gold"></i>
                    </div>
                    <p class="text-3xl font-bold mb-1">100+</p>
                    <p class="text-sm text-white/70 font-medium">Partner Hospitals</p>
                </div>
                <div class="glass-card p-6 text-center text-white hover:bg-white/15 transition-default">
                    <div class="w-14 h-14 rounded-xl bg-white/10 flex items-center justify-center mx-auto mb-4">
                        <i class="fas fa-heart text-2xl text-hemo-gold"></i>
                    </div>
                    <p class="text-3xl font-bold mb-1">5K+</p>
                    <p class="text-sm text-white/70 font-medium">Lives Saved</p>
                </div>
            </div>
        </div>
    </div>
</section>

<!-- Features Section -->
<section class="py-24 bg-white">
    <div class="max-w-7xl mx-auto px-6">
        <div class="text-center mb-16">
            <p class="text-sm font-semibold text-hemo-red uppercase tracking-widest mb-4">Why HemoLink</p>
            <h3 class="font-display text-4xl font-bold text-hemo-navy mb-4">A Smarter Blood Bank</h3>
            <p class="text-hemo-charcoal max-w-2xl mx-auto">Our platform provides end-to-end blood bank management with real-time inventory tracking, automated testing workflows, and instant hospital coordination.</p>
        </div>

        <div class="grid md:grid-cols-3 gap-8">
            <!-- Feature 1 -->
            <div class="group p-8 rounded-2xl bg-hemo-off-white hover:bg-white hover:shadow-card-hover transition-default border border-transparent hover:border-hemo-border">
                <div class="w-16 h-16 rounded-2xl gradient-red flex items-center justify-center mb-6 group-hover:scale-110 transition-default">
                    <i class="fas fa-users text-white text-2xl"></i>
                </div>
                <h4 class="text-xl font-bold text-hemo-navy mb-3">For Donors</h4>
                <p class="text-hemo-charcoal leading-relaxed">Track your donation history, schedule appointments, and see the direct impact of your contributions. Every drop makes a difference.</p>
            </div>

            <!-- Feature 2 -->
            <div class="group p-8 rounded-2xl bg-hemo-off-white hover:bg-white hover:shadow-card-hover transition-default border border-transparent hover:border-hemo-border">
                <div class="w-16 h-16 rounded-2xl bg-hemo-navy flex items-center justify-center mb-6 group-hover:scale-110 transition-default">
                    <i class="fas fa-hospital text-white text-2xl"></i>
                </div>
                <h4 class="text-xl font-bold text-hemo-navy mb-3">For Hospitals</h4>
                <p class="text-hemo-charcoal leading-relaxed">Request blood units instantly, track fulfillment in real-time, and manage your hospital's blood requirements with full transparency.</p>
            </div>

            <!-- Feature 3 -->
            <div class="group p-8 rounded-2xl bg-hemo-off-white hover:bg-white hover:shadow-card-hover transition-default border border-transparent hover:border-hemo-border">
                <div class="w-16 h-16 rounded-2xl bg-hemo-gold flex items-center justify-center mb-6 group-hover:scale-110 transition-default">
                    <i class="fas fa-shield-halved text-hemo-navy text-2xl"></i>
                </div>
                <h4 class="text-xl font-bold text-hemo-navy mb-3">Safe & Secure</h4>
                <p class="text-hemo-charcoal leading-relaxed">Full audit trail, role-based access control, and comprehensive blood testing workflows ensure safety at every step.</p>
            </div>
        </div>
    </div>
</section>

<!-- CTA Section -->
<section class="py-24 gradient-red-radial relative overflow-hidden">
    <div class="absolute inset-0 bg-[url('data:image/svg+xml,%3Csvg%20width%3D%2260%22%20height%3D%2260%22%20viewBox%3D%220%200%2060%2060%22%20xmlns%3D%22http%3A%2F%2Fwww.w3.org%2F2000%2Fsvg%22%3E%3Cpath%20d%3D%22M54.627%200l.83.828-1.415%201.415L51.8%200h2.827zM5.373%200l-.83.828L5.96%202.243%208.2%200H5.374zM48.97%200l.83.828-1.414%201.414-2.36-2.243L48.972%200zm-9.08%200l.828.828-1.414%201.414-2.36-2.243L39.89%200zm-9.082%200l.83.828-1.416%201.414-2.358-2.243L30.81%200zm-9.08%200l.828.828-1.414%201.414-2.36-2.243L21.728%200zm-9.08%200l.828.828L12.06%202.243%209.7%200h2.827z%22%20fill%3D%22rgba(255%2C255%2C255%2C0.03)%22%20fill-rule%3D%22evenodd%22%2F%3E%3C%2Fsvg%3E')] opacity-50"></div>
    <div class="max-w-4xl mx-auto px-6 text-center relative z-10">
        <h3 class="font-display text-4xl lg:text-5xl font-bold text-white mb-6">Ready to Save Lives?</h3>
        <p class="text-xl text-white/80 font-light mb-10 max-w-2xl mx-auto">Join HemoLink today and be part of a network that's transforming blood donation management across the nation.</p>
        <div class="flex flex-wrap justify-center gap-4">
            <a href="<?php echo BASE_URL; ?>/index.php?page=register_donor" 
               class="inline-flex items-center gap-3 px-10 py-4 rounded-xl bg-white text-hemo-red font-bold text-lg hover:bg-hemo-gold hover:text-hemo-navy transition-default btn-press shadow-xl">
                <i class="fas fa-heart-pulse"></i> Start Donating
            </a>
            <a href="<?php echo BASE_URL; ?>/index.php?page=login" 
               class="inline-flex items-center gap-3 px-10 py-4 rounded-xl glass-card text-white font-bold text-lg hover:bg-white/20 transition-default btn-press">
                <i class="fas fa-right-to-bracket"></i> Sign In
            </a>
        </div>
    </div>
</section>

<!-- Footer -->
<footer class="bg-hemo-navy text-white py-16">
    <div class="max-w-7xl mx-auto px-6">
        <div class="grid md:grid-cols-4 gap-10">
            <div class="md:col-span-2">
                <div class="flex items-center gap-3 mb-4">
                    <div class="w-10 h-10 rounded-lg gradient-red flex items-center justify-center">
                        <i class="fas fa-droplet text-white text-lg"></i>
                    </div>
                    <h2 class="font-display text-2xl font-bold">Hemo<span class="text-hemo-gold">Link</span></h2>
                </div>
                <p class="text-gray-400 text-sm leading-relaxed max-w-sm">Connecting donors and hospitals through modern blood bank management technology. Every drop saved is a life preserved.</p>
            </div>
            <div>
                <h4 class="font-semibold text-sm uppercase tracking-wider mb-4 text-hemo-gold">Quick Links</h4>
                <ul class="space-y-3 text-sm text-gray-400">
                    <li><a href="<?php echo BASE_URL; ?>/index.php?page=register_donor" class="hover:text-white transition-fast">Become a Donor</a></li>
                    <li><a href="<?php echo BASE_URL; ?>/index.php?page=register_hospital" class="hover:text-white transition-fast">Register Hospital</a></li>
                    <li><a href="<?php echo BASE_URL; ?>/index.php?page=login" class="hover:text-white transition-fast">Sign In</a></li>
                </ul>
            </div>
            <div>
                <h4 class="font-semibold text-sm uppercase tracking-wider mb-4 text-hemo-gold">Contact</h4>
                <ul class="space-y-3 text-sm text-gray-400">
                    <li class="flex items-center gap-2"><i class="fas fa-envelope w-4"></i> info@hemolink.org</li>
                    <li class="flex items-center gap-2"><i class="fas fa-phone w-4"></i> +254 797 574 302</li>
                    <li class="flex items-center gap-2"><i class="fas fa-location-dot w-4"></i> Nairobi, Kenya</li>
                </ul>
            </div>
        </div>
        <div class="border-t border-gray-700 mt-12 pt-8 text-center text-gray-500 text-sm">
            <p>&copy; <?php echo date('Y'); ?> HemoLink Blood Bank Management System. All rights reserved.</p>
        </div>
    </div>
</footer>

<?php require_once __DIR__ . '/layouts/footer.php'; ?>
