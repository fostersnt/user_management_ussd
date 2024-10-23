<?php
require ('./menus/Welcome.php');

class Registration {
    public static function Page_1 ()
    {
        $welcomeMessage = Welcome::getWelcomeMessage();

        return "$welcomeMessage\n\n1. Account registration\n2. Car registration\n3. Apartment registration";
    }
}