<?php

declare(strict_types=1);

require_once __DIR__ . '/../Security/Csrf.php';

class HomeController
{
	public function index(): void
	{
		if (session_status() === PHP_SESSION_NONE)
		{
			session_set_cookie_params([
				'lifetime' => 0,
				'path' => '/',
				'secure' => false,
				'httponly' => true,
				'samesite' => 'Lax'
			]);

			session_start();
		}

		$isAuthenticated = isset($_SESSION['user_id']); //Si existe, sabemos que tenemos una sesión autenticada.

		$username = $_SESSION['username'] ?? null;

		require __DIR__ . '/../Views/home.php';
	}
}