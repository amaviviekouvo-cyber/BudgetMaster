<?php

require_once("../includes/bootstrap.php");

requireLogin();

$id=0;

$goal=[
    "title"=>"",
    "target_amount"=>"",
    "current_amount"=>0,
    "deadline"=>""
];

require("form.php");

$pageTitle="Nouvel objectif";

include("../includes/header.php");
include("../includes/sidebar.php");

?>

<div class="content">

<?php include("../includes/navbar.php"); ?>

<div class="d-flex justify-content-between align-items-center mb-4">

<h2>🎯 Nouvel objectif</h2>

<a href="index.php" class="btn btn-secondary">

Retour

</a>

</div>

<?php goalForm($goal,$errors,"Enregistrer"); ?>

<?php include("../includes/footer.php"); ?>
