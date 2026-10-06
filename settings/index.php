<?php

require_once("../includes/bootstrap.php");

requireLogin();

$user_id=$_SESSION["user_id"];

$languages=["Français","English"];
$currencies=["€","$","FCFA"];
$themes=["light"=>"Clair","dark"=>"Sombre"];

/* Paramètres (créés si absents) */

$sql=$pdo->prepare("
SELECT *
FROM settings
WHERE user_id=?
LIMIT 1
");

$sql->execute([$user_id]);

$settings=$sql->fetch();

if(!$settings){

    $pdo->prepare("
    INSERT INTO settings(user_id,language,currency,theme)
    VALUES(?,?,?,?)
    ")->execute([$user_id,"Français","€","light"]);

    redirect("index.php");
}

$sql=$pdo->prepare("
SELECT firstname,lastname,email,phone,password
FROM users
WHERE id=?
");

$sql->execute([$user_id]);

$user=$sql->fetch();

$messages=[];

if($_SERVER["REQUEST_METHOD"]=="POST"){

    checkCsrf();

    $action=$_POST["action"] ?? "";

    /* Préférences */

    if($action=="preferences"){

        $language=in_array($_POST["language"] ?? "",$languages,true) ? $_POST["language"] : "Français";
        $currency=in_array($_POST["currency"] ?? "",$currencies,true) ? $_POST["currency"] : "€";
        $theme=isset($themes[$_POST["theme"] ?? ""]) ? $_POST["theme"] : "light";

        $pdo->prepare("
        UPDATE settings
        SET language=?, currency=?, theme=?
        WHERE user_id=?
        ")->execute([$language,$currency,$theme,$user_id]);

        $_SESSION["currency"]=$currency;
        $_SESSION["theme"]=$theme;

        flash("Préférences enregistrées.");

        redirect("index.php");

    }

    /* Profil */

    if($action=="profile"){

        $firstname=trim($_POST["firstname"] ?? "");
        $lastname=trim($_POST["lastname"] ?? "");
        $phone=trim($_POST["phone"] ?? "");

        if($firstname=="" || $lastname==""){

            $messages[]=alert("Le prénom et le nom sont obligatoires.","danger");

        }else{

            $pdo->prepare("
            UPDATE users
            SET firstname=?, lastname=?, phone=?
            WHERE id=?
            ")->execute([$firstname,$lastname,$phone,$user_id]);

            $_SESSION["firstname"]=$firstname;
            $_SESSION["lastname"]=$lastname;

            flash("Profil mis à jour.");

            redirect("index.php");

        }

    }

    /* Mot de passe */

    if($action=="password"){

        $currentPassword=$_POST["current_password"] ?? "";
        $newPassword=$_POST["new_password"] ?? "";
        $confirmPassword=$_POST["confirm_password"] ?? "";

        if(!password_verify($currentPassword,$user["password"])){

            $messages[]=alert("Mot de passe actuel incorrect.","danger");

        }elseif(strlen($newPassword)<8){

            $messages[]=alert("Le nouveau mot de passe doit contenir au moins 8 caractères.","danger");

        }elseif($newPassword!==$confirmPassword){

            $messages[]=alert("Les mots de passe sont différents.","danger");

        }else{

            $pdo->prepare("
            UPDATE users
            SET password=?
            WHERE id=?
            ")->execute([password_hash($newPassword,PASSWORD_DEFAULT),$user_id]);

            session_regenerate_id(true);

            flash("Mot de passe modifié.");

            redirect("index.php");

        }

    }

}

$pageTitle="Paramètres";

include("../includes/header.php");
include("../includes/sidebar.php");

?>

<div class="content">

<?php include("../includes/navbar.php"); ?>

<h2 class="mb-4">

⚙️ Paramètres

</h2>

<?= implode("",$messages) ?>

<div class="row">

<div class="col-lg-6 mb-4">

<div class="card p-4 shadow h-100">

<h4 class="mb-4">🎨 Préférences</h4>

<form method="POST">

<?= csrfField() ?>

<input type="hidden" name="action" value="preferences">

<div class="mb-4">

<label class="form-label">🌍 Langue</label>

<select
name="language"
class="form-select">

<?php foreach($languages as $language){ ?>

<option <?= $settings["language"]==$language ? "selected" : "" ?>><?= $language ?></option>

<?php } ?>

</select>

</div>

<div class="mb-4">

<label class="form-label">💶 Devise</label>

<select
name="currency"
class="form-select">

<?php foreach($currencies as $currency){ ?>

<option value="<?= e($currency) ?>" <?= $settings["currency"]==$currency ? "selected" : "" ?>><?= e($currency) ?></option>

<?php } ?>

</select>

</div>

<div class="mb-4">

<label class="form-label">🎨 Thème</label>

<select
name="theme"
class="form-select">

<?php foreach($themes as $value => $label){ ?>

<option value="<?= $value ?>" <?= $settings["theme"]==$value ? "selected" : "" ?>><?= $label ?></option>

<?php } ?>

</select>

</div>

<button class="btn btn-success">

💾 Enregistrer

</button>

</form>

</div>

</div>

<div class="col-lg-6 mb-4">

<div class="card p-4 shadow mb-4">

<h4 class="mb-4">👤 Profil</h4>

<form method="POST">

<?= csrfField() ?>

<input type="hidden" name="action" value="profile">

<div class="row">

<div class="col-md-6 mb-3">

<label class="form-label">Prénom</label>

<input type="text" name="firstname" class="form-control" value="<?= e($user["firstname"]) ?>" required>

</div>

<div class="col-md-6 mb-3">

<label class="form-label">Nom</label>

<input type="text" name="lastname" class="form-control" value="<?= e($user["lastname"]) ?>" required>

</div>

<div class="col-md-6 mb-3">

<label class="form-label">Email</label>

<input type="email" class="form-control" value="<?= e($user["email"]) ?>" disabled>

</div>

<div class="col-md-6 mb-3">

<label class="form-label">Téléphone</label>

<input type="tel" name="phone" class="form-control" value="<?= e($user["phone"]) ?>">

</div>

</div>

<button class="btn btn-success">

💾 Mettre à jour

</button>

</form>

</div>

<div class="card p-4 shadow">

<h4 class="mb-4">🔒 Mot de passe</h4>

<form method="POST">

<?= csrfField() ?>

<input type="hidden" name="action" value="password">

<div class="mb-3">

<input type="password" name="current_password" class="form-control" placeholder="Mot de passe actuel" required>

</div>

<div class="mb-3">

<input type="password" name="new_password" class="form-control" placeholder="Nouveau mot de passe (8 caractères min.)" minlength="8" required>

</div>

<div class="mb-3">

<input type="password" name="confirm_password" class="form-control" placeholder="Confirmer le nouveau mot de passe" minlength="8" required>

</div>

<button class="btn btn-success">

🔑 Changer le mot de passe

</button>

</form>

</div>

</div>

</div>

<script>

// Aperçu immédiat du thème

document.querySelector('select[name="theme"]').addEventListener("change",function(){

    document.body.classList.toggle("dark-mode",this.value==="dark");

});

</script>

<?php include("../includes/footer.php"); ?>
