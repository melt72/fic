<?php
include 'functions.php';
include 'include/configpdo.php';
include 'config-api2.php';
//array delle fatture

// Retrieve the first company id
$companies = $userApi->listUserCompanies();
// se il tipo è all allora prelevo tutte le fatture
$firstCompanyId = $companies->getData()->getCompanies()[1]->getId();
$field = 'status,paid_date';

$document_id = '356780199'; //id fattura
try {
    $issuedEInvoices = $issuedEInvoicesApi->getIssuedDocument($firstCompanyId, $document_id, '', 'detailed');
    print_r($issuedEInvoices);
    //Prelevo i dati della fattura
    $status = $issuedEInvoices->getData()->getPaymentsList()[0]->getStatus(); //stato della fattura
    $data_pag = $issuedEInvoices->getData()->getPaymentsList()[0]->getPaidDate(); //data di pagamento della fattura
    $paymentsList = $issuedEInvoices->getData()->getPaymentsList()[0]; //lista pagamenti
    $data_pag = $data_pag->format('Y-m-d');
    print_r($paymentsList);
    if ($status == 'paid') {
        echo 'fattura pagata ' . $document_id . '<br>';
        //     $data_pag = $data_pag->format('Y-m-d');
        $sql = "UPDATE fatture SET status = :status, data_pagamento=:datapagamento WHERE id_ffic = :id";
        $stmt = $db->prepare($sql);
        $stmt->bindParam('id', $document_id, PDO::PARAM_INT);
        $stmt->bindParam('status', $status, PDO::PARAM_STR);
        $stmt->bindParam('datapagamento', $data_pag, PDO::PARAM_STR);
        $stmt->execute();
        //     echo 'fattura aggiornata ' . $document_id . '<br>';
    }
    echo 'fattura ' . $document_id . ' status ' . $status . ' data pagamento ' . $data_pag . '<br>';
} catch (Exception $e) {
    echo 'Exception when calling the API: ', $e->getMessage(), PHP_EOL;
}
