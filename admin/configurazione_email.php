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
                <form id="form_configurazione_email" class="form-horizontal needs-validation" novalidate>
                    <!-- breadcrumb -->
                    <div class="breadcrumb-header justify-content-between">
                        <div class="left-content">
                            <div>
                                <h2 class="main-content-title tx-24 mg-b-1 mg-b-lg-1">Parametri Email</h2>

                            </div>
                        </div>
                        <div class="main-dashboard-header-right">

                            <button id="test" class="btn btn-primary btn-sm mg-r-2"><span class="fe fe-refresh-cw"> </span> Test Configurazione</button>

                            <button id="salva_mail_conf" class="btn btn-success btn-sm btn-salva"><span class="fe fe-save"> </span> Salva Configurazione</button>
                        </div>
                    </div>

                    <!-- breadcrumb -->

                    <!-- row -->
                    <div class="row row-sm">
                        <div class="col-xl-12">
                            <div class="card">
                                <div class="card-header pb-0">
                                    <div class="d-flex justify-content-between">
                                        <h4 class="card-title mg-b-0">Smtp</h4>
                                    </div>
                                </div>
                                <?php $par_mail = getParametriEmail(); ?>
                                <div class="card-body">
                                    <div class="row">
                                        <div class="col-md-6">
                                            <div class="pd-30 pd-sm-40 ">
                                                <div class="row row-xs align-items-center mg-b-20">
                                                    <div class="col-md-4">
                                                        <label class="form-label mg-b-0">Host:</label>
                                                    </div>
                                                    <div class="col-md-8 mg-t-5 mg-md-t-0">
                                                        <input class="form-control" placeholder="Indirizzo host" type="text" id="host" name="host" value="<?= $par_mail['host'] ?>" required>
                                                    </div>
                                                </div>
                                                <div class="row row-xs align-items-center mg-b-20">
                                                    <div class="col-md-4">
                                                        <label class="form-label mg-b-0">Porta:</label>
                                                    </div>
                                                    <div class="col-md-8 mg-t-5 mg-md-t-0">
                                                        <select class="form-control select2-no-search" data-bs-placeholder="Select Country" name="port" id="port" required>
                                                            <option value="" <?= $par_mail['port'] == '' ? ' selected' : '' ?>>Porta di invio</option>
                                                            <option value="587" <?= $par_mail['port'] == '587' ? ' selected' : '' ?>>587</option>
                                                            <option value="465" <?= $par_mail['port'] == '465' ? ' selected' : '' ?>>465</option>
                                                            <option value="25" <?= $par_mail['port'] == '25' ? ' selected' : '' ?>>25</option>
                                                        </select>
                                                    </div>
                                                </div>
                                                <div class="row row-xs align-items-center mg-b-20">
                                                    <div class="col-md-4">
                                                        <label class="form-label mg-b-0">Username:</label>
                                                    </div>
                                                    <div class="col-md-8 mg-t-5 mg-md-t-0">
                                                        <input class="form-control" id="username" name="username" placeholder="Username" type="text" value="<?= $par_mail['username'] ?>" required>
                                                    </div>
                                                </div>
                                                <div class="row row-xs align-items-center mg-b-20">
                                                    <div class="col-md-4">
                                                        <label class="form-label mg-b-0">Password:</label>
                                                    </div>
                                                    <div class="col-md-8 mg-t-5 mg-md-t-0">
                                                        <input class="form-control" id="password" name="password" placeholder="Password" type="text" value="<?= $par_mail['password'] ?>" required>
                                                    </div>
                                                </div>

                                            </div>
                                        </div>
                                        <div class="col-md-6">
                                            <div class="pd-30 pd-sm-40 ">
                                                <div class="row row-xs align-items-center mg-b-20">
                                                    <div class="col-md-4">
                                                        <label class="form-label mg-b-0">Da (E-mail):</label>
                                                    </div>
                                                    <div class="col-md-8 mg-t-5 mg-md-t-0">
                                                        <input class="form-control" id="from" name="from" placeholder="Enter your firstname" type="email" value="<?= $par_mail['from'] ?>">
                                                    </div>
                                                </div>
                                                <div class="row row-xs align-items-center mg-b-20">
                                                    <div class="col-md-4">
                                                        <label class="form-label mg-b-0">Da (Nome):</label>
                                                    </div>
                                                    <div class="col-md-8 mg-t-5 mg-md-t-0">
                                                        <input class="form-control" id="fromname" name="fromname" placeholder="Enter your lastname" type="text" value="<?= $par_mail['fromname'] ?>">
                                                    </div>
                                                </div>
                                                <div class="row row-xs align-items-center mg-b-20">
                                                    <div class="col-md-4">
                                                        <label class="form-label mg-b-0">Rispondi a (E-mail):</label>
                                                    </div>
                                                    <div class="col-md-8 mg-t-5 mg-md-t-0">
                                                        <input class="form-control" id="replayto" name="replayto" placeholder="Enter your email" type="email" value="<?= $par_mail['replayto'] ?>">
                                                    </div>
                                                </div>
                                                <div class="row row-xs align-items-center mg-b-20">
                                                    <div class="col-md-4">
                                                        <label class="form-label mg-b-0">Rispondi a (Nome):</label>
                                                    </div>
                                                    <div class="col-md-8 mg-t-5 mg-md-t-0">
                                                        <input class="form-control" id="replaytoname" name="replaytoname" placeholder="Enter your email" type="text" value="<?= $par_mail['replaytoname'] ?>">
                                                    </div>
                                                </div>

                                            </div>
                                        </div>
                                    </div>

                                </div>
                            </div>
                        </div>
                        <!--/div-->


                    </div>
                    <!-- /row -->
                </form>
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