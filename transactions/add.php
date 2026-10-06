<?php

require_once("../includes/bootstrap.php");

requireLogin();

$user_id = $_SESSION["user_id"];

$categories = userCategories($pdo,$user_id);

$transaction = [
    "title" => "",
    "amount" => "",
    "type" => "expense",
    "category" => "",
    "transaction_date" => date("Y-m-d"),
    "notes" => ""
];

$errors = [];

if($_SERVER["REQUEST_METHOD"]=="POST"){

    checkCsrf();

    require("validate.php");

    if(count($errors)==0){

        $sql = $pdo->prepare("
        INSERT INTO transactions
        (user_id,title,amount,type,category,transaction_date,notes)
        VALUES(?,?,?,?,?,?,?)
        ");

        $sql->execute([
            $user_id,
            $transaction["title"],
            $transaction["amount"],
            $transaction["type"],
            $transaction["category"],
            $transaction["transaction_date"],
            $transaction["notes"]
        ]);

        if($transaction["type"]=="expense"){
            checkBudget($pdo,$user_id,$transaction["transaction_date"]);
        }

        flash("Transaction ajoutée avec succès.");

        redirect("index.php");
    }

}

$pageTitle = "Nouvelle transaction";

include("../includes/header.php");
include("../includes/sidebar.php");
?>

<div class="content">

<?php include("../includes/navbar.php"); ?>

<div class="d-flex justify-content-between align-items-center mb-4">

<h2>💸 Nouvelle transaction</h2>

<a href="index.php" class="btn btn-secondary">Retour</a>

</div>

<?php foreach($errors as $error){ echo alert($error,"danger"); } ?>

<div class="card p-4">

<?php

$submitLabel = "Enregistrer";

include("form.php");

?>

</div>

<?php include("../includes/footer.php"); ?>
