<?php
session_start();

require_once('./vendor/autoload.php');
require_once('./menus/Registration.php');

require_once('./database/dbConnection.php');

$dotenv = Dotenv\Dotenv::createImmutable(__DIR__);
$dotenv->safeLoad();

$message = '';

try {
    $db_host = $_ENV['DB_HOST'];
    $db_name = $_ENV['DB_NAME'];
    $db_username = $_ENV['DB_USERNAME'];
    $db_password = $_ENV['DB_PASSWORD'];

    $message = dbConnection::myConnection($db_host, $db_username, $db_password, $db_name);

    // if (strtolower($_SERVER['REQUEST_METHOD']) == 'post') {

    //     $sessionId = $_POST['sessionId'];
    //     $serviceCode = $_POST['serviceCode'];
    //     $msisdn = $_POST['msisdn'];
    //     $text = $_POST['text'];

    //     if ($_SESSION['session_data']) {
    //         //state management variables
    //         $step = $_SESSION['session_data']['step'];
    //         $sub_menu = $_SESSION['session_data']['sub_menu'];
    //     } else {
    //         //state management variables
    //         $step = 0;
    //         $sub_menu = '';
    //     }

    //     $_SESSION['session_data'] = $session_data;

    //     $message = '';

    //     if ($step == 0) {
    //         if ($text == '') {
    //             $message = Registration::Page_1();
    //         } elseif ($text == 1) {
    //             $step = 1;
    //             $sub_menu = 'account_registration';
    //             $message = Registration::Page_2($text);
    //         } elseif ($text == 2) {
    //             $step = 1;
    //             $sub_menu = 'car_registration';
    //             $message = Registration::Page_2($text);
    //         } elseif ($text == 3) {
    //             $step = 1;
    //             $sub_menu = 'apartment_registration';
    //             $message = Registration::Page_2($text);
    //         }
    //         $_SESSION['session_data']['step'] = $step;
    //     } elseif ($step == 1 && $text == 1) {
    //         # code...
    //     }
    // } else {
    //     $message = 'Request Rejected';
    // }
} catch (\Throwable $th) {
    $message = "ERROR MESSAGE: " . $th->getMessage() . "\nLINE NUMBER: " . $th->getLine();
}

echo $message;
