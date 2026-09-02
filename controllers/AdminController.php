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
    private $requestModel;

    public function __construct() {
        $this->userModel = new User();
        $this->donorModel = new DonorModel();
        $this->hospitalModel = new HospitalModel();
        $this->bloodInventory = new BloodInventory();
        $this->requestModel = new RequestsModel();
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

    // ================================================================
    // Hospital Management
    // ================================================================

    /**
     * Show hospitals management page
     */
    public function manageHospitals() {
        requireRole(ROLE_ADMIN);

        $hospitals = $this->hospitalModel->getAll(200, 0);
        $stats = $this->hospitalModel->getGlobalStats();

        require_once __DIR__ . '/../views/admin/hospitals.php';
    }

    /**
     * View a single hospital's profile page
     */
    public function viewHospitalProfile() {
        requireRole(ROLE_ADMIN);

        $hospitalId = (int)($_GET['hospital_id'] ?? 0);
        if (!$hospitalId) {
            redirect('admin_hospitals', 'Invalid hospital ID', 'error');
        }

        $hospital = $this->hospitalModel->findById($hospitalId);
        if (!$hospital) {
            redirect('admin_hospitals', 'Hospital not found', 'error');
        }

        $requests     = $this->hospitalModel->getAllRequests($hospitalId);
        $requestStats = $this->hospitalModel->getExtendedRequestStats($hospitalId);
        $appointments = $this->hospitalModel->getAppointments($hospitalId);

        require_once __DIR__ . '/../views/admin/hospital_profile.php';
    }

    /**
     * Fulfill a blood request (admin action)
     */
    public function fulfillRequest() {
        requireRole(ROLE_ADMIN);

        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            redirect('admin_hospitals');
        }

        $requestId  = (int)($_POST['request_id'] ?? 0);
        $hospitalId = (int)($_POST['hospital_id'] ?? 0);

        if (!$requestId || !$hospitalId) {
            redirect('admin_hospitals', 'Invalid request', 'error');
        }

        try {
            $this->hospitalModel->fulfillRequest($requestId);
            redirect('admin_hospital_profile&hospital_id=' . $hospitalId, 'Blood request fulfilled successfully', 'success');
        } catch (Exception $e) {
            redirect('admin_hospital_profile&hospital_id=' . $hospitalId, 'Error fulfilling request: ' . $e->getMessage(), 'error');
        }
    }

    /**
     * Mark request as Ready for Pickup and generate 6-digit Digital Release PIN (Admin)
     */
    public function markReadyForPickup() {
        requireRole(ROLE_ADMIN);

        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            redirect('admin_hospitals');
        }

        $requestId  = (int)($_POST['request_id'] ?? 0);
        $hospitalId = (int)($_POST['hospital_id'] ?? 0);

        if (!$requestId || !$hospitalId) {
            redirect('admin_hospitals', 'Invalid request parameters', 'error');
        }

        try {
            // Generate secure 6-digit numeric PIN
            $releasePin = sprintf('%06d', mt_rand(100000, 999999));
            $this->hospitalModel->markReadyForPickup($requestId, $releasePin);

            redirect('admin_hospital_profile&hospital_id=' . $hospitalId, 'Request marked Ready for Pickup. Release PIN: ' . $releasePin, 'success');
        } catch (Exception $e) {
            redirect('admin_hospital_profile&hospital_id=' . $hospitalId, 'Error updating pickup status: ' . $e->getMessage(), 'error');
        }
    }

    /**
     * Verify Release PIN at counter during runner handover (Admin AJAX / POST)
     */
    public function verifyReleasePin() {
        requireRole(ROLE_ADMIN);
        header('Content-Type: application/json');

        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            echo json_encode(['success' => false, 'message' => 'Invalid request method']);
            exit;
        }

        $requestId    = (int)($_POST['request_id'] ?? 0);
        $enteredPin   = trim($_POST['release_pin'] ?? '');
        $runnerName   = trim($_POST['collected_by_name'] ?? '');
        $runnerPhone  = trim($_POST['collected_by_phone'] ?? '');

        if (!$requestId || empty($enteredPin) || empty($runnerName)) {
            echo json_encode(['success' => false, 'message' => 'Missing required fields (PIN, Runner Name)']);
            exit;
        }

        try {
            $result = $this->hospitalModel->verifyReleasePin($requestId, $enteredPin, $runnerName, $runnerPhone);
            echo json_encode($result);
            exit;
        } catch (Exception $e) {
            echo json_encode(['success' => false, 'message' => 'Error verifying PIN: ' . $e->getMessage()]);
            exit;
        }
    }

    /**
     * Delete an appointment (admin action)
     */
    public function deleteAppointment() {
        requireRole(ROLE_ADMIN);

        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            redirect('admin_hospitals');
        }

        $appointmentId = (int)($_POST['appointment_id'] ?? 0);
        $hospitalId    = (int)($_POST['hospital_id'] ?? 0);

        if (!$appointmentId || !$hospitalId) {
            redirect('admin_hospitals', 'Invalid appointment', 'error');
        }

        try {
            $this->hospitalModel->deleteAppointment($appointmentId);
            redirect('admin_hospital_profile&hospital_id=' . $hospitalId, 'Appointment deleted successfully', 'success');
        } catch (Exception $e) {
            redirect('admin_hospital_profile&hospital_id=' . $hospitalId, 'Error deleting appointment: ' . $e->getMessage(), 'error');
        }
    }

    /**
     * Delete a blood request (admin action — no status restriction)
     */
    public function deleteHospitalRequest() {
        requireRole(ROLE_ADMIN);

        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            redirect('admin_hospitals');
        }

        $requestId  = (int)($_POST['request_id'] ?? 0);
        $hospitalId = (int)($_POST['hospital_id'] ?? 0);

        if (!$requestId || !$hospitalId) {
            redirect('admin_hospitals', 'Invalid request', 'error');
        }

        try {
            $this->hospitalModel->deleteRequestAdmin($requestId);
            redirect('admin_hospital_profile&hospital_id=' . $hospitalId, 'Blood request deleted successfully', 'success');
        } catch (Exception $e) {
            redirect('admin_hospital_profile&hospital_id=' . $hospitalId, 'Error deleting request: ' . $e->getMessage(), 'error');
        }
    }

    /**
     * View single request details (AJAX/JSON — admin level)
     */
    public function viewHospitalRequest() {
        requireRole(ROLE_ADMIN);

        $requestId = (int)($_GET['request_id'] ?? 0);
        $request = $this->hospitalModel->getRequestByIdAdmin($requestId);

        if (!$request) {
            http_response_code(404);
            echo json_encode(['error' => 'Request not found']);
            exit;
        }

        // Fetch available count in inventory for this blood type
        $bloodInventory = new BloodInventory();
        $availableUnits = 0;
        if (!empty($request['blood_type'])) {
            $availableUnits = $bloodInventory->getAvailableCountByType($request['blood_type']);
        }

        header('Content-Type: application/json');
        echo json_encode([
            'request'         => $request,
            'available_units' => $availableUnits
        ]);
        exit;
    }

    /**
     * Dispatch blood units for a request and update request status (POST)
     */
    public function dispatchUnits() {
        requireRole(ROLE_ADMIN);

        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            redirect('admin_hospitals');
        }

        $requestId       = (int)($_POST['request_id'] ?? 0);
        $hospitalId      = (int)($_POST['hospital_id'] ?? 0);
        $unitsToDispatch = (int)($_POST['units_to_dispatch'] ?? 0);
        $targetStatus    = trim($_POST['status'] ?? 'Pending');

        if (!$requestId || !$hospitalId || $unitsToDispatch < 0) {
            redirect('admin_hospitals', 'Invalid parameters', 'error');
        }

        $request = $this->hospitalModel->getRequestByIdAdmin($requestId);
        if (!$request) {
            redirect('admin_hospitals', 'Request not found', 'error');
        }

        $bloodInventory = new BloodInventory();
        $adminId = $_SESSION['user_id'];
        $bloodType = $request['blood_type'];

        $result = $bloodInventory->dispatchUnitsForRequest($requestId, $hospitalId, $adminId, $bloodType, $unitsToDispatch, $targetStatus);

        if ($result['success']) {
            redirect('admin_hospital_profile&hospital_id=' . $hospitalId, $result['message'], 'success');
        } else {
            redirect('admin_hospital_profile&hospital_id=' . $hospitalId, $result['message'], 'error');
        }
    }

    //list all requests for a user (Donor or Hospital)
    public function listRequests() {
        requireRole(ROLE_ADMIN);

        $search = trim($_GET['search'] ?? '');
        $status = trim($_GET['status'] ?? '');

        $page = isset($_GET['p']) ? (int)$_GET['p'] : 1;
        if ($page < 1) $page = 1;
        $limit = 10; // Items per page
        $offset = ($page - 1) * $limit;

        $requests = $this->requestModel->getAllRequests($limit, $offset, $search, $status);
        $totalRequests = $this->requestModel->getTotalRequestsCount($search, $status);
        $totalPages = ceil($totalRequests / $limit);

        require_once __DIR__ . '/../views/admin/requests.php';
    }

    //get request by blood type
    public function getRequestByBloodType() {
        requireRole(ROLE_ADMIN);

        $bloodType = $_GET['blood_type'] ?? '';
        $requests = $this->requestModel->getRequestByBloodType($bloodType);

        require_once __DIR__ . '/../views/admin/requests.php';
    }
    //get request by blood type and status
    public function getRequestByBloodTypeAndStatus() {
        requireRole(ROLE_ADMIN);

        $bloodType = $_GET['blood_type'] ?? '';
        $status = $_GET['status'] ?? '';
        $requests = $this->requestModel->getRequestByBloodTypeAndStatus($bloodType, $status);

        require_once __DIR__ . '/../views/admin/requests.php';
    }

    //get request by id
    public function getRequestById() {
        requireRole(ROLE_ADMIN);

        $requestId = (int)($_GET['request_id'] ?? 0);
        $request = $this->requestModel->getRequestById($requestId);

        require_once __DIR__ . '/../views/admin/requests.php';
    }
    
    //get request by user id
    public function getRequestByUserId() {
        requireRole(ROLE_ADMIN);

        $userId = (int)($_GET['user_id'] ?? 0);
        $requests = $this->requestModel->getRequestByUserId($userId);

        require_once __DIR__ . '/../views/admin/requests.php';
    }
    //get request by hospital id
    public function getRequestByHospitalId() {
        requireRole(ROLE_ADMIN);

        $hospitalId = (int)($_GET['hospital_id'] ?? 0);
        $requests = $this->requestModel->getRequestByHospitalId($hospitalId);

        require_once __DIR__ . '/../views/admin/requests.php';
    }
    
    //delete request by admin
    public function deleteRequest() {
        requireRole(ROLE_ADMIN);

        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            redirect('admin_requests');
        }

        $requestId = (int)($_POST['request_id'] ?? 0);

        if (!$requestId) {
            redirect('admin_requests', 'Invalid request', 'error');
        }

        try {
            $this->requestModel->deleteRequest($requestId);
            redirect('admin_requests', 'Request deleted successfully', 'success');
        } catch (Exception $e) {
            redirect('admin_requests', 'Error deleting request: ' . $e->getMessage(), 'error');
        }
    }

    //update request by admin
    public function updateRequest() {
        requireRole(ROLE_ADMIN);

        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            redirect('admin_requests');
        }

        $requestId = (int)($_POST['request_id'] ?? 0);
        $data = $_POST;

        if (!$requestId) {
            redirect('admin_requests', 'Invalid request', 'error');
        }

        try {
            $this->requestModel->updateRequest($requestId, $data);
            redirect('admin_requests', 'Request updated successfully', 'success');
        } catch (Exception $e) {
            redirect('admin_requests', 'Error updating request: ' . $e->getMessage(), 'error');
        }
    }
    
}
