<?php
/**
 * Hospital Model
 * Handles hospital profiles and blood request management
 */
class HospitalModel {
    private $db;

    public function __construct() {
        $this->db = Database::getInstance()->getConnection();
    }

    /**
     * Create hospital record linked to manager user
     */
    public function create($data) {
        $sql = "INSERT INTO hospitals (user_id, hospital_name, hospital_code, address, city, region, postal_code, phone, email, license_number, is_active)
                VALUES (:user_id, :hospital_name, :hospital_code, :address, :city, :region, :postal_code, :phone, :email, :license_number, :is_active)";
        
        $stmt = $this->db->prepare($sql);
        $stmt->execute([
            'user_id'        => $data['user_id'],
            'hospital_name'  => $data['hospital_name'],
            'hospital_code'  => $data['hospital_code'],
            'address'        => $data['address'],
            'city'           => $data['city'],
            'region'         => $data['region'] ?? null,
            'postal_code'    => $data['postal_code'] ?? null,
            'phone'          => $data['phone'],
            'email'          => $data['email'] ?? null,
            'license_number' => $data['license_number'] ?? null,
            'is_active'      => $data['is_active'] ?? 1,
        ]);

        return $this->db->lastInsertId();
    }

    /**
     * Find hospital by manager user ID
     */
    public function findByUserId($userId) {
        $sql = "SELECT h.*, u.first_name, u.last_name, u.email as manager_email, u.username
                FROM hospitals h
                JOIN users u ON h.user_id = u.user_id
                WHERE h.user_id = :user_id";
        $stmt = $this->db->prepare($sql);
        $stmt->execute(['user_id' => $userId]);
        return $stmt->fetch();
    }

    /**
     * Find hospital by ID
     */
    public function findById($hospitalId) {
        $sql = "SELECT h.*, u.first_name, u.last_name, u.email as manager_email
                FROM hospitals h
                JOIN users u ON h.user_id = u.user_id
                WHERE h.hospital_id = :hospital_id";
        $stmt = $this->db->prepare($sql);
        $stmt->execute(['hospital_id' => $hospitalId]);
        return $stmt->fetch();
    }

    /**
     * Get all hospitals
     */
    public function getAll($limit = 50, $offset = 0) {
        $sql = "SELECT h.*, u.first_name, u.last_name, u.email as manager_email
                FROM hospitals h
                JOIN users u ON h.user_id = u.user_id
                ORDER BY h.created_at DESC
                LIMIT :limit OFFSET :offset";
        $stmt = $this->db->prepare($sql);
        $stmt->bindValue('limit', (int)$limit, PDO::PARAM_INT);
        $stmt->bindValue('offset', (int)$offset, PDO::PARAM_INT);
        $stmt->execute();
        return $stmt->fetchAll();
    }

    /**
     * Count all hospitals
     */
    public function countAll() {
        $stmt = $this->db->query("SELECT COUNT(*) as total FROM hospitals");
        return $stmt->fetch()['total'];
    }

    /**
     * Get request stats for a hospital
     */
    public function getRequestStats($hospitalId) {
        $stats = [];

        $stmt = $this->db->prepare("SELECT COUNT(*) as total FROM requests WHERE hospital_id = :hid");
        $stmt->execute(['hid' => $hospitalId]);
        $stats['total_requests'] = $stmt->fetch()['total'];

        $stmt = $this->db->prepare("SELECT COUNT(*) as total FROM requests WHERE hospital_id = :hid AND status = 'Fulfilled'");
        $stmt->execute(['hid' => $hospitalId]);
        $stats['fulfilled'] = $stmt->fetch()['total'];

        $stmt = $this->db->prepare("SELECT COUNT(*) as total FROM requests WHERE hospital_id = :hid AND status = 'Pending'");
        $stmt->execute(['hid' => $hospitalId]);
        $stats['pending'] = $stmt->fetch()['total'];

        $stmt = $this->db->prepare("SELECT COUNT(*) as total FROM requests WHERE hospital_id = :hid AND status = 'Rejected'");
        $stmt->execute(['hid' => $hospitalId]);
        $stats['rejected'] = $stmt->fetch()['total'];

        return $stats;
    }

    /**
     * Get recent requests for a hospital
     */
    public function getRecentRequests($hospitalId, $limit = 10) {
        $sql = "SELECT r.*, bt.type_name as blood_type
                FROM requests r
                LEFT JOIN blood_types bt ON r.blood_type_id = bt.blood_type_id
                WHERE r.hospital_id = :hospital_id
                ORDER BY r.created_at DESC
                LIMIT :limit";
        $stmt = $this->db->prepare($sql);
        $stmt->bindValue('hospital_id', (int)$hospitalId, PDO::PARAM_INT);
        $stmt->bindValue('limit', (int)$limit, PDO::PARAM_INT);
        $stmt->execute();
        return $stmt->fetchAll();
    }

    /**
     * Update hospital info
     */
    public function update($hospitalId, $data) {
        $sql = "UPDATE hospitals SET 
                hospital_name = :hospital_name, address = :address,
                city = :city, region = :region, postal_code = :postal_code,
                phone = :phone, email = :email, license_number = :license_number,
                updated_at = NOW()
                WHERE hospital_id = :hospital_id";
        $stmt = $this->db->prepare($sql);
        return $stmt->execute([
            'hospital_id'    => $hospitalId,
            'hospital_name'  => $data['hospital_name'],
            'address'        => $data['address'],
            'city'           => $data['city'],
            'region'         => $data['region'] ?? null,
            'postal_code'    => $data['postal_code'] ?? null,
            'phone'          => $data['phone'],
            'email'          => $data['email'] ?? null,
            'license_number' => $data['license_number'] ?? null,
        ]);
    }
}
