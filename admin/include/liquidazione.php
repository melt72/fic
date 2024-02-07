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
            $importo = 0;
            //Ricavo la sigla agente dal suo id
            $sigla_agente = getSiglaAgente($id_agente);
            foreach ($id as $id_fattura) {
                $importo += importoliquidzione($id_fattura);
            }
            // $id_liquidazione = setLiquidazione($sigla_agente, $data_formato_desiderato, $importo, $metodo_pagamento, $note);
            $id_liquidazione = setLiquidazione($sigla_agente, $data_formato_desiderato, $importo);
            //aggiorno le fatture con l'id della liquidazione
            foreach ($id as $id_fattura) {
                updateFattureLiquidazione($id_liquidazione, $id_fattura);
            }
            echo $id_liquidazione;

            break;
        case 'lista_roma':

            $zone = getTotaleLiquidazioneZoneRoma(); // Prendo tutte le zone
            foreach ($zone as $zona) {
?>
                <tr>
                    <td><?= $zona['nome'] ?></td>
                    <td><?= arrotondaEFormatta($zona['a']) ?> €</td>
                    <td><?= arrotondaEFormatta($zona['b']) ?> €</td>
                    <td></td>
                </tr>
            <?php

            }

            break;
        case 'liquida_zona':
            $id = $_POST['id_fattura'];
            $sigla_agente = 'RSC';
            $metodo_pagamento = $_POST['metodo_pagamento'];
            $note = $_POST['note'];
            $data_liquidazione = $_POST['data_liquidazione'];
            $data_formato_originale = DateTime::createFromFormat('d/m/Y',  $data_liquidazione);

            // Ottieni la data nel formato desiderato "anno mese giorno"
            $data_formato_desiderato = $data_formato_originale->format('Y-m-d');
            $importo = 0;
            //Ricavo la sigla agente dal suo id
            $importo = 0;
            // foreach ($id as $id_fattura) {
            //     $importo += importoliquidzione($id_fattura);
            // }
            $id_liquidazione = setLiquidazione($sigla_agente, $data_formato_desiderato, $importo, $metodo_pagamento, $note);
            //aggiorno le fatture con l'id della liquidazione
            foreach ($id as $id_fattura) {
                updateFattureLiquidazione($id_liquidazione, $id_fattura);
            }
            echo $id_liquidazione;

            break;

        case 'vedi_liquidazione': // Mostra i dettagli della liquidazione fatture che sono state liquidate
            $id_liquidazione = $_POST['id_liquidazione'];
            $liquidazione = getLiquidazione($id_liquidazione);
            ?>
            <div class="row">
                <div class="col-md-12">
                    <div class="table-responsive">
                        <table class="table table-bordered border text-nowrap mb-0" id="basic-edittable">
                            <thead>
                                <tr>
                                    <th>Cliente</th>
                                    <th>Fattura</th>
                                    <th>Importo</th>
                                    <th>Prov %</th>
                                    <th>Prov €</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php
                                foreach ($liquidazione as $fattura) {
                                    $provvigione = arrotondaEFormatta($fattura['provvigione']);
                                ?>
                                    <tr>
                                        <td><?= $fattura['nome_cliente'] ?></td>
                                        <td>N° <?= $fattura['num_f'] ?> del<br> <?= date('d/m/Y', strtotime($fattura['data_f'])) ?></td>
                                        <td><?= arrotondaEFormatta($fattura['imp_netto']) ?> €</td>
                                        <td><?= $fattura['provv_percent'] ?> %</td>
                                        <td><?= $provvigione ?> €</td>
                                    </tr>
                                <?php
                                }
                                ?>
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        <?php
            break;
        case 'referenza':
        ?>
            <div class="row  mg-b-20">
                <div class="col-md-6">
                    <label for="metodo_pagamento">Metodo di pagamento</label>
                    <select class="form-select select2-no-search" name="metodo_pagamento" id="metodo_pagamento">
                        <option value="">Scegli metodo</option>
                        <option value="1">Bonifico</option>
                        <option value="2">Assegno</option>
                        <option value="3">Contanti</option>
                    </select>
                </div>
                <div class="col-md-6"><label for="note">Note</label>
                    <input class="form-control" placeholder="Eventuali note di liquidazione" id="note" name="note"></input>
                </div>
            </div>
<?php
        default:
            # code...
            break;
    }
else :
    exit();
endif;
