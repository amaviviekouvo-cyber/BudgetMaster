<?php

require_once("includes/bootstrap.php");

$loggedIn = isLoggedIn();

?>
<!DOCTYPE html>
<html lang="fr">

<head>

    <meta charset="UTF-8">

    <meta name="viewport" content="width=device-width, initial-scale=1.0">

    <meta name="description" content="BudgetMaster : suivez vos revenus, dépenses, budgets, épargne et investissements simplement.">

    <title>BudgetMaster — Gère ton argent, réalise tes rêves</title>

    <!-- Bootstrap -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.7/dist/css/bootstrap.min.css" rel="stylesheet">

    <!-- Font Awesome -->
    <link rel="stylesheet"
        href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.7.2/css/all.min.css">

    <!-- Google Font -->
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;500;600;700;800&display=swap"
        rel="stylesheet">

    <link rel="stylesheet" href="assets/css/home.css">

</head>

<body>

    <!-- ================= NAVBAR ================= -->

    <nav class="navbar navbar-expand-lg navbar-dark">

        <div class="container">

            <a class="navbar-brand logo" href="#accueil">

                💖 BudgetMaster

            </a>

            <button class="navbar-toggler" type="button" data-bs-toggle="collapse"
                data-bs-target="#menu" aria-label="Menu">

                <span class="navbar-toggler-icon"></span>

            </button>

            <div class="collapse navbar-collapse" id="menu">

                <ul class="navbar-nav ms-auto align-items-lg-center">

                    <li class="nav-item"><a class="nav-link" href="#accueil">Accueil</a></li>

                    <li class="nav-item"><a class="nav-link" href="#fonctionnalites">Fonctionnalités</a></li>

                    <li class="nav-item"><a class="nav-link" href="#contact">Contact</a></li>

                    <li class="nav-item ms-lg-3">

                        <?php if($loggedIn){ ?>

                        <a class="btn btn-pink btn-sm" href="dashboard/index.php">Mon tableau de bord</a>

                        <?php }else{ ?>

                        <a class="btn btn-pink btn-sm" href="auth/login.php">Se connecter</a>

                        <?php } ?>

                    </li>

                </ul>

            </div>

        </div>

    </nav>

    <!-- ================= HERO ================= -->

    <section class="hero" id="accueil">

        <div class="container">

            <div class="row align-items-center g-5">

                <div class="col-lg-7">

                    <h1>

                        Gère ton argent,

                        <span>réalise tes rêves ✨</span>

                    </h1>

                    <p>

                        BudgetMaster t'aide à suivre tes revenus, tes dépenses,
                        ton épargne, tes investissements et tous tes objectifs financiers.

                    </p>

                    <?php if($loggedIn){ ?>

                    <a href="dashboard/index.php" class="btn btn-pink">

                        Accéder à mon espace

                    </a>

                    <?php }else{ ?>

                    <a href="auth/register.php" class="btn btn-pink">

                        Créer un compte

                    </a>

                    <a href="auth/login.php" class="btn btn-blue">

                        Se connecter

                    </a>

                    <?php } ?>

                </div>

                <div class="col-lg-5 text-center">

                    <div class="hero-card">

                        <div class="vivi-photo" role="img" aria-label="Photo de la développeuse"></div>

                        <p class="mb-0 mt-3">

                            <strong>EKOUVO Vivi</strong><br>

                            <small>Créatrice de BudgetMaster</small>

                        </p>

                    </div>

                </div>

            </div>

        </div>

    </section>

    <!-- ================= FEATURES ================= -->

    <section class="features" id="fonctionnalites">

        <div class="container">

            <h2 class="section-title">

                Pourquoi choisir BudgetMaster ?

            </h2>

            <p class="section-subtitle">

                Une application pensée pour gérer ton argent simplement.

            </p>

            <div class="row g-4 mt-4">

                <?php

                $features = [
                    ["fa-wallet", "Budget", "Fixe un budget mensuel et sois alertée avant de le dépasser."],
                    ["fa-chart-line", "Statistiques", "Analyse tes habitudes sur 6 ou 12 mois en un coup d'œil."],
                    ["fa-piggy-bank", "Épargne", "Crée des objectifs et suis ta progression versement après versement."],
                    ["fa-shield-heart", "Sécurité", "Mots de passe chiffrés et données protégées."]
                ];

                foreach($features as $feature){

                ?>

                <div class="col-md-6 col-lg-3">

                    <div class="feature-card">

                        <i class="fa-solid <?= $feature[0] ?>"></i>

                        <h3><?= $feature[1] ?></h3>

                        <p><?= $feature[2] ?></p>

                    </div>

                </div>

                <?php } ?>

            </div>

        </div>

    </section>

    <!-- ================= FOOTER ================= -->

    <footer id="contact">

        <p class="mb-1">© <?= date("Y") ?> BudgetMaster</p>

        <p class="mb-0">Designed & Developed by <strong>EKOUVO Vivi</strong> 💖</p>

    </footer>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.7/dist/js/bootstrap.bundle.min.js"></script>

</body>

</html>
