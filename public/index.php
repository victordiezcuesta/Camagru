<?php

declare(strict_types=1); //"PHP, dentro de este archivo, intenta ser estricto con los tipos que he declarado."

require_once __DIR__ . '/../src/Controllers/AuthController.php'; //Carga y ejecuta este otro archivo PHP, pero asegúrate de cargarlo una sola vez. parecido a los .hpp y .h pero sin serlo
require_once __DIR__ . '/../src/Controllers/HomeController.php';

$controller = new AuthController();
$homeController = new HomeController();

$path = parse_url($_SERVER['REQUEST_URI'], PHP_URL_PATH); //$_SERVER['REQUEST_URI'] contiene la petición realizada por el navegador. y si tiene /register?foo=bar se quedaria solo /register
$method = $_SERVER['REQUEST_METHOD']; //es el method del html es decir el get el set el post, ...

if ($path === '/' && $method === 'GET')
{
	$homeController->index();
	exit;
}

if ($path === '/login' && $method === 'GET')
{
	$controller->login();
	exit;
}

if ($path === '/login' && $method === 'POST')
{
	$controller->loginPost();
	exit;
}

if ($path === '/register' && $method === 'GET')
{
	$controller->register();
	exit;
}

if ($path === '/register' && $method === 'POST')
{
	$controller->registerPost();
	exit;
}

if ($path === '/verify' && $method === 'GET')
{
    $controller->verify();
    exit;
}

if ($path === '/logout' && $method === 'GET')
{
	$controller->logout();
	exit;
}

http_response_code(404);

echo '404 - Page not found';