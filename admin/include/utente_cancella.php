<?php
if (!empty($_SERVER['HTTP_X_REQUESTED_WITH']) &&  strtolower($_SERVER['HTTP_X_REQUESTED_WITH']) == 'xmlhttprequest') :
    $id = intval($_POST['id']);

    include "../../include/configpdo.php";
    $query = "DELETE FROM user WHERE id_user = :id";
    $stmt = $db->prepare($query);
    $stmt->bindParam(':id', $id, PDO::PARAM_INT);
    $stmt->execute();

else :
    exit();
endif;
