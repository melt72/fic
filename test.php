<?php
include 'functions.php';
include 'include/configpdo.php';


try {
    $query = "SELECT * FROM `lista_prodotti`";
    $stmt = $db->prepare($query);
    $stmt->execute();
    $dati   = $stmt->fetchAll();
    foreach ($dati as $row) {
        $cod_prod = $row['cod_prod'];
        $nome_prodotto = $row['nome_prodotto'];
        $prod_id = $row['prod_id']; //id del prodotto nella tabella prodotti
        $varieta = $row['varieta'];
        $tipo = $row['tipo'];
        //controllo se il prodotto è già presente nella tabella lista_prodotti2
        $sql = "SELECT * FROM lista_prodotti2 WHERE nome_prodotto = :nome";
        $stmt = $db->prepare($sql);
        $stmt->bindParam('nome', $row['nome_prodotto'], PDO::PARAM_STR);
        $stmt->execute();
        $result = $stmt->fetch(PDO::FETCH_ASSOC);
        if ($result) { //se il prodotto vado oltr e

            $prod_id_l2 = $result['prod_id']; //id del prodotto nella tabella lista_prodotti2

            //modifico nella tabella prodotti il prod_id con il prod_id della tabella lista_prodotti2          


            $sql = "UPDATE prodotti SET id_prod = :prod_id_l2 WHERE id_prod = :prod_id";
            $stmt = $db->prepare($sql);
            $stmt->bindParam('prod_id_l2', $prod_id_l2, PDO::PARAM_STR);
            $stmt->bindParam('prod_id', $prod_id, PDO::PARAM_STR);
            $stmt->execute();

            echo 'prodotto presente ' . $nome_prodotto . ' ' . $prod_id . '-' . $prod_id_l2 . '<br>';


            continue;
        } else {
            //se il prodotto non è presente nella lista prodotti allora lo inserisco nella lista prodotti2
            echo 'prodotto non presente' . $nome_prodotto . '<br>';
            try {
                $query = "INSERT INTO `lista_prodotti2`(`cod_prod`, `prod_id`, `nome_prodotto`, `varieta`, `tipo`) VALUES (:cod_prod, :prod_id, :nome_prodotto, :varieta, :tipo)";
                $stmt = $db->prepare($query);
                $stmt->bindParam('cod_prod', $cod_prod, PDO::PARAM_STR);
                $stmt->bindParam('prod_id', $prod_id, PDO::PARAM_STR);
                $stmt->bindParam('nome_prodotto', $nome_prodotto, PDO::PARAM_STR);
                $stmt->bindParam('varieta', $varieta, PDO::PARAM_STR);
                $stmt->bindParam('tipo', $tipo, PDO::PARAM_STR);
                $stmt->execute();
                echo 'prodotto inserito<br>';
            } catch (PDOException $e) {
                echo "Error : " . $e->getMessage();
            }
        }
    }
} catch (PDOException $e) {
    echo "Error : " . $e->getMessage();
}
