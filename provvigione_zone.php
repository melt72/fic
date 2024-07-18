<?php
if (isset($_GET['a'])) {
    $a = $_GET['a'];
} else {
    //anno corrente
    $a = date('Y');
}
include('include/configpdo.php');
//seleziono le zone e le provvigioni
try {
    $query = "SELECT `id_zona`,`provv` FROM `zone_roma`";
    $stmt = $db->prepare($query);
    $stmt->execute();
    $dati   = $stmt->fetchAll();
    foreach ($dati as $row) {
        $idzona = $row['id_zona'];
        $provv = $row['provv'];
        try {
            $query = "SELECT `id_cfic` FROM `agenti_roma` WHERE `id_zona`=:idzona";
            $stmt = $db->prepare($query);
            $stmt->bindParam('idzona', $idzona, PDO::PARAM_INT);
            $stmt->execute();
            $dati   = $stmt->fetchAll();
            foreach ($dati as $row) {
                $idcliente = $row['id_cfic'];
                echo $idcliente . " - " . $provv . "<br>";
                //seleziono le fatture dell'anno corrente per il cliente

                // try {
                //     $query = "SELECT `data_f`, `provv_percent` FROM `fatture` WHERE `id_cfic`= :idcliente AND year(data_f)='2024'";
                //     $stmt = $db->prepare($query);
                //     $stmt->bindParam('idcliente', $idcliente, PDO::PARAM_INT);
                //     $stmt->execute();
                //     $dati   = $stmt->fetchAll();
                //     foreach ($dati as $row) {
                //         echo $row['data_f'] . " - " . $row['provv_percent'] . '-' . $provv . "<br>";
                //     }
                // } catch (PDOException $e) {
                //     echo "Error : " . $e->getMessage();
                // }

                //aggiorno la provvigione
                try {
                    $query = "UPDATE `fatture` SET `provv_percent`='$provv' WHERE `id_cfic`=:idcliente AND year(data_f)='$a'";
                    $stmt = $db->prepare($query);
                    $stmt->bindParam('idcliente', $idcliente, PDO::PARAM_INT);
                    $stmt->execute();
                } catch (PDOException $e) {
                    echo "Error : " . $e->getMessage();
                }
            }
        } catch (PDOException $e) {
            echo "Error : " . $e->getMessage();
        }
    }
} catch (PDOException $e) {
    echo "Error : " . $e->getMessage();
}
