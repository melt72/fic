<?php
if (!empty($_SERVER['HTTP_X_REQUESTED_WITH']) &&  strtolower($_SERVER['HTTP_X_REQUESTED_WITH']) == 'xmlhttprequest') :
    include('../../include/init.php');
    include('../../include/configpdo.php');
    $password = md5($_POST['loginpassword']);
    try {
        $query = "SELECT username,  `password`, act FROM `user` WHERE `username`=:usernameuser ";
        $stmt = $db->prepare($query);
        $stmt->bindParam('usernameuser', $_POST['loginmail'], PDO::PARAM_STR);

        $stmt->execute();
        $row   = $stmt->fetch(PDO::FETCH_ASSOC);
        if ($row) :
            switch ($row['act']) {
                case '0':
                    echo 'account non attivato';
                    exit();
                    break;
                case '1':
                    if ($password == $row['password']) {
                        setcookie($sessione, $row['username'], time() + 3600 * 24 * 30, "/");
                        echo 'ok';
                        exit();
                    } else {
                        echo 'errore username o password';
                        exit();
                    }

                    break;
                case '2':
                    echo 'account bloccato';
                    break;
            }

        else :
            echo 'errore username o password';
            exit();
        endif;
    } catch (PDOException $e) {
        echo "Error : " . $e->getMessage();
    }
else :
    exit();
endif;
