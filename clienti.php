<?php
include 'functions.php';
include 'include/configpdo.php';
$clienti = get_clients();

//controllo se il cliente è già presente nel database altrimenti lo inserisco
foreach ($clienti as $cliente) {
    $id = $cliente['id'];
    $name = $cliente['name'];
    $citta = $cliente['citta'];
    $provincia = $cliente['provincia'];
    $paese = $cliente['paese'];
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
        echo 'aggiunto cliente<br>';
    } else {
        echo 'il cliente è già presente nel database<br>';
    }
}
echo 'fine';
