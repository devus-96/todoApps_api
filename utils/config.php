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
define("GOOGLE_REDIRET_URL", $_ENV['GOOGLE_REDIRECT']);

//github api configuration
define("GITHUB_CLIENT_ID", $_ENV['GITHUB_CLIENT_ID']);
define("GITHUB_CLIENT_SECRETE", $_ENV['GITHUB_CLIENT_SECRETE']);
define("GITHUB_REDIRET_URL", $_ENV['GOOGLE_REDIRECT']);

//auth 2.0 client config
$github_provider = new League\OAuth2\Client\Provider\Github([
    'clientId'          => GITHUB_CLIENT_ID,
    'clientSecret'      => GITHUB_CLIENT_SECRETE,
    'redirectUri'       => GOOGLE_REDIRET_URL,
]);

$google_provider = new League\OAuth2\Client\Provider\Google([
    'clientId'     => GOOGLE_CLIENT_ID,
    'clientSecret' => GOOGLE_CLIENT_SECRETE,
    'redirectUri'  => GOOGLE_REDIRET_URL,
]);
?>