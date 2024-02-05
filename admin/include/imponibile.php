<?php
if (!empty($_SERVER['HTTP_X_REQUESTED_WITH']) &&  strtolower($_SERVER['HTTP_X_REQUESTED_WITH']) == 'xmlhttprequest') :
    $tipo = $_POST['tipo'];
    include(__DIR__ . '/../../include/configpdo.php');
    include 'functions.php';
    switch ($tipo) {
        case 'lista_scaduti':

            $anno = $_POST['anno'];
            $data_oggi = date_create(date('Y-m-d'));
            try {
                $query = "SELECT nome, imp_netto,num_f,data_f, data_scadenza FROM `fatture` f INNER JOIN clienti c ON f.id_cfic=c.id_cfic WHERE YEAR(data_f)=:anno AND status ='not_paid'   AND data_scadenza < CURDATE() ORDER BY data_scadenza ASC";
                $stmt = $db->prepare($query);
                $stmt->bindParam(':anno', $anno, PDO::PARAM_STR);
                $stmt->execute();
                $dati   = $stmt->fetchAll();
                if (!empty($dati)) {
                    echo  '<table class="table table-striped mg-b-0 text-md-nowrap">';
                    echo '<thead>';
                    echo '<tr>';
                    echo '<th>Cliente</th>';
                    echo '<th>Imponibile</th>';
                    echo '<th>N.fatt</th>';
                    echo '<th>Data</th>';
                    echo '<th>Scadenza</th>';
                    echo '<th>Giorni</th>';
                    echo '</tr>';
                    echo '</thead>';
                    echo '<tbody>';


                    foreach ($dati as $row) {

                        $data_fattura = date_create($row['data_f']);
                        $data_fattura = $data_fattura->format('d/m/Y');

                        $data_scadenza = isset($row['data_scadenza']) ? date_create($row['data_scadenza']) : null;
                        $data_scadenza_format = $data_scadenza ? $data_scadenza->format('d/m/Y') : null;

                        $data_oggi = new DateTime(); // Presumo che tu abbia già creato $data_oggi da qualche parte nel tuo codice

                        if ($data_scadenza) {
                            $differenza = $data_oggi->diff($data_scadenza);
                            // Ottieni la differenza formattata
                            $differenza_formattata = $differenza->format('%a giorni');
                        } else {
                            // Gestisci la situazione in cui la data di scadenza non è disponibile
                            $differenza_formattata = "Data di scadenza non disponibile";
                        }


                        echo '<tr>';
                        echo '<td>' . $row['nome'] . '</td>';
                        echo '<td>' . $row['imp_netto'] . '</td>';
                        echo '<td>' . $row['num_f'] . '</td>';
                        echo '<td>' .  $data_fattura . '</td>';
                        echo '<td>' . $data_scadenza_format . '</td>';
                        echo '<td><span class="text-danger">' . $differenza_formattata . '</span></td>';
                        echo '</tr>';
                    }
                    echo '</tbody>';
                    echo '</table>';
                } else {
                    echo '<tr><td colspan="6">Nessun risultato</td></tr>';
                }
            } catch (PDOException $e) {
                echo "Error : " . $e->getMessage();
            }
            break;
        case 'lista_scaduti_cliente':

            $cliente = $_POST['cliente'];
            $data_oggi = date_create(date('Y-m-d'));
            try {
                $query = "SELECT imp_netto,num_f,data_f, data_scadenza FROM `fatture` f WHERE f.id_cfic=:cliente AND status ='not_paid' AND data_scadenza < CURDATE() ORDER BY data_scadenza ASC";
                $stmt = $db->prepare($query);
                $stmt->bindParam(':cliente', $cliente, PDO::PARAM_STR);
                $stmt->execute();
                $dati   = $stmt->fetchAll();
                if (!empty($dati)) {
                    echo  '<table class="table table-striped mg-b-0 text-md-nowrap">';
                    echo '<thead>';
                    echo '<tr>';
                    echo '<th>Imponibile</th>';
                    echo '<th>N.fatt</th>';
                    echo '<th>Data</th>';
                    echo '<th>Scadenza</th>';
                    echo '<th>Giorni</th>';
                    echo '</tr>';
                    echo '</thead>';
                    echo '<tbody>';


                    foreach ($dati as $row) {

                        $data_fattura = date_create($row['data_f']);
                        $data_fattura = $data_fattura->format('d/m/Y');

                        $data_scadenza = isset($row['data_scadenza']) ? date_create($row['data_scadenza']) : null;
                        $data_scadenza_format = $data_scadenza ? $data_scadenza->format('d/m/Y') : null;

                        $data_oggi = new DateTime(); // Presumo che tu abbia già creato $data_oggi da qualche parte nel tuo codice

                        if ($data_scadenza) {
                            $differenza = $data_oggi->diff($data_scadenza);
                            // Ottieni la differenza formattata
                            $differenza_formattata = $differenza->format('%a giorni');
                        } else {
                            // Gestisci la situazione in cui la data di scadenza non è disponibile
                            $differenza_formattata = "Data di scadenza non disponibile";
                        }


                        echo '<tr>';

                        echo '<td>' . $row['imp_netto'] . '</td>';
                        echo '<td>' . $row['num_f'] . '</td>';
                        echo '<td>' .  $data_fattura . '</td>';
                        echo '<td>' . $data_scadenza_format . '</td>';
                        echo '<td><span class="text-danger">' . $differenza_formattata . '</span></td>';
                        echo '</tr>';
                    }
                    echo '</tbody>';
                    echo '</table>';
                } else {
                    echo '<tr><td colspan="6">Nessun risultato</td></tr>';
                }
            } catch (PDOException $e) {
                echo "Error : " . $e->getMessage();
            }
            break;
        case 'vedi_provincia':

            $pv = $_POST['pv'];
            $anno = $_POST['anno'];
            try {
                $query = "SELECT
                c.id_cfic,
                c.nome AS cliente,
                c.provincia,
                SUM(f.imp_netto) AS totale_imponibile_netto
            FROM
                clienti c
            JOIN
                fatture f ON c.id_cfic = f.id_cfic
            WHERE
                YEAR(f.data_f) = :anno
                AND c.provincia = :pv
            GROUP BY
                c.id_cfic, c.nome, c.provincia
            ORDER BY
                totale_imponibile_netto DESC;";
                $stmt = $db->prepare($query);
                $stmt->bindParam(':anno', $anno, PDO::PARAM_STR);
                $stmt->bindParam(':pv', $pv, PDO::PARAM_STR);
                $stmt->execute();
                $dati   = $stmt->fetchAll();
                if (!empty($dati)) {
                    echo  '<table class="table table-striped mg-b-0 text-md-nowrap">';
                    echo '<thead>';
                    echo '<tr>';
                    echo '<th>Cliente</th>';
                    echo '<th>Imponibile</th>';
                    echo '<th></th>';
                    echo '</tr>';
                    echo '</thead>';
                    echo '<tbody>';
                    foreach ($dati as $row) {
                        echo '<tr>';
                        echo '<td>' . $row['cliente'] . '</td>';
                        echo '<td> € ' . arrotondaEFormatta($row['totale_imponibile_netto']) . '</td>';
                        echo '<td><a href="analisi-clienti.php?c=' . $row['id_cfic'] . '" class="btn btn-info btn-icon me-2 btn-b"><i class="fe fe-eye"></i></a></td>';
                        echo '</tr>';
                    }
                    echo '</tbody>';
                    echo '</table>';
                } else {
                    echo '<tr><td colspan="6">Nessun risultato</td></tr>';
                }
            } catch (PDOException $e) {
                echo "Error : " . $e->getMessage();
            }
            break;

        default:
            # code...
            break;
    }
else :
    exit();
endif;
