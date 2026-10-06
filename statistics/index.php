<?php

require_once("../includes/bootstrap.php");

requireLogin();

$user_id=$_SESSION["user_id"];

/* Période analysée : 6 ou 12 derniers mois */

$period=(int)($_GET["period"] ?? 12)==6 ? 6 : 12;

$monthly=monthlyTotals($pdo,$user_id,$period);

$since=date("Y-m-01",strtotime("-".($period-1)." months",strtotime(date("Y-m-01"))));

$byCategory=expensesByCategory($pdo,$user_id,$since);

$totalIncome=array_sum($monthly["income"]);
$totalExpense=array_sum($monthly["expense"]);

$savingsRate=$totalIncome>0 ? ($totalIncome-$totalExpense)/$totalIncome*100 : 0;

/* Solde cumulé mois par mois */

$cumulative=[];
$running=0;

foreach($monthly["income"] as $i => $income){
    $running+=$income-$monthly["expense"][$i];
    $cumulative[]=round($running,2);
}

$pageTitle="Statistiques";

include("../includes/header.php");
include("../includes/sidebar.php");

?>

<div class="content">

<?php include("../includes/navbar.php"); ?>

<div class="d-flex justify-content-between align-items-center mb-4">

<h2>📊 Mes Statistiques</h2>

<div class="btn-group">

<a href="?period=6" class="btn <?= $period==6 ? "btn-primary" : "btn-secondary" ?>">6 mois</a>

<a href="?period=12" class="btn <?= $period==12 ? "btn-primary" : "btn-secondary" ?>">12 mois</a>

</div>

</div>

<div class="row mb-2">

<div class="col-md-3 mb-3">

<div class="card p-3 h-100">

<small class="text-muted">Revenus (<?= $period ?> mois)</small>

<strong class="fs-5 text-success"><?= money($totalIncome) ?></strong>

</div>

</div>

<div class="col-md-3 mb-3">

<div class="card p-3 h-100">

<small class="text-muted">Dépenses (<?= $period ?> mois)</small>

<strong class="fs-5 text-danger"><?= money($totalExpense) ?></strong>

</div>

</div>

<div class="col-md-3 mb-3">

<div class="card p-3 h-100">

<small class="text-muted">Dépense moyenne / mois</small>

<strong class="fs-5"><?= money($totalExpense/$period) ?></strong>

</div>

</div>

<div class="col-md-3 mb-3">

<div class="card p-3 h-100">

<small class="text-muted">Taux d'épargne</small>

<strong class="fs-5 <?= $savingsRate>=0 ? "text-success" : "text-danger" ?>"><?= number_format($savingsRate,1,","," ") ?> %</strong>

</div>

</div>

</div>

<div class="row">

<div class="col-lg-6 mb-4">

<div class="card p-4 h-100">

<h4>📈 Revenus et dépenses mensuels</h4>

<canvas id="monthlyChart"></canvas>

</div>

</div>

<div class="col-lg-6 mb-4">

<div class="card p-4 h-100">

<h4>💹 Évolution du solde</h4>

<canvas id="balanceChart"></canvas>

</div>

</div>

</div>

<div class="row">

<div class="col-lg-6 mb-4">

<div class="card p-4 h-100">

<h4>🥧 Dépenses par catégorie</h4>

<?php if(count($byCategory)>0){ ?>

<canvas id="categoryChart"></canvas>

<?php }else{ ?>

<p class="text-muted">Aucune dépense sur la période.</p>

<?php } ?>

</div>

</div>

<div class="col-lg-6 mb-4">

<div class="card p-4 h-100">

<h4>🏆 Top des catégories</h4>

<?php foreach($byCategory as $cat){

$share=$totalExpense>0 ? $cat["total"]/$totalExpense*100 : 0;

?>

<div class="mb-3">

<div class="progress-label">

<span><?= e($cat["category"]) ?></span>

<span><?= money($cat["total"]) ?> · <?= round($share) ?> %</span>

</div>

<div class="progress" style="height:10px;">

<div class="progress-bar" style="width:<?= round($share,2) ?>%;background:#FF5FA2"></div>

</div>

</div>

<?php } ?>

<?php if(count($byCategory)==0){ ?>

<p class="text-muted">Aucune donnée.</p>

<?php } ?>

</div>

</div>

</div>

<script>

const palette=["#FF5FA2","#5B8DEF","#8A6BFF","#55D6AA","#FFC857","#FF6B6B","#6DD5ED","#A66CFF","#00C9A7","#F9A826"];

const labels=<?= json_encode($monthly["labels"]) ?>;

// ==================== Revenus / dépenses ====================

new Chart(document.getElementById("monthlyChart"),{

type:"bar",

data:{

labels:labels,

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
plugins:{legend:{position:"bottom"}},
scales:{y:{beginAtZero:true}}
}

});

// ==================== Solde cumulé ====================

new Chart(document.getElementById("balanceChart"),{

type:"line",

data:{

labels:labels,

datasets:[{
label:"Solde cumulé",
data:<?= json_encode($cumulative) ?>,
borderColor:"#8A6BFF",
backgroundColor:"rgba(138,107,255,0.15)",
fill:true,
tension:0.4
}]

},

options:{
responsive:true,
plugins:{legend:{display:false}}
}

});

// ==================== Catégories ====================

const category=document.getElementById("categoryChart");

if(category){

new Chart(category,{

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
plugins:{legend:{position:"bottom"}}
}

});

}

</script>

<?php include("../includes/footer.php"); ?>
