<?php
include 'partials/headerarea.php';
include 'partials/header.php';
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
                            <h2 class="main-content-title tx-24 mg-b-1 mg-b-lg-1">Home</h2>
                        </div>
                    </div>
                </div>
                <!-- breadcrumb -->
                <!-- row -->
                <div class="row row-sm">
                    <div class="col-xl-12 col-lg-12 col-md-12 col-xm-12">
                        <!-- Una card con la tabella di tutte le fatture che potrebbero essere liquidate -->
                        <?php
                        $fatture = get_fatture_roma('liquidazione');
                        ?>
                        <div class="card">
                            <div class="card-header d-flex justify-content-between align-items-center">
                                <h3 class="card-title">Fatture Roma</h3>
                                <div>
                                    <?php
                                    if (!empty($fatture)) : ?>
                                        <button class="btn btn-primary btn-sm liquidazione_zona">Liquida</span></button>
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
                                                    <th>Zona / Tipo Prov</th>
                                                    <th>Prov Agente €</th>
                                                    <th>Prov Agenzia €</th>
                                                    <th class="dt-filter">Stato</th>
                                                </tr>
                                            </thead>
                                            <tbody id="dati_fatture">
                                                <?php
                                                $fatture = get_fatture_roma('liquidazione');
                                                foreach ($fatture as $fattura) {
                                                    $tipo_di_provvigione = $fattura['provv_percent'];
                                                ?>
                                                    <tr class="inclusa_zona" data-id="<?= $fattura['id_fatt'] ?>">
                                                        <td><?= $fattura['nome'] ?></td>
                                                        <td><?= $fattura['num_f'] ?> del <br><?= date('d/m/Y', strtotime($fattura['data_f'])) ?></td>
                                                        <td><?= arrotondaEFormatta($fattura['imp_tot']) ?> €<br>
                                                            (IVA: <?= arrotondaEFormatta($fattura['imp_iva'])  ?> €)
                                                        </td>
                                                        <td><?= arrotondaEFormatta($fattura['imp_netto']) ?> €</td>
                                                        <td><?php
                                                            echo $fattura['nome_zona'] . '<br>';
                                                            $provvigione_totale = $fattura['imp_netto'] * 16 / 100;
                                                            if ($fattura['id_liquidazione'] != '') : ?>
                                                                <?= $tipo_di_provvigione ?> %
                                                                <?php else :
                                                                if (($tipo_di_provvigione == 1) || ($tipo_di_provvigione == 2) || ($tipo_di_provvigione == 3)) {
                                                                } else {
                                                                    //Imposto Il tipo di provvigione
                                                                    $tipo_di_provvigione =   setPercentualeFattura($fattura['id_fatt'], $fattura['id_zona']);
                                                                }
                                                                //se non è stata liquidata la provvigione
                                                                //mostro il tipo di provvigione
                                                                if ($tipo_di_provvigione == 1) { ?>
                                                                    <a href="#" data-pk="<?= $fattura['id_fatt'] ?>">50% - 50%</a>
                                                                <?php
                                                                    $prov_agente = arrotondaEFormatta($provvigione_totale / 2) . ' €';
                                                                    $prov_agenzia = arrotondaEFormatta($provvigione_totale / 2) . ' €';
                                                                } elseif ($tipo_di_provvigione == 2) { ?>
                                                                    <a href="#" data-pk="<?= $fattura['id_fatt'] ?>">100% Agenzia</a>
                                                                <?php
                                                                    $prov_agente = '';
                                                                    $prov_agenzia = arrotondaEFormatta($provvigione_totale) . ' €';
                                                                }
                                                                ?>
                                                            <?php endif ?>
                                                        </td>
                                                        <?php

                                                        ?>
                                                        <td id="prov_a<?= $fattura['id_fatt'] ?>"><?= $prov_agente ?></td>
                                                        <td id="prov_b<?= $fattura['id_fatt'] ?>"><?= $prov_agenzia ?></td>
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
        value: 0,
        source: [{
                value: 1,
                text: '50% - 50%'
            },
            {
                value: 2,
                text: '100% Agenzia'
            },
        ],
        name: 'status',
        url: 'include/provv_roma.php',
        title: 'Tipo provvigione',
        ajaxOptions: {
            type: 'post',
            dataType: 'json'
        },
        success: function(response, newValue) {
            var pk = $(this).data('pk');
            $('#prov_a' + pk).html(response.provv_agente);
            $('#prov_b' + pk).html(response.provv_agenzia);
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
</script>