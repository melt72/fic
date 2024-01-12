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
                <?php
                $imponibile = analisiTotale('2023');

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
                                            <p class="mb-0 tx-12 text-white op-7">Compared to last week</p>
                                        </div>
                                        <span class="float-end my-auto ms-auto">
                                            <i class="fas fa-arrow-circle-up text-white"></i>
                                            <span class="text-white op-7"> +427</span>
                                        </span>
                                    </div>
                                </div>
                            </div>
                            <span id="compositeline" class="pt-1">5,9,5,6,4,12,18,14,10,15,12,5,8,5,12,5,12,10,16,12</span>
                        </div>
                    </div>
                    <div class="col-xl-3 col-lg-6 col-md-6 col-xm-12">
                        <div class="card overflow-hidden sales-card bg-danger-gradient">
                            <div class="px-3 pt-3  pb-2 pt-0">
                                <div class="">
                                    <h6 class="mb-3 tx-12 text-white">Imponibile incassato</h6>
                                </div>
                                <div class="pb-0 mt-0">
                                    <div class="d-flex">
                                        <div class="">
                                            <h4 class="tx-20 fw-bold mb-1 text-white">€ <?= $imponibile['incassato'] ?></h4>
                                            <p class="mb-0 tx-12 text-white op-7">Compared to last week</p>
                                        </div>
                                        <span class="float-end my-auto ms-auto">
                                            <i class="fas fa-arrow-circle-down text-white"></i>
                                            <span class="text-white op-7"> -23.09%</span>
                                        </span>
                                    </div>
                                </div>
                            </div>
                            <span id="compositeline2" class="pt-1">3,2,4,6,12,14,8,7,14,16,12,7,8,4,3,2,2,5,6,7</span>
                        </div>
                    </div>
                    <div class="col-xl-3 col-lg-6 col-md-6 col-xm-12">
                        <div class="card overflow-hidden sales-card bg-success-gradient">
                            <div class="px-3 pt-3  pb-2 pt-0">
                                <div class="">
                                    <h6 class="mb-3 tx-12 text-white">Imponibile da incassare</h6>
                                </div>
                                <div class="pb-0 mt-0">
                                    <div class="d-flex">
                                        <div class="">
                                            <h4 class="tx-20 fw-bold mb-1 text-white">€ <?= $imponibile['da_incassare'] ?></h4>
                                            <p class="mb-0 tx-12 text-white op-7">Compared to last week</p>
                                        </div>
                                        <span class="float-end my-auto ms-auto">
                                            <i class="fas fa-arrow-circle-up text-white"></i>
                                            <span class="text-white op-7"> 52.09%</span>
                                        </span>
                                    </div>
                                </div>
                            </div>
                            <span id="compositeline3" class="pt-1">5,10,5,20,22,12,15,18,20,15,8,12,22,5,10,12,22,15,16,10</span>
                        </div>
                    </div>
                    <div class="col-xl-3 col-lg-6 col-md-6 col-xm-12">
                        <div class="card overflow-hidden sales-card bg-warning-gradient">
                            <div class="px-3 pt-3  pb-2 pt-0">
                                <div class="">
                                    <h6 class="mb-3 tx-12 text-white">Imponibile scaduto</h6>
                                </div>
                                <div class="pb-0 mt-0">
                                    <div class="d-flex">
                                        <div class="">
                                            <h4 class="tx-20 fw-bold mb-1 text-white">€ <?= $imponibile['non_pagato_scaduto'] ?></h4>
                                            <p class="mb-0 tx-12 text-white op-7">Compared to last week</p>
                                        </div>
                                        <span class="float-end my-auto ms-auto">
                                            <i class="fas fa-arrow-circle-down text-white"></i>
                                            <span class="text-white op-7"> -152.3</span>
                                        </span>
                                    </div>
                                </div>
                            </div>
                            <span id="compositeline4" class="pt-1">5,9,5,6,4,12,18,14,10,15,12,5,8,5,12,5,12,10,16,12</span>
                        </div>
                    </div>
                </div>
                <!-- row closed -->
                <!-- row -->
                <?php
                $trimestre = analisiImponibileTrimestre('2023');
                print_r($trimestre)
                ?>
                <div class="row row-sm">
                    <div class="col-xl-3 col-lg-6 col-md-6 col-xm-12">
                        <div class="card overflow-hidden sales-card bg-primary-gradient">
                            <div class="px-3 pt-3  pb-2 pt-0">
                                <div class="">
                                    <h6 class="mb-3 tx-12 text-white">Primo trimestre</h6>
                                </div>
                                <div class="pb-0 mt-0">
                                    <div class="d-flex">
                                        <div class="">
                                            <h4 class="tx-20 fw-bold mb-1 text-white">€ <?= $trimestre['1'] ?></h4>
                                            <p class="mb-0 tx-12 text-white op-7">Compared to last week</p>
                                        </div>
                                        <span class="float-end my-auto ms-auto">
                                            <i class="fas fa-arrow-circle-up text-white"></i>
                                            <span class="text-white op-7"> +427</span>
                                        </span>
                                    </div>
                                </div>
                            </div>
                            <span id="compositeline" class="pt-1">5,9,5,6,4,12,18,14,10,15,12,5,8,5,12,5,12,10,16,12</span>
                        </div>
                    </div>
                    <div class="col-xl-3 col-lg-6 col-md-6 col-xm-12">
                        <div class="card overflow-hidden sales-card bg-danger-gradient">
                            <div class="px-3 pt-3  pb-2 pt-0">
                                <div class="">
                                    <h6 class="mb-3 tx-12 text-white">secondo trimestre</h6>
                                </div>
                                <div class="pb-0 mt-0">
                                    <div class="d-flex">
                                        <div class="">
                                            <h4 class="tx-20 fw-bold mb-1 text-white">€ <?= $trimestre['2'] ?></h4>
                                            <p class="mb-0 tx-12 text-white op-7">Compared to last week</p>
                                        </div>
                                        <span class="float-end my-auto ms-auto">
                                            <i class="fas fa-arrow-circle-down text-white"></i>
                                            <span class="text-white op-7"> -23.09%</span>
                                        </span>
                                    </div>
                                </div>
                            </div>
                            <span id="compositeline2" class="pt-1">3,2,4,6,12,14,8,7,14,16,12,7,8,4,3,2,2,5,6,7</span>
                        </div>
                    </div>
                    <div class="col-xl-3 col-lg-6 col-md-6 col-xm-12">
                        <div class="card overflow-hidden sales-card bg-success-gradient">
                            <div class="px-3 pt-3  pb-2 pt-0">
                                <div class="">
                                    <h6 class="mb-3 tx-12 text-white">Terzo trimestre</h6>
                                </div>
                                <div class="pb-0 mt-0">
                                    <div class="d-flex">
                                        <div class="">
                                            <h4 class="tx-20 fw-bold mb-1 text-white">€ <?= $trimestre['3'] ?></h4>
                                            <p class="mb-0 tx-12 text-white op-7">Compared to last week</p>
                                        </div>
                                        <span class="float-end my-auto ms-auto">
                                            <i class="fas fa-arrow-circle-up text-white"></i>
                                            <span class="text-white op-7"> 52.09%</span>
                                        </span>
                                    </div>
                                </div>
                            </div>
                            <span id="compositeline3" class="pt-1">5,10,5,20,22,12,15,18,20,15,8,12,22,5,10,12,22,15,16,10</span>
                        </div>
                    </div>
                    <div class="col-xl-3 col-lg-6 col-md-6 col-xm-12">
                        <div class="card overflow-hidden sales-card bg-warning-gradient">
                            <div class="px-3 pt-3  pb-2 pt-0">
                                <div class="">
                                    <h6 class="mb-3 tx-12 text-white">quarto trimestre</h6>
                                </div>
                                <div class="pb-0 mt-0">
                                    <div class="d-flex">
                                        <div class="">
                                            <h4 class="tx-20 fw-bold mb-1 text-white">€ <?= $trimestre['4'] ?></h4>
                                            <p class="mb-0 tx-12 text-white op-7">Compared to last week</p>
                                        </div>
                                        <span class="float-end my-auto ms-auto">
                                            <i class="fas fa-arrow-circle-down text-white"></i>
                                            <span class="text-white op-7"> -152.3</span>
                                        </span>
                                    </div>
                                </div>
                            </div>
                            <span id="compositeline4" class="pt-1">5,9,5,6,4,12,18,14,10,15,12,5,8,5,12,5,12,10,16,12</span>
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
                                    <h4 class="card-title mb-0">Order status</h4>
                                    <a href="javascript:void(0);" class="tx-inverse" data-bs-toggle="dropdown"><i class="mdi mdi-dots-horizontal text-gray"></i></a>
                                    <div class="dropdown-menu">
                                        <a class="dropdown-item" href="javascript:void(0);">Action</a>
                                        <a class="dropdown-item" href="javascript:void(0);">Another
                                            Action</a>
                                        <a class="dropdown-item" href="javascript:void(0);">Something Else
                                            Here</a>
                                    </div>
                                </div>
                                <p class="tx-12 text-muted mb-0">Order Status and Tracking. Track your order from ship date to arrival. To begin, enter your order number.</p>
                            </div>
                            <div class="card-body b-p-apex">
                                <div class="total-revenue">
                                    <div>
                                        <h4>120,750</h4>
                                        <label><span class="bg-primary"></span>success</label>
                                    </div>
                                    <div>
                                        <h4>56,108</h4>
                                        <label><span class="bg-danger"></span>Pending</label>
                                    </div>
                                    <div>
                                        <h4>32,895</h4>
                                        <label><span class="bg-warning"></span>Failed</label>
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