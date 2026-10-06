<?php

require_once("../includes/bootstrap.php");

requireLogin();

require("filters.php");

$sql = $pdo->prepare("
    SELECT transaction_date,title,category,type,amount,notes
    FROM transactions
    WHERE $where
    ORDER BY transaction_date DESC, id DESC
");

$sql->execute($params);

header("Content-Type: text/csv; charset=utf-8");
header("Content-Disposition: attachment; filename=\"budgetmaster-transactions-".date("Y-m-d").".csv\"");

$out = fopen("php://output","w");

/* BOM UTF-8 pour un affichage correct des accents dans Excel */
fwrite($out,"\xEF\xBB\xBF");

fputcsv($out,["Date","Titre","Catégorie","Type","Montant","Notes"],";");

while($row = $sql->fetch()){

    fputcsv($out,[
        date("d/m/Y",strtotime($row["transaction_date"])),
        $row["title"],
        $row["category"],
        $row["type"]=="income" ? "Revenu" : "Dépense",
        number_format($row["type"]=="income" ? $row["amount"] : -$row["amount"],2,","," "),
        $row["notes"]
    ],";");

}

fclose($out);
