<?php
require __DIR__ . '/admin/assets/vendor/autoload.php';
include 'include/configpdo.php';

use FattureInCloud\Api\SuppliersApi;
use FattureInCloud\Api\ClientsApi;
use FattureInCloud\Api\IssuedDocumentsApi;
use FattureInCloud\Api\UserApi;
use FattureInCloud\Configuration;
use GuzzleHttp\Client;
use FattureInCloud\OAuth2\OAuth2AuthorizationCodeManager;
use FattureInCloud\OAuth2\Scope;


// se la sessione non è attiva la avvio
if (session_status() == PHP_SESSION_NONE) {
    session_start();
}
//cancello il token
//

$oauth = new OAuth2AuthorizationCodeManager("gYeBVqHZDNTB8LbuOc4XgLBN2POEoeB7", "8vWWSgLetSGpV7O9wpE4BAvaRorF3beWDNtJeUVb60GXSVS7guWAQAoL0pUESihn", "http://sviluppo4.locl/oauth.php");

// Funzione per ottenere un nuovo token di accesso utilizzando il token di refresh
if (!function_exists('refreshAccessToken')) {
    function refreshAccessToken($oauth, $refreshToken)

    {
        $newToken = $oauth->refreshToken($refreshToken);
        $accessToken = $newToken->getAccessToken();
        saveAccessToken($accessToken, 'token');
        // Salva il nuovo token di accesso nella sessione
        // $_SESSION['token'] = $newToken->getAccessToken();
        // $_SESSION['refresh'] = $newToken->getRefreshToken();
        return $accessToken;
    }
}

$accessToken = getToken('token');
try {

    // Provo a vedere se il token è ancora valido
    $config = FattureInCloud\Configuration::getDefaultConfiguration()->setAccessToken($accessToken);

    $userApi = new FattureInCloud\Api\UserApi(
        new GuzzleHttp\Client(),
        $config
    );
    // Chiamata API per ottenere le informazioni sull'utente
    $userInfo = $userApi->getUserInfo();
} catch (\FattureInCloud\ApiException $e) {
    // Se la chiamata API restituisce un'eccezione, verifica se è dovuta a un token scaduto
    $response = json_decode($e->getResponseBody());

    if ($response && isset($response->error) && $response->error == "invalid_token") {
        echo 'Messaggio di errore: ' . $response->error_description . '<br>';
        // Se il token è scaduto, ottieni un nuovo token di accesso utilizzando il token di refresh
        $refreshToken = getToken('refreshtoken');
        echo 'refresh token: ' . $refreshToken . '<br>';
        $accessToken = refreshAccessToken($oauth, $refreshToken);
        echo 'refresh token';
    }
    // else {
    //     if (!isset($_GET['code'])) {
    //         $url = $oauth->getAuthorizationUrl([Scope::ENTITY_SUPPLIERS_READ, Scope::ENTITY_CLIENTS_READ, Scope::ISSUED_DOCUMENTS_INVOICES_READ, Scope::PRODUCTS_READ], "EXAMPLE_STATE");
    //         header('location: ' . $url);
    //         exit;
    //     } else {
    //         // Se è presente il codice di autorizzazione, ottieni il token
    //         $code = $_GET['code'];
    //         $obj = $oauth->fetchToken($code);
    //         if (!isset($obj->error)) {
    //             //Salvo l'accesso token nel database .
    //             $accessToken = $obj->getAccessToken();
    //             saveAccessToken($accessToken, 'token');
    //             $refreshToken = $obj->getRefreshToken();
    //             saveAccessToken($refreshToken, 'refreshtoken');
    //         } else {
    //             // Gestire eventuali errori durante il recupero del token
    //             die('Error fetching access token');
    //         }
    //     }
    // }
}


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
