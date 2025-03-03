<?php
include 'functions.php';
include 'include/configpdo.php';
include 'config-api2.php';
//array delle fatture
if (isset($_GET['a'])) {
    $anno = $_GET['a'];
} else {
    $anno = date('Y');
}

// Retrieve the first company id
// $companies = $userApi->listUserCompanies();
// // se il tipo è all allora prelevo tutte le fatture
// $firstCompanyId = $companies->getData()->getCompanies()[1]->getId();

//seleziono tutte le fatture non ancora inviate

try {
    $query = "SELECT `id_ffic` FROM `fatture` WHERE year(data_f)='$anno' AND `status_invio`='not_sent'";
    $stmt = $db->prepare($query);
    $stmt->execute();
    $dati   = $stmt->fetchAll();
    // conta le fatture
    $count = $stmt->rowCount();
    echo 'fatture da inviare ' . $count . '<br>';
    foreach ($dati as $row) {
        // echo 'fattura ' . $row['id_ffic'] . ' da inviare<br>';

        //prelevo i dati della fattura
        $dati_fattura = get_fattura($row['id_ffic']);
        $id = $dati_fattura['id']; //id fattura
        $cliente = $dati_fattura['id_cliente']; //id cliente
        $numero = $dati_fattura['numero']; //numero fattura
        $imp_netto = $dati_fattura['imp_netto']; //importo netto
        $imp_iva = $dati_fattura['iva']; //iva
        $imp_tot = $dati_fattura['imp_tot']; //importo totale
        $agente = $dati_fattura['note2']; //agente
        $parti = explode('-', $agente); //splitto l'agente
        $prima_parte = $parti[0]; //prendo la prima parte dell'agente
        $provv_percent = get_percentuale($prima_parte, $cliente); //percentuale agente
        $status_invio = $dati_fattura['status_invio']; //stato invio fattura
        $status = $dati_fattura['status']; //stato pagamento

        if (($status == '') || ($status == null)) {
            $status = 'not_paid';
        }
        $stato_invio = $dati_fattura['status_invio'];   //stato invio fattura not_sent or sent

        $data = $dati_fattura['data'];
        $data_scadenza = $dati_fattura['data_scadenza'];
        if (($data_scadenza == '') || ($data_scadenza == null)) {
            $data_scadenza = $data;
        }
        $data_pagamento = $dati_fattura['data_pagamento'];
        //se lo stato invio è sent aggiorno tutti i dati della fattura
        if ($stato_invio == 'sent') {
            //      echo 'fattura ' . $id . ' status ' . $stato_invio .  ' è stata inviata<br>';
            $sql = "UPDATE fatture SET 
            id_cfic = :cliente, 
            sigla = :sigla, 
            num_f = :numero, 
            imp_netto = :imp_netto, 
            imp_iva = :imp_iva, 
            imp_tot = :imp_tot, 
            status = :status, 
            status_invio = :statusinvio, 
            data_f = :data, 
            data_scadenza = :data_scadenza, 
            data_pagamento = :data_pagamento, 
            provv_percent = :provv_percent 
            WHERE id_ffic = :id";
            $stmt = $db->prepare($sql);
            $stmt->bindParam('id', $id, PDO::PARAM_INT);
            $stmt->bindParam('cliente', $cliente, PDO::PARAM_INT);
            $stmt->bindParam('sigla', $prima_parte, PDO::PARAM_STR);
            $stmt->bindParam('numero', $numero, PDO::PARAM_STR);
            $stmt->bindParam('imp_netto', $imp_netto, PDO::PARAM_STR);
            $stmt->bindParam('imp_iva', $imp_iva, PDO::PARAM_STR);
            $stmt->bindParam('imp_tot', $imp_tot, PDO::PARAM_STR);
            $stmt->bindParam('status', $status, PDO::PARAM_STR);
            $stmt->bindParam('statusinvio', $status_invio, PDO::PARAM_STR);
            $stmt->bindParam('data', $data, PDO::PARAM_STR);
            $stmt->bindParam('data_scadenza', $data_scadenza, PDO::PARAM_STR);
            $stmt->bindParam('data_pagamento', $data_pagamento, PDO::PARAM_STR);
            $stmt->bindParam('provv_percent', $provv_percent, PDO::PARAM_STR);
            // Esecuzione della query
            if ($stmt->execute()) {
                echo 'Aggiornamento della fattura ' . $id . ' eseguito con successo.<br>';
            } else {
                echo 'Errore durante l\'aggiornamento della fattura ' . $id . '.<br>';
            }

            //cancello i prodotti della fattura se presenti
            $sql = "DELETE FROM prodotti WHERE id_ffic = :id";
            $stmt = $db->prepare($sql);
            $stmt->bindParam('id', $id, PDO::PARAM_INT);
            $stmt->execute();

            if (!empty($dati_fattura['prodotti'])) {
                //se è stata inviata inserisco i prodotti
                foreach ($dati_fattura['prodotti'] as $prodotto) {
                    $id_prodotto = $prodotto['id_prodotto'];
                    $codice_prodotto = $prodotto['cod_prodotto'];
                    if (empty($id_prodotto)) {

                        //Non cè il codice prodotto perché o è stato cancellato o è stato aggiunto manualmente,Controllo quindi se nella lista prodotti è presente un prodotto con lo stesso nome
                        $sql = "SELECT * FROM lista_prodotti WHERE nome_prodotto = :nome";
                        $stmt = $db->prepare($sql);
                        $stmt->bindParam('nome', $prodotto['nome_prodotto'], PDO::PARAM_STR);
                        $stmt->execute();
                        $result = $stmt->fetch(PDO::FETCH_ASSOC);
                        if ($result) {
                            //se il prodotto è presente nella lista prodotti allora inserisco il codice prodotto nella fattura
                            $id_prodotto = $result['prod_id'];
                            $codice_prodotto = $result['cod_prod'];
                        } else {
                            //se il prodotto non è presente nella lista prodotti allora lo inserisco nella lista prodotti e inserisco il codice prodotto nella fattura

                            $varieta = determinaVarietaVino($prodotto['nome_prodotto']);
                            if ($varieta == 'Cabernet' || $varieta == 'Pinot Nero' || $varieta == 'Filorosso' || $varieta == 'Refosco') {
                                $tipo = 'rosso';
                            } else {
                                $tipo = 'bianco';
                            }
                            $codice_prodotto = '9' . PasswordCasuale(6, 'num');
                            $id_prodotto = '9' . PasswordCasuale(6, 'num');
                            $sql = "INSERT INTO lista_prodotti (cod_prod,prod_id, nome_prodotto , varieta,tipo) VALUES (:codprod,:idprod,:nome, :varieta, :tipo)";
                            $stmt = $db->prepare($sql);
                            $stmt->bindParam('codprod', $codice_prodotto, PDO::PARAM_STR);
                            $stmt->bindParam('nome', $prodotto['nome_prodotto'], PDO::PARAM_STR);
                            $stmt->bindParam('idprod', $id_prodotto, PDO::PARAM_INT);
                            $stmt->bindParam('varieta', $varieta, PDO::PARAM_STR);
                            $stmt->bindParam('tipo', $tipo, PDO::PARAM_STR);
                            $stmt->execute();
                        }
                    } else {

                        //devo vedere se il prodotto è presente nella tabella prodotti
                        $sql = "SELECT * FROM lista_prodotti WHERE prod_id = :id";
                        $stmt = $db->prepare($sql);
                        $stmt->bindParam('id', $id_prodotto, PDO::PARAM_INT);
                        $stmt->execute();
                        $result = $stmt->fetch(PDO::FETCH_ASSOC);
                        if ($result) {
                            // è presente nella tabella prodotti
                            $id_prodotto = $prodotto['id_prodotto'];
                            $codice_prodotto = $prodotto['cod_prodotto'];
                        } else {
                            $varieta = determinaVarietaVino($prodotto['nome_prodotto']);
                            if ($varieta == 'Cabernet' || $varieta == 'Pinot Nero' || $varieta == 'Filorosso' || $varieta == 'Refosco') {
                                $tipo = 'rosso';
                            } else {
                                $tipo = 'bianco';
                            }
                            $codice_prodotto = '9' . PasswordCasuale(6, 'num');
                            $id_prodotto = '9' . PasswordCasuale(6, 'num');
                            //non è presente nella tabella prodotti
                            $sql = "INSERT INTO lista_prodotti (cod_prod,prod_id, nome_prodotto , varieta,tipo) VALUES (:codprod,:idprod,:nome, :varieta, :tipo)";
                            $stmt = $db->prepare($sql);
                            $stmt->bindParam('codprod', $codice_prodotto, PDO::PARAM_STR);
                            $stmt->bindParam('nome', $prodotto['nome_prodotto'], PDO::PARAM_STR);
                            $stmt->bindParam('idprod', $id_prodotto, PDO::PARAM_INT);
                            $stmt->bindParam('varieta', $varieta, PDO::PARAM_STR);
                            $stmt->bindParam('tipo', $tipo, PDO::PARAM_STR);
                            $stmt->execute();
                        }
                        // $id_prodotto = $prodotto['id_prodotto'];
                        // $codice_prodotto = $prodotto['cod_prodotto'];
                        // echo $id_prodotto . '-' . $codice_prodotto . '<br>';
                    }

                    //se La quantità è Diverso da 1 Allora inserisco nella tabella prodotti 
                    if ($prodotto['quantita'] != '0') {
                        $anno_fattura = new DateTime($data);
                        $anno = $anno_fattura->format('Y');
                        $sql = "INSERT INTO prodotti (id_prod, id_ffic, qta, anno, data_f) VALUES (:codprod, :idffic, :quantita, :anno, :data_f)";
                        $stmt = $db->prepare($sql);
                        $stmt->bindParam('codprod', $id_prodotto, PDO::PARAM_INT);
                        $stmt->bindParam('idffic', $id, PDO::PARAM_INT);
                        $stmt->bindParam('quantita', $prodotto['quantita'], PDO::PARAM_INT);
                        $stmt->bindParam('anno', $anno, PDO::PARAM_INT);
                        $stmt->bindParam('data_f', $data, PDO::PARAM_STR);
                        $stmt->execute();
                        echo 'aggiunto prodotto<br>';
                    }
                }
            }
        } else {
            // echo 'fattura ' . $id . ' status ' . $stato_invio .  ' non inviata<br>';
            continue;
        }
    }
} catch (PDOException $e) {
    echo "Error : " . $e->getMessage();
}
