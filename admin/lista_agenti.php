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
                            <h2 class="main-content-title tx-24 mg-b-1 mg-b-lg-1">Lista Agenti</h2>
                        </div>
                    </div>
                    <div class="main-dashboard-header-right">
                        <a href="agente.php" class="btn btn-primary btn-sm"><span class="fe fe-user-plus"> </span> Crea Nuovo Agente</a>
                    </div>
                </div>
                <!-- breadcrumb -->

                <!-- row -->
                <div class="row row-sm">
                    <div class="col-xl-12 col-lg-12 col-md-12 col-xm-12">
                        <div class="card">
                            <div class="card-header pb-0">
                                <div class="d-flex justify-content-between">
                                    <h4 class="card-title mg-b-0">LISTA AGENTI</h4>
                                </div>
                            </div>
                            <div class="card-body">
                                <?php
                                $agenti = get_agenti_totali();
                                if (!empty($agenti)) { ?>
                                    <div class="table-responsive border">
                                        <table class="table table-striped mg-b-0 text-md-nowrap">
                                            <thead>
                                                <tr>
                                                    <th>ID</th>
                                                    <th>Nome</th>
                                                    <th>Sigla</th>
                                                    <th>Prov %</th>
                                                    <th></th>
                                                </tr>
                                            </thead>
                                            <tbody>
                                                <?php
                                                foreach ($agenti as $agente) {
                                                    echo '<tr id="' . $agente['id'] . '">';
                                                    echo '<th scope="row">' . $agente['id'] . '</th>';
                                                    echo '<td>' . $agente['nome_agente'] . '</td>';
                                                    echo '<td>' . $agente['sigla'] . '</td>';
                                                    echo '<td>' . $agente['provv'] . '%</td>';
                                                    echo '<td><a href="agente.php?id=' . $agente['id'] . '" class="btn btn-primary btn-sm"><i class="fas fa-edit"></i></a>  <button class="btn btn-danger btn-sm cancellagente" data-id="' . $agente['id'] . '"><span class="fe fe-trash"> </span></button></td>';
                                                    echo '</tr>';
                                                }
                                                ?>
                                            </tbody>
                                        </table>
                                    </div><!-- bd -->
                                <?php
                                } else {
                                    echo '<div class="alert alert-warning" role="alert">
                                    <span class="alert-inner--icon"><i class="fe fe-info"></i></span>
                                    <span class="alert-inner--text"><strong>Warning!</strong> Non ci sono agenti!</span>
                                </div>';
                                }
                                ?>

                            </div><!-- bd -->
                        </div><!-- bd -->
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