<?php

require('./vendor/autoload.php');

use Dotenv\Dotenv;

class dbConnection
{
    public static function myConnection($db_host, $db_username, $db_password, $db_name)
    {
        $dotenv = Dotenv::createImmutable(__DIR__, '../.env');
        $dotenv->safeLoad();

        try {
            $connection = mysqli_connect($db_host, $db_username, $db_password, $db_name);
            if ($connection) {
                $result = 'true';
            }
        } catch (\Throwable $th) {
            $result = 'false';
        }

        return $result;
    }
}
