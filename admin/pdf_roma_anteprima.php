<?php
if (isset($_GET['fatture'])) {
    $fattureJson = $_GET['fatture']; //array con id_fattura
    $start = $_GET['start']; //periodo di riferimento per la liquidazione
    $end = $_GET['end']; //periodo di riferimento per la liquidazione
    $anno = $_GET['anno']; //anno di riferimento per la liquidazione
    $diritto_agenzia = 0;
    $diritto = 0;
    // Decodifica la stringa JSON
    $array_id_fattura = json_decode(urldecode($fattureJson), true); //array con id_fattura

    // Converti l'array di ID delle fatture in una stringa per l'uso nella query SQL
    $id_fatture_string = implode(',', $array_id_fattura);

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
<body><h1>Anteprima Liquidazione Agenzia Risaca</h1>
<h2>Anno di riferimento: ' . $anno . '</h2>
<h2>Periodo: ' . $start . ' - ' . $end . '</h2>';
    // Query per ottenere il totale per ciascuna zona
    $sql = "SELECT
    z.id_zona, 
    z.nome_zona, 
    IFNULL(SUM(f.imp_netto * 16 / 100), 0) AS totale, 
    z.provv AS tipo 
FROM 
    zone_roma z
LEFT JOIN 
    agenti_roma a ON z.id_zona = a.id_zona
LEFT JOIN 
    clienti c ON a.id_cfic = c.id_cfic
LEFT JOIN 
    fatture f ON c.id_cfic = f.id_cfic AND f.id IN ($id_fatture_string)
GROUP BY 
    z.id_zona
ORDER BY
    z.id_zona
        ";
    $stmt = $db->prepare($sql);
    $stmt->execute();
    $result = $stmt->fetchAll(PDO::FETCH_ASSOC);
    $totale_rsc = 0;
    $html .= '<table border="1">
    <tr><th class="col-1">Zona</th><th  class="col-2">Agente</th><th  class="col-3">Agenzia</th></tr>';
    foreach ($result as $row) {
        $totale = 0;
        $a = 0;
        $b = 0;
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
        $totale_rsc += $b;
        $zone_totali[] = array('id_zona' => $row['id_zona'], 'nome_zona' => $row['nome_zona'], 'a' => $a, 'b' => $b);
        if ($row['id_zona'] != 17) {
            $html .= '<tr>
       <td>' . $row['nome_zona'] . '</td>';
            $html .= '<td  class="right">' . arrotondaEFormatta($a) . ' €</td>';
            $html .= '<td  class="right">' . arrotondaEFormatta($b) . ' €</td></tr>';
        }
        if (($row['id_zona'] == 19) || ($a != 0 || $b != 0)) {
            $diritto_agenzia = 1;
            $diritto = $a;
        }
    }
    $html .= '</table><br><br>';
    //Ripartizione diritto d'agenzia .
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

    try {
        // Query per ottenere il totale per ciascuna zona
        $sql = "
        SELECT 
            z.nome_zona, 
            c.nome, 
            f.imp_tot,
            f.imp_netto,
            f.num_f,
            f.data_f 
        FROM 
            fatture f
        INNER JOIN 
            clienti c ON f.id_cfic = c.id_cfic
        INNER JOIN 
            agenti_roma a ON c.id_cfic = a.id_cfic
        INNER JOIN 
            zone_roma z ON a.id_zona = z.id_zona
        WHERE 
            f.id IN ($id_fatture_string)
        ORDER BY 
            z.id_zona, c.nome
    ";


        $stmt = $db->prepare($sql);
        $stmt->execute();
        $result = $stmt->fetchAll(PDO::FETCH_ASSOC);

        // Inizializza l'array per raggruppare le fatture per zona
        $fatture_per_zona = [];
        foreach ($result as $row) {
            $fatture_per_zona[$row['nome_zona']][] = $row;
        }

        // Genera l'output HTML
        $html .= '<table border="1">
        <tr><th>Cliente</th><th>F. numero</th><th>Data</th><th>Importo Tot</th><th>Importo Netto</th></tr>
        <tr><td colspan="5"><br><br></td></tr>';
        foreach ($fatture_per_zona as $zona => $fatture) {
            $html .= '<tr><td colspan="5" class="center" style="background-color: #f2f2f2;"><strong>' . htmlspecialchars($zona) . '</strong></td></tr>';
            foreach ($fatture as $fattura) {
                $html .= '<tr>
                <td>' . htmlspecialchars($fattura['nome']) . '</td>
                <td>' . htmlspecialchars($fattura['num_f']) . '</td>
                <td>' . htmlspecialchars(date('d/m/Y', strtotime($fattura['data_f']))) . '</td>
                <td class="right">' . htmlspecialchars(arrotondaEFormatta($fattura['imp_tot'])) . ' €</td>
                <td class="right">' . htmlspecialchars(arrotondaEFormatta($fattura['imp_netto'])) . ' €</td>
                </tr>';
            }
            $html .= '<tr><td colspan="3" style="background-color: #f2f2f2;"><strong>Totale ' . htmlspecialchars($zona) . '</strong></td><td  class="right" style="background-color: #f2f2f2;"><strong>' . arrotondaEFormatta(array_sum(array_column($fatture, 'imp_tot'))) . ' €</strong></td><td  class="right" style="background-color: #f2f2f2;"><strong>' . arrotondaEFormatta(array_sum(array_column($fatture, 'imp_netto'))) . ' €</strong></td></tr>';
            $html .= '<tr><td colspan="5"><br><br></td></tr>';
        }
        $html .= '</table>';
    } catch (PDOException $e) {
        echo 'Connection failed: ' . $e->getMessage();
    }

    // Genera il PDF qui utilizzando $array_id_fattura
    // ...
} else {
    echo "Nessun dato ricevuto.";
}
$html .= '</body>
</html>';
require_once 'assets/vendor/autoload.php';

$mpdf = new \Mpdf\Mpdf(['mode' => 'c']);
$mpdf->WriteHTML($html);
$file_name = 'Velina_RSC.pdf';
$mpdf->Output($file_name, 'I');
