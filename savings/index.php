<?php

require_once("../includes/bootstrap.php");

requireLogin();

$sql=$pdo->prepare("
SELECT s.*, g.title goal_title
FROM savings s
LEFT JOIN goals g ON g.id=s.goal_id
WHERE s.user_id=?
ORDER BY s.saving_date DESC, s.id DESC
");

$sql->execute([$_SESSION["user_id"]]);

$savings=$sql->fetchAll();

$total=array_sum(array_column($savings,"amount"));

$pageTitle="Épargne";

include("../includes/header.php");
include("../includes/sidebar.php");

?>

<div class="content">

<?php include("../includes/navbar.php"); ?>

<div class="d-flex justify-content-between align-items-center mb-4">

<h2>🐷 Mon Épargne</h2>

<a href="add.php" class="btn btn-primary">

<i class="fa-solid fa-plus"></i>

Ajouter

</a>

</div>

<div class="dashboard-card purple mb-4">

<div class="icon">🐷</div>

<div>

<h5>Total épargné</h5>

<h2><?= money($total) ?></h2>

</div>

</div>

<div class="card p-4">

<div class="table-responsive">

<table class="table table-hover align-middle">

<thead>

<tr>

<th>Date</th>

<th>Libellé</th>

<th>Objectif</th>

<th>Montant</th>

<th>Actions</th>

</tr>

</thead>

<tbody>

<?php if(count($savings)>0){ ?>

<?php foreach($savings as $saving){ ?>

<tr>

<td><?= date("d/m/Y",strtotime($saving["saving_date"])) ?></td>

<td><?= e($saving["title"]) ?></td>

<td><?= $saving["goal_title"] ? "🎯 ".e($saving["goal_title"]) : "<span class='text-muted'>—</span>" ?></td>

<td class="text-success fw-bold">

<?= money($saving["amount"]) ?>

</td>

<td class="text-nowrap">

<a
href="edit.php?id=<?= $saving["id"] ?>"
class="btn btn-warning btn-sm">

✏️

</a>

<?= deleteButton($saving["id"],"Supprimer cette épargne ?") ?>

</td>

</tr>

<?php } ?>

<?php }else{ ?>

<tr>

<td colspan="5" class="text-center">

Aucune épargne enregistrée.

</td>

</tr>

<?php } ?>

</tbody>

</table>

</div>

</div>

<?php include("../includes/footer.php"); ?>
