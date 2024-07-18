<?php
if (!empty($_SERVER['HTTP_X_REQUESTED_WITH']) &&  strtolower($_SERVER['HTTP_X_REQUESTED_WITH']) == 'xmlhttprequest') :
    include 'functions.php';
    include(__DIR__ . '/../../include/configpdo.php');
    try {
        $query = "UPDATE `fatture` SET `provv_percent`=:provv_percent WHERE `id`=:id";
        $stmt = $db->prepare($query);
        $stmt->bindParam('id', $_POST['pk'], PDO::PARAM_INT);
        $stmt->bindParam('provv_percent', $_POST['value'], PDO::PARAM_INT);
        $stmt->execute();
    } catch (PDOException $e) {
        echo "Error : " . $e->getMessage();
    }


    try {
        $query = "SELECT `imp_netto`,provv_percent FROM `fatture` WHERE `id`=:id";
        $stmt = $db->prepare($query);
        $stmt->bindParam('id', $_POST['pk'], PDO::PARAM_INT);
        $stmt->execute();
        $row   = $stmt->fetch(PDO::FETCH_ASSOC);
        $provv = $row['imp_netto'] * $row['provv_percent'] / 100;
        echo arrotondaEFormatta($provv) . ' €';
    } catch (PDOException $e) {
        echo "Error : " . $e->getMessage();
    }
else :
    exit();
endif;
