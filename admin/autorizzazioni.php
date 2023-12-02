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
                            <h2 class="main-content-title tx-24 mg-b-1 mg-b-lg-1">Autorizzazioni</h2>

                        </div>
                    </div>
                    <div class="main-dashboard-header-right">
                        <a href="utente.php" class="btn btn-primary btn-sm"><span class="fe fe-user-plus"> </span> Crea Nuovo Utente</a>
                    </div>
                </div>
                <!-- breadcrumb -->

                <!-- row -->
                <div class="row row-sm">
                    <div class="col-xl-12">
                        <div class="card">
                            <div class="card-header pb-0">
                                <div class="d-flex justify-content-between">
                                    <h4 class="card-title mg-b-0">LISTA UTENTI</h4>
                                </div>
                            </div>
                            <div class="card-body">
                                <div class="table-responsive border">
                                    <table class="table mg-b-0 text-md-nowrap">
                                        <thead>
                                            <tr>
                                                <th>#</th>
                                                <th>Utente</th>
                                                <th>E-mail</th>
                                                <th>Tipo</th>
                                                <th>Azioni</th>
                                            </tr>
                                        </thead>
                                        <tbody>
                                            <?php
                                            try {
                                                include('../include/configpdo.php');
                                                $query = "SELECT * FROM `user` WHERE username!='meltit72@gmail.com'";
                                                $stmt = $db->prepare($query);
                                                $stmt->execute();
                                                $count = $stmt->rowCount();
                                                if ($count != 0) {
                                                    $dati   = $stmt->fetchAll();
                                                    foreach ($dati as $row) {
                                            ?>
                                                        <tr>
                                                            <th scope="row" id="<?= $row['id_user'] ?>">-</th>
                                                            <td><?= $row['nome']; ?> <?= $row['cognome'] ?></td>
                                                            <td><?= $row['username']; ?></td>
                                                            <td><span class="badge 
                                                    <?php
                                                        $ruolo = '';
                                                        switch ($row['ruolo']) {
                                                            case 'sadmin':
                                                                echo 'badge bg-danger me-1';
                                                                $ruolo = 'Super Admin';
                                                                break;
                                                            case 'admin':
                                                                echo 'badge bg-warning me-1';
                                                                $ruolo = 'Admin';
                                                                break;
                                                            case 'user':
                                                                echo 'badge bg-success me-1';
                                                                $ruolo = 'Tecnico';
                                                                break;
                                                            case 'segr':
                                                                echo 'badge bg-info me-1';
                                                                $ruolo = 'Segreteria';
                                                                break;
                                                        }
                                                    ?>
                                                   "><?= $ruolo; ?></span></td>
                                                            <td> <a href="utente.php?id=<?= $row['id_user']; ?>" class="btn btn-primary btn-sm"><span class="fe fe-edit"> </span></a>
                                                                <?php
                                                                if ($row['ruolo'] != 'sadmin') {
                                                                    if ($row['act'] == '1') { ?>
                                                                        <button class="btn btn-secondary btn-sm bloccautente" data-id="<?= $row['id_user']; ?>" data-tipo="2"><span class="fe fe-unlock"> </span></button>
                                                                    <?php } else { ?>
                                                                        <button class="btn btn-warning btn-sm bloccautente" data-id="<?= $row['id_user']; ?>" data-tipo="1"><span class="fe fe-lock"> </span></button>
                                                                    <?php } ?>
                                                                    <button class="btn btn-danger btn-sm cancellautente" data-id="<?= $row['id_user']; ?>"><span class="fe fe-trash"> </span></button>
                                                                <?php
                                                                }
                                                                ?>
                                                            </td>
                                                        </tr>
                                            <?php
                                                    }
                                                } else {
                                                    echo "Nessun dato trovato!";
                                                }
                                            } catch (PDOException $e) {
                                                echo $e->getMessage();
                                            }
                                            ?>
                                        </tbody>
                                    </table>
                                </div>
                            </div>
                        </div>
                    </div>
                    <!--/div-->


                </div>
                <!-- /row -->
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