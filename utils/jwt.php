<?php 

use Firebase\JWT\JWT;
use Firebase\JWT\Key;

function generateJWT ($userdata) {

    $issuedAt = new DateTimeImmutable();
    //la date d'expiration
    $expire = $issuedAt->modify('+6 minutes')->getTimestamp();
    $serverName = "127.0.0.1:8000";

    $tab = [
        "iat" => $issuedAt->getTimestamp(),
        "iss" => $serverName,
        "nbf" => $issuedAt->getTimestamp(),
        "exp" => $expire,
        "userEmail" => $userdata,
    ];

    $token = JWT::encode(
        $tab,
        '',
        "HS512"
    );

    return $token;

}

?>