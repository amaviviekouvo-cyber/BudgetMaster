<?php

$current = basename(dirname($_SERVER['PHP_SELF']));

$menu = [
    "dashboard"     => ["fa-house", "Dashboard"],
    "transactions"  => ["fa-wallet", "Transactions"],
    "categories"    => ["fa-list", "Catégories"],
    "budgets"       => ["fa-chart-column", "Budgets"],
    "goals"         => ["fa-bullseye", "Objectifs"],
    "savings"       => ["fa-piggy-bank", "Épargne"],
    "investments"   => ["fa-chart-line", "Investissements"],
    "statistics"    => ["fa-chart-pie", "Statistiques"],
    "settings"      => ["fa-gear", "Paramètres"]
];

?>

<div class="sidebar">

    <div class="logo">

        💖 <span>BudgetMaster</span>

    </div>

    <nav class="menu">

        <?php foreach($menu as $folder => $item){ ?>

        <a href="../<?= $folder ?>/index.php" class="<?= $current==$folder ? "active" : "" ?>" title="<?= $item[1] ?>">
            <i class="fa-solid <?= $item[0] ?>"></i>
            <span><?= $item[1] ?></span>
        </a>

        <?php } ?>

    </nav>

    <div class="sidebar-bottom">

        <div class="profile-sidebar">

            <div class="avatar">

                <?= e(getInitials($_SESSION["firstname"] ?? "", $_SESSION["lastname"] ?? "")) ?>

            </div>

            <div>

                <strong><?= e($_SESSION["firstname"] ?? "") ?></strong><br>

                <small><?= e($_SESSION["user_type"] ?? "Membre") ?></small>

            </div>

        </div>

        <a href="../auth/logout.php" class="logout-btn" title="Déconnexion">

            <i class="fa-solid fa-right-from-bracket"></i>

            <span>Déconnexion</span>

        </a>

    </div>

</div>
