<?php
declare(strict_types=1); //forzar tipos estrictos

//carga automaticamente las clases que se usan en el proyecto
require "app/controllers/ProductController.php";
require "app/models/ProductGateway.php";
require "app/core/Database.php";
require "app/core/ErrorHandler.php";

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
$config = require "app/config/config.php"; 

$database = new Database($config['db']['connection1']);

//$database->getConnection();
$gateway = new ProductGateway($database);

//main controller
//$controller = new ProductController();
$controller = new ProductController($gateway);
$controller->processRequest($_SERVER["REQUEST_METHOD"], $id);

