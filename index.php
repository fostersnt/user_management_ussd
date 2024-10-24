<?php
session_start();

require_once('./vendor/autoload.php');
require_once('./menus/Registration.php');

require_once('./database/dbConnection.php');

$dotenv = Dotenv\Dotenv::createImmutable(__DIR__);
$dotenv->safeLoad();

$message = '';

try {
    if (strtolower($_SERVER['REQUEST_METHOD']) == 'post') {
        $sessionId = $_POST['sessionId'] ?? '';
        $serviceCode = $_POST['serviceCode'] ?? '';
        $msisdn = $_POST['msisdn'] ?? '';
        $text = $_POST['text'] ?? '';
        $region = $_POST['region'] ?? '';
        $name = $_POST['name'] ?? '';

        $db_host = $_ENV['DB_HOST'];
        $db_name = $_ENV['DB_NAME'];
        $db_username = $_ENV['DB_USERNAME'];
        $db_password = $_ENV['DB_PASSWORD'];

        $con = dbConnection::myConnection($db_host, $db_username, $db_password, $db_name);

        $stmt = $con->prepare("SELECT * FROM user_sessions WHERE msisdn = ? AND session_id = ? ORDER BY created_at DESC LIMIT 1");
        $new_msisdn = $msisdn . '3';
        $stmt->bind_param('ss', $msisdn, $sessionId);
        $stmt->execute();

        // $processedObj = $stmt->get_result();
        // $row = $processedObj->fetch_assoc();
        $row = $stmt->get_result()->fetch_assoc();
        echo json_encode($row);
       
        //state management variables
        $step = $row['step'] ?? 0;
        $sub_menu = $row['sub_menu'] ?? '';

        // $message = $row != null ? "User session is available" : "No user session available";

        // if ($step == 0) {
        //     if ($text == '') {
        //         $message = Registration::Page_1();
        //     } elseif ($text == 1) {
        //         $step = 1;
        //         $sub_menu = 'account_registration';
        //         $message = Registration::Page_2($text);
        //     } elseif ($text == 2) {
        //         $step = 1;
        //         $sub_menu = 'car_registration';
        //         $message = Registration::Page_2($text);
        //     } elseif ($text == 3) {
        //         $step = 1;
        //         $sub_menu = 'apartment_registration';
        //         $message = Registration::Page_2($text);
        //     }
        //     $_SESSION['session_data']['step'] = $step;
        // } elseif ($step == 1 && $text == 1) {
        //     # code...
        // }
    } else {
        $message = 'Request Rejected';
    }
} catch (\Throwable $th) {
    // $message = "Unable to process request";
    $message = "ERROR MESSAGE: " . $th->getMessage() . "\nLINE NUMBER: " . $th->getLine();
}

echo $message;
