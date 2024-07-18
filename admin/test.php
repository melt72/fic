<?php
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);
ini_set('max_execution_time', 0);
ini_set("memory_limit", "512M");
set_time_limit(0);
require_once __DIR__ . '/assets/vendor/autoload.php';
$mpdf = new \Mpdf\Mpdf(['tempDir' => __DIR__ . '/tmp']);
$mpdf = new \Mpdf\Mpdf(['debug' => true]);

$mpdf->WriteHTML("Hello World");
$mpdf->Output();
