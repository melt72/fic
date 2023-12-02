<?php
include 'partials/headerarea.php';
include 'partials/header.php';
?>
<?php
if (isset($_GET['id'])) {
    $id = $_GET['id'];
    if (isset($_GET['tipo'])) {
        $modifica = '';
    } else {
        $modifica = 'disabled';
    }
    include('../include/configpdo.php');
    try {
        $query = "SELECT * FROM user WHERE id_user=:iduser ";
        $stmt = $db->prepare($query);
        $stmt->bindParam('iduser', $_GET['id'], PDO::PARAM_STR);
        $stmt->execute();
        $utente = $stmt->fetch(PDO::FETCH_ASSOC);
        $autorizza = $utente['ruolo'];
    } catch (PDOException $e) {
        echo "Error : " . $e->getMessage();
    }
} else {
    $autorizza = '';
    $utente = '';
    $modifica = '';
    $id = '';
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
                <form id="formutente" class="form-horizontal needs-validation" novalidate>
                    <!-- breadcrumb -->
                    <div class="breadcrumb-header justify-content-between">
                        <div class="left-content">
                            <div>
                                <h2 class="main-content-title tx-24 mg-b-1 mg-b-lg-1">Utente</h2>

                            </div>
                        </div>
                        <div class="main-dashboard-header-right">
                            <?php
                            if (!empty($utente)) {
                                if ($modifica != '') : // Vedo solo il profilo 
                            ?>
                                    <a href="utente.php?id=<?= $id ?>&tipo=mod" class="btn btn-primary btn-sm btn-salva"><span class="fe fe-refresh-cw"> </span> Abilita modifiche</a>
                                <?php else : // sono in modifica 
                                ?>
                                    <button id="utentesubmitButton" class="btn btn-primary btn-sm btn-salva"><span class="fe fe-save"> </span> Salva Modifiche</button>
                                <?php endif; ?>
                            <?php
                            } else {
                            ?>
                                <button id="utentesubmitButton" class="btn btn-primary btn-sm btn-salva"><span class="fe fe-user-plus"> </span> Crea Nuovo Utente</button>
                            <?php
                            }
                            ?>
                        </div>
                    </div>
                    <!-- breadcrumb -->

                    <!-- row -->
                    <div class="row row-sm">
                        <!-- Col -->
                        <div class="col-xl-4 col-lg-5">
                            <div class="card mg-b-20">
                                <div class="card-body">
                                    <div class="ps-0">
                                        <div class="main-profile-overview">
                                            <div class="main-img-user profile-user">
                                                <img id="fotodelprofilo" alt="" src="<?= fotoProfilo($id) ?>"><label class="fas fa-camera profile-edit"> <input type="file" id="immagineprofilo" name="immagineprofilo" accept=".jpg" class="d-none" onchange="document.getElementById('fotodelprofilo').src = window.URL.createObjectURL(this.files[0])" <?= $modifica ?> /></label>
                                            </div>
                                            <div class="d-flex justify-content-between mg-b-20">
                                                <div>
                                                    <h5 class="main-profile-name"><?= !empty($utente) ? $utente['nome'] . ' ' . $utente['cognome'] : '' ?></h5>
                                                    <p class="main-profile-name-text"><?= ruolo($id) ?></p>
                                                </div>
                                            </div>

                                            <!--skill bar-->
                                        </div><!-- main-profile-overview -->
                                    </div>
                                </div>
                            </div>
                        </div>

                        <!-- Col -->
                        <div class="col-xl-8 col-lg-7">
                            <div class="card">
                                <div class="card-body">
                                    <input type="hidden" id="idutente" name="idutente" value="<?= !empty($utente) ? $utente['id_user'] : '' ?>">
                                    <div class="mb-4 main-content-label">Personal Information</div>

                                    <div class="form-group ">
                                        <div class="row">
                                            <div class="col-md-3">
                                                <label class="form-label">Nome</label>
                                            </div>
                                            <div class="col-md-9">
                                                <input type="text" class="form-control" placeholder="First Name" id="nome" name="nome" value="<?= !empty($utente) ? $utente['nome'] : '' ?>" required <?= $modifica ?>>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="form-group ">
                                        <div class="row">
                                            <div class="col-md-3">
                                                <label class="form-label">Cognome</label>
                                            </div>
                                            <div class="col-md-9">
                                                <input type="text" class="form-control" placeholder="Last Name" id="cognome" name="cognome" value="<?= !empty($utente) ? $utente['cognome'] : '' ?>" required <?= $modifica ?>>
                                            </div>
                                        </div>
                                    </div>

                                    <div class="mb-4 main-content-label">Info di contatto</div>
                                    <div class="form-group ">
                                        <div class="row">
                                            <div class="col-md-3">
                                                <label class="form-label">Email<i>(required)</i></label>
                                            </div>
                                            <div class="col-md-9">
                                                <input type="email" class="form-control" placeholder="Email" id="mail" name="mail" value="<?= !empty($utente) ? $utente['username'] : '' ?>" required <?= $modifica ?>>
                                            </div>
                                        </div>
                                    </div>

                                    <div class="form-group ">
                                        <div class="row">
                                            <div class="col-md-3">
                                                <label class="form-label">Ruolo</label>
                                            </div>
                                            <div class="col-md-9">
                                                <select class="form-control select2-no-search" data-bs-placeholder="Select Country" name="ruolo" id="ruolo" required <?= $modifica ?>>
                                                    <option value="">Scegli ruolo utente</option>
                                                    <option value="sadmin" <?= $autorizza == 'sadmin' ? ' selected' : '' ?>>SuperAdmin</option>
                                                    <option value="admin" <?= $autorizza == 'admin' ? ' selected' : '' ?>>Admin</option>
                                                    <option value="segr" <?php
                                                                            if ($autorizza == 'segr') {
                                                                                echo ' selected';
                                                                            }
                                                                            ?>>Segretaria</option>
                                                    <option value="user" <?php
                                                                            if ($autorizza == 'user') {
                                                                                echo ' selected';
                                                                            }
                                                                            ?>>Utente</option>
                                                    <option value="guest" <?php
                                                                            if ($autorizza == 'guest') {
                                                                                echo ' selected';
                                                                            }
                                                                            ?>>Ospite</option>
                                                </select>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </form>
                <!-- /Col -->
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