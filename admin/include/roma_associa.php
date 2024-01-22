<?php
if (!empty($_SERVER['HTTP_X_REQUESTED_WITH']) &&  strtolower($_SERVER['HTTP_X_REQUESTED_WITH']) == 'xmlhttprequest') :
    include 'functions.php';
    include(__DIR__ . '/../../include/configpdo.php');
    $zona = $_POST['value'];

    if ($zona == '0') {
        //Cancello dalla tabella . Agenti Roma . l'associazione con l'agente
        $query = "DELETE FROM `agenti_roma` WHERE `id_cfic`=:cliente";
        $stmt = $db->prepare($query);
        $stmt->bindParam('cliente', $_POST['pk'], PDO::PARAM_INT);
        $stmt->execute();
        exit();
    }
    //Gestisco la sigla con valore zero cioè senza agente

    //Controllo nella tabella agenti Roma se esiste id_cfic = $_POST['pk']
    $query = "SELECT * FROM `agenti_roma` WHERE `id_cfic`=:id";
    $stmt = $db->prepare($query);
    $stmt->bindParam('id', $_POST['pk'], PDO::PARAM_INT);
    $stmt->execute();
    $row = $stmt->fetch(PDO::FETCH_ASSOC);
    if ($row['id'] == '') :
        //Se non esiste inserisco il record
        $query = "INSERT INTO `agenti_roma` ( `id_cfic`, `id_zona`) VALUES (:cliente, :zona)";
        $stmt = $db->prepare($query);
        $stmt->bindParam('cliente', $_POST['pk'], PDO::PARAM_INT);
        $stmt->bindParam('zona', $zona, PDO::PARAM_STR);
        $stmt->execute();
    else :
        //Se esiste aggiorno il record
        $query = "UPDATE `agenti_roma` SET `id_zona`=:zona WHERE `id_cfic`=:cliente";
        $stmt = $db->prepare($query);
        $stmt->bindParam('cliente', $_POST['pk'], PDO::PARAM_INT);
        $stmt->bindParam('zona', $zona, PDO::PARAM_STR);
        $stmt->execute();
    endif;

else :
    exit();
endif;
