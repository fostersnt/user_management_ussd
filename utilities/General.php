<?php
// namespace \utilities\
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
}
