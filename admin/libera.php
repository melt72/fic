<?php
include 'partials/headerarea.php';
include 'partials/header.php';
if (isset($_GET['a'])) {
    $anno = $_GET['a'];
} else {
    $anno = date('Y');
}
if (isset($_GET['t'])) {
    $tipo = $_GET['t'];
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
                                        <li><a class="dropdown-item" href="analisi.php?a=<?= $annoCorrente ?>"><?= $annoCorrente ?></a></li>
                                    <?php
                                    }
                                    ?>
                                </ul>
                            </div>
                        </div>

                    </div>
                </div>
                <!-- row closed -->

                <!-- <div class="row row-sm">
                    <div class="col-sm-12 col-md-12">
                        <div class="card ">
                            <div class="card-body">
                                <div class="row">
                                    <div class="col-md-4">
                                        <div>Fatture</div>
                                        <div>
                                            <select class="form-control">
                                                <option value="option1">Tutte</option>
                                                <option value="option1">Fatture pagate</option>
                                                <option value="option2">Scadute</option>
                                                <option value="option3">Non scadute</option>
                                            </select>
                                        </div>

                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div> -->

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
                                            </tr>
                                        </thead>
                                        <tbody id="dati_fatture">
                                            <?php
                                            $fatture = get_fatture_anno_libera($anno);
                                            $oggi = date('Y-m-d');
                                            foreach ($fatture as $fattura) {
                                            ?>
                                                <tr>
                                                    <td><?= $fattura['nome'] ?></td>
                                                    <td><?= $fattura['num_f'] ?></td>
                                                    <td>Data: <?= date('d/m/Y', strtotime($fattura['data_f'])) ?><br>Scad: <?= date('d/m/Y', strtotime($fattura['data_scadenza'])) ?> </td>
                                                    <td><?php
                                                        if (($fattura['data_scadenza'] < $oggi) && ($fattura['status'] != 'paid')) {
                                                            echo '<span class="text-danger">' . $fattura['giorni_scaduti'] . '</span>';
                                                        }

                                                        ?></td>
                                                    <td><?= arrotondaEFormatta($fattura['imp_tot']) ?> €</td>
                                                    <td><?= arrotondaEFormatta($fattura['imp_netto']) ?> €</td>
                                                    <td><?= arrotondaEFormatta($fattura['imp_iva'])  ?> €</td>
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
    $('#basic-edittable-libera').DataTable({
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
</script>