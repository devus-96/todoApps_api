<?php 

require_once $_SERVER['DOCUMENT_ROOT'] . '/utils/config.php';
require_once $_SERVER['DOCUMENT_ROOT'] . '/utils/cors.php';
require_once $_SERVER['DOCUMENT_ROOT'] . '/controllers/authProviderController.php';
require $_SERVER['DOCUMENT_ROOT'] . '/vendor/autoload.php';

cors(); // fix cors policy

if (!isset($_GET['code'])) {
    // If we don't have an authorization code then get one
    $authUrl = $github_provider->getAuthorizationUrl();
    $state = $github_provider->getState();
    if ($authUrl) {
        header('HTTP/1.1 200 ok');
        echo json_encode(['authUrl' => $authUrl, 'state' => $state]);
    } else {
        exit("send url is not possible");
    }
} else {

    // Try to get an access token (using the authorization code grant)
    $token = $github_provider->getAccessToken('authorization_code', [
        'code' => $_GET['code']
    ]);
    // Optional: Now you have a token you can look up a users profile data
    try {
        // We got an access token, let's now get the user's details
        $user = $github_provider->getResourceOwner($token);
        $data = [
            "github_id" =>  $user->getId(),
            "firstname" => $user->getNickname(),
            "lastname" => $user->getName(),
            "email" => $user->getEmail() ,
            "provider" => 'github'
        ];
        $auth = new AUTH('');
        $auth->authProvider($data, 'github');
        // Use these details to create a new profile
        echo json_encode([
            "userData" => $data, 
            "token" => $token->getToken(), // Use this to interact with an API on the users behalf
            "expired" => $token->getExpires()
        ]);
    } catch (Exception $e) {
        // Failed to get user details
        exit('Oh dear...');
    }    
}