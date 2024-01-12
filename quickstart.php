<?php

use FattureInCloud\Api\SuppliersApi;
use FattureInCloud\Api\ClientsApi;
use FattureInCloud\Api\IssuedDocumentsApi;
use FattureInCloud\Api\UserApi;
use FattureInCloud\Configuration;
use GuzzleHttp\Client;

require __DIR__ . '/admin/assets/vendor/autoload.php';
session_start();

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

try {
    // Retrieve the first company id
    $companies = $userApi->listUserCompanies();

    $firstCompanyId = $companies->getData()->getCompanies()[1]->getId();

    // Retrieve the list of first 10 Suppliers for the selected company
    // $suppliers = $suppliersApi->listSuppliers($firstCompanyId, null, null, null, 1, 20);

    // foreach ($suppliers->getData() as $supplier) {
    //     $name = $supplier->getName();

    //     $tipe = $supplier->getEmail();
    //     echo $name . ' ' . $tipe . ' </br>';
    // }
} catch (Exception $e) {
    echo 'Exception when calling the API: ', $e->getMessage(), PHP_EOL;
}
// $campi = "address_city";
// $q = "address_province = 'RM'";
// $clients = $clientsAPI->listClients($firstCompanyId, null, null, null, 1, 20, $q);
// print_r($clients);
// foreach ($clients->getData() as $client) {
//     $name = $client->getName();

//     $tipe = $client->getEmail();
//     echo $name . ' ' . $tipe . ' </br>';
// }
$campi = "amount_net,entity,amount_vat";
$q = "date >= '2023-10-21'";
// // id, tipo, campi  , detailed, , page, per_page, filtro
$issuedEInvoices = $issuedEInvoicesApi->listIssuedDocuments($firstCompanyId, 'invoice', null, 'detailed', null, 1, 5, $q);
//print_r($issuedEInvoices);
if ($issuedEInvoices->getData()) {
    //     // Accedi alla proprietà 'total' dell'array 'data'
    $totali = $issuedEInvoices['total']; //numero di fatture trovate

    //     // Ora puoi utilizzare la variabile $totali
    echo "Il valore di total è: " . $totali;
} else {
    echo "Nessun dato restituito o proprietà 'total' non presente.";
}
foreach ($issuedEInvoices->getData() as $issuedEInvoice) {

    $id = $issuedEInvoice->getId(); //id della fattura
    $id_cliente = $issuedEInvoice->getEntity()->getId();    //id cliente
    $numero = $issuedEInvoice->getNumber(); //numero della fattura
    $imp_netto = $issuedEInvoice->getAmountNet(); //importo netto
    $iva = $issuedEInvoice->getAmountVat(); //iva
    $imp_tot = $issuedEInvoice->getAmountGross(); //importo totale
    //    $note = $issuedEInvoice->getNotes(); //note fattura non visibile
    $oggetto = $issuedEInvoice->getVisibleSubject(); //oggetto fattura visibile RSC
    $status = $issuedEInvoice->getPaymentsList()[0]->getStatus(); //stato della fattura

    $data = $issuedEInvoice->getDate(); //data della fattura
    //la data in formato aaaa-mm-gg
    $data = $data->format('Y-m-d');
    $data_scadenza = $issuedEInvoice->getPaymentsList()[0]->getDueDate(); //data di scadenza della fattura

    //la data in formato aaaa-mm-gg
    $data_scadenza = $data_scadenza->format('Y-m-d');

    // Creare un array per la lista dei prodotti e quantità
    $lista_prodotti = array();
    $prodotti_fattura = $issuedEInvoice->getItemsList();
    $datiFattura = array(
        'id' => $id,
        'id_cliente' => $id_cliente,
        'numero' => $numero,
        'imp_netto' => $imp_netto,
        'iva' => $iva,
        'imp_tot' => $imp_tot,
        'note' => $oggetto,
        'status' => $status,
        'data' => $data,
        'data_scadenza' => $data_scadenza
    );
    // Iterare attraverso la lista dei prodotti
    foreach ($prodotti_fattura as $prodotto) {
        $nome_prodotto = $prodotto->getName(); // nome del prodotto
        $quantita = $prodotto->getQty(); // quantità del prodotto

        // Aggiungere il prodotto e la quantità all'array
        $lista_prodotti[] = array(
            'nome_prodotto' => $nome_prodotto,
            'quantita' => $quantita
        );
    }
    $datiFattura['prodotti'] = $lista_prodotti;

    // Aggiungi l'array datiFattura all'array fatture_totali
    $fatture_totali[] = $datiFattura;
    //$prodotto = $issuedEInvoice->getItemsList()[0]->getName(); //prodotto fattura

    //     $am = $issuedEInvoice->getAmountNet(); //importo fattura
    //     $status = $issuedEInvoice->getPaymentsList()[0]->getStatus(); //stato pagamento
    //     $data_pag = $issuedEInvoice->getPaymentsList()[0]->getPaidDate();   //data pagamento

    //     if (!empty($data_pag)) :
    //         $data_pag = $data_pag->format('Y-m-d');
    //         $data_pag = date("d-m-Y", strtotime($data_pag));
    //     else :
    //         $data_pag = 'Non pagata';
    //     endif;
    //     //trasformo datetime in stringa
    //     $data = $data->format('Y-m-d');
    //     //trasformo la data in formato italiano
    //     $data = date("d-m-Y", strtotime($data));

    // echo $imp . ' ' . $note . ' ' . $oggetto . '</br>';
}
echo 'fine';
print_r($fatture_totali);
