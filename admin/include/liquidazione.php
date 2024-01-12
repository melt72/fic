<?php
if (!empty($_SERVER['HTTP_X_REQUESTED_WITH']) &&  strtolower($_SERVER['HTTP_X_REQUESTED_WITH']) == 'xmlhttprequest') :
    include 'functions.php';
    $tipo = $_POST['tipo'];

    switch ($tipo) {
        case 'liquida':
            $id = $_POST['id_fattura'];
            $id_agente = $_POST['id_agente'];

            $metodo_pagamento = $_POST['metodo_pagamento'];
            $note = $_POST['note'];
            $data_liquidazione = $_POST['data_liquidazione'];
            $data_formato_originale = DateTime::createFromFormat('d/m/Y',  $data_liquidazione);

            // Ottieni la data nel formato desiderato "anno mese giorno"
            $data_formato_desiderato = $data_formato_originale->format('Y-m-d');


            $importo = $_POST['importo_liquidazione'];
            //Ricavo la sigla agente dal suo id
            $sigla_agente = getSiglaAgente($id_agente);
            $id_liquidazione = setLiquidazione($sigla_agente, $data_formato_desiderato, $importo, $metodo_pagamento, $note);
            //aggiorno le fatture con l'id della liquidazione
            foreach ($id as $id_fattura) {
                updateFattureLiquidazione($id_liquidazione, $id_fattura);
            }
            echo $id_liquidazione;

            break;

        default:
            # code...
            break;
    }
else :
    exit();
endif;
