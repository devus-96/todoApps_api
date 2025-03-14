<?php 

require_once $_SERVER['DOCUMENT_ROOT'] . '/utils/config.php';
require_once $_SERVER['DOCUMENT_ROOT'] . '/utils/cors.php';
require_once $_SERVER['DOCUMENT_ROOT'] . '/controllers/authProviderController.php';
require $_SERVER['DOCUMENT_ROOT'] . '/vendor/autoload.php';

session_start();
cors(); // fix cors policy


if (!empty($_GET['error'])) {
    // Got an error, probably user denied access
    exit('Got error: ' . htmlspecialchars($_GET['error'], ENT_QUOTES, 'UTF-8'));
} else if (empty($_GET['code'])) {
    // If we don't have an authorization code then get one
    $authUrl = $google_provider->getAuthorizationUrl();
    $state = $google_provider->getState();

    if ($authUrl) {
        header('HTTP/1.1 200 ok');
        echo json_encode(['authUrl' => $authUrl, 'state' => $state]);
    } else {
        exit("send url is not possible");
    }
} else {
    // Try to get an access token (using the authorization code grant)
    $token = $google_provider->getAccessToken('authorization_code', [
        'code' => $_GET['code']
    ]);

    // Optional: Now you have a token you can look up a users profile data
    try {

        // We got an access token, let's now get the owner details
        $ownerDetails = $google_provider->getResourceOwner($token);

        $data = [
            "firstname" => $user->getFirstName(),
            "lastname" => $user->getLastName() ,
            "email" => $user->getEmail() ,
            "provider" => 'google'
        ];

        $auth = new AUTH('');
        $auth->authProvider($data);

        // Use these details to create a new profile
        echo json_encode([
            "userData" => $data, 
            "token" => $token->getToken(),
            "expired" => $token->getExpires() // Unix timestamp at which the access token expires
        ]);

    } catch (Exception $e) {

        // Failed to get user details
        exit('Something went wrong: ' . $e->getMessage());

    }


    // Use this to get a new access token if the old one expires
    echo $token->getRefreshToken();
}

?>