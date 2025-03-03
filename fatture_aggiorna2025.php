<?php
if (isset($_GET['a'])) {
    $anno = $_GET['a'];
} else {
    $anno = date('Y');
}
include 'functions.php';

$status = check_status('2025');
echo $status;
