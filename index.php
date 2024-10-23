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
        // $result = $con->query("SELECT * FROM user_sessions WHERE session_id = '$sessionId' AND msisdn = '$msisdn' ORDER BY created_at DESC LIMIT 1");
        $result = $con->query("SELECT * FROM users WHERE msisdn = $msisdn ORDER BY created_at DESC LIMIT 1");
        echo json_encode($result->fetch_assoc());
        return null;
        if ($result->fetch_assoc()) {
            //state management variables
            $step = $_SESSION["$sessionId"."_session_data"]['step'];
            $sub_menu = $_SESSION["$sessionId"."_session_data"]['sub_menu'];
        } else {
            //state management variables
            $step = 0;
            $sub_menu = '';
        }

        //Connecting to database

        $stmt = $con->prepare("INSERT INTO users (name, msisdn, region) VALUES (?, ?, ?)");

        $stmt->bind_param('sss', $name, $msisdn, $region);
        $stmt->execute();

        $message = $stmt->affected_rows;

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
