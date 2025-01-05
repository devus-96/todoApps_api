<?php
require "action/cors.php";

require __DIR__ . '/vendor/autoload.php';

cors();
// afficher les erreurs en mode de devellopement
ini_set("display_error", 1);
error_reporting(E_ALL);
// faire en sorte que l'url n'envoie que le pathname et pas d'autre argument qui peuvent etre 
//possiblement present dans l'url comment le nom du serveur et le port

$dotenv = DotenvVault\DotenvVault::createUnsafeImmutable(__DIR__);
$dotenv->safeLoad();

function pathFile ($url, $method, $fileName) {
    $uri = parse_url($_SERVER['REQUEST_URI'], PHP_URL_PATH);
    if ($uri === $url && $_SERVER['REQUEST_METHOD'] === $method) {
        require $fileName;
    }
}

//route for users
pathFile('/register/user', "POST", 'auth/create_user.php');
pathFile('/login/user', "POST", 'auth/login_user.php');

?>
