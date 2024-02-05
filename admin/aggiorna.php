<?php
include 'partials/headerarea.php';
include 'partials/header.php';
if (isset($_GET['a'])) {
    $anno = $_GET['a'];
} else {
    $anno = getAnnoRecente();
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
                            <h2 class="main-content-title tx-24 mg-b-1 mg-b-lg-1">Aggiornamenteo Database</h2>
                        </div>
                    </div>
                </div>
                <!-- breadcrumb -->

                <!-- row -->
                <div class="row row-sm">
                    <div class="col-xl-12 col-lg-12 col-md-12 col-xm-12">
                        <div class="card">
                            <div class="card-header pb-0">
                                <h5 class="card-title mb-0 pb-0">Progresso aggiornamento</h5>
                            </div>
                            <div class="card-body">
                                <p class="text-center" id="stato-bar"></p>
                                <div class="progress mg-b-20">
                                    <div class="progress-bar progress-bar-striped" role="progressbar" aria-valuenow="0" aria-valuemin="0" aria-valuemax="100"></div>
                                </div>
                            </div>
                            <div class="card-footer text-end">
                                <button class="btn btn-primary btn-sm aggiorna">Aggiorna</button>
                            </div>
                        </div>
                        <div class="card">
                            <div class="card-header pb-0">
                                <h5 class="card-title mb-0 pb-0">Monitor</h5>
                            </div>
                            <div class="card-body">
                                <div class="form-group">
                                    <textarea id="monitor" class="form-control" name="monitor" rows="10" readonly></textarea>
                                </div>

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
<script>
    $(document).ready(function() {
        var totalRequests = 4;
        var completedRequests = 0;

        function performAjaxCall(counter) {
            //Indico un array con i quattro status
            var status = ['Controllo clienti', 'Controllo prodotti', 'Controllo nuove fatture', 'Controllo fatture da aggiornare'];
            // Aggiorna la barra di avanzamento
            $('#stato-bar').html(status[counter - 1]);
            $.ajax({
                url: '../aggiorna_tutto.php', // Sostituisci con il tuo URL
                method: 'GET',
                data: {
                    counter: counter
                }, // Aggiungi il contatore come parametro
                success: function(data) {
                    //aggiungo il risultato al monitor
                    $('#monitor').append(data);
                    updateProgressBar();

                    if (completedRequests < totalRequests) {
                        // Passa al contatore successivo
                        performAjaxCall(counter + 1);
                    } else {
                        //aggiorno la pagina
                        $('#monitor').append('Aggiornamento completato');
                    }
                },
                error: function() {
                    // Gestisci gli errori se necessario
                }
            });
        }

        function updateProgressBar() {
            completedRequests++;
            var percentage = (completedRequests / totalRequests) * 100;
            $('.progress-bar').css('width', percentage + '%').attr('aria-valuenow', percentage);

            if (completedRequests === totalRequests) {
                // Riabilita il bottone
                $('.aggiorna').prop('disabled', false);
                console.log('Tutte le chiamate sono complete.');
            }
        }
        //Se schiaccio il bottone con classe aggiorna eseguo la funzione performAjaxCall
        $('.aggiorna').click(function() {
            //Disabilito il bottone
            $(this).prop('disabled', true);
            //resetto il monitor
            $('#monitor').html('Inizio aggiornamento');
            performAjaxCall(1); // Inizia con il contatore iniziale
        });


    });
</script>