<?php
/**
 * Patient Portal Auto-Payment Cron Job - Final Version (Corrected)
 */

// Define OpenEMR root
$openemr_path = realpath(__DIR__ . '/../../') . '/';
require_once($openemr_path . 'vendor/autoload.php');

// Database configuration
$db_host = 'localhost';
$db_port = '3306';
$db_user = 'openemr';
$db_pass = 'openemr';
$db_name = 'openemr';

try {
    $db = new PDO(
        "mysql:host=$db_host;port=$db_port;dbname=$db_name",
        $db_user,
        $db_pass,
        [PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION]
    );
} catch (PDOException $e) {
    die("DB Connection failed: " . $e->getMessage() . "\n");
}

// Stripe configuration
\Stripe\Stripe::setApiKey('sk_test_51Rl3jB4ddd1yI1wnu1ukykkf5gQm95aB9KQN2vRgRPNpZMrnd2NIZ0h32aU4Ah9uLXQvbU6TfWPVf0sCspvZTkAo006hZ2Iqsp');

try {
    $stmt = $db->query("SELECT * FROM patient_stripe");
    while ($row = $stmt->fetch()) {
        $pid = $row['pid'];
        $customerId = $row['stripe_customer_id'];
        $paymentMethodId = $row['stripe_payment_method_id'];

        $balance = getPatientBalance($db, $pid);

        if ($balance > 0) {
            echo "[" . date('Y-m-d H:i:s') . "] Charging PID $pid: $$balance\n";
            processAutoPayment($db, $pid, $balance, $customerId, $paymentMethodId);
            echo "[" . date('Y-m-d H:i:s') . "] Success for PID $pid\n";
        }
    }
} catch (Exception $e) {
    echo "ERROR: " . $e->getMessage() . "\n";
}

/**
 * Get outstanding patient balance
 */
function getPatientBalance(PDO $db, $pid) {
    $stmt = $db->prepare("
        SELECT SUM(fee) AS balance 
        FROM billing 
        WHERE pid = ? AND activity = 1
    ");
    $stmt->execute([$pid]);
    $row = $stmt->fetch();
    return $row['balance'] ?? 0;
}

/**
 * Process Stripe Payment and record in OpenEMR
 */
function processAutoPayment(PDO $db, $pid, $amount, $customerId, $paymentMethodId) {
    try {
        $intent = \Stripe\PaymentIntent::create([
            'amount' => intval($amount * 100), // in cents
            'currency' => 'usd',
            'customer' => $customerId,
            'payment_method' => $paymentMethodId,
            'off_session' => true,
            'confirm' => true,
            'description' => "Auto-payment for PID $pid"
        ]);

        // Insert into `payments`
        $db->prepare("
            INSERT INTO payments 
                (pid, dtime, encounter, user, method, source, amount1, amount2, posted1, posted2)
            VALUES 
                (:pid, :dtime, :encounter, :user, :method, :source, :amount1, :amount2, :posted1, :posted2)
        ")->execute([
            ':pid' => $pid,
            ':dtime' => date('Y-m-d H:i:s'),
            ':encounter' => 0,
            ':user' => 'auto',
            ':method' => 'Stripe',
            ':source' => 'AutoPay',
            ':amount1' => $amount,
            ':amount2' => 0.00,
            ':posted1' => 1,
            ':posted2' => 0
        ]);

        // Insert into `onsite_portal_activity`
        $stmt2 = $db->prepare("
            INSERT INTO onsite_portal_activity
                (`date`, `patient_id`, `activity`, `require_audit`, `pending_action`, `action_taken`, `status`, `narrative`, `table_action`, `table_args`, `action_user`, `action_taken_time`, `checksum`)
            VALUES
                (:date, :patient_id, :activity, :require_audit, :pending_action, :action_taken, :status, :narrative, :table_action, :table_args, :action_user, :action_taken_time, :checksum)
        ");
        $stmt2->execute([
            ':date' => date('Y-m-d H:i:s'),
            ':patient_id' => $pid,
            ':activity' => 'payment',
            ':require_audit' => 1,
            ':pending_action' => 'review',
            ':action_taken' => 'autopayment',
            ':status' => 'waiting',
            ':narrative' => 'Auto-payment processed via Stripe.',
            ':table_action' => 'autopayment.php',
            ':table_args' => json_encode([
                'form_pid' => $pid,
                'form_save' => 'Invoice',
                'form_method' => 'credit_card',
                'autopayment' => 'autopayment',
                'radio_type_of_coverage' => 'insurance',
                'radio_type_of_payment' => 'invoice_balance',
                'form_paytotal' => $amount,
                'hidden_patient_code' => $pid
            ]),
            ':action_user' => 0,
            ':action_taken_time' => '0000-00-00 00:00:00',
            ':checksum' => hash('sha256', uniqid("pid{$pid}amt{$amount}", true))
        ]);

    } catch (\Stripe\Exception\CardException $e) {
        throw new Exception("Stripe declined card: " . $e->getError()->message);
    } catch (Exception $e) {
        throw new Exception("Payment error: " . $e->getMessage());
    }
}
