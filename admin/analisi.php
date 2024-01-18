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
                    <div class="left-content">
                        <div>
                            <h2 class="main-content-title tx-24 mg-b-1 mg-b-lg-1">Analisi Imponibile</h2>
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
                <!-- breadcrumb -->
                <?php
                $imponibile = analisiTotale($anno);
                ?>
                <!-- row -->
                <div class="row row-sm">
                    <div class="col-xl-3 col-lg-6 col-md-6 col-xm-12">
                        <div class="card overflow-hidden sales-card bg-primary-gradient">
                            <div class="px-3 pt-3  pb-2 pt-0">
                                <div class="">
                                    <h6 class="mb-3 tx-12 text-white">IMPONIBILE TOTALE</h6>
                                </div>
                                <div class="pb-0 mt-0">
                                    <div class="d-flex">
                                        <div class="">
                                            <h4 class="tx-20 fw-bold mb-1 text-white">€ <?= $imponibile['totale'] ?></h4>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="col-xl-3 col-lg-6 col-md-6 col-xm-12">
                        <div class="card overflow-hidden sales-card bg-success-gradient">
                            <div class="px-3 pt-3  pb-2 pt-0">
                                <div class="">
                                    <h6 class="mb-3 tx-12 text-white">Imponibile incassato</h6>
                                </div>
                                <div class="pb-0 mt-0">
                                    <div class="d-flex">
                                        <div class="">
                                            <h4 class="tx-20 fw-bold mb-1 text-white">€ <?= $imponibile['incassato'] ?></h4>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="col-xl-3 col-lg-6 col-md-6 col-xm-12">
                        <div class="card overflow-hidden sales-card bg-warning-gradient">
                            <div class="px-3 pt-3  pb-2 pt-0">
                                <div class="">
                                    <h6 class="mb-3 tx-12 text-white">Imponibile da incassare</h6>
                                </div>
                                <div class="pb-0 mt-0">
                                    <div class="d-flex">
                                        <div class="">
                                            <h4 class="tx-20 fw-bold mb-1 text-white">€ <?= $imponibile['da_incassare'] ?></h4>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="col-xl-3 col-lg-6 col-md-6 col-xm-12">
                        <div class="card overflow-hidden sales-card bg-danger-gradient">
                            <div class="px-3 pt-3  pb-2 pt-0">
                                <div class="">
                                    <h6 class="mb-3 tx-12 text-white">Imponibile scaduto</h6>
                                </div>
                                <div class="pb-0 mt-0">
                                    <div class="d-flex">
                                        <div class="">
                                            <h4 class="tx-20 fw-bold mb-1 text-white">€ <?= $imponibile['non_pagato_scaduto'] ?></h4>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                <!-- row closed -->
                <!-- row -->
                <?php
                $trimestre = analisiImponibileTrimestre($anno);
                ?>
                <div class="row row-sm">
                    <div class="col-xl-3 col-lg-6 col-md-6 col-xm-12">
                        <div class="card overflow-hidden sales-card">
                            <div class="px-3 pt-3  pb-2 pt-0">
                                <div class="">
                                    <h6 class="mb-3 tx-12 text-white">I trimestre</h6>
                                </div>
                                <div class="pb-0 mt-0">
                                    <div class="d-flex">
                                        <div class="">
                                            <h4 class="tx-20 fw-bold mb-1 text-white">€ <?= $trimestre['1'] ?></h4>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="col-xl-3 col-lg-6 col-md-6 col-xm-12">
                        <div class="card overflow-hidden sales-card">
                            <div class="px-3 pt-3  pb-2 pt-0">
                                <div class="">
                                    <h6 class="mb-3 tx-12 text-white">II Trimestre</h6>
                                </div>
                                <div class="pb-0 mt-0">
                                    <div class="d-flex">
                                        <div class="">
                                            <h4 class="tx-20 fw-bold mb-1 text-white">€ <?= $trimestre['2'] ?></h4>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="col-xl-3 col-lg-6 col-md-6 col-xm-12">
                        <div class="card overflow-hidden sales-card">
                            <div class="px-3 pt-3  pb-2 pt-0">
                                <div class="">
                                    <h6 class="mb-3 tx-12 text-white">III Trimestre</h6>
                                </div>
                                <div class="pb-0 mt-0">
                                    <div class="d-flex">
                                        <div class="">
                                            <h4 class="tx-20 fw-bold mb-1 text-white">€ <?= $trimestre['3'] ?></h4>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="col-xl-3 col-lg-6 col-md-6 col-xm-12">
                        <div class="card overflow-hidden sales-card">
                            <div class="px-3 pt-3  pb-2 pt-0">
                                <div class="">
                                    <h6 class="mb-3 tx-12 text-white">IV Trimestre</h6>
                                </div>
                                <div class="pb-0 mt-0">
                                    <div class="d-flex">
                                        <div class="">
                                            <h4 class="tx-20 fw-bold mb-1 text-white">€ <?= $trimestre['4'] ?></h4>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                <!-- row closed -->
                <!-- row opened -->
                <div class="row row-sm">
                    <div class="col-md-12 col-lg-12 col-xl-12">
                        <div class="card">
                            <div class="card-header bg-transparent pd-b-0 pd-t-20 bd-b-0">
                                <div class="d-flex justify-content-between">
                                    <h4 class="card-title mb-0">Imponibile mensile</h4>
                                </div>
                            </div>
                            <?php
                            $analisiMese = analisiImponibile($anno);
                            $analisiMesePrecedente = analisiImponibile($anno - 1);
                            ?>
                            <div class="card-body b-p-apex">
                                <div class="total-revenue">
                                    <div>

                                        <label><span class="bg-primary"></span>Imponibile <?= $anno ?></label>
                                    </div>
                                    <div>

                                        <label><span class="bg-warning"></span>Imponibile <?= $anno - 1 ?></label>
                                    </div>
                                </div>
                                <div id="bar" class="sales-bar mt-4"></div>
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
    $(document).ready(function() {
        indexbar();
    });

    /* Apexcharts (#bar) */
    function indexbar() {
        var optionsBar = {
            chart: {
                height: 249,
                responsive: 'true',
                type: 'bar',
                toolbar: {
                    show: false,
                },
                fontFamily: 'Nunito, sans-serif',
            },
            colors: [myVarVal, '#f7a556'],
            plotOptions: {
                bar: {
                    dataLabels: {
                        enabled: false
                    },
                    columnWidth: '42%',
                    endingShape: 'rounded',
                }
            },
            dataLabels: {
                enabled: false
            },
            grid: {
                show: true,
                borderColor: '#f3f3f3',
            },
            stroke: {
                show: true,
                width: 2,
                endingShape: 'rounded',
                colors: ['transparent'],
            },
            responsive: [{
                enable: 'true',
                breakpoint: 576,
                options: {
                    stroke: {
                        show: true,
                        width: 1,
                        endingShape: 'rounded',
                        colors: ['transparent'],
                    },
                },

            }],
            series: [{
                name: 'Imponibile',
                data: [<?= implode(", ",  $analisiMese) ?>]
            }, {
                name: 'Imponibile <?= $anno - 1 ?>',
                data: [<?= implode(", ",  $analisiMesePrecedente) ?>]
            }],
            xaxis: {
                categories: ['Gen', 'Feb', 'Mar', 'Apr', 'Mag', 'Giu', 'Lug', 'Ago', 'Set', 'Ott', 'Nov', 'Dic'],
            },
            fill: {
                opacity: 1
            },
            legend: {
                show: false,
                floating: true,
                position: 'top',
                horizontalAlign: 'left',


            },

            tooltip: {
                y: {
                    formatter: function(val) {
                        return "€ " + val + " "
                    }
                }
            }
        }
        document.querySelector('#bar').innerHTML = ""
        new ApexCharts(document.querySelector('#bar'), optionsBar).render();
    }
    /*closed Apex charts(#bar)*/

    /*--- Map ---*/
</script>