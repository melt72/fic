<?php
if (!empty($_SERVER['HTTP_X_REQUESTED_WITH']) &&  strtolower($_SERVER['HTTP_X_REQUESTED_WITH']) == 'xmlhttprequest') {
    include 'functions.php';

    include(__DIR__ . '/../../include/configpdo.php');
    if (isset($_POST['tp'])) {
        $tp = $_POST['tp']; // Tipo di dati horeca, wineshop, all
    }
    $tipo = $_POST['tipo']; //tipo di query da eseguire

    if (isset($_POST['regioni'])) {
        $regioni = $_POST['regioni'];
        // Converti l'array delle regioni in una stringa per la query SQL
        $regions_list = implode("','", $regioni);
    } else {
        $regions_list = '';
    }

    //se ci sono dell province selezionate considerale altrimenti considera tutte le province
    if (isset($_POST['province'])) {
        $province = $_POST['province'];
        $province_list = implode("','", $province);
    } else {
        $province_list = '';
    }

    if (isset($_POST['paese'])) {
        $paese = $_POST['paese']; //all, italia, nonitalia, singolo paese
    }
    if ($tipo == '1') { // lista clienti
        $query_base = "SELECT * FROM `clienti` c ";

        switch ($paese) {
            case 'all': //tutti i paesi considero solo le date
                $query_base .= "";
                break;
            case 'Italia': //solo italia
                if ($regions_list == '') { //se non ci sono regioni selezionate
                    $query_base .= " JOIN province p ON c.provincia=p.pv  WHERE c.paese='Italia' ";
                } elseif ($province_list == '') { //se ci sono province selezionate
                    $query_base .= "JOIN province p ON c.provincia=p.pv WHERE c.paese='Italia' AND p.nome_regione in ('$regions_list') ";
                } else {
                    $query_base .= "JOIN province p ON c.provincia=p.pv WHERE c.paese='Italia' AND c.provincia in ('$province_list') ";
                }
                break;

            case   'Nitalia': //solo non italia  
                $query_base .= " WHERE c.paese !='Italia'";
                break;
            default: //singolo paese
                $query_base .= "  WHERE c.paese='$paese' ";
                break;
        }


        $query = $query_base . " ORDER BY nome ASC";
        $stmt = $db->prepare($query);
        $stmt->execute();
        $dati = $stmt->fetchAll();
        $dati_tabella = '<div class="row row-sm">
            <div class="col-sm-12 col-md-12 col-lg-12 col-xl-12">
                <div class="card">
                    <div class="card-header">
                        <h3 class="card-title">Lista Clienti</h3>
                    </div>
                    <div class="card-body">
                        <div class="table-responsive">
                            <table class="table table-bordered border text-nowrap key-buttons mb-0" id="basic-edittable-libera">
                                <thead>
                                    <tr>
                                        <th>Cliente</th>
                                        <th>Città</th>
                                        <th>Pv</th>
                                        <th>Regione</th>
                                        <th>Stato</th>
                                    </tr>
                                </thead>
                                <tbody id="dati_fatture">';

        foreach ($dati as $fattura) {
            $dati_tabella .= '<tr>
                                            <td>' . strtoupper($fattura['nome']) . '</td>
                                            <td>' . ucfirst(strtolower($fattura['citta'])) . '</td>
                                            <td>' . $fattura['provincia'] . '</td>
                                            <td>';
            if (isset($fattura['nome_regione'])) {
                $dati_tabella .=  $fattura['nome_regione'];
            }
            $dati_tabella .=
                '</td>
                                            <td>' . $fattura['paese']  . '</td>
                                        </tr>';
        }
        $dati_tabella .= '
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    ';

        //creo un array con i dati da inviare
        $datijson = array(
            'totali' => '',
            'dati_tabella' => $dati_tabella
        );

        echo json_encode($datijson);
    }

    if ($tipo == '2') {
        $anno = $_POST['anno'];
        $s = date('Y-m-d', strtotime($_POST['s']));
        $e = date('Y-m-d', strtotime($_POST['e']));

        $query_base = "SELECT 
    SUM(f.imp_netto) AS totale_importo_netto,
    SUM(CASE WHEN f.status = 'paid' THEN f.imp_netto ELSE 0 END) AS totale_importo_pagato,
    SUM(CASE WHEN f.status = 'not_paid' AND f.data_scadenza < CURDATE() THEN f.imp_netto ELSE 0 END) AS totale_importo_scaduto,
    SUM(CASE WHEN f.status = 'not_paid' THEN f.imp_netto ELSE 0 END) AS totale_importo_non_pagato,


   COUNT(f.id_ffic) AS numero_fatture_totali,
    COUNT(DISTINCT c.id_cfic) AS numero_clienti_totali,
    SUM(CASE WHEN a.id IS NOT NULL THEN f.imp_netto ELSE 0 END) AS totale_imponibile_agenti,
    SUM(CASE WHEN a.id IS NULL THEN f.imp_netto ELSE 0 END) AS totale_imponibile_non_agenti
FROM 
    fatture f
JOIN 
    clienti c ON f.id_cfic = c.id_cfic
LEFT JOIN 
    agenti a ON f.sigla = a.sigla";

        switch ($paese) {
            case 'all': //tutti i paesi considero solo le date
                $query_base .= " JOIN province p ON c.provincia=p.pv WHERE (f.data_f BETWEEN '$s' AND '$e') ";
                break;
            case 'Italia': //solo italia
                if ($regions_list == '') { //se non ci sono regioni selezionate
                    $query_base .= " JOIN province p ON c.provincia=p.pv  WHERE c.paese='Italia' AND (f.data_f BETWEEN '$s' AND '$e') ";
                } elseif ($province_list == '') { //se ci sono province selezionate
                    $query_base .= " JOIN province p ON c.provincia=p.pv  WHERE c.paese='Italia' AND p.nome_regione in ('$regions_list') AND (f.data_f BETWEEN '$s' AND '$e')  ";
                } else {
                    $query_base .= " JOIN province p ON c.provincia=p.pv  WHERE c.paese='Italia' AND c.provincia in ('$province_list') AND (f.data_f BETWEEN '$s' AND '$e') ";
                }
                break;

            case   'Nitalia': //solo non italia  
                $query_base .= " JOIN province p ON c.provincia=p.pv WHERE c.paese !='Italia' AND (f.data_f BETWEEN '$s' AND '$e')  ";
                break;
            default: //singolo paese
                $query_base .= " JOIN province p ON c.provincia=p.pv WHERE c.paese='$paese' AND (f.data_f BETWEEN '$s' AND '$e') ";
                break;
        }

        switch ($tp) {
            case 'horeca':

                $query_base .= " AND f.sigla!='WINE SHOP'";
                break;
            case 'wineshop':
                $query_base .= " AND f.sigla='WINE SHOP'";
                break;
            default:
                $query_base .= " ";
                break;
        }


        $query = $query_base . " AND f.ie='1' AND f.status_invio='sent'";
        $stmt = $db->prepare($query);
        $stmt->execute();
        $dati = $stmt->fetchAll();
        if ($dati[0]['totale_importo_netto'] > 0) {
            // Calcolo delle percentuali
            $percentale_agente = ($dati[0]['totale_imponibile_agenti'] * 100) / $dati[0]['totale_importo_netto'];
            $percentale_non_agente = ($dati[0]['totale_imponibile_non_agenti'] * 100) / $dati[0]['totale_importo_netto'];

            // Arrotondamento delle percentuali a 2 decimali
            $percentale_agente_arrotondato = round($percentale_agente, 2);
            $percentale_non_agente_arrotondato = round($percentale_non_agente, 2);
        } else {
            // Gestione caso di divisione per zero
            $percentale_agente_arrotondato = 0;
            $percentale_non_agente_arrotondato = 0;
        }

        $dati_tabella = '<div class="row row-sm">
            <div class="col-xl-3 col-lg-6 col-md-6 col-xm-12">
                <div class="card overflow-hidden sales-card bg-primary-gradient">
                    <div class="px-3 pt-3  pb-2 pt-0">
                        <div class="">
                            <h6 class="mb-3 tx-12 text-white">IMPONIBILE TOTALE</h6>
                        </div>
                        <div class="pb-0 mt-0">
                            <div class="d-flex">
                                <div class="">
                                    <h4 class="tx-20 fw-bold mb-1 text-white">€ ' . arrotondaEFormatta($dati[0]['totale_importo_netto']) . '</h4>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            <div class="col-xl-3 col-lg-6 col-md-6 col-xm-12">
                <div class="card overflow-hidden sales-card bg-success-gradient">
                    <div class="px-3 pt-3  pb-2 pt-0">
                        <div class="">
                            <h6 class="mb-3 tx-12 text-white">Imponibile incassato</h6>
                        </div>
                        <div class="pb-0 mt-0">
                            <div class="d-flex">
                                <div class="">
                                    <h4 class="tx-20 fw-bold mb-1 text-white">€ ' . arrotondaEFormatta($dati[0]['totale_importo_pagato']) . '</h4>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            <div class="col-xl-3 col-lg-6 col-md-6 col-xm-12">
                <div class="card overflow-hidden sales-card bg-warning-gradient">
                    <div class="px-3 pt-3  pb-2 pt-0">
                        <div class="">
                            <h6 class="mb-3 tx-12 text-white">Imponibile da incassare</h6>
                        </div>
                        <div class="pb-0 mt-0">
                            <div class="d-flex">
                                <div class="">
                                    <h4 class="tx-20 fw-bold mb-1 text-white">€ ' . arrotondaEFormatta($dati[0]['totale_importo_non_pagato']) . '</h4>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            <div class="col-xl-3 col-lg-6 col-md-6 col-xm-12">
                <div class="card overflow-hidden sales-card bg-danger-gradient">
                    <div class="px-3 pt-3  pb-2 pt-0">
                        <div class="">
                            <h6 class="mb-3 tx-12 text-white">Imponibile scaduto</h6>
                        </div>
                        <div class="pb-0 mt-0">
                            <div class="d-flex">
                                <div class="">
                                    <h4 class="tx-20 fw-bold mb-1 text-white">€ ' . arrotondaEFormatta($dati[0]['totale_importo_scaduto']) . '</h4>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <div class="col-xl-3 col-lg-6 col-md-6 col-xm-12">
                <div class="card overflow-hidden sales-card bg-secondary">
                    <div class="px-3 pt-3  pb-2 pt-0">
                        <div class="">
                            <h6 class="mb-3 tx-12 text-white">Imponibile da agente</h6>
                        </div>
                        <div class="pb-0 mt-0">
                            <div class="d-flex">
                                <div class="">
                                    <h4 class="tx-20 fw-bold mb-1 text-white">' . arrotondaEFormatta($dati[0]['totale_imponibile_agenti'])  . '</h4>
                                </div>
                                <span class="float-end my-auto ms-auto">
												<span class="text-white op-7"> ' . $percentale_agente_arrotondato . ' %</span>
											</span>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            <div class="col-xl-3 col-lg-6 col-md-6 col-xm-12">
                <div class="card overflow-hidden sales-card bg-purple">
                    <div class="px-3 pt-3  pb-2 pt-0">
                        <div class="">
                            <h6 class="mb-3 tx-12 text-white">Imponibile non agente</h6>
                        </div>
                        <div class="pb-0 mt-0">
                            <div class="d-flex">
                                <div class="">
                                    <h4 class="tx-20 fw-bold mb-1 text-white">' . arrotondaEFormatta($dati[0]['totale_imponibile_non_agenti'])  . '</h4>
                                </div>
                                  <span class="float-end my-auto ms-auto">
												<span class="text-white op-7"> ' . $percentale_non_agente_arrotondato . ' %</span>
											</span>
                            </div>
                        </div>
                    </div>
                </div>
            </div>    </div>
        <div class="row row-sm">        
            <div class="col-xl-3 col-lg-6 col-md-6 col-xm-12">
                <div class="card overflow-hidden sales-card bg-primary-gradient">
                    <div class="px-3 pt-3  pb-2 pt-0">
                        <div class="">
                            <h6 class="mb-3 tx-12 text-white">N° Fatture</h6>
                        </div>
                        <div class="pb-0 mt-0">
                            <div class="d-flex">
                                <div class="">
                                    <h4 class="tx-20 fw-bold mb-1 text-white">' . $dati[0]['numero_fatture_totali']  . '</h4>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            <div class="col-xl-3 col-lg-6 col-md-6 col-xm-12">
                <div class="card overflow-hidden sales-card bg-primary-gradient">
                    <div class="px-3 pt-3  pb-2 pt-0">
                        <div class="">
                            <h6 class="mb-3 tx-12 text-white">N° Clienti</h6>
                        </div>
                        <div class="pb-0 mt-0">
                            <div class="d-flex">
                                <div class="">
                                    <h4 class="tx-20 fw-bold mb-1 text-white">' . $dati[0]['numero_clienti_totali'] . '</h4>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        ';
        $totali = $dati[0];
        // calolo le bottiglie vendute
        $query1_base = "SELECT
    SUM(pr.qta) AS totale_bottiglie
    FROM
    fatture f
    JOIN 
    clienti c ON f.id_cfic = c.id_cfic
    JOIN 
    prodotti pr ON f.id_ffic = pr.id_ffic
    JOIN 
    lista_prodotti lp ON pr.id_prod = lp.prod_id
";
        switch ($paese) {
            case 'all': //tutti i paesi considero solo le date
                $query1_base .= " JOIN province p ON c.provincia=p.pv WHERE (f.data_f BETWEEN '$s' AND '$e') ";
                break;
            case 'Italia': //solo italia
                if ($regions_list == '') { //se non ci sono regioni selezionate
                    $query1_base .= " JOIN province p ON c.provincia=p.pv  WHERE c.paese='Italia' AND (f.data_f BETWEEN '$s' AND '$e') ";
                } elseif ($province_list == '') { //se ci sono province selezionate
                    $query1_base .= " JOIN province p ON c.provincia=p.pv  WHERE c.paese='Italia' AND p.nome_regione in ('$regions_list') AND (f.data_f BETWEEN '$s' AND '$e')  ";
                } else {
                    $query1_base .= " JOIN province p ON c.provincia=p.pv  WHERE c.paese='Italia' AND c.provincia in ('$province_list') AND (f.data_f BETWEEN '$s' AND '$e') ";
                }
                break;

            case   'Nitalia': //solo non italia  
                $query1_base .= " JOIN province p ON c.provincia=p.pv WHERE c.paese !='Italia' AND (f.data_f BETWEEN '$s' AND '$e')  ";
                break;
            default: //singolo paese
                $query1_base .= " JOIN province p ON c.provincia=p.pv WHERE c.paese='$paese' AND (f.data_f BETWEEN '$s' AND '$e') ";
                break;
        }
        switch ($tp) {
            case 'horeca':
                $query1_base .= " AND f.sigla!='WINE SHOP'";
                break;
            case 'wineshop':
                $query1_base .= " AND f.sigla='WINE SHOP'";
                break;
            default:
                $query1_base .= " ";
                break;
        }
        $query = $query1_base . " AND f.ie='1'  AND f.status_invio='sent'";
        $stmt = $db->prepare($query);
        $stmt->execute();
        $dati = $stmt->fetchAll();
        $dati_tabella .= '<div class="col-xl-3 col-lg-6 col-md-6 col-xm-12">
                <div class="card overflow-hidden sales-card bg-primary-gradient">
                    <div class="px-3 pt-3  pb-2 pt-0">
                        <div class="">
                            <h6 class="mb-3 tx-12 text-white">Bottiglie</h6>
                        </div>
                        <div class="pb-0 mt-0">
                            <div class="d-flex">
                                <div class="">
                                    <h4 class="tx-20 fw-bold mb-1 text-white">' . $dati[0]['totale_bottiglie'] . ' </h4>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div></div>';

        $totali += $dati[0];


        $query_base = "SELECT 
       
    f.id_ffic, f.num_f, f.imp_netto, f.imp_iva, f.imp_tot, f.status, 
    f.data_f, f.data_scadenza, f.data_pagamento,
    c.nome AS cliente_nome, c.paese, p.nome_provincia AS provincia_nome,
    a.nome_agente AS agente_nome,
    CASE 
        WHEN f.status = 'not_paid' AND f.data_scadenza < CURDATE() THEN 'Scaduta'
        WHEN f.status = 'not_paid' AND f.data_scadenza >= CURDATE() THEN 'Non pagata'
        WHEN f.status = 'paid' THEN 'Incassata'
        ELSE 'Non definita'
    END AS stato_fattura,
    CASE 
        WHEN f.status = 'not_paid' AND f.data_scadenza < CURDATE() THEN DATEDIFF(CURDATE(), f.data_scadenza)
        ELSE NULL 
    END AS giorni_dalla_scadenza

     FROM `fatture` f JOIN clienti c ON f.id_cfic=c.id_cfic 
     LEFT JOIN 
    agenti a ON f.sigla = a.sigla 
    ";


        switch ($paese) {
            case 'all': //tutti i paesi considero solo le date
                $query_base .= " JOIN province p ON c.provincia=p.pv WHERE (f.data_f BETWEEN '$s' AND '$e') ";
                break;
            case 'Italia': //solo italia
                if ($regions_list == '') { //se non ci sono regioni selezionate
                    $query_base .= " JOIN province p ON c.provincia=p.pv  WHERE c.paese='Italia' AND (f.data_f BETWEEN '$s' AND '$e') ";
                } elseif ($province_list == '') { //se ci sono province selezionate
                    $query_base .= " JOIN province p ON c.provincia=p.pv  WHERE c.paese='Italia' AND p.nome_regione in ('$regions_list') AND (f.data_f BETWEEN '$s' AND '$e')  ";
                } else {
                    $query_base .= " JOIN province p ON c.provincia=p.pv  WHERE c.paese='Italia' AND c.provincia in ('$province_list') AND (f.data_f BETWEEN '$s' AND '$e') ";
                }
                break;

            case   'Nitalia': //solo non italia  
                $query_base .= " JOIN province p ON c.provincia=p.pv WHERE c.paese !='Italia' AND (f.data_f BETWEEN '$s' AND '$e')  ";
                break;
            default: //singolo paese
                $query_base .= " JOIN province p ON c.provincia=p.pv WHERE c.paese='$paese' AND (f.data_f BETWEEN '$s' AND '$e') ";
                break;
        }
        switch ($tp) {
            case 'horeca':
                $query_base .= " AND f.sigla!='WINE SHOP'";
                break;
            case 'wineshop':
                $query_base .= " AND f.sigla='WINE SHOP'";
                break;
            default:
                $query_base .= " ";
                break;
        }

        $query = $query_base . " AND f.ie='1' AND f.status_invio='sent' GROUP BY f.id_ffic ORDER BY f.data_f DESC";

        $stmt = $db->prepare($query);
        $stmt->execute();
        $dati = $stmt->fetchAll();
        $dati_tabella .= '
        <div class="row row-sm">
            <div class="col-sm-12 col-md-12 col-lg-12 col-xl-12">
                <div class="card">
                    <div class="card-header">
                        <h3 class="card-title">Fatture</h3>
                    </div>
                    <div class="card-body">
                        <div class="table-responsive">
                            <table class="table table-bordered border text-nowrap mb-0" id="basic-edittable-libera">
                                <thead>
                                    <tr>
                                        <th>Cliente</th>
                                        <th>n.fatt</th>
                                        <th>data</th>
                                        <th>gg scad</th>
                                        <th>importo</th>
                                        <th>imponibile</th>
                                        <th>iva</th>
                                        <th class="dt-filter">Stato</th>
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
                                            <td>';
            if (($fattura['data_scadenza'] < $oggi) && ($fattura['stato_fattura'] != 'paid')) {
                $dati_tabella .= '<span class="text-danger">' . $fattura['giorni_dalla_scadenza'] . '</span>';
            }
            $dati_tabella .= '</td>
                                            <td>' . arrotondaEFormatta($fattura['imp_tot']) . ' €</td>
                                            <td>' . arrotondaEFormatta($fattura['imp_netto']) . ' €</td>
                                            <td>' . arrotondaEFormatta($fattura['imp_iva'])  . ' €</td>
                                            <td>';
            switch ($fattura['stato_fattura']) {
                case 'Scaduta':
                    $dati_tabella .= '<span class="badge bg-danger">' . $fattura['stato_fattura'] . '</span>';
                    break;
                case 'Non pagata':
                    $dati_tabella .= '<span class="badge bg-warning">Non scaduta <br> non incassata</span>';
                    break;
                case 'Incassata':
                    $dati_tabella .= '<span class="badge bg-success">' . $fattura['stato_fattura'] . '</span>';
                    break;

                default:
                    # code...
                    break;
            }


            $dati_tabella .= '</td>
                                            <td>' . $fattura['agente_nome'] . '</td>
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

        //creo un array con i dati da inviare
        $datijson = array(
            'totali' => $totali,
            'dati_tabella' => $dati_tabella
        );

        echo json_encode($datijson);
    }

    if ($tipo == '3') {
        $anno = $_POST['anno'];
        $s = date('Y-m-d', strtotime($_POST['s']));
        $e = date('Y-m-d', strtotime($_POST['e']));
        $varieta = $_POST['varieta'];
        if (isset($_POST['vini'])) {
            $vini = $_POST['vini'];
            $vini_list = implode("','", $vini);
        } else {
            $vini_list = '';
        }




        $query_base = "SELECT SUM(qta) AS totale_qta, 
          SUM(CASE WHEN c.paese = 'Italia' THEN pr.qta ELSE 0 END) AS totale_qta_italia,
    SUM(CASE WHEN c.paese != 'Italia' THEN pr.qta ELSE 0 END) AS totale_qta_estero
         FROM 
            prodotti pr
         JOIN 
            fatture f ON pr.id_ffic=f.id_ffic
         JOIN 
            clienti c ON f.id_cfic = c.id_cfic
        WHERE 
            id_prod  in ('$vini_list')";

        switch ($tp) {
            case 'horeca':
                $query_base .= " AND f.sigla!='WINE SHOP'";
                break;
            case 'wineshop':
                $query_base .= " AND f.sigla='WINE SHOP'";
                break;
            default:
                $query_base .= " ";
                break;
        }

        $query = $query_base . " AND pr.data_f BETWEEN '$s' AND '$e'";

        try {
            $stmt = $db->prepare($query);
            $stmt->execute();
            $row   = $stmt->fetch(PDO::FETCH_ASSOC);
            $totale_qta = $row['totale_qta'];
        } catch (PDOException $e) {
            echo "Error : " . $e->getMessage();
        }

        $dati_tabella = '<div class="row row-sm">
        <div class="col-xl-3 col-lg-6 col-md-6 col-xm-12">
            <div class="card overflow-hidden sales-card bg-primary-gradient">
                <div class="px-3 pt-3  pb-2 pt-0">
                    <div class="">
                        <h6 class="mb-3 tx-12 text-white">Bottiglie Totali Vendute</h6>
                    </div>
                    <div class="pb-0 mt-0">
                        <div class="d-flex">
                            <div class="">
                                <h4 class="tx-20 fw-bold mb-1 text-white">' . $totale_qta . ' bt</h4>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
         <div class="col-xl-3 col-lg-6 col-md-6 col-xm-12">
            <div class="card overflow-hidden sales-card bg-success-gradient">
                <div class="px-3 pt-3  pb-2 pt-0">
                    <div class="">
                        <h6 class="mb-3 tx-12 text-white">Bottiglie Italia</h6>
                    </div>
                    <div class="pb-0 mt-0">
                        <div class="d-flex">
                            <div class="">
                                <h4 class="tx-20 fw-bold mb-1 text-white">' . $row['totale_qta_italia'] . ' bt</h4>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
         <div class="col-xl-3 col-lg-6 col-md-6 col-xm-12">
            <div class="card overflow-hidden sales-card bg-success-gradient">
                <div class="px-3 pt-3  pb-2 pt-0">
                    <div class="">
                        <h6 class="mb-3 tx-12 text-white">Bottiglie Estero</h6>
                    </div>
                    <div class="pb-0 mt-0">
                        <div class="d-flex">
                            <div class="">
                                <h4 class="tx-20 fw-bold mb-1 text-white">' . $row['totale_qta_estero'] . ' bt</h4>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        </div>';
        // creo un array con i dati da inviare
        $totale = array('totale_qta' => $totale_qta, 'totale_qta_italia' => $row['totale_qta_italia'], 'totale_qta_estero' => $row['totale_qta_estero']);


        $query_base = "SELECT SUM(qta) AS totale_qta, c.nome, f.num_f, f.data_f, c.paese, prov.nome_regione
         FROM 
            prodotti pr
         JOIN 
            fatture f ON pr.id_ffic=f.id_ffic
         JOIN 
            clienti c ON f.id_cfic = c.id_cfic
         JOIN 
            province prov ON prov.pv=c.provincia
        WHERE 
            pr.id_prod  in ('$vini_list')";

        switch ($tp) {
            case 'horeca':
                $query_base .= " AND f.sigla!='WINE SHOP'";
                break;
            case 'wineshop':
                $query_base .= " AND f.sigla='WINE SHOP'";
                break;
            default:
                $query_base .= " ";
                break;
        }

        $query = $query_base . " AND pr.data_f BETWEEN '$s' AND '$e' GROUP BY f.id_ffic";

        try {
            $stmt = $db->prepare($query);
            $stmt->execute();
            $dati = $stmt->fetchAll();
        } catch (PDOException $e) {
            echo "Error : " . $e->getMessage();
        }



        $dati_tabella .= '
        <div class="row row-sm">
            <div class="col-sm-12 col-md-12 col-lg-12 col-xl-12">
                <div class="card">
                    <div class="card-header">
                        <h3 class="card-title">Fatture</h3>
                    </div>
                    <div class="card-body">
                        <div class="table-responsive">
                            <table class="table table-bordered border text-nowrap mb-0" id="basic-edittable-libera">
                                <thead>
                                    <tr>
                                        <th>Cliente</th>
                                        <th>n.fatt</th>
                                        <th>data</th>
                                        <th>N bott</th>
                                        <th class="dt-filter2">Regione</th>
                                        <th class="dt-filter">Paese</th>
                                    </tr>
                                </thead>
                                <tbody id="dati_fatture">';
        $oggi = date('Y-m-d');
        foreach ($dati as $fattura) {
            $dati_tabella .= '<tr><td>' . $fattura['nome'] . '</td> <td>' . $fattura['num_f'] . '</td> <td>Data: ' . date('d/m/Y', strtotime($fattura['data_f'])) . ' </td> <td>' . ($fattura['totale_qta']) . ' bt</td> <td>' . ($fattura['nome_regione']) . '</td> <td>' . ($fattura['paese']) . '</td> </tr>';
        }

        $dati_tabella .= '
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>
            </div>
        </div>';


        $datijson = array(
            'totali' => $totale,
            'dati_tabella' =>  $dati_tabella
        );
        echo json_encode($datijson);
    }

    if ($tipo == '4') {
        // $anno = $_POST['anno'];
        $mese_form = $_POST['mese'];
        list($mese, $anno) = explode('/', $mese_form);

        $varietaArray = array('cabernet', 'filorosso', 'pinot nero', 'refosco', 'chardonnay', 'friulano', 'malvasia', 'pinot grigio', 'ribolla', 'sauvignon');

        $dati_tabella = '<div class="row row-sm">
            <div class="col-sm-12 col-md-12 col-lg-12 col-xl-12">
                <div class="card">
                    <div class="card-header">
                        <h3 class="card-title">Fatture</h3>
                    </div>
                    <div class="card-body">
                        <div class="table-responsive">
                            <table class="table table-bordered border text-nowrap mb-0" id="basic-edittable-libera">
                                <thead>
                                    <tr>
                                        <th>Prodotto</th>
                                        <th>Parziale</th>
                                        <th>Totale</th>
                                    </tr>
                                </thead>
                                <tbody id="dati_fatture">';

        foreach ($varietaArray as $varieta) {
            $query_base = "SELECT lp.varieta, lp.nome_prodotto, lp.cod_prod, SUM(p.qta) AS totale_qta
        FROM prodotti p
        JOIN lista_prodotti lp ON p.id_prod = lp.prod_id
        WHERE lp.varieta = '$varieta'  -- Sostituisci con la varietà che ti interessa
          AND  MONTH(p.data_f) = '$mese' AND YEAR(p.data_f) = '$anno'
        GROUP BY lp.varieta, lp.nome_prodotto";
            $stmt = $db->prepare($query_base);
            $stmt->execute();
            $dati = $stmt->fetchAll();
            $totale = 0;

            if (!empty($dati)) {
                $dati_tabella .= '<tr>
                                                <td>' . strtoupper($varieta) . '</td>
                                                <td></td>
                                                <td></td>
                                            </tr>';
                foreach ($dati as $fattura) {
                    $totale += $fattura['totale_qta'];
                    $dati_tabella .= '<tr>

                                                <td>' . $fattura['cod_prod'] . ' - ' . $fattura['nome_prodotto'] . '</td>
                                                <td>' . $fattura['totale_qta'] . '</td>
                                                <td></td>
                                            </tr>';
                }
                $dati_tabella .= '<tr>
                                                <td></td>
                                                <td></td>
                                                <td>' . $totale . '</td>
                                            </tr>
                                            <tr>
                                                <td><hr></td>
                                                <td><hr></td>
                                                <td><hr></td>
                                            </tr>';
            }
        }
        $dati_tabella .= '
        </tbody>
    </table>
</div>
</div>
</div>
</div>
</div>';


        $datijson = array(
            'totali' => $totale,
            'dati_tabella' =>  $dati_tabella
        );
        echo json_encode($datijson);
    }
} else {
    exit();
}
