<?php

require_once('./vendor/autoload.php');

use Dotenv\Dotenv;

class dbConnection
{
    public static function myConnection($db_host, $db_username, $db_password, $db_name)
    {
        // Enable exception reporting for MySQLi
        mysqli_report(MYSQLI_REPORT_STRICT | MYSQLI_REPORT_ALL);

        $dotenv = Dotenv::createImmutable(__DIR__, '../.env');
        $dotenv->safeLoad();

        $connection = mysqli_connect($db_host, $db_username, $db_password);

        if ($connection->connect_error) {
            die("Database connection error: " . $connection->connect_error);
        }

        $sql = "CREATE DATABASE IF NOT EXISTS $db_name";
        if ($connection->query($sql) === TRUE) {
            // Switch to the newly created database
            $connection->select_db($db_name);
        } else {
            die("Error creating database: " . $connection->error);
        }

        $sql = "
        CREATE TABLE IF NOT EXISTS users (
            id INT(11) UNSIGNED AUTO_INCREMENT PRIMARY KEY,
            name VARCHAR(100) NOT NULL,
            msisdn VARCHAR(20) NOT NULL,
            region VARCHAR(30) NOT NULL,
            created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
        )";
        if ($connection->query($sql) !== TRUE) {
            die("Error creating table 'users': " . $connection->error);
        }

        // Create a composite index on multiple columns
        // Check if the index already exists
        $indexName = 'idx_multi_columns';
        $sql = "
        SELECT COUNT(*) AS index_exists 
        FROM information_schema.statistics 
        WHERE table_schema = '$db_name' 
          AND table_name = 'users' 
          AND index_name = '$indexName'";

        $result = $connection->query($sql);
        $row = $result->fetch_assoc();

        if ($row['index_exists'] == 0) {
            // Create the index if it doesn't exist
            $sql = "CREATE INDEX $indexName ON users(msisdn, name, region, created_at)";
            if ($connection->query($sql) !== TRUE) {
                die("Error creating index '$indexName': " . $connection->error);
            }
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
        if ($connection->query($sql) !== TRUE) {
            die("Error creating table 'user_sessions': " . $connection->error);
        }

        return $connection;
    }
}
