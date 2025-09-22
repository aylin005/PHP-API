<?php
declare(strict_types=1); //forzar tipos estrictos

//carga automaticamente las clases que se usan en el proyecto
spl_autoload_register(function ($class) { 
    require __DIR__ . "/src/$class.php";
});

//maneja las excepciones
set_error_handler("ErrorHandler::handleError");
set_exception_handler("ErrorHandler::handleException"); 

//indica que la respuesta sera en formato json
header("Content-Type: application/json; charset=UTF-8");

/////////////////////////////////////////////////////////////////////////
//routing
$parts = explode("/", $_SERVER["REQUEST_URI"]);

if ($parts[2] != "products") {
    http_response_code(404);
    exit;
}
$id = $parts[3] ?? null;

/////////////////////////////////////////////////////////////////////////
//database connection
$database = new Database("localhost","phpprueba","root","rootBD","3306");
//$database->getConnection();

$gateway = new ProductGateway($database);

//main controller
//$controller = new ProductController();
$controller = new ProductController($gateway);
$controller->processRequest($_SERVER["REQUEST_METHOD"], $id);

