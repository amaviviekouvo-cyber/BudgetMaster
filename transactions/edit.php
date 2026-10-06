<?php

require_once("../includes/bootstrap.php");

requireLogin();

$user_id = $_SESSION["user_id"];

$id = (int)($_GET["id"] ?? 0);

$sql = $pdo->prepare("
SELECT *
FROM transactions
WHERE id=? AND user_id=?
");

$sql->execute([$id,$user_id]);

$transaction = $sql->fetch();

if(!$transaction){
    redirect("index.php");
}

$categories = userCategories($pdo,$user_id);

$errors = [];

if($_SERVER["REQUEST_METHOD"]=="POST"){

    checkCsrf();

    require("validate.php");

    if(count($errors)==0){

        $update = $pdo->prepare("
        UPDATE transactions
        SET
            title=?,
            amount=?,
            type=?,
            category=?,
            transaction_date=?,
            notes=?
        WHERE id=? AND user_id=?
        ");

        $update->execute([
            $transaction["title"],
            $transaction["amount"],
            $transaction["type"],
            $transaction["category"],
            $transaction["transaction_date"],
            $transaction["notes"],
            $id,
            $user_id
        ]);

        if($transaction["type"]=="expense"){
            checkBudget($pdo,$user_id,$transaction["transaction_date"]);
        }

        flash("Transaction modifiée avec succès.");

        redirect("index.php");
    }

}

$pageTitle = "Modifier une transaction";

include("../includes/header.php");
include("../includes/sidebar.php");
?>

<div class="content">

<?php include("../includes/navbar.php"); ?>

<div class="d-flex justify-content-between align-items-center mb-4">

<h2>✏️ Modifier une transaction</h2>

<a href="index.php" class="btn btn-secondary">Retour</a>

</div>

<?php foreach($errors as $error){ echo alert($error,"danger"); } ?>

<div class="card p-4">

<?php

$submitLabel = "Enregistrer les modifications";

include("form.php");

?>

</div>

<?php include("../includes/footer.php"); ?>
