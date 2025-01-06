<?php 

require_once "../action/config.php";


if ($_SERVER['REQUEST_METHOD'] === 'GET') {
    $authUrl = $gClient->createAuthUrl();
    echo json_encode(['authUrl' => $authUrl]);
}


?>