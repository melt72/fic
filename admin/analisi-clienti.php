<?php
include 'partials/headerarea.php';
include 'partials/header.php';
if (isset($_GET['c'])) {
    $id_del_cliente = $_GET['c'];

    $dati_cliente = getClienteById($id_del_cliente);
    if (isset($_GET['a'])) {
        $annoricerca = $_GET['a'];
    } else {
        $annoricerca = getAnnoRecente();
    }
} else {
    $dati_cliente = '';
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


                    </div>
                </div>
                <!-- row closed -->

                <div class="row row-sm">
                    <div class="col-sm-12 col-md-12">
                        <div class="card ">
                            <div class="card-body">
                                <div class="row">
                                    <div class="col-md-6">
                                        <div>Nome Cliente</div>
                                        <div>
                                            <input type="text" class="form-control" id="txt_search" name="txt_search" value="<?= (!empty($dati_cliente)) ? $dati_cliente['nome'] : '' ?>">
                                        </div>
                                        <ul id="searchResult"></ul>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div><!-- col-6 -->
                </div>
                <?php if (!empty($dati_cliente)) :
                    $imponibile = imponibilePerClienteTotale($id_del_cliente); ?>
                    <div class="row row-sm">
                        <div class="col-md-6">
                            <div class="row">
                                <div class="col-xl-6 col-lg-6 col-md-6 col-xm-12">
                                    <div class="card overflow-hidden sales-card bg-primary-gradient">
                                        <div class="px-3 pt-3  pb-2 pt-0">
                                            <div class="">
                                                <h6 class="mb-3 tx-12 text-white">IMPONIBILE TOTALE CLIENTE</h6>
                                            </div>
                                            <div class="pb-0 mt-0">
                                                <div class="d-flex">
                                                    <div class="">
                                                        <h4 class="tx-20 fw-bold mb-1 text-white">€ <?= $imponibile['totale'] ?></h4>
                                                        <p class="mb-0 tx-12 text-white op-7">Totale complessivo generato</p>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                                <div class="col-xl-6 col-lg-6 col-md-6 col-xm-12">
                                    <div class="card overflow-hidden sales-card bg-success-gradient">
                                        <div class="px-3 pt-3  pb-2 pt-0">
                                            <div class="">
                                                <h6 class="mb-3 tx-12 text-white">TOTALE PAGATO</h6>
                                            </div>
                                            <div class="pb-0 mt-0">
                                                <div class="d-flex">
                                                    <div class="">
                                                        <h4 class="tx-20 fw-bold mb-1 text-white">€ <?= $imponibile['totale_pagato'] ?></h4>
                                                        <p class="mb-0 tx-12 text-white op-7">Totale complessivo pagato</p>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                                <div class="col-xl-6 col-lg-6 col-md-6 col-xm-12">
                                    <div class="card overflow-hidden sales-card bg-warning-gradient">
                                        <div class="px-3 pt-3  pb-2 pt-0">
                                            <div class="">
                                                <h6 class="mb-3 tx-12 text-white">TOTALE NON PAGATO</h6>

                                            </div>
                                            <div class="pb-0 mt-0">
                                                <div class="d-flex">
                                                    <div class="">
                                                        <h4 class="tx-20 fw-bold mb-1 text-white">€ <?= $imponibile['totale_non_pagato'] ?></h4>
                                                        <p class="mb-0 tx-12 text-white op-7">Alla data di oggi</p>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                                <div class="col-xl-6 col-lg-6 col-md-6 col-xm-12">
                                    <div class="card overflow-hidden sales-card bg-danger-gradient">
                                        <div class="px-3 pt-3  pb-2 pt-0">
                                            <div class="">
                                                <h6 class="mb-3 tx-12 text-white">TOTALE SCADUTO</h6>
                                            </div>
                                            <div class="pb-0 mt-0">
                                                <div class="d-flex">
                                                    <div class="">
                                                        <h4 class="tx-20 fw-bold mb-1 text-white">€ <?= $imponibile['totale_non_pagato_scaduto'] ?></h4>
                                                        <p class="mb-0 tx-12 text-white op-7">Alla data di oggi</p>
                                                    </div><span class="float-end my-auto ms-auto">
                                                        <button class="btn btn-primary btn-sm vedi_scaduti_cliente" data-id="<?= $id_del_cliente ?>"><i class="fe fe-eye text-white"></i></button>
                                                    </span>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="row">
                                <div class="col-xl-6 col-md-6 col-lg-6">
                                    <div class="card">
                                        <div class="card-header pb-0">
                                            <h3 class="card-title mb-2">Importanza cliente</h3>
                                            <p class="tx-12 mb-0 text-muted">Imponibile generato negli ultimi 12 mesi</p>
                                        </div>
                                        <div class="card-body sales-info ot-0 pb-0 pt-0">
                                            <div id="chart1" class="ht-100"></div>
                                        </div>
                                    </div>
                                </div>
                                <?php

                                ?>
                                <div class="col-xl-6 col-md-6 col-lg-6">
                                    <div class="card">
                                        <div class="card-header pb-0">
                                            <h3 class="card-title mb-2">Affidabilità cliente</h3>
                                            <p class="tx-12 mb-0 text-muted">Puntualità nel pagamento delle fatture</p>
                                        </div>
                                        <div class="card-body sales-info ot-0 pb-0 pt-0">
                                            <div id="chart2" class=" ht-100"></div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <!-- row closed -->
                        <?php
                        if (NdcCliente($id_del_cliente)) {

                        ?>
                            <div class="row row-sm">
                                <div class="col-sm-12 col-md-12">
                                    <div class="alert alert-solid-warning" role="alert">
                                        <button aria-label="Close" class="close" data-bs-dismiss="alert" type="button">
                                            <span aria-hidden="true">×</span></button>
                                        <strong>Warning!</strong> Il valore degli imponibili è stato rettificato dall'emissione di note di credito
                                    </div>
                                </div>
                            </div>
                        <?php
                        }
                        ?>
                        <div class="row row-sm">
                            <div class="col-md-12 col-lg-12 col-xl-12">
                                <div class="card">
                                    <div class="card-header bg-transparent pd-b-0 pd-t-20 bd-b-0">
                                        <div class="d-flex justify-content-between">
                                            <h4 class="card-title mb-0">Imponibile mensile</h4>
                                        </div>
                                    </div>
                                    <?php
                                    $anni_dati = anniConFatturePerCliente($id_del_cliente);
                                    ?>
                                    <div class="card-body b-p-apex">
                                        <div class="total-revenue">
                                            <?php
                                            //Vorrei che contiene I colori di background
                                            $colori = array('bg-primary', 'bg-success', 'bg-warning', 'bg-danger', 'bg-info', 'bg-purple', 'bg-pink', 'bg-dark', 'bg-secondary', 'bg-light', 'bg-primary', 'bg-success', 'bg-warning', 'bg-danger', 'bg-info', 'bg-purple', 'bg-pink', 'bg-dark', 'bg-secondary', 'bg-light');
                                            $i = 0;
                                            foreach ($anni_dati as $anno) :
                                            ?>
                                                <div>
                                                    <label><span class="<?= $colori[$i] ?>"></span>Imponibile <?= $anno ?></label>
                                                </div>
                                            <?php
                                                $i++;
                                            endforeach
                                            ?>
                                        </div>
                                        <div id="bar" class="sales-bar mt-4"></div>
                                    </div>
                                </div>
                            </div>
                        </div>
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
                                            <?= $annoricerca ?>
                                        </button>
                                        <ul class="dropdown-menu" aria-labelledby="dropdownMenuDate">
                                            <?php
                                            foreach ($anni_disponibili as $anno_disp) {
                                                $annoCorrente = $anno_disp['anno'];
                                            ?>
                                                <li><a class="dropdown-item" href="analisi-clienti.php?c=<?= $id_del_cliente ?>&a=<?= $annoCorrente ?>"><?= $annoCorrente ?></a></li>
                                            <?php
                                            }
                                            ?>
                                        </ul>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <?php
                        $totale_bottiglie =  analisiBottigliePerClienteAnno($id_del_cliente, $annoricerca);
                        ?>
                        <!-- row closed -->
                        <div class="row row-sm">
                            <div class="col-md-6 col-lg-6 col-xl-6">
                                <div class="card card-dashboard-eight pb-2">
                                    <h6 class="card-title text-danger">Varietà di vini ROSSI</h6><span class="d-block mg-b-10 text-muted tx-12">Bottiglie vendute in base alla varietà</span>

                                    <div class="table-responsive country-table">
                                        <table class="table table-striped table-bordered mb-0 text-sm-nowrap text-lg-nowrap text-xl-nowrap">
                                            <tbody>
                                                <?php
                                                $rossi = analisiBottigliePerTipoClienteAnno($id_del_cliente, 'rosso', $annoricerca);

                                                //Per ogni elemento dell'array $rossi
                                                foreach ($rossi as $varieta_vino) {
                                                    //Calcolo la quantità totale di bottiglie per ogni varietà
                                                    $quantita = $varieta_vino['quantita_prodotto'];
                                                    //Calcolo la percentuale di bottiglie per ogni varietà
                                                    $percentuale = number_format($quantita / $totale_bottiglie * 100, 2);
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
                                                $rossi = analisiBottigliePerTipoClienteAnno($id_del_cliente, 'bianco', $annoricerca);

                                                //Per ogni elemento dell'array $rossi
                                                foreach ($rossi as $varieta_vino) {
                                                    //Calcolo la quantità totale di bottiglie per ogni varietà
                                                    $quantita = $varieta_vino['quantita_prodotto'];
                                                    //Calcolo la percentuale di bottiglie per ogni varietà
                                                    $percentuale = number_format($quantita / $totale_bottiglie * 100, 2);
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
                        </div>
                    <?php endif; ?>

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

        $("#txt_search").keyup(function() {
            var search = $(this).val();

            if (search != "") {

                $.ajax({
                    url: 'include/cerca_cliente.php',
                    type: 'post',
                    data: {
                        search: search,
                        type: 1
                    },
                    dataType: 'json',
                    success: function(response) {
                        var len = response.length;
                        $("#searchResult").empty();
                        for (var i = 0; i < len; i++) {
                            var id = response[i]['id'];
                            var name = response[i]['name'];

                            $("#searchResult").append("<li value='" + id + "'>" + name + "</li>");

                        }

                        // binding click event to li
                        $("#searchResult li").bind("click", function() {
                            //Ricarico la pagina con il parametro c 
                            window.location.href = "analisi-clienti.php?c=" + $(this).val();

                        });


                    }
                });
            }

        });


    });


    function setText(element) {

        var userid = $(element).val();

        $("#txt_search").val(value);
        $("#searchResult").empty();

        // Request User Details
        $.ajax({
            url: 'include/cerca_cliente.php',
            type: 'post',
            data: {
                userid: userid,
                type: 2
            },
            dataType: 'json',
            success: function(response) {

                var len = response.length;
                $("#userDetail").empty();
                if (len > 0) {
                    var username = response[0]['username'];
                    var email = response[0]['email'];
                    $("#userDetail").append("Username : " + username + "<br/>");
                    $("#userDetail").append("Email : " + email);
                }
            }

        });
    }
    <?php
    if (!empty($dati_cliente)) :
    ?>
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
                colors: ['#0d6efd', '#198754', '#ffc107', '#dc3545'],
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
                series: [
                    <?php
                    foreach ($anni_dati as $anno) :
                        $dati = imponibilePerClienteMese($anno, $id_del_cliente);
                    ?> {
                            name: '<?= $anno ?>',
                            data: [<?= implode(", ", $dati) ?>]
                        },
                    <?php endforeach; ?>

                ],
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

        $(document).ready(function() {
            indexchart();
        });

        /*--- Apex (#chart) ---*/
        function indexchart() {

            var options = {
                chart: {
                    width: 200,
                    height: 205,
                    responsive: 'true',
                    reset: 'true',
                    type: 'radialBar',
                    offsetX: 0,
                    offsetY: 0,
                },
                plotOptions: {
                    radialBar: {
                        responsive: 'true',
                        startAngle: -135,
                        endAngle: 135,
                        size: 120,
                        imageWidth: 50,
                        imageHeight: 50,

                        track: {
                            strokeWidth: "80%",
                            background: '#ecf0fa',
                        },
                        dropShadow: {
                            enabled: false,
                            top: 0,
                            left: 0,
                            bottom: 0,
                            blur: 3,
                            opacity: 0.5
                        },
                        dataLabels: {
                            name: {
                                fontSize: '16px',
                                color: undefined,
                                offsetY: 30,
                            },
                            hollow: {
                                size: "60%"
                            },
                            value: {
                                offsetY: -10,
                                fontSize: '22px',
                                color: undefined,
                                formatter: function(val) {
                                    return val + "%";
                                }
                            }
                        }
                    }
                },
                colors: ['#0db2de'],
                fill: {
                    type: "gradient",
                    gradient: {
                        shade: "dark",
                        type: "horizontal",
                        shadeIntensity: .5,
                        gradientToColors: [myVarVal],
                        inverseColors: !0,
                        opacityFrom: 1,
                        opacityTo: 1,
                        stops: [0, 100]
                    }
                },
                stroke: {
                    dashArray: 4
                },
                series: [<?= Importanza_cliente($id_del_cliente) ?>],
                labels: [""]
            };

            document.querySelector('#chart1').innerHTML = ""
            var chart = new ApexCharts(document.querySelector("#chart1"), options);
            chart.render();
        }
        /*--- Apex (#chart)closed ---*/
        $(document).ready(function() {
            indexchart2();
        });

        /*--- Apex (#chart) ---*/
        function indexchart2() {

            var options = {
                chart: {
                    width: 200,
                    height: 205,
                    responsive: 'true',
                    reset: 'true',
                    type: 'radialBar',
                    offsetX: 0,
                    offsetY: 0,
                },
                plotOptions: {
                    radialBar: {
                        responsive: 'true',
                        startAngle: -135,
                        endAngle: 135,
                        size: 120,
                        imageWidth: 50,
                        imageHeight: 50,

                        track: {
                            strokeWidth: "80%",
                            background: '#ecf0fa',
                        },
                        dropShadow: {
                            enabled: false,
                            top: 0,
                            left: 0,
                            bottom: 0,
                            blur: 3,
                            opacity: 0.5
                        },
                        dataLabels: {
                            name: {
                                fontSize: '16px',
                                color: undefined,
                                offsetY: 30,
                            },
                            hollow: {
                                size: "60%"
                            },
                            value: {
                                offsetY: -10,
                                fontSize: '22px',
                                color: undefined,
                                formatter: function(val) {
                                    return val + "%";
                                }
                            }
                        }
                    }
                },
                colors: ['#ffc107'],
                fill: {
                    type: "gradient",
                    gradient: {
                        shade: "dark",
                        type: "horizontal",
                        shadeIntensity: .5,
                        gradientToColors: [myVarVal],
                        inverseColors: !0,
                        opacityFrom: 1,
                        opacityTo: 1,
                        stops: [0, 100]
                    }
                },
                stroke: {
                    dashArray: 4
                },
                series: [<?= Affidabilita_cliente($id_del_cliente) ?>],
                labels: [""]
            };

            document.querySelector('#chart2').innerHTML = ""
            var chart = new ApexCharts(document.querySelector("#chart2"), options);
            chart.render();
        }
        /*--- Apex (#chart)closed ---*/
    <?php
    endif;
    ?>
    /*--- Map ---*/
</script>