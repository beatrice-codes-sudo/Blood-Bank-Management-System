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
        $bloodTypes = $this->donorModel->getBloodTypes();

        require_once __DIR__ . '/../views/admin/donors.php';
    }

    /**
     * View single donor details (AJAX/JSON)
     */
    public function viewDonor() {
        requireRole(ROLE_ADMIN);

        $donorId = (int)($_GET['donor_id'] ?? 0);
        $donor = $this->donorModel->findById($donorId);

        if (!$donor) {
            http_response_code(404);
            echo json_encode(['error' => 'Donor not found']);
            exit;
        }

        $donorStats = $this->donorModel->getStats($donorId);

        header('Content-Type: application/json');
        echo json_encode([
            'donor' => $donor,
            'stats' => $donorStats,
        ]);
        exit;
    }

    /**
     * Add a new donor (POST)
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
            // Create user account
            $userId = $this->userModel->register([
                'role_id'    => ROLE_DONOR,
                'username'   => $username,
                'email'      => $email,
                'password'   => $password,
                'first_name' => $firstName,
                'last_name'  => $lastName,
                'phone'      => $_POST['phone'] ?? null,
                'is_active'  => 1,
            ]);

            // Create donor profile
            $this->donorModel->create([
                'user_id'            => $userId,
                'first_name'         => $firstName,
                'last_name'          => $lastName,
                'blood_type_id'      => !empty($_POST['blood_type_id']) ? (int)$_POST['blood_type_id'] : null,
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
     */
    public function editDonor() {
        requireRole(ROLE_ADMIN);

        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            redirect('admin_donors');
        }

        $donorId = (int)($_POST['donor_id'] ?? 0);
        if (!$donorId) {
            redirect('admin_donors', 'Invalid donor ID', 'error');
        }

        try {
            $this->donorModel->update($donorId, [
                'first_name'    => $_POST['first_name'] ?? '',
                'last_name'     => $_POST['last_name'] ?? '',
                'blood_type_id' => !empty($_POST['blood_type_id']) ? (int)$_POST['blood_type_id'] : null,
                'date_of_birth' => !empty($_POST['date_of_birth']) ? $_POST['date_of_birth'] : null,
                'gender'        => !empty($_POST['gender']) ? $_POST['gender'] : null,
                'address'       => $_POST['address'] ?? null,
                'city'          => $_POST['city'] ?? null,
            ]);

            // Update eligibility if provided
            if (!empty($_POST['eligibility_status'])) {
                $this->donorModel->updateEligibility($donorId, $_POST['eligibility_status']);
            }

            // Update user email/phone if provided
            $donor = $this->donorModel->findById($donorId);
            if ($donor) {
                $db = Database::getInstance()->getConnection();
                $stmt = $db->prepare("UPDATE users SET phone = :phone WHERE user_id = :user_id");
                $stmt->execute([
                    'phone'   => $_POST['phone'] ?? null,
                    'user_id' => $donor['user_id'],
                ]);
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

        $donorId = (int)($_POST['donor_id'] ?? 0);
        if (!$donorId) {
            redirect('admin_donors', 'Invalid donor ID', 'error');
        }

        try {
            $this->donorModel->delete($donorId);
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

        $donorId = (int)($_GET['donor_id'] ?? 0);
        $history = $this->donorModel->getDonationHistory($donorId);

        header('Content-Type: application/json');
        echo json_encode(['history' => $history]);
        exit;
    }
}
