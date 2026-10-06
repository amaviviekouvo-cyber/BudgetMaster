<?php

require_once("../includes/bootstrap.php");

requireLogin();

$user_id=$_SESSION["user_id"];

$id=(int)($_GET["id"] ?? 0);

$sql=$pdo->prepare("
SELECT *
FROM categories
WHERE id=? AND user_id=?
");

$sql->execute([$id,$user_id]);

$category=$sql->fetch();

if(!$category){
    redirect("index.php");
}

$message="";

if($_SERVER["REQUEST_METHOD"]=="POST"){

    checkCsrf();

    $name=trim($_POST["name"] ?? "");

    $type=in_array($_POST["type"] ?? "",["income","expense"],true) ? $_POST["type"] : null;

    $check=$pdo->prepare("
    SELECT COUNT(*)
    FROM categories
    WHERE user_id=? AND name=? AND id<>?
    ");

    $check->execute([$user_id,$name,$id]);

    if($name=="" || mb_strlen($name)>50){

        $message=alert("Le nom de la catégorie est obligatoire (50 caractères maximum).","danger");

    }elseif($check->fetchColumn()>0){

        $message=alert("Une autre catégorie porte déjà ce nom.","warning");

    }else{

        $pdo->beginTransaction();

        $pdo->prepare("
        UPDATE categories
        SET name=?, type=?
        WHERE id=? AND user_id=?
        ")->execute([$name,$type,$id,$user_id]);

        /* Les transactions référencent la catégorie par son nom */

        $pdo->prepare("
        UPDATE transactions
        SET category=?
        WHERE user_id=? AND category=?
        ")->execute([$name,$user_id,$category["name"]]);

        $pdo->commit();

        flash("Catégorie modifiée avec succès.");

        redirect("index.php");

    }

    $category["name"]=$name;
    $category["type"]=$type;

}

$pageTitle="Modifier une catégorie";

include("../includes/header.php");
include("../includes/sidebar.php");

?>

<div class="content">

<?php include("../includes/navbar.php"); ?>

<h2 class="mb-4">✏️ Modifier une catégorie</h2>

<?= $message ?>

<div class="card p-4">

<form method="POST">

<?= csrfField() ?>

<div class="mb-3">

<label class="form-label">Nom de la catégorie</label>

<input
type="text"
name="name"
class="form-control"
maxlength="50"
value="<?= e($category["name"]) ?>"
required>

</div>

<div class="mb-3">

<label class="form-label">Type</label>

<select name="type" class="form-select">

<option value="expense" <?= $category["type"]=="expense" ? "selected" : "" ?>>💸 Dépense</option>

<option value="income" <?= $category["type"]=="income" ? "selected" : "" ?>>💰 Revenu</option>

</select>

</div>

<button class="btn btn-success">

💾 Enregistrer

</button>

<a
href="index.php"
class="btn btn-secondary">

Annuler

</a>

</form>

</div>

<?php include("../includes/footer.php"); ?>
