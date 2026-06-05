<?php
/**
 * Donor Model
 * Handles donor profiles and donation history
 * Post-consolidation: queries users table directly (no donors table)
 */
class DonorModel {
    private $db;

    public function __construct() {
        $this->db = Database::getInstance()->getConnection();
    }

    /**
     * Find donor by user ID
     */
    public function findByUserId($userId) {
        $sql = "SELECT * FROM users
                WHERE user_id = :user_id AND role = 'Donor'";
        $stmt = $this->db->prepare($sql);
        $stmt->execute(['user_id' => $userId]);
        return $stmt->fetch();
    }

    /**
     * Find donor by user ID (alias for consistency)
     */
    public function findById($donorId) {
        $sql = "SELECT * FROM users
                WHERE user_id = :user_id AND role = 'Donor'";
        $stmt = $this->db->prepare($sql);
        $stmt->execute(['user_id' => $donorId]);
        return $stmt->fetch();
    }

    /**
     * Get all donors
     */
    public function getAll($limit = 50, $offset = 0) {
        $sql = "SELECT * FROM users
                WHERE role = 'Donor'
                ORDER BY created_at DESC
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
        $stmt = $this->db->query("SELECT COUNT(*) as total FROM users WHERE role = 'Donor'");
        return $stmt->fetch()['total'];
    }

    /**
     * Get donation history for a donor
     */
    public function getDonationHistory($userId) {
        $sql = "SELECT * FROM donations
                WHERE donor_id = :user_id
                ORDER BY donation_date DESC";
        $stmt = $this->db->prepare($sql);
        $stmt->execute(['user_id' => $userId]);
        return $stmt->fetchAll();
    }

    /**
     * Get donation stats for a donor
     */
    public function getStats($userId) {
        $stats = [];

        // Total donations
        $stmt = $this->db->prepare("SELECT COUNT(*) as total FROM donations WHERE donor_id = :uid AND status = 'Completed'");
        $stmt->execute(['uid' => $userId]);
        $stats['total_donations'] = $stmt->fetch()['total'];

        // Last donation date
        $stmt = $this->db->prepare("SELECT MAX(donation_date) as last_date FROM donations WHERE donor_id = :uid AND status = 'Completed'");
        $stmt->execute(['uid' => $userId]);
        $stats['last_donation_date'] = $stmt->fetch()['last_date'];

        // Total volume donated
        $stmt = $this->db->prepare("SELECT COALESCE(SUM(volume_ml), 0) as total_volume FROM donations WHERE donor_id = :uid AND status = 'Completed'");
        $stmt->execute(['uid' => $userId]);
        $stats['total_volume_ml'] = $stmt->fetch()['total_volume'];

        return $stats;
    }

    /**
     * Update donor profile (all fields now on users table)
     */
    public function update($userId, $data) {
        $sql = "UPDATE users SET 
                first_name = :first_name, last_name = :last_name,
                blood_type = :blood_type, date_of_birth = :date_of_birth,
                gender = :gender, address = :address, city = :city,
                email = :email, phone = :phone
                WHERE user_id = :user_id";
        $stmt = $this->db->prepare($sql);
        return $stmt->execute([
            'user_id'       => $userId,
            'first_name'    => $data['first_name'],
            'last_name'     => $data['last_name'],
            'blood_type'    => $data['blood_type'] ?? null,
            'date_of_birth' => $data['date_of_birth'] ?? null,
            'gender'        => $data['gender'] ?? null,
            'address'       => $data['address'] ?? null,
            'city'          => $data['city'] ?? null,
            'email'         => $data['email'] ?? null,
            'phone'         => $data['phone'] ?? null,
        ]);
    }

    /**
     * Get global donor statistics for admin dashboard
     */
    public function getGlobalStats() {
        $stats = [];

        // Total donors
        $stmt = $this->db->query("SELECT COUNT(*) as total FROM users WHERE role = 'Donor'");
        $stats['total'] = $stmt->fetch()['total'];

        // Active donors
        $stmt = $this->db->query("SELECT COUNT(*) as total FROM users WHERE role = 'Donor' AND is_active = 1");
        $stats['active'] = $stmt->fetch()['total'];

        // Inactive donors
        $stmt = $this->db->query("SELECT COUNT(*) as total FROM users WHERE role = 'Donor' AND is_active = 0");
        $stats['inactive'] = $stmt->fetch()['total'];

        // Eligible
        $stmt = $this->db->query("SELECT COUNT(*) as total FROM users WHERE role = 'Donor' AND eligibility_status = 'Eligible'");
        $stats['eligible'] = $stmt->fetch()['total'];

        // Deferred
        $stmt = $this->db->query("SELECT COUNT(*) as total FROM users WHERE role = 'Donor' AND eligibility_status = 'Deferred'");
        $stats['deferred'] = $stmt->fetch()['total'];

        // Permanently Deferred
        $stmt = $this->db->query("SELECT COUNT(*) as total FROM users WHERE role = 'Donor' AND eligibility_status = 'Permanently Deferred'");
        $stats['permanently_deferred'] = $stmt->fetch()['total'];

        // Donors who have donated at least once
        $stmt = $this->db->query("SELECT COUNT(DISTINCT donor_id) as total FROM donations WHERE status = 'Completed'");
        $stats['have_donated'] = $stmt->fetch()['total'];

        return $stats;
    }

    /**
     * Delete donor (just delete from users — cascades handled by FK)
     */
    public function delete($userId) {
        $stmt = $this->db->prepare("DELETE FROM users WHERE user_id = :user_id AND role = 'Donor'");
        return $stmt->execute(['user_id' => $userId]);
    }

    /**
     * Update donor eligibility status
     */
    public function updateEligibility($userId, $status) {
        $stmt = $this->db->prepare("UPDATE users SET eligibility_status = :status WHERE user_id = :user_id");
        return $stmt->execute(['status' => $status, 'user_id' => $userId]);
    }

    // ================================================================
    // Appointment Management
    // ================================================================

    /**
     * Get all appointments for a donor (joined with hospital info)
     */
    public function getAppointments($userId) {
        $sql = "SELECT a.*, h.hospital_name, h.address as hospital_address, h.city as hospital_city
                FROM appointments a
                LEFT JOIN hospitals h ON a.hospital_id = h.hospital_id
                WHERE a.donor_id = :user_id
                ORDER BY a.appointment_date DESC, a.appointment_time DESC";
        $stmt = $this->db->prepare($sql);
        $stmt->execute(['user_id' => $userId]);
        return $stmt->fetchAll();
    }

    /**
     * Get a single appointment by ID (scoped to donor)
     */
    public function getAppointmentById($appointmentId, $userId) {
        $sql = "SELECT a.*, h.hospital_name, h.address as hospital_address
                FROM appointments a
                LEFT JOIN hospitals h ON a.hospital_id = h.hospital_id
                WHERE a.appointment_id = :appointment_id AND a.donor_id = :user_id";
        $stmt = $this->db->prepare($sql);
        $stmt->execute([
            'appointment_id' => $appointmentId,
            'user_id'        => $userId,
        ]);
        return $stmt->fetch();
    }

    /**
     * Create a new appointment
     */
    public function createAppointment($data) {
        $sql = "INSERT INTO appointments (donor_id, hospital_id, appointment_date, appointment_time, purpose, status, notes, created_by)
                VALUES (:donor_id, :hospital_id, :appointment_date, :appointment_time, :purpose, 'Scheduled', :notes, :created_by)";
        $stmt = $this->db->prepare($sql);
        $stmt->execute([
            'donor_id'         => $data['donor_id'],
            'hospital_id'      => $data['hospital_id'] ?? null,
            'appointment_date' => $data['appointment_date'],
            'appointment_time' => $data['appointment_time'],
            'purpose'          => $data['purpose'] ?? 'Donation',
            'notes'            => $data['notes'] ?? null,
            'created_by'       => $data['created_by'] ?? null,
        ]);
        return $this->db->lastInsertId();
    }

    /**
     * Reschedule an appointment (donor-only: update date, time, hospital)
     */
    public function rescheduleAppointment($appointmentId, $userId, $data) {
        $sql = "UPDATE appointments SET
                hospital_id = :hospital_id,
                appointment_date = :appointment_date,
                appointment_time = :appointment_time,
                purpose = :purpose,
                notes = :notes
                WHERE appointment_id = :appointment_id
                AND donor_id = :user_id
                AND status = 'Scheduled'";
        $stmt = $this->db->prepare($sql);
        $stmt->execute([
            'appointment_id'   => $appointmentId,
            'user_id'          => $userId,
            'hospital_id'      => $data['hospital_id'] ?? null,
            'appointment_date' => $data['appointment_date'],
            'appointment_time' => $data['appointment_time'],
            'purpose'          => $data['purpose'] ?? 'Donation',
            'notes'            => $data['notes'] ?? null,
        ]);
        return $stmt->rowCount() > 0;
    }

    /**
     * Cancel an appointment (donor-only, only if Scheduled)
     */
    public function cancelAppointment($appointmentId, $userId) {
        $sql = "UPDATE appointments SET status = 'Cancelled'
                WHERE appointment_id = :appointment_id
                AND donor_id = :user_id
                AND status = 'Scheduled'";
        $stmt = $this->db->prepare($sql);
        $stmt->execute([
            'appointment_id' => $appointmentId,
            'user_id'        => $userId,
        ]);
        return $stmt->rowCount() > 0;
    }

    /**
     * Get appointment statistics for a donor
     */
    public function getAppointmentStats($userId) {
        $stats = [];

        $stmt = $this->db->prepare("SELECT COUNT(*) as total FROM appointments WHERE donor_id = :uid");
        $stmt->execute(['uid' => $userId]);
        $stats['total'] = $stmt->fetch()['total'];

        $stmt = $this->db->prepare("SELECT COUNT(*) as total FROM appointments WHERE donor_id = :uid AND status = 'Scheduled'");
        $stmt->execute(['uid' => $userId]);
        $stats['scheduled'] = $stmt->fetch()['total'];

        $stmt = $this->db->prepare("SELECT COUNT(*) as total FROM appointments WHERE donor_id = :uid AND status = 'Completed'");
        $stmt->execute(['uid' => $userId]);
        $stats['completed'] = $stmt->fetch()['total'];

        $stmt = $this->db->prepare("SELECT COUNT(*) as total FROM appointments WHERE donor_id = :uid AND status = 'Cancelled'");
        $stmt->execute(['uid' => $userId]);
        $stats['cancelled'] = $stmt->fetch()['total'];

        $stmt = $this->db->prepare("SELECT COUNT(*) as total FROM appointments WHERE donor_id = :uid AND status = 'No Show'");
        $stmt->execute(['uid' => $userId]);
        $stats['no_show'] = $stmt->fetch()['total'];

        return $stats;
    }

    /**
     * Get all active hospitals (for appointment booking dropdown)
     */
    public function getHospitalsList() {
        $sql = "SELECT h.hospital_id, h.hospital_name, h.address, h.city
                FROM hospitals h
                WHERE h.is_active = 1
                ORDER BY h.hospital_name ASC";
        $stmt = $this->db->query($sql);
        return $stmt->fetchAll();
    }

    /**
     * Get appointments for a specific month (for calendar rendering)
     */
    public function getAppointmentsByMonth($userId, $year, $month) {
        $sql = "SELECT a.*, h.hospital_name
                FROM appointments a
                LEFT JOIN hospitals h ON a.hospital_id = h.hospital_id
                WHERE a.donor_id = :user_id
                AND YEAR(a.appointment_date) = :year
                AND MONTH(a.appointment_date) = :month
                ORDER BY a.appointment_date ASC, a.appointment_time ASC";
        $stmt = $this->db->prepare($sql);
        $stmt->execute([
            'user_id' => $userId,
            'year'    => $year,
            'month'   => $month,
        ]);
        return $stmt->fetchAll();
    }
}
