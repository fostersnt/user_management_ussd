<?php

require ('./vendor/autoload.php');
require ('./menus/Registration.php');

$dotenv = Dotenv\Dotenv::createImmutable(__DIR__);
$dotenv->safeLoad();

if (strtolower($_SERVER['REQUEST_METHOD']) == 'post') {

    $serviceCode = $_POST['serviceCode'];
    $sessionId = $_POST['sessionId'];
    $msisdn = $_POST['msisdn'];
    $text = $_POST['text'];

    echo Registration::Page_1();
} else {
    echo 'Request Rejected';
}

