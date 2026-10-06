<?php

/* Nombre de notifications non lues */

$sql=$pdo->prepare("
SELECT COUNT(*)
FROM notifications
WHERE user_id=?
AND is_read=0
");

$sql->execute([$_SESSION["user_id"]]);

$nbNotifications=$sql->fetchColumn();

?>

<div class="topbar">

    <div class="topbar-left">

        <h2>

            Bonjour,
            <?= e($_SESSION["firstname"] ?? "") ?>
            👋

        </h2>

        <p>Bienvenue sur BudgetMaster</p>

    </div>

    <div class="topbar-right">

        <a href="../notifications/index.php" class="notification-btn position-relative" title="Notifications">

            <i class="fa-solid fa-bell"></i>

            <?php if($nbNotifications>0){ ?>

            <span class="position-absolute top-0 start-100 translate-middle badge rounded-pill bg-danger">

                <?= $nbNotifications ?>

            </span>

            <?php } ?>

        </a>

        <a href="../settings/index.php" class="notification-btn" title="Paramètres">

            <i class="fa-solid fa-gear"></i>

        </a>

    </div>

</div>

<?= showFlash() ?>
