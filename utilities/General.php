<?php
require 'vendor/autoload.php';

class General
{
    public static function sendUssdResponse($sessionId, $message)
    {
        $url = $_ENV['MTN_USSD_OUTBOUND_ENPOINT'];

        $clientId = 'YOUR_CLIENT_ID';
        $clientSecret = 'YOUR_CLIENT_SECRET';

        $postData = json_encode([
            'sessionId' => $sessionId,
            'message' => $message
        ]);

        $headers = [
            'Content-Type: application/json',
            'Authorization: Basic ' . base64_encode("$clientId:$clientSecret")
        ];

        $ch = curl_init($url);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($ch, CURLOPT_POSTFIELDS, $postData);
        curl_setopt($ch, CURLOPT_HTTPHEADER, $headers);

        $response = curl_exec($ch);

        curl_close($ch);

        return $response;
    }

    public static function logMessage($errorType, $message)
    {
        //The global variables below should be declared in another file and must have been assigned
        global $msisdn;
        global $sessionId;

        $full_name = "[MSISDN]: $msisdn, [SESSION_ID]: $sessionId, $message";
        
        $logger = new Katzgrau\KLogger\Logger(__DIR__ . '/logs', Psr\Log\LogLevel::INFO, array(
            'extension' => 'log',
            'dateFormat' => 'Y-m-d G:i:s'
        ));

        switch ($errorType) {
            case 'info':
                $logger->info($full_name);
                break;
            case 'error':
                $logger->error($full_name);
                break;
            case 'debug':
                $logger->debug($full_name);
                break;
            default:
                $logger->info($full_name);
                break;
        }
    }
}
