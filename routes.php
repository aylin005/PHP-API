<?php 
use core\Router;

$router = new Router();

$router->add('/products', 'app/controllers/ProductController.php');
$router->dispatch($_SERVER['REQUEST_URI']);