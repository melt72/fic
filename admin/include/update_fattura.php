<?php
if (!empty($_SERVER['HTTP_X_REQUESTED_WITH']) &&  strtolower($_SERVER['HTTP_X_REQUESTED_WITH']) == 'xmlhttprequest') :
    $idFattura = $_POST['id'];
    $newStatus = $_POST['status'];
    include(__DIR__ . '/../../include/configpdo.php');
    // Connessione al database
    try {

        // Aggiorna il campo IE della fattura
        $query = "UPDATE fatture SET ie = :status WHERE id = :id";
        $stmt = $db->prepare($query);
        $stmt->bindParam('status', $newStatus, PDO::PARAM_INT);
        $stmt->bindParam('id', $idFattura, PDO::PARAM_INT);
        if ($stmt->execute()) {
            echo json_encode(['success' => true]);
        } else {
            echo json_encode(['success' => false]);
        }
    } catch (PDOException $e) {
        echo json_encode(['success' => false, 'error' => $e->getMessage()]);
    }
else :
    exit();
endif;
