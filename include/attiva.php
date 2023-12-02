<?php
if (!empty($_SERVER['HTTP_X_REQUESTED_WITH']) &&  strtolower($_SERVER['HTTP_X_REQUESTED_WITH']) == 'xmlhttprequest') :
    $cod = $_POST['codice'];

    $password2 = md5($_POST['password']);
    include('configpdo.php');
    try {
        $query = "UPDATE `user` SET `password`=:passaccount , act='1' WHERE `controllo`= :codcontrollo";
        $stmt = $db->prepare($query);
        $stmt->bindParam('passaccount', $password2, PDO::PARAM_STR);
        $stmt->bindParam('codcontrollo', $cod, PDO::PARAM_STR);
        $stmt->execute();
        //ritorno un json di status ok
        echo json_encode(array('status' => 'ok'));
    } catch (PDOException $e) {
        echo "Error : " . $e->getMessage();
    }
else :
    exit();
endif;
