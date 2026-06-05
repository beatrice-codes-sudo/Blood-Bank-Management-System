<?php
/**
 * User Model
 * Handles user authentication and CRUD operations
 * Post-consolidation: absorbs donor/staff fields directly
 */
class User {
    private $db;

    public function __construct() {
        $this->db = Database::getInstance()->getConnection();
    }

    /**
     * Register a new user (includes donor/staff fields if applicable)
     */
    public function register($data) {
        $sql = "INSERT INTO users (role, username, email, password_hash, first_name, last_name, phone, is_active,
                    blood_type, date_of_birth, gender, address, city, eligibility_status)
                VALUES (:role, :username, :email, :password_hash, :first_name, :last_name, :phone, :is_active,
                    :blood_type, :date_of_birth, :gender, :address, :city, :eligibility_status)";
        
        $stmt = $this->db->prepare($sql);
        $stmt->execute([
            'role'               => $data['role'],
            'username'           => $data['username'],
            'email'              => $data['email'],
            'password_hash'      => password_hash($data['password'], PASSWORD_BCRYPT),
            'first_name'         => $data['first_name'],
            'last_name'          => $data['last_name'],
            'phone'              => $data['phone'] ?? null,
            'is_active'          => $data['is_active'] ?? 1,
            'blood_type'         => $data['blood_type'] ?? null,
            'date_of_birth'      => $data['date_of_birth'] ?? null,
            'gender'             => $data['gender'] ?? null,
            'address'            => $data['address'] ?? null,
            'city'               => $data['city'] ?? null,
            'eligibility_status' => $data['eligibility_status'] ?? 'Eligible',
        ]);

        return $this->db->lastInsertId();
    }

    /**
     * Authenticate user login
     */
    public function login($email, $password) {
        $sql = "SELECT * FROM users
                WHERE email = :email AND is_active = 1";
        
        $stmt = $this->db->prepare($sql);
        $stmt->execute(['email' => $email]);
        $user = $stmt->fetch();

        if ($user && password_verify($password, $user['password_hash'])) {
            // Update last login
            $this->updateLastLogin($user['user_id']);
            return $user;
        }

        return false;
    }

    /**
     * Find user by email
     */
    public function findByEmail($email) {
        $stmt = $this->db->prepare("SELECT * FROM users WHERE email = :email");
        $stmt->execute(['email' => $email]);
        return $stmt->fetch();
    }

    /**
     * Find user by username
     */
    public function findByUsername($username) {
        $stmt = $this->db->prepare("SELECT * FROM users WHERE username = :username");
        $stmt->execute(['username' => $username]);
        return $stmt->fetch();
    }

    /**
     * Find user by ID
     */
    public function findById($id) {
        $stmt = $this->db->prepare("SELECT * FROM users WHERE user_id = :id");
        $stmt->execute(['id' => $id]);
        return $stmt->fetch();
    }

    /**
     * Get all users (admin)
     */
    public function getAll($limit = 50, $offset = 0) {
        $stmt = $this->db->prepare(
            "SELECT * FROM users
             ORDER BY created_at DESC
             LIMIT :limit OFFSET :offset"
        );
        $stmt->bindValue('limit', (int)$limit, PDO::PARAM_INT);
        $stmt->bindValue('offset', (int)$offset, PDO::PARAM_INT);
        $stmt->execute();
        return $stmt->fetchAll();
    }

    /**
     * Count all users
     */
    public function countAll() {
        $stmt = $this->db->query("SELECT COUNT(*) as total FROM users");
        return $stmt->fetch()['total'];
    }

    /**
     * Count users by role
     */
    public function countByRole($role) {
        $stmt = $this->db->prepare("SELECT COUNT(*) as total FROM users WHERE role = :role AND is_active = 1");
        $stmt->execute(['role' => $role]);
        return $stmt->fetch()['total'];
    }

    /**
     * Update user active status
     */
    public function updateStatus($userId, $isActive) {
        $stmt = $this->db->prepare("UPDATE users SET is_active = :is_active, updated_at = NOW() WHERE user_id = :user_id");
        return $stmt->execute(['is_active' => $isActive, 'user_id' => $userId]);
    }

    /**
     * Update last login timestamp
     */
    private function updateLastLogin($userId) {
        $stmt = $this->db->prepare("UPDATE users SET last_login = NOW() WHERE user_id = :user_id");
        $stmt->execute(['user_id' => $userId]);
    }

    /**
     * Get recent registrations
     */
    public function getRecentRegistrations($limit = 10) {
        $stmt = $this->db->prepare(
            "SELECT * FROM users
             ORDER BY created_at DESC
             LIMIT :limit"
        );
        $stmt->bindValue('limit', (int)$limit, PDO::PARAM_INT);
        $stmt->execute();
        return $stmt->fetchAll();
    }
}
