<?php
/**
 * Hospital Controller
 * Handles hospital manager dashboard
 */
class HospitalController {
    private $hospitalModel;
    private $bloodInventory;

    public function __construct() {
        $this->hospitalModel = new HospitalModel();
        $this->bloodInventory = new BloodInventory();
    }

    /**
     * Show hospital dashboard
     */
    public function dashboard() {
        requireRole(ROLE_HOSPITAL);

        $hospital = $this->hospitalModel->findByUserId($_SESSION['user_id']);
        
        $data = [
            'hospital'   => $hospital,
            'stats'      => $hospital ? $this->hospitalModel->getRequestStats($hospital['hospital_id']) : ['total_requests' => 0, 'fulfilled' => 0, 'pending' => 0, 'rejected' => 0],
            'requests'   => $hospital ? $this->hospitalModel->getRecentRequests($hospital['hospital_id']) : [],
            'inventory'  => $this->bloodInventory->getInventorySummary(),
        ];

        require_once __DIR__ . '/../views/hospital/dashboard.php';
    }
}
