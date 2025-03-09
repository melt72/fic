<?php
include 'partials/headerarea.php';
include 'partials/header.php';
if (isset($_GET['id'])) {
    $id_agente = $_GET['id'];
    if (isset($_GET['a'])) {
        $anno = $_GET['a'];
    } else {
        $anno = date('Y');
    }
} else {
    $id_agente = 0;
    $anno = date('Y');
}
if (isset($_GET['di'])) {
    $data_iniziale = $_GET['di'];
} else {
    $data_iniziale = date('d-m-Y', strtotime('first day of January ' . $anno));
}

if (isset($_GET['df'])) {
    $data_finale = $_GET['df'];
} else {
    $data_finale = date('d-m-Y', strtotime('last day of December ' . $anno));
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
                            <h2 class="main-content-title tx-24 mg-b-1 mg-b-lg-1">Scadenziario</h2>

                        </div>
                    </div>
                    <div class="main-dashboard-header-right">
                    </div>
                </div>
                <!-- breadcrumb -->

                <!-- row -->
                <div class="row row-sm">
                    <div class="col-xl-12 col-lg-12 col-md-12 col-xm-12">
                        <div class="card">
                            <div class="card-body">
                                <label for="agente" class="form-label">Scelta agente</label>

                                <select class="form-select select2-no-search" name="scadenziario" id="scadenziario">
                                    <option>Seleziona Agente</option>
                                    <?php
                                    $agenti = get_agenti_totali();
                                    foreach ($agenti as $agente) { ?>
                                        <option value="<?= $agente['id'] ?>" <?php
                                                                                if (isset($id_agente)) {
                                                                                    if ($id_agente == $agente['id']) {
                                                                                        echo ' selected';
                                                                                    }
                                                                                } ?>>
                                            <?= $agente['nome_agente'] ?></option>
                                    <?php }
                                    ?>

                                </select>
                            </div>
                        </div>

                        <?php
                        // Se cè un agente
                        if ($id_agente != '0') {

                            // mostro la selezione dell'anno e del periodo
                        ?>

                            <div class="row row-sm">
                                <div class="col-md-12">
                                    <div class="card">
                                        <div class="card-body">

                                            <!-- anno e periodo -->
                                            <div class="row">
                                                <div class="col-md-2">
                                                    <?php
                                                    $anni_disponibili = getAnniFatture();
                                                    ?>
                                                    <div>Anno fattura</div>
                                                    <div>
                                                        <select class="form-control  testselect2" id="anno">
                                                            <?php
                                                            foreach ($anni_disponibili as $anno_disp) {
                                                            ?>
                                                                <option value="<?= $anno_disp['anno'] ?>" <?= $anno == $anno_disp['anno'] ? ' selected' : '' ?>><?= $anno_disp['anno'] ?></option>
                                                            <?php
                                                            }
                                                            ?>
                                                        </select>
                                                    </div>
                                                </div>
                                                <div class="col-md-2">
                                                    <label for="start-date">Data Iniziale Scadenza</label>
                                                    <input type="text" id="start-date" class="form-control" placeholder="Start Date" value="<?= $data_iniziale ?>">
                                                </div>

                                                <div class="col-md-2">
                                                    <label for="end-date">Data Finale Scadenza</label>
                                                    <input type="text" id="end-date" class="form-control" placeholder="End Date" value="<?= $data_finale ?>">
                                                </div>
                                            </div>
                                            <div class="row mg-t-20">
                                                <div class="col-md-12">
                                                    <button class="btn btn-success btn-block" id="cercascad">Cerca</button>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>

                            <div id="dati"></div>
                            <!-- <div class="row row-sm">
                                <div class="col-sm-12 col-md-12 col-lg-12 col-xl-12">
                                    <div class="card">
                                        <div class="card-header">
                                            <h3 class="card-title">Fatture</h3>
                                        </div>
                                        <div class="card-body" id="dati">
                                        </div>
                                    </div>
                                </div>
                            </div> -->


                        <?php } ?>



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
    //Quando finisco di caricare la pagina
    $(document).ready(function() {
        var sommaImporti = calcolaSommaImporti();
        //Se importo liquidazSe importo liquidazione è zero disattivo
        if (sommaImporti == 0) {
            $('.liquida_provv').prop('disabled', true);
        } else {
            $('.liquida_provv').prop('disabled', false);
        }
    });

    function calcolaSommaImporti() {
        var sommaImporti = 0;

        $('.inclusa').each(function() {
            var importo = parseFloat($(this).data('importo')) || 0;
            sommaImporti += importo;
        });

        return sommaImporti;
    }

    //Se cambio il valore del select . Ricarico la pagina con il valore del select
    $(document).on('change', '#scadenziario', function(event) {
        event.preventDefault();
        var id_agente = $(this).val();
        window.location = 'scadenziario.php?id=' + id_agente;
    });

    $('#anno').change(function() {
        var anno = $(this).val();
        window.location.href = 'scadenziario.php?a=' + anno + '&id=<?= $id_agente ?>';
    });

    $('#cercascad').click(function() {
        $('#cercascad').html('<span class="spinner-border spinner-border-sm" role="status" aria-hidden="true"></span> Caricamento...');
        $('#dati').html('');

        var di = $('#start-date').val();
        var df = $('#end-date').val();
        var anno = $('#anno').val();
        var id_agente = '<?= $id_agente ?>';
        $.ajax({
            url: 'include/scadenziario.php',
            type: 'POST',
            data: {
                s: di,
                e: df,
                anno: anno,
                agente: id_agente
            },
            dataType: 'html',
            success: function(response) {
                $('#dati').html(response);
                $('#basic-edittable-libera').DataTable();
                $('#cercascad').html('Cerca');
            }
        });
    });
    $(document).ready(function() {
        $('#start-date').datepicker({
            format: "dd-mm-yyyy",
            autoclose: true,
            'language': 'it'
        }).on('changeDate', function(selected) {
            var startDate = new Date(selected.date.valueOf());
            $('#end-date').datepicker('setStartDate', startDate);
        });
        //Month picker
        $('#datepicker-month').bootstrapdatepicker({
            format: "mm/yyyy",
            viewMode: "months",
            minViewMode: "months",
            multidate: false,
            'language': 'it'
        })

        $('#end-date').datepicker({
            format: "dd-mm-yyyy",
            autoclose: true,
            'language': 'it'
        }).on('changeDate', function(selected) {
            var endDate = new Date(selected.date.valueOf());
            $('#start-date').datepicker('setEndDate', endDate);
        });
    });
</script>