<?php

use Firebase\JWT\JWT;
use Firebase\JWT\Key;
use Firebase\JWT\ExpiredException;
use Firebase\JWT\SignatureInvalidException;
use Firebase\JWT\BeforeValidException;

require_once __DIR__ . '/../vendor/autoload.php';
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


function decodeJWT($token, $provider) {
    try { 
        if ($provider === 'google') {
            $client = new Google_Client(['client_id' => GOOGLE_CLIENT_ID]);
            $payload = $client->verifyIdToken(trim($token));
            try{
                if ($payload) {
                    $userid = $payload['email'];
                    echo $userid;
                    return $payload;
                    // If the request specified a Google Workspace domain
                    //$domain = $payload['hd'];
                  } else {
                    // Invalid ID token
                    echo "le jeton n'est pas valide";
                  }
            } catch(Exception $e) {
               echo $e;
            }
        } else if ($provider === 'github') {
            // configuration de la requette
            $context = stream_context_create([
                "http" => [
                    "header" => "Authorization: Bearer $token\r\n" .
                    "User-Agent: TodoApps",
                    "timeout" => 10 // Timeout de 10 secondes
                ]
            ]);
            //on poste les donnees 
            $response = file_get_contents('https://api.github.com/user', false, $context);
            if ($response === false) {
                // Récupérer les détails de l'erreur
                $error = error_get_last();
                echo "Erreur lors de la requête : " . $error['message'];
            } else {
                // Décoder la réponse JSON
                $userInfo = json_decode($response, true);
            
                if (json_last_error() === JSON_ERROR_NONE) {
                    // Afficher les informations de l'utilisateur
                    return $userInfo;
                } else {
                    echo "Erreur lors du décodage de la réponse JSON : " . json_last_error_msg();
                }
            }
        } else {
            // Décoder le token JWT
            $decoded = JWT::decode(trim($token), new Key(JWT_SECRET, 'HS512'));
            
            // Retourner les données décodées
            return $decoded['user'];
        }
    } catch (Exception $e) {
        try {
            $decoded = JWT::decode($token, new Key('GITHUB_CLIENT_SECRETE', 'RS256'));

            return $decoded;
            
        } catch (Exception $e) {
            try {
                // Décoder le token JWT
                $decoded = JWT::decode(trim($token), new Key(JWT_SECRET, 'HS512'));
        
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
                return "Erreur : Invalid token." ;
            }

        }
    }
}

?>