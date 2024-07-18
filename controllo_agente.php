<?php
// if (isset($_GET['id'])) {
//     $id = $_GET['id'];
//     include 'functions.php';
//     $dati = get_fattura($id);
//     echo json_encode($dati);
// } else {
//     echo json_encode(array('error' => 'Nessun id specificato'));
// }


include 'functions.php';
include 'include/configpdo.php';
if (isset($_GET['d'])) {
    $data_ultima_fattura = $_GET['d'];
    if (isset($_GET['f'])) {
        $fine = $_GET['f'];
    } else {
        $fine = 0;
    }
} else {
    $data_ultima_fattura = get_data_ultima_fattura();
    $fine = 0;
}

//prelevo i clienti da fatture in cloud

$fatture = get_fatture('1', $data_ultima_fattura, $fine);
//print_r($fatture);
$prima_parte = '';
//controllo se la fattura è già presente nel database altrimenti la inserisco
foreach ($fatture as $fattura) {
    $id = $fattura['id'];
    $cliente = $fattura['id_cliente'];
    $agente = $fattura['note'];
    $parti = explode('-', $agente);
    $prima_parte = $parti[0];
    // echo $prima_parte . '<br>';
    // Se $primaparte è vuoto o contiene la stringa RSC, continua al prossimo ciclo
    // if ($prima_parte == '' || str_contains($prima_parte, "RSC")) {
    //     continue;
    // }

    include('include/configpdo.php');




    try {
        $query = "SELECT * FROM `agenti` WHERE `sigla` = :prima_parte";
        $stmt = $db->prepare($query);
        $stmt->bindParam('prima_parte', $prima_parte, PDO::PARAM_STR);
        $stmt->execute();
        $row = $stmt->fetch(PDO::FETCH_ASSOC);
        if ($row) {
            $perc = 0;
            if ($prima_parte == 'RSC') {
                //leggo il cliente della fattura e lo unico all'agente RSC per il calcolo della percentuale
                $query = "SELECT z.provv FROM `fatture` f INNER JOIN agenti_roma a ON f.id_cfic=a.id_cfic INNER JOIN zone_roma z ON a.id_zona=z.id_zona WHERE f.id_ffic =:id";
                $stmt  = $db->prepare($query);
                $stmt->bindParam('id', $id, PDO::PARAM_INT);
                $stmt->execute();
                $row = $stmt->fetch(PDO::FETCH_ASSOC);
                if ($row) {
                    $perc = $row['provv'];
                }
            } else {
                //Leggo la percentuale di default della tua religione in base alla sigla per l'agente non RSC
                $query = "SELECT `provv` FROM `agenti` WHERE `sigla`=:sigla";
                $stmt  = $db->prepare($query);
                $stmt->bindParam('sigla',  $prima_parte, PDO::PARAM_STR);
                $stmt->execute();
                $row = $stmt->fetch(PDO::FETCH_ASSOC);
                $perc = $row['provv'];
            }

            //inserisco la percentuale di provvigione, la sigla dell'agente nelle fatture dove id_ffic è uguale a $id

            $query = "UPDATE `fatture` SET `sigla`=:prima_parte,`provv_percent`=:provv WHERE `id_ffic`= :id";
            $stmt = $db->prepare($query);
            $stmt->bindParam('prima_parte', $prima_parte, PDO::PARAM_STR);
            $stmt->bindParam('provv', $perc, PDO::PARAM_STR);
            $stmt->bindParam('id', $id, PDO::PARAM_INT);
            $stmt->execute();

            echo $id . ',' . $perc . '<br>';
        } else {
            echo 'Agente non trovato' . ' ' . $id . '-' . $cliente . '<br>';
        }
    } catch (PDOException $e) {
        echo "Error : " . $e->getMessage();
    }
}
