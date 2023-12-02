<?php
include('include/configpdo.php');
// array di configurazione vuoto
$config = array();
try {
    $query = "SELECT * FROM `config` WHERE `tipo_config`='email'";
    $stmt = $db->prepare($query);
    $stmt->execute();
    $dati   = $stmt->fetchAll();
    print_r($dati);
    foreach ($dati as $row) {
        // creo un array con i parametri di configurazione
        $config[$row['parametro_config']] = $row['valore_config'];
    }
    print_r($config);
} catch (PDOException $e) {
    echo "Error : " . $e->getMessage();
}
