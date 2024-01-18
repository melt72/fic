<?php
if (!empty($_SERVER['HTTP_X_REQUESTED_WITH']) &&  strtolower($_SERVER['HTTP_X_REQUESTED_WITH']) == 'xmlhttprequest') :
    include 'functions.php';
    $anno = $_POST['anno'];
    $varieta = $_POST['varieta'];

    $distribuzione = analisiBottigliePerMeseVarieta($anno, $varieta);
    $a = implode(", ",    $distribuzione);
    // Ritorna in formato Json

    echo json_encode($distribuzione);

else :
    exit();
endif;
