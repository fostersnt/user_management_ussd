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
        $session_id = $sessionData['session_id'] ?? null;
        $msisdn = $sessionData['msisdn'] ?? null;
        $step = $sessionData['step'] ?? null;
        $submenu = $sessionData['submenu'] ?? null;

        $result = self::search_User_Session_Two($con, $session_id, $msisdn, $submenu);
        
        if (! $result['status']) {
            $stmt = $con->prepare("INSERT INTO user_sessions (session_id, msisdn, step, submenu) VALUES (?, ?, ?, ?)");

            $stmt->bind_param('ssis', $session_id, $msisdn, $step, $submenu);

            $stmt->execute();
        
            $affected_rows = $stmt->affected_rows;

            $result = ($stmt != null && $affected_rows != null) ? ['status' => true, 'affected_rows' => $affected_rows] : ['status' => false, 'affected_rows' => null];
        }

        return $result;
    }

    public static function search_User_Session_One($con, $sessionId, $msisdn)
    {
        $stmt = $con->prepare("SELECT * FROM user_sessions WHERE msisdn = ? AND session_id = ? ORDER BY created_at DESC LIMIT 1");
        $stmt->bind_param('ss', $msisdn, $sessionId);
        $stmt->execute();

        $result = $stmt->get_result();

        return ($stmt != null) ? ['status' => true, 'data' => $result] : ['status' => false, 'data' => null];
    }

    public static function search_User_Session_Two($con, $sessionId, $msisdn, $submenu)
    {
        $stmt = $con->prepare("SELECT * FROM user_sessions WHERE msisdn = ? AND session_id = ? AND submenu = ? ORDER BY created_at DESC LIMIT 1");
        $stmt->bind_param('sss', $msisdn, $sessionId, $submenu);
        $stmt->execute();

        $result = $stmt->get_result();
        // echo $result->fetch_assoc();
        return ($stmt != null && $result->fetch_assoc() > 0) ? ['status' => true, 'data' => $result] : ['status' => false, 'data' => null];
    }
}
