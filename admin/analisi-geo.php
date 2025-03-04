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
                            <h2 class="main-content-title tx-24 mg-b-1 mg-b-lg-1">Analisi Geografica</h2>
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
                                        <li><a class="dropdown-item" href="analisi-geo.php?a=<?= $annoCorrente ?>"><?= $annoCorrente ?></a></li>
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
                $nazioni = analisiImponibilePerPaese($anno);
                ?>
                <div class="row row-sm">
                    <div class="col-sm-12 col-md-6">
                        <div class="card">
                            <div class="card-header">
                                <div class="d-flex justify-content-between">
                                    <h3 class="card-title">Nazioni HO.RE.CA.</h3>
                                </div>
                            </div><!-- card-header -->
                            <div class="card-body p-0">
                                <?php

                                $labels = [];
                                $percentuali = [];
                                foreach ($nazioni as $riga) :
                                    $labels[] = $riga['paese'];
                                    $percentuali[] = number_format($riga['percentuale'], 2);
                                ?>
                                    <div class="browser-stats">
                                        <div class="d-flex align-items-center item  border-bottom">
                                            <div class="d-flex">
                                                <div class="">
                                                    <h6 class=""><?= $riga['paese']; ?></h6>
                                                </div>
                                            </div>

                                            <div class="ms-auto my-auto">
                                                <div class="d-flex">
                                                    <span class="me-4 my-auto">€ <?= arrotondaEFormatta($riga['somma_imponibile']); ?></span>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                <?php endforeach; ?>
                            </div>
                        </div>
                    </div><!-- col-6 -->
                    <div class="col-sm-12 col-md-6">
                        <div class="card overflow-hidden">
                            <div class="card-body">
                                <div class="row">
                                    <div class="col-md-8">
                                        <div class="main-content-label mg-b-5">
                                            Grafico Nazioni HO.RE.CA.
                                        </div>
                                        <div class="chartjs-wrapper-demo">
                                            <canvas id="chartDonut"></canvas>
                                        </div>
                                    </div>
                                    <div class="col-md-4">
                                        <?php
                                        $n = 0;
                                        foreach ($nazioni as $riga) :
                                        ?>
                                            <div class="d-flex">
                                                <div style="margin-top:1px;margin-right:5px">
                                                    <div style="width:4px;height:0;border:5px solid <?= $colors[$n] ?>;overflow:hidden"></div>
                                                </div><?= $riga['paese'] ?>
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
                <?php
                $nazioni = analisiImponibilePerPaeseWineshop($anno);
                ?>
                <div class="row row-sm">
                    <div class="col-sm-12 col-md-6">
                        <div class="card">
                            <div class="card-header">
                                <div class="d-flex justify-content-between">
                                    <h3 class="card-title">Nazioni Wine Shop</h3>
                                </div>
                            </div><!-- card-header -->
                            <div class="card-body p-0">
                                <?php

                                $labels2 = [];
                                $percentuali2 = [];
                                foreach ($nazioni as $riga) :
                                    $labels2[] = $riga['paese'];
                                    $percentuali2[] = number_format($riga['percentuale'], 2);
                                ?>
                                    <div class="browser-stats">
                                        <div class="d-flex align-items-center item  border-bottom">
                                            <div class="d-flex">
                                                <div class="">
                                                    <h6 class=""><?= $riga['paese']; ?></h6>
                                                </div>
                                            </div>

                                            <div class="ms-auto my-auto">
                                                <div class="d-flex">
                                                    <span class="me-4 my-auto">€ <?= arrotondaEFormatta($riga['somma_imponibile']); ?></span>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                <?php endforeach; ?>
                            </div>
                        </div>
                    </div><!-- col-6 -->
                    <div class="col-sm-12 col-md-6">
                        <div class="card overflow-hidden">
                            <div class="card-body">
                                <div class="row">
                                    <div class="col-md-8">
                                        <div class="main-content-label mg-b-5">
                                            Grafico Nazioni Wine Shop
                                        </div>
                                        <div class="chartjs-wrapper-demo">
                                            <canvas id="chartDonut2"></canvas>
                                        </div>
                                    </div>
                                    <div class="col-md-4">
                                        <?php
                                        $n = 0;
                                        foreach ($nazioni as $riga) :
                                        ?>
                                            <div class="d-flex">
                                                <div style="margin-top:1px;margin-right:5px">
                                                    <div style="width:4px;height:0;border:5px solid <?= $colors[$n] ?>;overflow:hidden"></div>
                                                </div><?= $riga['paese'] ?>
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
                <!-- row closed -->

                <!-- divisione per macroregioni -->
                <div class="row row-sm">
                    <?php
                    $macro = analisiBottigliePerMacroRegione($anno);
                    $macroImponibile = analisiImportoPerMacroRegione($anno);

                    // Organizza i risultati in un array associativo usando 'nome_macro' come chiave
                    $macroAssociative = [];
                    foreach ($macro as $regioneData) {
                        $macroAssociative[$regioneData['regione']] = $regioneData;
                    }

                    $macroImponibileAssociative = [];
                    foreach ($macroImponibile as $regioneData) {
                        $macroImponibileAssociative[$regioneData['regione']] = $regioneData;
                    }

                    // Accedi ai dati delle regioni con verifica dell'esistenza della chiave
                    $nordOvestData = isset($macroAssociative['Nord - Ovest']) ? $macroAssociative['Nord - Ovest'] : null;
                    $nordEstData = isset($macroAssociative['Nord - Est']) ? $macroAssociative['Nord - Est'] : null;
                    $centroData = isset($macroAssociative['Centro']) ? $macroAssociative['Centro'] : null;
                    $sudData = isset($macroAssociative['Sud']) ? $macroAssociative['Sud'] : null;
                    $isoleData = isset($macroAssociative['Isole']) ? $macroAssociative['Isole'] : null;

                    $nordOvestImponibile = isset($macroImponibileAssociative['Nord - Ovest']) ? $macroImponibileAssociative['Nord - Ovest'] : null;
                    $nordEstImponibile = isset($macroImponibileAssociative['Nord - Est']) ? $macroImponibileAssociative['Nord - Est'] : null;
                    $centroImponibile = isset($macroImponibileAssociative['Centro']) ? $macroImponibileAssociative['Centro'] : null;
                    $sudImponibile = isset($macroImponibileAssociative['Sud']) ? $macroImponibileAssociative['Sud'] : null;
                    $isoleImponibile = isset($macroImponibileAssociative['Isole']) ? $macroImponibileAssociative['Isole'] : null;
                    ?>
                    <?php
                    if ($nordOvestData != null) {
                    ?>
                        <div class="col-sm-12 col-xl-4 col-lg-12">
                            <div class="card user-wideget user-wideget-widget widget-user">
                                <div class="widget-user-header bg-primary">
                                    <h3 class="widget-user-username">Nord-Ovest HO.RE.CA.</h3>
                                    <?= nomeRegione('Nord - Ovest') ?>
                                    <?php $reg = analisiImportoPerRegione($anno, 'Nord - Ovest'); ?>

                                </div>

                                <div class="user-wideget-footer">
                                    <div class="row">
                                        <div class="col-sm-4 border-end">
                                            <div class="description-block">
                                                <h5 class="description-header"><?= $nordOvestData['totale_bottiglie'] ?></h5>
                                                <span class="description-text">BOTTIGLIE</span>
                                            </div>
                                        </div>
                                        <div class="col-sm-4 border-end">
                                            <div class="description-block">
                                                <h5 class="description-header">€ <?= arrotondaEFormatta($nordOvestImponibile['totale_importo']) ?></h5>
                                                <span class="description-text">IMPONIBILE</span>
                                            </div>
                                        </div>
                                        <div class="col-sm-4">
                                            <div class="description-block">
                                                <h5 class="description-header"><?= number_format($nordOvestImponibile['percentuale_importo'], 2) ?>%</h5>
                                                <span class="description-text">PERCENTUALE</span>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="row">
                                        <div class="col-md-12">
                                            <div class="table-responsive country-table ">
                                                <table class="table table-striped table-bordered mb-0 text-sm-nowrap text-lg-nowrap text-xl-nowrap">
                                                    <tbody>
                                                        <?php
                                                        foreach ($reg as $riga) :
                                                        ?>
                                                            <tr>
                                                                <td><a href="libera2.php?a=2024&p=Italia&&tipo=2&r=<?= $riga['regione']; ?>" class="btn btn-info me-2 btn-b"><i class="fe fe-eye"></i> <?= $riga['regione']; ?></a></td>
                                                                <td class="tx-right tx-medium tx-inverse">€ <?= arrotondaEFormatta($riga['totale_importo']); ?></td>
                                                                <td class="tx-right tx-medium tx-inverse"><?= number_format($riga['percentuale_importo'], 2) ?>%</td>
                                                            </tr>
                                                        <?php endforeach; ?>
                                                    </tbody>
                                                </table>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    <?php
                    }
                    if ($nordEstData != null) {
                    ?>
                        <div class="col-sm-12 col-xl-4 col-lg-12">
                            <div class="card user-wideget user-wideget-widget widget-user">
                                <div class="widget-user-header bg-primary">
                                    <h3 class="widget-user-username">Nord-Est HO.RE.CA.</h3>
                                    <?= nomeRegione('Nord - Est') ?>
                                </div>

                                <div class="user-wideget-footer">
                                    <div class="row">
                                        <div class="col-sm-4 border-end">
                                            <div class="description-block">
                                                <h5 class="description-header"><?= $nordEstData['totale_bottiglie'] ?></h5>
                                                <span class="description-text">BOTTIGLIE</span>
                                            </div>
                                        </div>
                                        <div class="col-sm-4 border-end">
                                            <div class="description-block">
                                                <h5 class="description-header">€ <?= arrotondaEFormatta($nordEstImponibile['totale_importo']) ?></h5>
                                                <span class="description-text">IMPONIBILE</span>
                                            </div>
                                        </div>
                                        <div class="col-sm-4">
                                            <div class="description-block">
                                                <h5 class="description-header"><?= number_format($nordEstImponibile['percentuale_importo'], 2) ?>%</h5>
                                                <span class="description-text">PERCENTUALE</span>
                                            </div>
                                        </div>
                                    </div>
                                    <?php $reg = analisiImportoPerRegione($anno, 'Nord - Est'); ?>
                                    <div class="row">
                                        <div class="col-md-12">
                                            <div class="table-responsive country-table ">
                                                <table class="table table-striped table-bordered mb-0 text-sm-nowrap text-lg-nowrap text-xl-nowrap">
                                                    <tbody>
                                                        <?php
                                                        foreach ($reg as $riga) :
                                                        ?>
                                                            <tr>
                                                                <td><a href="libera2.php?a=2024&p=Italia&&tipo=2&r=<?= $riga['regione']; ?>" class="btn btn-info me-2 btn-b"><i class="fe fe-eye"></i> <?= $riga['regione']; ?></a></td>
                                                                <td class="tx-right tx-medium tx-inverse">€ <?= arrotondaEFormatta($riga['totale_importo']); ?></td>
                                                                <td class="tx-right tx-medium tx-inverse"><?= number_format($riga['percentuale_importo'], 2) ?>%</td>
                                                            </tr>
                                                        <?php endforeach; ?>
                                                    </tbody>
                                                </table>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    <?php
                    }
                    if ($centroData != null) {
                    ?>
                        <div class="col-sm-12 col-xl-4 col-lg-12">
                            <div class="card user-wideget user-wideget-widget widget-user">
                                <div class="widget-user-header bg-warning">
                                    <h3 class="widget-user-username">Centro HO.RE.CA.</h3>
                                    <?= nomeRegione('Centro') ?>
                                </div>
                                <div class="user-wideget-footer">
                                    <div class="row">
                                        <div class="col-sm-4 border-end">
                                            <div class="description-block">
                                                <h5 class="description-header"><?= $centroData['totale_bottiglie'] ?></h5>
                                                <span class="description-text">BOTTIGLIE</span>
                                            </div>
                                        </div>
                                        <div class="col-sm-4 border-end">
                                            <div class="description-block">
                                                <h5 class="description-header">€ <?= arrotondaEFormatta($centroImponibile['totale_importo']) ?></h5>
                                                <span class="description-text">IMPONIBILE</span>
                                            </div>
                                        </div>
                                        <div class="col-sm-4">
                                            <div class="description-block">
                                                <h5 class="description-header"><?= number_format($centroImponibile['percentuale_importo'], 2) ?>%</h5>
                                                <span class="description-text">PERCENTUALE</span>
                                            </div>
                                        </div>
                                    </div>
                                    <?php $reg = analisiImportoPerRegione($anno, 'Centro'); ?>
                                    <div class="row">
                                        <div class="col-md-12">
                                            <div class="table-responsive country-table ">
                                                <table class="table table-striped table-bordered mb-0 text-sm-nowrap text-lg-nowrap text-xl-nowrap">
                                                    <tbody>
                                                        <?php
                                                        foreach ($reg as $riga) :
                                                        ?>
                                                            <tr>
                                                                <td><a href="libera2.php?a=2024&p=Italia&&tipo=2&r=<?= $riga['regione']; ?>" class="btn btn-info me-2 btn-b"><i class="fe fe-eye"></i> <?= $riga['regione']; ?></a></td>
                                                                <td class="tx-right tx-medium tx-inverse">€ <?= arrotondaEFormatta($riga['totale_importo']); ?></td>
                                                                <td class="tx-right tx-medium tx-inverse"><?= number_format($riga['percentuale_importo'], 2) ?>%</td>
                                                            </tr>
                                                        <?php endforeach; ?>
                                                    </tbody>
                                                </table>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    <?php
                    }
                    if ($sudData != null) {
                    ?>
                        <div class="col-sm-12 col-xl-4 col-lg-12">
                            <div class="card user-wideget user-wideget-widget widget-user">
                                <div class="widget-user-header bg-danger">
                                    <h3 class="widget-user-username">Sud HO.RE.CA.</h3>
                                    <?= nomeRegione('Sud') ?>
                                </div>
                                <div class="user-wideget-footer">
                                    <div class="row">
                                        <div class="col-sm-4 border-end">
                                            <div class="description-block">
                                                <h5 class="description-header"><?= $sudData['totale_bottiglie'] ?></h5>
                                                <span class="description-text">BOTTIGLIE</span>
                                            </div>
                                        </div>
                                        <div class="col-sm-4 border-end">
                                            <div class="description-block">
                                                <h5 class="description-header">€ <?= arrotondaEFormatta($sudImponibile['totale_importo']) ?></h5>
                                                <span class="description-text">IMPONIBILE</span>
                                            </div>
                                        </div>
                                        <div class="col-sm-4">
                                            <div class="description-block">
                                                <h5 class="description-header"><?= number_format($sudImponibile['percentuale_importo'], 2) ?>%</h5>
                                                <span class="description-text">PERCENTUALE</span>
                                            </div>
                                        </div>
                                    </div>
                                    <?php $reg = analisiImportoPerRegione($anno, 'Sud'); ?>
                                    <div class="row">
                                        <div class="col-md-12">
                                            <div class="table-responsive country-table ">
                                                <table class="table table-striped table-bordered mb-0 text-sm-nowrap text-lg-nowrap text-xl-nowrap">
                                                    <tbody>
                                                        <?php
                                                        foreach ($reg as $riga) :
                                                        ?>
                                                            <tr>
                                                                <td><a href="libera2.php?a=2024&p=Italia&&tipo=2&r=<?= $riga['regione']; ?>" class="btn btn-info me-2 btn-b"><i class="fe fe-eye"></i> <?= $riga['regione']; ?></a></td>
                                                                <td class="tx-right tx-medium tx-inverse">€ <?= arrotondaEFormatta($riga['totale_importo']); ?></td>
                                                                <td class="tx-right tx-medium tx-inverse"><?= number_format($riga['percentuale_importo'], 2) ?>%</td>
                                                            </tr>
                                                        <?php endforeach; ?>
                                                    </tbody>
                                                </table>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        <?php }
                    if ($isoleData != null) {
                        ?>
                            <div class="col-sm-12 col-xl-4 col-lg-12">
                                <div class="card user-wideget user-wideget-widget widget-user">
                                    <div class="widget-user-header bg-primary">
                                        <h3 class="widget-user-username">Sud HO.RE.CA.</h3>
                                        <?= nomeRegione('Isole') ?>
                                    </div>
                                    <div class="user-wideget-footer">
                                        <div class="row">
                                            <div class="col-sm-4 border-end">
                                                <div class="description-block">
                                                    <h5 class="description-header"><?= $isoleData['totale_bottiglie'] ?></h5>
                                                    <span class="description-text">BOTTIGLIE</span>
                                                </div>
                                            </div>
                                            <div class="col-sm-4 border-end">
                                                <div class="description-block">
                                                    <h5 class="description-header">€ <?= arrotondaEFormatta($isoleImponibile['totale_importo']) ?></h5>
                                                    <span class="description-text">IMPONIBILE</span>
                                                </div>
                                            </div>
                                            <div class="col-sm-4">
                                                <div class="description-block">
                                                    <h5 class="description-header"><?= number_format($isoleImponibile['percentuale_importo'], 2) ?>%</h5>
                                                    <span class="description-text">PERCENTUALE</span>
                                                </div>
                                            </div>
                                        </div>
                                        <?php $reg = analisiImportoPerRegione($anno, 'Isole'); ?>
                                        <div class="row">
                                            <div class="col-md-12">
                                                <div class="table-responsive country-table ">
                                                    <table class="table table-striped table-bordered mb-0 text-sm-nowrap text-lg-nowrap text-xl-nowrap">
                                                        <tbody>
                                                            <?php
                                                            foreach ($reg as $riga) :
                                                            ?>
                                                                <tr>
                                                                    <td><?= $riga['regione']; ?></td>
                                                                    <td class="tx-right tx-medium tx-inverse">€ <?= arrotondaEFormatta($riga['totale_importo']); ?></td>
                                                                    <td class="tx-right tx-medium tx-inverse"><?= number_format($riga['percentuale_importo'], 2) ?>%</td>
                                                                </tr>
                                                            <?php endforeach; ?>
                                                        </tbody>
                                                    </table>
                                                </div>
                                            </div>

                                        </div>
                                    </div>
                                </div>
                            <?php } ?>
                            </div>




                            <div class="row row-sm">
                                <div class="col-sm-12 col-md-6">
                                    <div class="card">
                                        <div class="card-header">
                                            <div class="d-flex justify-content-between">
                                                <h3 class="card-title">ITALIA / Province</h3>
                                            </div>
                                        </div><!-- card-header -->
                                        <div class="card-body p-0">
                                            <div class="table-responsive country-table">
                                                <table class="table table-striped table-bordered mb-0 text-sm-nowrap text-lg-nowrap text-xl-nowrap">
                                                    <thead>
                                                        <tr>
                                                            <th class="wd-lg-25p">Provincia</th>
                                                            <th class="wd-lg-25p tx-right">Imponibile</th>
                                                            <th>n° fatture</th>
                                                            <th>n° bottiglie</th>
                                                            <th></th>
                                                        </tr>
                                                    </thead>
                                                    <tbody>
                                                        <?php
                                                        $province = analisiImponibilePerProvincia($anno);
                                                        foreach ($province as $riga) :
                                                        ?>
                                                            <tr>
                                                                <td><?= $riga['nome_provincia']; ?></td>
                                                                <td class="tx-right tx-medium tx-inverse">€ <?= arrotondaEFormatta($riga['imponibile']); ?></td>
                                                                <td class="tx-right tx-medium tx-inverse"><?= $riga['numero_fatture']; ?></td>
                                                                <td class="tx-right tx-medium tx-inverse"><?= $riga['numero_bottiglie']; ?> bt</td>
                                                                <td class="tx-right tx-medium tx-inverse"><button class="btn btn-info btn-icon me-2 btn-b vedi-provincia" data-pv="<?= $riga['pv'] ?>"><i class="fe fe-eye"></i></button></td>
                                                            </tr>
                                                        <?php endforeach; ?>
                                                    </tbody>
                                                </table>
                                            </div>

                                        </div>
                                    </div>
                                </div>
                                <!-- col-6 -->
                                <div class="col-sm-12 col-md-6">
                                    <div class="card card-table-two">
                                        <div class=" card-header p-0 d-flex justify-content-between">
                                            <h4 class="card-title mb-1">ALTRI STATI</h4>
                                            <span class="tx-12 tx-muted mb-3 ">Anno <?= $anno ?></span>
                                        </div>

                                        <div class="table-responsive country-table">
                                            <table class="table table-striped table-bordered mb-0 text-sm-nowrap text-lg-nowrap text-xl-nowrap">
                                                <thead>
                                                    <tr>
                                                        <th>Stato</th>
                                                        <th class="tx-right">Imponibile</th>
                                                        <th>n° fatt</th>
                                                        <th>n° bott</th>
                                                        <th></th>
                                                    </tr>
                                                </thead>
                                                <tbody>
                                                    <?php
                                                    $clienti = analisiImponibilePerPaeseHome($anno);
                                                    foreach ($clienti as $riga) :
                                                    ?>
                                                        <tr>
                                                            <td><?= $riga['paese']; ?></td>
                                                            <td class="tx-right tx-medium tx-inverse">€ <?= arrotondaEFormatta($riga['imponibile']); ?></td>
                                                            <td><?= $riga['numero_fatture']; ?></td>
                                                            <td><?= $riga['numero_bottiglie']; ?> bt</td>
                                                            <td class="tx-right tx-medium tx-inverse"><button class="btn btn-info btn-icon me-2 btn-b vedi-paese" data-pv="<?= $riga['paese'] ?>"><i class="fe fe-eye"></i></button></td>
                                                        </tr>
                                                    <?php endforeach; ?>


                                                </tbody>
                                            </table>
                                        </div>
                                    </div>
                                </div>
                                <div class="row">
                                    <div class="col-sm-12 col-md-12">
                                        <div class="card card-table-two">
                                            <div class=" card-header p-0 d-flex justify-content-between">
                                                <h4 class="card-title mb-1">Migliori Clienti</h4>
                                                <span class="tx-12 tx-muted mb-3 ">Anno <?= $anno ?></span>
                                            </div>

                                            <div class="table-responsive country-table">
                                                <table class="table table-striped table-bordered mb-0 text-sm-nowrap text-lg-nowrap text-xl-nowrap">
                                                    <thead>
                                                        <tr>
                                                            <th>Nome</th>
                                                            <th class="tx-right">Imponibile</th>
                                                            <th>n° fatt</th>
                                                            <th>n° bott</th>
                                                            <th class="tx-right">Pv/St</th>
                                                            <th></th>
                                                        </tr>
                                                    </thead>
                                                    <tbody>
                                                        <?php
                                                        $clienti = analisiMiglioriClienti($anno);
                                                        foreach ($clienti as $riga) :
                                                        ?>
                                                            <tr>
                                                                <td><?= $riga['nome_cliente']; ?></td>
                                                                <td class="tx-right tx-medium tx-inverse">€ <?= arrotondaEFormatta($riga['imponibile']); ?></td>
                                                                <td><?= $riga['numero_fatture']; ?></td>
                                                                <td><?= $riga['numero_bottiglie']; ?> bt</td>
                                                                <td class="tx-right tx-medium tx-inverse"><?= $riga['provincia']; ?></td>
                                                                <td class="tx-right tx-medium tx-inverse"><a href="analisi-clienti.php?c=<?= $riga['id_cfic'] ?>" class="btn btn-info btn-icon me-2 btn-b"><i class="fe fe-eye"></i></a></td>
                                                            </tr>
                                                        <?php endforeach; ?>
                                                    </tbody>
                                                </table>
                                            </div>
                                        </div>
                                    </div>
                                </div>

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

    var datapie = {
        labels: <?= json_encode($labels2) ?>,
        datasets: [{
            data: <?= json_encode(array_map('floatval', $percentuali2)) ?>,
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
    var ctx8 = document.getElementById('chartDonut2');
    var myPieChart7 = new Chart(ctx8, {
        type: 'pie',
        data: datapie,
        options: optionpie
    });
    var anno = '<?= $anno ?>';
</script>