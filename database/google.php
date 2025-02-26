<?php 
declare(strict_types = 1);


require_once $_SERVER['DOCUMENT_ROOT'] . "/utils/google.php";
require_once $_SERVER['DOCUMENT_ROOT'] . "/vendor/autoload.php";



$token = $gClient->fetchAccessTokenWithAuthCode($_GET['code']);
$gClient->setAccessToken($token);

$oauth2 = new Google\Service\Oauth2($gClient);
$userinfo = $oauth2->userinfo->get();

$gUser = array();

// Accéder aux informations utilisateur
$gUser["email"] = $userinfo->getEmail();
$gUser["firstname"] = $userinfo->getGivenName();
$gUser["lastname"] = $userinfo->getFamilyName();
$gUser['id'] = $userinfo->getId();
$gUser['provider'] = 'google';

?>