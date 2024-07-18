<?php
if (!empty($_SERVER['HTTP_X_REQUESTED_WITH']) &&  strtolower($_SERVER['HTTP_X_REQUESTED_WITH']) == 'xmlhttprequest') :
    include 'functions.php';
    include(__DIR__ . '/../../include/configpdo.php');
    $tipo = $_POST['tipo'];
    switch ($tipo) {
        case 'lista': //Lista delle fatture per un determinato anno
            $fatture = get_fatture_agente($_POST['id'], $_POST['anno']);
            foreach ($fatture as $fattura) {
?>
                <tr>
                    <td><?= $fattura['nome'] ?></td>
                    <td><?= $fattura['num_f'] ?></td>
                    <td>Data: <?= date('d/m/Y', strtotime($fattura['data_f'])) ?><br>Scad: <?= date('d/m/Y', strtotime($fattura['data_scadenza'])) ?> </td>
                    <td><?= arrotondaEFormatta($fattura['imp_tot']) ?> €</td>
                    <td><?= arrotondaEFormatta($fattura['imp_netto']) ?> €</td>
                    <td><?= arrotondaEFormatta($fattura['imp_iva'])  ?> €</td>
                    <td><?php
                        if ($fattura['id_liquidazione'] != '') : ?>
                            <?= $fattura['provv_percent'] ?> %
                        <?php else : ?>
                            <a href="#" data-pk="<?= $fattura['id_fatt'] ?>"><?= $fattura['provv_percent'] ?> %</a>
                        <?php endif ?>
                    </td>
                    <td id="prov_<?= $fattura['id_fatt'] ?>"><?= arrotondaEFormatta($fattura['imp_netto'] * $fattura['provv_percent'] / 100) ?> €</td>
                    <td><?= status_fattura($fattura['id_fatt']) ?></td>
                </tr>
<?php }
            break;



        default:
            # code...
            break;
    }
else :
    exit();
endif;
