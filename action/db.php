<?php
$host = 'localhost';
$user = "postgres";
$dbname = "appmanagebd";
$password = "daus985220";

/*

ATTR_ERRMODE : Mode de rapport d'erreur de PDO. dans mon cas cela lève une exception PDOException.

ATTR_CASE : force les a respecter une casse spécifique. CASE_NATURE permet de garder les noms telle définie dans la BD.

ATTR_ORACLE_NULLS : Détermine si et comment nullles chaînes vides doivent être converties, les chaînes vide seront convertit en NULL.

*/

try {
    $pdo = new PDO("pgsql:host=$host;dbname=$dbname", $user, $password);

    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);  
                      $pdo->setAttribute(PDO::ATTR_ORACLE_NULLS,PDO::NULL_EMPTY_STRING);

$pdo->setAttribute(PDO::ATTR_CASE, PDO::CASE_NATURAL);

} catch (PDOException $e) {
    die("Error: " . $e->getMessage());
}

?>