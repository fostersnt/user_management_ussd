<?php

class DbInteractions
{
    public static function search_User_By_Msisdn($con, $msisdn)
    {
        $stmt = $con->prepare("SELECT * FROM users WHERE msisdn = ? ORDER BY id ASC LIMIT 1");
        if ($stmt === false) {
            return ['status' => false, 'error' => 'Statement preparation failed: ' . $con->error];
        }
        $stmt->bind_param('s', $msisdn);
        if (!$stmt->execute()) {
            return ['status' => false, 'error' => 'Execution failed: ' . $stmt->error];
        }
        $result = $stmt->get_result();
        $data = $result ? $result->fetch_assoc() : null;
        $stmt->close();
        return $data ? ['status' => true, 'data' => $data] : ['status' => false, 'data' => null];
    }


    public static function createUser($con, array $userData)
    {
        $result = self::search_User_By_Msisdn($con, $userData['msisdn']);

        $name = $userData['name'] ?? null;
        $msisdn = $userData['msisdn'] ?? null;
        $region = $userData['region'] ?? null;

        if ($result['data'] == null) {
            $stmt = $con->prepare("INSERT INTO users (name, msisdn, region) VALUES (?, ?, ?)");

            $stmt->bind_param('sss', $name, $msisdn, $region);

            $stmt->execute();

            $result = $stmt->affected_rows;
        } else {
            $stmt = $con->prepare("UPDATE users SET name = ?, msisdn = ?, region = ? WHERE msisdn = '$msisdn'");

            $stmt->bind_param('sss', $name, $msisdn, $region);

            $stmt->execute();

            $result = $stmt->affected_rows;
        }

        return ($stmt != null) ? ['status' => true, 'data' => $result] : ['status' => false, 'data' => null];
    }

    public static function search_User_Session($con, $sessionId, $msisdn)
    {
        // echo "\nCURRENT INFO\nmsisdn: $msisdn\nSessionId: $sessionId\n\n";

        $stmt = $con->prepare("SELECT * FROM user_sessions WHERE msisdn = ? AND session_id = ? ORDER BY created_at DESC LIMIT 1");
        $stmt->bind_param('ss', $msisdn, $sessionId);
        $stmt->execute();

        $result = $stmt->get_result();
        $data = $result ? $result->fetch_assoc() : null;

        return ($data != null) ? ['status' => true, 'data' => $data] : ['status' => false, 'data' => null];
    }

    public static function createUserSession($con, array $sessionData)
    {
        $session_id = $sessionData['session_id'] ?? null;
        $msisdn = $sessionData['msisdn'] ?? null;
        $step = $sessionData['step'] ?? null;
        $current_menu = $sessionData['current_menu'] ?? null;
        $input_description = $sessionData['input_description'] ?? null;

        $result = self::search_User_Session($con, $session_id, $msisdn);

        // echo json_encode($result);

        if (! $result['status']) {
            $stmt = $con->prepare("INSERT INTO user_sessions (session_id, msisdn, step, current_menu, input_description) VALUES (?, ?, ?, ?, ?)");

            $stmt->bind_param('ssiss', $session_id, $msisdn, $step, $current_menu, $input_description);

            $stmt->execute();

            $affected_rows = $stmt->affected_rows;

            $result = ($stmt != null && $affected_rows != null) ? ['status' => true, 'data' => $affected_rows] : ['status' => false, 'data' => null];
        }

        return $result;
    }

    public static function delete_User_Session($con, $sessionId, $msisdn)
    {
        $stmt = $con->prepare("DELETE FROM user_sessions WHERE msisdn = ? AND session_id = ?");
        $stmt->bind_param('ss', $msisdn, $sessionId);
        $stmt->execute();

        $data = $stmt->affected_rows;

        return ($data != null) ? ['status' => true, 'data' => $data] : ['status' => false, 'data' => null];
    }
}
