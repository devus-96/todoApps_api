<?php 
require_once __DIR__ . '/../vendor/autoload.php';

//google api configuration
define("GOOGLE_CLIENT_ID", '');
define("GOOGLE_CLIENT_SECRETE", "");
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