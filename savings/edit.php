<?php

require_once("../includes/bootstrap.php");

requireLogin();

$id=(int)($_GET["id"] ?? 0);

$sql=$pdo->prepare("
SELECT *
FROM savings
WHERE id=? AND user_id=?
");

$sql->execute([$id,$_SESSION["user_id"]]);

$saving=$sql->fetch();

if(!$saving){
    redirect("index.php");
}

require("form.php");

$pageTitle="Modifier une épargne";

include("../includes/header.php");
include("../includes/sidebar.php");

?>

<div class="content">

<?php include("../includes/navbar.php"); ?>

<h2 class="mb-4">✏️ Modifier une épargne</h2>

<?php savingForm($saving,$goals,$errors,"Modifier"); ?>

<?php include("../includes/footer.php"); ?>
