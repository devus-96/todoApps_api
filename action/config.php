<?php 
require_once __DIR__ . '/../vendor/autoload.php';

define('DB_HOST', "localhost");
define('DB_USERNAME', "postgres");
define('DB_PASSWORD', "gummels1486325079");
define("DB_NAME", "appmanagebd");
define("DB_USER_TBL", "users");

//google api configuration

$secretGoogleKey = getenv("GOOGLE_CLIENT_SECRETE");

define("GOOGLE_CLIENT_ID", '560718008975-jsa232grcgd2irtp8280ntgh35rab7p2.apps.googleusercontent.com');
define("GOOGLE_CLIENT_SECRETE", "$secretGoogleKey");
define("GOOGLE_REDIRET_URL", "http://localhost:3000/auth/callback");

if (!session_id()) session_start();

//call Google api

$gClient = new Google\Client();
$gClient->setClientId(GOOGLE_CLIENT_ID);
$gClient -> setClientSecret(GOOGLE_CLIENT_SECRETE);
$gClient -> setRedirectUri(GOOGLE_REDIRET_URL);

$gClient->addScope(Google\Service\Oauth2::USERINFO_PROFILE); // Ajouter les scopes nécessaires
$gClient->addScope(Google\Service\Oauth2::USERINFO_EMAIL);
$gClient->addScope(Google\Service\Oauth2::OPENID)
?>