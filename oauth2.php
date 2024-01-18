<?php

require_once(__DIR__ . '/admin/assets/vendor/autoload.php');

use FattureInCloud\OAuth2\OAuth2AuthorizationCodeManager;
use FattureInCloud\OAuth2\Scope;

session_set_cookie_params(86400);
// se non è avviata la sessione la avvia
if (session_status() == PHP_SESSION_NONE)
    session_start();
//unset($_SESSION['token']);
$oauth = new OAuth2AuthorizationCodeManager("gYeBVqHZDNTB8LbuOc4XgLBN2POEoeB7", "8vWWSgLetSGpV7O9wpE4BAvaRorF3beWDNtJeUVb60GXSVS7guWAQAoL0pUESihn", "http://sviluppo4.locl/oauth.php");

if (isset($_SESSION['token'])) {
    die('You already have an access token');
}

if (!isset($_GET['code'])) {
    $url = $oauth->getAuthorizationUrl([Scope::ENTITY_SUPPLIERS_READ, Scope::ENTITY_CLIENTS_READ, Scope::ISSUED_DOCUMENTS_INVOICES_READ, Scope::PRODUCTS_READ], "EXAMPLE_STATE");
    header('location: ' . $url);
} else {
    $code = $_GET['code'];
    $obj = $oauth->fetchToken($code);
    if (!isset($obj->error)) {
        $_SESSION['token'] = $obj->getAccessToken(); //saving the oAuth access token in a session variable
        $_SESSION['refresh'] = $obj->getRefreshToken();
    }

    echo 'Token saved correctly in the session variable';
}
