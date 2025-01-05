<?php 

use Firebase\JWT\JWT;
use Firebase\JWT\Key;

function generateJWT ($userdata) {
    //la cles secrete qui va permettre d'encoder et de decoder le cles le moment venu
    $secretKey = getenv("SESSION_SECRET");

    $issuedAt = new DateTimeImmutable();
    //la date d'expiration
    $expire = $issuedAt->modify('+6 minutes')->getTimestamp();
    $serverName = "127.0.0.1:8000";
    $username = $userdata;

    $tab = [
        "iat" => $issuedAt->getTimestamp(),
        "iss" => $serverName,
        "nbf" => $issuedAt->getTimestamp(),
        "exp" => $expire,
        "userEmail" => $userdata,
    ];

    $token = JWT::encode(
        $tab,
        $secretKey,
        "HS512"
    );

    return $token;

}

?>