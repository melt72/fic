<?php
require_once(__DIR__ . '/admin/assets/vendor/autoload.php');

use FattureInCloud\OAuth2\OAuth2AuthorizationCodeManager;
use FattureInCloud\OAuth2\Scope;

session_set_cookie_params(86400);
session_start();

$oauth = new OAuth2AuthorizationCodeManager("gYeBVqHZDNTB8LbuOc4XgLBN2POEoeB7", "8vWWSgLetSGpV7O9wpE4BAvaRorF3beWDNtJeUVb60GXSVS7guWAQAoL0pUESihn", "http://sviluppo4.locl/oauth.php");

// Funzione per ottenere un nuovo token di accesso utilizzando il token di refresh
function refreshAccessToken($oauth, $refreshToken)
{
    $newToken = $oauth->refreshToken($refreshToken);
    $_SESSION['token'] = $newToken->getAccessToken();
    $_SESSION['token_expires'] = $newToken->getExpires();
    $_SESSION['refresh'] = $newToken->getRefreshToken();
}

// Se il token di accesso è presente nella sessione e non è scaduto
if (isset($_SESSION['token'])) {
    $accessToken = $_SESSION['token'];
    // Utilizza $accessToken per effettuare chiamate API sicure
    // ...

} else {
    // Se il token di accesso è scaduto o non è presente
    if (isset($_SESSION['refresh'])) {
        // Utilizza il token di refresh per ottenere un nuovo token di accesso
        refreshAccessToken($oauth, $_SESSION['refresh']);
        $accessToken = $_SESSION['token'];
        // Utilizza $accessToken per effettuare chiamate API sicure
        // ...
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
                $_SESSION['refresh'] = $obj->getRefreshToken();
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
