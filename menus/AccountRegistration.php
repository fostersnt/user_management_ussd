<?php

class AccountRegistration
{
    public static function registerAccount($con, $text, $sessionData)
    {
        // echo json_encode($sessionData);
        // echo "\n\nTEXT: $text\n";

        $sessionId = $sessionData['session_id'] ?? null;
        $msisdn = $sessionData['msisdn'] ?? null;
        $current_menu = $sessionData['current_menu'] ?? null;
        $input_description = $sessionData['input_description'] ?? null;
        
        $response = '';

        $account_registration_options = [
            1 => 'personal_account',
            2 => 'family_account'
        ];

        $descriptions = [
            1 => 'personal_name',
            2 => 'family_name',
            3 => 'personal_residence',
            4 => 'family_residence'
        ];

        if ($current_menu == 'account_registration') {
            // echo "\n\nTEXT DATA: " . $account_registration_options[$text];
            if (in_array($text, [1, 2])) {

                $sessionData = [
                    'session_id' => $sessionId,
                    'msisdn' => $msisdn,
                    'step' => 1,
                    'current_menu' => $account_registration_options[$text],
                    'input_description' => 'name'
                ];

                DbInteractions::delete_User_Session($con, $sessionId, $msisdn);

                $outcome = DbInteractions::createUserSession($con, $sessionData);

                // echo json_encode($outcome);

                $response = ($text == 1 && $outcome['status']) ? 'Enter your name' : (($text == 2 && $outcome['status']) ? 'Enter family name' : 'Invalid input');
            } else {
                $response = 'Unable to save session for account registration';
            }
        }elseif ($current_menu == 'personal_account') {
            if ($input_description == 'name') {
                // echo "\n\nYour name is: " . $text;
                $_SESSION['name'] = $text;

                $sessionData = [
                    'session_id' => $sessionId,
                    'msisdn' => $msisdn,
                    'step' => 1,
                    'current_menu' => $current_menu,
                    'input_description' => 'age'
                ];

                DbInteractions::delete_User_Session($con, $sessionId, $msisdn);

                $outcome = DbInteractions::createUserSession($con, $sessionData);

                $response = 'Enter your age';
            }
            elseif ($input_description == 'age') {
                // echo "\n\nYour name is: " . $text;
                $_SESSION['age'] = $text;

                $sessionData = [
                    'session_id' => $sessionId,
                    'msisdn' => $msisdn,
                    'step' => 1,
                    'current_menu' => $current_menu,
                    'input_description' => 'completed'
                ];

                DbInteractions::delete_User_Session($con, $sessionId, $msisdn);

                $outcome = DbInteractions::createUserSession($con, $sessionData);

                if ($outcome['status']) {
                    $response = "\nPlease confirm your details below:\n\nName: " . $_SESSION['name'] . "\nAGE: " . $_SESSION['age'] . "\n\n1. Confirm";
                } else {
                    $response = 'Unable to completed request';
                }
                
            }
            elseif ($input_description == 'completed') {
                if ($text == 1) {
                    $response = 'You have successfully completed account registration';
                } else {
                    $response = 'Please enter 1 to complete account registration';
                }
                
            }
            else{
                $response = 'Unknown selection';
            }
        }

        return $response;
    }
}
