<?php
/**
 * Admin: Donors Management View
 */
$pageTitle = 'Manage Donors';

ob_start();
?>

<!-- Page Header -->
<div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4 mb-8">
    <div>
        <h1 class="font-display text-[32px] font-bold text-hemo-navy">Donors</h1>
    </div>
    <div class="flex items-center gap-3">
        <button onclick="openModal('addDonorModal')" class="inline-flex items-center gap-2 px-4 py-2 rounded-lg bg-hemo-red text-white font-semibold text-sm hover:bg-hemo-deep-red transition-default shadow-button">
            <i class="fas fa-plus"></i> Add New Donor
        </button>
    </div>
</div>

<!-- Stats Grid -->
<div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-6 mb-8">
    <!-- Total Donors -->
    <div class="stat-card bg-white rounded-xl shadow-card p-5 border-l-4 border-hemo-red transition-default cursor-default">
        <div class="flex items-center justify-between mb-4">
            <div class="w-12 h-12 rounded-xl bg-hemo-light-red flex items-center justify-center">
                <i class="fas fa-users text-hemo-red text-lg"></i>
            </div>
        </div>
        <p class="text-3xl font-bold text-hemo-navy"><?php echo number_format($stats['total'] ?? 0); ?></p>
        <p class="text-sm text-hemo-gray mt-1">Total Donors</p>
    </div>

    <!-- Active Donors -->
    <div class="stat-card bg-white rounded-xl shadow-card p-5 border-l-4 border-hemo-success transition-default cursor-default">
        <div class="flex items-center justify-between mb-4">
            <div class="w-12 h-12 rounded-xl bg-green-50 flex items-center justify-center">
                <i class="fas fa-user-check text-hemo-success text-lg"></i>
            </div>
        </div>
        <p class="text-3xl font-bold text-hemo-navy"><?php echo number_format($stats['active'] ?? 0); ?></p>
        <p class="text-sm text-hemo-gray mt-1">Active Accounts</p>
    </div>

    <!-- Eligible Donors -->
    <div class="stat-card bg-white rounded-xl shadow-card p-5 border-l-4 border-blue-500 transition-default cursor-default">
        <div class="flex items-center justify-between mb-4">
            <div class="w-12 h-12 rounded-xl bg-blue-50 flex items-center justify-center">
                <i class="fas fa-check-circle text-blue-500 text-lg"></i>
            </div>
        </div>
        <p class="text-3xl font-bold text-hemo-navy"><?php echo number_format($stats['eligible'] ?? 0); ?></p>
        <p class="text-sm text-hemo-gray mt-1">Eligible to Donate</p>
    </div>

    <!-- Have Donated -->
    <div class="stat-card bg-white rounded-xl shadow-card p-5 border-l-4 border-hemo-gold transition-default cursor-default">
        <div class="flex items-center justify-between mb-4">
            <div class="w-12 h-12 rounded-xl bg-amber-50 flex items-center justify-center">
                <i class="fas fa-hand-holding-medical text-hemo-gold text-lg"></i>
            </div>
        </div>
        <p class="text-3xl font-bold text-hemo-navy"><?php echo number_format($stats['have_donated'] ?? 0); ?></p>
        <p class="text-sm text-hemo-gray mt-1">Have Donated</p>
    </div>
</div>

<!-- Filters & Table -->
<div class="bg-white rounded-xl shadow-card overflow-hidden">
    <div class="p-6 border-b border-hemo-border flex flex-col lg:flex-row lg:items-center justify-between gap-4">
        <div class="flex items-center gap-3">
            <i class="fas fa-list text-hemo-red"></i>
            <h3 class="text-lg font-semibold text-hemo-navy">Donor Directory</h3>
        </div>
        
        <!-- Filters -->
        <div class="flex flex-wrap items-center gap-3">
            <div class="relative">
                <i class="fas fa-search absolute left-3 top-1/2 -translate-y-1/2 text-hemo-gray text-xs"></i>
                <input type="text" id="searchDonor" placeholder="Search donors..." class="pl-9 pr-4 py-2 bg-hemo-light-gray border-none rounded-lg text-sm focus:ring-2 focus:ring-hemo-red/20 w-full sm:w-48 transition-fast">
            </div>
            
            <select id="filterBloodType" class="px-4 py-2 bg-hemo-light-gray border-none rounded-lg text-sm focus:ring-2 focus:ring-hemo-red/20 text-hemo-charcoal">
                <option value="">All Blood Types</option>
                <?php foreach ($bloodTypes as $bt): ?>
                    <option value="<?php echo sanitize($bt['type_name']); ?>"><?php echo sanitize($bt['type_name']); ?></option>
                <?php endforeach; ?>
            </select>
            
            <select id="filterEligibility" class="px-4 py-2 bg-hemo-light-gray border-none rounded-lg text-sm focus:ring-2 focus:ring-hemo-red/20 text-hemo-charcoal">
                <option value="">All Statuses</option>
                <option value="Eligible">Eligible</option>
                <option value="Deferred">Deferred</option>
                <option value="Permanently Deferred">Permanently Deferred</option>
            </select>
        </div>
    </div>
    
    <div class="overflow-x-auto">
        <table class="w-full text-left border-collapse" id="donorsTable">
            <thead>
                <tr class="bg-hemo-light-gray">
                    <th class="px-6 py-4 text-xs font-semibold text-hemo-gray uppercase tracking-wider">Donor</th>
                    <th class="px-6 py-4 text-xs font-semibold text-hemo-gray uppercase tracking-wider">Blood Type</th>
                    <th class="px-6 py-4 text-xs font-semibold text-hemo-gray uppercase tracking-wider">Demographics</th>
                    <th class="px-6 py-4 text-xs font-semibold text-hemo-gray uppercase tracking-wider">Eligibility</th>
                    <th class="px-6 py-4 text-xs font-semibold text-hemo-gray uppercase tracking-wider">Account</th>
                    <th class="px-6 py-4 text-xs font-semibold text-hemo-gray uppercase tracking-wider text-right">Actions</th>
                </tr>
            </thead>
            <tbody class="divide-y divide-hemo-border">
                <?php if (empty($donors)): ?>
                    <tr>
                        <td colspan="6" class="px-6 py-12 text-center text-hemo-gray">
                            <i class="fas fa-users-slash text-4xl mb-3 block text-hemo-border"></i>
                            <p>No donors found in the system.</p>
                        </td>
                    </tr>
                <?php else: ?>
                    <?php foreach ($donors as $donor): ?>
                    <tr class="hover:bg-hemo-off-white transition-fast donor-row">
                        <td class="px-6 py-4">
                            <div class="flex items-center gap-3">
                                <div class="w-10 h-10 rounded-full gradient-red flex items-center justify-center text-white font-bold text-xs shadow-sm flex-shrink-0">
                                    <?php echo strtoupper(substr($donor['first_name'], 0, 1) . substr($donor['last_name'], 0, 1)); ?>
                                </div>
                                <div>
                                    <p class="text-sm font-bold text-hemo-navy leading-none donor-name"><?php echo sanitize($donor['first_name'] . ' ' . $donor['last_name']); ?></p>
                                    <div class="flex items-center gap-2 mt-1">
                                        <span class="text-[10px] font-mono text-hemo-red bg-hemo-light-red px-1.5 py-0.5 rounded">ID: D-<?php echo str_pad($donor['donor_id'], 4, '0', STR_PAD_LEFT); ?></span>
                                        <span class="text-xs text-hemo-gray donor-email"><?php echo sanitize($donor['email']); ?></span>
                                    </div>
                                </div>
                            </div>
                        </td>
                        <td class="px-6 py-4">
                            <?php if ($donor['blood_type']): ?>
                                <span class="inline-flex items-center gap-1.5 px-3 py-1 rounded-lg text-sm font-bold bg-hemo-red text-white donor-blood">
                                    <i class="fas fa-droplet text-[10px]"></i>
                                    <?php echo sanitize($donor['blood_type']); ?>
                                </span>
                            <?php else: ?>
                                <span class="text-xs text-hemo-gray italic donor-blood">Unknown</span>
                            <?php endif; ?>
                        </td>
                        <td class="px-6 py-4">
                            <div class="space-y-1">
                                <p class="text-xs text-hemo-charcoal"><i class="fas fa-venus-mars w-4 text-hemo-gray"></i> <?php echo sanitize($donor['gender'] ?? 'N/A'); ?></p>
                                <p class="text-xs text-hemo-charcoal"><i class="fas fa-cake-candles w-4 text-hemo-gray"></i> <?php echo $donor['date_of_birth'] ? date('M d, Y', strtotime($donor['date_of_birth'])) : 'N/A'; ?></p>
                            </div>
                        </td>
                        <td class="px-6 py-4">
                            <?php 
                            $eligClass = 'bg-gray-100 text-gray-700';
                            $eligDot = 'bg-gray-500';
                            if ($donor['eligibility_status'] === 'Eligible') {
                                $eligClass = 'bg-green-100 text-hemo-success';
                                $eligDot = 'bg-hemo-success';
                            } elseif ($donor['eligibility_status'] === 'Deferred') {
                                $eligClass = 'bg-amber-100 text-hemo-amber';
                                $eligDot = 'bg-hemo-amber';
                            } elseif ($donor['eligibility_status'] === 'Permanently Deferred') {
                                $eligClass = 'bg-red-100 text-hemo-warning';
                                $eligDot = 'bg-hemo-warning';
                            }
                            ?>
                            <span class="inline-flex items-center gap-1.5 px-2.5 py-1 rounded-full text-xs font-semibold <?php echo $eligClass; ?> donor-eligibility">
                                <span class="w-1.5 h-1.5 rounded-full <?php echo $eligDot; ?>"></span> 
                                <?php echo sanitize($donor['eligibility_status']); ?>
                            </span>
                        </td>
                        <td class="px-6 py-4">
                            <?php if ($donor['is_active']): ?>
                                <span class="inline-flex items-center gap-1 px-2 py-0.5 rounded text-[10px] font-semibold bg-green-50 text-hemo-success border border-green-200">Active</span>
                            <?php else: ?>
                                <span class="inline-flex items-center gap-1 px-2 py-0.5 rounded text-[10px] font-semibold bg-red-50 text-hemo-warning border border-red-200">Inactive</span>
                            <?php endif; ?>
                            <p class="text-[10px] text-hemo-gray mt-1">Joined: <?php echo date('M Y', strtotime($donor['registered_at'])); ?></p>
                        </td>
                        <td class="px-6 py-4 text-right space-x-1 whitespace-nowrap">
                            <button onclick="viewProfile(<?php echo $donor['donor_id']; ?>)" class="p-2 rounded-lg bg-hemo-light-gray text-hemo-charcoal hover:bg-blue-50 hover:text-blue-600 transition-fast" title="View Profile">
                                <i class="fas fa-eye text-sm"></i>
                            </button>
                            <button onclick="openEditModal(<?php echo htmlspecialchars(json_encode([
                                'id' => $donor['donor_id'],
                                'first_name' => $donor['first_name'],
                                'last_name' => $donor['last_name'],
                                'blood_type_id' => $donor['blood_type_id'],
                                'date_of_birth' => $donor['date_of_birth'],
                                'gender' => $donor['gender'],
                                'address' => $donor['address'],
                                'city' => $donor['city'],
                                'eligibility_status' => $donor['eligibility_status'],
                                'phone' => $donor['phone']
                            ])); ?>)" class="p-2 rounded-lg bg-hemo-light-gray text-hemo-charcoal hover:bg-amber-50 hover:text-hemo-amber transition-fast" title="Edit Donor">
                                <i class="fas fa-edit text-sm"></i>
                            </button>
                            <button onclick="viewHistory(<?php echo $donor['donor_id']; ?>)" class="p-2 rounded-lg bg-hemo-light-gray text-hemo-charcoal hover:bg-green-50 hover:text-hemo-success transition-fast" title="Donation History">
                                <i class="fas fa-clock-rotate-left text-sm"></i>
                            </button>
                            <button onclick="openDeleteModal(<?php echo $donor['donor_id']; ?>)" class="p-2 rounded-lg bg-hemo-light-gray text-hemo-charcoal hover:bg-red-50 hover:text-hemo-warning transition-fast" title="Delete Donor">
                                <i class="fas fa-trash text-sm"></i>
                            </button>
                        </td>
                    </tr>
                    <?php endforeach; ?>
                <?php endif; ?>
            </tbody>
        </table>
    </div>
</div>

<!-- ================= MODALS ================= -->

<!-- Add Donor Modal -->
<div id="addDonorModal" class="fixed inset-0 bg-hemo-navy/50 backdrop-blur-sm z-[1001] hidden flex items-center justify-center p-4 opacity-0 transition-opacity duration-300">
    <div class="bg-white rounded-xl shadow-2xl w-full max-w-2xl max-h-[90vh] flex flex-col transform scale-95 transition-transform duration-300">
        <div class="px-6 py-4 border-b border-hemo-border flex items-center justify-between bg-hemo-off-white rounded-t-xl">
            <h3 class="text-lg font-semibold text-hemo-navy"><i class="fas fa-user-plus text-hemo-red mr-2"></i> Add New Donor</h3>
            <button type="button" onclick="closeModal('addDonorModal')" class="text-hemo-gray hover:text-hemo-red transition-fast"><i class="fas fa-times text-lg"></i></button>
        </div>
        <div class="p-6 overflow-y-auto flex-1">
            <form id="addDonorForm" action="<?php echo BASE_URL; ?>/index.php?page=admin_donor_add" method="POST">
                
                <h4 class="text-sm font-semibold text-hemo-gray uppercase tracking-wider mb-4 border-b border-hemo-border pb-2">Account Info</h4>
                <div class="grid grid-cols-1 sm:grid-cols-2 gap-4 mb-6">
                    <div>
                        <label class="block text-sm font-semibold text-hemo-charcoal mb-1">Username *</label>
                        <input type="text" name="username" required class="w-full px-4 py-2 rounded-lg border-2 border-hemo-border focus:border-hemo-red focus:ring-0 transition-fast outline-none text-sm">
                    </div>
                    <div>
                        <label class="block text-sm font-semibold text-hemo-charcoal mb-1">Password *</label>
                        <input type="password" name="password" required class="w-full px-4 py-2 rounded-lg border-2 border-hemo-border focus:border-hemo-red focus:ring-0 transition-fast outline-none text-sm">
                    </div>
                    <div>
                        <label class="block text-sm font-semibold text-hemo-charcoal mb-1">Email *</label>
                        <input type="email" name="email" required class="w-full px-4 py-2 rounded-lg border-2 border-hemo-border focus:border-hemo-red focus:ring-0 transition-fast outline-none text-sm">
                    </div>
                    <div>
                        <label class="block text-sm font-semibold text-hemo-charcoal mb-1">Phone</label>
                        <input type="text" name="phone" class="w-full px-4 py-2 rounded-lg border-2 border-hemo-border focus:border-hemo-red focus:ring-0 transition-fast outline-none text-sm">
                    </div>
                </div>

                <h4 class="text-sm font-semibold text-hemo-gray uppercase tracking-wider mb-4 border-b border-hemo-border pb-2">Personal Info</h4>
                <div class="grid grid-cols-1 sm:grid-cols-2 gap-4 mb-6">
                    <div>
                        <label class="block text-sm font-semibold text-hemo-charcoal mb-1">First Name *</label>
                        <input type="text" name="first_name" required class="w-full px-4 py-2 rounded-lg border-2 border-hemo-border focus:border-hemo-red focus:ring-0 transition-fast outline-none text-sm">
                    </div>
                    <div>
                        <label class="block text-sm font-semibold text-hemo-charcoal mb-1">Last Name *</label>
                        <input type="text" name="last_name" required class="w-full px-4 py-2 rounded-lg border-2 border-hemo-border focus:border-hemo-red focus:ring-0 transition-fast outline-none text-sm">
                    </div>
                    <div>
                        <label class="block text-sm font-semibold text-hemo-charcoal mb-1">Blood Type</label>
                        <select name="blood_type_id" class="w-full px-4 py-2 rounded-lg border-2 border-hemo-border focus:border-hemo-red focus:ring-0 transition-fast outline-none text-sm bg-white">
                            <option value="">Unknown / Pending test</option>
                            <?php foreach ($bloodTypes as $bt): ?>
                                <option value="<?php echo $bt['blood_type_id']; ?>"><?php echo sanitize($bt['type_name']); ?></option>
                            <?php endforeach; ?>
                        </select>
                    </div>
                    <div>
                        <label class="block text-sm font-semibold text-hemo-charcoal mb-1">Gender</label>
                        <select name="gender" class="w-full px-4 py-2 rounded-lg border-2 border-hemo-border focus:border-hemo-red focus:ring-0 transition-fast outline-none text-sm bg-white">
                            <option value="">Select Gender</option>
                            <option value="Male">Male</option>
                            <option value="Female">Female</option>
                            <option value="Other">Other</option>
                        </select>
                    </div>
                    <div>
                        <label class="block text-sm font-semibold text-hemo-charcoal mb-1">Date of Birth</label>
                        <input type="date" name="date_of_birth" class="w-full px-4 py-2 rounded-lg border-2 border-hemo-border focus:border-hemo-red focus:ring-0 transition-fast outline-none text-sm">
                    </div>
                    <div>
                        <label class="block text-sm font-semibold text-hemo-charcoal mb-1">City</label>
                        <input type="text" name="city" class="w-full px-4 py-2 rounded-lg border-2 border-hemo-border focus:border-hemo-red focus:ring-0 transition-fast outline-none text-sm">
                    </div>
                    <div class="sm:col-span-2">
                        <label class="block text-sm font-semibold text-hemo-charcoal mb-1">Address</label>
                        <textarea name="address" rows="2" class="w-full px-4 py-2 rounded-lg border-2 border-hemo-border focus:border-hemo-red focus:ring-0 transition-fast outline-none text-sm"></textarea>
                    </div>
                </div>
            </form>
        </div>
        <div class="px-6 py-4 border-t border-hemo-border bg-hemo-off-white flex justify-end gap-3 rounded-b-xl">
            <button type="button" onclick="closeModal('addDonorModal')" class="px-4 py-2 rounded-lg bg-white border border-hemo-border text-hemo-charcoal font-semibold text-sm hover:bg-gray-50 transition-fast">Cancel</button>
            <button type="submit" form="addDonorForm" class="px-6 py-2 rounded-lg bg-hemo-red text-white font-semibold text-sm hover:bg-hemo-deep-red transition-fast shadow-button">Add Donor</button>
        </div>
    </div>
</div>

<!-- Edit Donor Modal -->
<div id="editDonorModal" class="fixed inset-0 bg-hemo-navy/50 backdrop-blur-sm z-[1001] hidden flex items-center justify-center p-4 opacity-0 transition-opacity duration-300">
    <div class="bg-white rounded-xl shadow-2xl w-full max-w-2xl max-h-[90vh] flex flex-col transform scale-95 transition-transform duration-300">
        <div class="px-6 py-4 border-b border-hemo-border flex items-center justify-between bg-hemo-off-white rounded-t-xl">
            <h3 class="text-lg font-semibold text-hemo-navy"><i class="fas fa-edit text-hemo-amber mr-2"></i> Edit Donor</h3>
            <button type="button" onclick="closeModal('editDonorModal')" class="text-hemo-gray hover:text-hemo-red transition-fast"><i class="fas fa-times text-lg"></i></button>
        </div>
        <div class="p-6 overflow-y-auto flex-1">
            <form id="editDonorForm" action="<?php echo BASE_URL; ?>/index.php?page=admin_donor_edit" method="POST">
                <input type="hidden" name="donor_id" id="edit_donor_id">
                
                <h4 class="text-sm font-semibold text-hemo-gray uppercase tracking-wider mb-4 border-b border-hemo-border pb-2">Personal Info</h4>
                <div class="grid grid-cols-1 sm:grid-cols-2 gap-4 mb-6">
                    <div>
                        <label class="block text-sm font-semibold text-hemo-charcoal mb-1">First Name *</label>
                        <input type="text" name="first_name" id="edit_first_name" required class="w-full px-4 py-2 rounded-lg border-2 border-hemo-border focus:border-hemo-amber focus:ring-0 transition-fast outline-none text-sm">
                    </div>
                    <div>
                        <label class="block text-sm font-semibold text-hemo-charcoal mb-1">Last Name *</label>
                        <input type="text" name="last_name" id="edit_last_name" required class="w-full px-4 py-2 rounded-lg border-2 border-hemo-border focus:border-hemo-amber focus:ring-0 transition-fast outline-none text-sm">
                    </div>
                    <div>
                        <label class="block text-sm font-semibold text-hemo-charcoal mb-1">Blood Type</label>
                        <select name="blood_type_id" id="edit_blood_type_id" class="w-full px-4 py-2 rounded-lg border-2 border-hemo-border focus:border-hemo-amber focus:ring-0 transition-fast outline-none text-sm bg-white">
                            <option value="">Unknown / Pending test</option>
                            <?php foreach ($bloodTypes as $bt): ?>
                                <option value="<?php echo $bt['blood_type_id']; ?>"><?php echo sanitize($bt['type_name']); ?></option>
                            <?php endforeach; ?>
                        </select>
                    </div>
                    <div>
                        <label class="block text-sm font-semibold text-hemo-charcoal mb-1">Eligibility Status</label>
                        <select name="eligibility_status" id="edit_eligibility" class="w-full px-4 py-2 rounded-lg border-2 border-hemo-border focus:border-hemo-amber focus:ring-0 transition-fast outline-none text-sm bg-white">
                            <option value="Eligible">Eligible</option>
                            <option value="Deferred">Deferred</option>
                            <option value="Permanently Deferred">Permanently Deferred</option>
                        </select>
                    </div>
                    <div>
                        <label class="block text-sm font-semibold text-hemo-charcoal mb-1">Gender</label>
                        <select name="gender" id="edit_gender" class="w-full px-4 py-2 rounded-lg border-2 border-hemo-border focus:border-hemo-amber focus:ring-0 transition-fast outline-none text-sm bg-white">
                            <option value="">Select Gender</option>
                            <option value="Male">Male</option>
                            <option value="Female">Female</option>
                            <option value="Other">Other</option>
                        </select>
                    </div>
                    <div>
                        <label class="block text-sm font-semibold text-hemo-charcoal mb-1">Date of Birth</label>
                        <input type="date" name="date_of_birth" id="edit_dob" class="w-full px-4 py-2 rounded-lg border-2 border-hemo-border focus:border-hemo-amber focus:ring-0 transition-fast outline-none text-sm">
                    </div>
                    <div>
                        <label class="block text-sm font-semibold text-hemo-charcoal mb-1">Phone</label>
                        <input type="text" name="phone" id="edit_phone" class="w-full px-4 py-2 rounded-lg border-2 border-hemo-border focus:border-hemo-amber focus:ring-0 transition-fast outline-none text-sm">
                    </div>
                    <div>
                        <label class="block text-sm font-semibold text-hemo-charcoal mb-1">City</label>
                        <input type="text" name="city" id="edit_city" class="w-full px-4 py-2 rounded-lg border-2 border-hemo-border focus:border-hemo-amber focus:ring-0 transition-fast outline-none text-sm">
                    </div>
                    <div class="sm:col-span-2">
                        <label class="block text-sm font-semibold text-hemo-charcoal mb-1">Address</label>
                        <textarea name="address" id="edit_address" rows="2" class="w-full px-4 py-2 rounded-lg border-2 border-hemo-border focus:border-hemo-amber focus:ring-0 transition-fast outline-none text-sm"></textarea>
                    </div>
                </div>
            </form>
        </div>
        <div class="px-6 py-4 border-t border-hemo-border bg-hemo-off-white flex justify-end gap-3 rounded-b-xl">
            <button type="button" onclick="closeModal('editDonorModal')" class="px-4 py-2 rounded-lg bg-white border border-hemo-border text-hemo-charcoal font-semibold text-sm hover:bg-gray-50 transition-fast">Cancel</button>
            <button type="submit" form="editDonorForm" class="px-6 py-2 rounded-lg bg-hemo-amber text-white font-semibold text-sm hover:bg-amber-600 transition-fast shadow-sm">Save Changes</button>
        </div>
    </div>
</div>

<!-- View Profile Modal -->
<div id="viewDonorModal" class="fixed inset-0 bg-hemo-navy/50 backdrop-blur-sm z-[1001] hidden flex items-center justify-center p-4 opacity-0 transition-opacity duration-300">
    <div class="bg-white rounded-xl shadow-2xl w-full max-w-lg flex flex-col transform scale-95 transition-transform duration-300">
        <div class="px-6 py-4 border-b border-hemo-border flex items-center justify-between bg-hemo-off-white rounded-t-xl">
            <h3 class="text-lg font-semibold text-hemo-navy">Donor Profile</h3>
            <button type="button" onclick="closeModal('viewDonorModal')" class="text-hemo-gray hover:text-hemo-red transition-fast"><i class="fas fa-times text-lg"></i></button>
        </div>
        <div class="p-6">
            <div id="viewLoader" class="flex justify-center py-8">
                <i class="fas fa-spinner fa-spin text-3xl text-hemo-red"></i>
            </div>
            <div id="viewContent" class="hidden">
                <!-- Content populated via JS -->
            </div>
        </div>
    </div>
</div>

<!-- View History Modal -->
<div id="historyDonorModal" class="fixed inset-0 bg-hemo-navy/50 backdrop-blur-sm z-[1001] hidden flex items-center justify-center p-4 opacity-0 transition-opacity duration-300">
    <div class="bg-white rounded-xl shadow-2xl w-full max-w-3xl max-h-[80vh] flex flex-col transform scale-95 transition-transform duration-300">
        <div class="px-6 py-4 border-b border-hemo-border flex items-center justify-between bg-hemo-off-white rounded-t-xl">
            <h3 class="text-lg font-semibold text-hemo-navy"><i class="fas fa-clock-rotate-left text-hemo-success mr-2"></i> Donation History</h3>
            <button type="button" onclick="closeModal('historyDonorModal')" class="text-hemo-gray hover:text-hemo-red transition-fast"><i class="fas fa-times text-lg"></i></button>
        </div>
        <div class="p-0 overflow-y-auto flex-1">
            <div id="historyLoader" class="flex justify-center py-12">
                <i class="fas fa-spinner fa-spin text-3xl text-hemo-red"></i>
            </div>
            <div id="historyContent" class="hidden">
                <table class="w-full text-left border-collapse">
                    <thead class="sticky top-0 bg-hemo-light-gray">
                        <tr>
                            <th class="px-6 py-3 text-xs font-semibold text-hemo-gray uppercase">Date</th>
                            <th class="px-6 py-3 text-xs font-semibold text-hemo-gray uppercase">Location/Hospital</th>
                            <th class="px-6 py-3 text-xs font-semibold text-hemo-gray uppercase">Volume (ml)</th>
                            <th class="px-6 py-3 text-xs font-semibold text-hemo-gray uppercase">Status</th>
                        </tr>
                    </thead>
                    <tbody id="historyTableBody" class="divide-y divide-hemo-border">
                        <!-- Populated via JS -->
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</div>

<!-- Delete Confirm Modal -->
<div id="deleteDonorModal" class="fixed inset-0 bg-hemo-navy/50 backdrop-blur-sm z-[1001] hidden flex items-center justify-center p-4 opacity-0 transition-opacity duration-300">
    <div class="bg-white rounded-xl shadow-2xl w-full max-w-sm flex flex-col transform scale-95 transition-transform duration-300">
        <div class="p-6 text-center">
            <div class="w-16 h-16 rounded-full bg-red-100 flex items-center justify-center mx-auto mb-4">
                <i class="fas fa-exclamation-triangle text-2xl text-hemo-warning"></i>
            </div>
            <h3 class="text-xl font-bold text-hemo-navy mb-2">Delete Donor?</h3>
            <p class="text-sm text-hemo-charcoal mb-6">Are you sure you want to delete this donor and their user account? This action cannot be undone.</p>
            
            <form action="<?php echo BASE_URL; ?>/index.php?page=admin_donor_delete" method="POST" class="flex gap-3">
                <input type="hidden" name="donor_id" id="delete_donor_id">
                <button type="button" onclick="closeModal('deleteDonorModal')" class="flex-1 py-2.5 rounded-lg bg-hemo-light-gray text-hemo-charcoal font-semibold hover:bg-gray-200 transition-fast">Cancel</button>
                <button type="submit" class="flex-1 py-2.5 rounded-lg bg-hemo-warning text-white font-semibold hover:bg-red-700 transition-fast">Yes, Delete</button>
            </form>
        </div>
    </div>
</div>

<!-- JavaScript for Modals & Filtering -->
<script>
    // Search and Filter Logic
    document.addEventListener('DOMContentLoaded', () => {
        const searchInput = document.getElementById('searchDonor');
        const bloodTypeFilter = document.getElementById('filterBloodType');
        const eligibilityFilter = document.getElementById('filterEligibility');
        const rows = document.querySelectorAll('.donor-row');

        function filterTable() {
            const searchTerm = searchInput.value.toLowerCase();
            const bloodType = bloodTypeFilter.value.toLowerCase();
            const eligibility = eligibilityFilter.value.toLowerCase();

            rows.forEach(row => {
                const name = row.querySelector('.donor-name').textContent.toLowerCase();
                const email = row.querySelector('.donor-email').textContent.toLowerCase();
                const bType = row.querySelector('.donor-blood').textContent.toLowerCase();
                const elig = row.querySelector('.donor-eligibility').textContent.toLowerCase();

                const matchesSearch = name.includes(searchTerm) || email.includes(searchTerm);
                const matchesBlood = !bloodType || bType.includes(bloodType);
                const matchesElig = !eligibility || elig.includes(eligibility);

                if (matchesSearch && matchesBlood && matchesElig) {
                    row.style.display = '';
                } else {
                    row.style.display = 'none';
                }
            });
        }

        searchInput.addEventListener('input', filterTable);
        bloodTypeFilter.addEventListener('change', filterTable);
        eligibilityFilter.addEventListener('change', filterTable);
    });

    // Modal Helpers
    function openModal(id) {
        const modal = document.getElementById(id);
        modal.classList.remove('hidden');
        // trigger reflow
        void modal.offsetWidth;
        modal.classList.remove('opacity-0');
        modal.querySelector('div').classList.remove('scale-95');
    }

    function closeModal(id) {
        const modal = document.getElementById(id);
        modal.classList.add('opacity-0');
        modal.querySelector('div').classList.add('scale-95');
        setTimeout(() => {
            modal.classList.add('hidden');
        }, 300);
    }

    // Edit Modal Populator
    function openEditModal(donor) {
        document.getElementById('edit_donor_id').value = donor.id;
        document.getElementById('edit_first_name').value = donor.first_name || '';
        document.getElementById('edit_last_name').value = donor.last_name || '';
        document.getElementById('edit_blood_type_id').value = donor.blood_type_id || '';
        document.getElementById('edit_gender').value = donor.gender || '';
        document.getElementById('edit_dob').value = donor.date_of_birth || '';
        document.getElementById('edit_phone').value = donor.phone || '';
        document.getElementById('edit_city').value = donor.city || '';
        document.getElementById('edit_address').value = donor.address || '';
        document.getElementById('edit_eligibility').value = donor.eligibility_status || 'Eligible';
        openModal('editDonorModal');
    }

    function openDeleteModal(id) {
        document.getElementById('delete_donor_id').value = id;
        openModal('deleteDonorModal');
    }

    // AJAX for View Profile
    async function viewProfile(id) {
        openModal('viewDonorModal');
        document.getElementById('viewLoader').classList.remove('hidden');
        document.getElementById('viewContent').classList.add('hidden');

        try {
            const res = await fetch(`<?php echo BASE_URL; ?>/index.php?page=admin_donor_view&donor_id=${id}`);
            const data = await res.json();
            
            if (data.donor) {
                const d = data.donor;
                const s = data.stats;
                
                const initials = (d.first_name[0] + d.last_name[0]).toUpperCase();
                const bType = d.blood_type ? d.blood_type : 'Unknown';
                
                document.getElementById('viewContent').innerHTML = `
                    <div class="flex items-center gap-6 mb-6">
                        <div class="w-20 h-20 rounded-full gradient-red flex items-center justify-center text-white text-2xl font-bold shadow-lg flex-shrink-0">
                            ${initials}
                        </div>
                        <div>
                            <h4 class="text-xl font-bold text-hemo-navy">${d.first_name} ${d.last_name}</h4>
                            <p class="text-sm text-hemo-gray font-mono mb-2">ID: D-${String(d.donor_id).padStart(4, '0')}</p>
                            <span class="inline-flex items-center gap-1.5 px-3 py-1 rounded-lg text-sm font-bold bg-hemo-red text-white">
                                <i class="fas fa-droplet"></i> ${bType}
                            </span>
                        </div>
                    </div>
                    
                    <div class="grid grid-cols-2 gap-y-4 gap-x-6 mb-6">
                        <div><span class="text-xs text-hemo-gray block mb-1">Email</span><span class="text-sm font-semibold">${d.email}</span></div>
                        <div><span class="text-xs text-hemo-gray block mb-1">Phone</span><span class="text-sm font-semibold">${d.phone || 'N/A'}</span></div>
                        <div><span class="text-xs text-hemo-gray block mb-1">Date of Birth</span><span class="text-sm font-semibold">${d.date_of_birth || 'N/A'}</span></div>
                        <div><span class="text-xs text-hemo-gray block mb-1">Gender</span><span class="text-sm font-semibold">${d.gender || 'N/A'}</span></div>
                        <div class="col-span-2"><span class="text-xs text-hemo-gray block mb-1">Address</span><span class="text-sm font-semibold">${d.address || 'N/A'}, ${d.city || ''}</span></div>
                    </div>
                    
                    <div class="bg-hemo-light-gray rounded-xl p-4 grid grid-cols-3 text-center divide-x divide-hemo-border">
                        <div>
                            <p class="text-2xl font-bold text-hemo-navy">${s.total_donations}</p>
                            <p class="text-xs text-hemo-gray">Donations</p>
                        </div>
                        <div>
                            <p class="text-2xl font-bold text-hemo-navy">${s.total_volume_ml}</p>
                            <p class="text-xs text-hemo-gray">Total ml</p>
                        </div>
                        <div>
                            <p class="text-sm font-bold text-hemo-navy mt-1">${s.last_donation_date ? s.last_donation_date : 'Never'}</p>
                            <p class="text-xs text-hemo-gray mt-1">Last Donated</p>
                        </div>
                    </div>
                `;
            }
        } catch (e) {
            document.getElementById('viewContent').innerHTML = `<div class="text-center text-hemo-warning py-4">Failed to load donor data.</div>`;
        }
        
        document.getElementById('viewLoader').classList.add('hidden');
        document.getElementById('viewContent').classList.remove('hidden');
    }

    // AJAX for History
    async function viewHistory(id) {
        openModal('historyDonorModal');
        document.getElementById('historyLoader').classList.remove('hidden');
        document.getElementById('historyContent').classList.add('hidden');

        try {
            const res = await fetch(`<?php echo BASE_URL; ?>/index.php?page=admin_donor_history&donor_id=${id}`);
            const data = await res.json();
            
            const tbody = document.getElementById('historyTableBody');
            tbody.innerHTML = '';
            
            if (data.history && data.history.length > 0) {
                data.history.forEach(h => {
                    let statusClass = h.status === 'Completed' ? 'bg-green-100 text-hemo-success' : 
                                     (h.status === 'Pending' ? 'bg-amber-100 text-hemo-amber' : 'bg-red-100 text-hemo-warning');
                    
                    tbody.innerHTML += `
                        <tr class="hover:bg-hemo-off-white">
                            <td class="px-6 py-4 text-sm font-semibold text-hemo-navy">${h.donation_date}</td>
                            <td class="px-6 py-4 text-sm text-hemo-charcoal">${h.location || 'Blood Bank Center'}</td>
                            <td class="px-6 py-4 text-sm text-hemo-charcoal font-mono">${h.volume_ml || 0}</td>
                            <td class="px-6 py-4">
                                <span class="inline-flex items-center px-2.5 py-1 rounded-full text-xs font-semibold ${statusClass}">
                                    ${h.status}
                                </span>
                            </td>
                        </tr>
                    `;
                });
            } else {
                tbody.innerHTML = `<tr><td colspan="4" class="px-6 py-12 text-center text-hemo-gray italic">No donation history found for this donor.</td></tr>`;
            }
        } catch (e) {
            document.getElementById('historyTableBody').innerHTML = `<tr><td colspan="4" class="px-6 py-8 text-center text-hemo-warning">Failed to load history.</td></tr>`;
        }
        
        document.getElementById('historyLoader').classList.add('hidden');
        document.getElementById('historyContent').classList.remove('hidden');
    }
</script>

<?php
$content = ob_get_clean();
require_once __DIR__ . '/../layouts/dashboard_layout.php';
?>
