<?php
if (!empty($_SERVER['HTTP_X_REQUESTED_WITH']) &&  strtolower($_SERVER['HTTP_X_REQUESTED_WITH']) == 'xmlhttprequest') :
    include 'functions.php';
    include __DIR__ . '/../../include/configpdo.php';

    $tipo = $_POST['tipo'];
    switch ($tipo) {
        case 'add': // aggiungo una nuova zona
            if (isset($_POST['nome']) && isset($_POST['provv'])) :
                $nome_zona = $_POST['nome'];
                $provv = $_POST['provv'];
                $sql = "INSERT INTO zone_roma (nome_zona, provv) VALUES (:nome_zona, :provv)";
                $stmt = $db->prepare($sql);
                $stmt->bindParam('nome_zona', $nome_zona, PDO::PARAM_STR);
                $stmt->bindParam('provv', $provv, PDO::PARAM_STR);

                $stmt->execute();
                $zone = get_zone();
                $n = 1;
                $tab = '';
                $tabcontent = '';
                foreach ($zone as $zona) {
                    $tab .= '<li class=""><a href="#tab' . $zona['id_zona'] . '" class="' . ($n == 1 ? 'active' : '') . '" data-bs-toggle="tab"><i class="fa fa-laptop me-1"></i> ' . $zona['nome_zona'] . '</a></li>';
                    $tabcontent .= '<div class="tab-pane ' . ($n == 1 ? 'active' : '') . '" id="tab' . $zona['id_zona'] . '">';
                    $tabcontent .= '</div>';
                    $n++;
                }
                //Ritorno un json con i dati
                $response = array(
                    'tab' => $tab,
                    'tabcontent' => $tabcontent
                );
                echo json_encode($response);
            endif;
            # code...
            break;
        case 'mod':
            $id_zona = $_POST['idzona'];
            $tipo = $_POST['prov_tipo'];
            //Modifica il valore nella tabella zone_roma
            $sql = "UPDATE zone_roma SET provv = :tipo WHERE id_zona = :id_zona";
            $stmt = $db->prepare($sql);
            $stmt->bindParam('tipo', $tipo, PDO::PARAM_STR);
            $stmt->bindParam('id_zona', $id_zona, PDO::PARAM_STR);
            $stmt->execute();
            # code...
            break;
        case 'del':
            # code...
            break;

        case 'associa': // associa un cliente ad una zona

            $id_cliente = $_POST['idcliente'];
            $id_zona = $_POST['idzona'];

            $sql = "INSERT INTO agenti_roma (id_cfic, id_zona) VALUES (:id_cliente, :id_zona)";
            $stmt = $db->prepare($sql);
            $stmt->bindParam('id_cliente', $id_cliente, PDO::PARAM_STR);
            $stmt->bindParam('id_zona', $id_zona, PDO::PARAM_STR);
            $stmt->execute();
            //leggo il nome della zona_roma
            $zona = get_nome_zona($id_zona);
            $response = array(
                'zona' => $zona
            );
            //Ritorno un json con i dati
            echo json_encode($response);

            break;

        case 'disassocia': // disassocia un cliente da una zona
            $id_cliente = $_POST['idcliente'];
            $id_zona = $_POST['idzona'];
            $sql = "DELETE FROM agenti_roma WHERE id_cfic = :id_cliente";
            $stmt = $db->prepare($sql);
            $stmt->bindParam('id_cliente', $id_cliente, PDO::PARAM_STR);

            $stmt->execute();
            $response = array(
                'zona' => '--'
            );
            //Ritorno un json con i dati
            echo json_encode($response);

            break;
    }


else :
    exit();
endif;
