<?php

require_once("../includes/bootstrap.php");

requireLogin();

if($_SERVER["REQUEST_METHOD"]=="POST"){

    checkCsrf();

    $delete=$pdo->prepare("
    DELETE FROM categories
    WHERE id=? AND user_id=?
    ");

    $delete->execute([
        (int)($_POST["id"] ?? 0),
        $_SESSION["user_id"]
    ]);

    if($delete->rowCount()>0){
        flash("Catégorie supprimée.");
    }

}

redirect("index.php");
