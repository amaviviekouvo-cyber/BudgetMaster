<?php

require_once("../includes/bootstrap.php");

requireLogin();

if($_SERVER["REQUEST_METHOD"]=="POST"){

    checkCsrf();

    $name=trim($_POST["name"] ?? "");

    $type=in_array($_POST["type"] ?? "",["income","expense"],true) ? $_POST["type"] : null;

    if($name=="" || mb_strlen($name)>50){

        flash("Le nom de la catégorie est obligatoire (50 caractères maximum).","danger");

    }else{

        $check=$pdo->prepare("
        SELECT COUNT(*)
        FROM categories
        WHERE user_id=? AND name=?
        ");

        $check->execute([$_SESSION["user_id"],$name]);

        if($check->fetchColumn()>0){

            flash("Cette catégorie existe déjà.","warning");

        }else{

            $pdo->prepare("
            INSERT INTO categories(user_id,name,type)
            VALUES(?,?,?)
            ")->execute([$_SESSION["user_id"],$name,$type]);

            flash("Catégorie ajoutée avec succès.");

        }

    }

}

redirect("index.php");
