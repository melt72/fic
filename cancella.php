<?php
session_set_cookie_params(86400);
session_start();
if (isset($_SESSION['token'])) echo $_SESSION['token'];
//distruzione sessione 
unset($_SESSION['token']);
session_destroy();
session_start();

// Imposta una variabile di sessione
$_SESSION['test_session'] = 'Hello, this is a test session!';

// Stampa l'ID della sessione e il valore della variabile di sessione
echo "Session ID: " . session_id() . "<br>";
echo "Session Variable: " . $_SESSION['test_session'] . "<br>";

// Verifica se la variabile di sessione esiste
if (isset($_SESSION['test_session'])) {
    echo "Session is working!";
} else {
    echo "Session is not working!";
}
