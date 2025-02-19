<?php
require $_SERVER['DOCUMENT_ROOT'] . '/utils/cors.php';
require $_SERVER['DOCUMENT_ROOT'] . '/controllers/UserController.php';
require __DIR__ . '/vendor/autoload.php';

cors();
// afficher les erreurs en mode de devellopement
ini_set("display_error", 1);
error_reporting(E_ALL);
// faire en sorte que l'url n'envoie que le pathname et pas d'autre argument qui peuvent etre 
//possiblement present dans l'url comment le nom du serveur et le port

// Définir les routes
$routes = require_once 'routes/user.php';
//$routes = array_merge($routes);

// Récupérer la méthode et l'URI de la requête
$method = $_SERVER['REQUEST_METHOD'];
$uri = $_SERVER['REQUEST_URI'];

// Trouver la route correspondante
foreach ($routes as $route) {
    if ($route['method'] === $method && preg_match($route['pattern'], $uri, $matches)) {
        // Extraire les paramètres de l'URI
        $params = array_slice($matches, 1);

        // Instancier le contrôleur et appeler la méthode
        $controller = new $route['controller']();
        call_user_func_array([$controller, $route['action']], $params);

        echo "hello";

        exit();
    }
}

// Gérer les erreurs 404
header('HTTP/1.0 404 Not Found');
echo $uri;

?>

