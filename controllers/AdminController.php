<?php
/**
 * Admin Controller
 * Handles admin dashboard and system management
 * Post-consolidation: donor CRUD operates on users table directly
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

    // ================================================================
    // Donor Management
    // ================================================================

    /**
     * Show donors management page
     */
    public function manageDonors() {
        requireRole(ROLE_ADMIN);

        $donors = $this->donorModel->getAll(200, 0);
        $stats = $this->donorModel->getGlobalStats();
        $bloodTypes = BLOOD_TYPES;

        require_once __DIR__ . '/../views/admin/donors.php';
    }

    /**
     * View single donor details (AJAX/JSON)
     */
    public function viewDonor() {
        requireRole(ROLE_ADMIN);

        $userId = (int)($_GET['donor_id'] ?? 0);
        $donor = $this->donorModel->findById($userId);

        if (!$donor) {
            http_response_code(404);
            echo json_encode(['error' => 'Donor not found']);
            exit;
        }

        $donorStats = $this->donorModel->getStats($userId);

        header('Content-Type: application/json');
        echo json_encode([
            'donor' => $donor,
            'stats' => $donorStats,
        ]);
        exit;
    }

    /**
     * Add a new donor (POST)
     * Post-consolidation: single INSERT into users with donor fields
     */
    public function addDonor() {
        requireRole(ROLE_ADMIN);

        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            redirect('admin_donors');
        }

        // Validate required fields
        $firstName = trim($_POST['first_name'] ?? '');
        $lastName  = trim($_POST['last_name'] ?? '');
        $email     = trim($_POST['email'] ?? '');
        $username  = trim($_POST['username'] ?? '');
        $password  = trim($_POST['password'] ?? '');

        if (!$firstName || !$lastName || !$email || !$username || !$password) {
            redirect('admin_donors', 'All required fields must be filled', 'error');
        }

        // Check for duplicate email/username
        if ($this->userModel->findByEmail($email)) {
            redirect('admin_donors', 'Email already exists in the system', 'error');
        }
        if ($this->userModel->findByUsername($username)) {
            redirect('admin_donors', 'Username already taken', 'error');
        }

        try {
            // Single INSERT — all donor fields go into users
            $this->userModel->register([
                'role'               => ROLE_DONOR,
                'username'           => $username,
                'email'              => $email,
                'password'           => $password,
                'first_name'         => $firstName,
                'last_name'          => $lastName,
                'phone'              => $_POST['phone'] ?? null,
                'is_active'          => 1,
                'blood_type'         => !empty($_POST['blood_type']) ? $_POST['blood_type'] : null,
                'date_of_birth'      => !empty($_POST['date_of_birth']) ? $_POST['date_of_birth'] : null,
                'gender'             => !empty($_POST['gender']) ? $_POST['gender'] : null,
                'address'            => $_POST['address'] ?? null,
                'city'               => $_POST['city'] ?? null,
                'eligibility_status' => 'Eligible',
            ]);

            redirect('admin_donors', 'Donor added successfully', 'success');
        } catch (Exception $e) {
            redirect('admin_donors', 'Error adding donor: ' . $e->getMessage(), 'error');
        }
    }

    /**
     * Edit an existing donor (POST)
     * Post-consolidation: single UPDATE on users table
     */
    public function editDonor() {
        requireRole(ROLE_ADMIN);

        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            redirect('admin_donors');
        }

        $userId = (int)($_POST['donor_id'] ?? 0);
        if (!$userId) {
            redirect('admin_donors', 'Invalid donor ID', 'error');
        }

        try {
            // Update donor fields on users table
            $this->donorModel->update($userId, [
                'first_name'    => $_POST['first_name'] ?? '',
                'last_name'     => $_POST['last_name'] ?? '',
                'email'         => $_POST['email'] ?? '',
                'phone'         => $_POST['phone'] ?? null,
                'blood_type'    => !empty($_POST['blood_type']) ? $_POST['blood_type'] : null,
                'date_of_birth' => !empty($_POST['date_of_birth']) ? $_POST['date_of_birth'] : null,
                'gender'        => !empty($_POST['gender']) ? $_POST['gender'] : null,
                'address'       => $_POST['address'] ?? null,
                'city'          => $_POST['city'] ?? null,
            ]);

            // Update eligibility if provided
            if (!empty($_POST['eligibility_status'])) {
                $this->donorModel->updateEligibility($userId, $_POST['eligibility_status']);
            }

            redirect('admin_donors', 'Donor updated successfully', 'success');
        } catch (Exception $e) {
            redirect('admin_donors', 'Error updating donor: ' . $e->getMessage(), 'error');
        }
    }

    /**
     * Delete a donor (POST)
     */
    public function deleteDonor() {
        requireRole(ROLE_ADMIN);

        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            redirect('admin_donors');
        }

        $userId = (int)($_POST['donor_id'] ?? 0);
        if (!$userId) {
            redirect('admin_donors', 'Invalid donor ID', 'error');
        }

        try {
            $this->donorModel->delete($userId);
            redirect('admin_donors', 'Donor deleted successfully', 'success');
        } catch (Exception $e) {
            redirect('admin_donors', 'Error deleting donor: ' . $e->getMessage(), 'error');
        }
    }

    /**
     * Get donation history for a donor (AJAX/JSON)
     */
    public function donorHistory() {
        requireRole(ROLE_ADMIN);

        $userId = (int)($_GET['donor_id'] ?? 0);
        $history = $this->donorModel->getDonationHistory($userId);

        header('Content-Type: application/json');
        echo json_encode(['history' => $history]);
        exit;
    }
}
