<?php
include 'functions.php';
include 'include/configpdo.php';
include 'config-api2.php';
//array delle fatture
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


// Retrieve the first company id
$companies = $userApi->listUserCompanies();
// se il tipo è all allora prelevo tutte le fatture
$firstCompanyId = $companies->getData()->getCompanies()[1]->getId();
$fatture = get_fatture('1', $data_ultima_fattura, $fine);
//seleziono tutte le fatture non ancora inviate



foreach ($fatture as $fattura) {
    $id = $fattura['id'];
    $cliente = $fattura['id_cliente'];
    $imp_netto = $fattura['imp_netto'];
    $imp_iva = $fattura['iva'];
    $imp_tot = $fattura['imp_tot'];
    $invio = $fattura['invio'];

    //seleziono dal db i dati della fattura e li comparo
    try {
        $query = "SELECT * FROM `fatture` WHERE `id_ffic`= :id";
        $stmt = $db->prepare($query);
        $stmt->bindParam('id', $id, PDO::PARAM_INT);
        $stmt->execute();
        $row   = $stmt->fetch(PDO::FETCH_ASSOC);
        if ($row) {
            if ($row['imp_netto'] != $imp_netto || $row['imp_iva'] != $imp_iva || $row['imp_tot'] != $imp_tot || $row['id_cfic'] != $cliente || $row['status_invio'] != $invio) {
                echo 'fattura ' . $id . ' non corretta n ' . $row['num_f'] . '<br>';
                $query_base = "UPDATE `fatture` SET ";
                if ($row['imp_netto'] != $imp_netto) {
                    echo 'imp_netto ' . $row['imp_netto'] . ' ' . $imp_netto . '<br>';
                    $query_base .= "imp_netto = '$imp_netto',";
                }
                if ($row['imp_iva'] != $imp_iva) {
                    echo 'imp_iva ' . $row['imp_iva'] . ' ' . $imp_iva . '<br>';
                    $query_base .= "imp_iva = '$imp_iva',";
                }
                if ($row['imp_tot'] != $imp_tot) {
                    echo 'imp_tot ' . $row['imp_tot'] . ' ' . $imp_tot . '<br>';
                    $query_base .= "imp_tot = '$imp_tot',";
                }
                if ($row['id_cfic'] != $cliente) {
                    echo 'cliente ' . $row['id_cfic'] . ' ' . $cliente . '<br>';
                    $query_base .= "id_cfic = '$cliente',";
                }
                if ($row['status_invio'] != $invio) {
                    echo 'status_invio ' . $row['status_invio'] . ' ' . $invio . '<br>';
                    $query_base .= "status_invio = '$invio',";
                }
                $query_base = rtrim($query_base, ',');
                $query_base .= " WHERE `id_ffic`= '$id'";
                $stmt = $db->prepare($query_base);

                $stmt->execute();
                echo 'fattura ' . $id . ' aggiornata ' . $query_base . '<br>';
            } else {
                echo   'fattura ' . $id . ' corretta<br>';
            }
        } else {
            echo 'fattura ' . $id . ' non presente nel database<br>';
        }
    } catch (PDOException $e) {
        echo "Error : " . $e->getMessage();
    }
}
