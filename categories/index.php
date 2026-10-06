<?php

require_once("../includes/bootstrap.php");

requireLogin();

$user_id=$_SESSION["user_id"];

$sql=$pdo->prepare("
SELECT c.*,
       (SELECT COUNT(*) FROM transactions t WHERE t.user_id=c.user_id AND t.category=c.name) nb
FROM categories c
WHERE c.user_id=?
ORDER BY c.type DESC, c.name
");

$sql->execute([$user_id]);

$categories=$sql->fetchAll();

$pageTitle="Catégories";

include("../includes/header.php");
include("../includes/sidebar.php");

?>

<div class="content">

<?php include("../includes/navbar.php"); ?>

<div class="d-flex justify-content-between align-items-center mb-4">

<h2>📂 Mes catégories</h2>

</div>

<div class="card p-4 mb-4">

<form method="POST" action="add.php">

<?= csrfField() ?>

<div class="row g-2">

<div class="col-md-7">

<input
type="text"
name="name"
class="form-control"
maxlength="50"
placeholder="Nom de la catégorie"
required>

</div>

<div class="col-md-3">

<select name="type" class="form-select">

<option value="expense">💸 Dépense</option>

<option value="income">💰 Revenu</option>

</select>

</div>

<div class="col-md-2">

<button class="btn btn-primary w-100">

Ajouter

</button>

</div>

</div>

</form>

</div>

<div class="card">

<div class="table-responsive">

<table class="table table-hover align-middle">

<thead>

<tr>

<th>Nom</th>

<th>Type</th>

<th>Transactions</th>

<th>Actions</th>

</tr>

</thead>

<tbody>

<?php if(count($categories)>0){ ?>

<?php foreach($categories as $cat){ ?>

<tr>

<td><?= e($cat["name"]) ?></td>

<td>

<?php if($cat["type"]=="income"){ ?>

<span class="badge bg-success">Revenu</span>

<?php }elseif($cat["type"]=="expense"){ ?>

<span class="badge bg-danger">Dépense</span>

<?php }else{ ?>

<span class="badge bg-secondary">Les deux</span>

<?php } ?>

</td>

<td><?= (int)$cat["nb"] ?></td>

<td class="text-nowrap">

<a
href="edit.php?id=<?= $cat["id"] ?>"
class="btn btn-warning btn-sm">

✏️ Modifier

</a>

<?= deleteButton($cat["id"],"Supprimer cette catégorie ? Les transactions associées sont conservées.","🗑️ Supprimer") ?>

</td>

</tr>

<?php } ?>

<?php }else{ ?>

<tr>

<td colspan="4" class="text-center">

Aucune catégorie. Les catégories par défaut sont proposées lors de l'ajout d'une transaction.

</td>

</tr>

<?php } ?>

</tbody>

</table>

</div>

</div>

<?php include("../includes/footer.php"); ?>
