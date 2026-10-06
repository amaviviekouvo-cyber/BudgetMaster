<?php

/*
 * Formulaire et validation partagés par add.php et edit.php.
 * Variables attendues : $goal (tableau), $id (0 pour un ajout)
 */

if(!isset($goal)){
    exit();
}

$errors=[];

if($_SERVER["REQUEST_METHOD"]=="POST"){

    checkCsrf();

    $previousStatus=$goal["status"] ?? "En cours";

    $goal=[
        "title"=>trim($_POST["title"] ?? ""),
        "target_amount"=>postAmount("target_amount"),
        "current_amount"=>postAmount("current_amount",true) ?? 0,
        "deadline"=>postDate("deadline",false)
    ];

    if($goal["title"]=="" || mb_strlen($goal["title"])>200){
        $errors[]="Le titre est obligatoire.";
    }

    if($goal["target_amount"]===null){
        $errors[]="Le montant cible doit être un nombre positif.";
    }

    if($goal["deadline"]===null){
        $errors[]="Date limite invalide.";
    }

    if(count($errors)==0){

        $status=$goal["current_amount"]>=$goal["target_amount"] ? "Terminé" : "En cours";

        $deadline=$goal["deadline"]!=="" ? $goal["deadline"] : null;

        if($id){

            $pdo->prepare("
            UPDATE goals
            SET title=?, target_amount=?, current_amount=?, deadline=?, status=?
            WHERE id=? AND user_id=?
            ")->execute([$goal["title"],$goal["target_amount"],$goal["current_amount"],$deadline,$status,$id,$_SESSION["user_id"]]);

            flash("Objectif modifié avec succès.");

        }else{

            $pdo->prepare("
            INSERT INTO goals(user_id,title,target_amount,current_amount,deadline,status)
            VALUES(?,?,?,?,?,?)
            ")->execute([$_SESSION["user_id"],$goal["title"],$goal["target_amount"],$goal["current_amount"],$deadline,$status]);

            flash("Objectif créé avec succès.");

        }

        if($status=="Terminé" && $previousStatus!="Terminé"){
            notify($pdo,$_SESSION["user_id"],"🎉 Félicitations ! Objectif « ".$goal["title"]." » atteint.");
        }

        redirect("index.php");

    }

}

function goalForm($goal,$errors,$submitLabel){

    $currency=e($_SESSION["currency"] ?? "€");

?>

<?php foreach($errors as $error){ echo alert($error,"danger"); } ?>

<div class="card p-4 shadow">

<form method="POST">

<?= csrfField() ?>

<div class="row">

<div class="col-md-6 mb-3">

<label class="form-label">Titre</label>

<input
type="text"
name="title"
class="form-control"
maxlength="200"
placeholder="Ex : Acheter une voiture"
value="<?= e($goal["title"]) ?>"
required>

</div>

<div class="col-md-6 mb-3">

<label class="form-label">Montant cible (<?= $currency ?>)</label>

<input
type="number"
step="0.01"
min="0.01"
name="target_amount"
class="form-control"
value="<?= e($goal["target_amount"]) ?>"
required>

</div>

<div class="col-md-6 mb-3">

<label class="form-label">Montant déjà épargné (<?= $currency ?>)</label>

<input
type="number"
step="0.01"
min="0"
name="current_amount"
class="form-control"
value="<?= e($goal["current_amount"]) ?>">

</div>

<div class="col-md-6 mb-3">

<label class="form-label">Date limite (facultative)</label>

<input
type="date"
name="deadline"
class="form-control"
value="<?= e($goal["deadline"]) ?>">

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
