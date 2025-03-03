<?php
include 'functions.php';
include 'include/configpdo.php';
if (isset($_GET['d'])) {
    $data_ultima_fattura = $_GET['d'];
    if (isset($_GET['f'])) {
        $fine = $_GET['f'];
    } else {
        $fine = 0;
    }
} else {
    $data_ultima_fattura = get_data_ultima_fattura();
    $fine = 0;
}

//prelevo i clienti da fatture in cloud

$fatture = get_fatture('1', $data_ultima_fattura, $fine);
print_r($fatture);
$prima_parte = '';
//controllo se la fattura è già presente nel database altrimenti la inserisco
foreach ($fatture as $fattura) {
    $id = $fattura['id'];
    $cliente = $fattura['id_cliente'];
    if (($cliente == '') || ($cliente == null) || ($cliente == 0)) {
        $cliente = 0;
    }
    $numero = $fattura['numero'];
    // $imp_netto = $fattura['imp_netto'];
    // $imp_iva = $fattura['iva'];
    // $imp_tot = $fattura['imp_tot'];
    // $agente = $fattura['note'];
    // $parti = explode('-', $agente);
    // $prima_parte = $parti[0];
    // $provv_percent = get_percentuale($prima_parte, $cliente);
    // $status_invio = $fattura['status_invio'];
    // $status = $fattura['status'];

    // if (($status == '') || ($status == null)) {
    //     $status = 'not_paid';
    // }
    $data = $fattura['data'];
    // $data_scadenza = $fattura['data_scadenza'];
    // if (($data_scadenza == '') || ($data_scadenza == null)) {
    //     $data_scadenza = $data;
    // }
    // $data_pagamento = $fattura['data_pagamento'];

    //Controllo che la fattura non sia già stata inserita nel database
    // $sql = "SELECT * FROM fatture WHERE id_ffic = :id";
    // $stmt = $db->prepare($sql);
    // $stmt->bindParam('id', $id, PDO::PARAM_INT);
    // $stmt->execute();
    // $result = $stmt->fetch(PDO::FETCH_ASSOC);
    // if (!$result) {
    //se la fattura non è presente nel database la inserisco
    // $sql = "INSERT INTO fatture (id_ffic, id_cfic,sigla, num_f, imp_netto, imp_iva, imp_tot, status,status_invio, data_f, data_scadenza, data_pagamento,  provv_percent) VALUES (:id, :cliente, :sigla, :numero, :imp_netto, :imp_iva, :imp_tot, :status,:statusinvio, :data, :data_scadenza, :data_pagamento, :provv_percent)";
    // $stmt = $db->prepare($sql);
    // $stmt->bindParam('id', $id, PDO::PARAM_INT);
    // $stmt->bindParam('cliente', $cliente, PDO::PARAM_INT);
    // $stmt->bindParam('sigla', $prima_parte, PDO::PARAM_STR);
    // $stmt->bindParam('numero', $numero, PDO::PARAM_STR);
    // $stmt->bindParam('imp_netto', $imp_netto, PDO::PARAM_STR);
    // $stmt->bindParam('imp_iva', $imp_iva, PDO::PARAM_STR);
    // $stmt->bindParam('imp_tot', $imp_tot, PDO::PARAM_STR);
    // $stmt->bindParam('status', $status, PDO::PARAM_STR);
    // $stmt->bindParam('statusinvio', $status_invio, PDO::PARAM_STR);
    // $stmt->bindParam('data', $data, PDO::PARAM_STR);
    // $stmt->bindParam('data_scadenza', $data_scadenza, PDO::PARAM_STR);
    // $stmt->bindParam('data_pagamento', $data_pagamento, PDO::PARAM_STR);
    // $stmt->bindParam('provv_percent', $provv_percent, PDO::PARAM_STR);
    // $stmt->execute();
    // echo 'aggiunta fattura<br>';
    // Accedi ai prodotti
    foreach ($fattura['prodotti'] as $prodotto) {
        $id_prodotto = $prodotto['id_prodotto'];
        $codice_prodotto = $prodotto['cod_prodotto'];
        echo 'id del prodotto ' . $id_prodotto . '<br>';
        if (empty($id_prodotto)) {
            echo 'prodotto senza id ' . $prodotto['id_prodotto'] . ' ' . $prodotto['nome_prodotto'] . '<br>';
            //Non cè il codice prodotto perché o è stato cancellato o è stato aggiunto manualmente,Controllo quindi se nella lista prodotti è presente un prodotto con lo stesso nome
            $sql = "SELECT * FROM lista_prodotti WHERE nome_prodotto = :nome";
            $stmt = $db->prepare($sql);
            $stmt->bindParam('nome', $prodotto['nome_prodotto'], PDO::PARAM_STR);
            $stmt->execute();
            $result = $stmt->fetch(PDO::FETCH_ASSOC);
            print_r($result);
            if ($result) {
                //se il prodotto è presente nella lista prodotti allora inserisco il codice prodotto nella fattura
                echo 'prodotto presente nella lista prodotti ' . $prodotto['nome_prodotto'] . '<br>';
                $id_prodotto = $result['prod_id'];
                $codice_prodotto = $result['cod_prod'];
            } else {
                //se il prodotto non è presente nella lista prodotti allora lo inserisco nella lista prodotti e inserisco il codice prodotto nella fattura

                $varieta = determinaVarietaVino($prodotto['nome_prodotto']);
                if ($varieta == 'Cabernet' || $varieta == 'Pinot Nero' || $varieta == 'Filorosso') {
                    $tipo = 'rosso';
                } else {
                    $tipo = 'bianco';
                }
                if (empty($prodotto['cod_prodotto'])) {
                    $codice_prodotto = 'c' . PasswordCasuale(6, 'num');
                } else {
                    $codice_prodotto = $prodotto['cod_prodotto'];
                }
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

            // $prodotto['cod_prodotto'] = 0;
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
                if ($varieta == 'Cabernet' || $varieta == 'Pinot Nero' || $varieta == 'Filorosso') {
                    $tipo = 'rosso';
                } else {
                    $tipo = 'bianco';
                }
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



            $id_prodotto = $prodotto['id_prodotto'];
            $codice_prodotto = $prodotto['cod_prodotto'];
            // echo $id_prodotto . '-' . $codice_prodotto . '<br>';
        }
        //se La quantità è Diverso da 1 Allora inserisco nella tabella prodotti 
        if ($prodotto['quantita'] != '1') {
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
            // echo 'aggiunto prodotto ' . $id_prodotto . '<br>';
        }
    }
}
    // else {
    //     //se la fattura è già presente nel database controllo se è stata è stata pagata
    //     if ($result['status'] != 'paid') {
    //         //se la fattura è già presente nel database ma non è stata pagata aggiorno lo status
    //         $sql = "UPDATE fatture SET status = :status WHERE id_ffic = :id";
    //         $stmt = $db->prepare($sql);
    //         $stmt->bindParam('id', $id, PDO::PARAM_INT);
    //         $stmt->bindParam('status', $status, PDO::PARAM_STR);
    //         $stmt->execute();
    //         echo 'fattura aggiornata ' . $id . '<br>';
    //     }
    // }
// }
