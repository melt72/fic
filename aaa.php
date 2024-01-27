<?php
require __DIR__ . '/admin/assets/vendor/autoload.php';
// The following dependencies are required
// composer require stechstudio/backoff
// composer require fattureincloud/fattureincloud-php-sdk

use FattureInCloud\Api\ProductsApi;
use FattureInCloud\Configuration;
use GuzzleHttp\Client;
use STS\Backoff\Backoff;
use FattureInCloud\OAuth2\OAuth2AuthorizationCodeManager;
use FattureInCloud\OAuth2\Scope;

// This code should be executed periodically using a cron library or job scheduler.
// For example: https://github.com/Cron/Cron

// Here we init the Fatture in Cloud SDK
// The Access Token is retrieved using the "getToken" method
$config = Configuration::getDefaultConfiguration()->setAccessToken(getToken());
// In this example we're using the Products API
$productsApiInstance = new ProductsApi(
    new Client(),
    $config
);

// In this example we suppose to export the data to a JSON Lines file.
// First, we cancel the content of the destination file
file_put_contents("./products.jsonl", "");

// This is the ID of the company we're currently managing
$companyId = 2;
// We require the first page using the ListProducts method
$result = listProductsWithBackoff($productsApiInstance, $companyId, 1);
// We extract the index of the last page from the first response
$lastPage = $result["last_page"];
// We append all the products to the destination file
// "data" contains an array of products 
appendProductsToFile($result["data"]);

// For all the missing pages (we already have the first one)
for ($i = 2; $i <= $lastPage; $i++) {
    // We require the page at the selected index to the API
    $result = listProductsWithBackoff($productsApiInstance, $companyId, $i);
    // We append this page products to the file
    appendProductsToFile($result["data"]);
}

// In this function we append the products in the JSON Lines file.
// You can replace this function to perform the operations you need.
// For example, you can build SQL queries or call a third-party API using the retrieved products.
function appendProductsToFile($products)
{
    // For each product in the array
    foreach ($products as $product) {
        // We encode it to a JSON string and append it to the file as a single line
        file_put_contents("products.jsonl", json_encode($product) . "\n", FILE_APPEND);
    }
}

// Here we wrap the SDK method with an exponential backoff
// This is to manage the quota exceeded issue
function listProductsWithBackoff($productsApiInstance, $companyId, $currentPage): Object
{
    $attempt = 0;
    $perPage = 50; // Every page will contain at most 50 products
    $backoff = new Backoff(20, 'exponential', 300000, true);
    return $backoff->run(function () use ($productsApiInstance, $companyId, $currentPage, $perPage, &$attempt) {
        $waitTime = 2 ** $attempt * 1000;
        echo sprintf("Page: %s Attempt: %s WaitTime(millis): %s\n", $currentPage, $attempt++, $waitTime);

        // The actual SDK method is executed here
        $result = $productsApiInstance->listProducts($companyId, null, "detailed", null, $currentPage, $perPage);

        return $result;
    });
}

function getToken(): string
{
    // Creare l'istanza di OAuth2AuthorizationCodeManager
    $oauth = new OAuth2AuthorizationCodeManager(
        "gYeBVqHZDNTB8LbuOc4XgLBN2POEoeB7",
        "8vWWSgLetSGpV7O9wpE4BAvaRorF3beWDNtJeUVb60GXSVS7guWAQAoL0pUESihn",
        "http://sviluppo4.locl/oauth.php"
    );

    // Ottenere l'URL di autorizzazione
    $authorizationUrl = $oauth->getAuthorizationUrl(
        [
            Scope::ISSUED_DOCUMENTS_INVOICES_READ,
            Scope::PRODUCTS_READ
        ],
        "YOUR_STATE"
    );
    echo $authorizationUrl . "\n";

    // Wait for the user to visit the authorization URL and grant access
    // You can handle this step using a redirect server or a JavaScript frontend

    // Once the user grants access, the authorization server will redirect back
    // to your redirect URI with an authorization code in the query string
    $code = $_GET['code'] ?? null;

    if ($code !== null) {
        // Retrieve the access token from the OAuth2AuthorizationCodeManager class
        $token = $oauth->fetchToken($code);
        echo $token . "\n";
        return $token->getAccessToken();
    } else {
        // Gestisci il caso in cui il parametro 'code' non è presente nell'URL
        echo "Il parametro 'code' non è presente nell'URL";
        // Puoi gestire questa situazione in base alle tue esigenze
        // Ad esempio, potresti reindirizzare l'utente o mostrare un messaggio di errore
        // Ritorno una stringa vuota in caso di errore
        return "";
    }
}
