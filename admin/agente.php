<?php
include 'partials/headerarea.php';
include 'partials/header.php';
if (isset($_GET['id'])) {
    $id = $_GET['id'];
    $agente = get_agente($id);
    if (isset($_GET['tipo'])) {
        $modifica = '';
        $modifica_sigla = ' disabled'; //non posso modificare la sigla 
        $titolo = 'Modifica Agente';
    } else {
        $modifica = ' disabled';
        $modifica_sigla = ' disabled'; //non posso modificare la sigla
        $titolo = 'Visualizza Agente';
    }
} else {
    $id = '';
    $agente = '';
    $modifica = '';
    $modifica_sigla = '';
    $titolo = 'Creazione Agente';
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
                <form id="formagente" class="form-horizontal needs-validation" novalidate>
                    <!-- breadcrumb -->
                    <div class="breadcrumb-header justify-content-between">
                        <div class="left-content">
                            <div>
                                <h2 class="main-content-title tx-24 mg-b-1 mg-b-lg-1"><?= $titolo ?></h2>

                            </div>
                        </div>
                        <div class="main-dashboard-header-right">
                            <?php
                            if (!empty($agente)) {
                                if ($modifica != '') : // Vedo solo il profilo 
                            ?>
                                    <a href="agente.php?id=<?= $id ?>&tipo=mod" class="btn btn-primary btn-sm btn-salva"><span class="fe fe-refresh-cw"> </span> Abilita modifiche</a>
                                <?php else : // sono in modifica 
                                ?>
                                    <button id="agentesubmitButton" class="btn btn-primary btn-sm btn-salva" data-tipo="mod"><span class="fe fe-save"> </span> Salva Modifiche</button>
                                <?php endif; ?>
                            <?php
                            } else {
                            ?>
                                <button id="agentesubmitButton" class="btn btn-primary btn-sm btn-salva" data-tipo="add"><span class="fe fe-user-plus"> </span> Crea Nuovo Utente</button>
                            <?php
                            }
                            ?>
                        </div>
                    </div>
                    <!-- breadcrumb -->

                    <!-- row -->
                    <div class="row row-sm">
                        <div class="col-xl-12 col-lg-12 col-md-12 col-xm-12">
                            <div class="card">
                                <div class="card-header pb-0">
                                    <h5 class="card-title mb-0 pb-0">Card title</h5>
                                </div>
                                <div class="card-body">
                                    <div class="row">
                                        <div class="col-md-6">
                                            <div class="form-group">
                                                <label class="form-label">Sigla</label>
                                                <input class="form-control" placeholder="Sigla Agente" type="text" id="sigla" name="sigla" value="<?= !empty($agente) ? $agente['sigla'] : '' ?>" required <?= $modifica_sigla ?>>
                                            </div>
                                        </div>
                                        <div class="col-md-6">
                                            <div class="mb-3">
                                                <label for="provv" class="form-label">Prov % default</label>
                                                <?php
                                                $default = !empty($agente) ? $agente['provv'] : '16';
                                                ?>
                                                <select class="form-select select2-no-search" name="provv" id="provv" <?= $modifica ?> required>
                                                    <option>Seleziona %</option>
                                                    <option value="10" <?= $default == 10 ? ' selected' : '' ?>>10%</option>
                                                    <option value="11" <?= $default == 11 ? ' selected' : '' ?>>11%</option>
                                                    <option value="12" <?= $default == 12 ? ' selected' : '' ?>>12%</option>
                                                    <option value="13" <?= $default == 13 ? ' selected' : '' ?>>13%</option>
                                                    <option value="14" <?= $default == 14 ? ' selected' : '' ?>>14%</option>
                                                    <option value="15" <?= $default == 15 ? ' selected' : '' ?>>15%</option>
                                                    <option value="16" <?= $default == 16 ? ' selected' : '' ?>>16%</option>
                                                    <option value="17" <?= $default == 17 ? ' selected' : '' ?>>17%</option>
                                                    <option value="18" <?= $default == 18 ? ' selected' : '' ?>>18%</option>
                                                    <option value="19" <?= $default == 19 ? ' selected' : '' ?>>19%</option>
                                                    <option value="20" <?= $default == 20 ? ' selected' : '' ?>>20%</option>
                                                </select>
                                            </div>

                                        </div>
                                    </div>
                                    <div class="row">
                                        <div class="col-md-12">
                                            <div class="form-group">
                                                <label class="form-label">Nome</label> <input class="form-control" placeholder="Nome del agente o Rag. Sociale" type="text" id="nome_agente" name="nome_agente" value="<?= !empty($agente) ? $agente['nome_agente'] : '' ?>" required <?= $modifica ?>>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="row">
                                        <div class="col-md-12">
                                            <div class="form-group">
                                                <label class="form-label">Descrizione</label>
                                                <textarea class="form-control" id="descrizione" name="descrizione" placeholder="Descrizione/Note per l'agente" rows="5" <?= $modifica ?>><?= !empty($agente) ? $agente['descrizione'] : '' ?></textarea>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                    <!-- row closed -->
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
<script>
    var id = '<?= $id ?>';
</script>