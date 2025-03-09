<?php

$start = $_GET['s']; //periodo di riferimento per la liquidazione
$end = $_GET['e']; //periodo di riferimento per la liquidazione


$anno = $_GET['anno']; //anno di riferimento per la liquidazione



include 'include/functions.php';
// Connessione al database
include(__DIR__ . '/../include/configpdo.php');

$html = '
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
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
    </style>
    <title>Tabella</title>
</head>
<body><h1>Scadenziario Agenzia Risaca</h1>
<h2>Anno di riferimento: ' . $anno . '</h2>
<h2>Periodo: ' . $start . ' - ' . $end . '</h2>';
// ottengo i dati delle zone
$query = "SELECT * FROM zone_roma";
$stmt = $db->prepare($query);
$stmt->execute();
$zone = $stmt->fetchAll(PDO::FETCH_ASSOC);
$html .= '
        <table class="table table-bordered border text-nowrap mb-0" >
        <thead>
            <tr>
                <th>Cliente</th>
                <th>n.fatt</th>
                <th>data</th>
                <th>importo</th>
                <th>imponibile</th>
                <th>iva</th>
            </tr>
        </thead>';
$html .= '<tbody id="da' . $zona['nome_zona'] . '">';
foreach ($zone as $zona) {
    $query = "SELECT  f.num_f, f.imp_netto, f.imp_iva, f.imp_tot, f.data_f, f.data_scadenza, c.nome  AS cliente_nome   
        FROM `fatture` f 
        INNER JOIN clienti c ON f.id_cfic = c.id_cfic 
        INNER JOIN agenti_roma ag ON ag.id_cfic=c.id_cfic
        WHERE  ag.id_zona = :id_zona
        AND f.status_invio = 'sent' 
        AND f.status = 'not_paid' 
        AND  f.ie='1'
        AND YEAR(f.data_f) = '$anno' 
        AND f.data_scadenza BETWEEN '$start' AND '$end'";
    $stmt = $db->prepare($query);
    $stmt->bindParam('id_zona', $zona['id_zona'], PDO::PARAM_STR);
    $stmt->execute();
    $dati = $stmt->fetchAll(PDO::FETCH_ASSOC);

    if (count($dati) > 0) {

        $html .= '<tr>
                    <td colspan="6" class="center"><br><h2>' . $zona['nome_zona'] . '</h2></td>
                </tr>';
        $oggi = date('Y-m-d');
        foreach ($dati as $fattura) {
            $html .= '<tr>
                    <td>' . $fattura['cliente_nome'] . '</td>
                    <td>' . $fattura['num_f'] . '</td>
                    <td>Data: ' . date('d/m/Y', strtotime($fattura['data_f'])) . '<br>Scad: ' . date('d/m/Y', strtotime($fattura['data_scadenza'])) . ' </td>
                    ';
            $html .= '
                    <td>' . arrotondaEFormatta($fattura['imp_tot']) . ' €</td>
                    <td>' . arrotondaEFormatta($fattura['imp_netto']) . ' €</td>
                    <td>' . arrotondaEFormatta($fattura['imp_iva'])  . ' €</td>';



            $html .= '</td>
                  
                </tr>';
        }
    }
}
$html .= '
        </tbody>
    </table>';





$html .= '</body>
</html>';


require_once 'assets/vendor/autoload.php';

$mpdf = new \Mpdf\Mpdf(['mode' => 'c']);
$mpdf->WriteHTML($html);
$file_name = 'Scadenziario_RSC.pdf';
$mpdf->Output($file_name, 'I');
