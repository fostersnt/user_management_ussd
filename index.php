<?php

require ('./vendor/autoload.php');

$dotenv = Dotenv\Dotenv::createImmutable(__DIR__);
$dotenv->safeLoad();

if (strtolower($_SERVER['REQUEST_METHOD']) == 'post') {
    echo 'Request Accepted';
} else {
    echo 'Request Rejected';
}

