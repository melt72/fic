<?php
if (!empty($_SERVER['HTTP_X_REQUESTED_WITH']) &&  strtolower($_SERVER['HTTP_X_REQUESTED_WITH']) == 'xmlhttprequest') :
    include 'mailer.php';
    // invio la mail al cliente

    $mail->addAddress('meltit72@gmail.com', 'Test');
    $mail->Subject = 'Test mail';
    $mail->isHTML(true);

    $message = 'test';
    $mail->msgHTML($message, __DIR__);

    if (!$mail->send()) {
        echo 'Mailer Error: ' . $mail->ErrorInfo;
    } else {
        echo 'ok';
    }

else :
    exit();
endif;
