<?php

declare(strict_types=1);

require_once __DIR__ . '/../Security/Csrf.php';
require_once __DIR__ . '/../Security/Session.php';

class HomeController
{
	public function index(): void
	{
		Session::start();

		$isAuthenticated = isset($_SESSION['user_id']); //Si existe, sabemos que tenemos una sesión autenticada.
		$username = $_SESSION['username'] ?? null;
		$csrfToken = Csrf::token();

		require __DIR__ . '/../Views/home.php';
	}
}