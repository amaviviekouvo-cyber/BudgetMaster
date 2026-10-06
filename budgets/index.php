<?php

require_once("../includes/bootstrap.php");

requireLogin();

$user_id=$_SESSION["user_id"];

/* Budgets avec le total des dépenses du mois correspondant */

$sql=$pdo->prepare("
SELECT b.*,
       COALESCE((
           SELECT SUM(t.amount)
           FROM transactions t
           WHERE t.user_id=b.user_id
           AND t.type='expense'
           AND MONTH(t.transaction_date)=b.month
           AND YEAR(t.transaction_date)=b.year
       ),0) spent
FROM budgets b
WHERE b.user_id=?
ORDER BY b.year DESC, b.month DESC
");

$sql->execute([$user_id]);

$budgets=$sql->fetchAll();

$pageTitle="Budgets";

include("../includes/header.php");
include("../includes/sidebar.php");

?>

<div class="content">

<?php include("../includes/navbar.php"); ?>

<div class="d-flex justify-content-between align-items-center mb-4">

<h2>💰 Mes Budgets</h2>

<a href="add.php" class="btn btn-primary">

<i class="fa-solid fa-plus"></i>

Nouveau Budget

</a>

</div>

<?php if(count($budgets)==0){ ?>

<div class="card p-5 text-center">

<h4>Aucun budget enregistré.</h4>

<p>Commence par créer ton premier budget.</p>

<a href="add.php" class="btn btn-primary">

Créer un budget

</a>

</div>

<?php } ?>

<div class="row">

<?php foreach($budgets as $budget){

$spent=$budget["spent"];

$progress=$budget["amount"]>0 ? min(100,($spent/$budget["amount"])*100) : 0;

$remaining=$budget["amount"]-$spent;

$barClass=$progress>=100 ? "bg-danger" : ($progress>=80 ? "bg-warning" : "bg-success");

?>

<div class="col-lg-6 mb-4">

<div class="card p-4 shadow">

<h3>

📅 <?= monthName($budget["month"]) ?> <?= (int)$budget["year"] ?>

</h3>

<p>

Budget : <strong><?= money($budget["amount"]) ?></strong>

</p>

<p>

Dépenses : <strong><?= money($spent) ?></strong>

</p>

<div class="progress mb-3" style="height:20px;">

<div
class="progress-bar <?= $barClass ?>"
style="width:<?= round($progress,2) ?>%">

<?= round($progress) ?> %

</div>

</div>

<?php if($remaining<0){ ?>

<p class="text-danger">

⚠ Budget dépassé de <?= money(-$remaining) ?>

</p>

<?php }else{ ?>

<p class="text-success">

✔ Reste à dépenser : <?= money($remaining) ?>

</p>

<?php } ?>

<div>

<a
href="edit.php?id=<?= $budget["id"] ?>"
class="btn btn-warning">

✏ Modifier

</a>

<?= deleteButton($budget["id"],"Supprimer ce budget ?","🗑 Supprimer","btn btn-danger") ?>

</div>

</div>

</div>

<?php } ?>

</div>

<?php include("../includes/footer.php"); ?>
