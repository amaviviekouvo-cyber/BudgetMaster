<?php

/*
 * Formulaire et validation partagés par add.php et edit.php.
 * Variables attendues : $budget (tableau), $id (0 pour un ajout)
 */

if(!isset($budget)){
    exit();
}

$errors=[];

if($_SERVER["REQUEST_METHOD"]=="POST"){

    checkCsrf();

    $budget=[
        "month"=>(int)($_POST["month"] ?? 0),
        "year"=>(int)($_POST["year"] ?? 0),
        "amount"=>postAmount("amount")
    ];

    if($budget["month"]<1 || $budget["month"]>12){
        $errors[]="Mois invalide.";
    }

    if($budget["year"]<2000 || $budget["year"]>2100){
        $errors[]="Année invalide.";
    }

    if($budget["amount"]===null){
        $errors[]="Le montant doit être un nombre positif.";
    }

    $check=$pdo->prepare("
    SELECT COUNT(*)
    FROM budgets
    WHERE user_id=? AND month=? AND year=? AND id<>?
    ");

    $check->execute([$_SESSION["user_id"],$budget["month"],$budget["year"],$id]);

    if($check->fetchColumn()>0){
        $errors[]="Un budget existe déjà pour ".monthName($budget["month"])." ".$budget["year"].".";
    }

    if(count($errors)==0){

        if($id){

            $pdo->prepare("
            UPDATE budgets
            SET month=?, year=?, amount=?
            WHERE id=? AND user_id=?
            ")->execute([$budget["month"],$budget["year"],$budget["amount"],$id,$_SESSION["user_id"]]);

            flash("Budget modifié avec succès.");

        }else{

            $pdo->prepare("
            INSERT INTO budgets(user_id,month,year,amount)
            VALUES(?,?,?,?)
            ")->execute([$_SESSION["user_id"],$budget["month"],$budget["year"],$budget["amount"]]);

            flash("Budget créé avec succès.");

        }

        checkBudget($pdo,$_SESSION["user_id"],sprintf("%04d-%02d-01",$budget["year"],$budget["month"]));

        redirect("index.php");

    }

}

function budgetForm($budget,$errors,$submitLabel){

?>

<?php foreach($errors as $error){ echo alert($error,"danger"); } ?>

<div class="card p-4">

<form method="POST">

<?= csrfField() ?>

<div class="row">

<div class="col-md-4 mb-3">

<label class="form-label">Mois</label>

<select
name="month"
class="form-select">

<?php for($i=1;$i<=12;$i++){ ?>

<option value="<?= $i ?>" <?= $i==$budget["month"] ? "selected" : "" ?>><?= monthName($i) ?></option>

<?php } ?>

</select>

</div>

<div class="col-md-4 mb-3">

<label class="form-label">Année</label>

<input
type="number"
name="year"
min="2000"
max="2100"
class="form-control"
value="<?= e($budget["year"]) ?>"
required>

</div>

<div class="col-md-4 mb-3">

<label class="form-label">Budget (<?= e($_SESSION["currency"] ?? "€") ?>)</label>

<input
type="number"
step="0.01"
min="0.01"
name="amount"
class="form-control"
value="<?= e($budget["amount"]) ?>"
required>

</div>

</div>

<button class="btn btn-success">

💾 <?= e($submitLabel) ?>

</button>

<a
href="index.php"
class="btn btn-danger">

Annuler

</a>

</form>

</div>

<?php

}
