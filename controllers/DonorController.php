<?php
/**
 * Donor Controller
 * Handles donor dashboard, appointments, history, and profile
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

    // ================================================================
    // Appointments
    // ================================================================

    /**
     * Show appointments page
     */
    public function appointments() {
        requireRole(ROLE_DONOR);

        $donor = $this->donorModel->findByUserId($_SESSION['user_id']);
        if (!$donor) { redirect('donor_dashboard', 'Donor profile not found.', 'error'); }

        $donorId = $donor['donor_id'];

        $data = [
            'donor'        => $donor,
            'appointments' => $this->donorModel->getAppointments($donorId),
            'stats'        => $this->donorModel->getAppointmentStats($donorId),
            'hospitals'    => $this->donorModel->getHospitalsList(),
        ];

        require_once __DIR__ . '/../views/donor/appointments.php';
    }

    /**
     * Create a new appointment (POST)
     */
    public function addAppointment() {
        requireRole(ROLE_DONOR);

        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            redirect('donor_appointments');
        }

        $donor = $this->donorModel->findByUserId($_SESSION['user_id']);
        if (!$donor) { redirect('donor_dashboard', 'Donor profile not found.', 'error'); }

        $appointmentDate = trim($_POST['appointment_date'] ?? '');
        $appointmentTime = trim($_POST['appointment_time'] ?? '');
        $hospitalId      = intval($_POST['hospital_id'] ?? 0);
        $purpose         = trim($_POST['purpose'] ?? 'Donation');
        $notes           = trim($_POST['notes'] ?? '');

        // Validation
        if (empty($appointmentDate) || empty($appointmentTime) || $hospitalId <= 0) {
            redirect('donor_appointments', 'Please fill in all required fields (date, time, hospital).', 'error');
        }

        // Don't allow past dates
        if (strtotime($appointmentDate) < strtotime(date('Y-m-d'))) {
            redirect('donor_appointments', 'Cannot book an appointment in the past.', 'error');
        }

        $result = $this->donorModel->createAppointment([
            'donor_id'         => $donor['donor_id'],
            'hospital_id'      => $hospitalId,
            'appointment_date' => $appointmentDate,
            'appointment_time' => $appointmentTime,
            'purpose'          => $purpose,
            'notes'            => $notes,
            'created_by'       => $_SESSION['user_id'],
        ]);

        if ($result) {
            redirect('donor_appointments', 'Appointment booked successfully!', 'success');
        } else {
            redirect('donor_appointments', 'Failed to book appointment. Please try again.', 'error');
        }
    }

    /**
     * Reschedule an appointment (POST, donor-only)
     */
    public function rescheduleAppointment() {
        requireRole(ROLE_DONOR);

        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            redirect('donor_appointments');
        }

        $donor = $this->donorModel->findByUserId($_SESSION['user_id']);
        if (!$donor) { redirect('donor_dashboard', 'Donor profile not found.', 'error'); }

        $appointmentId   = intval($_POST['appointment_id'] ?? 0);
        $appointmentDate = trim($_POST['appointment_date'] ?? '');
        $appointmentTime = trim($_POST['appointment_time'] ?? '');
        $hospitalId      = intval($_POST['hospital_id'] ?? 0);
        $purpose         = trim($_POST['purpose'] ?? 'Donation');
        $notes           = trim($_POST['notes'] ?? '');

        if ($appointmentId <= 0 || empty($appointmentDate) || empty($appointmentTime) || $hospitalId <= 0) {
            redirect('donor_appointments', 'Please fill in all required fields.', 'error');
        }

        if (strtotime($appointmentDate) < strtotime(date('Y-m-d'))) {
            redirect('donor_appointments', 'Cannot reschedule to a past date.', 'error');
        }

        $result = $this->donorModel->rescheduleAppointment($appointmentId, $donor['donor_id'], [
            'hospital_id'      => $hospitalId,
            'appointment_date' => $appointmentDate,
            'appointment_time' => $appointmentTime,
            'purpose'          => $purpose,
            'notes'            => $notes,
        ]);

        if ($result) {
            redirect('donor_appointments', 'Appointment rescheduled successfully!', 'success');
        } else {
            redirect('donor_appointments', 'Failed to reschedule. The appointment may have already been completed or cancelled.', 'error');
        }
    }

    /**
     * Cancel an appointment (POST, donor-only)
     */
    public function cancelAppointment() {
        requireRole(ROLE_DONOR);

        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            redirect('donor_appointments');
        }

        $donor = $this->donorModel->findByUserId($_SESSION['user_id']);
        if (!$donor) { redirect('donor_dashboard', 'Donor profile not found.', 'error'); }

        $appointmentId = intval($_POST['appointment_id'] ?? 0);

        if ($appointmentId <= 0) {
            redirect('donor_appointments', 'Invalid appointment.', 'error');
        }

        $result = $this->donorModel->cancelAppointment($appointmentId, $donor['donor_id']);

        if ($result) {
            redirect('donor_appointments', 'Appointment cancelled.', 'success');
        } else {
            redirect('donor_appointments', 'Failed to cancel. The appointment may have already been completed.', 'error');
        }
    }

    // ================================================================
    // Donation History
    // ================================================================

    /**
     * Show donation history page
     */
    public function donationHistory() {
        requireRole(ROLE_DONOR);

        $donor = $this->donorModel->findByUserId($_SESSION['user_id']);
        if (!$donor) { redirect('donor_dashboard', 'Donor profile not found.', 'error'); }

        $data = [
            'donor'     => $donor,
            'stats'     => $this->donorModel->getStats($donor['donor_id']),
            'donations' => $this->donorModel->getDonationHistory($donor['donor_id']),
        ];

        require_once __DIR__ . '/../views/donor/donation_history.php';
    }

    // ================================================================
    // Profile
    // ================================================================

    /**
     * Show profile page
     */
    public function profile() {
        requireRole(ROLE_DONOR);

        $donor = $this->donorModel->findByUserId($_SESSION['user_id']);
        if (!$donor) { redirect('donor_dashboard', 'Donor profile not found.', 'error'); }

        $data = [
            'donor'      => $donor,
            'stats'      => $this->donorModel->getStats($donor['donor_id']),
            'bloodTypes' => $this->donorModel->getBloodTypes(),
        ];

        require_once __DIR__ . '/../views/donor/profile.php';
    }

    /**
     * Update profile (POST)
     */
    public function updateProfile() {
        requireRole(ROLE_DONOR);

        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            redirect('donor_profile');
        }

        $donor = $this->donorModel->findByUserId($_SESSION['user_id']);
        if (!$donor) { redirect('donor_dashboard', 'Donor profile not found.', 'error'); }

        $firstName   = trim($_POST['first_name'] ?? '');
        $lastName    = trim($_POST['last_name'] ?? '');
        $email       = trim($_POST['email'] ?? '');
        $phone       = trim($_POST['phone'] ?? '');
        $bloodTypeId = intval($_POST['blood_type_id'] ?? 0) ?: null;
        $dateOfBirth = trim($_POST['date_of_birth'] ?? '') ?: null;
        $gender      = trim($_POST['gender'] ?? '') ?: null;
        $address     = trim($_POST['address'] ?? '') ?: null;
        $city        = trim($_POST['city'] ?? '') ?: null;

        // Validation
        if (empty($firstName) || empty($lastName) || empty($email)) {
            redirect('donor_profile', 'First name, last name, and email are required.', 'error');
        }

        // Update donor record
        $this->donorModel->update($donor['donor_id'], [
            'first_name'    => $firstName,
            'last_name'     => $lastName,
            'blood_type_id' => $bloodTypeId,
            'date_of_birth' => $dateOfBirth,
            'gender'        => $gender,
            'address'       => $address,
            'city'          => $city,
        ]);

        // Update user record (email, phone)
        $this->donorModel->updateUserContact($_SESSION['user_id'], $email, $phone);

        // Update session name if changed
        $_SESSION['first_name'] = $firstName;
        $_SESSION['last_name'] = $lastName;

        redirect('donor_profile', 'Profile updated successfully!', 'success');
    }
}
