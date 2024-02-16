<?php
if (!empty($_SERVER['HTTP_X_REQUESTED_WITH']) &&  strtolower($_SERVER['HTTP_X_REQUESTED_WITH']) == 'xmlhttprequest') :
    include 'functions.php';
    $tipo = $_POST['tipo'];

    switch ($tipo) {
        case 'liquida':
            $id = $_POST['id_fattura'];
            $id_agente = $_POST['id_agente'];
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
            // $metodo_pagamento = $_POST['metodo_pagamento'];
            // $note = $_POST['note'];
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
            $id_liquidazione = setLiquidazione($sigla_agente, $data_formato_desiderato, $importo);
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
        case 'vedi_liquidazione_roma': // Mostra i dettagli della liquidazione fatture che sono state liquidate per roma
            $id_liquidazione = $_POST['id_liquidazione'];
            $zone = getVediLiquidazioneZoneRoma($id_liquidazione); // Prendo tutte le zone
        ?>
            <table class="table table-bordered border text-nowrap mb-0" id="basic-edittable">
                <thead>
                    <tr>
                        <th>Zona</th>
                        <th>Prov Agente</th>
                        <th>Prov Agenzia</th>

                    </tr>
                </thead>
                <tbody>
                    <?php
                    foreach ($zone as $zona) {
                    ?>
                        <tr>
                            <td><?= $zona['nome'] ?></td>
                            <td><?= arrotondaEFormatta($zona['a']) ?> €</td>
                            <td><?= arrotondaEFormatta($zona['b']) ?> €</td>
                        </tr>
                    <?php
                    }
                    ?>
                </tbody>
            </table>
            </div> <!-- Tabella con le fatture da liquidare -->
        <?php
            break;

        case 'referenza':
            $id_liquidazione = $_POST['id_liquidazione'];
            $dati_liquidazione =  getDatiLiquidazione($id_liquidazione); // Prendo i dati della liquidazione
        ?>
            <div class="row  mg-b-20">
                <div class="col-md-6">
                    <label for="metodo_pagamento">Metodo di pagamento</label>
                    <select class="form-select select2-no-search" name="metodo_pagamento_agente" id="metodo_pagamento_agente">
                        <option value="">Scegli metodo</option>
                        <option value="1" <?= $dati_liquidazione['pagamento'] == '1' ? ' selected' : '' ?>>Bonifico</option>
                        <option value="2" <?= $dati_liquidazione['pagamento'] == '2' ? ' selected' : '' ?>>Assegno</option>
                        <option value="3" <?= $dati_liquidazione['pagamento'] == '3' ? ' selected' : '' ?>>Contanti</option>
                    </select>
                </div>
                <div class="col-md-6"><label for="note">Note</label>
                    <input class="form-control" placeholder="Note di liquidazione" id="note_agente" name="note_agente" value="<?= $dati_liquidazione['note'] ?>"></input>
                </div>
            </div>
        <?php
            break;
        case 'referenzaroma':
            $id_liquidazione = $_POST['id_liquidazione'];
            //  $dati_liquidazione =  getDatiLiquidazione($id_liquidazione); // Prendo i dati della liquidazione
        ?><div class="row">
                <div class="col-md-12 text-end mg-b-20">
                    <button class="btn btn-primary btn-sm nuova-referenza">Nuova referenza</button>
                </div>
            </div>
            <div id="inserimentoroma" style="display: none;">
                <div class="row mg-b-20">
                    <div class="col-md-6">
                        <label for="data">Data</label>
                        <div class="input-group">
                            <div class="input-group-text">
                                <i class="typcn typcn-calendar-outline tx-24 lh--9 op-6"></i>
                            </div>
                            <input class="form-control" id="data_liquidazione_zona2" name="data_liquidazione_zona2" placeholder="MM/DD/YYYY" type="text" required>
                        </div>
                    </div>
                    <div class="col-md-6"><label for="importo_roma">Importo</label>
                        <input class="form-control" type="number" placeholder="Agente o Zona o Referente" id="importo_roma" name="importo_roma"></input>
                    </div>
                </div>
                <div class="row mg-b-20">
                    <div class="col-md-12><label for=" nome_agente_roma">Riferimento Nome</label>
                        <input class="form-control" placeholder="Agente o Zona o Referente" id="nome_agente_roma" name="nome_agente_roma" type="text"></input>
                    </div>
                </div>
                <div class="row mg-b-20">
                    <div class="col-md-6">
                        <label for="metodo_pagamento">Metodo di pagamento</label>
                        <select class="form-select select2-no-search" name="metodo_pagamento_agente" id="metodo_pagamento_agente">
                            <option value="">Scegli metodo</option>
                            <option value="1">Bonifico</option>
                            <option value="2">Assegno</option>
                            <option value="3">Contanti</option>
                        </select>
                    </div>
                    <div class="col-md-6"><label for="note">Note</label>
                        <input class="form-control" placeholder="Note di liquidazione" id="note_agente" name="note_agente"></input>
                    </div>
                </div>
                <div class="row">
                    <div class="col-md-12 text-center"><button class="btn btn-primary inserisci_referenza_roma">Inserisci Referenza</button></div>
                </div>
            </div>
            <?php
            $dati = getNoteLiquidazioneRoma($id_liquidazione);
            if (!empty($dati)) { ?>
                <div class="row mg-b-20">
                    <div class="table-responsive country-table">
                        <table class="table table-striped table-bordered mb-0 text-sm-nowrap text-lg-nowrap text-xl-nowrap">
                            <tbody id="dati_liquida_roma">
                                <?php
                                foreach ($dati as $dato) {
                                ?>
                                    <tr>
                                        <td><?= date('d/m/Y', strtotime($dato['data_pagamento'])) ?></td>
                                        <td><?= strtoupper($dato['nome_liquidaroma']) ?></td>
                                        <td><?= arrotondaEFormatta($dato['importo'])  ?> €</td>
                                        <td class="tx-right tx-medium tx-inverse">
                                            <?= getMetodoPagamento($dato['metodo']); ?>
                                        </td>
                                        <td class="tx-right tx-medium tx-inverse"> <?= $dato['note_liquidaroma']; ?></td>
                                    </tr>
                                <?php
                                }
                                ?>
                            </tbody>
                        </table>
                    </div>
                </div>
            <?php }
            break;
        case 'inserisci_referenza':
            $id_liquidazione = $_POST['id_liquidazione'];
            $metodo_pagamento = $_POST['metodo_pagamento'];
            $note = $_POST['note'];
            include(__DIR__ . '/../../include/configpdo.php');
            try {
                $query = "UPDATE `liquidazioni` SET`pagamento`=:metodo,`note`=:nota WHERE `id`=:id_liquidazione ";
                $stmt = $db->prepare($query);
                $stmt->bindParam(':metodo', $metodo_pagamento, PDO::PARAM_STR);
                $stmt->bindParam(':nota', $note, PDO::PARAM_STR);
                $stmt->bindParam(':id_liquidazione', $id_liquidazione, PDO::PARAM_INT);
                $stmt->execute();
            } catch (PDOException $e) {
                echo "Error : " . $e->getMessage();
            }
            break;

        case 'inserisci_referenza_roma':
            $id_liquidazione = $_POST['id_liquidazione'];
            $metodo_pagamento = $_POST['metodo_pagamento'];
            $nome_agente = $_POST['nome_agente'];
            $data_referenza = $_POST['data_referenza'];
            $importo = $_POST['importo'];
            //Trasformo la data in formato per il db
            $data_formato_originale = DateTime::createFromFormat('d/m/Y',  $data_referenza);
            $data_formato_desiderato = $data_formato_originale->format('Y-m-d');

            $note = $_POST['note'];
            include(__DIR__ . '/../../include/configpdo.php');
            try {
                $query = "INSERT INTO `liquidazioni_roma`(`id_liquidazione`, `nome_liquidaroma`, `metodo`, `importo`, `note_liquidaroma`, `data_pagamento`) VALUES (:id_liquidazione, :agente, :metodo, :importo, :nota, :data_ref) ";
                $stmt = $db->prepare($query);
                $stmt->bindParam(':id_liquidazione', $id_liquidazione, PDO::PARAM_INT);
                $stmt->bindParam(':agente', $nome_agente, PDO::PARAM_STR);
                $stmt->bindParam(':metodo', $metodo_pagamento, PDO::PARAM_STR);
                $stmt->bindParam(':importo', $importo, PDO::PARAM_STR);
                $stmt->bindParam(':nota', $note, PDO::PARAM_STR);
                $stmt->bindParam(':data_ref',  $data_formato_desiderato, PDO::PARAM_STR);
                $stmt->execute();
            } catch (PDOException $e) {
                echo "Error : " . $e->getMessage();
            }
            $dati = getNoteLiquidazioneRoma($id_liquidazione);
            if (!empty($dati)) { ?>

                <?php
                foreach ($dati as $dato) {
                ?>
                    <tr>
                        <td><?= date('d/m/Y', strtotime($dato['data_pagamento'])) ?></td>
                        <td><?= strtoupper($dato['nome_liquidaroma']) ?></td>
                        <td><?= arrotondaEFormatta($dato['importo'])  ?> €</td>
                        <td class="tx-right tx-medium tx-inverse">
                            <?= getMetodoPagamento($dato['metodo']); ?>
                        </td>
                        <td class="tx-right tx-medium tx-inverse"> <?= $dato['note_liquidaroma']; ?></td>
                    </tr>
<?php
                }
            }
            break;
        default:
            # code...
            break;
    }
else :
    exit();
endif;
