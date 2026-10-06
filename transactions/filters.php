<?php

if(!function_exists("postAmount")){
    exit();
}

/*
 * Filtres communs à index.php et export.php.
 * Produit $where, $params et $filters.
 */

$filters = [
    "search" => trim($_GET["search"] ?? ""),
    "type" => in_array($_GET["type"] ?? "",["income","expense"],true) ? $_GET["type"] : "",
    "month" => preg_match('/^\d{4}-\d{2}$/',$_GET["month"] ?? "") ? $_GET["month"] : ""
];

$where = "user_id=?";
$params = [$_SESSION["user_id"]];

if($filters["search"]!=""){
    $where .= " AND (title LIKE ? OR category LIKE ? OR notes LIKE ?)";
    $like = "%".$filters["search"]."%";
    array_push($params,$like,$like,$like);
}

if($filters["type"]!=""){
    $where .= " AND type=?";
    $params[] = $filters["type"];
}

if($filters["month"]!=""){
    $where .= " AND DATE_FORMAT(transaction_date,'%Y-%m')=?";
    $params[] = $filters["month"];
}
