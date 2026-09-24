<?php

declare(strict_types=1); //"PHP, dentro de este archivo, intenta ser estricto con los tipos que he declarado."

require_once __DIR__ . '/../src/Controllers/AuthController.php'; //Carga y ejecuta este otro archivo PHP, pero asegúrate de cargarlo una sola vez. parecido a los .hpp y .h pero sin serlo
require_once __DIR__ . '/../src/Controllers/HomeController.php';
require_once __DIR__ . '/../src/Controllers/ProfileController.php';
require_once __DIR__ . '/../src/Controllers/GalleryController.php';
require_once __DIR__ . '/../src/Controllers/PhotoController.php';
require_once __DIR__ . '/../src/Controllers/GalleryInteractionController.php';

$controller = new AuthController();
$homeController = new HomeController();
$profileController = new ProfileController();
$galleryController = new GalleryController();
$photoController = new PhotoController();
$galleryInteractionController = new GalleryInteractionController();

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

if ($path === '/forgot-password' && $method === 'GET')
{
    $controller->forgotPassword();
    exit;
}

if ($path === '/forgot-password' && $method === 'POST')
{
    $controller->forgotPasswordPost();
    exit;
}

if ($path === '/reset-password' && $method === 'GET')
{
    $controller->resetPassword();
    exit;
}

if ($path === '/reset-password' && $method === 'POST')
{
    $controller->resetPasswordPost();
    exit;
}

if ($path === '/logout' && $method === 'POST')
{
    $controller->logout();
    exit;
}

if ($path === '/profile' && $method === 'GET')
{
	$profileController->index();
	exit;
}

if ($path === '/profile/username' && $method === 'POST')
{
	$profileController->updateUsername();
	exit;
}

if ($path === '/profile/email' && $method === 'POST')
{
	$profileController->updateEmail();
	exit;
}

if ($path === '/profile/password' && $method === 'POST')
{
	$profileController->updatePassword();
	exit;
}

if ($path === '/profile/comment-notifications' && $method === 'POST')
{
	$profileController->updateCommentNotifications();
	exit;
}

if ($path === '/gallery' && $method === 'GET')
{
	$galleryController->index();
	exit;
}

if ($path === '/photo/create' && $method === 'GET')
{
	$photoController->create();
	exit;
}

if ($path === '/photo' && $method === 'POST')
{
	$photoController->store();
	exit;
}

if ($path === '/photo/delete' && $method === 'POST')
{
	$photoController->delete();
	exit;
}

if ($path === '/gallery/like' && $method === 'POST')
{
	$galleryInteractionController->toggleLike();
	exit;
}

if ($path === '/gallery/comment' && $method === 'POST')
{
	$galleryInteractionController->addComment();
	exit;
}

http_response_code(404);

$errorTitle = 'Page not found';
$errorMessage = 'The requested page could not be found.';

require __DIR__ . '/../src/Views/error.php';
exit;