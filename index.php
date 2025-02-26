<?php
require_once $_SERVER['DOCUMENT_ROOT'] . '/utils/cors.php';
require_once $_SERVER['DOCUMENT_ROOT'] . '/controllers/userController.php';
require_once $_SERVER['DOCUMENT_ROOT'] . '/controllers/taskController.php';
require_once $_SERVER['DOCUMENT_ROOT'] . '/controllers/calendarController.php';
require_once $_SERVER['DOCUMENT_ROOT'] . '/controllers/teamController.php';
require_once __DIR__ . '/vendor/autoload.php';

cors();
// afficher les erreurs en mode de devellopement
ini_set("display_error", 1);
error_reporting(E_ALL);
// faire en sorte que l'url n'envoie que le pathname et pas d'autre argument qui peuvent etre 
//possiblement present dans l'url comment le nom du serveur et le port

// Définir les routes
$routes = require_once 'routes/user.php';
$routes = array_merge(
        $routes, 
        require_once 'routes/task.php',
        require_once 'routes/calendar.php',
        require_once 'routes/team.php'
    );

// Récupérer la méthode et l'URI de la requête
$method = $_SERVER['REQUEST_METHOD'];
$uri = $_SERVER['REQUEST_URI'];

// Trouver la route correspondante
foreach ($routes as $route) {
    if ($route['method'] === $method && preg_match($route['pattern'], $uri, $matches)) {
        $params = [];
        // decouper le resultat
        $pathnameAndParams = explode('?', $matches[0]);
        if (count($pathnameAndParams) === 2) {
            // recuperer les parametres
            // Découper les paramètres en un tableau associatif 
            parse_str($pathnameAndParams[1], $params);
        }
        // Instancier le contrôleur et appeler la méthode
        $controller = new $route['controller']();
        call_user_func_array([$controller, $route['action']], $params);

        exit();
    }
}

// Gérer les erreurs 404
header('HTTP/1.0 404 Not Found');
echo 'routes not found';

?>

