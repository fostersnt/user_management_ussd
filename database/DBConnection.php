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

        try {
            $connection = mysqli_connect($db_host, $db_username, $db_password, $db_name);
            if (! $connection->connect_error) {
                $result = 'true';
            }
        } 
        catch (\Throwable $th) {
            $result = 'false';
        }

        return $result;
    }
}
