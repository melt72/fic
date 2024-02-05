<?php
include 'functions.php';
include 'include/configpdo.php';
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
            $sql = "INSERT INTO agenti_Roma (id_cfic, zona) VALUES (:id, :zona)";
            $stmt = $db->prepare($sql);
            $stmt->bindParam('id', $id, PDO::PARAM_INT);
            $stmt->bindParam('zona', $zona, PDO::PARAM_STR);
            $stmt->execute();
        }
        echo 'aggiunto cliente<br>';
    } else {
        if (isset($prima_parte) && ($prima_parte == 'RSC')) {
            $sql = "SELECT * FROM agenti_Roma WHERE id_cfic = :id";
            $stmt = $db->prepare($sql);
            $stmt->bindParam('id', $id, PDO::PARAM_INT);
            $stmt->execute();
            $result = $stmt->fetch(PDO::FETCH_ASSOC);
            if (!$result) {
                $sql = "INSERT INTO agenti_roma (id_cfic, id_zona) VALUES (:id, :zona)";
                $stmt = $db->prepare($sql);
                $stmt->bindParam('id', $id, PDO::PARAM_INT);
                $stmt->bindParam('zona', $zona, PDO::PARAM_STR);
                $stmt->execute();
                echo 'aggiunto agente alla zona di roma<br>';
            }
        }
    }
}
echo 'fine';
