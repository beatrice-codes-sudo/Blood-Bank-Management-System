<?php
/**
 * Payment Model
 * Handles Paystack API verification and hospital SaaS subscription lifecycle
 */
class Payment {
    /** @var PDO */
    private PDO $db;

    public function __construct() {
        $this->db = Database::getInstance()->getConnection();
    }

    /**
     * Verify transaction reference with Paystack REST API
     * 
     * @param string $reference
     * @return array
     */
    public function verifyPaystackReference(string $reference): array {
        $secretKey = PAYSTACK_SECRET_KEY;
        $url = "https://api.paystack.co/transaction/verify/" . rawurlencode($reference);

        $ch = curl_init();
        curl_setopt($ch, CURLOPT_URL, $url);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($ch, CURLOPT_HTTPHEADER, [
            "Authorization: Bearer " . $secretKey,
            "Cache-Control: no-cache",
        ]);
        curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, true);

        $response = curl_exec($ch);
        $err = curl_error($ch);
        curl_close($ch);

        if ($err) {
            return ['status' => false, 'message' => 'cURL Error: ' . $err];
        }

        return json_decode($response, true) ?: ['status' => false, 'message' => 'Invalid response from Paystack'];
    }

    /**
     * Record successful subscription and update hospital status
     * 
     * @param int $hospitalId
     * @param string $reference
     * @param string $planName
     * @param string $interval
     * @param float $amount
     * @param string $channel
     * @param array $paystackData
     * @return bool
     */
    public function activateHospitalSubscription(int $hospitalId, string $reference, string $planName, string $interval, float $amount, string $channel, array $paystackData): bool {
        $this->db->beginTransaction();
        try {
            // 1. Insert transaction ledger record
            $sql = "INSERT INTO payments (hospital_id, reference, amount, currency, plan_name, billing_interval, channel, status, paystack_response, paid_at)
                    VALUES (:hid, :ref, :amount, :currency, :plan, :interval, :channel, 'Success', :resp, NOW())";
            $stmt = $this->db->prepare($sql);
            $stmt->execute([
                'hid'      => $hospitalId,
                'ref'      => $reference,
                'amount'   => $amount,
                'currency' => PAYSTACK_CURRENCY,
                'plan'     => $planName,
                'interval' => $interval,
                'channel'  => $channel,
                'resp'     => json_encode($paystackData),
            ]);

            // 2. Calculate expiration date (+30 days for Monthly, +365 days for Annual)
            $daysToAdd = ($interval === 'Annual') ? 365 : 30;
            $expiresAt = date('Y-m-d H:i:s', strtotime("+{$daysToAdd} days"));
            $customerCode = $paystackData['customer']['customer_code'] ?? null;

            // 3. Update hospital subscription record
            $sqlHosp = "UPDATE hospitals SET 
                        subscription_status = 'Active',
                        subscription_plan = :plan,
                        billing_interval = :interval,
                        subscription_expires_at = :expires_at,
                        paystack_customer_code = :cust_code,
                        updated_at = NOW()
                        WHERE hospital_id = :hid";
            $stmtHosp = $this->db->prepare($sqlHosp);
            $stmtHosp->execute([
                'plan'       => $planName,
                'interval'   => $interval,
                'expires_at' => $expiresAt,
                'cust_code'  => $customerCode,
                'hid'        => $hospitalId,
            ]);

            $this->db->commit();
            return true;
        } catch (Exception $e) {
            $this->db->rollBack();
            error_log('Subscription Activation Error: ' . $e->getMessage());
            return false;
        }
    }

    /**
     * Get payment history for a hospital
     * 
     * @param int $hospitalId
     * @return array
     */
    public function getPaymentsByHospital(int $hospitalId): array {
        $stmt = $this->db->prepare("SELECT * FROM payments WHERE hospital_id = :hid ORDER BY created_at DESC");
        $stmt->execute(['hid' => $hospitalId]);
        return $stmt->fetchAll();
    }

    /**
     * Get all payment transactions (Admin)
     * 
     * @param int $limit
     * @param int $offset
     * @return array
     */
    public function getAllPayments(int $limit = 50, int $offset = 0): array {
        $sql = "SELECT p.*, h.hospital_name, h.hospital_code 
                FROM payments p
                JOIN hospitals h ON p.hospital_id = h.hospital_id
                ORDER BY p.created_at DESC
                LIMIT :limit OFFSET :offset";
        $stmt = $this->db->prepare($sql);
        $stmt->bindValue('limit', (int)$limit, PDO::PARAM_INT);
        $stmt->bindValue('offset', (int)$offset, PDO::PARAM_INT);
        $stmt->execute();
        return $stmt->fetchAll();
    }
}
