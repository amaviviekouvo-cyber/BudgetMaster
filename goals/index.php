<?php

require_once("../includes/bootstrap.php");

requireLogin();

$sql=$pdo->prepare("
SELECT *
FROM goals
WHERE user_id=?
ORDER BY status='Terminé', deadline IS NULL, deadline ASC
");

$sql->execute([$_SESSION["user_id"]]);

$goals=$sql->fetchAll();

$pageTitle="Objectifs";

include("../includes/header.php");
include("../includes/sidebar.php");

?>

<div class="content">

<?php include("../includes/navbar.php"); ?>

<div class="d-flex justify-content-between align-items-center mb-4">

<h2>🎯 Mes Objectifs</h2>

<a href="add.php" class="btn btn-primary">

<i class="fa-solid fa-plus"></i>

Nouvel objectif

</a>

</div>

<?php if(count($goals)==0){ ?>

<div class="card p-5 text-center">

<h4>Aucun objectif enregistré.</h4>

<p>Commence par créer ton premier objectif.</p>

<a href="add.php" class="btn btn-primary">

Créer un objectif

</a>

</div>

<?php } ?>

<div class="row">

<?php foreach($goals as $goal){

$progress=$goal["target_amount"]>0 ? min(100,($goal["current_amount"]/$goal["target_amount"])*100) : 0;

$remaining=max(0,$goal["target_amount"]-$goal["current_amount"]);

/* Montant à épargner chaque mois pour tenir l'échéance */

$monthly=null;

if($goal["deadline"] && $remaining>0){

    $diff=(new DateTime("today"))->diff(new DateTime($goal["deadline"]));

    $months=$diff->invert ? 0 : max(1,$diff->y*12+$diff->m+($diff->d>0 ? 1 : 0));

    $monthly=$months>0 ? $remaining/$months : false;

}

?>

<div class="col-lg-6 mb-4">

<div class="card p-4 shadow">

<h3><?= e($goal["title"]) ?></h3>

<p>

🎯 Objectif : <strong><?= money($goal["target_amount"]) ?></strong>

</p>

<p>

💰 Épargné : <strong><?= money($goal["current_amount"]) ?></strong>

</p>

<div class="progress mb-3" style="height:20px;">

<div
class="progress-bar bg-success"
style="width:<?= round($progress,2) ?>%">

<?= round($progress) ?> %

</div>

</div>

<p>

📅 Échéance :

<?= $goal["deadline"] ? date("d/m/Y",strtotime($goal["deadline"])) : "aucune" ?>

</p>

<?php if($monthly){ ?>

<p class="text-muted">

💡 Épargne conseillée : <strong><?= money($monthly) ?></strong> / mois

</p>

<?php }elseif($monthly===false){ ?>

<p class="text-danger">

⏰ Échéance dépassée — il reste <?= money($remaining) ?> à épargner.

</p>

<?php } ?>

<p>

État :

<?php if($goal["status"]=="Terminé"){ ?>

<span class="badge bg-success">Terminé</span>

<?php }else{ ?>

<span class="badge bg-warning text-dark">En cours</span>

<?php } ?>

</p>

<div>

<a
href="edit.php?id=<?= $goal["id"] ?>"
class="btn btn-warning">

✏️ Modifier

</a>

<?= deleteButton($goal["id"],"Supprimer cet objectif et les épargnes associées ?","🗑️ Supprimer","btn btn-danger") ?>

</div>

</div>

</div>

<?php } ?>

</div>

<?php include("../includes/footer.php"); ?>
