<?php

require_once(dirname(__FILE__) . "/../../src/Common/Session/SessionUtil.php");
OpenEMR\Common\Session\SessionUtil::portalSessionStart();

if (isset($_SESSION['pid']) && isset($_SESSION['patient_portal_onsite_two'])) {
    $pid = $_SESSION['pid'];
    $ignoreAuth_onsite_portal = true;
    require_once(dirname(__FILE__) . "/../../interface/globals.php");
} else {
    OpenEMR\Common\Session\SessionUtil::portalSessionCookieDestroy();
    $ignoreAuth = false;
    require_once(dirname(__FILE__) . "/../../interface/globals.php");
    if (!isset($_SESSION['authUserID'])) {
        $landingpage = "index.php";
        header('Location: ' . $landingpage);
        exit();
    }
}

if (empty($_SESSION['site_id'])) {
    $_SESSION['site_id'] = 'default';
    error_log("Temporary fix: Setting default site_id");
}

require_once(__DIR__ . '/../../vendor/autoload.php');
require_once(__DIR__ . '/../../interface/globals.php');

header('Content-Type: application/json');

$input = json_decode(file_get_contents('php://input'), true);
if (json_last_error() !== JSON_ERROR_NONE || !$input) {
    http_response_code(400);
    echo json_encode(['error' => 'Invalid JSON input']);
    exit;
}

$required = ['pid', 'payment_method', 'customer_id'];
foreach ($required as $field) {
    if (empty($input[$field])) {
        http_response_code(400);
        echo json_encode(['error' => "Missing required field: $field"]);
        exit;
    }
}

try {
    $pid = intval($input['pid']);
    $payment_method_id = trim($input['payment_method']);
    $stripe_customer_id = trim($input['customer_id']);

    if (!$pid || !$payment_method_id || !$stripe_customer_id) {
        throw new Exception('Invalid input data');
    }

    $patientExists = sqlQuery("SELECT pid FROM patient_data WHERE pid = ?", [$pid]);
    if (!$patientExists) {
        throw new Exception('Patient does not exist');
    }

    $existing = sqlQuery(
        "SELECT id FROM patient_stripe WHERE pid = ?",
        [$pid]
    );

    if ($existing) {

        $result = sqlStatement(
            "UPDATE patient_stripe SET 
            stripe_customer_id = ?,
            stripe_payment_method_id = ?,
            updated_at = NOW()
            WHERE pid = ?",
            [$stripe_customer_id, $payment_method_id, $pid]
        );

        $message = 'Payment method updated';
    } else {

        $result = sqlStatement(
            "INSERT INTO patient_stripe 
            (pid, stripe_customer_id, stripe_payment_method_id, created_at, updated_at) 
            VALUES (?, ?, ?, NOW(), NOW())",
            [$pid, $stripe_customer_id, $payment_method_id]
        );

        $message = 'Payment method saved successfully';
    }

    if ($result === false) {
        throw new Exception('Database operation failed');
    }

    echo json_encode([
        'success' => true,
        'message' => $message,
        'pid' => $pid,
        'customer_id' => $stripe_customer_id
    ]);
} catch (Exception $e) {
    http_response_code(500);
    echo json_encode(['error' => $e->getMessage()]);
}
