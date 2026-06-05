<?php
/**
 * Blood Inventory Model
 * Handles blood unit inventory queries
 * Post-consolidation: blood_type is an inline ENUM, no blood_types table
 */
class BloodInventory {
    private $db;

    public function __construct() {
        $this->db = Database::getInstance()->getConnection();
    }

    /**
     * Get inventory summary by blood type
     * Uses BLOOD_TYPES constant to ensure all 8 types appear even with zero stock
     */
    public function getInventorySummary() {
        $sql = "SELECT blood_type, COUNT(*) as unit_count
                FROM blood_units
                WHERE status = 'Available'
                GROUP BY blood_type
                ORDER BY blood_type";
        $stmt = $this->db->query($sql);
        $rows = $stmt->fetchAll();

        // Build a lookup from DB results
        $counts = [];
        foreach ($rows as $row) {
            $counts[$row['blood_type']] = (int)$row['unit_count'];
        }

        // Ensure all 8 blood types appear (fill missing with 0)
        $summary = [];
        foreach (BLOOD_TYPES as $type) {
            $summary[] = [
                'type_name'  => $type,
                'blood_type' => $type,
                'unit_count' => $counts[$type] ?? 0,
            ];
        }

        return $summary;
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
        // Get counts for all types, then filter in PHP to catch zero-stock types
        $summary = $this->getInventorySummary();
        $critical = [];
        foreach ($summary as $row) {
            if ($row['unit_count'] < $threshold) {
                $critical[] = $row;
            }
        }
        return $critical;
    }

    /**
     * Get total pending requests
     */
    public function getPendingRequests() {
        $stmt = $this->db->query("SELECT COUNT(*) as total FROM requests WHERE status = 'Pending'");
        return $stmt->fetch()['total'];
    }
}
