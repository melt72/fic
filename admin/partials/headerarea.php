<?php
include('../include/init.php');
include('include/functions.php'); ?>
<?php
if (isset($_COOKIE[$sessione])) :
    $utente = $_COOKIE[$sessione];
    $datiutente = DatiUtente($_COOKIE[$sessione]);
    $accesso = $datiutente['accesso'];
else :
    header('location: index.php');
endif;
