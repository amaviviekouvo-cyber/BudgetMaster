<?php

require_once("../includes/bootstrap.php");

$message = "";

if(isLoggedIn()){

    redirect("../dashboard/index.php");

}

if($_SERVER["REQUEST_METHOD"]=="POST"){

    checkCsrf();

    $email = trim($_POST["email"] ?? "");

    $password = $_POST["password"] ?? "";

    $sql = $pdo->prepare("SELECT * FROM users WHERE email=?");

    $sql->execute([$email]);

    $user = $sql->fetch();

    if($user && password_verify($password,$user["password"])){

        /* Nouvel identifiant de session pour éviter la fixation de session */

        session_regenerate_id(true);

        $_SESSION["user_id"] = $user["id"];

        $_SESSION["firstname"] = $user["firstname"];

        $_SESSION["lastname"] = $user["lastname"];

        $_SESSION["user_type"] = $user["user_type"];

        /* Thème et devise rechargés par le header */

        unset($_SESSION["theme"],$_SESSION["currency"]);

        redirect("../dashboard/index.php");

    }

    $message = "<div class='alert alert-danger'>Email ou mot de passe incorrect.</div>";

}

?>

<!DOCTYPE html>

<html lang="fr">

<head>

<meta charset="UTF-8">

<meta name="viewport" content="width=device-width, initial-scale=1">

<title>Connexion | BudgetMaster</title>

<link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.7/dist/css/bootstrap.min.css" rel="stylesheet">

<link rel="stylesheet" href="../assets/css/login.css">

<link href="https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;500;600;700&display=swap" rel="stylesheet">

</head>

<body>

<div class="login-box">

<h1>💖 BudgetMaster</h1>

<h2>Connexion</h2>

<?php

if(isset($_GET["success"])){

echo "<div class='alert alert-success'>Compte créé avec succès 🎉</div>";

}

echo $message;

?>

<form method="POST">

<?= csrfField() ?>

<input
type="email"
name="email"
placeholder="Adresse email"
value="<?= e($_POST["email"] ?? "") ?>"
required>

<input
type="password"
name="password"
placeholder="Mot de passe"
required>

<button type="submit">

Se connecter

</button>

</form>

<p>

Pas encore de compte ?

<a href="register.php">

Créer un compte

</a>

</p>

<p>

<a href="../index.php">← Retour à l'accueil</a>

</p>

</div>

</body>

</html>
