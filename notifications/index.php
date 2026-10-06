<?php

require_once("../includes/bootstrap.php");

requireLogin();

$user_id = $_SESSION["user_id"];

/* Suppression de toutes les notifications */

if($_SERVER["REQUEST_METHOD"]=="POST"){

    checkCsrf();

    $pdo->prepare("
    DELETE FROM notifications
    WHERE user_id=?
    ")->execute([$user_id]);

    flash("Notifications supprimées.");

    redirect("index.php");

}

/* Récupérer les notifications (avant de les marquer comme lues) */

$sql = $pdo->prepare("
SELECT *
FROM notifications
WHERE user_id=?
ORDER BY created_at DESC, id DESC
");

$sql->execute([$user_id]);

$notifications = $sql->fetchAll();

/* Marquer toutes les notifications comme lues */

$pdo->prepare("
UPDATE notifications
SET is_read=1
WHERE user_id=?
")->execute([$user_id]);

$pageTitle = "Notifications";

include("../includes/header.php");
include("../includes/sidebar.php");

?>

<div class="content">

<?php include("../includes/navbar.php"); ?>

<div class="d-flex justify-content-between align-items-center mb-4">

<h2>🔔 Notifications</h2>

<?php if(count($notifications)>0){ ?>

<form method="POST" onsubmit="return confirm('Supprimer toutes les notifications ?')">

<?= csrfField() ?>

<button class="btn btn-secondary">

<i class="fa-solid fa-trash"></i>

Tout effacer

</button>

</form>

<?php } ?>

</div>

<?php if(count($notifications)==0){ ?>

<div class="card p-5 text-center">

<h4>Aucune notification</h4>

<p>Vous n'avez aucune notification pour le moment.</p>

</div>

<?php } ?>

<?php foreach($notifications as $notification){ ?>

<div class="card shadow-sm p-3 mb-3">

<div class="d-flex justify-content-between gap-3">

<div>

<p class="mb-1">

<?= e($notification["message"]) ?>

</p>

<small class="text-muted">

<?= date("d/m/Y H:i",strtotime($notification["created_at"])) ?>

</small>

</div>

<div>

<?php if($notification["is_read"]==0){ ?>

<span class="badge bg-danger">Nouveau</span>

<?php }else{ ?>

<span class="badge bg-success">Lu</span>

<?php } ?>

</div>

</div>

</div>

<?php } ?>

<?php include("../includes/footer.php"); ?>
