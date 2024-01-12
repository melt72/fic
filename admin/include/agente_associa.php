<?php
if (!empty($_SERVER['HTTP_X_REQUESTED_WITH']) &&  strtolower($_SERVER['HTTP_X_REQUESTED_WITH']) == 'xmlhttprequest') :
    include 'functions.php';
    include(__DIR__ . '/../../include/configpdo.php');

    //Gestisco la sigla con valore zero cioè senza agente
    if ($_POST['value'] == '0') :
        $sigle = '';
        $perc = '0';
    else :
        $sigle = $_POST['value'];
        //Leggo la percentuale di default della tua religione in base alla sigla
        $query = "SELECT `provv` FROM `agenti` WHERE `sigla`=:sigla";
        $stmt  = $db->prepare($query);
        $stmt->bindParam('sigla', $_POST['value'], PDO::PARAM_STR);
        $stmt->execute();
        $row = $stmt->fetch(PDO::FETCH_ASSOC);
        $perc = $row['provv'];
    endif;

    //Aggiorno la tabella fatture
    $query = "UPDATE `fatture` SET `sigla`=:sigla, provv_percent=:provv_percent WHERE `id`=:id";
    $stmt = $db->prepare($query);
    $stmt->bindParam('id', $_POST['pk'], PDO::PARAM_INT);
    $stmt->bindParam('sigla', $_POST['value'], PDO::PARAM_STR);
    $stmt->bindParam('provv_percent', $perc, PDO::PARAM_INT);
    $stmt->execute();

else :
    exit();
endif;
