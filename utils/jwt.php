<?php

use Firebase\JWT\JWT;
use Firebase\JWT\Key;
use Firebase\JWT\ExpiredException;
use Firebase\JWT\SignatureInvalidException;
use Firebase\JWT\BeforeValidException;

require_once $_SERVER['DOCUMENT_ROOT'] . '/utils/config.php';

function generateJWT($userdata) {
    try {
        $issuedAt = new DateTimeImmutable();
        $expire = $issuedAt->modify("+24 hours")->getTimestamp();
        $serverName = "127.0.0.1:8001";

        $payload = [
            "iat" => $issuedAt->getTimestamp(),
            "iss" => $serverName,
            "nbf" => $issuedAt->getTimestamp(),
            "exp" => $expire,
            "user" => $userdata,
        ];

        $token = JWT::encode($payload, JWT_SECRET, "HS512");
        return $token;
    } catch (Exception $e) {
        // Gestion des erreurs (ex : journalisation)
        error_log("Erreur lors de la génération du JWT : " . $e->getMessage());
        return null;
    }
} 


function decodeJWT($token, $secretKey = JWT_SECRET) {
    try {
        // Décoder le token JWT
        $decoded = JWT::decode(trim($token), new Key($secretKey, 'HS512'));

        // Retourner les données décodées
        return $decoded;
    } catch (ExpiredException $e) {
        // Token expiré
        return "Erreur : The token has expired.";
    } catch (SignatureInvalidException $e) {
        // Signature invalide
        return "Erreur : Invalid token signature.";
    } catch (BeforeValidException $e) {
        // Token pas encore valide
        return "Erreur : The token is not yet valid.";
    } catch (Exception $e) {
        // Autres erreurs
        return "Erreur : Invalid token.";
    }
}

?>