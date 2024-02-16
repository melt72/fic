<?php session_start();
if (isset($_GET['id_liquidazione'])) {
    $id_fattura = $_GET['id_liquidazione'];
} else {
    header("Location: ../../404.php");
}

include(__DIR__ . '/../include/configpdo.php');
include 'include/functions.php';

try {
    $query = "SELECT * FROM `liquidazioni` INNER JOIN agenti ON liquidazioni.sigla=agenti.sigla WHERE liquidazioni.id=:id_liquidazione";
    $stmt = $db->prepare($query);
    $stmt->bindParam('id_liquidazione', $id_fattura, PDO::PARAM_INT);
    $stmt->execute();
    $row   = $stmt->fetch(PDO::FETCH_ASSOC);
} catch (PDOException $e) {
    echo "Error : " . $e->getMessage();
}


$html = '
<!doctype html>
<html>
<head>
    <meta charset="utf-8">
    <title>Prospetto Liquidazione</title>

    <style>
    .invoice-box {
        max-width: 800px;
        margin: auto;
        padding: 30px;
       
        font-size: 16px;
        line-height: 24px;

       
    }

    .invoice-box table {
        width: 100%;
        line-height: inherit;
        text-align: left;
    }

    .invoice-box table td {
        padding: 5px;
        vertical-align: top;
    }

    .invoice-box table tr td:nth-child(2) {
        text-align: right;
    }

    .invoice-box table tr.top table td {
        padding-bottom: 20px;
    }

    .invoice-box table tr.top table td.title {
        font-size: 45px;
        line-height: 45px;
        color: #333;
    }

    .invoice-box table tr.information table td {
        padding-bottom: 40px;
    }

    .invoice-box table tr.heading td {
        background: #eee;
        border-bottom: 1px solid #ddd;
        font-weight: bold;
    }

    .invoice-box table tr.details td {
        padding-bottom: 20px;
    }

    .invoice-box table tr.item td{
        border-bottom: 1px solid #eee;
    }

    .invoice-box table tr.item.last td {
        border-bottom: none;
    }

    .invoice-box table tr.total td:nth-child(2) {
        border-top: 2px solid #eee;
        font-weight: bold;
    }

    @media only screen and (max-width: 600px) {
        .invoice-box table tr.top table td {
            width: 100%;
            display: block;
            text-align: center;
        }

        .invoice-box table tr.information table td {
            width: 100%;
            display: block;
            text-align: center;
        }
    }

    /** RTL **/
    .rtl {
        direction: rtl;

    }

    .rtl table {
        text-align: right;
    }

    .rtl table tr td:nth-child(2) {
        text-align: left;
    }

    .scritte{
        font-size: 13px;
    }
    </style>
</head>

<body>
    <div class="invoice-box">
        <table cellpadding="0" cellspacing="0">
            <tr class="top">
                <td colspan="3"> 
                <img src="logo.jpg" style="width:100%; max-width:250px;">
                            </td>
                <td colspan="3">
                Data: ' . date('d/m/Y', strtotime($row['data'])) . '<br><br><br><br>
                AGENZIA: <br>' . $row['nome_agente'] . '</td>
            </tr>

            <tr class="heading">
                <td colspan="3"></td>
                <td colspan="3"></td>
            </tr>

<tr><td  colspan="6"><br></td></tr>';
// Per Roma controllo Nella tabella liquidazione Roma se è presente un pagamento

try {
    $query = "SELECT * FROM `liquidazioni_roma` WHERE `id_liquidazione`=:id_liquidazione";
    $stmt = $db->prepare($query);
    $stmt->bindParam('id_liquidazione', $id_fattura, PDO::PARAM_INT);
    $stmt->execute();
    $dati   = $stmt->fetchAll();
    if (!empty($dati)) {
        foreach ($dati as $rowroma) {

            $html .= '
            <tr class="heading">
                <td>' . date('d/m/Y', strtotime($rowroma['data_pagamento'])) . '</td>
                <td colspan="2">' . $rowroma['nome_liquidaroma'] . '</td>
                <td>' . arrotondaEFormatta($rowroma['importo'])  . '€</td>
                <td>';
            switch ($rowroma['metodo']) {
                case '1':
                    $html .= ' Bonifico';
                    # code...
                    break;
                case '2':
                    $html .= ' Assegno';
                    # code...
                    break;
                case '3':
                    $html .= ' Contanti';
                    # code...
                    break;
            };
            $html .= '
            </td>
            
            <td>' . $rowroma['note_liquidaroma'] . '</td></tr>';
        }
        $html .= '<tr class="item"><td><br></td><td></td></tr>';
    }
} catch (PDOException $e) {
    echo "Error : " . $e->getMessage();
}


$html .= '


<tr class="heading">
    <td colspan="2">Zona</td>
    <td colspan="2">Agente</td>
    <td colspan="2">Agenzia</td>
</tr>';

$zone = get_zone(); //$zone è un array con tutte le zone
$totale_complessivo = 0;
$totale_a = 0;
$totale_b = 0;
foreach ($zone as $zona) {
    $nome_zona = $zona['nome_zona'];
    $id_zona = $zona['id_zona'];
    $totale = 0; //totale della liquidazione
    $a = 0; //totale della liquidazione per l'agente 
    $b = 0; //totale della liquidazione per agenzia
    try {
        $query = "SELECT (`imp_netto` * 16 / 100) AS totale, provv_percent AS tipo FROM `fatture` INNER JOIN agenti_roma ON fatture.id_cfic=agenti_roma.id_cfic WHERE agenti_roma.id_zona=:id_zona AND id_liquidazione ='$id_fattura'";
        $stmt = $db->prepare($query);
        $stmt->bindParam('id_zona', $id_zona, PDO::PARAM_INT);
        $stmt->execute();
        $dati =  $stmt->fetchAll();
        foreach ($dati as $row) {
            switch ($row['tipo']) { //tipo di provvigione 1=50% agente 2=100% agenzia 3=caso particolare di roma (50% agenzia e il restante ripartito tra le altre zone)
                case '1':
                    $a += $row['totale'] / 2;
                    $b += $row['totale'] / 2;
                    break;
                case '2':
                    $a += 0;
                    $b += $row['totale'];
                    break;
                case '3': //caso particolare di roma (50% agenzia e il restante ripartito tra le altre zone)
                    $a += $row['totale'] / 2;
                    $b += $row['totale'] / 2;
                    break;
            }
            $totale += $row['totale'];
        }
        $totale_complessivo += $totale;
        $totale_a += $a;
        $totale_b += $b;
        $html .= '
        <tr  class="item">
        <td colspan="2">' . $nome_zona . '</td>
        <td colspan="2">' . arrotondaEFormatta($a) . '€</td>
        <td colspan="2">' . arrotondaEFormatta($b) . '€</td>
    </tr>';
    } catch (PDOException $e) {
        echo "Error : " . $e->getMessage();
    }
}

$html .= '
<tr>
<td colspan="6"><br></td>
</tr>';

//Per ogni zona
foreach ($zone as $zona) {
    $nome_zona = $zona['nome_zona']; //nome della zona
    $id_zona = $zona['id_zona']; //id della zona
    $html .= '
    <tr class="item">
    <td colspan="6" align="center" >' . $nome_zona . '</td> 
    </tr>

<tr class="heading">
    <td colspan="2" class="scritte">Cliente</td>
    <td class="scritte">Fatt n</td>
    <td class="scritte">Data</td>
    <td colspan="2" class="scritte">Importo Fatt.</td>
    <td colspan="2" class="scritte">Imponibile Netto</td>
</tr>';
    try {
        $query = "SELECT nome, `num_f`,`data_f`,`imp_tot` ,`imp_netto` FROM `fatture` f INNER JOIN clienti c ON f.id_cfic=c.id_cfic INNER JOIN agenti_roma ag ON f.id_cfic=ag.id_cfic INNER JOIN zone_roma z ON ag.id_zona=z.id_zona WHERE f.id_liquidazione='$id_fattura' AND z.id_zona=:id_zona";
        $stmt = $db->prepare($query);
        $stmt->bindParam('id_zona', $id_zona, PDO::PARAM_INT);
        $stmt->execute();
        $dati =  $stmt->fetchAll();
        foreach ($dati as $row) {
            $html .= '
            <tr class="item">
            <td colspan="2" class="scritte">' . $row['nome'] . '</td>
            <td class="scritte">' . $row['num_f'] . '</td>
            <td class="scritte">' . date('d/m/Y', strtotime($row['data_f'])) . '</td>
            <td colspan="2" class="scritte">' . arrotondaEFormatta($row['imp_tot']) . '€</td>
            <td colspan="2" class="scritte">' . arrotondaEFormatta($row['imp_netto']) . '€</td>
        </tr>';
        }
    } catch (PDOException $e) {
        echo "Error : " . $e->getMessage();
    }
    $html .= '
    <tr>
    <td  colspan="6"><br></td>
</tr>';
}
$html .= '
</table>
</div>
</body>

</html>
';
// $html = str_replace('%importo_totale%', arrotondaEFormatta($totale_complessivo), $html);

// require_once 'assets/vendor/autoload.php';


// $mpdf = new \Mpdf\Mpdf(['mode' => 'c']);

// $mpdf->WriteHTML($html);
// $file_name = 'ricevuta.pdf';
// $mpdf->Output($file_name, 'I');
echo $html;
