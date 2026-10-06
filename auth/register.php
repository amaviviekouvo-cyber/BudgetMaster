<?php

require_once("../includes/bootstrap.php");

if(isLoggedIn()){

    redirect("../dashboard/index.php");

}

$message = "";

$userTypes = [
    "Etudiant" => "🎓 Étudiant",
    "Salarié" => "👨‍💼 Salarié",
    "Freelance" => "💻 Freelance",
    "Famille" => "👨‍👩‍👧 Famille"
];

if ($_SERVER["REQUEST_METHOD"] == "POST") {

    checkCsrf();

    $firstname = trim($_POST["firstname"] ?? "");
    $lastname = trim($_POST["lastname"] ?? "");
    $email = trim($_POST["email"] ?? "");
    $phone = trim($_POST["phone"] ?? "");
    $password = $_POST["password"] ?? "";
    $confirm_password = $_POST["confirm_password"] ?? "";
    $user_type = $_POST["user_type"] ?? "";

    if (
        empty($firstname) ||
        empty($lastname) ||
        empty($email) ||
        empty($password)
    ) {

        $message = "<div class='alert alert-danger'>Veuillez remplir tous les champs obligatoires.</div>";

    } elseif (!filter_var($email, FILTER_VALIDATE_EMAIL)) {

        $message = "<div class='alert alert-danger'>Adresse email invalide.</div>";

    } elseif (!isset($userTypes[$user_type])) {

        $message = "<div class='alert alert-danger'>Profil invalide.</div>";

    } elseif (strlen($password) < 8) {

        $message = "<div class='alert alert-danger'>Le mot de passe doit contenir au moins 8 caractères.</div>";

    } elseif ($password !== $confirm_password) {

        $message = "<div class='alert alert-danger'>Les mots de passe sont différents.</div>";

    } else {

        $check = $pdo->prepare("SELECT id FROM users WHERE email=?");
        $check->execute([$email]);

        if ($check->fetch()) {

            $message = "<div class='alert alert-warning'>Cet email existe déjà.</div>";

        } else {

            $pdo->beginTransaction();

            $insert = $pdo->prepare("
                INSERT INTO users
                (firstname, lastname, email, password, phone, user_type)
                VALUES (?, ?, ?, ?, ?, ?)
            ");

            $insert->execute([
                $firstname,
                $lastname,
                $email,
                password_hash($password, PASSWORD_DEFAULT),
                $phone,
                $user_type
            ]);

            $user_id = $pdo->lastInsertId();

            /* Paramètres et catégories par défaut */

            $pdo->prepare("
                INSERT INTO settings(user_id,language,currency,theme)
                VALUES(?,?,?,?)
            ")->execute([$user_id, "Français", "€", "light"]);

            $insertCategory = $pdo->prepare("
                INSERT INTO categories(user_id,name,type)
                VALUES(?,?,?)
            ");

            foreach (defaultCategories() as $cat) {
                $insertCategory->execute([$user_id, $cat[0], $cat[1]]);
            }

            $pdo->prepare("
                INSERT INTO notifications(user_id,message)
                VALUES(?,?)
            ")->execute([$user_id, "👋 Bienvenue sur BudgetMaster ! Commence par ajouter ta première transaction."]);

            $pdo->commit();

            redirect("login.php?success=1");

        }

    }

}
?>

<!DOCTYPE html>

<html lang="fr">

<head>

<meta charset="UTF-8">

<meta name="viewport"
content="width=device-width, initial-scale=1">

<title>Créer un compte | BudgetMaster</title>

<link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.7/dist/css/bootstrap.min.css" rel="stylesheet">

<link rel="stylesheet" href="../assets/css/register.css">

<link rel="preconnect" href="https://fonts.googleapis.com">

<link href="https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;500;600;700&display=swap" rel="stylesheet">

</head>

<body>

<div class="container">

<div class="register-box">

<h1>💖 BudgetMaster</h1>

<h2>Créer un compte</h2>

<?= $message ?>

<form method="POST">

<?= csrfField() ?>

<input
type="text"
name="firstname"
placeholder="Prénom"
value="<?= e($_POST["firstname"] ?? "") ?>"
required>

<input
type="text"
name="lastname"
placeholder="Nom"
value="<?= e($_POST["lastname"] ?? "") ?>"
required>

<input
type="email"
name="email"
placeholder="Adresse email"
value="<?= e($_POST["email"] ?? "") ?>"
required>

<input
type="tel"
name="phone"
placeholder="Téléphone"
value="<?= e($_POST["phone"] ?? "") ?>">

<select
name="user_type">

<?php foreach($userTypes as $value => $label){ ?>

<option value="<?= e($value) ?>" <?= ($_POST["user_type"] ?? "")==$value ? "selected" : "" ?>><?= $label ?></option>

<?php } ?>

</select>

<input
type="password"
id="password"
name="password"
placeholder="Mot de passe (8 caractères minimum)"
minlength="8"
required>

<input
type="password"
name="confirm_password"
placeholder="Confirmer le mot de passe"
minlength="8"
required>

<button type="submit">

Créer mon compte

</button>

</form>

<p class="login">

Déjà un compte ?

<a href="login.php">

Se connecter

</a>

</p>

</div>

</div>

<script src="../assets/js/register.js"></script>

</body>

</html>
