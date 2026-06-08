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

    /**
     * Get count of available units of a specific blood type
     */
    public function getAvailableCountByType($bloodType) {
        $stmt = $this->db->prepare("SELECT COUNT(*) as total FROM blood_units WHERE blood_type = :blood_type AND status = 'Available'");
        $stmt->execute(['blood_type' => $bloodType]);
        return (int)($stmt->fetch()['total'] ?? 0);
    }

    /**
     * Dispatch blood units for a request
     * Updates blood_units, inserts distribution record, updates request status/notes.
     */
    public function dispatchUnitsForRequest($requestId, $hospitalId, $adminId, $bloodType, $unitsToDispatch, $targetStatus) {
        if ($unitsToDispatch == 0) {
            $reqSql = "UPDATE requests SET 
                       status = :status,
                       notes = CONCAT(COALESCE(notes, ''), :dispatch_note),
                       updated_at = NOW()
                       WHERE request_id = :request_id";
            $reqStmt = $this->db->prepare($reqSql);
            $dispatchNote = "\n[System " . date('Y-m-d H:i:s') . ": Request updated to {$targetStatus} without dispatching units.]";
            $reqStmt->execute([
                'status'        => $targetStatus,
                'dispatch_note' => $dispatchNote,
                'request_id'    => $requestId
            ]);
            return [
                'success' => true,
                'message' => "Request status updated to {$targetStatus}."
            ];
        }

        // 1. Verify availability
        $availableCount = $this->getAvailableCountByType($bloodType);
        
        if ($availableCount < $unitsToDispatch) {
            // Units NOT available. Update request to Pending and notify hospital manager.
            $notifyNote = "\n[Notification " . date('Y-m-d H:i:s') . ": Admin attempted to dispatch " . $unitsToDispatch . " units of " . $bloodType . ", but only " . $availableCount . " were available. Request updated to Pending.]";
            
            $sql = "UPDATE requests SET 
                    status = 'Pending',
                    notes = CONCAT(COALESCE(notes, ''), :notify_note),
                    updated_at = NOW()
                    WHERE request_id = :request_id";
            $stmt = $this->db->prepare($sql);
            $stmt->execute([
                'notify_note' => $notifyNote,
                'request_id'  => $requestId
            ]);
            return [
                'success' => false,
                'message' => "Insufficient inventory. Required: {$unitsToDispatch}, Available: {$availableCount}. Status set to Pending. Hospital manager notified."
            ];
        }

        // 2. Units available! Start transaction to ensure atomicity
        $this->db->beginTransaction();
        try {
            // Find oldest available units (FIFO)
            // MySQL LIMIT in prepared statements can be tricky depending on PDO configuration, so let's bind it as parameter or fetch then update
            $sql = "SELECT unit_id FROM blood_units 
                    WHERE blood_type = :blood_type AND status = 'Available' 
                    ORDER BY collection_date ASC LIMIT " . (int)$unitsToDispatch;
            $stmt = $this->db->prepare($sql);
            $stmt->execute(['blood_type' => $bloodType]);
            $units = $stmt->fetchAll(PDO::FETCH_COLUMN);

            if (count($units) < $unitsToDispatch) {
                throw new Exception("Inventory check race condition: units count mismatch.");
            }

            // Update individual units to 'Dispatched'
            $updateStmt = $this->db->prepare("UPDATE blood_units SET status = 'Dispatched' WHERE unit_id = :unit_id");
            foreach ($units as $unitId) {
                $updateStmt->execute(['unit_id' => $unitId]);
            }

            // Create distribution record
            $distSql = "INSERT INTO distributions (request_id, hospital_id, dispatched_by, transport_method, receiver_name, delivery_notes, is_delivered)
                        VALUES (:request_id, :hospital_id, :dispatched_by, 'Courier', 'Hospital Manager', :delivery_notes, 0)";
            $distStmt = $this->db->prepare($distSql);
            $deliveryNotes = "Dispatched " . count($units) . " units of " . $bloodType . " (FIFO).";
            $distStmt->execute([
                'request_id'     => $requestId,
                'hospital_id'    => $hospitalId,
                'dispatched_by'  => $adminId,
                'delivery_notes' => $deliveryNotes
            ]);

            // Update request
            $dispatchNote = "\n[System " . date('Y-m-d H:i:s') . ": Dispatched " . $unitsToDispatch . " units of " . $bloodType . ".]";
            $reqSql = "UPDATE requests SET 
                       units_fulfilled = COALESCE(units_fulfilled, 0) + :dispatched,
                       status = :status,
                       notes = CONCAT(COALESCE(notes, ''), :dispatch_note),
                       updated_at = NOW()
                       WHERE request_id = :request_id";
            $reqStmt = $this->db->prepare($reqSql);
            $reqStmt->execute([
                'dispatched'    => $unitsToDispatch,
                'status'        => $targetStatus,
                'dispatch_note' => $dispatchNote,
                'request_id'    => $requestId
            ]);

            $this->db->commit();
            return [
                'success' => true,
                'message' => "Successfully dispatched {$unitsToDispatch} units of {$bloodType}. Request status updated to {$targetStatus}."
            ];
        } catch (Exception $e) {
            $this->db->rollBack();
            return [
                'success' => false,
                'message' => "Failed to dispatch units: " . $e->getMessage()
            ];
        }
    }
}

