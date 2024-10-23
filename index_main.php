<?php
require ('./vendor/autoload.php');
require ('./utilities/General.php');

header('Content-type: text/plain');


$dotenv = Dotenv\Dotenv::createImmutable(__DIR__);
$dotenv->safeLoad();

$db_host = $_ENV['DB_HOST'];
$db_username = $_ENV['DB_USERNAME'];
$db_password = $_ENV['DB_PASSWORD'];
$db_name = $_ENV['DB_NAME'];

// echo "HOST: $db_host\nUSERNAME: $db_username\nPASSWORD: $db_password\nDB NAME: $db_name";

$conn = new mysqli($db_host, $db_username, $db_password);

if ($conn->connect_error) {
    die("Database connection error: " . $conn->connect_error);
}

$sql = "CREATE DATABASE IF NOT EXISTS $db_name";
if ($conn->query($sql) === TRUE) {
    // Switch to the newly created database
    $conn->select_db($db_name);
} else {
    die("Error creating database: " . $conn->error);
}

$sql = "
CREATE TABLE IF NOT EXISTS users (
    id INT(11) UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    name VARCHAR(100) NOT NULL,
    msisdn VARCHAR(20) NOT NULL,
    region VARCHAR(30) NOT NULL,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
)";
if ($conn->query($sql) !== TRUE) {
    die("Error creating table 'users': " . $conn->error);
}

// Table for user sessions
$sql = "
CREATE TABLE IF NOT EXISTS user_sessions (
    session_id VARCHAR(255) PRIMARY KEY,
    msisdn VARCHAR(20),
    step INT DEFAULT 0,
    submenu VARCHAR(50),
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
)";
if ($conn->query($sql) !== TRUE) {
    die("Error creating table 'user_sessions': " . $conn->error);
}

// Collect incoming parameters from the USSD gateway
$sessionId = $_POST["sessionId"] ?? '';
$serviceCode = $_POST["serviceCode"] ?? '';
$phoneNumber = $_POST["phoneNumber"] ?? '';
$text = $_POST["text"] ?? '';

// Database connection
$conn = new mysqli($db_host, $db_username, $db_password, $db_name);
if ($conn->connect_error) {
    die("Database connection error: " . $conn->connect_error);
}

// Check if session exists
$result = $conn->query("SELECT step, submenu FROM user_sessions WHERE session_id = '$sessionId'");
if ($result->num_rows > 0) {
    $sessionData = $result->fetch_assoc();
    $step = $sessionData['step'];
    $submenu = $sessionData['submenu'];
    $name = $sessionData['name'];
} else {
    // New session
    $step = 0;
    $submenu = "";
}

// Split the input text into an array to track user navigation
$textArray = explode("*", $text);
$userResponse = trim(end($textArray));

// Define the USSD menu logic
if ($step == 0) {
    // Main Menu
    $response  = "CON Main Menu\n";
    $response .= "1. Register\n";
    $response .= "2. Account details\n";

    if ($text == "1") {
        // User chooses to register
        $response = "CON Register Menu\n1. Single registration\n2. Double registration\n";
        $conn->query("INSERT INTO user_sessions (session_id, msisdn, step, submenu) VALUES ('$sessionId', '$phoneNumber', 1, 'register')");
    } elseif ($text == "2") {
        $response = "END Account details option selected (Not implemented yet).\n";
    }

} elseif ($submenu == "register" && $step == 1) {
    // Sub-menu for Register
    if ($text == "1") {
        // Single registration
        $response = "CON Enter your name:\n";
        $conn->query("UPDATE user_sessions SET step=2, submenu='single_registration' WHERE session_id='$sessionId'");
    } elseif ($text == "2") {
        // Double registration
        $response = "CON Double registration selected (Not implemented yet).\n";
        $conn->query("UPDATE user_sessions SET step=2, submenu='double_registration' WHERE session_id='$sessionId'");
    }

} elseif ($submenu == "single_registration" && $step == 2) {
    // Sub-sub-menu for Single Registration - Enter Name
    if ($text) {
        $name = $text;
        $response = "CON Enter additional details:\n";
        $conn->query("UPDATE user_sessions SET name='$name', step=3 WHERE session_id='$sessionId'");
    }

} elseif ($submenu == "single_registration" && $step == 3) {
    // Sub-sub-menu for Single Registration - Additional Details
    if ($text) {
        $region = $text;
        // Save the user's details in a different table or process them here
        $conn->query("INSERT INTO users (name, msisdn, region) VALUES ('$name', '$phoneNumber', '$region')");
        // End the session and delete it
        $conn->query("DELETE FROM user_sessions WHERE session_id='$sessionId'");
        $response = "END Thank you, $name! Your registration is complete.\n";
    }

} else {
    $response = "END Invalid input or session expired.\n";
}

// Send response to the USSD gateway
// $final_response = General::sendUssdResponse($sessionId, $response);
    echo $response;
// echo json_encode($final_response);
// echo $final_response;

$conn->close();
?>
