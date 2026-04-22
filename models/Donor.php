<?php
/**
 * Donor Model
 * Handles donor profiles and donation history
 */
class DonorModel {
    private $db;

    public function __construct() {
        $this->db = Database::getInstance()->getConnection();
    }

    /**
     * Create donor profile linked to user
     */
    public function create($data) {
        $sql = "INSERT INTO donors (user_id, first_name, last_name, blood_type_id, date_of_birth, gender, address, city, eligibility_status)
                VALUES (:user_id, :first_name, :last_name, :blood_type_id, :date_of_birth, :gender, :address, :city, :eligibility_status)";
        
        $stmt = $this->db->prepare($sql);
        $stmt->execute([
            'user_id'            => $data['user_id'],
            'first_name'         => $data['first_name'],
            'last_name'          => $data['last_name'],
            'blood_type_id'      => $data['blood_type_id'] ?? null,
            'date_of_birth'      => $data['date_of_birth'] ?? null,
            'gender'             => $data['gender'] ?? null,
            'address'            => $data['address'] ?? null,
            'city'               => $data['city'] ?? null,
            'eligibility_status' => $data['eligibility_status'] ?? 'Eligible',
        ]);

        return $this->db->lastInsertId();
    }

    /**
     * Find donor by user ID
     */
    public function findByUserId($userId) {
        $sql = "SELECT d.*, bt.type_name as blood_type, u.email, u.phone, u.username
                FROM donors d 
                LEFT JOIN blood_types bt ON d.blood_type_id = bt.blood_type_id
                JOIN users u ON d.user_id = u.user_id
                WHERE d.user_id = :user_id";
        $stmt = $this->db->prepare($sql);
        $stmt->execute(['user_id' => $userId]);
        return $stmt->fetch();
    }

    /**
     * Find donor by donor ID
     */
    public function findById($donorId) {
        $sql = "SELECT d.*, bt.type_name as blood_type, u.email, u.phone, u.username
                FROM donors d 
                LEFT JOIN blood_types bt ON d.blood_type_id = bt.blood_type_id
                JOIN users u ON d.user_id = u.user_id
                WHERE d.donor_id = :donor_id";
        $stmt = $this->db->prepare($sql);
        $stmt->execute(['donor_id' => $donorId]);
        return $stmt->fetch();
    }

    /**
     * Get all donors
     */
    public function getAll($limit = 50, $offset = 0) {
        $sql = "SELECT d.*, bt.type_name as blood_type, u.email, u.phone, u.is_active, u.created_at as registered_at
                FROM donors d 
                LEFT JOIN blood_types bt ON d.blood_type_id = bt.blood_type_id
                JOIN users u ON d.user_id = u.user_id
                ORDER BY u.created_at DESC
                LIMIT :limit OFFSET :offset";
        $stmt = $this->db->prepare($sql);
        $stmt->bindValue('limit', (int)$limit, PDO::PARAM_INT);
        $stmt->bindValue('offset', (int)$offset, PDO::PARAM_INT);
        $stmt->execute();
        return $stmt->fetchAll();
    }

    /**
     * Count all donors
     */
    public function countAll() {
        $stmt = $this->db->query("SELECT COUNT(*) as total FROM donors");
        return $stmt->fetch()['total'];
    }

    /**
     * Get donation history for a donor
     */
    public function getDonationHistory($donorId) {
        $sql = "SELECT dn.*, bt.type_name as blood_type
                FROM donations dn
                LEFT JOIN blood_types bt ON dn.blood_type_id = bt.blood_type_id
                WHERE dn.donor_id = :donor_id
                ORDER BY dn.donation_date DESC";
        $stmt = $this->db->prepare($sql);
        $stmt->execute(['donor_id' => $donorId]);
        return $stmt->fetchAll();
    }

    /**
     * Get donation stats for a donor
     */
    public function getStats($donorId) {
        $stats = [];

        // Total donations
        $stmt = $this->db->prepare("SELECT COUNT(*) as total FROM donations WHERE donor_id = :donor_id AND status = 'Completed'");
        $stmt->execute(['donor_id' => $donorId]);
        $stats['total_donations'] = $stmt->fetch()['total'];

        // Last donation date
        $stmt = $this->db->prepare("SELECT MAX(donation_date) as last_date FROM donations WHERE donor_id = :donor_id AND status = 'Completed'");
        $stmt->execute(['donor_id' => $donorId]);
        $stats['last_donation_date'] = $stmt->fetch()['last_date'];

        // Total volume donated
        $stmt = $this->db->prepare("SELECT COALESCE(SUM(volume_ml), 0) as total_volume FROM donations WHERE donor_id = :donor_id AND status = 'Completed'");
        $stmt->execute(['donor_id' => $donorId]);
        $stats['total_volume_ml'] = $stmt->fetch()['total_volume'];

        return $stats;
    }

    /**
     * Update donor profile
     */
    public function update($donorId, $data) {
        $sql = "UPDATE donors SET 
                first_name = :first_name, last_name = :last_name,
                blood_type_id = :blood_type_id, date_of_birth = :date_of_birth,
                gender = :gender, address = :address, city = :city
                WHERE donor_id = :donor_id";
        $stmt = $this->db->prepare($sql);
        return $stmt->execute([
            'donor_id'      => $donorId,
            'first_name'    => $data['first_name'],
            'last_name'     => $data['last_name'],
            'blood_type_id' => $data['blood_type_id'] ?? null,
            'date_of_birth' => $data['date_of_birth'] ?? null,
            'gender'        => $data['gender'] ?? null,
            'address'       => $data['address'] ?? null,
            'city'          => $data['city'] ?? null,
        ]);
    }

    /**
     * Get global donor statistics for admin dashboard
     */
    public function getGlobalStats() {
        $stats = [];

        // Total donors
        $stmt = $this->db->query("SELECT COUNT(*) as total FROM donors");
        $stats['total'] = $stmt->fetch()['total'];

        // Active donors (user is_active = 1)
        $stmt = $this->db->query("SELECT COUNT(*) as total FROM donors d JOIN users u ON d.user_id = u.user_id WHERE u.is_active = 1");
        $stats['active'] = $stmt->fetch()['total'];

        // Inactive donors
        $stmt = $this->db->query("SELECT COUNT(*) as total FROM donors d JOIN users u ON d.user_id = u.user_id WHERE u.is_active = 0");
        $stats['inactive'] = $stmt->fetch()['total'];

        // Eligible
        $stmt = $this->db->query("SELECT COUNT(*) as total FROM donors WHERE eligibility_status = 'Eligible'");
        $stats['eligible'] = $stmt->fetch()['total'];

        // Deferred
        $stmt = $this->db->query("SELECT COUNT(*) as total FROM donors WHERE eligibility_status = 'Deferred'");
        $stats['deferred'] = $stmt->fetch()['total'];

        // Permanently Deferred
        $stmt = $this->db->query("SELECT COUNT(*) as total FROM donors WHERE eligibility_status = 'Permanently Deferred'");
        $stats['permanently_deferred'] = $stmt->fetch()['total'];

        // Donors who have donated at least once
        $stmt = $this->db->query("SELECT COUNT(DISTINCT donor_id) as total FROM donations WHERE status = 'Completed'");
        $stats['have_donated'] = $stmt->fetch()['total'];

        return $stats;
    }

    /**
     * Get all blood types (for dropdowns)
     */
    public function getBloodTypes() {
        $stmt = $this->db->query("SELECT * FROM blood_types ORDER BY blood_type_id");
        return $stmt->fetchAll();
    }

    /**
     * Delete donor and associated user record
     */
    public function delete($donorId) {
        // Get user_id first
        $stmt = $this->db->prepare("SELECT user_id FROM donors WHERE donor_id = :donor_id");
        $stmt->execute(['donor_id' => $donorId]);
        $donor = $stmt->fetch();

        if (!$donor) return false;

        // Delete user (donor cascades via FK ON DELETE CASCADE)
        $stmt = $this->db->prepare("DELETE FROM users WHERE user_id = :user_id");
        return $stmt->execute(['user_id' => $donor['user_id']]);
    }

    /**
     * Update donor eligibility status
     */
    public function updateEligibility($donorId, $status) {
        $stmt = $this->db->prepare("UPDATE donors SET eligibility_status = :status WHERE donor_id = :donor_id");
        return $stmt->execute(['status' => $status, 'donor_id' => $donorId]);
    }
}
