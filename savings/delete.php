<?php

require_once("../includes/bootstrap.php");

requireLogin();

if($_SERVER["REQUEST_METHOD"]=="POST"){

    checkCsrf();

    $user_id=$_SESSION["user_id"];

    $sql=$pdo->prepare("
    SELECT *
    FROM savings
    WHERE id=? AND user_id=?
    ");

    $sql->execute([(int)($_POST["id"] ?? 0),$user_id]);

    $saving=$sql->fetch();

    if($saving){

        $pdo->beginTransaction();

        $pdo->prepare("
        DELETE FROM savings
        WHERE id=? AND user_id=?
        ")->execute([$saving["id"],$user_id]);

        if($saving["goal_id"]){
            adjustGoal($pdo,$user_id,$saving["goal_id"],-$saving["amount"]);
        }

        $pdo->commit();

        flash("Épargne supprimée.");

    }

}

redirect("index.php");
