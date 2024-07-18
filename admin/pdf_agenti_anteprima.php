<?php
if (isset($_GET['fatture'])) {
    $fattureJson = $_GET['fatture']; //array con id_fattura
    $start = $_GET['start']; //periodo di riferimento per la liquidazione
    $end = $_GET['end']; //periodo di riferimento per la liquidazione
    $data_start = date('d/m/Y', strtotime($start));
    $data_end = date('d/m/Y', strtotime($end));
    $anno = $_GET['anno']; //anno di riferimento per la liquidazione
    $id_agente = $_GET['id']; //id_agente
    // Decodifica la stringa JSON
    $array_id_fattura = json_decode(urldecode($fattureJson), true); //array con id_fattura

    // Converti l'array di ID delle fatture in una stringa per l'uso nella query SQL
    $id_fatture_string = implode(',', $array_id_fattura);

    include 'include/functions.php';
    // Connessione al database
    include(__DIR__ . '/../include/configpdo.php');
    //prelevo i dati dell'agente
    $sql = "SELECT * FROM agenti WHERE id = :id";
    $stmt = $db->prepare($sql);
    $stmt->execute(['id' => $id_agente]);
    $agente = $stmt->fetch(PDO::FETCH_ASSOC);
    $nome_agente = $agente['nome_agente'];
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
<body><h1>Anteprima Liquidazione Agente<br>' . $nome_agente . '</h1>
<h2>Anno di riferimento: ' . $anno . '</h2>
<h2>Periodo: ' . $start . ' - ' . $end . '</h2>';

    $html .= '<table border="1">
        <tr><th>Cliente</th><th>F. numero</th><th>Provv %</th><th>Importo Tot</th><th>Importo Netto</th><th>Provvigione</th></tr>
        <tr><td colspan="6"><br><br></td></tr>';
    try {
        $query = "SELECT
            f.*,
            (`imp_netto` * `provv_percent` / 100) AS provvigione,
            c.nome AS nome_cliente
        FROM
            `fatture` f
        INNER JOIN
            clienti c ON f.id_cfic = c.id_cfic
        WHERE
             f.id IN ($id_fatture_string)";
        $stmt = $db->prepare($query);
        $stmt->execute();
        $count = $stmt->rowCount();
        if ($count != 0) {
            $dati = $stmt->fetchAll();
            foreach ($dati as $row) {

                $html .= '<tr>
            <td >' . $row['nome_cliente'] . '</td>
            <td class="right">n° ' . $row['num_f'] . ' del ' . date('d/m/Y', strtotime($row['data_f'])) . '</td>
            <td class="right">' . $row['provv_percent'] . ' %</td>
            <td class="right">' . arrotondaEFormatta($row['imp_tot']) . ' €</td>
            <td class="right">' . arrotondaEFormatta($row['imp_netto']) . ' €</td>
            <td class="right">' . arrotondaEFormatta($row['provvigione']) . ' €</td>
        </tr>';
            }
            $html .= '<tr><td colspan="3" style="background-color: #f2f2f2;"><strong>Totale</strong></td><td  class="right" style="background-color: #f2f2f2;"><strong>' . arrotondaEFormatta(array_sum(array_column($dati, 'imp_tot'))) . ' €</strong></td><td  class="right" style="background-color: #f2f2f2;"><strong>' . arrotondaEFormatta(array_sum(array_column($dati, 'imp_netto'))) . ' €</strong></td><td  class="right" style="background-color: #f2f2f2;"><strong>' . arrotondaEFormatta(array_sum(array_column($dati, 'provvigione'))) . ' €</strong></td></tr>';
        }
    } catch (PDOException $e) {
        echo "Error : " . $e->getMessage();
    }
    $html .= '</table><br><br>';
    //Ripartizione diritto d'agenzia .

    $html .= '</body>
</html>';
    require_once 'assets/vendor/autoload.php';


    $mpdf = new \Mpdf\Mpdf(['mode' => 'c']);

    $mpdf->WriteHTML($html);
    $file_name = 'Velina_' . $nome_agente . '_' . $anno . '_' . $data_start . '-' . $data_end . '.pdf';
    $mpdf->Output($file_name, 'I');
}
