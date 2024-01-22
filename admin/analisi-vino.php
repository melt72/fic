<?php
include 'partials/headerarea.php';
include 'partials/header.php';
if (isset($_GET['a'])) {
    $anno = $_GET['a'];
} else {
    $anno = getAnnoRecente();
}

$varieta_vino = 'cabernet';

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
                            <h2 class="main-content-title tx-24 mg-b-1 mg-b-lg-1">Analisi Vini</h2>
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
                                        <li><a class="dropdown-item" href="analisi-vino.php?a=<?= $annoCorrente ?>"><?= $annoCorrente ?></a></li>
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
                $bottiglie = analisiBottiglie($anno);
                ?>
                <!-- row -->
                <div class="row row-sm">
                    <div class="col-xl-3 col-lg-6 col-md-6 col-xm-12">
                        <div class="card overflow-hidden sales-card bg-primary-gradient">
                            <div class="px-3 pt-3  pb-2 pt-0">
                                <div class="">
                                    <h6 class="mb-3 tx-12 text-white">TOTALE BOTTIGLIE VENDUTE</h6>
                                </div>
                                <div class="pb-0 mt-0">
                                    <div class="d-flex">
                                        <div class="">
                                            <h4 class="tx-20 fw-bold mb-1 text-white">BT <?= $bottiglie ?></h4>

                                        </div>
                                        <span class="float-end my-auto ms-auto">

                                        </span>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                    <?php
                    $tipo = analisiBottigliePerTipo($anno); ?>
                    <div class="col-xl-3 col-lg-6 col-md-6 col-xm-12">
                        <div class="card overflow-hidden sales-card bg-success-gradient">
                            <div class="px-3 pt-3  pb-2 pt-0">
                                <div class="">
                                    <h6 class="mb-3 tx-12 text-white">Bottiglie 75 cl</h6>
                                </div>
                                <div class="pb-0 mt-0">
                                    <div class="d-flex">
                                        <div class="">
                                            <h4 class="tx-20 fw-bold mb-1 text-white">BT <?= $tipo['quantita_75cl'] ?></h4>

                                        </div>
                                        <span class="float-end my-auto ms-auto">

                                            <span class="text-white op-7"> <?= number_format($tipo['percentuale_75cl'], 2) ?>%</span>
                                        </span>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="col-xl-3 col-lg-6 col-md-6 col-xm-12">
                        <div class="card overflow-hidden sales-card bg-success-gradient">
                            <div class="px-3 pt-3  pb-2 pt-0">
                                <div class="">
                                    <h6 class="mb-3 tx-12 text-white">Bottiglie 150 cl</h6>
                                </div>
                                <div class="pb-0 mt-0">
                                    <div class="d-flex">
                                        <div class="">
                                            <h4 class="tx-20 fw-bold mb-1 text-white">BT <?= $tipo['quantita_150cl'] ?></h4>
                                        </div>
                                        <span class="float-end my-auto ms-auto">
                                            <span class="text-white op-7"> <?= number_format($tipo['percentuale_150cl'], 2) ?>%</span>
                                        </span>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>

                </div>
                <!-- row closed -->
                <!-- row -->
                <?php
                $varieta_totale = analisiBottigliePerVarietaId($anno);

                ?>
                <div class="row row-sm">
                    <div class="col-md-6 col-lg-6 col-xl-6">
                        <div class="card card-dashboard-eight pb-2">
                            <h6 class="card-title text-danger">Varietà di vini ROSSI</h6><span class="d-block mg-b-10 text-muted tx-12">Bottiglie vendute in base alla varietà</span>

                            <div class="table-responsive country-table">
                                <table class="table table-striped table-bordered mb-0 text-sm-nowrap text-lg-nowrap text-xl-nowrap">
                                    <tbody>
                                        <?php
                                        $rossi =  analisiBottigliePerTipoAnno($anno, 'rosso');

                                        //Per ogni elemento dell'array $rossi
                                        foreach ($rossi as $varieta_vino) {
                                            //Calcolo la quantità totale di bottiglie per ogni varietà
                                            $quantita = $varieta_vino['quantita_prodotto'];
                                            //Calcolo la percentuale di bottiglie per ogni varietà
                                            $percentuale = number_format($quantita / $bottiglie * 100, 2);

                                        ?>
                                            <tr>
                                                <td><?= strtoupper($varieta_vino['varieta'])  ?></td>
                                                <td class="tx-right tx-medium tx-inverse"><?= $quantita ?> BT</td>
                                                <td class="tx-right tx-medium tx-inverse"><?= $percentuale ?>%</td>
                                            </tr>
                                        <?php
                                        }
                                        ?>
                                    </tbody>
                                </table>
                            </div>
                        </div>
                    </div>
                    <div class=" col-md-6 col-lg-6 col-xl-6">
                        <div class="card card-dashboard-eight pb-2">
                            <h6 class="card-title text-warning">Varietà di vini BIANCHI</h6><span class="d-block mg-b-10 text-muted tx-12">Bottiglie vendute in base alla varietà</span>
                            <div class="table-responsive country-table">
                                <table class="table table-striped table-bordered mb-0 text-sm-nowrap text-lg-nowrap text-xl-nowrap">
                                    <tbody>
                                        <?php
                                        $bianchi =  analisiBottigliePerTipoAnno($anno, 'bianco');
                                        //Per ogni elemento dell'array $bianchi
                                        foreach ($bianchi as $varieta_vino) {
                                            //Calcolo la quantità totale di bottiglie per ogni varietà
                                            $quantita = $varieta_vino['quantita_prodotto'];
                                            //Calcolo la percentuale di bottiglie per ogni varietà
                                            $percentuale = number_format($quantita / $bottiglie * 100, 2);
                                        ?>
                                            <tr>
                                                <td><?= strtoupper($varieta_vino['varieta']) ?></td>
                                                <td class="tx-right tx-medium tx-inverse"><?= $quantita ?> BT</td>
                                                <td class="tx-right tx-medium tx-inverse"><?= $percentuale ?>%</td>
                                            </tr>
                                        <?php
                                        }
                                        ?>
                                    </tbody>
                                </table>
                            </div>
                        </div>
                    </div>
                </div>
                <!-- row closed -->
                <!-- row -->
                <?php
                //     $aa = analisiBottigliePerVarietaId($anno);
                $array = array('cabernet', 'filorosso', 'pinot nero', 'chardonnay',  'friulano', 'malvasia', 'pinot grigio',  'ribolla', 'sauvignon');
                ?>
                <div class="row row-sm">
                    <?php
                    foreach ($array as $varieta_vino) {
                        $varieta = $varieta_totale[$varieta_vino];
                        $colore = in_array($varieta_vino, ['cabernet', 'filorosso', 'pinot nero']) ? 'text-danger' : 'text-warning';
                    ?>
                        <div class=" col-md-4 col-lg-4 col-xl-4">
                            <div class="card">
                                <div class="card-header">
                                    <div class="d-flex justify-content-between">
                                        <h3 class="card-title <?= $colore ?>"><?= $varieta_vino ?></h3>
                                    </div>
                                </div><!-- card-header -->
                                <div class="card-body p-0">
                                    <?php foreach ($varieta as $riga) : ?>
                                        <div class="browser-stats">
                                            <div class="d-flex align-items-center item  border-bottom">
                                                <div class="d-flex">
                                                    <div class="">
                                                        <h6 class=""><?= $riga['nome']; ?></h6>
                                                    </div>
                                                </div>
                                                <div class="ms-auto my-auto">
                                                    <div class="d-flex">
                                                        <span class="me-4 my-auto"><?= $riga['quantita']; ?> BT</span>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                    <?php endforeach; ?>
                                </div>
                            </div>
                        </div>
                    <?php } ?>
                </div>
                <!-- row closed -->
                <?php
                $distribuzione = analisiBottigliePerMeseVarieta($anno, 'cabernet'); ?>
                <!-- row -->
                <div class="row row-sm">
                    <div class="col-sm-12 col-md-12">
                        <div class="card overflow-hidden">
                            <div class="card-body">
                                <div class="main-content-label mg-b-5 d-flex justify-content-between align-items-center">
                                    Stagionalità
                                    <div>
                                        <div class="dropdown">
                                            <button class="btn btn-primary dropdown-toggle" type="button" id="dropdownMenuVarieta" data-bs-toggle="dropdown" aria-expanded="false">
                                                Cabernet
                                            </button>
                                            <ul id="scelta-varieta" class="dropdown-menu" aria-labelledby="dropdownMenuVarieta">
                                                <li><button class="dropdown-item " data-anno="<?= $anno ?>" data-varieta="cabernet">Cabernet</button></li>
                                                <li><button class="dropdown-item " data-anno="<?= $anno ?>" data-varieta="chardonnay">Chardonay</button></li>
                                                <li><button class="dropdown-item " data-anno="<?= $anno ?>" data-varieta="filorosso">Filorosso</button></li>
                                                <li><button class="dropdown-item " data-anno="<?= $anno ?>" data-varieta="nero">Pinot nero</button></li>
                                                <li><button class="dropdown-item " data-anno="<?= $anno ?>" data-varieta="grigio">Pinot grigio</button></li>
                                                <li><button class="dropdown-item " data-anno="<?= $anno ?>" data-varieta="sauvignon">Sauvignon</button></li>
                                                <li><button class="dropdown-item " data-anno="<?= $anno ?>" data-varieta="friulano">Friulano</button></li>
                                                <li><button class="dropdown-item " data-anno="<?= $anno ?>" data-varieta="malvasia">Malvasia</button></li>
                                                <li><button class="dropdown-item " data-anno="<?= $anno ?>" data-varieta="ribolla">Ribolla</button></li>
                                            </ul>
                                        </div>
                                    </div>
                                </div>
                                <p class=" mg-b-20">Ripartizione delle vendite in base al mese</p>
                                <div class="chartjs-wrapper-demo">
                                    <canvas id="chartLine1" width="494" height="300" style="display: block; box-sizing: border-box; height: 300px; width: 494px;"></canvas>
                                </div>
                            </div>
                        </div>
                    </div><!-- col-6 -->
                </div>

                <!-- /Container -->
            </div>
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
        indexbar2([<?= implode(", ", $distribuzione) ?>]);
    });

    $('#scelta-varieta').on('click', 'button', function() {
        var anno = $(this).data('anno');
        var varieta = $(this).data('varieta');
        // Elimino tutti i grafici presenti
        Chart.helpers.each(Chart.instances, function(instance) {
            instance.destroy();
        });
        // Imposto il bottone dropdownMenuVarieta con il valore della variabile varieta
        $('#dropdownMenuVarieta').html(varieta);

        // Invio anni e varietà alla pagina grafico-vino.php Tramite ajax per ottenere i dati
        $.ajax({
            url: 'include/grafico_vino.php',
            type: 'POST',
            data: {
                anno: anno,
                varieta: varieta
            },
            success: function(data) {
                var datiNumerici = JSON.parse(data).map(Number);
                indexbar2(datiNumerici);
            }
        });
    });

    /* Resto del codice rimane invariato */

    /*closed Apex charts(#bar)*/
    function indexbar2(dati) {
        // Crea il nuovo grafico

        var ctx8 = document.getElementById('chartLine1');
        new Chart(ctx8, {
            type: 'line',
            data: {
                labels: ['Jan', 'Feb', 'Mar', 'Apr', 'May', 'Jun', 'July', 'Aug', 'Sep', 'Oct', 'Nov', 'Dec'],
                datasets: [{
                    data: dati,
                    borderColor: '#f7557a ',
                    borderWidth: 1,
                    fill: false
                }]
            },
            options: {
                maintainAspectRatio: false,
                plugins: {
                    legend: {
                        display: false,
                        labels: {
                            display: false
                        }
                    },
                },
                scales: {
                    y: {
                        suggestedMax: 80,
                        ticks: {
                            beginAtZero: true,
                            fontSize: 10,
                            fontColor: "rgba(171, 167, 167,0.9)",
                        },
                        grid: {
                            display: true,
                            color: 'rgba(171, 167, 167,0.2)',
                            drawBorder: false
                        },
                    },
                    x: {
                        ticks: {
                            beginAtZero: true,
                            fontSize: 11,
                            fontColor: "rgba(171, 167, 167,0.9)",
                        },
                        grid: {
                            display: true,
                            color: 'rgba(171, 167, 167,0.2)',
                            drawBorder: false
                        },
                    }
                }
            }
        });
    }
</script>