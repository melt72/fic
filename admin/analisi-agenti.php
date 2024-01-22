<?php
include 'partials/headerarea.php';
include 'partials/header.php';
if (isset($_GET['a'])) {
    $anno = $_GET['a'];
} else {
    $anno = getAnnoRecente();
}
$colors = ['#285cf7', '#f10075', '#8500ff', '#7987a1', '#74de00', '#ff5733', '#31ff57', '#5733ff', '#ffcf33', '#33ffc4'];
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
                            <h2 class="main-content-title tx-24 mg-b-1 mg-b-lg-1">Analisi Agenti</h2>
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
                                        <li><a class="dropdown-item" href="analisi-agenti.php?a=<?= $annoCorrente ?>"><?= $annoCorrente ?></a></li>
                                    <?php
                                    }
                                    ?>
                                </ul>
                            </div>
                        </div>

                    </div>
                </div>
                <!-- row closed -->
                <?php
                $ag = analisiImponibilePerAgente($anno);
                ?>
                <div class="row row-sm">
                    <div class="col-sm-12 col-md-6">
                        <div class="card card-table-two">
                            <div class=" card-header p-0 d-flex justify-content-between">
                                <h4 class="card-title mb-1">Agenti</h4>
                                <span class="tx-12 tx-muted mb-3 ">Anno <?= $anno ?></span>

                            </div>

                            <div class="table-responsive country-table">
                                <table class="table table-striped table-bordered mb-0 text-sm-nowrap text-lg-nowrap text-xl-nowrap">
                                    <thead>
                                        <tr>
                                            <th class="wd-lg-50p">Nome</th>
                                            <th class="wd-lg-25p tx-right">Imponibile</th>
                                            <th class="wd-lg-25p tx-right">% sul Tot</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        <?php
                                        foreach ($ag as $riga) :
                                        ?>
                                            <tr>
                                                <td><?= $riga['nome_agente']; ?></td>
                                                <td class="tx-right tx-medium tx-inverse">€ <?= arrotondaEFormatta($riga['imponibile']); ?></td>
                                                <td class="tx-right tx-medium tx-inverse"><?= number_format($riga['percentuale'], 2); ?>%</td>
                                            </tr>
                                        <?php endforeach; ?>
                                    </tbody>
                                </table>
                            </div>
                        </div>


                    </div><!-- col-6 -->
                    <div class="col-sm-12 col-md-6">
                        <div class="card overflow-hidden">
                            <div class="card-body">
                                <div class="row">
                                    <div class="col-md-8">
                                        <div class="main-content-label mg-b-5">
                                            Agenti / Diretti
                                        </div>
                                        <div class="chartjs-wrapper-demo">
                                            <canvas id="chartDonut"></canvas>
                                        </div>
                                    </div>
                                    <div class="col-md-4">
                                        <?php
                                        $agdiretti = analisiImponibileAgenteDiretto($anno);

                                        $n = 0;
                                        foreach ($agdiretti as $riga) :
                                            $labels[] = $riga['tipo'];
                                            $percentuali[] = number_format($riga['percentuale'], 2);
                                        ?>
                                            <div class="d-flex">
                                                <div style="margin-top:1px;margin-right:5px">
                                                    <div style="width:4px;height:0;border:5px solid <?= $colors[$n] ?>;overflow:hidden"></div>
                                                </div><?= $riga['tipo'] ?>
                                                <div class="ms-auto my-auto">
                                                    <div class="d-flex">
                                                        <span class="me-4 my-auto"><?= number_format($riga['percentuale'], 2); ?> %</span>
                                                    </div>
                                                </div>
                                            </div>
                                        <?php
                                            $n++;
                                        endforeach; ?>
                                    </div>
                                </div>

                            </div>
                        </div>
                    </div><!-- col-6 -->
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
    var datapie = {
        labels: <?= json_encode($labels) ?>,
        datasets: [{
            data: <?= json_encode(array_map('floatval', $percentuali)) ?>,
            backgroundColor: <?= json_encode($colors) ?>,
        }]
    };
    var optionpie = {
        maintainAspectRatio: false,
        responsive: true,
        plugins: {
            legend: {
                display: false,
            },
        },
        animation: {
            animateScale: true,
            animateRotate: true
        }
    };
    // For a pie chart
    var ctx7 = document.getElementById('chartDonut');
    var myPieChart7 = new Chart(ctx7, {
        type: 'pie',
        data: datapie,
        options: optionpie
    });
</script>