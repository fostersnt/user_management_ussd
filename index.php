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

        $result = DbInteractions::search_User_Session_One($con, $sessionId, $msisdn);
        $userSessionData = $result['data'];

        $row = $userSessionData->fetch_assoc();

        //state management variables
        $step = $row['step'] ?? 0;
        // $sub_menu = $row['submenu'] ?? '';
        // echo "STEP: $step\nSUB-MENU: $sub_menu";

        if ($step == 0) {
            if ($text == '') {
                $message = Registration::Page_1();
            } elseif (in_array($text, [1, 2, 3])) {
                $submenuOptions = [
                    1 => 'account_registration',
                    2 => 'car_registration',
                    3 => 'apartment_registration',
                ];
        
                $sessionData = [
                    'session_id' => $sessionId,
                    'msisdn' => $msisdn,
                    'step' => 1,
                    'submenu' => $submenuOptions[$text],
                ];
        
                $outcome = DbInteractions::createUserSession($con, $sessionData);
        
                if ($outcome['status'] && $outcome['affected_rows'] != null) {
                    $message = Registration::Page_2($text);
                } else {
                    $message = "Unable to store session data. Please try again\n" . Registration::Page_2($text);
                }
            }
        } elseif ($step == 3 && $sub_menu == 'account_registration') {
            echo "YOU ARE IN STEP 1";
        }
        elseif ($step == 2 && $sub_menu == 'car_registration') {
            echo "YOU ARE IN STEP 1";
        }
        elseif ($step == 2 && $sub_menu == 'car_registration') {
            echo "YOU ARE IN STEP 1";
        }
    } else {
        $message = 'Request Rejected';
    }
} catch (\Throwable $th) {
    // $message = "Unable to process request";
    $message = "ERROR MESSAGE: " . $th->getMessage() . "\nLINE NUMBER: " . $th->getLine();
}

echo $message;
