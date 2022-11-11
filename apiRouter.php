<?php
require_once './libs/Router.php';
require_once './app/controllers/librosApiController.php';

// crea el router
$router = new Router();

// defina la tabla de ruteo
$router->addRoute('libros', 'GET', 'librosApiController', 'getLibros');
$router->addRoute('libros/:ID', 'GET', 'librosApiController', 'getLibro');
$router->addRoute('libros/:ID', 'DELETE', 'librosApiController', 'deleteLibro');
$router->addRoute('libros', 'POST', 'librosApiController', 'insertLibro'); 

// ejecuta la ruta (sea cual sea)
$router->route($_GET["resource"], $_SERVER['REQUEST_METHOD']);
