<?php

require_once $_SERVER['DOCUMENT_ROOT'] . "/utils/google.php";

/*$_SERVER est un tableau contenant des informations telles que les en-têtes, les chemins et les emplacements des scripts.
ici il sert a savoir si la méthode de récupération est GET*/

/*il oui il utilise la createAuthUrl() pour créer l'URL de redirection vers la page Google d'authentification*/

/* json_encode renvoie soit false soit un string représentant le json de la valeur entrée ici il renvoie '{authUrl : value}' au front grace au echo*/

if ($_SERVER['REQUEST_METHOD'] === 'GET') {
    $authUrl = $gClient->createAuthUrl();
    echo json_encode(['authUrl' => $authUrl]);
}


?>