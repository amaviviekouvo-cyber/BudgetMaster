<?php

/*
 * Connexion à la base de données.
 * Les valeurs peuvent être surchargées par des variables d'environnement
 * (utile en production / Docker). Par défaut : configuration XAMPP locale.
 */

$host = getenv("DB_HOST") ?: "localhost";
$port = getenv("DB_PORT") ?: "3306";
$dbname = getenv("DB_NAME") ?: "budgetmaster";
$username = getenv("DB_USER") ?: "root";
$password = getenv("DB_PASS") !== false ? getenv("DB_PASS") : "";

try {

    $pdo = new PDO(
        "mysql:host=$host;port=$port;dbname=$dbname;charset=utf8mb4",
        $username,
        $password,
        [
            PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
            PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
            PDO::ATTR_EMULATE_PREPARES => false
        ]
    );

} catch (PDOException $e) {

    error_log("BudgetMaster - connexion BDD : " . $e->getMessage());

    http_response_code(500);

    die("Impossible de se connecter à la base de données. Réessayez plus tard.");

}
