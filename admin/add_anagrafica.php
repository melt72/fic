<?php
include 'partials/headerarea.php';
include 'partials/header.php';

if (isset($_GET['id'])) :
    $id = $_GET['id'];
    $cliente = anagrafica($id);
    $modifica = '';
    $salva = ' style="display: none;"';
    $abilita = ' disabled';
else :
    $id = '';
    $cliente = '';
    $modifica = ' style="display: none;"';
    $salva = '';
    $abilita = '';
endif;
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
                <div class="breadcrumb-header justify-content-between ">
                    <div class="left-content">
                        <div>
                            <h2 class="main-content-title tx-24 mg-b-1 mg-b-lg-1">Aggiungi anagrafica</h2>
                        </div>
                    </div>
                    <div class="main-dashboard-header-right">
                        <a href="anagrafica.php" class="btn btn-secondary btn-sm mg-r-2"><span class="fe fe-x-circle"></span> Chiudi</a>
                        <span id="modifica" <?= $modifica ?>>
                            <button class="btn btn-warning btn-sm mg-r-2 abilitamodifiche"><span class="fe fe-check-circle"></span> Abilita Modifiche</button>
                        </span>
                        <span id="salvataggio" <?= $salva ?>>
                            <button class="btn btn-success btn-sm mg-r-2 addmodcliente" data-bk="s" <?= !empty($cliente) ? 'data-tipo="mod"' : 'data-tipo="add"' ?>><span class="fe fe-check-circle"></span> Salva</button>
                            <button class="btn btn-primary btn-sm addmodcliente" data-bk="se" <?= !empty($cliente) ? 'data-tipo="mod"' : 'data-tipo="add"' ?>><span class="fe fe-check-circle"></span> Salva e chiudi</button>
                        </span>

                    </div>
                </div>

                <!-- breadcrumb -->

                <!-- row -->
                <div class="row row-sm">
                    <div class="col-xl-12">
                        <form id="formclienti" name="formclienti" class="needs-validation" novalidate>
                            <div class="card">
                                <div class="card-header">
                                    <h3 class="card-title">Generale</h3>
                                </div>
                                <!-- /.card-header -->
                                <div class="card-body">
                                    <div class="row">
                                        <div class="col-md-4 d-flex align-items-center">
                                            <div class="checkbox icheck-default">
                                                <input type="checkbox" id="privato" name="privato" value="1" <?php
                                                                                                                if (!empty($cliente)) :
                                                                                                                    if ($cliente['privato'] == 1) {
                                                                                                                        echo ' checked';
                                                                                                                    }
                                                                                                                endif;
                                                                                                                ?> <?= $abilita ?> />
                                                <label for="privato">Privato</label>
                                            </div>
                                        </div>
                                        <div class="col-md-4">

                                            <div class="form-group input-material">
                                                <?php
                                                if (!empty($cliente)) :
                                                    $tipo = $cliente['tipo'];
                                                else :
                                                    $tipo = '';
                                                endif;
                                                ?>
                                                <select class="form-control " name="tipo" id="tipo" <?= $abilita ?>>
                                                    <option value="" <?= $tipo == '' ? 'selected' : '' ?>></option>
                                                    <option value="c" <?= $tipo == 'c' ? 'selected' : '' ?>>Cliente</option>
                                                    <option value="f" <?= $tipo == 'f' ? 'selected' : '' ?>>Fornitore</option>
                                                    <option value="cf" <?= $tipo == 'cf' ? 'selected' : '' ?>>Cliente/Fornitore</option>
                                                </select>
                                                <label for="tipo">Tipo</label>
                                            </div>

                                        </div>
                                        <div class="col-md-4">
                                            <div class="form-group  input-material ">
                                                <input type="text" class="form-control" name="codcliente" id="codcliente" value="<?= !empty($cliente) ? $cliente['codcliente'] :  PasswordCasuale(6, 1) ?>" <?= !empty($cliente) ? 'disabled' :  '' ?>required> <label for="codcliente">Codice</label>
                                            </div>
                                        </div>
                                    </div>
                                    <?php if (!empty($cliente)) {
                                        if ($cliente['privato'] == '1') {
                                            $vediprivato = '';
                                            $vedirs =  'style="display: none;"';
                                        } else {
                                            $vediprivato = 'style="display: none;"';
                                            $vedirs = '';
                                        }
                                    } else {
                                        $vediprivato = 'style="display: none;"';
                                        $vedirs = '';
                                    } ?>
                                    <div id="datiprivato" class="row" <?= $vediprivato ?>>
                                        <div class="col-md-4">
                                            <div class="form-group input-material">
                                                <input type="text" class="form-control" name="nome" id="nome" value="<?= !empty($cliente) ? $cliente['nome'] : '' ?>" <?= $abilita ?>>
                                                <label for="nome">Nome</label>
                                            </div>
                                        </div>
                                        <div class="col-md-4">
                                            <div class="form-group input-material">
                                                <input type="text" class="form-control" name="cognome" id="cognome" value="<?= !empty($cliente) ? $cliente['cognome'] : '' ?>" <?= $abilita ?>>
                                                <label for="cognome">Cognome</label>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="row" id="datirs" <?= $vedirs ?>>
                                        <div class="col-md-8">
                                            <div class="form-group  input-material">

                                                <input type="text" class="form-control" name="rs" id="rs" value="<?= !empty($cliente) ? $cliente['rs'] : '' ?>" required <?= $abilita ?>>
                                                <label for="rs">Ragione Sociale</label>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="row">
                                        <div class="col-md-4">
                                            <div class="form-group  input-material">

                                                <input type="text" class="form-control" name="piva" id="piva" value="<?= !empty($cliente) ? $cliente['piva'] : '' ?>" <?= $abilita ?>> <label for="piva">P.IVA</label>
                                            </div>
                                        </div>
                                        <div class="col-md-4">
                                            <div class="form-group  input-material">
                                                <input type="text" class="form-control" name="cf" id="cf" value="<?= !empty($cliente) ? $cliente['cf'] : '' ?>" <?= $abilita ?>> <label for="">Cod. Fiscale</label>
                                            </div>
                                        </div>
                                        <div class="col-md-4">
                                            <div class="form-group  input-material">
                                                <input type="text" class="form-control" name="cd" id="cd" value="<?= !empty($cliente) ? $cliente['cd'] : '' ?>" <?= $abilita ?>><label for="">Codice Destinatario</label>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="row">
                                        <div class="col-md-4">
                                            <div class="form-group  input-material">
                                                <input type="text" class="form-control" name="tel" id="tel" value="<?= !empty($cliente) ? $cliente['tel'] : '' ?>" <?= $abilita ?>> <label for="tel">Tel.</label>
                                            </div>
                                        </div>
                                        <div class="col-md-4">
                                            <div class="form-group  input-material">
                                                <input type="text" class="form-control" name="cell" id="cell" value="<?= !empty($cliente) ? $cliente['cell'] : '' ?>" required <?= $abilita ?>><label for="cell">Cell.</label>
                                            </div>
                                        </div>
                                        <div class="col-md-4">
                                            <div class="form-group  input-material">
                                                <input type="text" class="form-control" name="fax" id="fax" value="<?= !empty($cliente) ? $cliente['fax'] : '' ?>" <?= $abilita ?>> <label for="fax">Fax</label>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="row">
                                        <div class="col-md-4">
                                            <div class="form-group  input-material">
                                                <input type="text" class="form-control" name="email" id="email" value="<?= !empty($cliente) ? $cliente['email'] : '' ?>" required <?= $abilita ?>><label for="mail">E-mail</label>
                                                <div></div>
                                            </div>
                                        </div>
                                        <div class="col-md-4">
                                            <div class="form-group  input-material">
                                                <input type="text" class="form-control" name="pec" id="pec" value="<?= !empty($cliente) ? $cliente['pec'] : '' ?>" <?= $abilita ?>> <label for="pec">PEC</label>
                                            </div>
                                        </div>
                                        <div class="col-md-4">
                                            <div class="form-group  input-material">
                                                <input type="text" class="form-control" name="www" id="www" value="<?= !empty($cliente) ? $cliente['www'] : '' ?>" <?= $abilita ?>> <label for="www">WWW</label>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                                <!-- /.card-body -->
                            </div>
                            <div class="card">
                                <div class="card-header">
                                    <h3 class="card-title">Sede Legale</h3>
                                </div>
                                <!-- /.card-header -->
                                <div class="card-body">
                                    <div class="row">
                                        <div class="col-md-8">
                                            <div class="form-group  input-material">
                                                <input type="text" class="form-control" name="indirizzo" id="indirizzo" value="<?= !empty($cliente) ? $cliente['indirizzo'] : '' ?>" <?= $abilita ?>> <label for="ind">Indirizzo</label>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="row">
                                        <div class="col-md-2">
                                            <div class="form-group  input-material">
                                                <input type="text" class="form-control" name="cap" id="cap" value="<?= !empty($cliente) ? $cliente['cap'] : '' ?>" <?= $abilita ?>> <label for="cap">CAP</label>
                                            </div>
                                        </div>
                                        <div class="col-md-5">
                                            <div class="form-group  input-material">
                                                <input type="text" class="form-control" name="citta" id="citta" value="<?= !empty($cliente) ? $cliente['citta'] : '' ?>" <?= $abilita ?>> <label for="citta">Città</label>
                                            </div>
                                        </div>
                                        <div class="col-md-2">
                                            <div class="form-group  input-material">
                                                <input type="text" class="form-control" name="pv" id="pv" value="<?= !empty($cliente) ? $cliente['pv'] : '' ?>" <?= $abilita ?>><label for="pv">Sigla provincia</label>
                                            </div>
                                        </div>
                                        <div class="col-md-3">
                                            <div class="form-group  input-material">
                                                <input type="text" class="form-control" name="pve" id="pve" value="<?= !empty($cliente) ? $cliente['pve'] : '' ?>" <?= $abilita ?>> <label for="pve">Provincia (estesa)</label>
                                            </div>
                                        </div>
                                    </div>


                                    <div class="row">
                                        <div class="col-md-4">

                                            <div class="form-group  input-material">
                                                <?php
                                                if (!empty($cliente)) :
                                                    $stato = $cliente['stato'];
                                                else :
                                                    $stato = '';
                                                endif;
                                                ?>
                                                <select class="form-control" name="stato" id="stato" <?= $abilita ?>>
                                                    <option value=""></option>
                                                    <option value="116">Italia</option>
                                                    <?php
                                                    $nazioni = getNazioni();
                                                    foreach ($nazioni as $row) { ?>
                                                        <option value="<?= $row['id_stati'] ?>" <?= $stato == $row['id_stati'] ? 'selected' : '' ?>><?= $row['nome_stati']  ?></option>
                                                    <?php }
                                                    ?>
                                                </select>
                                                <label for="stato">Stato</label>

                                            </div>
                                        </div>
                                    </div>
                                </div>
                                <!-- /.card-body -->
                            </div>
                        </form>
                    </div>
                    <!--/div-->
                </div>
                <!-- /row -->

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