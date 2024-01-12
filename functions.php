<?php

//Funzione per prelevare i prodotti da fatture in cloud .
function get_products($page = 1)
{
    include 'config-api.php';
    //array dei prodotti
    $prodotti = array();
    try {
        // Retrieve the first company id
        $companies = $userApi->listUserCompanies();

        $firstCompanyId = $companies->getData()->getCompanies()[1]->getId();
        $products = $productsApi->listProducts($firstCompanyId, null, null, null, $page, 50);
        //se ci sono prodotti leggo il numero di pagine
        if ($products->getData()) {
            $pagine = $products['last_page']; //numero di prodotti trovati
            array_push($prodotti, $pagine);
        }

        //per ogni prodotto prelevo i dati
        foreach ($products->getData() as $product) {

            $id = $product->getId(); //id del prodotto
            $name = $product->getName(); //nome del prodotto

            $datiProdotto = array(
                'id' => $id,
                'name' => $name
            );
            // Aggiungi l'array datiProdotto all'array prodotti_totali
            $prodotti_totali[] = $datiProdotto;
        }
        //se il numero di pagina è minore del numero di pagine totali
        if ($page < $pagine) {
            //richiamo la funzione ricorsivamente
            $page++;
            $prodotti_totali = array_merge($prodotti_totali, get_products($page));
        }
        return $prodotti_totali;
    } catch (Exception $e) {
        echo 'Exception when calling the API: ', $e->getMessage(), PHP_EOL;
    }
}

//funzione per prelevare i clienti da fatture in cloud
function get_clients($page = 1)
{
    include 'config-api.php';
    //array dei nomi dei clienti
    $clienti = array();
    try {
        // Retrieve the first company id
        $companies = $userApi->listUserCompanies();

        $firstCompanyId = $companies->getData()->getCompanies()[1]->getId();
        $clients = $clientsAPI->listClients($firstCompanyId, null, null, null, $page, 50);
        //se ci sono clienti leggo il numero di pagine
        if ($clients->getData()) {
            $pagine = $clients['last_page']; //numero di fatture trovate
            array_push($clienti, $pagine);
        }

        //per ogni cliente prelevo i dati
        foreach ($clients->getData() as $client) {

            $id = $client->getId(); //id del cliente
            $name = $client->getName(); //nome del cliente
            $citta = $client->getAddressCity(); //città del cliente
            $provincia = $client->getAddressProvince(); //provincia del cliente
            $paese = $client->getCountry(); //paese del cliente
            $datiCliente = array(
                'id' => $id,
                'name' => $name,
                'citta' => $citta,
                'provincia' => $provincia,
                'paese' => $paese
            );
            // Aggiungi l'array datiCliente all'array clienti_totali
            $clienti_totali[] = $datiCliente;
        }
        //se il numero di pagina è minore del numero di pagine totali
        if ($page < $pagine) {
            //richiamo la funzione ricorsivamente
            $page++;
            $clienti_totali = array_merge($clienti_totali, get_clients($page));
        }
        return $clienti_totali;
    } catch (Exception $e) {
        echo 'Exception when calling the API: ', $e->getMessage(), PHP_EOL;
    }
}

//funzione per prelevare le fatture da fatture in cloud
function get_fatture($page = 1, $data_inizio = '0')
{
    include 'config-api.php';
    //array delle fatture
    $fatture = array();
    try {
        // Retrieve the first company id
        $companies = $userApi->listUserCompanies();

        // se il tipo è all allora prelevo tutte le fatture

        $firstCompanyId = $companies->getData()->getCompanies()[1]->getId();
        //Se date l'inizio non è nulla allora prelevo le fatture in base alla data
        if ($data_inizio != '0') {
            // $q = "date >= " . $data_inizio;
            $q = "date >= '$data_inizio'";
            //id, tipo, campi  , detailed, , page, per_page, filtro$firstCompanyId, 'invoice', null, 'detailed', null, 1, 5, $q
            $issuedEInvoices = $issuedEInvoicesApi->listIssuedDocuments($firstCompanyId, 'invoice', null, 'detailed', null, $page, 50, $q);
        } else {
            //altrimenti prelevo tutte le fatture
            $issuedEInvoices = $issuedEInvoicesApi->listIssuedDocuments($firstCompanyId, 'invoice', null, 'detailed', null, $page, 50);
        }

        // Se ci sono fatture, leggi il numero di pagine
        if (!empty($issuedEInvoices->getData())) {
            $pagine = $issuedEInvoices['last_page']; // Numero di fatture trovate
            array_push($fatture, $pagine);
        }


        //per ogni fattura prelevo i dati
        foreach ($issuedEInvoices->getData() as $issuedEInvoice) {

            $id = $issuedEInvoice->getId(); //id della fattura
            $id_cliente = $issuedEInvoice->getEntity()->getId();    //id cliente
            $numero = $issuedEInvoice->getNumber(); //numero della fattura
            $imp_netto = $issuedEInvoice->getAmountNet(); //importo netto
            $iva = $issuedEInvoice->getAmountVat(); //iva
            $imp_tot = $issuedEInvoice->getAmountGross(); //importo totale
            //    $note = $issuedEInvoice->getNotes(); //note fattura non visibile
            $oggetto = $issuedEInvoice->getVisibleSubject(); //oggetto fattura visibile RSC
            if ($issuedEInvoice->getPaymentsList()) {
                $status = $issuedEInvoice->getPaymentsList()[0]->getStatus(); //stato della fattura
            } else {
                $status = null;
            }
            // $status = $issuedEInvoice->getPaymentsList()[0]->getStatus(); //stato della fattura

            $data = $issuedEInvoice->getDate(); //data della fattura
            //la data in formato aaaa-mm-gg
            $data = $data->format('Y-m-d');
            if ($issuedEInvoice->getPaymentsList()) {
                $data_scadenza = $issuedEInvoice->getPaymentsList()[0]->getDueDate(); //data di scadenza della fattura 
                $data_scadenza = $data_scadenza->format('Y-m-d');
            } else {
                $data_scadenza = null;
            }


            //la data in formato aaaa-mm-gg


            $prodotti_fattura = $issuedEInvoice->getItemsList();

            // Creare un array per la lista dei prodotti e quantità
            $lista_prodotti = array();
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
                $cod_prodotto = $prodotto->getProductId(); // codice del prodotto
                $quantita = $prodotto->getQty(); // quantità del prodotto
                if ($quantita != '1') { //Escludo le quantità date in omaggio
                    // Aggiungere il prodotto e la quantità all'array
                    $lista_prodotti[] = array(
                        'cod_prodotto' => $cod_prodotto,
                        'quantita' => $quantita
                    );
                }
            }


            $datiFattura['prodotti'] = $lista_prodotti;



            // Aggiungi l'array datiFattura all'array fatture_totali
            $fatture_totali[] = $datiFattura;
        }
        //se il 
        if ($page < $pagine) {
            $page++;
            $fatture_totali = array_merge($fatture_totali, get_fatture($page, $data_inizio));
        }

        return $fatture_totali;
    } catch (Exception $e) {
        echo 'Exception when calling the API: ', $e->getMessage(), PHP_EOL;
    }
}

//funzione per prelevare la fattura singola da fatture in cloud
function get_fattura($id_doc)
{
    include 'config-api.php';
    //array delle fatture
    $fatture = array();
    try {
        // Retrieve the first company id
        $companies = $userApi->listUserCompanies();

        // se il tipo è all allora prelevo tutte le fatture

        $firstCompanyId = $companies->getData()->getCompanies()[1]->getId();
        $issuedEInvoices = $issuedEInvoicesApi->getIssuedDocument($firstCompanyId, $id_doc);
        $issuedEInvoice = $issuedEInvoices->getData();
        //Prelevo i dati della fattura
        $id = $issuedEInvoice->getId(); //id della fattura
        $id_cliente = $issuedEInvoice->getEntity()->getId();    //id cliente
        $numero = $issuedEInvoice->getNumber(); //numero della fattura
        $imp_netto = $issuedEInvoice->getAmountNet(); //importo netto
        $note = $issuedEInvoice->getNotes(); //note fattura non visibile
        $oggetto = $issuedEInvoice->getVisibleSubject(); //oggetto fattura visibile RSC
        $iva = $issuedEInvoice->getAmountVat(); //iva
        $imp_tot = $issuedEInvoice->getAmountGross(); //importo totale
        $status = $issuedEInvoice->getPaymentsList()[0]->getStatus(); //stato della fattura

        $data = $issuedEInvoice->getDate(); //data della fattura
        //la data in formato aaaa-mm-gg
        $data = $data->format('Y-m-d');
        $data_scadenza = $issuedEInvoice->getPaymentsList()[0]->getDueDate(); //data di scadenza della fattura

        //la data in formato aaaa-mm-gg
        $data_scadenza = $data_scadenza->format('Y-m-d');
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
        //Ritorno array con i dati della fattura
        return $datiFattura;
    } catch (Exception $e) {
        echo 'Exception when calling the API: ', $e->getMessage(), PHP_EOL;
    }
}

//Funzione per prelevare lo status della fattura
function get_status($id_doc)
{
    include 'config-api.php';
    //array delle fatture
    $fatture = array();
    try {
        // Retrieve the first company id
        $companies = $userApi->listUserCompanies();

        // se il tipo è all allora prelevo tutte le fatture

        $firstCompanyId = $companies->getData()->getCompanies()[1]->getId();
        $issuedEInvoices = $issuedEInvoicesApi->getIssuedDocument($firstCompanyId, $id_doc);
        $issuedEInvoice = $issuedEInvoices->getData();
        //Prelevo i dati della fattura
        $status = $issuedEInvoice->getPaymentsList()[0]->getStatus(); //stato della fattura
        return $status;
    } catch (Exception $e) {
        echo 'Exception when calling the API: ', $e->getMessage(), PHP_EOL;
    }
}
