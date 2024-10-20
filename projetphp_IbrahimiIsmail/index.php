<?php
require_once 'config/config.php';

// Récupérer le contrôleur et l'action
$controller = $_GET['controller'] ?? 'task'; 
$action = $_GET['action'] ?? 'index'; 

// Nom du fichier du contrôleur
$controllerFile = 'controllers/' . ucfirst($controller) . 'Controller.php';

if (file_exists($controllerFile)) {
    require $controllerFile;
    $controllerClass = ucfirst($controller) . 'Controller';
    $controllerInstance = new $controllerClass();

    // Vérifier que l'action existe dans le contrôleur
    if (method_exists($controllerInstance, $action)) {
        $controllerInstance->$action();
    } else {
        echo "Action $action non trouvée !";
    }
} else {
    echo "Contrôleur $controller non trouvé !";
}
