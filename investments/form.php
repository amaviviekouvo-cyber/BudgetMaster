<?php

/*
 * Formulaire et validation partagés par add.php et edit.php.
 * Variables attendues : $investment (tableau), $id (0 pour un ajout)
 */

if(!isset($investment)){
    exit();
}

$errors=[];

if($_SERVER["REQUEST_METHOD"]=="POST"){

    checkCsrf();

    $investment=[
        "name"=>trim($_POST["name"] ?? ""),
        "platform"=>trim($_POST["platform"] ?? ""),
        "invested"=>postAmount("invested",true),
        "current_value"=>postAmount("current_value",true)
    ];

    if($investment["name"]=="" || mb_strlen($investment["name"])>200){
        $errors[]="Le nom est obligatoire.";
    }

    if($investment["platform"]=="" || mb_strlen($investment["platform"])>100){
        $errors[]="La plateforme est obligatoire.";
    }

    if($investment["invested"]===null || $investment["current_value"]===null){
        $errors[]="Les montants doivent être des nombres positifs.";
    }

    if(count($errors)==0){

        if($id){

            $pdo->prepare("
            UPDATE investments
            SET name=?, platform=?, invested=?, current_value=?
            WHERE id=? AND user_id=?
            ")->execute([$investment["name"],$investment["platform"],$investment["invested"],$investment["current_value"],$id,$_SESSION["user_id"]]);

            flash("Investissement modifié avec succès.");

        }else{

            $pdo->prepare("
            INSERT INTO investments(user_id,name,platform,invested,current_value)
            VALUES(?,?,?,?,?)
            ")->execute([$_SESSION["user_id"],$investment["name"],$investment["platform"],$investment["invested"],$investment["current_value"]]);

            flash("Investissement ajouté avec succès.");

        }

        redirect("index.php");

    }

}

function investmentForm($investment,$errors,$submitLabel){

    $currency=e($_SESSION["currency"] ?? "€");

?>

<?php foreach($errors as $error){ echo alert($error,"danger"); } ?>

<div class="card p-4 shadow">

<form method="POST">

<?= csrfField() ?>

<div class="row">

<div class="col-md-6 mb-3">

<label class="form-label">Nom</label>

<input
type="text"
name="name"
class="form-control"
maxlength="200"
placeholder="Ex : MSCI World, Apple..."
value="<?= e($investment["name"]) ?>"
required>

</div>

<div class="col-md-6 mb-3">

<label class="form-label">Plateforme</label>

<input
type="text"
name="platform"
class="form-control"
maxlength="100"
placeholder="Trade Republic, Boursorama..."
value="<?= e($investment["platform"]) ?>"
required>

</div>

<div class="col-md-6 mb-3">

<label class="form-label">Montant investi (<?= $currency ?>)</label>

<input
type="number"
step="0.01"
min="0"
name="invested"
class="form-control"
value="<?= e($investment["invested"]) ?>"
required>

</div>

<div class="col-md-6 mb-3">

<label class="form-label">Valeur actuelle (<?= $currency ?>)</label>

<input
type="number"
step="0.01"
min="0"
name="current_value"
class="form-control"
value="<?= e($investment["current_value"]) ?>"
required>

</div>

</div>

<div class="mt-3">

<button
type="submit"
class="btn btn-success">

💾 <?= e($submitLabel) ?>

</button>

<a
href="index.php"
class="btn btn-danger">

Annuler

</a>

</div>

</form>

</div>

<?php

}
