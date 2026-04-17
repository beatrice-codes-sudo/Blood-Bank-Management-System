<?php
/**
 * Admin Controller
 * Handles admin dashboard and system management
 */
class AdminController {
    private $userModel;
    private $donorModel;
    private $hospitalModel;
    private $bloodInventory;

    public function __construct() {
        $this->userModel = new User();
        $this->donorModel = new DonorModel();
        $this->hospitalModel = new HospitalModel();
        $this->bloodInventory = new BloodInventory();
    }

    /**
     * Show admin dashboard
     */
    public function dashboard() {
        requireRole(ROLE_ADMIN);

        $data = [
            'total_donors'    => $this->donorModel->countAll(),
            'total_hospitals' => $this->hospitalModel->countAll(),
            'total_units'     => $this->bloodInventory->getTotalUnits(),
            'pending_requests'=> $this->bloodInventory->getPendingRequests(),
            'inventory'       => $this->bloodInventory->getInventorySummary(),
            'recent_users'    => $this->userModel->getRecentRegistrations(8),
            'critical_stock'  => $this->bloodInventory->getCriticalStock(5),
        ];

        require_once __DIR__ . '/../views/admin/dashboard.php';
    }

    /**
     * Manage users
     */
    public function manageUsers() {
        requireRole(ROLE_ADMIN);

        $users = $this->userModel->getAll();
        require_once __DIR__ . '/../views/admin/users.php';
    }

    /**
     * Toggle user status
     */
    public function toggleUserStatus() {
        requireRole(ROLE_ADMIN);

        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $userId = (int)($_POST['user_id'] ?? 0);
            $status = (int)($_POST['status'] ?? 0);
            $this->userModel->updateStatus($userId, $status);
            redirect('admin_dashboard', 'User status updated', 'success');
        }
    }
}
