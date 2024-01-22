<?php

include(__DIR__ . '/../../include/configpdo.php');
$type = 0;
if (isset($_POST['type'])) {
    $type = $_POST['type'];
}
// Search result
$searchText = $_POST['search'];
try {
    $query = "SELECT * FROM `clienti` WHERE nome LIKE '%$searchText%' ORDER BY nome ASC LIMIT 5";
    $stmt = $db->prepare($query);
    $stmt->execute();
    $dati   = $stmt->fetchAll();
    if (count($dati) > 0) {
        foreach ($dati as $row) {
            $search_arr[] = array("id" => $row['id_cfic'], "name" => $row['nome']);
        }
    } else {
        $search_arr[] = array("id" => 0, "name" => "Nessun risultato");
    }
} catch (PDOException $e) {
    echo "Error : " . $e->getMessage();
}

echo json_encode($search_arr);
