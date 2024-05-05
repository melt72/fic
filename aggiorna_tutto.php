
<?php
include 'functions.php';
include 'include/configpdo.php';
$counter = $_GET['counter'];

switch ($counter) {
    case '1': // Nuovi clienti
        $clienti = get_clients();

        //controllo se il cliente è già presente nel database altrimenti lo inserisco
        foreach ($clienti as $cliente) {
            $prima_parte = ''; //variabile che conterrà la prima parte della nota del cliente
            $id = $cliente['id'];
            $name = $cliente['name'];
            $citta = $cliente['citta'];
            $provincia = $cliente['provincia'];
            $paese = $cliente['paese'];
            $note = $cliente['note'];
            $agente = '';
            if ($note != '') {
                $parti = explode('-', $note);
                // Quanti sono le parti?
                $num_parti = count($parti);
                // La prima parte è l'agente
                $prima_parte = $parti[0]; // PRC-1
                if ($prima_parte == 'RSC') {
                    $zona = get_id_zona($parti[1]);
                }
            }
            $sql = "SELECT * FROM clienti WHERE id_cfic = :id";
            $stmt = $db->prepare($sql);
            $stmt->bindParam('id', $id, PDO::PARAM_INT);
            $stmt->execute();
            $result = $stmt->fetch(PDO::FETCH_ASSOC);
            if (!$result) {
                $sql = "INSERT INTO clienti (id_cfic, nome, citta, provincia, paese) VALUES (:id, :name, :citta, :provincia, :paese)";
                $stmt = $db->prepare($sql);
                $stmt->bindParam('id', $id, PDO::PARAM_INT);
                $stmt->bindParam('name', $name, PDO::PARAM_STR);
                $stmt->bindParam('citta', $citta, PDO::PARAM_STR);
                $stmt->bindParam('provincia', $provincia, PDO::PARAM_STR);
                $stmt->bindParam('paese', $paese, PDO::PARAM_STR);
                $stmt->execute();
                //Se il cliente è un agente RSC di Roma lo aggiungo anche alla tabella agenti_Roma 
                if (isset($prima_parte) && ($prima_parte == 'RSC')) {
                    $sql = "INSERT INTO agenti_roma (id_cfic, zona) VALUES (:id, :zona)";
                    $stmt = $db->prepare($sql);
                    $stmt->bindParam('id', $id, PDO::PARAM_INT);
                    $stmt->bindParam('zona', $zona, PDO::PARAM_STR);
                    $stmt->execute();
                }
                echo 'aggiunto cliente ' . $name . '<br>';
            } else {
                //Il cliente è già presente nel database, controllo se è un agente RSC di Roma
                if (isset($prima_parte) && ($prima_parte == 'RSC')) {
                    $sql = "SELECT * FROM agenti_Roma WHERE id_cfic = :id";
                    $stmt = $db->prepare($sql);
                    $stmt->bindParam('id', $id, PDO::PARAM_INT);
                    $stmt->execute();
                    $result = $stmt->fetch(PDO::FETCH_ASSOC);
                    if (!$result) {
                        $sql = "INSERT INTO agenti_roma (id_cfic, zona) VALUES (:id, :zona)";
                        $stmt = $db->prepare($sql);
                        $stmt->bindParam('id', $id, PDO::PARAM_INT);
                        $stmt->bindParam('zona', $zona, PDO::PARAM_STR);
                        $stmt->execute();
                        echo 'aggiunto agente alla zona di roma ' . $name . '<br>';
                    }
                }
                //echo 'il cliente è già presente nel database<br>';
            }
        }
        echo 'Clienti aggiornati<br>';
        break;
    case '2': // Nuovi prodotti
        $prodotti = get_products();
        //controllo se la fattura è già presente nel database altrimenti la inserisco

        foreach ($prodotti as $prodotto) {
            $id = $prodotto['id'];
            $nome = $prodotto['name'];
            $codprod = $prodotto['cod'];
            $varieta = determinaVarietaVino($nome);
            if ($varieta == 'Cabernet' || $varieta == 'Pinot Nero' || $varieta == 'Filorosso') {
                $tipo = 'rosso';
            } else {
                $tipo = 'bianco';
            }

            //Controllo se nella tabella lista prodotti cè già il prodotto in base al codice
            $sql = "SELECT * FROM lista_prodotti WHERE prod_id = :id";
            $stmt = $db->prepare($sql);
            $stmt->bindParam('id', $id, PDO::PARAM_INT);
            $stmt->execute();
            $result = $stmt->fetch(PDO::FETCH_ASSOC);
            if (!$result) {
                //se il prodotto non è presente nel database lo inserisco
                $sql = "INSERT INTO lista_prodotti (cod_prod,prod_id, nome_prodotto, varieta,tipo) VALUES (:codprod, :id, :nome, :varieta, :tipo)";
                $stmt = $db->prepare($sql);
                $stmt->bindParam('codprod', $codprod, PDO::PARAM_STR);
                $stmt->bindParam('id', $id, PDO::PARAM_INT);
                $stmt->bindParam('nome', $nome, PDO::PARAM_STR);
                $stmt->bindParam('varieta', $varieta, PDO::PARAM_STR);
                $stmt->bindParam('tipo', $tipo, PDO::PARAM_STR);
                $stmt->execute();
                echo 'aggiunto prodotto ' . $nome . '<br>';
            }
        }
        echo 'Prodotti aggiornati<br>';
        break;
    case '3': // Nuove fatture

        $data_ultima_fattura = get_data_ultima_fattura();


        //prelevo i clienti da fatture in cloud

        $fatture = get_fatture('1', $data_ultima_fattura);
        $prima_parte = '';
        //controllo se la fattura è già presente nel database altrimenti la inserisco
        foreach ($fatture as $fattura) {
            $id = $fattura['id'];
            $cliente = $fattura['id_cliente'];
            $numero = $fattura['numero'];
            $imp_netto = $fattura['imp_netto'];
            $imp_iva = $fattura['iva'];
            $imp_tot = $fattura['imp_tot'];
            $agente = $fattura['note'];
            $parti = explode('-', $agente);
            $prima_parte = $parti[0];
            $provv_percent = get_percentuale($prima_parte, $cliente);
            $status = $fattura['status'];
            if (($status == '') || ($status == null)) {
                $status = 'not_paid';
            }
            $data = $fattura['data'];
            $data_scadenza = $fattura['data_scadenza'];
            if (($data_scadenza == '') || ($data_scadenza == null)) {
                $data_scadenza = $data;
            }
            $data_pagamento = $fattura['data_pagamento'];

            //Controllo che la fattura non sia già stata inserita nel database
            $sql = "SELECT * FROM fatture WHERE id_ffic = :id";
            $stmt = $db->prepare($sql);
            $stmt->bindParam('id', $id, PDO::PARAM_INT);
            $stmt->execute();
            $result = $stmt->fetch(PDO::FETCH_ASSOC);
            if (!$result) {
                //se la fattura non è presente nel database la inserisco
                $sql = "INSERT INTO fatture (id_ffic, id_cfic,sigla, num_f, imp_netto, imp_iva, imp_tot, status, data_f, data_scadenza, data_pagamento,  provv_percent) VALUES (:id, :cliente, :sigla, :numero, :imp_netto, :imp_iva, :imp_tot, :status, :data, :data_scadenza, :data_pagamento, :provv_percent)";
                $stmt = $db->prepare($sql);
                $stmt->bindParam('id', $id, PDO::PARAM_INT);
                $stmt->bindParam('cliente', $cliente, PDO::PARAM_INT);
                $stmt->bindParam('sigla', $prima_parte, PDO::PARAM_STR);
                $stmt->bindParam('numero', $numero, PDO::PARAM_STR);
                $stmt->bindParam('imp_netto', $imp_netto, PDO::PARAM_STR);
                $stmt->bindParam('imp_iva', $imp_iva, PDO::PARAM_STR);
                $stmt->bindParam('imp_tot', $imp_tot, PDO::PARAM_STR);
                $stmt->bindParam('status', $status, PDO::PARAM_STR);
                $stmt->bindParam('data', $data, PDO::PARAM_STR);
                $stmt->bindParam('data_scadenza', $data_scadenza, PDO::PARAM_STR);
                $stmt->bindParam('data_pagamento', $data_pagamento, PDO::PARAM_STR);
                $stmt->bindParam('provv_percent', $provv_percent, PDO::PARAM_STR);
                $stmt->execute();
                echo 'Aggiunta fattura n. ' . $numero . '<br>';
                // Accedi ai prodotti
                foreach ($fattura['prodotti'] as $prodotto) {
                    if (($prodotto['id_prodotto'] == null) || ($prodotto['id_prodotto'] == '')) {
                        //Non cè il codice prodotto perché o è stato cancellato o è stato aggiunto manualmente,Controllo quindi se nella lista prodotti è presente un prodotto con lo stesso nome
                        $sql = "SELECT * FROM lista_prodotti WHERE nome_prodotto = :nome";
                        $stmt = $db->prepare($sql);
                        $stmt->bindParam('nome', $prodotto['nome'], PDO::PARAM_STR);
                        $stmt->execute();
                        $result = $stmt->fetch(PDO::FETCH_ASSOC);
                        if ($result) {
                            //se il prodotto è presente nella lista prodotti allora inserisco il codice prodotto nella fattura
                            $prodotto['id_prodotto'] = $result['prod_id'];
                        } else {
                            //se il prodotto non è presente nella lista prodotti allora lo inserisco nella lista prodotti e inserisco il codice prodotto nella fattura

                            $varieta = determinaVarietaVino($prodotto['nome_prodotto']);
                            if ($varieta == 'Cabernet' || $varieta == 'Pinot Nero' || $varieta == 'Filorosso') {
                                $tipo = 'rosso';
                            } else {
                                $tipo = 'bianco';
                            }
                            $codice_prodotto = '000' . PasswordCasuale(6, 'num');
                            $sql = "INSERT INTO lista_prodotti (cod_prod,prod_id, nome_prodotto , varieta,tipo) VALUES (:codprod,:idprod,:nome, :varieta, :tipo)";
                            $stmt = $db->prepare($sql);
                            $stmt->bindParam('codprod', $prodotto['cod_prodotto'], PDO::PARAM_INT);
                            $stmt->bindParam('nome', $prodotto['nome_prodotto'], PDO::PARAM_STR);
                            $stmt->bindParam('idprod', $codice_prodotto, PDO::PARAM_INT);
                            $stmt->bindParam('varieta', $varieta, PDO::PARAM_STR);
                            $stmt->bindParam('tipo', $tipo, PDO::PARAM_STR);
                            $stmt->execute();
                            $prodotto['id_prodotto'] = $codice_prodotto;
                        }

                        // $prodotto['cod_prodotto'] = 0;
                    }
                    //se La quantità è Diverso da 1 Allora inserisco nella tabella prodotti 
                    if ($prodotto['quantita'] != '1') {
                        $anno_fattura = new DateTime($data);
                        $anno = $anno_fattura->format('Y');
                        $sql = "INSERT INTO prodotti (id_prod, id_ffic, qta, anno, data_f) VALUES (:codprod, :idffic, :quantita, :anno, :data_f)";
                        $stmt = $db->prepare($sql);
                        $stmt->bindParam('codprod', $prodotto['cod_prodotto'], PDO::PARAM_INT);
                        $stmt->bindParam('idffic', $id, PDO::PARAM_INT);
                        $stmt->bindParam('quantita', $prodotto['quantita'], PDO::PARAM_INT);
                        $stmt->bindParam('anno', $anno, PDO::PARAM_INT);
                        $stmt->bindParam('data_f', $data, PDO::PARAM_STR);
                        $stmt->execute();
                        //echo 'aggiunto prodotto<br>';
                    }
                }
            } else {
                //se la fattura è già presente nel database controllo se è stata è stata pagata
                if ($result['status'] != 'paid') {
                    //se la fattura è già presente nel database ma non è stata pagata aggiorno lo status
                    $sql = "UPDATE fatture SET status = :status WHERE id_ffic = :id";
                    $stmt = $db->prepare($sql);
                    $stmt->bindParam('id', $id, PDO::PARAM_INT);
                    $stmt->bindParam('status', $status, PDO::PARAM_STR);
                    $stmt->execute();
                    //echo 'fattura aggiornata ' . $id . '<br>';
                }
            }
        }
        echo 'Fatture aggiornate<br>';
        # code...
        break;
    case '4': // Controllo dello status delle fatture vecchie
        $status = check_status();
        echo $status;
        break;

    default:
        # code...
        break;
}
