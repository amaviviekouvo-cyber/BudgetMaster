<?php

require_once("../includes/bootstrap.php");

requireLogin();

if($_SERVER["REQUEST_METHOD"]=="POST"){

    checkCsrf();

    $sql=$pdo->prepare("
    DELETE FROM budgets
    WHERE id=? AND user_id=?
    ");

    $sql->execute([
        (int)($_POST["id"] ?? 0),
        $_SESSION["user_id"]
    ]);

    if($sql->rowCount()>0){
        flash("Budget supprimé.");
    }

}

redirect("index.php");
