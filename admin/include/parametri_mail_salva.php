<?php
if (!empty($_SERVER['HTTP_X_REQUESTED_WITH']) &&  strtolower($_SERVER['HTTP_X_REQUESTED_WITH']) == 'xmlhttprequest') :
    $dati = $_POST['dat'];

    include('../../include/configpdo.php');
    foreach ($dati as $k => $v) {
        $query = "UPDATE config SET  valore_config = '" . $v['value'] . "' where parametro_config = '" . $v['name'] . "'";
        // $prep[':' . $v['name']] = $v['value'];
        $stmt = $db->prepare($query);
        $stmt->execute();
    }

else :
    exit();
endif;
