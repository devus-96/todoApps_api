<?php

require_once "../action/config.php";

/*$_SERVER est un tableau contenant des informations telles que les en-têtes, les chemins et les emplacements des scripts.
ici il sert a savoir si la méthode de récupération est GET*/

/*il oui il utilise la createAuthUrl() pour créer l'URL de redirection vers la page Google d'authentification*/

if ($_SERVER['REQUEST_METHOD'] === 'GET') {
    $authUrl = $gClient->createAuthUrl();
    echo json_encode(['authUrl' => $authUrl]);
}


?>