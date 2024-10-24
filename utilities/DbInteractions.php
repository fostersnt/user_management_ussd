<?php

class DbInteractions
{
    public static function createUser($con, array $userData)
    {
        $stmt = $con->prepare("INSERT INTO users (name, msisdn, region) VALUES (?, ?, ?)");

        $name = $userData['name'] ?? null;
        $msisdn = $userData['msisdn'] ?? null;
        $region = $userData['region'] ?? null;



        $stmt->bind_param('sss', $name, $msisdn, $region);

        $stmt->execute();

        $result = $stmt->affected_rows;

        return ($stmt != null) ? ['status' => true, 'affected_rows' => $result] : ['status' => false, 'affected_rows' => null];
    }

    public static function search_User_By_Msisdn($con, $msisdn)
    {
        $stmt = $con->prepare("SELECT * FROM users WHERE msisdn = ? ORDER BY id ASC LIMIT 1");

        $stmt->bind_param('s', $msisdn);

        $stmt->execute();

        $result = $stmt->get_result();

        return ($stmt != null) ? ['status' => true, 'data' => $result] : ['status' => false, 'data' => null];
    }

    public static function createUserSession($con, array $sessionData)
    {
        $stmt = $con->prepare("INSERT INTO user_sessions (session_id, msisdn, step, submenu) VALUES (?, ?, ?, ?)");

        $session_id = $sessionData['session_id'] ?? null;
        $msisdn = $sessionData['msisdn'] ?? null;
        $step = $sessionData['step'] ?? null;
        $submenu = $sessionData['submenu'] ?? null;

        $stmt->bind_param('ssis', $session_id, $msisdn, $step, $submenu);

        $stmt->execute();
        
        $result = $stmt->affected_rows;

        return ($stmt != null) ? ['status' => true, 'affected_rows' => $result] : ['status' => false, 'affected_rows' => null];
    }
    
    public static function search_User_Session($con, $sessionId, $msisdn)
    {
        $stmt = $con->prepare("SELECT * FROM user_sessions WHERE msisdn = ? AND session_id = ? ORDER BY created_at DESC LIMIT 1");
        $stmt->bind_param('ss', $msisdn, $sessionId);
        $stmt->execute();

        $result = $stmt->get_result();

        return ($stmt != null) ? ['status' => true, 'data' => $result] : ['status' => false, 'data' => null];
    }
    
}
