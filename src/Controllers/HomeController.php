<?php

declare(strict_types=1);

require_once __DIR__ . '/../config/Database.php';
require_once __DIR__ . '/../Security/Csrf.php';
require_once __DIR__ . '/../Security/Session.php';

class HomeController
{
	public function index(): void
	{
		Session::start();

		$isAuthenticated = isset($_SESSION['user_id']); ////Si existe, sabemos que tenemos una sesión autenticada.
		$username = $_SESSION['username'] ?? null;
		$csrfToken = Csrf::token();

		$database = new Database();
		$pdo = $database->getConnection();

		$stmt = $pdo->query(
			'SELECT
				images.id,
				images.filename,
				images.created_at,
				users.username
			FROM images
			INNER JOIN users
				ON users.id = images.user_id
			ORDER BY images.created_at DESC
			LIMIT 3'
		);

		$recentImages = $stmt->fetchAll();

		require __DIR__ . '/../Views/home.php';
	}
}