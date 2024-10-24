<?php
require('./menus/Welcome.php');

class Registration
{
    public static function Page_1()
    {
        $welcomeMessage = Welcome::getWelcomeMessage();

        return "$welcomeMessage\n\n1. Account registration\n2. Car registration\n3. Apartment registration";
    }

    public static function Page_2($option)
    {
        $output = '';

        switch ($option) {
            case '1':
                $output = "1. Personal account\n2. Family account";
                break;
            case '2':
                $output = "Enter name of car";
                break;
            case '3':
                $output = "Enter apartment name";
                break;
            default:
                $output = 'Invalid input entered';
                break;
        }
        
        return $output;
    }

    public static function sub_Menu_One($search)
    {
        $sub_menus = [
            'ACCOUNT_REGISTRATION'  => "1. Personal account\n2. Family account",
        ];
    }

}
