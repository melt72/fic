<?php
//******************************************************************
//* configurazione PDO per il collegamento con il database         *
//******************************************************************
try {
    $hostname = "localhost";
    $database = "c2";
    $username = "root";
    $password = "";
    @define("DB_Connect_Charset", "utf8");
    $db = new PDO("mysql:host=$hostname;dbname=$database", $username, $password);
} catch (PDOException $e) {
    echo "Errore: " . $e->getMessage();
    die();
}
