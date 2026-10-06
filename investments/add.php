<?php

require_once("../includes/bootstrap.php");

requireLogin();

$id=0;

$investment=[
    "name"=>"",
    "platform"=>"",
    "invested"=>"",
    "current_value"=>""
];

require("form.php");

$pageTitle="Nouvel investissement";

include("../includes/header.php");
include("../includes/sidebar.php");

?>

<div class="content">

<?php include("../includes/navbar.php"); ?>

<div class="d-flex justify-content-between align-items-center mb-4">

<h2>📈 Nouvel investissement</h2>

<a href="index.php" class="btn btn-secondary">

Retour

</a>

</div>

<?php investmentForm($investment,$errors,"Enregistrer"); ?>

<?php include("../includes/footer.php"); ?>
