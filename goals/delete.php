<?php

require_once("../includes/bootstrap.php");

requireLogin();

if($_SERVER["REQUEST_METHOD"]=="POST"){

    checkCsrf();

    /* Les épargnes liées sont supprimées en cascade (clé étrangère) */

    $delete=$pdo->prepare("
    DELETE FROM goals
    WHERE id=? AND user_id=?
    ");

    $delete->execute([
        (int)($_POST["id"] ?? 0),
        $_SESSION["user_id"]
    ]);

    if($delete->rowCount()>0){
        flash("Objectif supprimé.");
    }

}

redirect("index.php");
