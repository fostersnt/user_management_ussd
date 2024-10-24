<?php
session_start();

require_once('./vendor/autoload.php');
require_once('./menus/Registration.php');

require_once('./database/dbConnection.php');
require_once('./utilities/DbInteractions.php');

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

        $result = DbInteractions::search_User_Session($con, $sessionId, $msisdn);
        $userSessionData = $result['data'];

        $row = $userSessionData->fetch_assoc();
        // echo json_encode($row);
       
        //state management variables
        $step = $row['step'] ?? 0;
        $sub_menu = $row['sub_menu'] ?? '';

        // $message = $row != null ? "User session is available" : "No user session available";

        if ($step == 0) {
            if ($text == '') {
                $message = Registration::Page_1();
            } elseif ($text == 1) {
                // $step = 1;
                // $sub_menu = 'account_registration';
                $sessionData = [
                    'session_id' => $sessionId,
                    'msisdn' => $msisdn,
                    'step' => 1,
                    'submenu' => 'account_registration',
                ];
                $outcome = DbInteractions::createUserSession($con, $sessionData);
                if ($outcome['status'] && $outcome['insert_id'] != null) {
                    $message = Registration::Page_2($text);
                } else {
                    $message = "Unable to store session data. Please try again\n" . Registration::Page_2($text);
                }
                
            } elseif ($text == 2) {
                $step = 1;
                $sub_menu = 'car_registration';
                $message = Registration::Page_2($text);
            } elseif ($text == 3) {
                $step = 1;
                $sub_menu = 'apartment_registration';
                $message = Registration::Page_2($text);
            }
            $_SESSION['session_data']['step'] = $step;
        } elseif ($step == 1 && $text == 1) {
            # code...
        }
    } else {
        $message = 'Request Rejected';
    }
} catch (\Throwable $th) {
    // $message = "Unable to process request";
    $message = "ERROR MESSAGE: " . $th->getMessage() . "\nLINE NUMBER: " . $th->getLine();
}

echo $message;
