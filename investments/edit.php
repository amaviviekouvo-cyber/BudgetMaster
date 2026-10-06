<?php

require_once("../includes/bootstrap.php");

requireLogin();

$id=(int)($_GET["id"] ?? 0);

$sql=$pdo->prepare("
SELECT *
FROM investments
WHERE id=? AND user_id=?
");

$sql->execute([$id,$_SESSION["user_id"]]);

$investment=$sql->fetch();

if(!$investment){
    redirect("index.php");
}

require("form.php");

$pageTitle="Modifier un investissement";

include("../includes/header.php");
include("../includes/sidebar.php");

?>

<div class="content">

<?php include("../includes/navbar.php"); ?>

<h2 class="mb-4">✏️ Modifier un investissement</h2>

<?php investmentForm($investment,$errors,"Modifier"); ?>

<?php include("../includes/footer.php"); ?>
