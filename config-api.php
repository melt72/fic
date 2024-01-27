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
//unset($_SESSION['token']);
//unset($_SESSION['refresh']);

$oauth = new OAuth2AuthorizationCodeManager("gYeBVqHZDNTB8LbuOc4XgLBN2POEoeB7", "8vWWSgLetSGpV7O9wpE4BAvaRorF3beWDNtJeUVb60GXSVS7guWAQAoL0pUESihn", "http://sviluppo4.locl/oauth.php");
if (!function_exists('refreshAccessToken')) {
    // Funzione per ottenere un nuovo token di accesso utilizzando il token di refresh
    function refreshAccessToken($oauth, $refreshToken)
    {
        $newToken = $oauth->refreshToken($refreshToken);
        $_SESSION['token'] = $newToken->getAccessToken();
        $_SESSION['refresh'] = $newToken->getRefreshToken();
    }
}



// Se il token di accesso è presente nella sessione e non è scaduto
if (isset($_SESSION['token'])) {
    echo "token";
    $accessToken = $_SESSION['token'];
    try {
        $config = FattureInCloud\Configuration::getDefaultConfiguration()->setAccessToken($accessToken);

        $userApi = new FattureInCloud\Api\UserApi(
            new GuzzleHttp\Client(),
            $config
        );
        // Chiamata API per ottenere le informazioni sull'utente
        $userInfo = $userApi->getUserInfo();

        // Se la chiamata API ha avuto successo, il token è ancora valido
        echo "Il token è ancora valido";
    } catch (\FattureInCloud\ApiException $e) {
        // Se la chiamata API restituisce un'eccezione, verifica se è dovuta a un token scaduto
        $response = json_decode($e->getResponseBody());
        if ($response && $response->code == 401 && $response->error == "invalid_token") {
            // Il token è scaduto, esegui la procedura di refreshAccessToken
            refreshAccessToken($oauth, $_SESSION['refresh']);
            $accessToken = $_SESSION['token'];
            echo "Il token è stato refreshato";
        } else {
            // Gestisci altri tipi di errori
            echo "Errore nella chiamata API: " . $e->getMessage();
        }
    }
} else {
    // Se il token di accesso è scaduto o non è presente
    if (isset($_SESSION['refresh'])) {

        // Utilizza il token di refresh per ottenere un nuovo token di accesso
        refreshAccessToken($oauth, $_SESSION['refresh']);
        $accessToken = $_SESSION['token'];
    } else {
        // Se il token di refresh non è presente, reindirizza per ottenere un nuovo token
        if (!isset($_GET['code'])) {
            $url = $oauth->getAuthorizationUrl([Scope::ENTITY_SUPPLIERS_READ, Scope::ENTITY_CLIENTS_READ, Scope::ISSUED_DOCUMENTS_INVOICES_READ, Scope::PRODUCTS_READ], "EXAMPLE_STATE");
            header('location: ' . $url);
            exit;
        } else {
            // Se è presente il codice di autorizzazione, ottieni il token
            $code = $_GET['code'];
            $obj = $oauth->fetchToken($code);
            if (!isset($obj->error)) {
                $_SESSION['token'] = $obj->getAccessToken();
                saveAccessToken($obj->getAccessToken(), 'token');
                $_SESSION['refresh'] = $obj->getRefreshToken();
                saveAccessToken($obj->getRefreshToken(), 'refreshtoken');
                $accessToken = $_SESSION['token'];
                // Utilizza $accessToken per effettuare chiamate API sicure
                // ...
            } else {
                // Gestire eventuali errori durante il recupero del token
                die('Error fetching access token');
            }
        }
    }
}
// Retrieve the access token from the session variable
// if (!isset($_SESSION['token'])) {
//     $accessToken = $_SESSION['refresh'];
// }
// $accessToken = $_SESSION['token'];


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
