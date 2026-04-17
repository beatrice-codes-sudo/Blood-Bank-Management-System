<?php
/**
 * Donor Controller
 * Handles donor dashboard
 */
class DonorController {
    private $donorModel;

    public function __construct() {
        $this->donorModel = new DonorModel();
    }

    /**
     * Show donor dashboard
     */
    public function dashboard() {
        requireRole(ROLE_DONOR);

        $donor = $this->donorModel->findByUserId($_SESSION['user_id']);
        
        $data = [
            'donor'     => $donor,
            'stats'     => $donor ? $this->donorModel->getStats($donor['donor_id']) : ['total_donations' => 0, 'last_donation_date' => null, 'total_volume_ml' => 0],
            'donations' => $donor ? $this->donorModel->getDonationHistory($donor['donor_id']) : [],
        ];

        require_once __DIR__ . '/../views/donor/dashboard.php';
    }
}
