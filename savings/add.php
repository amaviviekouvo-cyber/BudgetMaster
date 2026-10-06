<?php

require_once("../includes/bootstrap.php");

requireLogin();

$id=0;

$saving=[
    "goal_id"=>(int)($_GET["goal"] ?? 0),
    "title"=>"",
    "amount"=>"",
    "saving_date"=>date("Y-m-d")
];

require("form.php");

$pageTitle="Ajouter une épargne";

include("../includes/header.php");
include("../includes/sidebar.php");

?>

<div class="content">

<?php include("../includes/navbar.php"); ?>

<h2 class="mb-4">🐷 Ajouter une épargne</h2>

<?php savingForm($saving,$goals,$errors,"Enregistrer"); ?>

<?php include("../includes/footer.php"); ?>
