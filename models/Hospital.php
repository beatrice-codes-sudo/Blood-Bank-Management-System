<?php
/**
 * Hospital Model
 * Handles hospital profiles and blood request management
 * Post-consolidation: blood_type is an inline ENUM, no blood_types table
 */
class HospitalModel {
    /** @var PDO */
    private PDO $db;

    public function __construct() {
        $this->db = Database::getInstance()->getConnection();
    }

    /**
     * Create hospital record linked to manager user
     *
     * @param array $data
     * @return string|false
     */
    public function create(array $data) {
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
     *
     * @param int $userId
     * @return array|false
     */
    public function findByUserId(int $userId) {
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
     *
     * @param int $hospitalId
     * @return array|false
     */
    public function findById(int $hospitalId) {
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
     *
     * @param int $limit
     * @param int $offset
     * @return array
     */
    public function getAll(int $limit = 50, int $offset = 0): array {
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
     *
     * @return int
     */
    public function countAll(): int {
        $stmt = $this->db->query("SELECT COUNT(*) as total FROM hospitals");
        return (int)($stmt->fetch()['total'] ?? 0);
    }

    /**
     * Get request stats for a hospital
     *
     * @param int $hospitalId
     * @return array
     */
    public function getRequestStats(int $hospitalId): array {
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
     * Get recent requests for a hospital (blood_type is now a direct column)
     *
     * @param int $hospitalId
     * @param int $limit
     * @return array
     */
    public function getRecentRequests(int $hospitalId, int $limit = 10): array {
        $sql = "SELECT r.*
                FROM requests r
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
     *
     * @param int $hospitalId
     * @param array $data
     * @return bool
     */
    public function update(int $hospitalId, array $data): bool {
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

    // ================================================================
    // Blood Request Management (Hospital-scoped)
    // ================================================================

    /**
     * Get all blood requests for a hospital
     *
     * @param int $hospitalId
     * @param int $limit
     * @param int $offset
     * @return array
     */
    public function getAllRequests(int $hospitalId, int $limit = 200, int $offset = 0): array {
        $sql = "SELECT r.*,
                u.first_name as requester_first, u.last_name as requester_last
                FROM requests r
                LEFT JOIN users u ON r.requested_by = u.user_id
                WHERE r.hospital_id = :hospital_id
                ORDER BY r.created_at DESC
                LIMIT :limit OFFSET :offset";
        $stmt = $this->db->prepare($sql);
        $stmt->bindValue('hospital_id', (int)$hospitalId, PDO::PARAM_INT);
        $stmt->bindValue('limit', (int)$limit, PDO::PARAM_INT);
        $stmt->bindValue('offset', (int)$offset, PDO::PARAM_INT);
        $stmt->execute();
        return $stmt->fetchAll();
    }

    /**
     * Get a single request by ID (scoped to hospital)
     *
     * @param int $requestId
     * @param int $hospitalId
     * @return array|false
     */
    public function getRequestById(int $requestId, int $hospitalId) {
        $sql = "SELECT r.*,
                u.first_name as requester_first, u.last_name as requester_last,
                h.hospital_name
                FROM requests r
                LEFT JOIN users u ON r.requested_by = u.user_id
                LEFT JOIN hospitals h ON r.hospital_id = h.hospital_id
                WHERE r.request_id = :request_id AND r.hospital_id = :hospital_id";
        $stmt = $this->db->prepare($sql);
        $stmt->execute([
            'request_id'  => $requestId,
            'hospital_id' => $hospitalId,
        ]);
        return $stmt->fetch();
    }

    /**
     * Get extended request stats for a hospital
     *
     * @param int $hospitalId
     * @return array
     */
    public function getExtendedRequestStats(int $hospitalId): array {
        $stats = [];

        // Total
        $stmt = $this->db->prepare("SELECT COUNT(*) as total FROM requests WHERE hospital_id = :hid");
        $stmt->execute(['hid' => $hospitalId]);
        $stats['total'] = $stmt->fetch()['total'];

        // By status
        $statuses = ['Pending', 'Processing', 'Fulfilled', 'Partially Fulfilled', 'Rejected', 'Cancelled'];
        foreach ($statuses as $status) {
            $stmt = $this->db->prepare("SELECT COUNT(*) as total FROM requests WHERE hospital_id = :hid AND status = :status");
            $stmt->execute(['hid' => $hospitalId, 'status' => $status]);
            $key = strtolower(str_replace(' ', '_', $status));
            $stats[$key] = $stmt->fetch()['total'];
        }

        // Emergency count
        $stmt = $this->db->prepare("SELECT COUNT(*) as total FROM requests WHERE hospital_id = :hid AND urgency = 'Emergency'");
        $stmt->execute(['hid' => $hospitalId]);
        $stats['emergency'] = $stmt->fetch()['total'];

        // Total units requested
        $stmt = $this->db->prepare("SELECT COALESCE(SUM(units_requested), 0) as total FROM requests WHERE hospital_id = :hid");
        $stmt->execute(['hid' => $hospitalId]);
        $stats['total_units_requested'] = $stmt->fetch()['total'];

        // Total units fulfilled
        $stmt = $this->db->prepare("SELECT COALESCE(SUM(units_fulfilled), 0) as total FROM requests WHERE hospital_id = :hid");
        $stmt->execute(['hid' => $hospitalId]);
        $stats['total_units_fulfilled'] = $stmt->fetch()['total'];

        return $stats;
    }

    /**
     * Create a new blood request
     *
     * @param array $data
     * @return string|false
     */
    public function createRequest(array $data) {
        $sql = "INSERT INTO requests (hospital_id, blood_type, units_requested, urgency, status, notes, requested_by)
                VALUES (:hospital_id, :blood_type, :units_requested, :urgency, :status, :notes, :requested_by)";
        $stmt = $this->db->prepare($sql);
        $stmt->execute([
            'hospital_id'     => $data['hospital_id'],
            'blood_type'      => $data['blood_type'] ?? null,
            'units_requested' => $data['units_requested'] ?? 1,
            'urgency'         => $data['urgency'] ?? 'Normal',
            'status'          => 'Pending',
            'notes'           => $data['notes'] ?? null,
            'requested_by'    => $data['requested_by'] ?? null,
        ]);
        return $this->db->lastInsertId();
    }

    /**
     * Update a blood request (hospital-scoped)
     *
     * @param int $requestId
     * @param int $hospitalId
     * @param array $data
     * @return bool
     */
    public function updateRequest(int $requestId, int $hospitalId, array $data): bool {
        $sql = "UPDATE requests SET
                blood_type = :blood_type,
                units_requested = :units_requested,
                urgency = :urgency,
                status = :status,
                notes = :notes,
                updated_at = NOW()
                WHERE request_id = :request_id AND hospital_id = :hospital_id";
        $stmt = $this->db->prepare($sql);
        return $stmt->execute([
            'request_id'      => $requestId,
            'hospital_id'     => $hospitalId,
            'blood_type'      => $data['blood_type'] ?? null,
            'units_requested' => $data['units_requested'] ?? 1,
            'urgency'         => $data['urgency'] ?? 'Normal',
            'status'          => $data['status'] ?? 'Pending',
            'notes'           => $data['notes'] ?? null,
        ]);
    }

    /**
     * Delete a blood request (only Pending or Cancelled)
     *
     * @param int $requestId
     * @param int $hospitalId
     * @return bool
     */
    public function deleteRequest(int $requestId, int $hospitalId): bool {
        $sql = "DELETE FROM requests
                WHERE request_id = :request_id
                AND hospital_id = :hospital_id
                AND status IN ('Pending', 'Cancelled')";
        $stmt = $this->db->prepare($sql);
        $stmt->execute([
            'request_id'  => $requestId,
            'hospital_id' => $hospitalId,
        ]);
        return $stmt->rowCount() > 0;
    }

    /**
     * Get distribution/fulfillment history for a request
     *
     * @param int $requestId
     * @return array
     */
    public function getRequestDistributions(int $requestId): array {
        $sql = "SELECT d.*, u.first_name as dispatcher_first, u.last_name as dispatcher_last
                FROM distributions d
                LEFT JOIN users u ON d.dispatched_by = u.user_id
                WHERE d.request_id = :request_id
                ORDER BY d.dispatched_date DESC";
        $stmt = $this->db->prepare($sql);
        $stmt->execute(['request_id' => $requestId]);
        return $stmt->fetchAll();
    }

    // ================================================================
    // Admin-level Methods (no hospital-scope restriction)
    // ================================================================

    /**
     * Get global hospital stats for the admin listing page
     *
     * @return array
     */
    public function getGlobalStats(): array {
        $stats = [];

        $stmt = $this->db->query("SELECT COUNT(*) as total FROM hospitals");
        $stats['total'] = $stmt->fetch()['total'];

        $stmt = $this->db->query("SELECT COUNT(*) as total FROM hospitals WHERE is_active = 1");
        $stats['active'] = $stmt->fetch()['total'];

        $stmt = $this->db->query("SELECT COUNT(*) as total FROM hospitals WHERE is_active = 0");
        $stats['inactive'] = $stmt->fetch()['total'];

        return $stats;
    }

    /**
     * Get all appointments for a hospital with donor info
     *
     * @param int $hospitalId
     * @return array
     */
    public function getAppointments(int $hospitalId): array {
        $sql = "SELECT a.*,
                u.first_name as donor_first, u.last_name as donor_last,
                u.email as donor_email, u.phone as donor_phone,
                u.blood_type as donor_blood_type
                FROM appointments a
                JOIN users u ON a.donor_id = u.user_id
                WHERE a.hospital_id = :hospital_id
                ORDER BY a.appointment_date DESC, a.appointment_time DESC";
        $stmt = $this->db->prepare($sql);
        $stmt->execute(['hospital_id' => $hospitalId]);
        return $stmt->fetchAll();
    }

    /**
     * Delete an appointment (admin action — no scope restriction)
     *
     * @param int $appointmentId
     * @return bool
     */
    public function deleteAppointment(int $appointmentId): bool {
        $sql = "DELETE FROM appointments WHERE appointment_id = :id";
        $stmt = $this->db->prepare($sql);
        $stmt->execute(['id' => $appointmentId]);
        return $stmt->rowCount() > 0;
    }

    /**
     * Fulfill a blood request (admin action — sets status to Fulfilled)
     *
     * @param int $requestId
     * @return bool
     */
    public function fulfillRequest(int $requestId): bool {
        $sql = "UPDATE requests SET
                status = 'Fulfilled',
                units_fulfilled = units_requested,
                updated_at = NOW()
                WHERE request_id = :request_id";
        $stmt = $this->db->prepare($sql);
        $stmt->execute(['request_id' => $requestId]);
        return $stmt->rowCount() > 0;
    }

    /**
     * Get a single request by ID (admin — no hospital scope)
     *
     * @param int $requestId
     * @return array|false
     */
    public function getRequestByIdAdmin(int $requestId) {
        $sql = "SELECT r.*,
                u.first_name as requester_first, u.last_name as requester_last,
                u.email as requester_email, u.phone as requester_phone,
                h.hospital_name
                FROM requests r
                LEFT JOIN users u ON r.requested_by = u.user_id
                LEFT JOIN hospitals h ON r.hospital_id = h.hospital_id
                WHERE r.request_id = :request_id";
        $stmt = $this->db->prepare($sql);
        $stmt->execute(['request_id' => $requestId]);
        return $stmt->fetch();
    }

    /**
     * Delete a blood request (admin action — no status restriction)
     *
     * @param int $requestId
     * @return bool
     */
    public function deleteRequestAdmin(int $requestId): bool {
        // First delete related request_items
        $sql = "DELETE FROM request_items WHERE request_id = :id";
        $stmt = $this->db->prepare($sql);
        $stmt->execute(['id' => $requestId]);

        // Then delete the request itself
        $sql = "DELETE FROM requests WHERE request_id = :id";
        $stmt = $this->db->prepare($sql);
        $stmt->execute(['id' => $requestId]);
        return $stmt->rowCount() > 0;
    }

    /**
     * Mark a request as Ready for Pickup and assign 6-digit release PIN (Admin)
     *
     * @param int $requestId
     * @param string $releasePin
     * @return bool
     */
    public function markReadyForPickup(int $requestId, string $releasePin): bool {
        $sql = "UPDATE requests SET 
                status = 'Processing',
                collection_status = 'Ready for Pickup',
                release_pin = :pin,
                pin_generated_at = NOW(),
                updated_at = NOW()
                WHERE request_id = :rid";
        $stmt = $this->db->prepare($sql);
        return $stmt->execute([
            'pin' => $releasePin,
            'rid' => $requestId
        ]);
    }

    /**
     * Verify Release PIN at central counter: marks request as Dispatched / In Transit (Admin)
     *
     * @param int $requestId
     * @param string $enteredPin
     * @param string $runnerName
     * @param string $runnerPhone
     * @return array
     */
    public function verifyReleasePin(int $requestId, string $enteredPin, string $runnerName, string $runnerPhone): array {
        $stmt = $this->db->prepare("SELECT * FROM requests WHERE request_id = :rid");
        $stmt->execute(['rid' => $requestId]);
        $req = $stmt->fetch();

        if (!$req || trim($req['release_pin']) !== trim($enteredPin)) {
            return ['success' => false, 'message' => 'Invalid or incorrect 6-digit Release PIN!'];
        }

        $sql = "UPDATE requests SET 
                status = 'Dispatched',
                collection_status = 'Dispatched',
                dispatched_at = NOW(),
                collected_at = NOW(),
                collected_by_name = :runner_name,
                collected_by_phone = :runner_phone,
                units_fulfilled = units_requested,
                updated_at = NOW()
                WHERE request_id = :rid";
        $stmtUpdate = $this->db->prepare($sql);
        $success = $stmtUpdate->execute([
            'runner_name'  => $runnerName,
            'runner_phone' => $runnerPhone,
            'rid'          => $requestId
        ]);

        return [
            'success' => $success,
            'message' => $success ? 'PIN Verified! Units dispatched with driver ' . $runnerName . ' (In Transit)' : 'Database update failed.'
        ];
    }

    /**
     * Confirm physical package arrival and cold-chain integrity at destination hospital (Hospital Manager)
     *
     * @param int $requestId
     * @param int $hospitalId
     * @param int|bool $tempVerified
     * @return array
     */
    public function confirmReceipt(int $requestId, int $hospitalId, $tempVerified = 1): array {
        $sql = "UPDATE requests SET 
                status = 'Fulfilled',
                collection_status = 'Received',
                received_at = NOW(),
                temp_verified = :temp_verified,
                updated_at = NOW()
                WHERE request_id = :rid AND hospital_id = :hid";
        $stmt = $this->db->prepare($sql);
        $success = $stmt->execute([
            'temp_verified' => $tempVerified ? 1 : 0,
            'rid'           => $requestId,
            'hid'           => $hospitalId
        ]);

        return [
            'success' => $success,
            'message' => $success ? 'Package arrival confirmed! Request marked as Fulfilled.' : 'Failed to confirm receipt.'
        ];
    }

    /**
     * Get complete request audit details for View Modal (used by both Admin & Hospital)
     *
     * @param int $requestId
     * @param int|null $hospitalId
     * @return array|false
     */
    public function getRequestDetailsWithAudit(int $requestId, ?int $hospitalId = null) {
        $params = ['rid' => $requestId];
        $hClause = "";
        if ($hospitalId !== null) {
            $hClause = "AND r.hospital_id = :hid";
            $params['hid'] = $hospitalId;
        }

        $sql = "SELECT r.*,
                h.hospital_name, h.phone as hospital_phone, h.address as hospital_address, h.city as hospital_city,
                u.first_name as requester_first, u.last_name as requester_last, u.email as requester_email, u.phone as requester_phone
                FROM requests r
                LEFT JOIN hospitals h ON r.hospital_id = h.hospital_id
                LEFT JOIN users u ON r.requested_by = u.user_id
                WHERE r.request_id = :rid {$hClause}";
        $stmt = $this->db->prepare($sql);
        $stmt->execute($params);
        return $stmt->fetch();
    }
}

