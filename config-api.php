<?php

use FattureInCloud\Api\SuppliersApi;
use FattureInCloud\Api\ClientsApi;
use FattureInCloud\Api\IssuedDocumentsApi;
use FattureInCloud\Api\UserApi;
use FattureInCloud\Configuration;
use GuzzleHttp\Client;

require __DIR__ . '/admin/assets/vendor/autoload.php';

// se la sessione non è attiva la avvio
if (session_status() == PHP_SESSION_NONE) {
    session_start();
}


// Retrieve the access token from the session variable
$accessToken = $_SESSION['token'];

// Get the API config and construct the service object.
$config = FattureInCloud\Configuration::getDefaultConfiguration()->setAccessToken($accessToken);

$userApi = new FattureInCloud\Api\UserApi(
    new GuzzleHttp\Client(),
    $config
);
$suppliersApi = new FattureInCloud\Api\SuppliersApi(
    new GuzzleHttp\Client(),
    $config
);
$clientsAPI = new FattureInCloud\Api\ClientsApi(
    new GuzzleHttp\Client(),
    $config
);
$issuedEInvoicesApi = new FattureInCloud\Api\IssuedDocumentsApi(
    new GuzzleHttp\Client(),
    $config
);
$productsApi = new FattureInCloud\Api\ProductsApi(
    new GuzzleHttp\Client(),
    $config
);
