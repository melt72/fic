<?php session_start();
if (isset($_GET['id_liquidazione'])) {
    $id_fattura = $_GET['id_liquidazione'];
} else {
    header("Location: ../../404.php");
}

include('../include/configpdo.php');
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
                AGENTE: <br>' . $row['nome_agente'] . '</td>
            </tr>
            <tr class="heading">
                <td colspan="3">
                Metodo di pagamento :  ';
switch ($row['pagamento']) {
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

<td colspan="3">
    Totale pagamento : ' . $row['importo'] . ' €
</td>

</tr>


<tr class="item">
    <td><br></td>
    <td></td>
</tr>

<tr class="heading">
    <td colspan="2">
        Cliente
    </td>
    <td>N°</td>
    <td>
        Imponibile
    </td>
    <td>
        Provv %
    </td>
    <td>
        Provv €
    </td>
</tr>';
try {
    $query = "SELECT
    fatture.*,
    (`imp_netto` * `provv_percent` / 100) AS provvigione,
    clienti.nome AS nome_cliente
FROM
    `fatture`
INNER JOIN
    clienti ON fatture.id_cfic = clienti.id_cfic
WHERE
    `id_liquidazione` = :idfatt;";
    $stmt = $db->prepare($query);
    $stmt->bindParam('idfatt', $id_fattura, PDO::PARAM_INT);
    $stmt->execute();
    $count = $stmt->rowCount();
    if ($count != 0) {
        $dati = $stmt->fetchAll();
        foreach ($dati as $row) {
            $totale += $row['provvigione'];
            $html .= '<tr>
    <td colspan="2">' . $row['nome_cliente'] . '</td>
    <td>n° ' . $row['num_f'] . ' del ' . date('d/m/Y', strtotime($row['data_f'])) . '</td>
    <td>' . arrotondaEFormatta($row['imp_netto']) . ' €</td>
    <td>' . $row['provv_percent'] . ' %</td>
    <td>' . arrotondaEFormatta($row['provvigione']) . ' €</td>
</tr>';
        }
    }
} catch (PDOException $e) {
    echo "Error : " . $e->getMessage();
}


$html .= '

</table>
</div>
</body>

</html>
';

require_once 'assets/vendor/autoload.php';


$mpdf = new \Mpdf\Mpdf(['mode' => 'c']);

$mpdf->WriteHTML($html);
$file_name = 'ricevuta.pdf';
$mpdf->Output($file_name, 'I');
