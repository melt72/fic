<?php

use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\Exception;
use PHPMailer\PHPMailer\SMTP;
/* Exception class. */

require '../assets/vendor/PHPMailer/src/Exception.php';

/* The main PHPMailer class. */
require '../assets/vendor/PHPMailer/src/PHPMailer.php';

/* SMTP class, needed if you want to use SMTP. */
require '../assets/vendor/PHPMailer/src/SMTP.php';

// leggo i parametri di configurazione dal database
include('../../include/configpdo.php');
try {
    $query = "SELECT * FROM `config` WHERE `tipo_config`='email'";
    $stmt = $db->prepare($query);
    $stmt->execute();
    $dati   = $stmt->fetchAll();
    foreach ($dati as $row) {
        // creo un array con i parametri di configurazione
        $config[$row['parametro_config']] = $row['valore_config'];
    }
} catch (PDOException $e) {
    echo "Error : " . $e->getMessage();
}


//Create a new PHPMailer instance
$mail = new PHPMailer;

//Tell PHPMailer to use SMTP
$mail->isSMTP();

//Enable SMTP debugging
// SMTP::DEBUG_OFF = off (for production use)
// SMTP::DEBUG_CLIENT = client messages
// SMTP::DEBUG_SERVER = client and server messages
$mail->SMTPDebug = SMTP::DEBUG_OFF;

//Set the hostname of the mail server
$mail->Host = $config['host'];
// use
// $mail->Host = gethostbyname('smtp.gmail.com');
// if your network does not support SMTP over IPv6

//Set the SMTP port number - 587 for authenticated TLS, a.k.a. RFC4409 SMTP submission
$mail->Port = $config['port'];

//Set the encryption mechanism to use - STARTTLS or SMTPS
$mail->SMTPSecure = PHPMailer::ENCRYPTION_SMTPS;

//Whether to use SMTP authentication
$mail->SMTPAuth = true;

//Username to use for SMTP authentication - use full email address for gmail
$mail->Username = $config['username'];

//Password to use for SMTP authentication
$mail->Password = $config['password'];

//Set who the message is to be sent from da chi
$mail->setFrom($config['from'], $config['fromname']);

//Set an alternative reply-to address
$mail->addReplyTo($config['replayto'], $config['replaytoname']);
