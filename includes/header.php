<?php

require_once(__DIR__."/bootstrap.php");

/* Thème & devise mémorisés en session (chargés une seule fois) */

if(isLoggedIn() && !isset($_SESSION["theme"])){

    $sql=$pdo->prepare("
    SELECT theme,currency
    FROM settings
    WHERE user_id=?
    LIMIT 1
    ");

    $sql->execute([$_SESSION["user_id"]]);

    $userSettings=$sql->fetch();

    $_SESSION["theme"]=$userSettings["theme"] ?? "light";
    $_SESSION["currency"]=$userSettings["currency"] ?? "€";

}

$theme=$_SESSION["theme"] ?? "light";

?>
<!DOCTYPE html>
<html lang="fr">

<head>

    <meta charset="UTF-8">

    <meta name="viewport" content="width=device-width, initial-scale=1.0">

    <title><?= isset($pageTitle) ? e($pageTitle)." | " : "" ?>BudgetMaster</title>

    <!-- Bootstrap -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.7/dist/css/bootstrap.min.css" rel="stylesheet">

    <!-- Font Awesome -->
    <link rel="stylesheet"
    href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.7.2/css/all.min.css">

    <!-- Google Font -->
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;500;600;700&display=swap"
    rel="stylesheet">

    <!-- CSS -->
    <link rel="stylesheet" href="../assets/css/dashboard.css">

    <!-- Chart.js doit être chargé avant les scripts des pages -->
    <script src="https://cdn.jsdelivr.net/npm/chart.js@4.4.4/dist/chart.umd.min.js"></script>

</head>

<body class="<?= $theme=="dark" ? "dark-mode" : "" ?>">
