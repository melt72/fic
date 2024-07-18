<?php
include 'partials/headerarea.php';
include 'partials/header.php';
include('../include/configpdo.php');
try {
    $query = "SELECT id FROM `agenti` WHERE `sigla`='RSC'";
    $stmt = $db->prepare($query);
    $stmt->execute();
    $row   = $stmt->fetch(PDO::FETCH_ASSOC);
    $id = $row['id']; //id dell'agente
} catch (PDOException $e) {
    echo "Error : " . $e->getMessage();
}
if (isset($_GET['a'])) {
    $a = $_GET['a'];
} else {
    $a = date('Y'); //anno corrente
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
                            <h2 class="main-content-title tx-24 mg-b-1 mg-b-lg-1">Provvigione Roma</h2>
                        </div>
                    </div>
                    <div class="d-flex my-xl-auto right-content align-items-center">
                        <?php
                        $anni_disponibili =  getAnniFattureRoma();

                        ?>
                        <div class="mb-xl-0">
                            <div class="dropdown">
                                <button class="btn btn-primary dropdown-toggle" type="button" id="dropdownMenuDate" data-bs-toggle="dropdown" aria-expanded="false">
                                    <?= $a ?>
                                </button>
                                <ul class="dropdown-menu" aria-labelledby="dropdownMenuDate">
                                    <?php
                                    foreach ($anni_disponibili as $anno_disp) {
                                        $annod = $anno_disp['anno'];
                                    ?>
                                        <li><a class="dropdown-item" href="roma.php?a=<?= $annod ?>"><?= $annod ?></a></li>
                                    <?php
                                    }
                                    ?>
                                </ul>
                            </div>
                        </div>
                    </div>
                </div>
                <!-- breadcrumb -->

                <!-- row -->
                <div class="row row-sm">
                    <div class="col-xl-12 col-lg-12 col-md-12 col-xm-12">
                        <?php
                        $fatture = get_fatture_roma($a);

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
                                                <?php
                                                if (!empty($fatture)) {
                                                ?>
                                                    <div class="row">
                                                        <div class="col-sm-12 col-md-6 col-lg-6 col-xl-3">
                                                            <div class="card">
                                                                <div class="card-body">
                                                                    <div class="card-widget">
                                                                        <h6 class="mb-2">Fatturato RSC</h6>
                                                                        <h2 class="text-end"><i class="icon-size mdi mdi-poll-box   float-start text-warning text-warning-shadow"><span><small>Totale</small></span></i><span><?= getFatturatoTotZona() ?> €</span>
                                                                        </h2>
                                                                        <h2 class="text-end"><i class="icon-size mdi mdi-poll-box float-start text-primary text-warning-shadow">
                                                                                <span><small>Imponibile</small></span>
                                                                            </i><span><?= getFatturatoNettoZona() ?> €</span>
                                                                        </h2>
                                                                    </div>
                                                                </div>
                                                            </div>
                                                        </div>
                                                    </div>
                                                <?php } ?>
                                                <?php
                                                if (!empty($fatture)) {
                                                ?>
                                                    <div class="row">
                                                        <div class="col-sm-12 col-md-12 col-lg-12 col-xl-12">
                                                            <div class="card">
                                                                <div class="card-header">
                                                                    <h3 class="card-title">Fatture Roma</h3>
                                                                </div>
                                                                <div class="card-body">
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
                                                                            <tbody id="dati_fatture_anni">
                                                                                <?php
                                                                                foreach ($fatture as $fattura) {
                                                                                    $tipo_di_provvigione = $fattura['provv_percent'];
                                                                                ?>
                                                                                    <tr>
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
                                                                                                if (($tipo_di_provvigione == 1) || ($tipo_di_provvigione == 2) || ($tipo_di_provvigione == 3) || ($tipo_di_provvigione == 4)) {
                                                                                                } else {
                                                                                                    //Imposto Il tipo di provvigione
                                                                                                    $tipo_di_provvigione =   setPercentualeFattura($fattura['id_fatt'], $fattura['id_zona']);
                                                                                                }
                                                                                                //se non è stata liquidata la provvigione
                                                                                                //mostro il tipo di provvigione
                                                                                                if ($tipo_di_provvigione == 1) { ?>
                                                                                                    <a href="#" data-pk="<?= $fattura['id_fatt'] ?>">8% - 8%</a>
                                                                                                <?php
                                                                                                    $prov_agente = arrotondaEFormatta($provvigione_totale / 2) . ' €';
                                                                                                    $prov_agenzia = arrotondaEFormatta($provvigione_totale / 2) . ' €';
                                                                                                } elseif ($tipo_di_provvigione == 4) { ?>
                                                                                                    <a href="#" data-pk="<?= $fattura['id_fatt'] ?>">9% Agente - 7% Agenzia</a>
                                                                                                <?php
                                                                                                    $prov_agente = arrotondaEFormatta($provvigione_totale * 9 / 100) . ' €';
                                                                                                    $prov_agenzia = arrotondaEFormatta($provvigione_totale * 7 / 100) . ' €';
                                                                                                } elseif ($tipo_di_provvigione == 2) { ?>
                                                                                                    <a href="#" data-pk="<?= $fattura['id_fatt'] ?>">16% Agenzia</a>
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
                                                                </div>
                                                            </div>
                                                        </div>
                                                    </div>
                                                <?php } else {
                                                    echo '<div class="alert alert-warning" role="alert">
                                                                      <span class="alert-inner--icon"><i class="fe fe-info"></i></span>
                                                                  <span class="alert-inner--text"><strong>Warning!</strong> Non ci sono dati!</span>
                                                              </div>';
                                                } ?>
                                            </div>
                                            <!-- liquidazioni -->
                                            <div class="tab-pane" id="tab5">
                                                <div class="card">
                                                    <div class="card-header d-flex justify-content-between align-items-center">
                                                        <h3 class="card-title">Liquidazioni provvigione</h3>
                                                        <div>
                                                            <a href="roma-liquidazione.php" class="btn btn-primary btn-sm"><span class="fe fe-plus"> Nuova Liquidazione</span></a>
                                                        </div>
                                                    </div>
                                                    <div class="card-body">
                                                        <?php
                                                        $liquidazioni = getLiquidazioniRoma();
                                                        if (!empty($liquidazioni)) :
                                                        ?>
                                                            <div class="table-responsive">
                                                                <table class="table table-bordered border text-nowrap mb-0" id="basic-liquidazione">
                                                                    <thead>
                                                                        <tr>
                                                                            <th>Data</th>
                                                                            <th>Anno</th>
                                                                            <th>Periodo</th>
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
                                                                                <td><?= $liquidazione['anno'] ?></td>
                                                                                <td><?= date('d/m/Y', strtotime($liquidazione['periodo_start'])) ?>
                                                                                    <br>
                                                                                    <?= date('d/m/Y', strtotime($liquidazione['periodo_end'])) ?>
                                                                                </td>

                                                                                <td><?= arrotondaEFormatta($liquidazione['importo']) ?> €</td>
                                                                                <td><?= getMetodoPagamento($liquidazione['pagamento']); ?> - <?= $liquidazione['note'] ?></td>
                                                                                <td>

                                                                                    <button class="btn btn-primary btn-sm vediliquidazioneroma" data-id="<?= $liquidazione['id'] ?>"><i class="fe fe-eye" data-bs-toggle="tooltip" title="" data-bs-original-title="fe fe-eye" aria-label="fe fe-eye"></i></button>
                                                                                    <button class="btn btn-secondary btn-sm nsreferenzaroma" data-id="<?= $liquidazione['id'] ?>"><i class="fe fe-edit-3" data-bs-toggle="tooltip" title="" data-bs-original-title="fe fe-edit-3" aria-label="fe fe-edit-3"></i></button>
                                                                                    <a href="xl_roma.php?id_liquidazione=<?= $liquidazione['id'] ?>" class="btn btn-warning btn-sm" target="_blank"><i class="fe fe-file-text"></i></a>
                                                                                </td>
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
                        <?php
                        // else :
                        //     echo '<div class="alert alert-warning" role="alert">
                        //             <span class="alert-inner--icon"><i class="fe fe-info"></i></span>
                        //             <span class="alert-inner--text"><strong>Warning!</strong> Non ci sono dati!</span>
                        //         </div>';
                        // endif 

                        ?>
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
<option value="<span class=" badge="" badge-pill="" bg-primary="" me-1="" my-2="">Da liquidare<span class="badge badge-pill bg-primary me-1 my-2">Da liquidare</span></option>

</html>
<script>
    var id = '<?= $id ?>';
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
                text: '8% - 8%'
            },
            {
                value: 2,
                text: '16% Agenzia'
            },
            {
                value: 4,
                text: '9% Agente - 7% Agenzia'
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

    //Quando clicco su una .nuova-refereza mostro il div inserimentoroma
    $(document).on('click', '.nuova-referenza', function() {
        $('#inserimentoroma').show();
    });

    //Quando schiaccio su una .annofattureroma
    $(document).on('click', '.annofattureroma', function() {
        $('.annofattureroma').removeClass('btn-primary');
        $('.annofattureroma').addClass('btn-secondary');
        $(this).addClass('btn-primary');
        $(this).removeClass('btn-secondary');
        var anno = $(this).data('anno');
        var id = $(this).data('id');
        //ricarico la pagina con l'anno selezionato
        window.location.href = 'roma.php?a=' + anno;
    });
</script>