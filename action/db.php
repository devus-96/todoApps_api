<?php
$host = 'localhost';
$user = "postgres";
$dbname = "appmanagebd";
$password = "gummels1486325079";

try {
    $pdo = new PDO("pgsql:host=$host;dbname=$dbname", $user, $password);
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
} catch (PDOException $e) {
    die("Error: " . $e->getMessage());
}

?>