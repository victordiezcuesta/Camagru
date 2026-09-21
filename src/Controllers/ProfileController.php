<?php

declare(strict_types=1);

require_once __DIR__ . '/../config/Database.php';

class ProfileController
{
	public function index(): void
	{
		$userId = $this->requireAuthentication();

		$database = new Database();
		$pdo = $database->getConnection();

		$stmt = $pdo->prepare(
			'SELECT username, email
			FROM users
			WHERE id = :id'
		);

		$stmt->execute([
			'id' => $userId
		]);

		$user = $stmt->fetch();

		if ($user === false)
		{
			http_response_code(404);
			echo 'User not found.';
			exit;
		}

		require __DIR__ . '/../Views/auth/profile.php';
	}

	private function requireAuthentication(): int //si intentan hacer http://localhost:8080/profile sin estar iniciados sesion no puedan
	{
		session_start();

		if (!isset($_SESSION['user_id']))
		{
			header('Location: /login');
			exit;
		}

		return (int) $_SESSION['user_id'];
	}
}