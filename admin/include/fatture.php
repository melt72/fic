<?php
include 'functions.php';
require __DIR__ . '/../include/configpdo.php';
//prelevo i clienti da fatture in cloud

$fatture = get_fatture();
//controllo se la fattura è già presente nel database altrimenti la inserisco
foreach ($fatture as $fattura) {
    $id = $fattura['id'];
    $cliente = $fattura['id_cliente'];
    $numero = $fattura['numero'];
    $imp_netto = $fattura['imp_netto'];
    $imp_iva = $fattura['iva'];
    $imp_tot = $fattura['imp_tot'];
    $status = $fattura['status'];
    $data = $fattura['data'];
    $data_scadenza = $fattura['data_scadenza'];
    $sql = "SELECT * FROM fatture WHERE id_ffic = :id";
    $stmt = $db->prepare($sql);
    $stmt->bindParam('id', $id, PDO::PARAM_INT);
    $stmt->execute();
    $result = $stmt->fetch(PDO::FETCH_ASSOC);
    if (!$result) {
        //se la fattura non è presente nel database la inserisco
        $sql = "INSERT INTO fatture (id_ffic, id_cfic, num_f, imp_netto, imp_iva, imp_tot, status, data_f, data_scadenza) VALUES (:id, :cliente, :numero, :imp_netto, :imp_iva, :imp_tot, :status, :data, :data_scadenza)";
        $stmt = $db->prepare($sql);
        $stmt->bindParam('id', $id, PDO::PARAM_INT);
        $stmt->bindParam('cliente', $cliente, PDO::PARAM_INT);
        $stmt->bindParam('numero', $numero, PDO::PARAM_STR);
        $stmt->bindParam('imp_netto', $imp_netto, PDO::PARAM_STR);
        $stmt->bindParam('imp_iva', $imp_iva, PDO::PARAM_STR);
        $stmt->bindParam('imp_tot', $imp_tot, PDO::PARAM_STR);
        $stmt->bindParam('status', $status, PDO::PARAM_STR);
        $stmt->bindParam('data', $data, PDO::PARAM_STR);
        $stmt->bindParam('data_scadenza', $data_scadenza, PDO::PARAM_STR);
        $stmt->execute();
        echo 'fattura inserita';
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
