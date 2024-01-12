<?php
include 'functions.php';
include 'include/configpdo.php';

$prodotti = get_products();
//controllo se la fattura è già presente nel database altrimenti la inserisco

foreach ($prodotti as $prodotto) {
    $id = $prodotto['id'];
    $nome = $prodotto['name'];

    //Controllo se nella tabella lista prodotti cè già il prodotto in base al codice
    $sql = "SELECT * FROM lista_prodotti WHERE prod_id = :id";
    $stmt = $db->prepare($sql);
    $stmt->bindParam('id', $id, PDO::PARAM_INT);
    $stmt->execute();
    $result = $stmt->fetch(PDO::FETCH_ASSOC);
    if (!$result) {
        //se il prodotto non è presente nel database lo inserisco
        $sql = "INSERT INTO lista_prodotti (prod_id, nome_prodotto) VALUES (:id, :nome)";
        $stmt = $db->prepare($sql);
        $stmt->bindParam('id', $id, PDO::PARAM_INT);
        $stmt->bindParam('nome', $nome, PDO::PARAM_STR);
        $stmt->execute();
    }
}
