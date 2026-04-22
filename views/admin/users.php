<?php
/**
 * Admin: User Management View
 */
$pageTitle = 'Manage Users';

// Start output buffering
ob_start();
?>

<!-- Page Header -->
<div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4 mb-8">
    <div>
        <h1 class="font-display text-[32px] font-bold text-hemo-navy">User Management</h1>
        <p class="text-hemo-charcoal mt-1">Manage all registered accounts and system access.</p>
    </div>
    <div class="flex items-center gap-3">
        <a href="<?php echo BASE_URL; ?>/index.php?page=admin_dashboard" class="inline-flex items-center gap-2 px-4 py-2 rounded-lg bg-hemo-light-red text-hemo-red font-semibold text-sm hover:bg-hemo-red hover:text-white transition-default">
            <i class="fas fa-arrow-left"></i> Dashboard
        </a>
    </div>
</div>

<!-- Users Table -->
<div class="bg-white rounded-xl shadow-card overflow-hidden">
    <div class="p-6 border-b border-hemo-border flex flex-col sm:flex-row sm:items-center justify-between gap-4">
        <div class="flex items-center gap-3">
            <i class="fas fa-users text-hemo-red"></i>
            <h3 class="text-lg font-semibold text-hemo-navy">All System Users</h3>
        </div>
        <!-- Search/Filter Placeholder -->
        <div class="relative">
            <i class="fas fa-search absolute left-3 top-1/2 -translate-y-1/2 text-hemo-gray text-xs"></i>
            <input type="text" placeholder="Search users..." class="pl-9 pr-4 py-2 bg-hemo-light-gray border-none rounded-lg text-sm focus:ring-2 focus:ring-hemo-red/20 w-full sm:w-64 transition-fast">
        </div>
    </div>
    
    <div class="overflow-x-auto">
        <table class="w-full text-left border-collapse">
            <thead>
                <tr class="bg-hemo-light-gray">
                    <th class="px-6 py-4 text-xs font-semibold text-hemo-gray uppercase tracking-wider">User Details</th>
                    <th class="px-6 py-4 text-xs font-semibold text-hemo-gray uppercase tracking-wider">Role</th>
                    <th class="px-6 py-4 text-xs font-semibold text-hemo-gray uppercase tracking-wider">Contact</th>
                    <th class="px-6 py-4 text-xs font-semibold text-hemo-gray uppercase tracking-wider">Status</th>
                    <th class="px-6 py-4 text-xs font-semibold text-hemo-gray uppercase tracking-wider">Last Login</th>
                    <th class="px-6 py-4 text-xs font-semibold text-hemo-gray uppercase tracking-wider text-right">Actions</th>
                </tr>
            </thead>
            <tbody class="divide-y divide-hemo-border">
                <?php if (empty($users)): ?>
                    <tr>
                        <td colspan="6" class="px-6 py-12 text-center text-hemo-gray">
                            <i class="fas fa-user-slash text-4xl mb-3 block text-hemo-border"></i>
                            <p>No users found in the system.</p>
                        </td>
                    </tr>
                <?php else: ?>
                    <?php foreach ($users as $user): ?>
                    <tr class="hover:bg-hemo-off-white transition-fast">
                        <td class="px-6 py-4">
                            <div class="flex items-center gap-3">
                                <div class="w-10 h-10 rounded-full gradient-red flex items-center justify-center text-white font-bold text-xs shadow-sm">
                                    <?php echo strtoupper(substr($user['first_name'], 0, 1) . substr($user['last_name'], 0, 1)); ?>
                                </div>
                                <div>
                                    <p class="text-sm font-bold text-hemo-navy leading-none"><?php echo sanitize($user['first_name'] . ' ' . $user['last_name']); ?></p>
                                    <p class="text-xs text-hemo-gray mt-1 font-mono">@<?php echo sanitize($user['username']); ?></p>
                                </div>
                            </div>
                        </td>
                        <td class="px-6 py-4">
                            <?php 
                            $roleClass = '';
                            $roleIcon = '';
                            switch($user['role_name']) {
                                case 'admin': 
                                    $roleClass = 'bg-purple-100 text-purple-700'; 
                                    $roleIcon = 'fa-shield-halved';
                                    break;
                                case 'hospital_manager': 
                                    $roleClass = 'bg-blue-100 text-blue-700'; 
                                    $roleIcon = 'fa-hospital';
                                    break;
                                case 'donor': 
                                    $roleClass = 'bg-red-100 text-hemo-red'; 
                                    $roleIcon = 'fa-heart';
                                    break;
                            }
                            ?>
                            <span class="inline-flex items-center gap-1.5 px-2.5 py-1 rounded-full text-[11px] font-bold <?php echo $roleClass; ?> uppercase tracking-tight">
                                <i class="fas <?php echo $roleIcon; ?> text-[9px]"></i>
                                <?php echo sanitize(str_replace('_', ' ', $user['role_name'])); ?>
                            </span>
                        </td>
                        <td class="px-6 py-4">
                            <div class="space-y-1">
                                <div class="flex items-center gap-2 text-xs text-hemo-charcoal">
                                    <i class="fas fa-envelope w-4 text-hemo-red"></i>
                                    <?php echo sanitize($user['email']); ?>
                                </div>
                                <div class="flex items-center gap-2 text-xs text-hemo-gray">
                                    <i class="fas fa-phone w-4"></i>
                                    <?php echo sanitize($user['phone'] ?? 'N/A'); ?>
                                </div>
                            </div>
                        </td>
                        <td class="px-6 py-4">
                            <?php if ($user['is_active']): ?>
                                <span class="inline-flex items-center gap-1.5 px-2.5 py-1 rounded-full text-xs font-semibold bg-green-100 text-hemo-success">
                                    <span class="w-1.5 h-1.5 rounded-full bg-hemo-success"></span> Active
                                </span>
                            <?php else: ?>
                                <span class="inline-flex items-center gap-1.5 px-2.5 py-1 rounded-full text-xs font-semibold bg-red-100 text-hemo-warning">
                                    <span class="w-1.5 h-1.5 rounded-full bg-hemo-warning"></span> Inactive
                                </span>
                            <?php endif; ?>
                        </td>
                        <td class="px-6 py-4 text-xs text-hemo-gray">
                            <?php echo $user['last_login'] ? date('M d, Y H:i', strtotime($user['last_login'])) : 'Never logged in'; ?>
                        </td>
                        <td class="px-6 py-4 text-right">
                            <?php if ($user['username'] !== $_SESSION['username']): // Prevent self-disabling ?>
                            <form action="<?php echo BASE_URL; ?>/index.php?page=admin_toggle_user" method="POST" class="inline">
                                <input type="hidden" name="user_id" value="<?php echo $user['user_id']; ?>">
                                <input type="hidden" name="status" value="<?php echo $user['is_active'] ? 0 : 1; ?>">
                                <button type="submit" class="p-2 rounded-lg transition-fast <?php echo $user['is_active'] ? 'hover:bg-red-50 text-hemo-warning' : 'hover:bg-green-50 text-hemo-success'; ?>" title="<?php echo $user['is_active'] ? 'Deactivate User' : 'Activate User'; ?>">
                                    <i class="fas <?php echo $user['is_active'] ? 'fa-user-minus' : 'fa-user-check'; ?> text-sm"></i>
                                </button>
                            </form>
                            <?php endif; ?>
                            <button class="p-2 rounded-lg hover:bg-hemo-light-red text-hemo-gray hover:text-hemo-red transition-fast" title="View Details">
                                <i class="fas fa-eye text-sm"></i>
                            </button>
                        </td>
                    </tr>
                    <?php endforeach; ?>
                <?php endif; ?>
            </tbody>
        </table>
    </div>
    
    <div class="p-4 bg-hemo-off-white border-t border-hemo-border text-center">
        <p class="text-xs text-hemo-gray">Showing all registered users in the HemoLink system.</p>
    </div>
</div>

<?php
$content = ob_get_clean();
require_once __DIR__ . '/../layouts/dashboard_layout.php';
?>
