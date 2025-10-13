<?php

declare(strict_types=1); //forzar tipos estrictos

//carga automaticamente las clases que se usan en el proyecto
require __DIR__ . '/../app/core/ErrorHandler.php'; // dir es la carpeta actual (public) por eso subimos un nivel

//maneja las excepciones
set_error_handler("ErrorHandler::handleError");
set_exception_handler("ErrorHandler::handleException"); 

//indica que la respuesta sera en formato json
header("Content-Type: application/json; charset=UTF-8");

/////////////////////////////////////////////////////////////////////////
//routing
require  __DIR__ . "/../app/core/routes.php";