<?php
include 'partials/headerarea.php';
include 'partials/header.php';
if (isset($_GET['s'])) {
    $data_iniziale = $_GET['s']; //data iniziale
} else {
    $data_iniziale = date('01-01-Y'); //data iniziale inizio anno
}
if (isset($_GET['e'])) {
    $data_finale = $_GET['e']; //data finale
} else {
    $data_finale = date('d-m-Y');  //data finale oggi
}
if (isset($_GET['a'])) {
    $anno = $_GET['a'];
} else {
    //anno corrente
    $anno = date('Y');
}
if (isset($_GET['id'])) {
    $id = $_GET['id']; //id dell'agente
}
//inizializzo l'array delle fatture
$array_id_fattura = [];
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

                            <h2 class="main-content-title tx-24 mg-b-1 mg-b-lg-1"> <a href="provv_agenti.php" class="btn btn-primary"><i class="fe fe-chevron-left" data-bs-toggle="tooltip" title="" data-bs-original-title="fe fe-chevron-right" aria-label="fe fe-chevron-right"></i></a> Liquidazione Agente</h2>
                        </div>
                    </div>
                </div>
                <!-- breadcrumb -->
                <!-- row -->
                <div class="row row-sm">
                    <div class="col-xl-12 col-lg-12 col-md-12 col-xm-12">
                        <div class="card">
                            <div class="card-header d-flex justify-content-between align-items-center">
                                <h3 class="card-title">Fatture Agente</h3>
                            </div>
                            <div class="card-body">
                                <div class="row mg-b-20">
                                    <div class="col-md-2">
                                        <label for="anno">Anno fatture</label>
                                        <select class="form-control" id="anno">
                                            <?php
                                            $anni_disponibili = getAnniFatture();
                                            foreach ($anni_disponibili as $anno_disp) {
                                                $annoCorrente = $anno_disp['anno'];
                                            ?>
                                                <option value="<?= $anno_disp['anno'] ?>" <?php if ($anno == $anno_disp['anno']) echo 'selected'; ?>><?= $anno_disp['anno'] ?></option>
                                            <?php } ?>
                                        </select>
                                    </div>
                                </div>
                                <?php
                                //data di oggi in formato dd/mm/yyyy
                                $data_oggi = date('d/m/Y');

                                ?>
                                <div class="row">
                                    <div class="col-md-2">
                                        <label for="start-date">Data Iniziale (pagamento)</label>
                                        <input type="text" id="start-date" class="form-control" placeholder="Start Date" value="<?= $data_iniziale ?>">
                                    </div>
                                    a
                                    <div class="col-md-2">
                                        <label for="end-date">Data Finale (pagamento)</label>
                                        <input type="text" id="end-date" class="form-control" placeholder="End Date" value="<?= $data_finale ?>">
                                    </div>
                                    <div class="col-md-2">
                                        <button class="btn btn-primary btn-sm filtro">Imposta filtro</button>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div class="col-xl-12 col-lg-12 col-md-12 col-xm-12">
                            <!-- Una card con la tabella di tutte le fatture che potrebbero essere liquidate -->
                            <?php
                            $fatture = get_fatture_agente($id, 'def', $anno, $data_iniziale, $data_finale);
                            ?>
                            <div class="card">
                                <div class="card-header d-flex justify-content-between align-items-center">
                                    <h3 class="card-title">Fatture Roma</h3>
                                    <div>
                                        <?php
                                        if (!empty($fatture)) : ?>
                                            <button class="btn btn-primary btn-sm liquidazione">Liquida</span></button>
                                        <?php endif ?>
                                    </div>
                                </div>
                                <div class="card-body">
                                    <?php
                                    if (!empty($fatture)) { ?>
                                        <div class="table-responsive">
                                            <table class="table table-bordered border text-nowrap mb-0" id="basic-edittable">
                                                <thead>
                                                    <tr>
                                                        <th>Cliente</th>
                                                        <th>n.fatt/data</th>
                                                        <th>importo</th>
                                                        <th>imponibile</th>
                                                        <th>Prov %</th>
                                                        <th>Prov Agente €</th>
                                                        <th class="dt-filter">Stato</th>
                                                    </tr>
                                                </thead>
                                                <tbody id="dati_fatture">
                                                    <?php
                                                    foreach ($fatture as $fattura) {
                                                        $tipo_di_provvigione = $fattura['provv_percent'];
                                                        $array_id_fattura[] = $fattura['id_fatt'];
                                                    ?>
                                                        <tr class="inclusa_zona" data-id="<?= $fattura['id_fatt'] ?>">
                                                            <td><?= $fattura['nome'] ?></td>
                                                            <td><?= $fattura['num_f'] ?> del <br><?= date('d/m/Y', strtotime($fattura['data_f'])) ?></td>
                                                            <td><?= arrotondaEFormatta($fattura['imp_tot']) ?> €<br>
                                                                (IVA: <?= arrotondaEFormatta($fattura['imp_iva'])  ?> €)
                                                            </td>
                                                            <td><?= arrotondaEFormatta($fattura['imp_netto']) ?> €</td>
                                                            <td>
                                                                <?php
                                                                if ($fattura['id_liquidazione'] != '') : ?>
                                                                    <?= $fattura['provv_percent'] ?> %
                                                                <?php else : ?>
                                                                    <a href="#" data-pk="<?= $fattura['id_fatt'] ?>"><?= $fattura['provv_percent'] ?> %</a>
                                                                <?php endif ?>

                                                                <?php
                                                                // echo $fattura['provv_percent'] . '%';
                                                                if ($fattura['status'] == 'paid') {
                                                                    $prov_agente = $fattura['imp_netto'] * $fattura['provv_percent'] / 100;
                                                                }
                                                                ?>
                                                            </td>
                                                            <td id="prov_a<?= $fattura['id_fatt'] ?>"><?= $fattura['status'] == 'paid' ? arrotondaEFormatta($prov_agente) . '€' : '' ?></td>

                                                            <td><?= status_fattura($fattura['id_fatt']) ?></td>
                                                        </tr>
                                                    <?php } ?>
                                                </tbody>
                                            </table>
                                        </div>
                                    <?php
                                    } else {
                                        echo '<div class="alert alert-danger">Non ci sono fatture da liquidare</div>';
                                    }
                                    ?>
                                </div>
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
    $(document).on('click', '.liquidazione_zona', function(event) {
        event.preventDefault();
        var start_date = $('#start-date').val();
        var end_date = $('#end-date').val();
        var anno = $('#anno').val();
        //Genero i dati tramite ajax
        $.ajax({
            type: "post",
            url: "include/liquidazione.php",
            data: {
                tipo: 'lista_roma',
                start_date: start_date,
                end_date: end_date,
                anno: anno
            },
            dataType: "html",
            success: function(response) {
                //Inserisco i valori json nel div #tab-zone e #tab-content-zone
                $('#dati_fatture_modal').html(response);
                $.ajax({
                    type: "post",
                    url: "include/liquidazione.php",
                    data: {
                        tipo: 'totali_roma',
                        start_date: start_date,
                        end_date: end_date,
                        anno: anno
                    },
                    dataType: "json",
                    success: function(response) {
                        // aggiungo data-agente e data-agenzia al bottone liquida_provv_roma
                        $('.liquida_provv_roma').attr('data-agente', response.agenti);
                        $('.liquida_provv_roma').attr('data-agenzia', response.agenzia);
                        $('#modal-liquidazione-zona').modal('show');
                    }
                });

            }
        });
    });


    $('#basic-edittable').DataTable({
        language: {
            url: 'http://cdn.datatables.net/plug-ins/1.12.1/i18n/it-IT.json'
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
                    var statiMap = {};
                    column
                        .data()
                        .unique()
                        .sort()
                        .each(function(d, j) {
                            // Utilizza un selettore jQuery per trovare gli elementi con classe 'badge'
                            var badgeElement = $('<span>' + d + '</span>').find('.badge.badge-pill');

                            // Estrai il testo della classe 'badge' e assicurati che non sia vuoto
                            var testoBadge = badgeElement.length > 0 ? badgeElement.text() : '';

                            // Verifica che il testoBadge non sia già stato aggiunto
                            if (testoBadge && !statiMap[testoBadge]) {
                                select.append('<option value="' + testoBadge + '">' + testoBadge + '</option>');
                                statiMap[testoBadge] = true; // Imposta il flag per evitare duplicati
                            }
                        });
                });
        },
        "responsive": true,
    });

    $('#basic-edittable a').editable({
        type: 'select',
        name: 'provv_percent',
        value: 16,
        source: [{
                value: 12,
                text: '12 %'
            },
            {
                value: 13,
                text: '13 %'
            },
            {
                value: 14,
                text: '14 %'
            },
            {
                value: 15,
                text: '15 %'
            },
            {
                value: 16,
                text: '16 %'
            },
            {
                value: 17,
                text: '17 %'
            },
            {
                value: 18,
                text: '18 %'
            },
            {
                value: 19,
                text: '19 %'
            },
            {
                value: 20,
                text: '20 %'
            },
        ],
        name: 'status',
        url: 'include/provv_agenti.php',
        title: 'Provv %',
        success: function(response, newValue) {
            var pk = $(this).data('pk');
            $('#prov_a' + pk).html(response);
            console.log(response);
        }
    });
    $('#basic-liquidazione').DataTable({
        language: {
            searchPlaceholder: 'Search...',
            sSearch: '',
        }
    });

    //Quando finisco di caricare la pagina
    $(document).ready(function() {
        var sommaImporti = calcolaSommaImporti();
        //Se importo liquidazSe importo liquidazione è zero disattivo
        if (sommaImporti == 0) {
            $('.liquida_provv').prop('disabled', true);
        } else {
            $('.liquida_provv').prop('disabled', false);
        }
    });

    function calcolaSommaImporti() {
        var sommaImporti = 0;

        $('.inclusa').each(function() {
            var importo = parseFloat($(this).data('importo')) || 0;
            sommaImporti += importo;
        });

        return sommaImporti;
    }

    $(document).ready(function() {
        $('#start-date').datepicker({
            format: "dd-mm-yyyy",
            autoclose: true
        }).on('changeDate', function(selected) {
            var startDate = new Date(selected.date.valueOf());
            $('#end-date').datepicker('setStartDate', startDate);
        });

        $('#end-date').datepicker({
            format: "dd-mm-yyyy",
            autoclose: true
        }).on('changeDate', function(selected) {
            var endDate = new Date(selected.date.valueOf());
            $('#start-date').datepicker('setEndDate', endDate);
        });
    });

    //se schiaccio il pulsante filtro ricarico la pagina con i nuovi parametri
    $('.filtro').click(function() {
        var start_date = $('#start-date').val();
        var end_date = $('#end-date').val();
        var anno = $('#anno').val();
        var id = <?= $id ?>;
        window.location.href = 'agenti-liquidazione.php?id=' + id + '&a=' + anno + '&s=' + start_date + '&e=' + end_date;
    });

    //anteprina liquidazione
    $(document).on('click', '.anteprima', function(e) {
        e.preventDefault();
        var data_liquidazione = $('#data_liquidazione').val();
        var anno = $('#anno').val();
        var periodo_da = $('#start-date').val();
        var periodo_a = $('#end-date').val();
        var fatture = <?= json_encode($array_id_fattura) ?>; //array con le fatture da liquidare
        var fattureJson = encodeURIComponent(JSON.stringify(fatture));
        var agente = <?= $id ?>;
        console.log(anno + ' ' + periodo_da + ' ' + periodo_a + ' ' + fattureJson + ' ' + agente);
        // Apro una nuova scheda con il PDF
        window.open('pdf_agenti_anteprima.php?fatture=' + fattureJson + '&anno=' + anno + '&start=' + periodo_da + '&end=' + periodo_a + '&id=' + agente, '_blank');
    });

    //Quando schiaccio sul bottone liquida_provv_roma Invio i dati tramite ajax
    $(document).on('click', '.liquida_provv_roma', function(e) {
        e.preventDefault();
        //Disabilito il bottone
        $('.liquida_provv_roma').prop('disabled', true);
        var data_liquidazione = $('#data_liquidazione_zona').val();
        var fatture = <?= json_encode($array_id_fattura) ?>;
        var agente = $(this).data('agente');
        var agenzia = $(this).data('agenzia');
        var start_date = $('#start-date').val();
        var end_date = $('#end-date').val();
        var anno = $('#anno').val();

        $.ajax({
            url: 'include/liquidazione.php',
            type: 'POST',
            data: {
                id_fattura: fatture,
                agente: agente,
                agenzia: agenzia,
                data_liquidazione: data_liquidazione,
                start_date: start_date,
                end_date: end_date,
                anno: anno,
                tipo: 'liquida_zona'
            },
            success: function(response) {
                var id_fattura = response;
                //Chiudo il model
                $('#modal-liquidazione-zona').modal('hide');
                //Success Message
                Swal.fire({
                    title: "Well done!",
                    text: 'Liquidazione registrata!',
                    icon: 'success',
                    showCancelButton: true,
                    confirmButtonText: 'Vedi Velina',
                    cancelButtonText: 'Esci',
                    confirmButtonColor: '#57a94f'
                }).then((result) => {
                    if (result.isConfirmed) {
                        //apro  una nuova scheda con il pdf
                        window.open('pdf_roma.php?id_liquidazione=' + id_fattura, '_blank');
                    }
                });
            },
            error: function(error) {
                console.error('Errore durante la chiamata AJAX:', error);
            }
        });
    });
    $(document).on('click', '.liquidazione', function(event) {
        event.preventDefault();
        var start_date = $('#start-date').val();
        var end_date = $('#end-date').val();
        var anno = $('#anno').val();
        var agente = <?= $id ?>;
        $.ajax({
            url: 'include/liquidazione.php',
            type: 'POST',
            data: {
                agente: agente,
                start_date: start_date,
                end_date: end_date,
                anno: anno,
                tipo: 'liquida_agente'
            },
            success: function(response) {
                $('#dati_fatture_agente').html(response);
            }
        });
        $('#modal-liquidazione').modal('show');
    });
    // $(document).on('click', '.liquida_provv', function(e) {
    //     e.preventDefault();
    //     //Disabilito il bottone
    //     $('.liquida_provv').prop('disabled', true);
    //     var metodo_pagamento = $('#metodo_pagamento').val();
    //     var note = $('#note').val();
    //     var data_liquidazione = $('#data_liquidazione').val();
    //     var importo_liquidazione = $('#importo_liquidazione').text();
    //     var fatture = [];
    //     $('.inclusa').each(function() {
    //         var id_fattura = $(this).data('id');
    //         fatture.push(id_fattura);
    //     });
    //     $.ajax({
    //         url: 'include/liquidazione.php',
    //         type: 'POST',
    //         data: {
    //             id_fattura: fatture,
    //             id_agente: id,
    //             metodo_pagamento: metodo_pagamento,
    //             note: note,
    //             data_liquidazione: data_liquidazione,
    //             importo_liquidazione: importo_liquidazione,
    //             tipo: 'liquida'
    //         },
    //         success: function(response) {
    //             var id_fattura = response;
    //             //Chiudo il model
    //             $('#modal-liquidazione').modal('hide');
    //             //Success Message
    //             Swal.fire({
    //                 title: "Well done!",
    //                 text: 'Liquidazione registrata!',
    //                 icon: 'success',
    //                 showCancelButton: true,
    //                 confirmButtonText: 'Vedi PDF',
    //                 cancelButtonText: 'Esci',
    //                 confirmButtonColor: '#57a94f'
    //             }).then((result) => {
    //                 /* Read more about isConfirmed, isDenied below */
    //                 if (result.isConfirmed) {
    //                     //apro  una nuova scheda con il pdf
    //                     window.open('pdf.php?id_fattura=' + id_fattura, '_blank');
    //                 }
    //             });
    //         },
    //         error: function(error) {
    //             console.error('Errore durante la chiamata AJAX:', error);
    //         }
    //     });
    // });
</script>