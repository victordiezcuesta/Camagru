<?php

declare(strict_types=1);

class HomeController
{
	public function index(): void
	{
		session_start();

		$isAuthenticated = isset($_SESSION['user_id']); //Si existe, sabemos que tenemos una sesión autenticada.

		$username = $_SESSION['username'] ?? null;

		require __DIR__ . '/../Views/home.php';
	}
}