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
    $data_ultima_fattura = get_data_ultima_notadicredito();
    $fine = 0;
}

//prelevo i clienti da fatture in cloud

$fatture = get_notedicredito('1', $data_ultima_fattura, $fine);
$prima_parte = '';
//controllo se la fattura è già presente nel database altrimenti la inserisco
foreach ($fatture as $fattura) {
    $id = $fattura['id'];
    $cliente = $fattura['id_cliente'];
    if (($cliente == '') || ($cliente == null) || ($cliente == 0)) {
        $cliente = 0;
    }
    $numero = $fattura['numero'];
    $imp_netto = $fattura['imp_netto'];
    $imp_iva = $fattura['iva'];
    $imp_tot = $fattura['imp_tot'];

    $data = $fattura['data'];


    //Controllo che la fattura non sia già stata inserita nel database
    $sql = "SELECT * FROM ndc WHERE id_ndc = :id";
    $stmt = $db->prepare($sql);
    $stmt->bindParam('id', $id, PDO::PARAM_INT);
    $stmt->execute();
    $result = $stmt->fetch(PDO::FETCH_ASSOC);
    if (!$result) {
        //se la fattura non è presente nel database la inserisco
        $sql = "INSERT INTO `ndc`(`id_ndc`, `id_cfic`, `num_ndc`, `imp_netto`, `imp_iva`, `imp_tot`, `data_ndc`) VALUES (:id, :cliente,:numero, :imp_netto, :imp_iva, :imp_tot,:data)";
        $stmt = $db->prepare($sql);
        $stmt->bindParam('id', $id, PDO::PARAM_INT);
        $stmt->bindParam('cliente', $cliente, PDO::PARAM_INT);
        $stmt->bindParam('numero', $numero, PDO::PARAM_STR);
        $stmt->bindParam('imp_netto', $imp_netto, PDO::PARAM_STR);
        $stmt->bindParam('imp_iva', $imp_iva, PDO::PARAM_STR);
        $stmt->bindParam('imp_tot', $imp_tot, PDO::PARAM_STR);
        $stmt->bindParam('data', $data, PDO::PARAM_STR);

        $stmt->execute();
        echo 'aggiunta ndc<br>';
        // Accedi ai prodotti
        foreach ($fattura['prodotti'] as $prodotto) {
            if (($prodotto['id'] == null) || ($prodotto['id'] == '')) {
                //Non cè il codice prodotto perché o è stato cancellato o è stato aggiunto manualmente,Controllo quindi se nella lista prodotti è presente un prodotto con lo stesso nome
                $sql = "SELECT * FROM lista_prodotti WHERE nome_prodotto = :nome";
                $stmt = $db->prepare($sql);
                $stmt->bindParam('nome', $prodotto['nome_prodotto'], PDO::PARAM_STR);
                $stmt->execute();
                $result = $stmt->fetch(PDO::FETCH_ASSOC);
                if ($result) {
                    //se il prodotto è presente nella lista prodotti allora inserisco il codice prodotto nella fattura
                    $prodotto['id'] = $result['prod_id'];
                } else {
                    //se il prodotto non è presente nella lista prodotti allora lo inserisco nella lista prodotti e inserisco il codice prodotto nella fattura

                    $varieta = determinaVarietaVino($prodotto['nome_prodotto']);
                    if ($varieta == 'Cabernet' || $varieta == 'Pinot Nero' || $varieta == 'Filorosso') {
                        $tipo = 'rosso';
                    } else {
                        $tipo = 'bianco';
                    }
                    $codice_prodotto = '900' . PasswordCasuale(6, 'num');
                    $sql = "INSERT INTO lista_prodotti (cod_prod,prod_id, nome_prodotto , varieta,tipo) VALUES (:codprod,:idprod,:nome, :varieta, :tipo)";
                    $stmt = $db->prepare($sql);
                    $stmt->bindParam('codprod', $prodotto['cod_prodotto'], PDO::PARAM_INT);
                    $stmt->bindParam('nome', $prodotto['nome_prodotto'], PDO::PARAM_STR);
                    $stmt->bindParam('idprod', $codice_prodotto, PDO::PARAM_INT);
                    $stmt->bindParam('varieta', $varieta, PDO::PARAM_STR);
                    $stmt->bindParam('tipo', $tipo, PDO::PARAM_STR);
                    $stmt->execute();
                    $prodotto['cod_prodotto'] = $codice_prodotto;
                }

                // $prodotto['cod_prodotto'] = 0;
            }
            //se La quantità è Diverso da 1 Allora inserisco nella tabella prodotti 
            if ($prodotto['quantita'] != '1') {
                $anno_fattura = new DateTime($data);
                $anno = $anno_fattura->format('Y');
                $sql = "INSERT INTO prodotti_ndc (id_prod, id_ffic, qta, anno, data_f) VALUES (:codprod, :idffic, :quantita, :anno, :data_f)";
                $stmt = $db->prepare($sql);
                $stmt->bindParam('codprod', $prodotto['cod_prodotto'], PDO::PARAM_INT);
                $stmt->bindParam('idffic', $id, PDO::PARAM_INT);
                $stmt->bindParam('quantita', $prodotto['quantita'], PDO::PARAM_INT);
                $stmt->bindParam('anno', $anno, PDO::PARAM_INT);
                $stmt->bindParam('data_f', $data, PDO::PARAM_STR);
                $stmt->execute();
                echo 'aggiunto prodotto<br>';
            }
        }
    }
}
