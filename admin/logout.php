<?php
include '../include/init.php';
session_start();
// remove all session variables
session_unset();

// destroy the session 
session_destroy();

unset($_COOKIE[$sessione]);
setcookie($sessione, null, -1, '/');
header('Location: index.php');
