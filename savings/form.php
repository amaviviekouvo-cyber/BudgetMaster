<?php

/*
 * Formulaire et validation partagés par add.php et edit.php.
 * Variables attendues : $saving (tableau), $id (0 pour un ajout)
 * Chaque épargne liée à un objectif fait progresser cet objectif.
 */

if(!isset($saving)){
    exit();
}

$user_id=$_SESSION["user_id"];

$sql=$pdo->prepare("
SELECT id,title
FROM goals
WHERE user_id=?
ORDER BY title ASC
");

$sql->execute([$user_id]);

$goals=$sql->fetchAll();

$goalTitles=array_column($goals,"title","id");

$errors=[];

if($_SERVER["REQUEST_METHOD"]=="POST"){

    checkCsrf();

    $previous=$saving;

    $goal_id=(int)($_POST["goal_id"] ?? 0);

    $saving=[
        "goal_id"=>$goal_id ?: null,
        "title"=>trim($_POST["title"] ?? ""),
        "amount"=>postAmount("amount"),
        "saving_date"=>postDate("saving_date")
    ];

    if($saving["goal_id"] && !isset($goalTitles[$saving["goal_id"]])){
        $errors[]="Objectif invalide.";
    }

    if($saving["goal_id"] && $saving["title"]==""){
        $saving["title"]=$goalTitles[$saving["goal_id"]] ?? "";
    }

    if($saving["title"]=="" || mb_strlen($saving["title"])>200){
        $errors[]="Choisissez un objectif ou donnez un nom à cette épargne.";
    }

    if($saving["amount"]===null){
        $errors[]="Le montant doit être un nombre positif.";
    }

    if($saving["saving_date"]===null){
        $errors[]="Date invalide.";
    }

    if(count($errors)==0){

        $pdo->beginTransaction();

        if($id){

            $pdo->prepare("
            UPDATE savings
            SET goal_id=?, title=?, amount=?, saving_date=?
            WHERE id=? AND user_id=?
            ")->execute([$saving["goal_id"],$saving["title"],$saving["amount"],$saving["saving_date"],$id,$user_id]);

            if($previous["goal_id"]){
                adjustGoal($pdo,$user_id,$previous["goal_id"],-$previous["amount"]);
            }

            flash("Épargne modifiée avec succès.");

        }else{

            $pdo->prepare("
            INSERT INTO savings(user_id,goal_id,title,amount,saving_date)
            VALUES(?,?,?,?,?)
            ")->execute([$user_id,$saving["goal_id"],$saving["title"],$saving["amount"],$saving["saving_date"]]);

            flash("Épargne enregistrée avec succès.");

        }

        if($saving["goal_id"]){
            adjustGoal($pdo,$user_id,$saving["goal_id"],$saving["amount"]);
        }

        $pdo->commit();

        redirect("index.php");

    }

}

function savingForm($saving,$goals,$errors,$submitLabel){

?>

<?php foreach($errors as $error){ echo alert($error,"danger"); } ?>

<div class="card p-4">

<form method="POST">

<?= csrfField() ?>

<div class="row">

<div class="col-md-6 mb-3">

<label class="form-label">Objectif</label>

<select
name="goal_id"
class="form-select">

<option value="">— Aucun (épargne libre) —</option>

<?php foreach($goals as $g){ ?>

<option value="<?= $g["id"] ?>" <?= $saving["goal_id"]==$g["id"] ? "selected" : "" ?>>

<?= e($g["title"]) ?>

</option>

<?php } ?>

</select>

</div>

<div class="col-md-6 mb-3">

<label class="form-label">Libellé</label>

<input
type="text"
name="title"
class="form-control"
maxlength="200"
placeholder="Par défaut : nom de l'objectif"
value="<?= e($saving["title"]) ?>">

</div>

<div class="col-md-6 mb-3">

<label class="form-label">Montant (<?= e($_SESSION["currency"] ?? "€") ?>)</label>

<input
type="number"
step="0.01"
min="0.01"
name="amount"
class="form-control"
value="<?= e($saving["amount"]) ?>"
required>

</div>

<div class="col-md-6 mb-3">

<label class="form-label">Date</label>

<input
type="date"
name="saving_date"
class="form-control"
value="<?= e($saving["saving_date"]) ?>"
required>

</div>

</div>

<button
class="btn btn-success">

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
