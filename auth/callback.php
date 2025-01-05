<?php 

require_once "../action/config.php";
require_once "../action/auth_google.php";
require '../action/jwt.php';
require "action/cors.php";

cors();


if (isset($_GET['code'])) {
    $token = $gClient->fetchAccessTokenWithAuthCode($_GET['code']);
    $gClient->setAccessToken($token);

    $oauth2 = new Google\Service\Oauth2($gClient);
    $userinfo = $oauth2->userinfo->get();

    $gUser = array();
    $user = new User();

    // Accéder aux informations utilisateur
    $gUser["email"] = $userinfo->getEmail();
    $gUser["firstname"] = $userinfo->getGivenName();
    $gUser["lastname"] = $userinfo->getFamilyName();
    $gUser['id'] = $userinfo->getId();
    $gUser['oauth_provider'] = 'google';

    $userData = $user -> checkUser($gUser);
    $_SESSION["userData"] = $userData;

    $token = generateJWT($gUser["email"]);
    header("HTTP/1.1 200 OK");
    echo $token;
}

?>