<?php
require '../assets/vendor/autoload.php';

// import the Intervention Image Manager Class
use Intervention\Image\ImageManager;

if (!empty($_SERVER['HTTP_X_REQUESTED_WITH']) &&  strtolower($_SERVER['HTTP_X_REQUESTED_WITH']) == 'xmlhttprequest') {
    $successo = true;
    include('../../include/init.php');
    include('functions.php');
    include('../../include/configpdo.php');

    // create an image manager instance with favored driver
    $manager = new ImageManager(
        new Intervention\Image\Drivers\Gd\Driver()
    );


    $nome = ucfirst($_POST['nome']);
    $cognome = ucfirst($_POST['cognome']);
    $email = $_POST['mail'];
    $ruolo = $_POST['ruolo'];
    $idutente = $_POST['idutente'];

    $controllo = PasswordCasuale(8);
    try {
        if ($idutente != '') : // MODIFICA UTENTE
            $query = "UPDATE `user` SET `nome`=:nome,`cognome`=:cognome,`username`=:usernameutente,`controllo`=:controllo,`ruolo`=:ruoloutente WHERE `id_user`='$idutente'";
            $messaggio = 'Utente modificato correttamente';
        else : // CREAZIONE UTENTE
            $query = "INSERT INTO `user`(`nome`,`cognome`,`username`,`controllo`, `ruolo`) VALUES (:nome, :cognome, :usernameutente, :controllo, :ruoloutente)";
            $messaggio = 'Utente creato correttamente';
        endif;

        $stmt = $db->prepare($query);
        $stmt->bindParam('nome', $nome, PDO::PARAM_STR);
        $stmt->bindParam('cognome', $cognome, PDO::PARAM_STR);
        $stmt->bindParam('controllo', $controllo, PDO::PARAM_STR);
        $stmt->bindParam('usernameutente', $email, PDO::PARAM_STR);
        $stmt->bindParam('ruoloutente', $ruolo, PDO::PARAM_STR);
        $stmt->execute();
        if ($idutente == '') :
            $ultimo =  $db->lastInsertId();
        else :
            $ultimo = $idutente;
        endif;
    } catch (PDOException $e) {
        echo "Error : " . $e->getMessage();
    }



    $dst = '../img_profilo';
    //$path_to_image_directory = '../images/original/';

    if (isset($_FILES['file'])) {
        $src = $_FILES["file"]["tmp_name"];
        $fileExtension = 'jpg';

        $dstx = 250;
        $dsty = 250;

        $filename = PasswordCasuale(8)  . '.' . $fileExtension;
        $image = $manager->read($src)->cover($dstx, $dsty);
        $image->save($dst . '/' . $filename);

        // AGGIUNGO L'IMMAGINE AL PROFILO
        try {
            $query = "UPDATE `user` SET `imm_profilo`=:immagine WHERE `id_user`='$ultimo'";
            $stmt = $db->prepare($query);

            $stmt->bindParam('immagine', $filename, PDO::PARAM_STR);
            $stmt->execute();
        } catch (PDOException $e) {
            echo "Error : " . $e->getMessage();
        }
    }
    /// FINE UPLOAD IMMAGINE

    /// INIZIO INVIO MAIL DI ATTIVAZIONE solo se è un nuovo utente

    if (($idutente == '') && ($invio_mail)) :
        require_once('mailer.php');

        $lingua = 'it';
        $link = $hosting . '/attiva.php?cod=' . $controllo;
        // invio la mail al cliente
        $file = file_get_contents("../language/$lingua-mail.json");
        $contenutomail = json_decode($file);
        $mail->addAddress($email, $nome);
        $mail->Subject = $contenutomail->{"mail"}->{"attiva-titolo"};
        $mail->isHTML(true);
        $message = TestoMailJson($contenutomail->{"mail"}->{"attiva-testo"});
        $message = str_replace('%utente%', $nome, $message);
        $message = str_replace('%link%', $link, $message);

        $mail->msgHTML($message, __DIR__);

        if (!$mail->send()) {
            $successo = false;
            $messaggio = 'Mailer Error: ' . $mail->ErrorInfo;
        }
    endif;
    /// FINE INVIO MAIL
    // creo un json
    $json = array(
        'status' => $successo,
        'messaggio' => $messaggio,
    );
    // lo trasformo in json
    $json = json_encode($json);
    // lo mando indietro
    echo $json;
} else {
    exit();
}
