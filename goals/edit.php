<?php

require_once("../includes/bootstrap.php");

requireLogin();

$id=(int)($_GET["id"] ?? 0);

$sql=$pdo->prepare("
SELECT *
FROM goals
WHERE id=? AND user_id=?
");

$sql->execute([$id,$_SESSION["user_id"]]);

$goal=$sql->fetch();

if(!$goal){
    redirect("index.php");
}

require("form.php");

$pageTitle="Modifier un objectif";

include("../includes/header.php");
include("../includes/sidebar.php");

?>

<div class="content">

<?php include("../includes/navbar.php"); ?>

<h2 class="mb-4">✏️ Modifier un objectif</h2>

<?php goalForm($goal,$errors,"Enregistrer"); ?>

<?php include("../includes/footer.php"); ?>
