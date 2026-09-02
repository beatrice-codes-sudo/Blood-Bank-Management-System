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
                            switch($user['role']) {
                                case 'Admin': 
                                    $roleClass = 'bg-purple-100 text-purple-700'; 
                                    $roleIcon = 'fa-shield-halved';
                                    break;
                                case 'Hospital': 
                                    $roleClass = 'bg-blue-100 text-blue-700'; 
                                    $roleIcon = 'fa-hospital';
                                    break;
                                case 'Donor': 
                                    $roleClass = 'bg-red-100 text-hemo-red'; 
                                    $roleIcon = 'fa-heart';
                                    break;
                            }
                            ?>
                            <span class="inline-flex items-center gap-1.5 px-2.5 py-1 rounded-full text-[11px] font-bold <?php echo $roleClass; ?> uppercase tracking-tight">
                                <i class="fas <?php echo $roleIcon; ?> text-[9px]"></i>
                                <?php echo sanitize($user['role']); ?>
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
                                <span class="inline-flex items-center px-2.5 py-1 rounded-full text-xs font-semibold bg-green-100 text-hemo-success">Active</span>
                            <?php else: ?>
                                <span class="inline-flex items-center px-2.5 py-1 rounded-full text-xs font-semibold bg-red-100 text-hemo-warning">Inactive</span>
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
                            <button onclick="viewUserDetails(<?php echo htmlspecialchars(json_encode([
                                'id' => $user['user_id'],
                                'name' => $user['first_name'] . ' ' . $user['last_name'],
                                'username' => $user['username'],
                                'role' => $user['role'],
                                'email' => $user['email'],
                                'phone' => $user['phone'] ?? 'N/A',
                                'status' => $user['is_active'] ? 'Active' : 'Inactive',
                                'created_at' => date('M d, Y H:i', strtotime($user['created_at'])),
                                'last_login' => $user['last_login'] ? date('M d, Y H:i', strtotime($user['last_login'])) : 'Never logged in'
                            ])); ?>)" class="p-2 rounded-lg hover:bg-blue-50 text-hemo-gray hover:text-blue-600 transition-fast" title="View Details">
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

<!-- View User Details Modal -->
<div id="viewUserModal" class="fixed inset-0 bg-hemo-navy/50 backdrop-blur-sm z-[1001] hidden flex items-center justify-center p-4 opacity-0 transition-opacity duration-300">
    <div class="bg-white rounded-2xl shadow-2xl w-full max-w-lg overflow-hidden transform scale-95 transition-transform duration-300">
        <div class="px-6 py-4 border-b border-hemo-border flex items-center justify-between bg-hemo-off-white">
            <h3 class="text-lg font-bold text-hemo-navy flex items-center gap-2">
                <i class="fas fa-user-circle text-hemo-red"></i> User Account Details
            </h3>
            <button type="button" onclick="closeModal('viewUserModal')" class="text-hemo-gray hover:text-hemo-red transition-fast">
                <i class="fas fa-times text-lg"></i>
            </button>
        </div>
        <div class="p-6 space-y-6">
            <!-- Header Profile Info -->
            <div class="flex items-center gap-4 p-4 rounded-xl bg-gray-50 border border-gray-100">
                <div id="modalUserAvatar" class="w-14 h-14 rounded-2xl gradient-red flex items-center justify-center text-white font-bold text-lg shadow-sm flex-shrink-0">
                    U
                </div>
                <div class="min-w-0 flex-1">
                    <h4 id="modalUserName" class="text-lg font-bold text-hemo-navy truncate">User Name</h4>
                    <p id="modalUserUsername" class="text-xs font-mono text-hemo-gray">@username</p>
                    <div class="mt-2 flex items-center gap-2">
                        <span id="modalUserRole" class="px-2.5 py-0.5 rounded-full text-xs font-bold uppercase tracking-tight">Role</span>
                        <span id="modalUserStatus" class="px-2.5 py-0.5 rounded-full text-xs font-semibold">Status</span>
                    </div>
                </div>
            </div>

            <!-- Details Grid -->
            <div class="grid grid-cols-2 gap-4 text-xs">
                <div class="p-3.5 rounded-xl bg-hemo-off-white border border-hemo-border">
                    <span class="text-hemo-gray block mb-1 font-semibold uppercase tracking-wider text-[10px]">Email Address</span>
                    <p id="modalUserEmail" class="font-medium text-hemo-navy break-all">user@example.com</p>
                </div>
                <div class="p-3.5 rounded-xl bg-hemo-off-white border border-hemo-border">
                    <span class="text-hemo-gray block mb-1 font-semibold uppercase tracking-wider text-[10px]">Phone Number</span>
                    <p id="modalUserPhone" class="font-medium text-hemo-navy">+254...</p>
                </div>
                <div class="p-3.5 rounded-xl bg-hemo-off-white border border-hemo-border">
                    <span class="text-hemo-gray block mb-1 font-semibold uppercase tracking-wider text-[10px]">Registered On</span>
                    <p id="modalUserCreated" class="font-medium text-hemo-navy">Date</p>
                </div>
                <div class="p-3.5 rounded-xl bg-hemo-off-white border border-hemo-border">
                    <span class="text-hemo-gray block mb-1 font-semibold uppercase tracking-wider text-[10px]">Last Login</span>
                    <p id="modalUserLogin" class="font-medium text-hemo-navy">Date</p>
                </div>
            </div>

            <div class="flex justify-end pt-2">
                <button type="button" onclick="closeModal('viewUserModal')" class="px-5 py-2.5 rounded-xl bg-hemo-light-gray text-hemo-charcoal font-semibold text-xs hover:bg-gray-200 transition-fast">
                    Close
                </button>
            </div>
        </div>
    </div>
</div>

<script>
function openModal(id) {
    const modal = document.getElementById(id);
    if (!modal) return;
    modal.classList.remove('hidden');
    setTimeout(() => {
        modal.classList.remove('opacity-0');
        const content = modal.querySelector('.transform');
        if (content) content.classList.remove('scale-95');
    }, 10);
}

function closeModal(id) {
    const modal = document.getElementById(id);
    if (!modal) return;
    modal.classList.add('opacity-0');
    const content = modal.querySelector('.transform');
    if (content) content.classList.add('scale-95');
    setTimeout(() => modal.classList.add('hidden'), 300);
}

function viewUserDetails(u) {
    document.getElementById('modalUserName').textContent = u.name;
    document.getElementById('modalUserUsername').textContent = '@' + u.username;
    document.getElementById('modalUserEmail').textContent = u.email;
    document.getElementById('modalUserPhone').textContent = u.phone;
    document.getElementById('modalUserCreated').textContent = u.created_at;
    document.getElementById('modalUserLogin').textContent = u.last_login;

    const initials = u.name.split(' ').map(n => n.charAt(0)).join('').substring(0, 2).toUpperCase();
    document.getElementById('modalUserAvatar').textContent = initials || 'U';

    const roleBadge = document.getElementById('modalUserRole');
    roleBadge.textContent = u.role;
    if (u.role === 'Admin') {
        roleBadge.className = 'px-2.5 py-0.5 rounded-full text-xs font-bold uppercase tracking-tight bg-purple-100 text-purple-700';
    } else if (u.role === 'Hospital') {
        roleBadge.className = 'px-2.5 py-0.5 rounded-full text-xs font-bold uppercase tracking-tight bg-blue-100 text-blue-700';
    } else {
        roleBadge.className = 'px-2.5 py-0.5 rounded-full text-xs font-bold uppercase tracking-tight bg-red-100 text-hemo-red';
    }

    const statusBadge = document.getElementById('modalUserStatus');
    statusBadge.textContent = u.status;
    if (u.status === 'Active') {
        statusBadge.className = 'px-2.5 py-0.5 rounded-full text-xs font-semibold bg-green-100 text-hemo-success';
    } else {
        statusBadge.className = 'px-2.5 py-0.5 rounded-full text-xs font-semibold bg-red-100 text-hemo-warning';
    }

    openModal('viewUserModal');
}
</script>

<?php
$content = ob_get_clean();
require_once __DIR__ . '/../layouts/dashboard_layout.php';
?>
