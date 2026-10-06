<?php

if(!function_exists("postAmount")){
    exit();
}

/*
 * Lecture et validation des champs d'une transaction (add.php / edit.php).
 * Remplit $transaction et $errors.
 */

$transaction = [
    "title" => trim($_POST["title"] ?? ""),
    "amount" => postAmount("amount"),
    "type" => $_POST["type"] ?? "",
    "category" => trim($_POST["category"] ?? ""),
    "transaction_date" => postDate("transaction_date"),
    "notes" => trim($_POST["notes"] ?? "")
];

$errors = [];

if($transaction["title"]=="" || mb_strlen($transaction["title"])>100){
    $errors[] = "Le titre est obligatoire (100 caractères maximum).";
}

if($transaction["amount"]===null){
    $errors[] = "Le montant doit être un nombre positif.";
}

if(!in_array($transaction["type"],["income","expense"],true)){
    $errors[] = "Type de transaction invalide.";
}

if($transaction["category"]=="" || mb_strlen($transaction["category"])>50){
    $errors[] = "Veuillez choisir une catégorie.";
}

if($transaction["transaction_date"]===null){
    $errors[] = "Date invalide.";
}
