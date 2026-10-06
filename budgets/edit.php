<?php

require_once("../includes/bootstrap.php");

requireLogin();

$id=(int)($_GET["id"] ?? 0);

$sql=$pdo->prepare("
SELECT *
FROM budgets
WHERE id=? AND user_id=?
");

$sql->execute([$id,$_SESSION["user_id"]]);

$budget=$sql->fetch();

if(!$budget){
    redirect("index.php");
}

require("form.php");

$pageTitle="Modifier un budget";

include("../includes/header.php");
include("../includes/sidebar.php");

?>

<div class="content">

<?php include("../includes/navbar.php"); ?>

<h2 class="mb-4">✏ Modifier Budget</h2>

<?php budgetForm($budget,$errors,"Modifier"); ?>

<?php include("../includes/footer.php"); ?>
