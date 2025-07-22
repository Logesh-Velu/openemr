<?php
// openemr-7.0.3/google-login-handler.php

require_once("interface/globals.php");
require_once("src/Common/Auth/GoogleAuth.php");

use OpenEMR\Common\Auth\GoogleAuth;
use GuzzleHttp\Client;

// Only for development environments!
$httpClient = new Client([
    'verify' => false // Disables SSL verification (INSECURE for production)
]);

$googleAuth = new GoogleAuth();
$googleAuth->setHttpClient($httpClient);
$googleAuth->handleCallback();