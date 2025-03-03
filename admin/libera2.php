<?php
include 'partials/headerarea.php';
include 'partials/header.php';
if (isset($_GET['tipo'])) { //tipo di richiesta (1=lista clienti, 2=imponibili, 3=bottiglie)
    $tipo = $_GET['tipo'];
} else {
    $tipo = 1;
}
if ($tipo == 1) {
    $anno = 'all';
} else if ($tipo == 2) {
    if (isset($_GET['a'])) {
        $anno = $_GET['a'];
    } else {
        $anno = date('Y');
    }

    if (isset($_GET['tp'])) {
        $tp = $_GET['tp'];
    } else {
        $tp = 'horeca'; //tipo di cliente (horeca, wineshop, all)
    }
} else if ($tipo == 3) {
    if (isset($_GET['a'])) {
        $anno = $_GET['a'];
    } else {
        $anno = date('Y') - 1;
    }

    if (isset($_GET['tp'])) {
        $tp = $_GET['tp'];
    } else {
        $tp = 'horeca'; //tipo di cliente (horeca, wineshop, all)
    }
    if (isset($_GET['v'])) {
        $v = $_GET['v'];
    } else {
        $v = 'cabernet'; //varietà di vino
    }
} else if ($tipo == 4) {
    if (isset($_GET['a'])) {
        $anno = $_GET['a'];
    } else {
        $anno = date('Y');
    }
}
$anno_riferimento = date('Y');
if (isset($_GET['di'])) {
    $data_iniziale = $_GET['di'];
} else {
    $data_iniziale = date('d-m-Y', strtotime('first day of January ' . date('Y')));
}

$mesecorrente = date('m') . '/' . date('Y');
if (isset($_GET['df'])) {
    $data_finale = $_GET['df'];
} else {
    //se l'anno è l'anno corrente allora la data finale è la data corrente
    if (($tipo == 2) || ($tipo == 4)) {
        if ($anno_riferimento == date('Y')) {
            $data_finale = date('d-m-Y');
        } else {
            $data_finale = date('d-m-Y', strtotime('last day of December ' . $anno));
        }
    } else {
        if ($anno_riferimento == date('Y')) {
            $data_finale = date('d-m-Y');
        } else {
            $data_finale = date('d-m-Y', strtotime('last day of December ' . $anno_riferimento));
        }
    }
}

//paese
if (isset($_GET['p'])) {
    $p = $_GET['p'];
} else {
    $p = 'all';
}

//regioni
if ($p == 'Italia') {
    if (isset($_GET['r'])) {
        $r = $_GET['r'];
    } else {
        $r = 'all';
    }
} else {
    $r = 'all';
}

?>

<body class="main-body app sidebar-mini ltr">

    <!-- Loader -->
    <div id="global-loader">
        <img src="assets/img/svgicons/loader.svg" class="loader-img" alt="Loader">
    </div>
    <!-- /Loader -->

    <!-- Page -->
    <div class="page custom-index">
        <div>
            <!-- main-header -->
            <?php
            include 'partials/navbar.php';
            ?>
            <!-- /main-header -->

            <!-- main-sidebar -->
            <?php
            include 'partials/sidebar.php';
            ?>
            <!-- main-sidebar -->
        </div>

        <!-- main-content -->
        <div class="main-content app-content">

            <!-- container -->
            <div class="main-container container-fluid">

                <!-- breadcrumb -->
                <div class="breadcrumb-header justify-content-between">
                    <div class="left-content">
                        <div>
                            <h2 class="main-content-title tx-24 mg-b-1 mg-b-lg-1">Analisi Clienti</h2>
                        </div>
                    </div>
                    <div class="d-flex my-xl-auto right-content align-items-center">


                    </div>
                </div>
                <!-- row closed -->

                <div class="row row-sm">
                    <div class="col-sm-12 col-md-12">
                        <div class="card ">
                            <div class="card-body">
                                <div class="row">
                                    <div class="col-md-4">
                                        <div>Tipo di dati</div>
                                        <div>
                                            <select class="form-control" id="tipo">
                                                <option value="1" <?= $tipo == 1 ? ' selected' : '' ?>>Lista clienti</option>
                                                <option value="2" <?= $tipo == 2 ? ' selected' : '' ?>>Imponibili</option>
                                                <option value="3" <?= $tipo == 3 ? ' selected' : '' ?>>Bottiglie</option>
                                                <option value="4" <?= $tipo == 4 ? ' selected' : '' ?>>Bottiglie (riepilogo)</option>
                                            </select>
                                        </div>

                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="row row-sm">
                    <div class="col-md-12">
                        <div class="card">
                            <div class="card-body">
                                <?php if ($tipo == 2) { ?>
                                    <!-- anno e periodo -->
                                    <div class="row">
                                        <div class="col-md-2">
                                            <?php
                                            $anni_disponibili = getAnniFatture();
                                            ?>
                                            <div>Anno</div>
                                            <div>
                                                <select class="form-control  testselect2" id="anno">
                                                    <?php
                                                    foreach ($anni_disponibili as $anno_disp) {
                                                    ?>
                                                        <option value="<?= $anno_disp['anno'] ?>" <?= $anno == $anno_disp['anno'] ? ' selected' : '' ?>><?= $anno_disp['anno'] ?></option>
                                                    <?php
                                                    }
                                                    ?>
                                                </select>
                                            </div>
                                        </div>
                                        <div class="col-md-2">
                                            <label for="start-date">Data Iniziale</label>
                                            <input type="text" id="start-date" class="form-control" placeholder="Start Date" value="<?= $data_iniziale ?>">
                                        </div>

                                        <div class="col-md-2">
                                            <label for="end-date">Data Finale</label>
                                            <input type="text" id="end-date" class="form-control" placeholder="End Date" value="<?= $data_finale ?>">
                                        </div>
                                        <!-- <div class="col-md-2">
                                            <button class="btn btn-primary btn-sm filtro">Imposta periodo</button>
                                        </div> -->
                                    </div>
                                <?php } ?>
                                <?php if ($tipo == 3) { ?>
                                    <div class="row">
                                        <div class="col-md-2">
                                            <div>Annata vino</div>
                                            <div>
                                                <select class="form-control testselect3" id="anno">
                                                </select>
                                            </div>
                                        </div>
                                        <div class="col-md-2">
                                            <label for="start-date">Data Iniziale</label>
                                            <input type="text" id="start-date" class="form-control" placeholder="Start Date" value="<?= $data_iniziale ?>">
                                        </div>

                                        <div class="col-md-2">
                                            <label for="end-date">Data Finale</label>
                                            <input type="text" id="end-date" class="form-control" placeholder="End Date" value="<?= $data_finale ?>">
                                        </div>
                                    </div>
                                    <div class="row">
                                        <div class="col-md-2">
                                            <div>Tipo</div>
                                            <div>
                                                <select class="form-control testselect2" id="tipodati">
                                                    <option value="horeca" <?= $tp == 'horeca' ? ' selected' : '' ?>>Horeca</option>
                                                    <option value="wineshop" <?= $tp == 'wineshop' ? ' selected' : '' ?>>Wine Shop</option>
                                                    <option value="all" <?= $tp == 'tutto' ? ' selected' : '' ?>>Tutto</option>
                                                </select>
                                            </div>
                                        </div>
                                        <div class="col-md-2">
                                            <div>Varietà</div>
                                            <div>
                                                <?php
                                                $varietaArray = array('cabernet', 'filorosso', 'pinot nero', 'refosco', 'chardonnay', 'friulano', 'malvasia', 'pinot grigio', 'ribolla', 'sauvignon');

                                                // Inizia il <select>
                                                echo '<select class="form-control testselect3" name="varieta" id="varieta" placeholder="Seleziona varietà">';

                                                // Crea un'opzione per ciascun elemento nell'array
                                                foreach ($varietaArray as $varieta) {
                                                    echo '<option value="' . $varieta . '"';
                                                    if ($v == $varieta) {
                                                        echo ' selected';
                                                    }

                                                    echo '>' . ucfirst($varieta) . '</option>';
                                                }

                                                // Chiude il <select>
                                                echo '</select>';
                                                ?>

                                            </div>
                                        </div>
                                        <div class="col-md-8">
                                            <div>Vini</div>
                                            <div>
                                                <select class="form-control testselect2" multiple="multiple" id="vini">
                                                    <?php
                                                    include(__DIR__ . '/../include/configpdo.php');
                                                    try {
                                                        $query = "SELECT * 
                                                        FROM lista_prodotti
                                                        WHERE varieta = '$v'
                                                        AND nome_prodotto LIKE '%$anno%'";
                                                        $stmt = $db->prepare($query);
                                                        $stmt->execute();
                                                        $dati   = $stmt->fetchAll();
                                                        foreach ($dati as $row) {
                                                    ?>
                                                            <option value="<?= $row['prod_id'] ?>"><?= $row['cod_prod'] ?> - <?= $row['nome_prodotto'] ?></option>
                                                    <?php
                                                        }
                                                    } catch (PDOException $e) {
                                                        echo "Error : " . $e->getMessage();
                                                    }

                                                    ?>

                                                </select>
                                            </div>
                                        </div>
                                    </div>
                                <?php } ?>
                                <?php if ($tipo == 4) { ?>
                                    <div class="row">

                                        <div class="col-md-2">
                                            <label for="start-date">Mese</label>
                                            <input type="text" id="datepicker-month" class="form-control" placeholder="Start Date" value="<?= $mesecorrente ?>">
                                        </div>
                                    </div>
                                <?php
                                } ?>
                                <!-- paese, regioni e province -->
                                <div class="row">
                                    <?php if ($tipo == 2) { ?>
                                        <div class="col-md-2">
                                            <div>Tipo</div>
                                            <div>
                                                <select class="form-control testselect2" id="tipodati">
                                                    <option value="horeca" <?= $tp == 'horeca' ? ' selected' : '' ?>>Horeca</option>
                                                    <option value="wineshop" <?= $tp == 'wineshop' ? ' selected' : '' ?>>Wine Shop</option>
                                                    <option value="all" <?= $tp == 'tutto' ? ' selected' : '' ?>>Tutto</option>
                                                </select>
                                            </div>
                                        </div>

                                        <div class="col-md-2">
                                            <div>Paese</div>
                                            <div>
                                                <select class="form-control testselect2" id="paese">
                                                    <option value="all" <?= $p == 'all' ? ' selected' : '' ?>>Tutti</option>
                                                    <option value="Italia" <?= $p == 'Italia' ? ' selected' : '' ?>>Italia</option>
                                                    <option value="Nitalia" <?= $p == 'Nitalia' ? ' selected' : '' ?>>Non Italia</option>
                                                    <?php
                                                    $paesi = paesi($anno);
                                                    if (!empty($paesi)) {
                                                        foreach ($paesi as $paese) { ?>
                                                            <option value="<?= $paese['paese'] ?>" <?= $p == $paese['paese'] ? ' selected' : '' ?>><?= $paese['paese'] ?></option>
                                                    <?php
                                                        }
                                                    }
                                                    ?>
                                                </select>
                                            </div>
                                        </div><?php } ?>
                                    <?php
                                    //se il paese è italia mostro il filtro per le regioni
                                    if ($p == 'Italia') {
                                        $regioni = regioni($anno);
                                    ?>
                                        <div class="col-md-2">
                                            <div>Regioni</div>
                                            <div>
                                                <select class="form-control testselect2" multiple="multiple" id="regioni">
                                                    <!-- <option value="all">Tutte</option> -->
                                                    <?php
                                                    foreach ($regioni as $regione) { ?>
                                                        <option value="<?= $regione['regione'] ?>" <?= $r == $regione['regione'] ? ' selected' : ''  ?>><?= $regione['regione'] ?></option>
                                                    <?php
                                                    }
                                                    ?>
                                                </select>
                                            </div>
                                        </div>
                                    <?php
                                    }
                                    ?>
                                    <div class="col-md-2">
                                        <div id="province_form"></div>
                                    </div>

                                </div>

                                <!-- bottone cerca -->
                                <div class="row mg-t-20">
                                    <div class="col-md-12">
                                        <button class="btn btn-success btn-block" id="cerca">Cerca</button>

                                    </div>
                                </div>
                                <div class="row">
                                    <div class="col-md-6"></div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="row row-sm">
                    <div class="col-sm-12 col-md-12 col-lg-12 col-xl-12">
                        <div class="card">
                            <div class="card-header">
                                <h3 class="card-title">Fatture</h3>
                            </div>
                            <div class="card-body" id="dati">
                            </div>
                        </div>
                    </div>
                </div>
                <!-- /Container -->
            </div>
            <!-- /main-content -->

            <!-- Footer opened -->
            <?php
            include 'partials/footer.php';
            include 'partials/modal.php';
            ?>
            <!-- Footer closed -->

        </div>
        <!-- End Page -->

        <!-- Back-to-top -->
        <?php
        include 'partials/library.php';
        ?>

</body>

</html>

<script>
    // $('#basic-edittable-libera').DataTable({
    //     language: {
    //         url: 'http://cdn.datatables.net/plug-ins/1.12.1/i18n/it-IT.json'
    //     },
    //     initComplete: function() {
    //         this.api()
    //             .columns('.dt-filter')
    //             .every(function() {
    //                 var column = this;
    //                 var select = $('<select><option value="">Stato</option></select>')
    //                     .appendTo($(column.header()).empty())
    //                     .on('change', function() {
    //                         var val = $.fn.dataTable.util.escapeRegex($(this).val());

    //                         column.search(val ? '^' + val + '$' : '', true, false).draw();
    //                     });
    //                 var statiMap = {};
    //                 column
    //                     .data()
    //                     .unique()
    //                     .sort()
    //                     .each(function(d, j) {
    //                         // Utilizza un selettore jQuery per trovare gli elementi con classe 'badge'
    //                         var badgeElement = $('<span>' + d + '</span>').find('.badge.badge-pill');

    //                         // Estrai il testo della classe 'badge' e assicurati che non sia vuoto
    //                         var testoBadge = badgeElement.length > 0 ? badgeElement.text() : '';

    //                         // Verifica che il testoBadge non sia già stato aggiunto
    //                         if (testoBadge && !statiMap[testoBadge]) {
    //                             select.append('<option value="' + testoBadge + '">' + testoBadge + '</option>');
    //                             statiMap[testoBadge] = true; // Imposta il flag per evitare duplicati
    //                         }
    //                     });
    //             });
    //     },
    //     "responsive": true,
    // });

    //se cambia il tipo ricarico la pagina con il parametro tipo
    $('#tipo').change(function() {
        var tipo = $(this).val();
        window.location.href = 'libera2.php?&tipo=' + tipo;
    });

    //se cambia l'anno ricarico la pagina con il parametro anno
    $('#anno').change(function() {
        var anno = $(this).val();
        window.location.href = 'libera2.php?a=' + anno + '&tipo=' + <?= $tipo ?>;
    });

    //se cambia il paese ricarico la pagina con il parametro paese
    $('#paese').change(function() {
        var paese = $(this).val();
        var anno = '<?= $anno ?>';
        var data_iniziale = $('#start-date').val();
        var data_finale = $('#end-date').val();
        window.location.href = 'libera2.php?a=' + anno + '&p=' + paese + '&di=' + data_iniziale + '&df=' + data_finale + '&tipo=' + <?= $tipo ?> + '&tp=' + $('#tipodati').val();
    });

    //se clicco su imposta periodo ricarico la pagina con i parametri data iniziale e data finale
    $('.filtro').click(function() {
        var data_iniziale = $('#start-date').val();
        var data_finale = $('#end-date').val();
        var anno = $('#anno').val();
        var paese = $('#paese').val();
        window.location.href = 'libera2.php?a=' + anno + '&p=' + paese + '&di=' + data_iniziale + '&df=' + data_finale;
    });


    //se seleziono una regione o più carico le province tramite ajax
    $('#regioni').change(function() {
        var regioni = $(this).val();
        var di = $('#start-date').val();
        var df = $('#end-date').val();
        var paese = $('#paese').val();
        var tipo = '<?= $tipo ?>';
        $.ajax({
            url: 'include/get_province.php',
            type: 'POST',
            data: {
                regioni: regioni,
                s: di,
                e: df,
                paese: paese,
                tipo: tipo
            },
            success: function(data) {
                //creo una select con le province
                $('#province_form').html(data);
                $('.testselect2').SumoSelect();
            }
        });
    });

    function getParameterByName(name) {
        var match = RegExp('[?&]' + name + '=([^&]*)').exec(window.location.search);
        return match && decodeURIComponent(match[1].replace(/\+/g, ' '));
    }

    // Recupera il parametro 'r' dalla URL
    var regioneSelezionata = getParameterByName('r');
    if (regioneSelezionata) {
        // Setta il valore del select #regioni
        $('#regioni').val(regioneSelezionata);

        // Simula il cambiamento per attivare il caricamento delle province
        $('#regioni').change();

        //simula il click sul bottone cerca
        //aspeta 1 secondo per far caricare le province
        setTimeout(function() {
            $('#cerca').click();
        }, 1000);

    }

    $('.testselect2').SumoSelect();

    $(document).ready(function() {
        $('#start-date').datepicker({
            format: "dd-mm-yyyy",
            autoclose: true,
            'language': 'it'
        }).on('changeDate', function(selected) {
            var startDate = new Date(selected.date.valueOf());
            $('#end-date').datepicker('setStartDate', startDate);
        });
        //Month picker
        $('#datepicker-month').bootstrapdatepicker({
            format: "mm/yyyy",
            viewMode: "months",
            minViewMode: "months",
            multidate: false,
            'language': 'it'
        })

        $('#end-date').datepicker({
            format: "dd-mm-yyyy",
            autoclose: true,
            'language': 'it'
        }).on('changeDate', function(selected) {
            var endDate = new Date(selected.date.valueOf());
            $('#start-date').datepicker('setEndDate', endDate);
        });
    });

    //se clicco su cerca invio i dati dei form tramite ajax
    $('#cerca').click(function() {
        $('#cerca').html('<span class="spinner-border spinner-border-sm" role="status" aria-hidden="true"></span> Caricamento...');
        $('#dati').html('');
        var regioni = $('#regioni').val();
        var province = $('#province').val();
        var di = $('#start-date').val();
        var df = $('#end-date').val();
        var mese = $('#datepicker-month').val();
        var paese = $('#paese').val();
        var anno = $('#anno').val();
        var tipo = $('#tipo').val();
        var tp = $('#tipodati').val();
        var vini = $('#vini').val();
        var varieta = $('#varieta').val();
        $.ajax({
            url: 'include/get_dati_libera.php',
            type: 'POST',
            data: {
                tp: tp,
                regioni: regioni,
                province: province,
                varieta: varieta,
                vini: vini,
                s: di,
                e: df,
                mese: mese,
                paese: paese,
                anno: anno,
                tipo: tipo
            },
            dataType: 'json',
            success: function(response) {
                console.log(response);
                var data = response.dati_tabella;
                var totali = response.totali;
                $('#dati').html(data);
                if (tipo == 1) {
                    var table = $('#basic-edittable-libera').DataTable({
                        buttons: [{
                            extend: 'pdfHtml5',
                            text: 'Esporta in PDF',
                            title: 'Lista Clienti',
                            messageTop: '\n Rapporto dettagliato per i clienti selezionati.\n\n',
                            messageBottom: 'Generato il: ' + new Date().toLocaleDateString(),
                            customize: function(doc) {
                                doc.defaultStyle.fontSize = 10; // Dimensione del carattere per il contenuto
                                doc.styles.tableHeader.fontSize = 12; // Dimensione del carattere per l'intestazione della tabella
                                doc.content.splice(0, 0, {
                                    text: 'Rapporto Generato per il Paese: ' + paese,
                                    alignment: 'left',
                                    margin: [20, 10, 20, 20],
                                    fontSize: 12
                                });
                            }
                        }, 'colvis'],
                        language: {
                            searchPlaceholder: 'Cerca...',
                            scrollX: "100%",
                            sSearch: '',
                        }
                    });
                }
                if (tipo == 2) {
                    var table = $('#basic-edittable-libera').DataTable({
                        buttons: [{
                            extend: 'pdfHtml5',
                            text: 'Esporta in PDF',
                            title: 'Imponibili',
                            messageTop: 'Rapporto dettagliato per i clienti selezionati.\n\n',
                            messageBottom: 'Generato il: ' + new Date().toLocaleDateString(),
                            customize: function(doc) {
                                doc.defaultStyle.fontSize = 9; // Dimensione del carattere per il contenuto
                                doc.styles.tableHeader.fontSize = 12; // Dimensione del carattere per l'intestazione della tabella
                                // Aggiungi i dati di totali al documento PDF
                                doc.content.splice(0, 0, {
                                    text: 'Rapporto Generato per il Periodo: ' + di + ' - ' + df + '\nPaese: ' + paese +

                                        '\n\nNumero Clienti Totali: ' + totali.numero_clienti_totali +
                                        '\nNumero Fatture Totali: ' + totali.numero_fatture_totali +
                                        '\n\nImponibile Totale: ' + totali.totale_importo_netto + ' €' +
                                        '\nImponibile Incassato: ' + totali.totale_importo_pagato + ' €' +
                                        '\nImponibile da Incassare: ' + totali.totale_importo_non_pagato + ' €' +
                                        '\nImponibile Scaduto: ' + totali.totale_importo_scaduto + ' €' +
                                        '\n\nImponibile da agente: ' + totali.totale_imponibile_agenti + ' €' +
                                        '\nImponibile non agente: ' + totali.totale_imponibile_non_agenti + ' €',

                                    margin: [20, 10, 20, 20],
                                    fontSize: 12
                                });
                                // Imposta le larghezze delle colonne per occupare il 100% della larghezza del foglio
                                // Verifica che ci sia almeno un contenuto e una tabella nel documento
                                if (doc.content.length > 0 && doc.content[doc.content.length - 1].table) {
                                    var tableBody = doc.content[doc.content.length - 1].table.body;
                                    if (tableBody && tableBody.length > 0) {
                                        var tableColumnCount = tableBody[0].length;
                                        var columnWidth = 100 / tableColumnCount; // Calcola la larghezza in base al numero di colonne
                                        doc.content[doc.content.length - 1].table.widths = Array(tableColumnCount).fill(columnWidth + '%');
                                    }
                                }
                            }
                        }, 'colvis'],
                        language: {
                            searchPlaceholder: 'Cerca...',
                            scrollX: "100%",
                            sSearch: '',
                        }
                    });
                }
                if (tipo == 3) {
                    var table = $('#basic-edittable-libera').DataTable({

                        buttons: [{
                            extend: 'pdfHtml5',
                            text: 'Esporta in PDF',
                            title: 'Lista Bottiglie',
                            messageTop: '\n Rapporto dettagliato per la varietà selezionata.\n\n',
                            messageBottom: 'Generato il: ' + new Date().toLocaleDateString(),
                            customize: function(doc) {
                                doc.defaultStyle.fontSize = 9; // Dimensione del carattere per il contenuto
                                doc.styles.tableHeader.fontSize = 12; // Dimensione del carattere per l'intestazione della tabella
                                // Aggiungi i dati di totali al documento PDF
                                doc.content.splice(0, 0, {
                                    text: 'Rapporto Generato per il Periodo: ' + di + ' - ' + df + '\nTipo: ' + tp +

                                        '\n\nNumero Bottigli Totali: ' + totali.totale_qta +
                                        '\nNumero Bottiglie Italia: ' + totali.totale_qta_italia +
                                        '\nNumero Bottiglie Estero: ' + totali.totale_qta_estero,

                                    margin: [20, 10, 20, 20],
                                    fontSize: 12
                                });
                                // Imposta le larghezze delle colonne per occupare il 100% della larghezza del foglio
                                // Verifica che ci sia almeno un contenuto e una tabella nel documento
                                if (doc.content.length > 0 && doc.content[doc.content.length - 1].table) {
                                    var tableBody = doc.content[doc.content.length - 1].table.body;
                                    if (tableBody && tableBody.length > 0) {
                                        var tableColumnCount = tableBody[0].length;
                                        var columnWidth = 100 / tableColumnCount; // Calcola la larghezza in base al numero di colonne
                                        doc.content[doc.content.length - 1].table.widths = Array(tableColumnCount).fill(columnWidth + '%');
                                    }
                                }
                            }
                        }, 'colvis'],
                        language: {
                            searchPlaceholder: 'Cerca...',
                            scrollX: "100%",
                            sSearch: '',
                        },
                        initComplete: function() {
                            this.api()
                                .columns('.dt-filter')
                                .every(function() {
                                    var column = this;
                                    var select = $('<select><option value="">Stato</option></select>')
                                        .appendTo($(column.header()).empty())
                                        .on('change', function() {
                                            var val = $.fn.dataTable.util.escapeRegex($(this).val());

                                            column.search(val ? '^' + val + '$' : '', true, false).draw();
                                        });

                                    // Aggiunge ogni valore unico al menu a tendina
                                    column
                                        .data()
                                        .unique()
                                        .sort()
                                        .each(function(d, j) {
                                            // Se il dato è un elemento HTML, estrai solo il testo, altrimenti usa il dato direttamente
                                            var optionText = $('<div>').html(d).text().trim();

                                            if (optionText) {
                                                select.append('<option value="' + optionText + '">' + optionText + '</option>');
                                            }
                                        });
                                });
                            this.api()
                                .columns('.dt-filter2')
                                .every(function() {
                                    var column = this;
                                    var select = $('<select><option value="">Provincia</option></select>')
                                        .appendTo($(column.header()).empty())
                                        .on('change', function() {
                                            var val = $.fn.dataTable.util.escapeRegex($(this).val());
                                            column.search(val ? '^' + val + '$' : '', true, false).draw();
                                        });

                                    column
                                        .data()
                                        .unique()
                                        .sort()
                                        .each(function(d, j) {
                                            var optionText = $('<div>').html(d).text().trim();
                                            if (optionText) {
                                                select.append('<option value="' + optionText + '">' + optionText + '</option>');
                                            }
                                        });
                                });
                        },
                    });
                }

                if (tipo == 4) {

                    var table = $('#basic-edittable-libera').DataTable({
                        ordering: false, // Disabilita l'ordinamento
                        pageLength: 100, // Imposta 100 righe per pagina
                        buttons: [{
                            extend: 'pdfHtml5',
                            text: 'Esporta in PDF',
                            title: 'Lista Bottiglie',
                            messageTop: '\n Rapporto Bottiglie.\n\n',
                            messageBottom: 'Generato il: ' + new Date().toLocaleDateString(),
                            customize: function(doc) {
                                doc.defaultStyle.fontSize = 10; // Dimensione del carattere per il contenuto
                                doc.styles.tableHeader.fontSize = 12; // Dimensione del carattere per l'intestazione della tabella
                                doc.content.splice(0, 0, {
                                    text: 'Rapporto Generato per il Periodo: ' + di + ' - ' + df,
                                    alignment: 'left',
                                    margin: [20, 10, 20, 20],
                                    fontSize: 12
                                });
                                if (doc.content.length > 0 && doc.content[doc.content.length - 1].table) {
                                    var tableBody = doc.content[doc.content.length - 1].table.body;
                                    if (tableBody && tableBody.length > 0) {
                                        var tableColumnCount = tableBody[0].length;
                                        var columnWidth = 100 / tableColumnCount; // Calcola la larghezza in base al numero di colonne
                                        doc.content[doc.content.length - 1].table.widths = Array(tableColumnCount).fill(columnWidth + '%');
                                    }
                                }
                            }
                        }, 'colvis'],
                        language: {
                            searchPlaceholder: 'Cerca...',
                            scrollX: "100%",
                            sSearch: '',
                        }
                    });

                }

                $('#cerca').html('Cerca');
                table.buttons().container()
                    .appendTo('#basic-edittable-libera_wrapper .col-md-6:eq(0)');

            }
        });
    });
</script>
<?php
if ($tipo == 3) { ?>
    <script>
        // Ottieni l'anno corrente
        const currentYear = new Date().getFullYear();

        // Definisci l'anno di partenza
        const startYear = 2021;

        // Seleziona l'elemento <select>

        for (let year = startYear; year <= currentYear; year++) {
            // Crea una nuova opzione
            const option = $('<option>', {
                value: year,
                text: year
            });
            if (year === <?= $anno ?>) {
                option.attr('selected', 'selected');
            }
            // Aggiungi l'opzione al select
            $('#anno').append(option);
        }
        $('.testselect3').SumoSelect();

        //se cambia la varietà ricarico la pagina con il parametro varietà
        $('#varieta').change(function() {
            var v = $(this).val();
            var anno = $('#anno').val();
            var data_iniziale = $('#start-date').val();
            var data_finale = $('#end-date').val();
            var tp = $('#tipodati').val();
            window.location.href = 'libera2.php?a=' + anno + '&tipo=' + <?= $tipo ?> + '&tp=' + tp + '&v=' + v + '&di=' + data_iniziale + '&df=' + data_finale;
        });
    </script>
<?php } ?>