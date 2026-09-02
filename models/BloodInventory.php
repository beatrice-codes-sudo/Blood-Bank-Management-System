<?php
/**
 * Blood Inventory Model
 * Handles blood unit inventory queries
 * Post-consolidation: blood_type is an inline ENUM, no blood_types table
 */
class BloodInventory {
    /** @var PDO */
    private PDO $db;

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
     * Get minimum threshold settings per blood type
     *
     * @return array Associative array ['A+' => 5, 'O-' => 8, ...]
     */
    public function getThresholds(): array {
        $defaults = [
            'O-' => 8,
            'O+' => 8,
            'A+' => 5,
            'B+' => 5,
            'A-' => 4,
            'B-' => 4,
            'AB+' => 3,
            'AB-' => 3,
        ];

        try {
            $stmt = $this->db->query("SELECT blood_type, min_threshold FROM stock_thresholds");
            $rows = $stmt->fetchAll(PDO::FETCH_KEY_PAIR);
            if (!empty($rows)) {
                return array_merge($defaults, $rows);
            }
        } catch (Exception $e) {
            // Table might not exist yet; return defaults
        }

        return $defaults;
    }

    /**
     * Update stock threshold settings for all blood types
     *
     * @param array $thresholds
     * @return bool
     */
    public function updateThresholds(array $thresholds): bool {
        try {
            $sql = "INSERT INTO stock_thresholds (blood_type, min_threshold) 
                    VALUES (:blood_type, :min_threshold)
                    ON DUPLICATE KEY UPDATE min_threshold = VALUES(min_threshold), updated_at = NOW()";
            $stmt = $this->db->prepare($sql);
            foreach ($thresholds as $bType => $val) {
                if (in_array($bType, BLOOD_TYPES)) {
                    $stmt->execute([
                        'blood_type'    => $bType,
                        'min_threshold' => max(1, (int)$val)
                    ]);
                }
            }
            return true;
        } catch (Exception $e) {
            return false;
        }
    }

    /**
     * Get comprehensive inventory summary with thresholds and deficit stats
     */
    public function getInventorySummaryWithThresholds(): array {
        $thresholds = $this->getThresholds();
        $summary = $this->getInventorySummary();

        foreach ($summary as &$item) {
            $type = $item['blood_type'];
            $count = (int)$item['unit_count'];
            $threshold = (int)($thresholds[$type] ?? 5);

            $item['min_threshold'] = $threshold;
            $item['deficit'] = max(0, $threshold - $count);
            $item['fill_percent'] = $threshold > 0 ? min(100, round(($count / $threshold) * 100)) : 100;
            
            if ($count === 0) {
                $item['stock_status'] = 'Empty';
                $item['status_badge'] = 'bg-red-100 text-red-700 border-red-300';
            } elseif ($count < $threshold) {
                $item['stock_status'] = 'Critical';
                $item['status_badge'] = 'bg-amber-100 text-amber-800 border-amber-300';
            } else {
                $item['stock_status'] = 'Adequate';
                $item['status_badge'] = 'bg-emerald-100 text-emerald-800 border-emerald-300';
            }
        }

        return $summary;
    }

    /**
     * Get critical stock (types with less than their specific minimum threshold)
     *
     * @param int|null $fallbackThreshold
     * @return array
     */
    public function getCriticalStock($fallbackThreshold = null) {
        $summary = $this->getInventorySummaryWithThresholds();
        $critical = [];

        foreach ($summary as $row) {
            $threshold = $fallbackThreshold !== null ? (int)$fallbackThreshold : (int)$row['min_threshold'];
            if ($row['unit_count'] < $threshold) {
                $critical[] = $row;
            }
        }
        return $critical;
    }

    /**
     * Add batch of blood units into inventory (Intake)
     *
     * @param string $bloodType
     * @param int $unitsCount
     * @param int $volumeMl
     * @param string $collectionDate
     * @param string|null $expiryDate
     * @return int Number of units added
     */
    public function addBloodUnitsBatch(string $bloodType, int $unitsCount, int $volumeMl = 450, string $collectionDate = '', ?string $expiryDate = null): int {
        if (!in_array($bloodType, BLOOD_TYPES) || $unitsCount <= 0) {
            return 0;
        }

        if (empty($collectionDate)) {
            $collectionDate = date('Y-m-d');
        }

        if (empty($expiryDate)) {
            // Whole blood standard shelf life in CPDA-1 anticoagulant: 42 days
            $expiryDate = date('Y-m-d', strtotime($collectionDate . ' + 42 days'));
        }

        $sql = "INSERT INTO blood_units (blood_type, status, collection_date, expiry_date, volume_ml, created_at)
                VALUES (:blood_type, 'Available', :collection_date, :expiry_date, :volume_ml, NOW())";
        $stmt = $this->db->prepare($sql);

        $inserted = 0;
        for ($i = 0; $i < $unitsCount; $i++) {
            $stmt->execute([
                'blood_type'      => $bloodType,
                'collection_date' => $collectionDate,
                'expiry_date'     => $expiryDate,
                'volume_ml'       => $volumeMl
            ]);
            $inserted++;
        }

        return $inserted;
    }

    /**
     * Publish or update an active in-app emergency appeal for a blood group
     *
     * @param string $bloodType
     * @param string $urgency
     * @param string $message
     * @param int $adminId
     * @return int New Appeal ID
     */
    public function publishEmergencyAppeal(string $bloodType, string $urgency, string $message, int $adminId): int {
        // Deactivate previous active appeals for this blood type
        $deactSql = "UPDATE emergency_appeals SET is_active = 0, resolved_at = NOW() 
                     WHERE blood_type = :blood_type AND is_active = 1";
        $deactStmt = $this->db->prepare($deactSql);
        $deactStmt->execute(['blood_type' => $bloodType]);

        // Insert new active appeal
        $sql = "INSERT INTO emergency_appeals (blood_type, urgency, message, is_active, created_by, created_at)
                VALUES (:blood_type, :urgency, :message, 1, :created_by, NOW())";
        $stmt = $this->db->prepare($sql);
        $stmt->execute([
            'blood_type' => $bloodType,
            'urgency'    => $urgency,
            'message'    => $message,
            'created_by' => $adminId,
        ]);

        return (int)$this->db->lastInsertId();
    }

    /**
     * Get all currently active emergency appeals
     *
     * @return array
     */
    public function getActiveAppeals(): array {
        try {
            $stmt = $this->db->query("SELECT * FROM emergency_appeals WHERE is_active = 1 ORDER BY created_at DESC");
            return $stmt->fetchAll();
        } catch (Exception $e) {
            return [];
        }
    }

    /**
     * Get active emergency appeal relevant for a specific donor
     * (matching their blood group or compatible)
     *
     * @param string|null $donorBloodType
     * @return array|null
     */
    public function getActiveAppealForDonor(?string $donorBloodType): ?array {
        if (!$donorBloodType) {
            return null;
        }

        try {
            $sql = "SELECT * FROM emergency_appeals 
                    WHERE is_active = 1 
                    AND blood_type = :blood_type 
                    ORDER BY created_at DESC 
                    LIMIT 1";
            $stmt = $this->db->prepare($sql);
            $stmt->execute(['blood_type' => $donorBloodType]);
            $appeal = $stmt->fetch();
            return $appeal ?: null;
        } catch (Exception $e) {
            return null;
        }
    }

    /**
     * Resolve / dismiss an active emergency appeal
     *
     * @param int $appealId
     * @return bool
     */
    public function resolveEmergencyAppeal(int $appealId): bool {
        try {
            $stmt = $this->db->prepare("UPDATE emergency_appeals SET is_active = 0, resolved_at = NOW() WHERE appeal_id = :appeal_id");
            return $stmt->execute(['appeal_id' => $appealId]);
        } catch (Exception $e) {
            return false;
        }
    }

    /**
     * Get eligible registered donors matching a blood group for emergency appeal
     *
     * @param string $bloodType
     * @return array
     */
    public function getEligibleDonorsForAppeal(string $bloodType): array {
        $sql = "SELECT u.user_id, u.first_name, u.last_name, u.email, u.phone, u.blood_type, 
                       u.gender, u.city, u.created_at,
                       MAX(d.donation_date) as last_donation_date,
                       DATEDIFF(NOW(), MAX(d.donation_date)) as days_since_last
                FROM users u
                LEFT JOIN donations d ON u.user_id = d.donor_id AND d.status = 'Completed'
                WHERE u.role = 'Donor'
                AND u.is_active = 1
                AND (u.eligibility_status = 'Eligible' OR u.eligibility_status IS NULL)
                AND u.blood_type = :blood_type
                GROUP BY u.user_id
                HAVING last_donation_date IS NULL OR days_since_last >= 90
                ORDER BY last_donation_date ASC";
        $stmt = $this->db->prepare($sql);
        $stmt->execute(['blood_type' => $bloodType]);
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
     * Get count of available units of a specific blood type
     *
     * @param string $bloodType
     * @return int
     */
    public function getAvailableCountByType(string $bloodType): int {
        $stmt = $this->db->prepare("SELECT COUNT(*) as total FROM blood_units WHERE blood_type = :blood_type AND status = 'Available'");
        $stmt->execute(['blood_type' => $bloodType]);
        return (int)($stmt->fetch()['total'] ?? 0);
    }

    /**
     * Dispatch blood units for a request
     * Updates blood_units, inserts distribution record, updates request status/notes.
     *
     * @param int $requestId
     * @param int $hospitalId
     * @param int $adminId
     * @param string $bloodType
     * @param int $unitsToDispatch
     * @param string $targetStatus
     * @return array
     */
    public function dispatchUnitsForRequest(int $requestId, int $hospitalId, int $adminId, string $bloodType, int $unitsToDispatch, string $targetStatus): array {
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

