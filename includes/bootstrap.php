<?php

/*
 * Point d'entrée commun : session sécurisée, base de données et fonctions.
 */

if(session_status()===PHP_SESSION_NONE){

    session_set_cookie_params([
        "httponly" => true,
        "samesite" => "Lax",
        "secure" => !empty($_SERVER["HTTPS"]) && $_SERVER["HTTPS"]!=="off"
    ]);

    session_start();

}

date_default_timezone_set("Europe/Paris");

require_once(__DIR__."/../config/database.php");
require_once(__DIR__."/functions.php");
