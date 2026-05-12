<?php
/**
 * Hospital Controller
 * Handles hospital manager dashboard and blood request management
 */
class HospitalController {
    private $hospitalModel;
    private $bloodInventory;

    public function __construct() {
        $this->hospitalModel = new HospitalModel();
        $this->bloodInventory = new BloodInventory();
    }

    /**
     * Get the hospital record for the current logged-in user
     */
    private function getMyHospital() {
        return $this->hospitalModel->findByUserId($_SESSION['user_id']);
    }

    /**
     * Show hospital dashboard
     */
    public function dashboard() {
        requireRole(ROLE_HOSPITAL);

        $hospital = $this->getMyHospital();
        
        $data = [
            'hospital'   => $hospital,
            'stats'      => $hospital ? $this->hospitalModel->getRequestStats($hospital['hospital_id']) : ['total_requests' => 0, 'fulfilled' => 0, 'pending' => 0, 'rejected' => 0],
            'requests'   => $hospital ? $this->hospitalModel->getRecentRequests($hospital['hospital_id']) : [],
            'inventory'  => $this->bloodInventory->getInventorySummary(),
        ];

        require_once __DIR__ . '/../views/hospital/dashboard.php';
    }

    // ================================================================
    // Blood Request Management
    // ================================================================

    /**
     * Show blood requests management page
     */
    public function bloodRequests() {
        requireRole(ROLE_HOSPITAL);

        $hospital = $this->getMyHospital();
        if (!$hospital) {
            redirect('hospital_dashboard', 'Hospital profile not found', 'error');
        }

        $hospitalId = $hospital['hospital_id'];
        $requests   = $this->hospitalModel->getAllRequests($hospitalId);
        $stats      = $this->hospitalModel->getExtendedRequestStats($hospitalId);
        $bloodTypes = $this->hospitalModel->getBloodTypes();

        require_once __DIR__ . '/../views/hospital/blood_requests.php';
    }

    /**
     * View single request details (AJAX/JSON)
     */
    public function viewRequest() {
        requireRole(ROLE_HOSPITAL);

        $hospital = $this->getMyHospital();
        if (!$hospital) {
            http_response_code(404);
            echo json_encode(['error' => 'Hospital not found']);
            exit;
        }

        $requestId = (int)($_GET['request_id'] ?? 0);
        $request = $this->hospitalModel->getRequestById($requestId, $hospital['hospital_id']);

        if (!$request) {
            http_response_code(404);
            echo json_encode(['error' => 'Request not found']);
            exit;
        }

        header('Content-Type: application/json');
        echo json_encode(['request' => $request]);
        exit;
    }

    /**
     * Add a new blood request (POST)
     */
    public function addRequest() {
        requireRole(ROLE_HOSPITAL);

        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            redirect('hospital_requests');
        }

        $hospital = $this->getMyHospital();
        if (!$hospital) {
            redirect('hospital_dashboard', 'Hospital profile not found', 'error');
        }

        // Validate required fields
        $bloodTypeId   = !empty($_POST['blood_type_id']) ? (int)$_POST['blood_type_id'] : null;
        $unitsRequested = (int)($_POST['units_requested'] ?? 0);
        $urgency       = trim($_POST['urgency'] ?? 'Normal');
        $notes         = trim($_POST['notes'] ?? '');

        if (!$bloodTypeId || $unitsRequested < 1) {
            redirect('hospital_requests', 'Blood type and at least 1 unit are required', 'error');
        }

        try {
            $this->hospitalModel->createRequest([
                'hospital_id'     => $hospital['hospital_id'],
                'blood_type_id'   => $bloodTypeId,
                'units_requested' => $unitsRequested,
                'urgency'         => $urgency,
                'notes'           => $notes ?: null,
                'requested_by'    => $_SESSION['user_id'],
            ]);

            redirect('hospital_requests', 'Blood request created successfully', 'success');
        } catch (Exception $e) {
            redirect('hospital_requests', 'Error creating request: ' . $e->getMessage(), 'error');
        }
    }

    /**
     * Edit an existing blood request (POST)
     */
    public function editRequest() {
        requireRole(ROLE_HOSPITAL);

        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            redirect('hospital_requests');
        }

        $hospital = $this->getMyHospital();
        if (!$hospital) {
            redirect('hospital_dashboard', 'Hospital profile not found', 'error');
        }

        $requestId = (int)($_POST['request_id'] ?? 0);
        if (!$requestId) {
            redirect('hospital_requests', 'Invalid request ID', 'error');
        }

        try {
            $this->hospitalModel->updateRequest($requestId, $hospital['hospital_id'], [
                'blood_type_id'   => !empty($_POST['blood_type_id']) ? (int)$_POST['blood_type_id'] : null,
                'units_requested' => (int)($_POST['units_requested'] ?? 1),
                'urgency'         => $_POST['urgency'] ?? 'Normal',
                'status'          => $_POST['status'] ?? 'Pending',
                'notes'           => $_POST['notes'] ?? null,
            ]);

            redirect('hospital_requests', 'Blood request updated successfully', 'success');
        } catch (Exception $e) {
            redirect('hospital_requests', 'Error updating request: ' . $e->getMessage(), 'error');
        }
    }

    /**
     * Delete a blood request (POST)
     */
    public function deleteRequest() {
        requireRole(ROLE_HOSPITAL);

        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            redirect('hospital_requests');
        }

        $hospital = $this->getMyHospital();
        if (!$hospital) {
            redirect('hospital_dashboard', 'Hospital profile not found', 'error');
        }

        $requestId = (int)($_POST['request_id'] ?? 0);
        if (!$requestId) {
            redirect('hospital_requests', 'Invalid request ID', 'error');
        }

        try {
            $deleted = $this->hospitalModel->deleteRequest($requestId, $hospital['hospital_id']);
            if ($deleted) {
                redirect('hospital_requests', 'Blood request deleted successfully', 'success');
            } else {
                redirect('hospital_requests', 'Cannot delete this request. Only Pending or Cancelled requests can be deleted.', 'error');
            }
        } catch (Exception $e) {
            redirect('hospital_requests', 'Error deleting request: ' . $e->getMessage(), 'error');
        }
    }

    /**
     * Get fulfillment/distribution history for a request (AJAX/JSON)
     */
    public function requestHistory() {
        requireRole(ROLE_HOSPITAL);

        $hospital = $this->getMyHospital();
        if (!$hospital) {
            http_response_code(404);
            echo json_encode(['error' => 'Hospital not found']);
            exit;
        }

        $requestId = (int)($_GET['request_id'] ?? 0);

        // Verify the request belongs to this hospital
        $request = $this->hospitalModel->getRequestById($requestId, $hospital['hospital_id']);
        if (!$request) {
            http_response_code(404);
            echo json_encode(['error' => 'Request not found']);
            exit;
        }

        $distributions = $this->hospitalModel->getRequestDistributions($requestId);

        header('Content-Type: application/json');
        echo json_encode([
            'request'       => $request,
            'distributions' => $distributions,
        ]);
        exit;
    }
}
