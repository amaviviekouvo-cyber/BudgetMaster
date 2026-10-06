<?php

require_once("../includes/bootstrap.php");

requireLogin();

$sql=$pdo->prepare("
SELECT *
FROM investments
WHERE user_id=?
ORDER BY id DESC
");

$sql->execute([$_SESSION["user_id"]]);

$investments=$sql->fetchAll();

$totalInvested=array_sum(array_column($investments,"invested"));
$totalValue=array_sum(array_column($investments,"current_value"));
$totalGain=$totalValue-$totalInvested;
$totalPerf=$totalInvested>0 ? $totalGain/$totalInvested*100 : 0;

$pageTitle="Investissements";

include("../includes/header.php");
include("../includes/sidebar.php");

?>

<div class="content">

<?php include("../includes/navbar.php"); ?>

<div class="d-flex justify-content-between align-items-center mb-4">

<h2>📈 Mes investissements</h2>

<a
href="add.php"
class="btn btn-primary">

<i class="fa-solid fa-plus"></i>

Nouvel investissement

</a>

</div>

<div class="cards cards-auto mb-4">

<div class="dashboard-card blue">

<div class="icon">💼</div>

<div>

<h5>Total investi</h5>

<h2><?= money($totalInvested) ?></h2>

</div>

</div>

<div class="dashboard-card purple">

<div class="icon">📊</div>

<div>

<h5>Valeur actuelle</h5>

<h2><?= money($totalValue) ?></h2>

</div>

</div>

<div class="dashboard-card <?= $totalGain>=0 ? "pink" : "red" ?>">

<div class="icon"><?= $totalGain>=0 ? "🚀" : "📉" ?></div>

<div>

<h5>Plus / moins-value</h5>

<h2><?= ($totalGain>=0 ? "+" : "").money($totalGain) ?></h2>

<small><?= ($totalPerf>=0 ? "+" : "").number_format($totalPerf,2,","," ") ?> %</small>

</div>

</div>

</div>

<div class="card p-4">

<div class="table-responsive">

<table class="table table-hover align-middle">

<thead>

<tr>

<th>Nom</th>

<th>Plateforme</th>

<th>Investi</th>

<th>Valeur actuelle</th>

<th>Gain / Perte</th>

<th>Actions</th>

</tr>

</thead>

<tbody>

<?php if(count($investments)>0){ ?>

<?php foreach($investments as $inv){

$gain=$inv["current_value"]-$inv["invested"];

$perf=$inv["invested"]>0 ? $gain/$inv["invested"]*100 : 0;

?>

<tr>

<td><?= e($inv["name"]) ?></td>

<td><?= e($inv["platform"]) ?></td>

<td><?= money($inv["invested"]) ?></td>

<td><?= money($inv["current_value"]) ?></td>

<td>

<span class="fw-bold <?= $gain>=0 ? "text-success" : "text-danger" ?>">

<?= ($gain>=0 ? "+" : "").money($gain) ?>

(<?= ($perf>=0 ? "+" : "").number_format($perf,2,","," ") ?> %)

</span>

</td>

<td class="text-nowrap">

<a
href="edit.php?id=<?= $inv["id"] ?>"
class="btn btn-warning btn-sm">

✏️

</a>

<?= deleteButton($inv["id"],"Supprimer cet investissement ?") ?>

</td>

</tr>

<?php } ?>

<?php }else{ ?>

<tr>

<td colspan="6" class="text-center">

Aucun investissement enregistré.

</td>

</tr>

<?php } ?>

</tbody>

</table>

</div>

</div>

<?php include("../includes/footer.php"); ?>
