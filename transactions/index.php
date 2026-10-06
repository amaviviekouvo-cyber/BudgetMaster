<?php

require_once("../includes/bootstrap.php");

requireLogin();

require("filters.php");

$sql = $pdo->prepare("
    SELECT *
    FROM transactions
    WHERE $where
    ORDER BY transaction_date DESC, id DESC
");

$sql->execute($params);

$transactions = $sql->fetchAll();

/* Totaux de la sélection */

$totalIncome = 0;
$totalExpense = 0;

foreach($transactions as $t){

    if($t["type"]=="income"){
        $totalIncome += $t["amount"];
    }else{
        $totalExpense += $t["amount"];
    }

}

$pageTitle = "Transactions";

include("../includes/header.php");
include("../includes/sidebar.php");

?>

<div class="content">

<?php include("../includes/navbar.php"); ?>

<div class="d-flex justify-content-between align-items-center flex-wrap gap-2 mb-4">

    <h2>💸 Mes Transactions</h2>

    <div class="d-flex gap-2">

        <a href="export.php?<?= e(http_build_query($filters)) ?>" class="btn btn-secondary">

            <i class="fa-solid fa-file-csv"></i>

            Exporter

        </a>

        <a href="add.php" class="btn btn-primary">

            <i class="fa-solid fa-plus"></i>

            Nouvelle transaction

        </a>

    </div>

</div>

<form method="GET" class="card p-3 mb-4">

<div class="row g-2">

<div class="col-md-5">

<input
type="text"
name="search"
class="form-control"
placeholder="Rechercher (titre, catégorie, notes)..."
value="<?= e($filters["search"]) ?>">

</div>

<div class="col-md-2">

<select name="type" class="form-select">

<option value="">Tous les types</option>

<option value="income" <?= $filters["type"]=="income" ? "selected" : "" ?>>Revenus</option>

<option value="expense" <?= $filters["type"]=="expense" ? "selected" : "" ?>>Dépenses</option>

</select>

</div>

<div class="col-md-3">

<input
type="month"
name="month"
class="form-control"
value="<?= e($filters["month"]) ?>">

</div>

<div class="col-md-2 d-flex gap-2">

<button class="btn btn-primary flex-fill" title="Filtrer">

<i class="fa-solid fa-magnifying-glass"></i>

</button>

<a href="index.php" class="btn btn-secondary flex-fill" title="Réinitialiser">

<i class="fa-solid fa-xmark"></i>

</a>

</div>

</div>

</form>

<div class="row mb-2">

<div class="col-md-4 mb-2">

<div class="card p-3">

<small class="text-muted">Revenus</small>

<strong class="text-success fs-5">+<?= money($totalIncome) ?></strong>

</div>

</div>

<div class="col-md-4 mb-2">

<div class="card p-3">

<small class="text-muted">Dépenses</small>

<strong class="text-danger fs-5">-<?= money($totalExpense) ?></strong>

</div>

</div>

<div class="col-md-4 mb-2">

<div class="card p-3">

<small class="text-muted">Solde de la sélection</small>

<strong class="fs-5 <?= $totalIncome-$totalExpense>=0 ? "text-success" : "text-danger" ?>"><?= money($totalIncome-$totalExpense) ?></strong>

</div>

</div>

</div>

<div class="card">

<div class="table-responsive">

<table class="table table-hover align-middle">

<thead class="table-light">

<tr>

<th>Date</th>

<th>Titre</th>

<th>Catégorie</th>

<th>Type</th>

<th>Montant</th>

<th>Actions</th>

</tr>

</thead>

<tbody>

<?php if(count($transactions)>0){ ?>

<?php foreach($transactions as $t){ ?>

<tr>

<td><?= date("d/m/Y",strtotime($t["transaction_date"])) ?></td>

<td>

<?= e($t["title"]) ?>

<?php if($t["notes"]){ ?>

<br><small class="text-muted"><?= e($t["notes"]) ?></small>

<?php } ?>

</td>

<td><?= e($t["category"]) ?></td>

<td>

<?php if($t["type"]=="income"){ ?>

<span class="badge bg-success">Revenu</span>

<?php }else{ ?>

<span class="badge bg-danger">Dépense</span>

<?php } ?>

</td>

<td>

<?php if($t["type"]=="income"){ ?>

<span class="text-success fw-bold">+<?= money($t["amount"]) ?></span>

<?php }else{ ?>

<span class="text-danger fw-bold">-<?= money($t["amount"]) ?></span>

<?php } ?>

</td>

<td class="text-nowrap">

<a
href="edit.php?id=<?= $t["id"] ?>"
class="btn btn-warning btn-sm"
title="Modifier">

✏️

</a>

<?= deleteButton($t["id"],"Supprimer cette transaction ?") ?>

</td>

</tr>

<?php } ?>

<?php }else{ ?>

<tr>

<td colspan="6" class="text-center">

Aucune transaction trouvée.

</td>

</tr>

<?php } ?>

</tbody>

</table>

</div>

</div>

<?php include("../includes/footer.php"); ?>
