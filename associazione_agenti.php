<?php
$sigla_agente = 'RSC';
$anno = 2024;
$anno_pagamento = 2024;
include('include/configpdo.php');

// trovo tutte le fatture della zona di Roma
try {
    $query = "SELECT f.id , z.provv FROM `fatture` f INNER JOIN `agenti_roma` a ON f.id_cfic = a.id_cfic INNER JOIN zone_roma z ON a.id_zona=z.id_zona WHERE YEAR(f.data_f) = :anno";
    // $query = "SELECT f.id FROM `fatture` f INNER JOIN `agenti_roma` a ON f.id_cfic = a.id_cfic WHERE YEAR(f.data_f) = :anno AND YEAR(f.data_pagamento)=:anno_pagamento";
    $stmt = $db->prepare($query);
    $stmt->bindParam(':anno', $anno, PDO::PARAM_INT);
    // $stmt->bindParam(':anno_pagamento', $anno_pagamento, PDO::PARAM_INT);

    $stmt->execute();
    $dati   = $stmt->fetchAll();
    echo "Fatture trovate: " . count($dati) . "<br>";
    foreach ($dati as $row) {
        //associo la fattura all'agente
        $query = "UPDATE `fatture` SET `sigla`=:sigla, provv_percent=:provv WHERE `id`=:id";
        $stmt = $db->prepare($query);
        $stmt->bindParam('id', $row['id'], PDO::PARAM_INT);
        $stmt->bindParam('sigla', $sigla_agente, PDO::PARAM_STR);
        $stmt->bindParam('provv', $row['provv'], PDO::PARAM_INT);
        $stmt->execute();
        echo "Fattura " . $row['id'] . " associata all'agente " . $sigla_agente . "<br>";
    }
} catch (PDOException $e) {
    echo "Error : " . $e->getMessage();
}
