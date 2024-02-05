<?php
// funzione per generare una password casuale
function PasswordCasuale($lunghezza = 8, $tipo = 'all')
{
    //$caratteri_disponibili ="ABCDEFGHIJKLMNOPQRSTUVWXYZabcdefghijklmnopqrstuvwxyz1234567890";
    switch ($tipo) {
        case 'all':
            $caratteri_disponibili = "abcdefghijklmnpqrstuvwxyz123456789"; # code...
            break;

        case 'num':
            $caratteri_disponibili = "123456789"; # code...
            break;

        default:
            $caratteri_disponibili = "ABCDEFGHIJKLMNOPQRSTUVWXYZ"; # code...
            break;
    }

    $password = "";
    for ($i = 0; $i < $lunghezza; $i++) {
        $password = $password . substr($caratteri_disponibili, rand(0, strlen($caratteri_disponibili) - 1), 1);
    }
    return $password;
}
//Funzione per prelevare i prodotti da fatture in cloud .
function get_products($page = 1)
{
    include 'config-api2.php';
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
            $cod = $product->getCode(); //codice del prodotto
            $name = $product->getName(); //nome del prodotto

            $datiProdotto = array(
                'id' => $id,
                'cod' => $cod,
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
    include 'config-api2.php';
    //array dei nomi dei clienti
    $clienti = array();
    try {
        // Retrieve the first company id
        $companies = $userApi->listUserCompanies();

        $firstCompanyId = $companies->getData()->getCompanies()[1]->getId();
        $clients = $clientsAPI->listClients($firstCompanyId, null, 'detailed', null, $page, 50);
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
            $note = $client->getNotes(); //note extra del cliente

            $datiCliente = array(
                'id' => $id,
                'name' => $name,
                'citta' => $citta,
                'provincia' => $provincia,
                'paese' => $paese,
                'note' => $note
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

//Funziona per determinare id_zona partendo dalla codzona
function get_id_zona($codzona)
{
    include 'include/configpdo.php';
    $sql = "SELECT id_zona FROM zone_roma WHERE codzona = :codzona";
    $stmt = $db->prepare($sql);
    $stmt->bindParam('codzona', $codzona, PDO::PARAM_STR);
    $stmt->execute();
    $result = $stmt->fetch(PDO::FETCH_ASSOC);
    if ($result) {
        $id_zona = $result['id_zona'];
    } else {
        $id_zona = 0;
    }
    return $id_zona;
}

//funzione per prelevare le fatture da fatture in cloud
function get_fatture($page = 1, $data_inizio = '0')
{
    include 'config-api2.php';
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
            $oggetto = $issuedEInvoice->getNotes(); //note fattura non visibile
            //$oggetto = $issuedEInvoice->getVisibleSubject(); //oggetto fattura visibile RSC    

            //Per una determinata fattura possono essere stati fatti più pagamenti fino a quando tutto l'importo non è stato pagato la fattura si considera non pagata

            $paymentsList = $issuedEInvoice->getPaymentsList(); //lista pagamenti
            if (!empty($paymentsList)) {
                $status = 'not_paid';
                $status = $paymentsList[0]->getStatus(); //stato della fattura
                // Verificare se ci sono altre voci nell'elenco
                if (count($paymentsList) > 1) {
                    // Ci sono altre voci nell'elenco
                    // Puoi fare qualcosa con le voci aggiuntive se necessario
                    // Ad esempio, iterare attraverso l'elenco e ottenere le informazioni
                    foreach ($paymentsList as $payment) {
                        $status = $payment->getStatus();
                        // Fai qualcosa con lo stato del pagamento...
                    }
                }
            } else {
                $status = null;
            }

            $data = $issuedEInvoice->getDate(); //data della fattura
            //la data in formato aaaa-mm-gg
            $data = $data->format('Y-m-d');
            if ($issuedEInvoice->getPaymentsList()) {
                $data_scadenza = $issuedEInvoice->getPaymentsList()[0]->getDueDate(); //data di scadenza della fattura 
                $data_scadenza = $data_scadenza->format('Y-m-d');
            } else {
                $data_scadenza = null;
            }
            if ($issuedEInvoice->getPaymentsList()) {
                $pagamento = $issuedEInvoice->getPaymentsList()[0]->getPaidDate(); //data di pagamento della fattura
                if ($pagamento != null) {
                    $pagamento = $pagamento->format('Y-m-d');
                } else {
                    $pagamento = '';
                }
            } else {
                $pagamento = '';
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
                'data_scadenza' => $data_scadenza,
                'data_pagamento' => $pagamento
            );
            // Iterare attraverso la lista dei prodotti
            foreach ($prodotti_fattura as $prodotto) {
                $cod_prodotto = $prodotto->getProductId(); // codice del prodotto
                $nome_prodotto = $prodotto->getName(); // nome del prodotto
                $quantita = $prodotto->getQty(); // quantità del prodotto
                if ($quantita != '1') { //Escludo le quantità date in omaggio
                    // Aggiungere il prodotto e la quantità all'array
                    $lista_prodotti[] = array(
                        'cod_prodotto' => $cod_prodotto,
                        'nome_prodotto' => $nome_prodotto,
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
        //Per una determinata fattura possono essere stati fatti più pagamenti fino a quando tutto l'importo non è stato pagato la fattura si considera non pagata

        $paymentsList = $issuedEInvoice->getPaymentsList(); //lista pagamenti
        if (!empty($paymentsList)) {
            $status = 'not_paid';
            $status = $paymentsList[0]->getStatus(); //stato della fattura
            // Verificare se ci sono altre voci nell'elenco
            if (count($paymentsList) > 1) {
                // Ci sono altre voci nell'elenco
                // Puoi fare qualcosa con le voci aggiuntive se necessario
                // Ad esempio, iterare attraverso l'elenco e ottenere le informazioni
                foreach ($paymentsList as $payment) {
                    $status = $payment->getStatus();
                    // Fai qualcosa con lo stato del pagamento...
                }
            }
        } else {
            $status = null;
        }
        //    $status = $issuedEInvoice->getPaymentsList()[0]->getStatus(); //stato della fattura

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
    include 'config-api2.php';
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

//Funzione per determinare la percentuale di provvigione di default in base alla sigla dell'agente
function get_percentuale($sigla, $cod_cliente = '')
{
    $percentuale = 0;
    include 'include/configpdo.php';
    switch ($sigla) {
        case 'RSC': //Agenzia di Roma
            //Controllo la tabella agenti Roma inner join . Zone Roma . Per determinare la provvigione di default per quel cliente e quella zona .
            $sql = "SELECT * FROM agenti_roma INNER JOIN zone_roma ON agenti_roma.id_zona = zone_roma.id_zona WHERE agenti_roma.id_cfic = :cod_cliente";
            $stmt = $db->prepare($sql);
            $stmt->bindParam('cod_cliente', $cod_cliente, PDO::PARAM_INT);
            $stmt->execute();
            $result = $stmt->fetch(PDO::FETCH_ASSOC);
            if ($result) {
                $percentuale = $result['provv'];
            } else {
                $percentuale = 0;
            }
            break;

        default: //Altri agenti
            $sql = "SELECT * FROM agenti WHERE sigla = :sigla";
            $stmt = $db->prepare($sql);
            $stmt->bindParam('sigla', $sigla, PDO::PARAM_STR);
            $stmt->execute();
            $result = $stmt->fetch(PDO::FETCH_ASSOC);
            if ($result) {
                $percentuale = $result['provv'];
            } else {
                $percentuale = 0;
            }    # code...
            break;
    }
    return $percentuale;
}

//Funzione per determinare la data dell'ultima fattura emessa
function get_data_ultima_fattura()
{
    include 'include/configpdo.php';
    $sql = "SELECT MAX(data_f) AS ultima_data FROM fatture";
    $stmt = $db->prepare($sql);
    $stmt->execute();
    $result = $stmt->fetch(PDO::FETCH_ASSOC);
    if ($result) {
        $last_date = $result['ultima_data'];
    } else {
        $last_date = '0';
    }
    return $last_date;
}

function determinaVarietaVino($nome_stringa)
{
    $array_varieta = array(
        'Cabernet',
        'Chardonnay',
        'Filorosso',
        'Friulano',
        'Malvasia',
        'Pinot Grigio',
        'Pinot Nero',
        'Ribolla',
        'Sauvignon',
        'Castadiva'
    );
    $nome_stringa_minuscolo = strtolower($nome_stringa);

    foreach ($array_varieta as $varieta) {
        $varieta_minuscolo = strtolower($varieta);

        // Utilizza stripos per verificare la presenza della varietà nella stringa
        if (stripos($nome_stringa_minuscolo, $varieta_minuscolo) !== false) {
            return $varieta;
        }
    }

    // Se nessuna corrispondenza è stata trovata
    return "Varietà non trovata";
}

//Funzione che trova la lista di tutte le fatture non pagate
function fatture_non_pagate()
{
    include 'include/configpdo.php';
    $sql = "SELECT id_ffic FROM fatture WHERE status = 'not_paid'";
    $stmt = $db->prepare($sql);
    $stmt->execute();
    $result = $stmt->fetchAll(PDO::FETCH_ASSOC);
    return $result;
}

//Funzione che Controlla lo status della fattura in fatture in cloud
function check_status()
{
    include 'include/configpdo.php';
    include 'config-api2.php';
    //array delle fatture
    $fatture_non_pagate = fatture_non_pagate();
    // Retrieve the first company id
    $companies = $userApi->listUserCompanies();
    // se il tipo è all allora prelevo tutte le fatture
    $firstCompanyId = $companies->getData()->getCompanies()[1]->getId();
    $field = 'status,paid_date';
    foreach ($fatture_non_pagate as $fattura) {
        $document_id = $fattura['id_ffic']; //id fattura
        try {
            $issuedEInvoices = $issuedEInvoicesApi->getIssuedDocument($firstCompanyId, $document_id, $field, 'detailed');

            //Prelevo i dati della fattura
            $status = $issuedEInvoices->getData()->getPaymentsList()[0]->getStatus(); //stato della fattura
            $data_pag = $issuedEInvoices->getData()->getPaymentsList()[0]->getPaidDate(); //data di pagamento della fattura
            if ($status == 'paid') {
                $data_pag = $data_pag->format('Y-m-d');
                $sql = "UPDATE fatture SET status = :status, data_pagamento=:datapagamento WHERE id_ffic = :id";
                $stmt = $db->prepare($sql);
                $stmt->bindParam('id', $document_id, PDO::PARAM_INT);
                $stmt->bindParam('status', $status, PDO::PARAM_STR);
                $stmt->bindParam('datapagamento', $data_pag, PDO::PARAM_STR);
                $stmt->execute();
                echo 'fattura aggiornata ' . $document_id . '<br>';
            }
        } catch (Exception $e) {
            echo 'Exception when calling the API: ', $e->getMessage(), PHP_EOL;
        }
    }
}

function saveAccessToken($accessToken, $tipo)
{
    include 'include/configpdo.php';
    // Salva il token di accesso nel database nella tabella config

    try {
        $query = "UPDATE `config` SET valore_config=:acctoken WHERE `parametro_config`= :tipo";
        $stmt = $db->prepare($query);
        $stmt->bindParam('acctoken', $accessToken, PDO::PARAM_STR);
        $stmt->bindParam('tipo', $tipo, PDO::PARAM_STR);
        $stmt->execute();
    } catch (PDOException $e) {
        echo "Error : " . $e->getMessage();
    }
}

function getToken($tipo)
{
    // Implementa la logica per recuperare il refresh token dal tuo sistema
    // Ad esempio, puoi recuperarlo dal database o da un file sicuro
    // Assicurati di proteggere questo dato sensibile

    include 'include/configpdo.php';
    try {
        $query = "SELECT `valore_config` FROM `config` WHERE `parametro_config`=:tipo";
        $stmt = $db->prepare($query);
        $stmt->bindParam('tipo', $tipo, PDO::PARAM_STR);
        $stmt->execute();
        $row   = $stmt->fetch(PDO::FETCH_ASSOC);
        $token = $row['valore_config'];
    } catch (PDOException $e) {
        echo "Error : " . $e->getMessage();
    }
    return $token;
}
