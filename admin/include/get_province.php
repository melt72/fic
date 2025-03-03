<?php
if (!empty($_SERVER['HTTP_X_REQUESTED_WITH']) &&  strtolower($_SERVER['HTTP_X_REQUESTED_WITH']) == 'xmlhttprequest') :
    if (isset($_POST['regioni'])) {
        $regioni = $_POST['regioni'];
        // Converti l'array delle regioni in una stringa per la query SQL
        $regions_list = implode("','", $regioni);
    } else {
        $regions_list = '';
    }
    $tipo = $_POST['tipo']; //tipo di richiesta lista clienti o altro

    switch ($tipo) {
        case '1': //lista clienti
            $query = "SELECT DISTINCT nome_provincia, pv FROM province p JOIN clienti c ON c.provincia=p.pv  WHERE p.nome_regione in ('$regions_list')";
            break;

        case '2': //lista fatture    
            $s = $_POST['s'];
            $s = date('Y-m-d', strtotime($s));
            $e = $_POST['e'];
            $e = date('Y-m-d', strtotime($e));
            $query = "SELECT DISTINCT nome_provincia, pv FROM province p JOIN clienti c ON c.provincia=p.pv JOIN fatture f ON c.id_cfic=f.id_cfic WHERE p.nome_regione in ('$regions_list') AND (f.data_f BETWEEN '$s' AND '$e') AND f.ie='1'";
            break;
    }


    // print_r($regioni);
    // $anno = $_POST['anno'];
    // $paese = $_POST['paese'];

    include(__DIR__ . '/../../include/configpdo.php');

    if (empty($regioni)) {
        exit();
    } else {

        $stmt = $db->prepare($query);
        $stmt->execute();
        $dati = $stmt->fetchAll();
        if (!empty($dati)) {
            echo '<div>Province</div><select class="form-control testselect2" multiple="multiple" id="province">';
            // echo '<option value="">Seleziona la provincia</option>';
            foreach ($dati as $row) {
                echo '<option value="' . $row['pv'] . '">' . $row['nome_provincia'] . '</option>';
            }
            echo '</select>';
        }
    }

else :
    exit();
endif;
