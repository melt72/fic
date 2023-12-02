<?php
if (!empty($_SERVER['HTTP_X_REQUESTED_WITH']) &&  strtolower($_SERVER['HTTP_X_REQUESTED_WITH']) == 'xmlhttprequest') :

    include 'functions.php';
    if ($_POST['tipodiinserimento'] == 'add') :
        inserisci('anagrafica', $_POST['dat']);
        print_r($_POST['dat']);
    else :
        modifica('anagrafica', $_POST['dat'], $_POST['id']);
    endif;

else :
    exit();
endif;
