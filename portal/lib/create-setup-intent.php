<?php
// Directly load the Stripe library without globals.php

require_once(__DIR__ . '/../../vendor/autoload.php');
use Stripe\Stripe;
use Stripe\SetupIntent;
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
// Manually define your Stripe secret key here (plaintext or from your own secure store)
$stripeSecretKey = 'sk_test_51Rl3jB4ddd1yI1wnu1ukykkf5gQm95aB9KQN2vRgRPNpZMrnd2NIZ0h32aU4Ah9uLXQvbU6TfWPVf0sCspvZTkAo006hZ2Iqsp'; // Replace with your real key

// Set API key
Stripe::setApiKey($stripeSecretKey);

// Check for customer_id in GET
if (!isset($_GET['customer_id'])) {
    http_response_code(400);
    echo json_encode(['error' => 'Missing customer_id']);
    exit;
}

try {
    $setupIntent = SetupIntent::create([
        'customer' => $_GET['customer_id'],
        'usage' => 'off_session'
    ]);

    echo json_encode(['clientSecret' => $setupIntent->client_secret]);
} catch (Exception $e) {
    http_response_code(500);
    echo json_encode(['error' => $e->getMessage()]);
}
