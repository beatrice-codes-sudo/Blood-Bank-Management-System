<?php
/**
 * Blood Inventory Model
 * Handles blood unit inventory queries
 */
class BloodInventory {
    private $db;

    public function __construct() {
        $this->db = Database::getInstance()->getConnection();
    }

    /**
     * Get inventory summary by blood type
     */
    public function getInventorySummary() {
        $sql = "SELECT bt.blood_type_id, bt.type_name, 
                COALESCE(COUNT(bu.unit_id), 0) as unit_count
                FROM blood_types bt
                LEFT JOIN blood_units bu ON bt.blood_type_id = bu.blood_type_id AND bu.status = 'Available'
                GROUP BY bt.blood_type_id, bt.type_name
                ORDER BY bt.blood_type_id";
        $stmt = $this->db->query($sql);
        return $stmt->fetchAll();
    }

    /**
     * Get total available units
     */
    public function getTotalUnits() {
        $stmt = $this->db->query("SELECT COUNT(*) as total FROM blood_units WHERE status = 'Available'");
        return $stmt->fetch()['total'];
    }

    /**
     * Get critical stock (types with less than threshold)
     */
    public function getCriticalStock($threshold = 5) {
        $sql = "SELECT bt.type_name, COUNT(bu.unit_id) as unit_count
                FROM blood_types bt
                LEFT JOIN blood_units bu ON bt.blood_type_id = bu.blood_type_id AND bu.status = 'Available'
                GROUP BY bt.blood_type_id, bt.type_name
                HAVING unit_count < :threshold";
        $stmt = $this->db->prepare($sql);
        $stmt->bindValue('threshold', (int)$threshold, PDO::PARAM_INT);
        $stmt->execute();
        return $stmt->fetchAll();
    }

    /**
     * Get total pending requests
     */
    public function getPendingRequests() {
        $stmt = $this->db->query("SELECT COUNT(*) as total FROM requests WHERE status = 'Pending'");
        return $stmt->fetch()['total'];
    }

    /**
     * Get all blood types
     */
    public function getBloodTypes() {
        $stmt = $this->db->query("SELECT * FROM blood_types ORDER BY blood_type_id");
        return $stmt->fetchAll();
    }
}
