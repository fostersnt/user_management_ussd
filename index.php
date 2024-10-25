<?php
session_start();

require_once('./vendor/autoload.php');
require_once('./menus/Registration.php');

require_once('./database/dbConnection.php');
require_once('./utilities/DbInteractions.php');
require_once('./menus/AccountRegistration.php');
require_once('./utilities/General.php');

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

        General::logMessage('info', 'Hello world');
        echo 'GHANA IS GHANA';
        return null;

        $db_host = $_ENV['DB_HOST'];
        $db_name = $_ENV['DB_NAME'];
        $db_username = $_ENV['DB_USERNAME'];
        $db_password = $_ENV['DB_PASSWORD'];

        $con = dbConnection::myConnection($db_host, $db_username, $db_password, $db_name);

        $result = DbInteractions::search_User_Session($con, $sessionId, $msisdn);

        $userSessionData = $result['data'];

        //state management variables
        $step = $userSessionData['step'] ?? 0;
        $current_menu = strtolower($userSessionData['current_menu'] ?? '');
        $input_description = strtolower($userSessionData['input_description'] ?? '');
        // echo "STEP: $step\nSUB-MENU: $current_menu";

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
                    'current_menu' => $submenuOptions[$text],
                    'input_description' => NULL
                ];

                $outcome = DbInteractions::createUserSession($con, $sessionData);

                if ($outcome['status'] && $outcome['data'] != null) {
                    $message = Registration::Page_2($text);
                } else {
                    $message = "Unable to store session data. Please try again\n" . Registration::Page_2($text);
                }
            }
        } elseif ($step == 1) {
            $message = AccountRegistration::registerAccount($con, $text, $userSessionData);
        } elseif ($step == 2 && $current_menu == 'car_registration') {
            echo json_encode($userSessionData);
        } elseif ($step == 2 && $current_menu == 'car_registration') {
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
