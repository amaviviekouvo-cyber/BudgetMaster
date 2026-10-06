<?php

require_once("../includes/bootstrap.php");

requireLogin();

$user_id=$_SESSION["user_id"];

$month=(int)date("n");
$year=(int)date("Y");

/*==========================
SOLDE GLOBAL & MOIS EN COURS
==========================*/

$sql=$pdo->prepare("
SELECT
    COALESCE(SUM(CASE WHEN type='income' THEN amount ELSE -amount END),0) balance,
    COALESCE(SUM(CASE WHEN type='income' AND MONTH(transaction_date)=? AND YEAR(transaction_date)=? THEN amount END),0) month_income,
    COALESCE(SUM(CASE WHEN type='expense' AND MONTH(transaction_date)=? AND YEAR(transaction_date)=? THEN amount END),0) month_expense
FROM transactions
WHERE user_id=?
");

$sql->execute([$month,$year,$month,$year,$user_id]);

$totals=$sql->fetch();

/*==========================
EPARGNE
==========================*/

$sql=$pdo->prepare("
SELECT COALESCE(SUM(amount),0)
FROM savings
WHERE user_id=?
");

$sql->execute([$user_id]);

$totalSaving=$sql->fetchColumn();

/*==========================
BUDGET DU MOIS
==========================*/

$sql=$pdo->prepare("
SELECT amount
FROM budgets
WHERE user_id=? AND month=? AND year=?
LIMIT 1
");

$sql->execute([$user_id,$month,$year]);

$monthBudget=$sql->fetchColumn();

/*==========================
GRAPHIQUES
==========================*/

$monthly=monthlyTotals($pdo,$user_id,6);

$byCategory=expensesByCategory($pdo,$user_id,date("Y-m-01"));

/*==========================
OBJECTIFS EN COURS
==========================*/

$sql=$pdo->prepare("
SELECT *
FROM goals
WHERE user_id=? AND status='En cours'
ORDER BY deadline IS NULL, deadline
LIMIT 3
");

$sql->execute([$user_id]);

$goals=$sql->fetchAll();

/*==========================
DERNIERES TRANSACTIONS
==========================*/

$sql=$pdo->prepare("
SELECT *
FROM transactions
WHERE user_id=?
ORDER BY transaction_date DESC, id DESC
LIMIT 5
");

$sql->execute([$user_id]);

$transactions=$sql->fetchAll();

$pageTitle="Dashboard";

include("../includes/header.php");
include("../includes/sidebar.php");

?>

<div class="content">

<?php include("../includes/navbar.php"); ?>

<div class="cards mb-4">

<a href="../transactions/index.php" class="dashboard-link">

<div class="dashboard-card pink">

<div class="icon">💰</div>

<div>

<h5>Solde</h5>

<h2><?= money($totals["balance"]) ?></h2>

</div>

</div>

</a>

<a href="../transactions/index.php?type=income&month=<?= date("Y-m") ?>" class="dashboard-link">

<div class="dashboard-card blue">

<div class="icon">📈</div>

<div>

<h5>Revenus du mois</h5>

<h2><?= money($totals["month_income"]) ?></h2>

</div>

</div>

</a>

<a href="../transactions/index.php?type=expense&month=<?= date("Y-m") ?>" class="dashboard-link">

<div class="dashboard-card red">

<div class="icon">📉</div>

<div>

<h5>Dépenses du mois</h5>

<h2><?= money($totals["month_expense"]) ?></h2>

</div>

</div>

</a>

<a href="../savings/index.php" class="dashboard-link">

<div class="dashboard-card purple">

<div class="icon">🐷</div>

<div>

<h5>Épargne</h5>

<h2><?= money($totalSaving) ?></h2>

</div>

</div>

</a>

</div>

<!-- ===========================
     BUDGET & OBJECTIFS
=========================== -->

<div class="row">

<div class="col-lg-6 mb-4">

<div class="card h-100">

<h3>💰 Budget — <?= monthName($month) ?></h3>

<?php if($monthBudget!==false){

$progress=$monthBudget>0 ? min(100,$totals["month_expense"]/$monthBudget*100) : 0;

$remaining=$monthBudget-$totals["month_expense"];

?>

<div class="progress-label">

<span><?= money($totals["month_expense"]) ?> dépensés</span>

<span>sur <?= money($monthBudget) ?></span>

</div>

<div class="progress mb-3" style="height:20px;">

<div class="progress-bar <?= $progress>=100 ? "bg-danger" : ($progress>=80 ? "bg-warning" : "bg-success") ?>" style="width:<?= round($progress,2) ?>%">

<?= round($progress) ?> %

</div>

</div>

<p class="<?= $remaining<0 ? "text-danger" : "text-success" ?>">

<?= $remaining<0 ? "⚠ Budget dépassé de ".money(-$remaining) : "✔ Reste à vivre : ".money($remaining) ?>

</p>

<?php }else{ ?>

<p class="text-muted">Aucun budget défini pour ce mois.</p>

<a href="../budgets/add.php" class="btn btn-primary">Définir un budget</a>

<?php } ?>

</div>

</div>

<div class="col-lg-6 mb-4">

<div class="card h-100">

<h3>🎯 Objectifs en cours</h3>

<?php if(count($goals)==0){ ?>

<p class="text-muted">Aucun objectif en cours.</p>

<a href="../goals/add.php" class="btn btn-primary">Créer un objectif</a>

<?php } ?>

<?php foreach($goals as $goal){

$progress=$goal["target_amount"]>0 ? min(100,$goal["current_amount"]/$goal["target_amount"]*100) : 0;

?>

<div class="mb-3">

<div class="progress-label">

<strong><?= e($goal["title"]) ?></strong>

<span><?= money($goal["current_amount"]) ?> / <?= money($goal["target_amount"]) ?></span>

</div>

<div class="progress" style="height:12px;">

<div class="progress-bar bg-success" style="width:<?= round($progress,2) ?>%"></div>

</div>

</div>

<?php } ?>

</div>

</div>

</div>

<!-- ===========================
     GRAPHIQUES
=========================== -->

<div class="row">

<div class="col-lg-7 mb-4">

<div class="card h-100">

    <h3>📊 Revenus vs Dépenses (6 mois)</h3>

    <canvas id="budgetChart" height="160"></canvas>

</div>

</div>

<div class="col-lg-5 mb-4">

<div class="card h-100">

    <h3>🥧 Dépenses du mois par catégorie</h3>

    <?php if(count($byCategory)>0){ ?>

    <canvas id="pieChart"></canvas>

    <?php }else{ ?>

    <p class="text-muted">Aucune dépense ce mois-ci.</p>

    <?php } ?>

</div>

</div>

</div>

<!-- ===========================
     DERNIÈRES TRANSACTIONS
=========================== -->

<div class="card">

    <div class="d-flex justify-content-between align-items-center mb-3">

        <h3>📝 Dernières transactions</h3>

        <a href="../transactions/index.php" class="btn btn-primary">
            Voir tout
        </a>

    </div>

    <div class="table-responsive">

    <table class="table">

        <thead>

            <tr>

                <th>Date</th>

                <th>Titre</th>

                <th>Catégorie</th>

                <th>Type</th>

                <th>Montant</th>

            </tr>

        </thead>

        <tbody>

        <?php if(count($transactions)>0){ ?>

            <?php foreach($transactions as $t){ ?>

            <tr>

                <td><?= date("d/m/Y",strtotime($t["transaction_date"])) ?></td>

                <td><?= e($t["title"]) ?></td>

                <td><?= e($t["category"]) ?></td>

                <td>

                    <?php if($t["type"]=="income"){ ?>

                        <span class="badge bg-success">Revenu</span>

                    <?php }else{ ?>

                        <span class="badge bg-danger">Dépense</span>

                    <?php } ?>

                </td>

                <td>

                    <?php if($t["type"]=="income"){ ?>

                        <span class="text-success fw-bold">+<?= money($t["amount"]) ?></span>

                    <?php }else{ ?>

                        <span class="text-danger fw-bold">-<?= money($t["amount"]) ?></span>

                    <?php } ?>

                </td>

            </tr>

            <?php } ?>

        <?php }else{ ?>

            <tr>

                <td colspan="5" class="text-center">

                    Aucune transaction enregistrée.
                    <a href="../transactions/add.php">Ajouter la première</a>

                </td>

            </tr>

        <?php } ?>

        </tbody>

    </table>

    </div>

</div>

<script>

const palette=["#FF5FA2","#5B8DEF","#8A6BFF","#55D6AA","#FFC857","#FF6B6B","#6DD5ED","#7BD389","#F4A261","#A78BFA"];

/* ==================== Barres ==================== */

new Chart(document.getElementById("budgetChart"),{

    type:"bar",

    data:{

        labels:<?= json_encode($monthly["labels"]) ?>,

        datasets:[
            {
                label:"Revenus",
                data:<?= json_encode($monthly["income"]) ?>,
                backgroundColor:"#5B8DEF",
                borderRadius:8
            },
            {
                label:"Dépenses",
                data:<?= json_encode($monthly["expense"]) ?>,
                backgroundColor:"#FF5FA2",
                borderRadius:8
            }
        ]

    },

    options:{

        responsive:true,

        plugins:{
            legend:{position:"bottom"}
        },

        scales:{
            y:{beginAtZero:true}
        }

    }

});

/* ==================== Camembert ==================== */

const pie=document.getElementById("pieChart");

if(pie){

    new Chart(pie,{

        type:"doughnut",

        data:{

            labels:<?= json_encode(array_column($byCategory,"category")) ?>,

            datasets:[{
                data:<?= json_encode(array_map("floatval",array_column($byCategory,"total"))) ?>,
                backgroundColor:palette,
                borderWidth:0
            }]

        },

        options:{

            responsive:true,

            plugins:{
                legend:{position:"bottom"}
            }

        }

    });

}

</script>

<?php include("../includes/footer.php"); ?>
