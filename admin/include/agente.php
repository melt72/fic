<?php
if (!empty($_SERVER['HTTP_X_REQUESTED_WITH']) &&  strtolower($_SERVER['HTTP_X_REQUESTED_WITH']) == 'xmlhttprequest') :

    include(__DIR__ . '/../../include/configpdo.php');
    $tipo = $_POST['tipo'];
    switch ($tipo) {
        case 'add':
            $nome_agente = $_POST['nome'];
            //sigla in maiuscolo
            $sigla = strtoupper($_POST['sigla']);

            $provv = $_POST['prov'];
            $descrizione = $_POST['descrizione'];

            //Controllo che la sigla non sia già presente nel database degli agenti

            try {
                $query = "SELECT * FROM `agenti` WHERE `sigla`= :sigla ";
                $stmt = $db->prepare($query);
                $stmt->bindParam(':sigla', $sigla, PDO::PARAM_STR);
                $stmt->execute();
                $row   = $stmt->fetch(PDO::FETCH_ASSOC);
                if ($row) : //Sigla già presente
                    $successo = false;
                    $messaggio = 'Sigla già presente';
                else : //Sigla non presente inserisco l'agente

                    $query = "INSERT INTO `agenti` (`nome_agente`, `sigla`, `provv`,`descrizione`) VALUES (:nome_agente, :sigla, :provv, :descrizione)";
                    $stmt = $db->prepare($query);
                    $stmt->bindParam(':nome_agente', $nome_agente, PDO::PARAM_STR);
                    $stmt->bindParam(':sigla', $sigla, PDO::PARAM_STR);
                    $stmt->bindParam(':provv', $provv, PDO::PARAM_STR);
                    $stmt->bindParam(':descrizione', $descrizione, PDO::PARAM_STR);
                    $stmt->execute();
                    $successo = true;
                    $messaggio = 'Agente inserito correttamente';
                endif;
            } catch (PDOException $e) {
                echo "Error : " . $e->getMessage();
            }
            // creo un json
            $json = array(
                'status' => $successo,
                'messaggio' => $messaggio,
            );
            // lo trasformo in json
            $json = json_encode($json);
            // lo mando indietro
            echo $json;
            break;

        case 'mod':
            $id_agente = $_POST['id'];
            $nome_agente = $_POST['nome'];
            //sigla in maiuscolo
            $sigla = strtoupper($_POST['sigla']);

            $provv = $_POST['prov'];
            $descrizione = $_POST['descrizione'];

            //Controllo che la sigla non sia già presente nel database degli agenti

            try {
                $query = "SELECT * FROM `agenti` WHERE `sigla`= :sigla AND `id` != :id_agente";
                $stmt = $db->prepare($query);
                $stmt->bindParam(':sigla', $sigla, PDO::PARAM_STR);
                $stmt->bindParam(':id_agente', $id_agente, PDO::PARAM_INT);
                $stmt->execute();
                $row   = $stmt->fetch(PDO::FETCH_ASSOC);
                if ($row) : //Sigla già presente
                    $successo = false;
                    $messaggio = 'Sigla già presente';
                else : //Sigla non presente inserisco l'agente

                    $query = "UPDATE `agenti` SET `nome_agente` = :nome_agente, `sigla` = :sigla, `provv` = :provv, `descrizione` = :descrizione WHERE `id` = :id_agente";
                    $stmt = $db->prepare($query);
                    $stmt->bindParam(':nome_agente', $nome_agente, PDO::PARAM_STR);
                    $stmt->bindParam(':sigla', $sigla, PDO::PARAM_STR);
                    $stmt->bindParam(':provv', $provv, PDO::PARAM_STR);
                    $stmt->bindParam(':descrizione', $descrizione, PDO::PARAM_STR);
                    $stmt->bindParam(':id_agente', $id_agente, PDO::PARAM_INT);
                    $stmt->execute();
                    $successo = true;
                    $messaggio = 'Agente modificato correttamente';
                endif;
            } catch (PDOException $e) {
                echo "Error : " . $e->getMessage();
            }
            // creo un json
            $json = array(
                'status' => $successo,
                'messaggio' => $messaggio,
            );
            // lo trasformo in json
            $json = json_encode($json);
            // lo mando indietro
            echo $json;
            break;

        case 'del':
            $id_agente = $_POST['id'];
            try {
                $query = "DELETE FROM `agenti` WHERE `id` = :id_agente";
                $stmt = $db->prepare($query);
                $stmt->bindParam(':id_agente', $id_agente, PDO::PARAM_INT);
                $stmt->execute();
            } catch (PDOException $e) {
                echo "Error : " . $e->getMessage();
            }
            break;
    }
else :
    exit();
endif;
