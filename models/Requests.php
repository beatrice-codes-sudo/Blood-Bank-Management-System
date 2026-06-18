<?php
//this the model file for requests
class RequestsModel{
    private $db;
    public function __construct() {
        $this->db = Database::getInstance()->getConnection();
    }

    //get all requests with pagination and filters
    public function getAllRequests($limit = 10, $offset = 0, $search = '', $status = '') {
        $sql = "SELECT r.*, 
                u.first_name, u.last_name,
                h.hospital_name
                FROM requests r
                LEFT JOIN users u ON r.requested_by = u.user_id
                LEFT JOIN hospitals h ON r.hospital_id = h.hospital_id
                WHERE 1=1";
        
        $params = [];
        
        if (!empty($status)) {
            $sql .= " AND r.status = :status";
            $params[':status'] = $status;
        }
        
        if (!empty($search)) {
            $sql .= " AND (u.first_name LIKE :search1 OR u.last_name LIKE :search2 OR h.hospital_name LIKE :search3 OR r.blood_type LIKE :search4)";
            $params[':search1'] = "%$search%";
            $params[':search2'] = "%$search%";
            $params[':search3'] = "%$search%";
            $params[':search4'] = "%$search%";
        }
        
        $sql .= " ORDER BY r.created_at DESC LIMIT :limit OFFSET :offset";
        
        $stmt = $this->db->prepare($sql);
        foreach ($params as $key => $value) {
            $stmt->bindValue($key, $value);
        }
        $stmt->bindValue(':limit', (int)$limit, PDO::PARAM_INT);
        $stmt->bindValue(':offset', (int)$offset, PDO::PARAM_INT);
        $stmt->execute();
        return $stmt->fetchAll();
    }
    
    //count total requests for pagination
    public function getTotalRequestsCount($search = '', $status = '') {
        $sql = "SELECT COUNT(*) as total 
                FROM requests r
                LEFT JOIN users u ON r.requested_by = u.user_id
                LEFT JOIN hospitals h ON r.hospital_id = h.hospital_id
                WHERE 1=1";
                
        $params = [];
        
        if (!empty($status)) {
            $sql .= " AND r.status = :status";
            $params[':status'] = $status;
        }
        
        if (!empty($search)) {
            $sql .= " AND (u.first_name LIKE :search1 OR u.last_name LIKE :search2 OR h.hospital_name LIKE :search3 OR r.blood_type LIKE :search4)";
            $params[':search1'] = "%$search%";
            $params[':search2'] = "%$search%";
            $params[':search3'] = "%$search%";
            $params[':search4'] = "%$search%";
        }
        
        $stmt = $this->db->prepare($sql);
        $stmt->execute($params);
        return $stmt->fetch()['total'];
    }
    //get request by id
    public function getRequestById($requestId) {
        $stmt = $this->db->prepare("SELECT * FROM requests WHERE request_id = :request_id");
        $stmt->execute(['request_id' => $requestId]);
        return $stmt->fetch();
    }
    //update request
    public function updateRequest($requestId, $data) {
        $stmt = $this->db->prepare("UPDATE requests SET status = :status, updated_at = NOW() WHERE request_id = :request_id");
        $stmt->execute(['request_id' => $requestId, 'status' => $data['status']]);
        return $stmt->rowCount();
    }
    //delete request
    public function deleteRequest($requestId) {
        $stmt = $this->db->prepare("DELETE FROM requests WHERE request_id = :request_id");
        $stmt->execute(['request_id' => $requestId]);
        return $stmt->rowCount();
    }
    //get request by user id
    public function getRequestByUserId($userId) {
        $stmt = $this->db->prepare("SELECT * FROM requests WHERE requested_by = :user_id");
        $stmt->execute(['user_id' => $userId]);
        return $stmt->fetchAll();
    }
    //get request by hospital id
    public function getRequestByHospitalId($hospitalId) {
        $stmt = $this->db->prepare("SELECT * FROM requests WHERE hospital_id = :hospital_id");
        $stmt->execute(['hospital_id' => $hospitalId]);
        return $stmt->fetchAll();
    }

    //get request by blood type
    public function getRequestByBloodType($bloodType) {
        $stmt = $this->db->prepare("SELECT * FROM requests WHERE blood_type = :blood_type");
        $stmt->execute(['blood_type' => $bloodType]);
        return $stmt->fetchAll();
    }
    //get request by blood type and status
    public function getRequestByBloodTypeAndStatus($bloodType, $status) {
        $stmt = $this->db->prepare("SELECT * FROM requests WHERE blood_type = :blood_type AND status = :status");
        $stmt->execute(['blood_type' => $bloodType, 'status' => $status]);
        return $stmt->fetchAll();
    }

}