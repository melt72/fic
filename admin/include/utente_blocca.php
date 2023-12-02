<?php
if (!empty($_SERVER['HTTP_X_REQUESTED_WITH']) &&  strtolower($_SERVER['HTTP_X_REQUESTED_WITH']) == 'xmlhttprequest') :
    include('../../include/configpdo.php');

    // modifica stato utente
    try {
        $query = "UPDATE `user` SET`act`=:activa WHERE `id_user`=:iduser ";
        $stmt = $db->prepare($query);
        $stmt->bindParam('iduser', $_POST['id'], PDO::PARAM_STR);
        $stmt->bindParam('activa', $_POST['tipo'], PDO::PARAM_STR);
        $stmt->execute();
        echo 'ok';
    } catch (PDOException $e) {
        echo "Error : " . $e->getMessage();
    }
else :
    exit();
endif;
