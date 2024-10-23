<?php
require ('./menus/Welcome.php');

class Registration {
    public static function Page_1 ()
    {
        $welcomeMessage = Welcome::getWelcomeMessage();

        return "$welcomeMessage\n1. Account registration\n2. Car registration\nApartment registration";
    }
}