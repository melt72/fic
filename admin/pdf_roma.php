<?php
session_start();
if (!isset($_GET['id_liquidazione'])) {
    header("Location: ../../404.php");
    exit();
}

$id_fattura = $_GET['id_liquidazione'];

$diritto_agenzia = 0;
$diritto = 0;

include(__DIR__ . '/../include/configpdo.php');
include 'include/functions.php';

try {
    $query = "SELECT * FROM `liquidazioni` INNER JOIN agenti ON liquidazioni.sigla=agenti.sigla WHERE liquidazioni.id=:id_liquidazione";
    $stmt = $db->prepare($query);
    $stmt->bindParam('id_liquidazione', $id_fattura, PDO::PARAM_INT);
    $stmt->execute();
    $row = $stmt->fetch(PDO::FETCH_ASSOC);
    print_r($row);
} catch (PDOException $e) {
    echo "Error : " . $e->getMessage();
    exit();
}

$html = '
<!doctype html>
<html>
<head>
    <meta charset="utf-8">
    <title>Prospetto Liquidazione</title>
        <style>
        table {
            width: 100%;
            border-collapse: collapse;
        }
        th, td {
            border: 1px solid #ddd;
            padding: 8px;
        }
        th {
            background-color: #f2f2f2;
        }
        .col-1 {
            width: 50%;
        }
        .col-2, .col-3 {
            width: 25%;
        }
            .right {
            text-align: right;
        }
            .center {
            text-align: center;
        }
            .no-border {
            border: none;
        }
    </style>
</head>
<body>';

$html .= '<table class="no-border">
<tr>
<td  class="no-border"><img src="logo.jpg" style="width:100%; max-width:250px;"></td>
<td  class="no-border">Data: ' . date('d/m/Y', strtotime($row['data'])) . '<br><br><h1>AGENZIA: <br>Risaca</h1><br>
<h2>Anno di riferimento: ' . $row['anno'] . '</h2>
<h2>Periodo: ' . date('d/m/Y', strtotime($row['periodo_start'])) . ' - ' . date('d/m/Y', strtotime($row['periodo_end'])) . '</h2></td>
</tr>
</table>';




// try {
//     $query = "SELECT * FROM `liquidazioni_roma` WHERE `id_liquidazione`=:id_liquidazione";
//     $stmt = $db->prepare($query);
//     $stmt->bindParam(':id_liquidazione', $id_fattura, PDO::PARAM_INT);
//     $stmt->execute();
//     $dati = $stmt->fetchAll(PDO::FETCH_ASSOC);
//     if (!empty($dati)) {
//         foreach ($dati as $rowroma) {
//             $html .= '
//             <tr class="heading">
//                 <td>' . date('d/m/Y', strtotime($rowroma['data_pagamento'])) . '</td>
//                 <td colspan="2">' . htmlspecialchars($rowroma['nome_liquidaroma']) . '</td>
//                 <td>' . arrotondaEFormatta($rowroma['importo']) . '€</td>
//                 <td>';
//             switch ($rowroma['metodo']) {
//                 case '1':
//                     $html .= ' Bonifico';
//                     break;
//                 case '2':
//                     $html .= ' Assegno';
//                     break;
//                 case '3':
//                     $html .= ' Contanti';
//                     break;
//             }
//             $html .= '</td>
//                       <td>' . htmlspecialchars($rowroma['note_liquidaroma']) . '</td>
//                       </tr>';
//         }
//         $html .= '<tr class="item"><td><br></td><td></td></tr>';
//     }
// } catch (PDOException $e) {
//     echo "Error : " . $e->getMessage();
//     exit();
// }

$zone = get_zone(); // $zone è un array con tutte le zone
$totale_complessivo = 0;
$totale_a = 0;
$totale_b = 0;
$totale_rsc = 0;

$html .= '<table border="1">
<tr><th class="col-1">Zona</th><th  class="col-2">Agente</th><th  class="col-3">Agenzia</th></tr>';

foreach ($zone as $zona) {
    $nome_zona = $zona['nome_zona'];
    $id_zona = $zona['id_zona'];
    $totale = 0;
    $a = 0;
    $b = 0;
    try {
        $query = "SELECT (`imp_netto` * 16 / 100) AS totale, provv_percent AS tipo 
                  FROM `fatture` 
                  INNER JOIN agenti_roma ON fatture.id_cfic = agenti_roma.id_cfic 
                  WHERE agenti_roma.id_zona = :id_zona AND id_liquidazione = :id_liquidazione";
        $stmt = $db->prepare($query);
        $stmt->bindParam(':id_zona', $id_zona, PDO::PARAM_INT);
        $stmt->bindParam(':id_liquidazione', $id_fattura, PDO::PARAM_INT);
        $stmt->execute();
        $dati = $stmt->fetchAll(PDO::FETCH_ASSOC);

        foreach ($dati as $row) {
            switch ($row['tipo']) {
                case '1':
                    $a += $row['totale'] / 2;
                    $b += $row['totale'] / 2;
                    break;
                case '2':
                    $b += $row['totale'];
                    break;
                case '3':
                    $a += $row['totale'] / 2;
                    $b += $row['totale'] / 2;
                    break;
            }
            $totale += $row['totale'];
        }
        $totale_complessivo += $totale;
        $totale_a += $a;
        $totale_b += $b;
        $totale_rsc += $b;
        //creo un array con i totali per ogni zona
        $zone_totali[] = array('id_zona' => $id_zona, 'nome_zona' => $nome_zona, 'a' => $a, 'b' => $b);


        $html .= '
            <tr>
                <td>' . htmlspecialchars($nome_zona) . '</td>';
        $html .= '<td  class="right">' . arrotondaEFormatta($a) . ' €</td>';
        $html .= '<td  class="right">' . arrotondaEFormatta($b) . ' €</td></tr>';

        //se la zona è 19 e ci sono importi di complessi di agenzia
        if (($id_zona == 19) || ($a != 0 || $b != 0)) {
            $diritto_agenzia = 1;
            $diritto = $a;
        }
    } catch (PDOException $e) {
        echo "Error : " . $e->getMessage();
        exit();
    }
}
$html .= '</table><br><br>';

//



//totale della colonna agenzia
if ($diritto_agenzia == 1) {
    $diritto_zona = $diritto / 10;

    $html .= '
    <h2>Ripartizione Complesso di Agenzia</h2>
    <h3>Zone con Agente</h3>
    <table border="1">';

    //tutte le  zone con agente
    try {
        $query = "SELECT * FROM `zone_roma` WHERE `provv`='1'";
        $stmt = $db->prepare($query);
        $stmt->execute();
        $dati   = $stmt->fetchAll();
        foreach ($dati as $row) {
            if (($row['id_zona'] != '17') || ($row['id_zona'] != '19')) {
                //creo un array con gli id delle zone con agente
                $zone_con_agente[] = $row['id_zona'];
                $html .= '<tr>
                <td>' . $row['nome_zona'] . '</td>
                <td  class="right">' . arrotondaEFormatta($diritto_zona) . ' €</td></tr>';
            }
        }
        $html .= '</table><br><br>';
    } catch (PDOException $e) {
        echo "Error : " . $e->getMessage();
    }
    $html .= '
<h3>Zone con Agenzia</h3>
    <table border="1">';
    //tutte le  zone con solo agenzia
    try {
        $query = "SELECT * FROM `zone_roma` WHERE `provv`='2'";
        $stmt = $db->prepare($query);
        $stmt->execute();
        $dati   = $stmt->fetchAll();
        $n = 0;
        foreach ($dati as $row) {
            if (($row['id_zona'] != '17') && ($row['id_zona'] != '19')) {
                $html .= '<tr>
                <td>' . $row['nome_zona'] . '</td>
                <td  class="right">' . arrotondaEFormatta($diritto_zona) . ' €</td></tr>';
                $n++;
            }
        }
    } catch (PDOException $e) {
        echo "Error : " . $e->getMessage();
    }
    $risaca = $totale_rsc + ($diritto_zona * $n);
    $html .= '</table><br><br>';
    $html .= '<h2>Riepilogo totale Zone</h2>
  <table border="1">
  <tr>
  <th>Zona</th>
    <th>Provvigione</th>
    <th>Complesso Agenzia</th>
    <th>Totale</th>
</tr>';
    $zone_assoc = [];

    foreach ($zone_totali as $zona) {
        $zone_assoc[$zona['id_zona']] = $zona;
    }

    foreach ($zone_con_agente as $id_zona) {
        if (isset($zone_assoc[$id_zona])) {
            $zona = $zone_assoc[$id_zona];
            $html .= '
    <tr>
        <td>' . htmlspecialchars($zona['nome_zona']) . '</td>
        <td  class="right">' . arrotondaEFormatta($zona['a']) . ' €</td>
        <td  class="right">' . arrotondaEFormatta($diritto_zona) . ' €</td>
        <td  class="right">' . arrotondaEFormatta($zona['a'] + $diritto_zona) . ' €</td>
    </tr>';
        }
    }
    $html .= '</table><br><br>';
    $html .= '<h2>Riepilogo totale Risaca</h2>
    <table border="1">
    <tr>
        <td >Totale Zone</td>
        <td  class="right">' . arrotondaEFormatta($totale_rsc) . ' €</td></tr>
      <tr><td>Complesso di Agenzia</td><td  class="right">' . arrotondaEFormatta($diritto_zona * $n) . ' €</td></tr>
        <tr> <td style="background-color: #f2f2f2;"><strong>Totale Agenzia Risaca</strong></td>
        <td  class="right" style="background-color: #f2f2f2;">' . arrotondaEFormatta($risaca) . ' €</td></tr>
        ';
    $html .= '</table><br><br>';
} else {


    $html .= '<table border="1">
<tr>
<td style="background-color: #f2f2f2;"><strong>Totale Agenzia Risaca</strong></td>
<td  class="right" style="background-color: #f2f2f2;"><strong>' . arrotondaEFormatta($totale_rsc) . ' €</strong></td></tr>';
    $html .= '</table><br><br>';
}


// $html .= '<table border="1">
//  <tr>
//      <td style="background-color: #f2f2f2;"><strong>Totale Agenzia Risaca</strong></td>
//      <td  class="right" style="background-color: #f2f2f2;"><strong>' . arrotondaEFormatta($totale_rsc) . ' €</strong></td></tr>';
// $html .= '</table><br><br>';




$html .= '<table border="1">
            <tr>
            <th>Cliente</th>
            <th>F. numero</th>
            <th>Data</th>
            <th>Importo Tot</th>
            <th>Importo Netto</th>
            </tr>
            <tr>
            <td colspan="5"><br><br></td>
            </tr>';
foreach ($zone as $zona) {
    $nome_zona = $zona['nome_zona'];
    $id_zona = $zona['id_zona'];

    try {
        $query = "SELECT nome, `num_f`, `data_f`, `imp_tot`, `imp_netto` 
                  FROM `fatture` f 
                  INNER JOIN clienti c ON f.id_cfic = c.id_cfic 
                  INNER JOIN agenti_roma ag ON f.id_cfic = ag.id_cfic 
                  INNER JOIN zone_roma z ON ag.id_zona = z.id_zona 
                  WHERE f.id_liquidazione = :id_liquidazione AND z.id_zona = :id_zona
                  order by nome asc
                  ";
        $stmt = $db->prepare($query);
        $stmt->bindParam(':id_zona', $id_zona, PDO::PARAM_INT);
        $stmt->bindParam(':id_liquidazione', $id_fattura, PDO::PARAM_INT);
        $stmt->execute();
        $dati = $stmt->fetchAll(PDO::FETCH_ASSOC);

        if (!empty($dati)) {


            $imp_totale = 0;
            $imp_netto = 0;
            $html .= '<tr><td colspan="5" class="center" style="background-color: #f2f2f2;"><strong>' . htmlspecialchars($nome_zona) . '</strong></td></tr>';
            foreach ($dati as $row) {

                $imp_totale += $row['imp_tot'];
                $imp_netto += $row['imp_netto'];
                $html .= '
                <tr>
                    <td >' . htmlspecialchars($row['nome']) . '</td>
                    <td class="scritte">' . htmlspecialchars($row['num_f']) . '</td>
                    <td class="scritte">' . date('d/m/Y', strtotime($row['data_f'])) . '</td>
                    <td  class="right">' . arrotondaEFormatta($row['imp_tot']) . '€</td>
                    <td  class="right">' . arrotondaEFormatta($row['imp_netto']) . '€</td>
                </tr>';
            }

            $html .= '
            <tr>
                <td colspan="3" style="background-color: #f2f2f2;"><strong>Totale ' . htmlspecialchars($nome_zona) . '</strong></td>
                <td class="right" style="background-color: #f2f2f2;">' . arrotondaEFormatta($imp_totale) . '€</td>
                <td class="right" style="background-color: #f2f2f2;">' . arrotondaEFormatta($imp_netto) . '€</td>
            </tr>
          ';
        }
        $html .= '<tr><td colspan="5"><br><br></td></tr>';
    } catch (PDOException $e) {
        echo "Error : " . $e->getMessage();
        exit();
    }
}
$html .= '</table>';
$html .= '
</body>
</html>';

require_once 'assets/vendor/autoload.php';

$mpdf = new \Mpdf\Mpdf(['mode' => 'c']);
$mpdf->WriteHTML($html);
$file_name = 'Velina_RSC.pdf';
$mpdf->Output($file_name, 'I');
