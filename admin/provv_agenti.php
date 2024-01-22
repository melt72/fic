<?php
include 'partials/headerarea.php';
include 'partials/header.php';
if (isset($_GET['id'])) {
    $id_agente = $_GET['id'];
} else {
    $id_agente = 0;
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
                            <h2 class="main-content-title tx-24 mg-b-1 mg-b-lg-1">Provvigione agenti</h2>

                        </div>
                    </div>
                    <div class="main-dashboard-header-right">
                    </div>
                </div>
                <!-- breadcrumb -->

                <!-- row -->
                <div class="row row-sm">
                    <div class="col-xl-12 col-lg-12 col-md-12 col-xm-12">
                        <div class="card">
                            <div class="card-body">
                                <label for="agente" class="form-label">Scelta agente</label>

                                <select class="form-select select2-no-search" name="provv_agente" id="provv_agente">
                                    <option>Seleziona Agente</option>
                                    <?php
                                    $agenti = get_agenti_totali();
                                    foreach ($agenti as $agente) { ?>
                                        <option value="<?= $agente['id'] ?>" <?php
                                                                                if (isset($id_agente)) {
                                                                                    if ($id_agente == $agente['id']) {
                                                                                        echo ' selected';
                                                                                    }
                                                                                } ?>>
                                            <?= $agente['nome_agente'] ?></option>
                                    <?php }
                                    ?>
                                </select>
                            </div>
                        </div>
                        <?php
                        $fatture = get_fatture_agente($id_agente, 'all');
                        if (!empty($fatture)) :
                        ?>
                            <div class="card mg-b-20" id="tabs-style2">
                                <div class="card-body">
                                    <div class="panel panel-primary tabs-style-2">
                                        <div class=" tab-menu-heading">
                                            <div class="tabs-menu1">
                                                <!-- Tabs -->
                                                <ul class="nav panel-tabs main-nav-line">
                                                    <li><a href="#tab4" class="nav-link active me-1" data-bs-toggle="tab">Fatture</a></li>
                                                    <li><a href="#tab5" class="nav-link me-1" data-bs-toggle="tab">Liquidazioni</a></li>
                                                </ul>
                                            </div>
                                        </div>
                                        <div class="panel-body tabs-menu-body main-content-body-right border">
                                            <div class="tab-content">
                                                <!-- fatture -->
                                                <div class="tab-pane active" id="tab4">
                                                    <div class="row">
                                                        <div class="col-sm-12 col-md-6 col-lg-6 col-xl-3">
                                                            <div class="card">
                                                                <div class="card-body">
                                                                    <div class="card-widget">
                                                                        <h6 class="mb-2">Fatturato</h6>
                                                                        <h2 class="text-end"><i class="icon-size mdi mdi-poll-box   float-start text-warning text-warning-shadow"><span><small>Totale</small></span></i><span><?= getFatturatoTotAgente($id_agente) ?> €</span>
                                                                        </h2>
                                                                        <h2 class="text-end"><i class="icon-size mdi mdi-poll-box float-start text-primary text-warning-shadow">
                                                                                <span><small>Imponibile</small></span>
                                                                            </i><span><?= getFatturatoNettoAgente($id_agente) ?> €</span>
                                                                        </h2>
                                                                    </div>
                                                                </div>
                                                            </div>
                                                        </div>
                                                        <div class="col-sm-12 col-md-6 col-lg-6 col-xl-3">
                                                            <div class="card">
                                                                <div class="card-body">
                                                                    <div class="card-widget">
                                                                        <h6 class="mb-2">Provvigione Totale</h6>
                                                                        <h2 class="text-end"><i class="icon-size mdi mdi-poll-box   float-start text-warning text-warning-shadow"><span><small>Liquidata</small></span></i><span><?= getProvvigioneAgente($id_agente, 'totale_liquidata') ?> €</span>
                                                                        </h2>
                                                                        <h2 class="text-end"><i class="icon-size mdi mdi-poll-box float-start text-primary text-warning-shadow">
                                                                                <span><small>Da Liquidare</small></span>
                                                                            </i><span><?= getProvvigioneAgente($id_agente, 'totale_da_liquidare') ?> €</span>
                                                                        </h2>
                                                                    </div>
                                                                </div>
                                                            </div>
                                                        </div>
                                                        <div class="col-sm-12 col-md-6 col-lg-6 col-xl-3">
                                                            <div class="card">
                                                                <div class="card-body">
                                                                    <div class="card-widget">
                                                                        <h6 class="mb-2">Fatturato Imponibile</h6>
                                                                        <h2 class="text-end"><i class="icon-size mdi mdi-poll-box   float-start text-warning text-warning-shadow"><span><small>Incassato</small></span></i><span><?= getFatturatoNettoAgente($id_agente, 'incassato') ?> €</span>
                                                                        </h2>
                                                                        <h2 class="text-end"><i class="icon-size mdi mdi-poll-box float-start text-primary text-warning-shadow">
                                                                                <span><small>Da Incassare</small></span>
                                                                            </i><span><?= getFatturatoNettoAgente($id_agente, 'da_incassare') ?> €</span>
                                                                        </h2>
                                                                    </div>
                                                                </div>
                                                            </div>
                                                        </div>
                                                    </div>
                                                    <div class="row">
                                                        <div class="col-sm-12 col-md-12 col-lg-12 col-xl-12">
                                                            <div class="card">
                                                                <div class="card-header">
                                                                    <h3 class="card-title">Fatture agente</h3>
                                                                </div>
                                                                <div class="card-body">
                                                                    <div class="pd-15 background mg-b-5">
                                                                        <?php
                                                                        //prendo gli anni dei prodotti di marketing
                                                                        $anni =  getAnniFattureAgente($id_agente);
                                                                        if (!empty($anni)) {
                                                                            //conto quanti anni sono
                                                                            $n_anni = count($anni);
                                                                            $conta_a = 0;
                                                                            foreach ($anni as $anno) :
                                                                                //se è l'ultimo anno cambia colore
                                                                                if ($conta_a == $n_anni - 1) {
                                                                                    $anno_di_riferimento = $anno['anno'];
                                                                        ?>
                                                                                    <button class="btn btn-primary btn-sm annofatture" data-anno="<?= $anno['anno'] ?>"><?= $anno['anno'] ?></button>
                                                                                <?php
                                                                                } else { ?>
                                                                                    <button class="btn btn-secondary btn-sm annofatture" data-anno="<?= $anno['anno'] ?>"><?= $anno['anno'] ?></button>
                                                                            <?php
                                                                                }
                                                                                $conta_a++;
                                                                            endforeach;
                                                                        } else { ?>
                                                                            <div class="alert alert-warning " role="alert"> Non ci sono prodotti di marketing per questo punto vendita</div>
                                                                        <?php }
                                                                        ?>
                                                                    </div>
                                                                    <div class="table-responsive">
                                                                        <table class="table table-bordered border text-nowrap mb-0" id="basic-edittable">
                                                                            <thead>
                                                                                <tr>
                                                                                    <th>Cliente</th>
                                                                                    <th>n.fatt</th>
                                                                                    <th>data</th>
                                                                                    <th>importo</th>
                                                                                    <th>imponibile</th>
                                                                                    <th>iva</th>
                                                                                    <th>Prov %</th>
                                                                                    <th>Prov €</th>
                                                                                    <th>Stato</th>
                                                                                </tr>
                                                                            </thead>
                                                                            <tbody id="dati_fatture">
                                                                                <?php

                                                                                foreach ($fatture as $fattura) {
                                                                                ?>
                                                                                    <tr>
                                                                                        <td><?= $fattura['nome'] ?></td>
                                                                                        <td><?= $fattura['num_f'] ?></td>
                                                                                        <td>Data: <?= date('d/m/Y', strtotime($fattura['data_f'])) ?><br>Scad: <?= date('d/m/Y', strtotime($fattura['data_scadenza'])) ?> </td>
                                                                                        <td><?= arrotondaEFormatta($fattura['imp_tot']) ?> €</td>
                                                                                        <td><?= arrotondaEFormatta($fattura['imp_netto']) ?> €</td>
                                                                                        <td><?= arrotondaEFormatta($fattura['imp_iva'])  ?> €</td>
                                                                                        <td><?php
                                                                                            if ($fattura['id_liquidazione'] != '') : ?>
                                                                                                <?= $fattura['provv_percent'] ?> %
                                                                                            <?php else : ?>
                                                                                                <a href="#" data-pk="<?= $fattura['id_fatt'] ?>"><?= $fattura['provv_percent'] ?> %</a>
                                                                                            <?php endif ?>
                                                                                        </td>
                                                                                        <td id="prov_<?= $fattura['id_fatt'] ?>"><?= arrotondaEFormatta($fattura['imp_netto'] * $fattura['provv_percent'] / 100) ?> €</td>
                                                                                        <td><?= status_fattura($fattura['id_fatt']) ?></td>
                                                                                    </tr>
                                                                                <?php } ?>
                                                                            </tbody>
                                                                        </table>
                                                                    </div>
                                                                </div>
                                                            </div>
                                                        </div>
                                                    </div>
                                                </div>
                                                <!-- liquidazioni -->
                                                <div class="tab-pane" id="tab5">
                                                    <div class="card">
                                                        <div class="card-header d-flex justify-content-between align-items-center">
                                                            <h3 class="card-title">Liquidazioni provvigione</h3>
                                                            <div>
                                                                <button class="btn btn-primary btn-sm liquidazione"><span class="fe fe-plus"> Nuova Liquidazione</span></button>
                                                            </div>
                                                        </div>
                                                        <div class="card-body">
                                                            <?php
                                                            $liquidazioni = getLiquidazioniAgente($id_agente);
                                                            if (!empty($liquidazioni)) :
                                                            ?>
                                                                <div class="table-responsive">
                                                                    <table class="table table-bordered border text-nowrap mb-0" id="basic-liquidazione">
                                                                        <thead>
                                                                            <tr>
                                                                                <th>Data</th>
                                                                                <th>Importo</th>
                                                                                <th>Metodo Pagamento</th>
                                                                                <th></th>
                                                                            </tr>
                                                                        </thead>
                                                                        <tbody id="dati_fatture">
                                                                            <?php
                                                                            foreach ($liquidazioni as $liquidazione) {
                                                                            ?>
                                                                                <tr>
                                                                                    <td><?= date('d/m/Y', strtotime($liquidazione['data'])) ?></td>
                                                                                    <td><?= arrotondaEFormatta($liquidazione['importo']) ?> €</td>
                                                                                    <td><?= getMetodoPagamento($liquidazione['pagamento']); ?> - <?= $liquidazione['note'] ?></td>
                                                                                    <td><a href="pdf.php?id_liquidazione=<?= $liquidazione['id'] ?>" class="btn btn-primary btn-sm" target="_blank"><i class="fe fe-file-text"></i></a></td>
                                                                                </tr>
                                                                            <?php } ?>
                                                                        </tbody>
                                                                    </table>
                                                                </div>
                                                            <?php
                                                            else :
                                                                echo '<div class="alert alert-warning" role="alert">
                                                                <span class="alert-inner--icon"><i class="fe fe-info"></i></span>
                                                                <span class="alert-inner--text"><strong>Warning!</strong> Non ci sono dati!</span>
                                                            </div>';
                                                            endif ?>
                                                        </div>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        <?php else :
                            echo '<div class="alert alert-warning" role="alert">
                                    <span class="alert-inner--icon"><i class="fe fe-info"></i></span>
                                    <span class="alert-inner--text"><strong>Warning!</strong> Non ci sono dati!</span>
                                </div>';
                        endif ?>
                    </div>
                </div>
                <!-- row closed -->
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
    var id = '<?= $id_agente ?>';
    $('#basic-edittable').DataTable({
        language: {
            searchPlaceholder: 'Search...',
            sSearch: '',
        }
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
            $('#prov_' + pk).html(response);
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
    // $('#data_liquidazione').bootstrapdatepicker({
    //     format: "dd/mm/yyyy",
    //     viewMode: "date",
    //     multidate: false,
    //     multidateSeparator: "/",
    // })

    // var today = new Date();
    // var dd = today.getDate();
    // var mm = today.getMonth() + 1; //Gennaio è 0!
    // var yyyy = today.getFullYear();
    // if (dd < 10) {
    //     dd = '0' + dd
    // }
    // if (mm < 10) {
    //     mm = '0' + mm
    // }
    // today = dd + '/' + mm + '/' + yyyy;
    // $('#data_liquidazione').val(today);


    // $(document).on('click', '.li-scelta', function(e) {
    //     e.preventDefault();
    //     var button = $(this);
    //     var id_fattura = $(this).data('id');
    //     var id_importo = $(this).data('importo');
    //     if (button.hasClass('text-success')) {
    //         // Cambia lo stato del bottone e chiama l'API con AJAX
    //         toggleButtonState(button, 'text-danger', 'text-success', 'fe-square', 'fe-check-square');
    //         button.removeClass('inclusa');


    //     } else {
    //         // Cambia lo stato del bottone e chiama l'API con AJAX
    //         toggleButtonState(button, 'text-success', 'text-danger', 'fe-check-square', 'fe-square');
    //         button.addClass('inclusa');
    //     }
    //     // Aggiorna la somma degli importi
    //     var sommaImporti = calcolaSommaImporti();
    //     $('#importo_liquidazione').text(sommaImporti.toFixed(2));
    //     //Se importo liquidazSe importo liquidazione è zero disattivo
    //     if (sommaImporti == 0) {
    //         $('.liquida_provv').prop('disabled', true);
    //     } else {
    //         $('.liquida_provv').prop('disabled', false);
    //     }
    // });

    // function toggleButtonState(button, addClass, removeClass, addIconClass, removeIconClass) {
    //     button.removeClass(removeClass).addClass(addClass);
    //     button.removeClass(removeIconClass).addClass(addIconClass);
    // }

    // function calcolaSommaImporti() {
    //     var sommaImporti = 0;

    //     $('.inclusa').each(function() {
    //         var importo = parseFloat($(this).data('importo')) || 0;
    //         sommaImporti += importo;
    //     });

    //     return sommaImporti;
    // }

    // function callAjaxAPI(id_fatt, action, callback) {
    //     // Esegui la chiamata AJAX qui
    //     $.ajax({
    //         url: 'include/liquidazione.php',
    //         type: 'POST',
    //         data: {
    //             id_fattura: id_fatt,
    //             action: action,
    //             tipo: 'valore'
    //         },
    //         success: function(response) {
    //             if (typeof callback === 'function') {
    //                 callback(response);
    //             }
    //         },
    //         error: function(error) {
    //             console.error('Errore durante la chiamata AJAX:', error);
    //         }
    //     });
    // }

    // //Quando schiaccio sul bottone liquida provvigione Invio i dati tramite ajax
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