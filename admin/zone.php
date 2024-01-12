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
                            <h2 class="main-content-title tx-24 mg-b-1 mg-b-lg-1">Hi, welcome back!</h2>
                            <p class="mg-b-0">Sales monitoring dashboard template.</p>
                        </div>
                    </div>
                    <div class="main-dashboard-header-right">
                        <div>
                            <label class="tx-13">Customer Ratings</label>
                            <div class="main-star">
                                <i class="typcn typcn-star active"></i> <i class="typcn typcn-star active"></i> <i class="typcn typcn-star active"></i> <i class="typcn typcn-star active"></i> <i class="typcn typcn-star"></i> <span>(14,873)</span>
                            </div>
                        </div>
                        <div>
                            <label class="tx-13">Online Sales</label>
                            <h5>563,275</h5>
                        </div>
                        <div>
                            <label class="tx-13">Offline Sales</label>
                            <h5>783,675</h5>
                        </div>
                    </div>
                </div>
                <!-- breadcrumb -->

                <!-- row -->
                <div class="row row-sm">
                    <div class="col-md-12">
                        <div class="card" id="tabs-style4">
                            <div class="card-header  d-flex justify-content-between align-items-center">
                                <h3 class="card-title">Definizione delle zone</h3>
                                <div>
                                    <button class="btn btn-primary btn-sm addzona"><span class="fe fe-plus"> </span></button>
                                </div>
                            </div>
                            <div class="card-body">
                                <?php
                                $zone = get_zone(1);
                                if (!empty($zone)) {
                                    $n = 1; ?>
                                    <div class="d-md-flex">
                                        <div class="">
                                            <div class="panel panel-primary tabs-style-4">
                                                <div class="tab-menu-heading">
                                                    <div class="tabs-menu ">
                                                        <!-- Tabs -->
                                                        <ul class="nav panel-tabs me-3" id="tab-zone">
                                                            <?php
                                                            $n = 1;
                                                            foreach ($zone as $zona) { ?>
                                                                <li class=""><a href="#tab<?= $zona['id_zona'] ?>" class="<?= $n == 1 ? 'active' : '' ?>" data-bs-toggle="tab"><i class="fa fa-laptop me-1"></i> <?= $zona['nome_zona'] ?></a></li>
                                                            <?php
                                                                $n++;
                                                            }
                                                            ?>
                                                        </ul>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                        <div class="tabs-style-4 container ">
                                            <div class="panel-body tabs-menu-body">
                                                <div class="tab-content" id="tab-content-zone">
                                                    <?php
                                                    $n = 1;
                                                    foreach ($zone as $zona) { ?>
                                                        <div class="tab-pane <?= $n == 1 ? 'active' : '' ?>" id="tab<?= $zona['id_zona'] ?>">
                                                            <div class="row mg-b-20">
                                                                <div class="col-md-12 d-flex  justify-content-between">
                                                                    <div class="mb-3">
                                                                        <label for="" class="form-label"> Ripartizione predefinita:</label>
                                                                        <select class="form-select form-select prov_tipo" name="prov_tipo" id="prov_tipo" data-id="<?= $zona['id_zona'] ?>">
                                                                            <option selected>Select one</option>
                                                                            <option value="1" <?= $zona['provv'] == 1 ? ' selected' : '' ?>>50% Agente - 50% Agenzia</option>
                                                                            <option value="2" <?= $zona['provv'] == 2 ? ' selected' : '' ?>>100% Agenzia</option>
                                                                        </select>
                                                                    </div>

                                                                    <div><button class="btn btn-primary associazona" data-id="<?= $zona['id_zona']  ?>">Associa Cliente</button></div>
                                                                </div>
                                                            </div>
                                                            <div class="row justify-content-center align-items-center g-2">
                                                                <?php
                                                                $cl_zone = get_clienti_zone($zona['id_zona']);
                                                                if (!empty($cl_zone)) { ?>
                                                                    <div class="table-responsive border">
                                                                        <table class="table table-striped mg-b-0 text-md-nowrap">
                                                                            <thead>
                                                                                <tr>
                                                                                    <th>ID</th>
                                                                                    <th>Name</th>
                                                                                    <th>Position</th>
                                                                                    <th>Salary</th>
                                                                                </tr>
                                                                            </thead>
                                                                            <tbody>
                                                                                <?php
                                                                                foreach ($cl_zone as $cl) { ?>

                                                                                    <tr>
                                                                                        <th scope="row"><?= $cl['id_cfic'] ?></th>
                                                                                        <td><?= $cl['nome'] ?></td>
                                                                                        <td></td>
                                                                                        <td></td>
                                                                                    </tr>
                                                                                <?php
                                                                                } ?>
                                                                            </tbody>
                                                                        </table>
                                                                    </div>
                                                                <?php
                                                                } else {
                                                                    echo 'Non ci sono clienti in questa zona';
                                                                }
                                                                ?>
                                                            </div>
                                                        </div>
                                                    <?php
                                                        $n++;
                                                    }

                                                    ?>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                <?php
                                } else { ?>
                                    <div class="alert alert-warning" role="alert">
                                        <strong>Attenzione!</strong> Non ci sono zone da visualizzare.
                                    </div>
                                <?php
                                }
                                ?>
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
    var idzona;

    $(document).on('click', '.associazona', function(event) {
        event.preventDefault();
        //Leggo la zona  tramite data ID
        idzona = $(this).attr("data-id");
        console.log('idzona assegnato:', idzona);
        //Apro il model zona .modal('show')
        $('#modal-cliente').modal('show');
    });
    //Se schiaccio su .aggiungicliente Invio i dati tramite ajax
    $(document).on('click', '.associacliente', function(event) {
        event.preventDefault();
        var idcliente = $(this).attr("data-cliente");
        //Modifico il colore del bottone da .btn-primary a .btn-danger
        $(this).removeClass('btn-primary');
        $(this).addClass('btn-danger');

        //Modifico la classe del bottone da .associacliente a .disassociacliente
        $(this).removeClass('associacliente');
        $(this).addClass('disassociacliente');
        //Modifico il nome del bottone da Associa a Disassocia
        $(this).html('Disassocia');

        //Valore del select provvigione
        $.ajax({
            type: "post",
            url: "include/zone.php",
            data: {
                idcliente: idcliente,
                idzona: idzona,
                tipo: 'associa'
            },
            dataType: "json",
            success: function(response) {

                //Inserisco i valori json nel div #tab-zone e #tab-content-zone
                // $('#tab-content-zone').html(response.tabcontent);
                $('#cl' + idcliente).html(response.zona);
                notif({
                    type: "success",
                    msg: "Zona associata con successo .",
                    position: "right",
                    fade: true
                });
            }
        });
    });
    //Se schiaccio sulla classe .disassociacliente Invio i dati tramite ajax e disassocia il cliente
    $(document).on('click', '.disassociacliente', function(event) {
        event.preventDefault();
        var idcliente = $(this).attr("data-cliente");
        // Cambio la classe del bottone da .disassociacliente a .associacliente
        $(this).removeClass('disassociacliente');
        $(this).addClass('associacliente');
        // Cambio il colore del bottone da .btn-danger a .btn-success
        $(this).removeClass('btn-danger');
        $(this).addClass('btn-primary');
        // Cambio il nome del bottone da Disassocia a Associa
        $(this).html('Associa');
        $.ajax({
            type: "post",
            url: "include/zone.php",
            data: {
                idcliente: idcliente,
                idzona: idzona,
                tipo: 'disassocia'
            },
            dataType: "json",
            success: function(response) {
                //Inserisco i valori json nel div #tab-zone e #tab-content-zone
                // $('#tab-content-zone').html(response.tabcontent);
                $('#cl' + idcliente).html(response.zona);
                notif({
                    type: "success",
                    msg: "Zona disassociata con successo .",
                    position: "right",
                    fade: true
                });
            }
        });
    });
</script>