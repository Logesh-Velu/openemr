    <?php
    // File: openemr-7.0.3/portal/lib/create-stripe-customer.php

    use OpenEMR\Common\Crypto\CryptoGen;

    // Start session if not already started
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

    header('Content-Type: application/json');

    // Check site_id in session
    if (empty($_SESSION['site_id'])) {
        echo json_encode(['error' => 'Site ID is missing from session data!']);
        exit;
    }

    // Handle pid
    $pid = $_SESSION['pid'] ?? 0;
    if (!$pid) {
        error_log("Warning: pid is missing from session. Using fallback PID: 0");
    }

    // Get JSON input
    $input = json_decode(file_get_contents('php://input'), true);
    $payment_method_id = $input['payment_method'] ?? null;

    if (!$payment_method_id) {
        echo json_encode(['error' => 'Missing payment method.']);
        exit;
    }

    // Load Stripe SDK
    require_once(__DIR__ . '/../../vendor/autoload.php');

    // Set Stripe API key
    $stripeSecretKey = 'sk_test_51Rl3jB4ddd1yI1wnu1ukykkf5gQm95aB9KQN2vRgRPNpZMrnd2NIZ0h32aU4Ah9uLXQvbU6TfWPVf0sCspvZTkAo006hZ2Iqsp';
    \Stripe\Stripe::setApiKey($stripeSecretKey);

    try {
        // Create customer with the payment method
        $customer = \Stripe\Customer::create([
            'description' => "OpenEMR Patient #$pid",
            'payment_method' => $payment_method_id,
            'invoice_settings' => ['default_payment_method' => $payment_method_id]
        ]);

        // Attach payment method to customer
        $paymentMethod = \Stripe\PaymentMethod::retrieve($payment_method_id);
        $paymentMethod->attach(['customer' => $customer->id]);

        echo json_encode([
            'customer_id' => $customer->id,
            'payment_method' => $payment_method_id
        ]);

    } catch (\Stripe\Exception\ApiErrorException $e) {
        echo json_encode(['error' => 'Stripe API Error: ' . $e->getMessage()]);
    } catch (Exception $e) {
        echo json_encode(['error' => $e->getMessage()]);
    }