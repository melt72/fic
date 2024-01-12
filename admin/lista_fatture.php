<?php
include 'partials/headerarea.php';
include 'partials/header.php';
if (isset($_GET['a'])) {
    $anno = $_GET['a'];
} else {
    $anno = getAnnoRecente();
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
                    <div class="my-auto">
                        <h4 class="page-title">Lista Fatture</h4>
                    </div>
                    <div class="d-flex my-xl-auto right-content align-items-center">
                        <?php
                        $anni_disponibili = getAnniFatture();

                        ?>
                        <div class="mb-xl-0">
                            <div class="dropdown">
                                <button class="btn btn-primary dropdown-toggle" type="button" id="dropdownMenuDate" data-bs-toggle="dropdown" aria-expanded="false">
                                    <?= $anno ?>
                                </button>
                                <ul class="dropdown-menu" aria-labelledby="dropdownMenuDate">
                                    <?php
                                    foreach ($anni_disponibili as $anno_disp) {
                                        $annoCorrente = $anno_disp['anno'];
                                    ?>
                                        <li><a class="dropdown-item" href="lista_fatture.php?a=<?= $annoCorrente ?>"><?= $annoCorrente ?></a></li>
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
                    <div class="col-sm-12 col-md-12 col-lg-12 col-xl-12">
                        <div class="card">
                            <div class="card-header">
                                <h3 class="card-title">Fatture agente</h3>
                            </div>
                            <div class="card-body">
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
                                                <th>Agente</th>
                                                <th>Stato</th>
                                            </tr>
                                        </thead>
                                        <tbody id="dati_fatture">
                                            <?php
                                            $fatture = get_fatture_anno($anno);
                                            foreach ($fatture as $fattura) {
                                            ?>
                                                <tr>
                                                    <td><?= $fattura['nome'] ?></td>
                                                    <td><?= $fattura['num_f'] ?></td>
                                                    <td>Data: <?= date('d/m/Y', strtotime($fattura['data_f'])) ?><br>Scad: <?= date('d/m/Y', strtotime($fattura['data_scadenza'])) ?> </td>
                                                    <td><?= arrotondaEFormatta($fattura['imp_tot']) ?> €</td>
                                                    <td><?= arrotondaEFormatta($fattura['imp_netto']) ?> €</td>
                                                    <td><?= arrotondaEFormatta($fattura['imp_iva'])  ?> €</td>
                                                    <td>
                                                        <?php
                                                        if ($fattura['id_liquidazione'] != '') : ?>
                                                            --
                                                        <?php else : ?>
                                                            <a href="#" data-pk="<?= $fattura['id_fatt'] ?>"><?= getDatiAgente($fattura['sigla']) ?></a>
                                                        <?php endif ?>
                                                    </td>
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
    try {
        $('#basic-edittable a').editable({
            type: 'select',
            name: 'sigla',
            value: 'a',
            source: [{
                    value: 'a',
                    text: 'Seleziona'
                },
                {
                    value: '0',
                    text: '--'
                },
                <?php
                $ag = get_agenti_totali();
                foreach ($ag as $a) {
                    echo "{value: '" . $a['sigla'] . "', text: '" . $a['nome_agente'] . "'},";
                }
                ?>
            ],
            name: 'status',
            url: 'include/agente_associa.php',
            title: 'Agente',
            success: function(response, newValue) {
                var pk = $(this).data('pk');
            }
        });
        $('#basic-edittable').DataTable({
            language: {
                searchPlaceholder: 'Search...',
                sSearch: '',
            }
        });

    } catch (error) {
        console.error("Errore nello script JavaScript:", error);
    }
</script>