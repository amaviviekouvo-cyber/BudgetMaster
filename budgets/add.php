<?php

require_once("../includes/bootstrap.php");

requireLogin();

$id=0;

$budget=[
    "month"=>(int)date("n"),
    "year"=>(int)date("Y"),
    "amount"=>""
];

require("form.php");

$pageTitle="Nouveau budget";

include("../includes/header.php");
include("../includes/sidebar.php");

?>

<div class="content">

<?php include("../includes/navbar.php"); ?>

<h2 class="mb-4">💰 Nouveau Budget</h2>

<?php budgetForm($budget,$errors,"Enregistrer"); ?>

<?php include("../includes/footer.php"); ?>
