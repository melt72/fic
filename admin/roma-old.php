<?php
include 'partials/headerarea.php';
include 'partials/header.php';
if (isset($_GET['id_zona'])) {
    $id_zona = $_GET['id_zona'];
} else {
    $id_zona = '';
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
                    <div class="main-dashboard-header-right">
                    </div>
                </div>
                <!-- breadcrumb -->

                <!-- row -->
                <div class="row row-sm">
                    <div class="col-xl-12 col-lg-12 col-md-12 col-xm-12">
                        <div class="card">
                            <div class="card-body">
                                <label for="provv_zona" class="form-label">Scelta zona</label>
                                <select class="form-select select2-no-search" name="provv_zona" id="provv_zona">
                                    <option>Seleziona Zona</option>
                                    <?php
                                    $zone = get_zone();
                                    foreach ($zone as $zona) { ?>
                                        <option value="<?= $zona['id_zona'] ?>" <?php
                                                                                if (isset($id_zona)) {
                                                                                    if ($id_zona == $zona['id_zona']) {
                                                                                        echo ' selected';
                                                                                    }
                                                                                } ?>>
                                            <?= $zona['nome_zona'] ?></option>
                                    <?php }
                                    ?>
                                </select>
                            </div>
                        </div>
                        <?php
                        $fatture = get_fatture_zona($id_zona, 'recenti');
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
                                                        <!-- <div class="col-sm-12 col-md-6 col-lg-6 col-xl-3">
                                                            <div class="card">
                                                                <div class="card-body">
                                                                    <div class="card-widget">
                                                                        <h6 class="mb-2">Provvigione Totale</h6>
                                                                        <h2 class="text-end"><i class="icon-size mdi mdi-poll-box float-start text-warning text-warning-shadow">
                                                                                <span><small>Liquidata</small></span>
                                                                            </i><span><?= getProvvigioneAgente($id_agente, 'totale_liquidata') ?> €</span>
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
                                                        </div> -->
                                                    </div>
                                                    <div class="row">
                                                        <div class="col-sm-12 col-md-12 col-lg-12 col-xl-12">
                                                            <div class="card">
                                                                <div class="card-header">
                                                                    <h3 class="card-title">Fatture Zona</h3>
                                                                </div>
                                                                <div class="card-body">
                                                                    <div class="pd-15 background mg-b-5">
                                                                        <?php
                                                                        //prendo gli anni dei prodotti di marketing
                                                                        $anni =  getAnniFattureZone($id_zona);
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
                                                                                    <th>Tipo Prov</th>
                                                                                    <th>Prov Agente €</th>
                                                                                    <th>Prov Agenzia €</th>
                                                                                    <th>Stato</th>
                                                                                </tr>
                                                                            </thead>
                                                                            <tbody id="dati_fatture">
                                                                                <?php

                                                                                foreach ($fatture as $fattura) {
                                                                                ?>
                                                                                    <tr>
                                                                                        <td>Bella</td>
                                                                                        <td><?= $fattura['num_f'] ?></td>
                                                                                        <td>Data: <?= date('d/m/Y', strtotime($fattura['data_f'])) ?><br>Scad: <?= date('d/m/Y', strtotime($fattura['data_scadenza'])) ?> </td>
                                                                                        <td><?= arrotondaEFormatta($fattura['imp_tot']) ?> €<br>
                                                                                            (IVA: <?= arrotondaEFormatta($fattura['imp_iva'])  ?> €)
                                                                                        </td>
                                                                                        <td><?= arrotondaEFormatta($fattura['imp_netto']) ?> €</td>
                                                                                        <td><?php
                                                                                            $provvigione_totale = $fattura['imp_netto'] * 16 / 100;
                                                                                            if ($fattura['id_liquidazione'] != '') : ?>
                                                                                                <?= $fattura['provv_percent'] ?> %
                                                                                                <?php else :
                                                                                                //se non è stata liquidata la provvigione
                                                                                                //mostro il tipo di provvigione
                                                                                                if ($fattura['provv_percent'] == 1) { ?>
                                                                                                    <a href="#" data-pk="<?= $fattura['id_fatt'] ?>">50% - 50%</a>
                                                                                                <?php
                                                                                                    $prov_agente = arrotondaEFormatta($provvigione_totale / 2) . ' €';
                                                                                                    $prov_agenzia = arrotondaEFormatta($provvigione_totale / 2) . ' €';
                                                                                                } elseif ($fattura['provv_percent'] == 2) { ?>
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
                                                                <button class="btn btn-primary btn-sm liquidazione_zona"><span class="fe fe-plus"> Nuova Liquidazione</span></button>
                                                            </div>
                                                        </div>
                                                        <div class="card-body">
                                                            <?php
                                                            $liquidazioni = getLiquidazioniZona($id_zona);
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
    var id = '<?= $id_zona ?>';
    $('#basic-edittable').DataTable({
        language: {
            searchPlaceholder: 'Search...',
            sSearch: '',
        }
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