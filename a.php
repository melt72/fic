<?php
session_start();

use FattureInCloud\Api\SuppliersApi;
use FattureInCloud\Api\UserApi;
use FattureInCloud\Configuration;
use GuzzleHttp\Client;

require __DIR__ . 'admin/assets/vendor/autoload.php';


// Retrieve the access token from the session variable
$accessToken = $_SESSION['token'];
echo 'token ' . $accessToken;

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

try {
    // Retrieve the first company id
    $companies = $userApi->listUserCompanies();
    $firstCompanyId = $companies->getData()->getCompanies()[0]->getId();

    // Retrieve the list of first 10 Suppliers for the selected company
    $suppliers = $suppliersApi->listSuppliers($firstCompanyId, null, null, null, 1, 10);
    foreach ($suppliers->getData() as $supplier) {
        $name = $supplier->getName();
        echo ("$name </br>n");
    }
} catch (Exception $e) {
    echo 'Exception when calling the API: ', $e->getMessage(), PHP_EOL;
}
