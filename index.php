<?php
// Set header content type to text/plain
header('Content-type: text/plain');

// Database connection
$servername = "localhost"; // Your DB server address
$username = "foster";        // Your DB username
$password = "";            // Your DB password
$dbname = "ussd_app";      // Your DB name

// Create connection
$conn = new mysqli($servername, $username, $password);

// Check connection
if ($conn->connect_error) {
    die("Database connection error: " . $conn->connect_error);
}

// Create the database if it doesn't exist
$sql = "CREATE DATABASE IF NOT EXISTS $dbname";
if ($conn->query($sql) === TRUE) {
    // Switch to the newly created database
    $conn->select_db($dbname);
} else {
    die("Error creating database: " . $conn->error);
}

// Create tables if they don't exist
// Table for users
$sql = "
CREATE TABLE IF NOT EXISTS users (
    id INT(11) UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    name VARCHAR(100) NOT NULL,
    phone VARCHAR(20) NOT NULL,
    additional_info TEXT,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
)";
if ($conn->query($sql) !== TRUE) {
    die("Error creating table 'users': " . $conn->error);
}

// Table for user sessions
$sql = "
CREATE TABLE IF NOT EXISTS user_sessions (
    session_id VARCHAR(255) PRIMARY KEY,
    phone_number VARCHAR(20),
    step INT DEFAULT 0,
    submenu VARCHAR(50),
    name VARCHAR(100),
    additional_info TEXT,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
)";
if ($conn->query($sql) !== TRUE) {
    die("Error creating table 'user_sessions': " . $conn->error);
}

// Collect incoming parameters from the USSD gateway
$sessionId = $_POST["sessionId"];
$serviceCode = $_POST["serviceCode"];
$phoneNumber = $_POST["phoneNumber"];
$text = $_POST["text"];

// Database connection
$conn = new mysqli("host", "username", "password", "database");
if ($conn->connect_error) {
    die("Database connection error: " . $conn->connect_error);
}

// Check if session exists
$result = $conn->query("SELECT step, submenu, name FROM user_sessions WHERE session_id = '$sessionId'");
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
        $conn->query("INSERT INTO user_sessions (session_id, phone_number, step, submenu) VALUES ('$sessionId', '$phoneNumber', 1, 'register')");
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
        $additionalInfo = $text;
        // Save the user's details in a different table or process them here
        $conn->query("INSERT INTO users (name, phone, additional_info) VALUES ('$name', '$phoneNumber', '$additionalInfo')");
        // End the session and delete it
        $conn->query("DELETE FROM user_sessions WHERE session_id='$sessionId'");
        $response = "END Thank you, $name! Your registration is complete.\n";
    }

} else {
    $response = "END Invalid input or session expired.\n";
}

// Send response to the USSD gateway
echo $response;

// Close database connection
$conn->close();
?>
