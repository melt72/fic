<?php
include 'functions.php';
include 'include/configpdo.php';
//prelevo i clienti da fatture in cloud

$fatture = get_fatture('1', '2023-11-01');
print_r($fatture);
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
    $status = $fattura['status'];
    $data = $fattura['data'];
    $data_scadenza = $fattura['data_scadenza'];

    //Controllo che la fattura non sia già stata inserita nel database
    $sql = "SELECT * FROM fatture WHERE id_ffic = :id";
    $stmt = $db->prepare($sql);
    $stmt->bindParam('id', $id, PDO::PARAM_INT);
    $stmt->execute();
    $result = $stmt->fetch(PDO::FETCH_ASSOC);
    if (!$result) {
        //se la fattura non è presente nel database la inserisco
        $sql = "INSERT INTO fatture (id_ffic, id_cfic,sigla, num_f, imp_netto, imp_iva, imp_tot, status, data_f, data_scadenza) VALUES (:id, :cliente, :sigla, :numero, :imp_netto, :imp_iva, :imp_tot, :status, :data, :data_scadenza)";
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
        $stmt->execute();

        // Accedi ai prodotti
        foreach ($fattura['prodotti'] as $prodotto) {
            if ($prodotto['cod_prodotto'] == null) {
                $prodotto['cod_prodotto'] = 0;
            }
            //se La quantità è Diverso da 1 Allora inserisco nella tabella prodotti 
            if ($prodotto['quantita'] != '1') {
                $sql = "INSERT INTO prodotti (id_prod, id_ffic, qta) VALUES (:codprod, :idffic, :quantita)";
                $stmt = $db->prepare($sql);
                $stmt->bindParam('codprod', $prodotto['cod_prodotto'], PDO::PARAM_INT);
                $stmt->bindParam('idffic', $id, PDO::PARAM_INT);
                $stmt->bindParam('quantita', $prodotto['quantita'], PDO::PARAM_INT);
                $stmt->execute();
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
        }
    }
}
