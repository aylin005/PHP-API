<?php

declare(strict_types=1); //forzar tipos estrictos

//carga automaticamente las clases que se usan en el proyecto
require __DIR__ . '/../vendor/autoload.php';
use App\Core\ErrorHandler;
//maneja las excepciones
set_error_handler([ErrorHandler::class, 'handleError']);
set_exception_handler([ErrorHandler::class, 'handleException']); 

//indica que la respuesta sera en formato json
header("Content-Type: application/json; charset=UTF-8");

/////////////////////////////////////////////////////////////////////////
//routing
require  __DIR__ . "/../App/Core/routes.php";