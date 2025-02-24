<?php 
require_once __DIR__ . '/../vendor/autoload.php';

// Charger le fichier .env
$dotenv = Dotenv\Dotenv::createImmutable($_SERVER['DOCUMENT_ROOT'] . '/');
$dotenv->load();

//database config
define("HOST", $_ENV['DB_HOST']);
define("BD_USER", $_ENV['DB_USER']);
define("DB_NAME", $_ENV['DB_NAME']);
define("DB_PASS", $_ENV['DB_PASS']);

//JWT config
define("JWT_SECRET", $_ENV['JWT_SECRET']);

//google api configuration
define("GOOGLE_CLIENT_ID", $_ENV['GOOGLE_CLIENT_ID']);
define("GOOGLE_CLIENT_SECRETE", $_ENV['GOOGLE_CLIENT_SECRETE']);
define("GOOGLE_REDIRET_URL", $_ENV['GOOCLE_REDIRECT']);
?>