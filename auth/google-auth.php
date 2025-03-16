<?php 

require_once $_SERVER['DOCUMENT_ROOT'] . '/utils/config.php';
require_once $_SERVER['DOCUMENT_ROOT'] . '/utils/cors.php';
require_once $_SERVER['DOCUMENT_ROOT'] . '/controllers/authProviderController.php';
require $_SERVER['DOCUMENT_ROOT'] . '/vendor/autoload.php';

if (!session_id()) session_start();
cors(); // fix cors policy

$gClient = new Google\Client();
$gClient->setClientId(GOOGLE_CLIENT_ID);
$gClient -> setClientSecret(GOOGLE_CLIENT_SECRETE);
$gClient -> setRedirectUri(GOOGLE_REDIRET_URL);
// Ajouter les scopes nécessaires
$gClient->addScope(Google\Service\Oauth2::USERINFO_PROFILE);
$gClient->addScope(Google\Service\Oauth2::USERINFO_EMAIL);
$gClient->addScope(Google\Service\Oauth2::OPENID);


if (!isset($_GET['code'])) {
    if ($_SERVER['REQUEST_METHOD'] === 'GET') {
        $authUrl = $gClient->createAuthUrl();
        echo json_encode(['authUrl' => $authUrl]);
    }
} else {
    try {

        $token = $gClient->fetchAccessTokenWithAuthCode($_GET['code']);
        $gClient->setAccessToken($token);

        $oauth2 = new Google\Service\Oauth2($gClient);
        $userinfo = $oauth2->userinfo->get();

        $gUser = array();

        // Accéder aux informations utilisateur
        $gUser["email"] = $userinfo->getEmail();
        $gUser["firstname"] = $userinfo->getGivenName();
        $gUser["lastname"] = $userinfo->getFamilyName();
        $gUser['google_id'] = $userinfo->getId();
        $gUser['provider'] = 'google';

        $auth = new AUTH('');
        $auth->authProvider($gUser, 'google');

        echo json_encode([
            "id_token" => $token['id_token'],
            "expired" => $token['expires_in'],
            "userData" => [
                "firstname" => $gUser["firstname"],
                "lastname" => $gUser["lastname"]
            ]
        ]);

    } catch (Exception $e) {
        exit($e . 'Oh dear...');
    }
}