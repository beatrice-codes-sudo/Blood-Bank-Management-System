<?php
/**
 * Auth Controller
 * Handles login, registration, and logout
 * Post-consolidation: single INSERT for donor registration, role is a string
 */
class AuthController {
    /** @var User */
    private User $userModel;

    /** @var HospitalModel */
    private HospitalModel $hospitalModel;

    public function __construct() {
        $this->userModel = new User();
        $this->hospitalModel = new HospitalModel();
    }

    /**
     * Show login page
     */
    public function showLogin() {
        if (isLoggedIn()) {
            $this->redirectToDashboard();
            return;
        }
        require_once __DIR__ . '/../views/auth/login.php';
    }

    /**
     * Show donor registration page
     */
    public function showRegisterDonor() {
        if (isLoggedIn()) {
            $this->redirectToDashboard();
            return;
        }
        // Blood types from constant array instead of DB
        $bloodTypes = BLOOD_TYPES;
        require_once __DIR__ . '/../views/auth/register_donor.php';
    }

    /**
     * Show hospital registration page
     */
    public function showRegisterHospital() {
        if (isLoggedIn()) {
            $this->redirectToDashboard();
            return;
        }
        require_once __DIR__ . '/../views/auth/register_hospital.php';
    }

    /**
     * Process login
     */
    public function login() {
        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            redirect('login');
            return;
        }

        $email = sanitize($_POST['email'] ?? '');
        $password = $_POST['password'] ?? '';

        // Validation
        $errors = [];
        if (empty($email)) $errors[] = 'Email is required';
        if (empty($password)) $errors[] = 'Password is required';

        if (!empty($errors)) {
            $_SESSION['errors'] = $errors;
            $_SESSION['old_input'] = ['email' => $email];
            redirect('login');
            return;
        }

        // Attempt login
        $user = $this->userModel->login($email, $password);

        if ($user) {
            // Set session — role is now a string from the ENUM column
            $_SESSION['user_id'] = $user['user_id'];
            $_SESSION['role'] = $user['role'];
            $_SESSION['username'] = $user['username'];
            $_SESSION['first_name'] = $user['first_name'];
            $_SESSION['last_name'] = $user['last_name'];
            $_SESSION['email'] = $user['email'];

            $this->redirectToDashboard();
        } else {
            $_SESSION['errors'] = ['Invalid email or password'];
            $_SESSION['old_input'] = ['email' => $email];
            redirect('login');
        }
    }

    /**
     * Process donor registration
     * Post-consolidation: single INSERT into users with donor fields
     */
    public function registerDonor() {
        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            redirect('register_donor');
            return;
        }

        $data = [
            'first_name'    => sanitize($_POST['first_name'] ?? ''),
            'last_name'     => sanitize($_POST['last_name'] ?? ''),
            'email'         => sanitize($_POST['email'] ?? ''),
            'phone'         => sanitize($_POST['phone'] ?? ''),
            'password'      => $_POST['password'] ?? '',
            'confirm_password' => $_POST['confirm_password'] ?? '',
            'blood_type'    => $_POST['blood_type'] ?? null,
            'date_of_birth' => $_POST['date_of_birth'] ?? null,
            'gender'        => $_POST['gender'] ?? null,
            'address'       => sanitize($_POST['address'] ?? ''),
            'city'          => sanitize($_POST['city'] ?? ''),
        ];

        // Validation
        $errors = $this->validateRegistration($data);
        if (empty($data['blood_type'])) {
            $errors[] = 'Blood type is required';
        }

        if (!empty($errors)) {
            $_SESSION['errors'] = $errors;
            $_SESSION['old_input'] = $data;
            redirect('register_donor');
            return;
        }

        try {
            // Single INSERT — donor fields go directly into users
            $username = strtolower($data['first_name'] . '.' . $data['last_name'] . rand(100, 999));
            $this->userModel->register([
                'role'               => ROLE_DONOR,
                'username'           => $username,
                'email'              => $data['email'],
                'password'           => $data['password'],
                'first_name'         => $data['first_name'],
                'last_name'          => $data['last_name'],
                'phone'              => $data['phone'],
                'blood_type'         => $data['blood_type'],
                'date_of_birth'      => $data['date_of_birth'],
                'gender'             => $data['gender'],
                'address'            => $data['address'],
                'city'               => $data['city'],
            ]);

            redirect('login', 'Registration successful! Please login.', 'success');
        } catch (PDOException $e) {
            if ($e->getCode() == 23000) {
                $_SESSION['errors'] = ['An account with this email already exists'];
            } else {
                $_SESSION['errors'] = ['Registration failed. Please try again.'];
            }
            $_SESSION['old_input'] = $data;
            redirect('register_donor');
        }
    }

    /**
     * Process hospital registration
     */
    public function registerHospital() {
        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            redirect('register_hospital');
            return;
        }

        $data = [
            'first_name'     => sanitize($_POST['first_name'] ?? ''),
            'last_name'      => sanitize($_POST['last_name'] ?? ''),
            'email'          => sanitize($_POST['email'] ?? ''),
            'phone'          => sanitize($_POST['phone'] ?? ''),
            'password'       => $_POST['password'] ?? '',
            'confirm_password' => $_POST['confirm_password'] ?? '',
            'hospital_name'  => sanitize($_POST['hospital_name'] ?? ''),
            'hospital_code'  => sanitize($_POST['hospital_code'] ?? ''),
            'address'        => sanitize($_POST['address'] ?? ''),
            'city'           => sanitize($_POST['city'] ?? ''),
            'region'         => sanitize($_POST['region'] ?? ''),
            'postal_code'    => sanitize($_POST['postal_code'] ?? ''),
            'license_number' => sanitize($_POST['license_number'] ?? ''),
        ];

        // Validation
        $errors = $this->validateRegistration($data);
        if (empty($data['hospital_name'])) $errors[] = 'Hospital name is required';
        if (empty($data['hospital_code'])) $errors[] = 'Hospital code is required';
        if (empty($data['address'])) $errors[] = 'Address is required';
        if (empty($data['city'])) $errors[] = 'City is required';

        if (!empty($errors)) {
            $_SESSION['errors'] = $errors;
            $_SESSION['old_input'] = $data;
            redirect('register_hospital');
            return;
        }

        try {
            // Create user (hospital manager)
            $username = strtolower(str_replace(' ', '', $data['hospital_name'])) . rand(100, 999);
            $userId = $this->userModel->register([
                'role'       => ROLE_HOSPITAL,
                'username'   => $username,
                'email'      => $data['email'],
                'password'   => $data['password'],
                'first_name' => $data['first_name'],
                'last_name'  => $data['last_name'],
                'phone'      => $data['phone'],
            ]);

            // Create hospital record (hospitals table is kept)
            $this->hospitalModel->create([
                'user_id'        => $userId,
                'hospital_name'  => $data['hospital_name'],
                'hospital_code'  => $data['hospital_code'],
                'address'        => $data['address'],
                'city'           => $data['city'],
                'region'         => $data['region'],
                'postal_code'    => $data['postal_code'],
                'phone'          => $data['phone'],
                'email'          => $data['email'],
                'license_number' => $data['license_number'],
            ]);

            redirect('login', 'Hospital registered successfully! Please login.', 'success');
        } catch (PDOException $e) {
            if ($e->getCode() == 23000) {
                $_SESSION['errors'] = ['An account with this email or hospital code already exists'];
            } else {
                $_SESSION['errors'] = ['Registration failed. Please try again.'];
            }
            $_SESSION['old_input'] = $data;
            redirect('register_hospital');
        }
    }

    /**
     * Process logout
     */
    public function logout() {
        session_destroy();
        header('Location: ' . BASE_URL . '/index.php?page=login');
        exit;
    }

    /**
     * Redirect user to their role-specific dashboard
     */
    private function redirectToDashboard() {
        switch (getUserRole()) {
            case ROLE_ADMIN:
                redirect('admin_dashboard');
                break;
            case ROLE_HOSPITAL:
                redirect('hospital_dashboard');
                break;
            case ROLE_DONOR:
                redirect('donor_dashboard');
                break;
            default:
                redirect('login');
        }
    }

    /**
     * Common registration validation
     *
     * @param array $data
     * @return array
     */
    private function validateRegistration(array $data): array {
        $errors = [];

        if (empty($data['first_name'])) $errors[] = 'First name is required';
        if (empty($data['last_name'])) $errors[] = 'Last name is required';
        if (empty($data['email'])) $errors[] = 'Email is required';
        if (!filter_var($data['email'], FILTER_VALIDATE_EMAIL)) $errors[] = 'Invalid email format';
        if (empty($data['password'])) $errors[] = 'Password is required';
        if (strlen($data['password']) < 6) $errors[] = 'Password must be at least 6 characters';
        if ($data['password'] !== $data['confirm_password']) $errors[] = 'Passwords do not match';

        // Check if email already exists
        if ($this->userModel->findByEmail($data['email'])) {
            $errors[] = 'An account with this email already exists';
        }

        return $errors;
    }
}
