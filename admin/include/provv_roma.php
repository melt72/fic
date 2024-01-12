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

    include(__DIR__ . '/../../include/configpdo.php');
    try {
        $query = "SELECT `imp_netto`,provv_percent FROM `fatture` WHERE `id`=:id";
        $stmt = $db->prepare($query);
        $stmt->bindParam('id', $_POST['pk'], PDO::PARAM_INT);
        $stmt->execute();
        $row   = $stmt->fetch(PDO::FETCH_ASSOC);
        $provv = $row['imp_netto'] * 16 / 100;
        if ($row['provv_percent'] == 1) {
            $prov_agente = arrotondaEFormatta($provv / 2) . ' €';
            $prov_agenzia = arrotondaEFormatta($provv / 2) . ' €';
        } else {
            $prov_agente = '';
            $prov_agenzia = arrotondaEFormatta($provv) . ' €';
        }
        echo json_encode(array('provv_agente' => $prov_agente, 'provv_agenzia' => $prov_agenzia));
    } catch (PDOException $e) {
        echo "Error : " . $e->getMessage();
    }
else :
    exit();
endif;
