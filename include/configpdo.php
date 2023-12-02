<?php
//******************************************************************
//* configurazione PDO per il collegamento con il database         *
//******************************************************************
try {
    $hostname = "localhost";
    $database = "test";
    $username = "root";
    $password = "";
    @define("DB_Connect_Charset", "utf8");
    $db = new PDO("mysql:host=$hostname;dbname=$database", $username, $password);
} catch (PDOException $e) {
    echo "Errore: " . $e->getMessage();
    die();
}
