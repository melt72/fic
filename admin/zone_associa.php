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
                            <h2 class="main-content-title tx-24 mg-b-1 mg-b-lg-1">Zone Roma</h2>
                        </div>
                    </div>
                    <div class="main-dashboard-header-right">

                    </div>
                    <!-- breadcrumb -->
                </div>
                <!-- row -->
                <div class="row row-sm">
                    <div class="col-md-12">
                        <div class="card" id="tabs-style4">
                            <div class="card-header  d-flex justify-content-between align-items-center">
                                <h3 class="card-title">Definizione delle zone</h3>
                            </div>
                            <div class="card-body">
                                <div class="table-responsive">
                                    <table class="table border-top-0 table-bordered text-nowrap border-bottom" id="clienti-datatable">
                                        <thead>
                                            <tr>
                                                <th class="wd-15p border-bottom-0">Nome cliente</th>
                                                <th class="wd-15p border-bottom-0">Città</th>
                                                <th class="wd-20p border-bottom-0">Prov</th>
                                                <th class="wd-15p border-bottom-0">Zona</th>
                                            </tr>
                                        </thead>
                                        <tbody>
                                            <?php
                                            $clienti = get_clienti_totali('RM');
                                            if ($clienti) :
                                                foreach ($clienti as $cliente) :
                                            ?>
                                                    <tr>
                                                        <td><?= $cliente['nome'] ?></td>
                                                        <td><?= $cliente['citta'] ?></td>
                                                        <td><?= $cliente['provincia'] ?></td>
                                                        <td>
                                                            <a href="#" data-pk="<?= $cliente['id'] ?>"><?= cliente_associato($cliente['id']) ?></a>
                                                        </td>
                                                    </tr>
                                            <?php
                                                endforeach;
                                            endif;
                                            ?>

                                        </tbody>
                                    </table>
                                </div>
                            </div>
                            <!-- /div -->
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
        $('#clienti-datatable a').editable({
            type: 'select',
            name: 'zona',
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
                $ag = get_zone();
                foreach ($ag as $a) {
                    echo "{value: '" . $a['id_zona'] . "', text: '" . $a['nome_zona'] . "'},";
                }
                ?>
            ],
            name: 'status',
            url: 'include/roma_associa.php',
            title: 'Seleziona la zona',
            success: function(response, newValue) {
                var pk = $(this).data('pk');
            }
        });
        $('#clienti-datatable').DataTable({
            language: {
                searchPlaceholder: 'Search...',
                sSearch: '',
            }
        });

    } catch (error) {
        console.error("Errore nello script JavaScript:", error);
    }
</script>