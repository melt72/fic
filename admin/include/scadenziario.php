<?php
if (!empty($_SERVER['HTTP_X_REQUESTED_WITH']) &&  strtolower($_SERVER['HTTP_X_REQUESTED_WITH']) == 'xmlhttprequest'):
    include 'functions.php';

    include(__DIR__ . '/../../include/configpdo.php');
    $start = $_POST['s'];
    $date = DateTime::createFromFormat('d-m-Y', $start);
    $start = $date->format('Y-m-d');
    $end = $_POST['e'];
    $date = DateTime::createFromFormat('d-m-Y', $end);
    $end = $date->format('Y-m-d');
    $id_agente = $_POST['agente'];
    $anno = $_POST['anno'];

    //ricavo i dati dell'agente
    $query = "SELECT * FROM agenti WHERE id = :id";
    $stmt = $db->prepare($query);
    $stmt->bindParam('id', $id_agente, PDO::PARAM_STR);
    $stmt->execute();
    $row   = $stmt->fetch(PDO::FETCH_ASSOC);
    $nome_agente = $row['nome_agente'];
    $sigla_agente = $row['sigla'];

    switch ($id_agente) {
        case '1': // RISACA

            $query = "SELECT  f.num_f, f.imp_netto, f.imp_iva, f.imp_tot, f.data_f, f.data_scadenza, c.nome  AS cliente_nome, z.nome_zona  
              
FROM `fatture` f 
INNER JOIN clienti c ON f.id_cfic = c.id_cfic 
INNER JOIN agenti_roma ag ON ag.id_cfic=c.id_cfic
INNER JOIN zone_roma z ON z.id_zona=ag.id_zona
WHERE f.sigla = :sigla 
   AND  f.ie='1'
AND f.status_invio = 'sent' 
AND f.status = 'not_paid' 
AND YEAR(f.data_f) = $anno 
AND f.data_scadenza BETWEEN '$start' AND '$end'";
            $stmt = $db->prepare($query);
            $stmt->bindParam('sigla', $sigla_agente, PDO::PARAM_STR);
            $stmt->execute();
            $dati = $stmt->fetchAll(PDO::FETCH_ASSOC);


            $dati_tabella = '
            <div class="row row-sm">
                <div class="col-sm-12 col-md-12 col-lg-12 col-xl-12">
                    <div class="card">
                        <div class="card-header">
                            <h3 class="card-title">Fatture</h3>
                        </div>
                        <div class="card-body">
                          <a href="scadrisacaxls.php?anno=' . $anno . '&s=' . $start . '&e=' . $end . '" class="btn btn-primary mb-3" target="_blank">Stampa XLS</a>
                            <div class="table-responsive">
                                <table class="table table-bordered border text-nowrap mb-0" id="basic-edittable-libera">
                                    <thead>
                                        <tr>
                                            <th>Cliente</th>
                                            <th>n.fatt</th>
                                            <th>data</th>
                                          
                                            <th>importo</th>
                                            <th>Agente</th>
                                        </tr>
                                    </thead>
                                    <tbody id="dati_fatture">';
            $oggi = date('Y-m-d');
            foreach ($dati as $fattura) {
                $dati_tabella .= '<tr>
                                                <td>' . $fattura['cliente_nome'] . '</td>
                                                <td>' . $fattura['num_f'] . '</td>
                                                <td>Data: ' . date('d/m/Y', strtotime($fattura['data_f'])) . '<br>Scad: ' . date('d/m/Y', strtotime($fattura['data_scadenza'])) . ' </td>
                                                ';
                $dati_tabella .= '<td>' . arrotondaEFormatta($fattura['imp_tot']) . ' €</td>';



                $dati_tabella .= '<td>' . $fattura['nome_zona'] . '</td>
                                            </tr>';
            }

            $dati_tabella .= '
                                    </tbody>
                                </table>
                            </div>
                        </div>
                    </div>
                </div>
            </div>';
            break;

        default: // ALTRI AGENTI
            $query = "SELECT  f.num_f, f.imp_netto, f.imp_iva, f.imp_tot, f.data_f, f.data_scadenza, c.nome  AS cliente_nome  
              
        FROM `fatture` f 
        INNER JOIN clienti c ON f.id_cfic = c.id_cfic 
        WHERE f.sigla = :sigla 
           AND  f.ie='1'
        AND f.status_invio = 'sent' 
        AND f.status = 'not_paid' 
        AND YEAR(f.data_f) = $anno 
        AND f.data_scadenza BETWEEN '$start' AND '$end'";
            $stmt = $db->prepare($query);
            $stmt->bindParam('sigla', $sigla_agente, PDO::PARAM_STR);
            $stmt->execute();
            $dati = $stmt->fetchAll(PDO::FETCH_ASSOC);


            $dati_tabella = '
                    <div class="row row-sm">
                        <div class="col-sm-12 col-md-12 col-lg-12 col-xl-12">
                            <div class="card">
                                <div class="card-header">
                                    <h3 class="card-title">Fatture</h3>
                                </div>
                                <div class="card-body">
                                  <a href="scadagentexls.php?agente=' . $sigla_agente . '&anno=' . $anno . '&s=' . $start . '&e=' . $end . '" class="btn btn-primary mb-3" target="_blank">Stampa XLS</a>
                                    <div class="table-responsive">
                                        <table class="table table-bordered border text-nowrap mb-0" id="basic-edittable-libera">
                                            <thead>
                                                <tr>
                                                    <th>Cliente</th>
                                                    <th>n.fatt</th>
                                                    <th>data</th>
                                                  
                                                    <th>importo</th>
                                                   
                                                </tr>
                                            </thead>
                                            <tbody id="dati_fatture">';
            $oggi = date('Y-m-d');
            foreach ($dati as $fattura) {
                $dati_tabella .= '<tr>
                                                        <td>' . $fattura['cliente_nome'] . '</td>
                                                        <td>' . $fattura['num_f'] . '</td>
                                                        <td>Data: ' . date('d/m/Y', strtotime($fattura['data_f'])) . '<br>Scad: ' . date('d/m/Y', strtotime($fattura['data_scadenza'])) . ' </td>
                                                        ';
                $dati_tabella .= '<td>' . arrotondaEFormatta($fattura['imp_tot']) . ' €</td></tr>';
            }

            $dati_tabella .= '
                                            </tbody>
                                        </table>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>';
            break;
    }


    echo $dati_tabella;
//echo json_encode($dati_tabella);
//echo json_encode($dati);
//echo json_encode($fatture
else :
    exit();
endif;
