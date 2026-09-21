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
			'SELECT username, email, email_verified
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

	public function updateUsername(): void
	{
		$userId = $this->requireAuthentication();

		$username = trim($_POST['username'] ?? '');

		if ($username === '')
		{
			http_response_code(400);
			echo 'Username is required.';
			exit;
		}

		if (strlen($username) > 50)
		{
			http_response_code(400);
			echo 'Username must contain at most 50 characters.';
			exit;
		}

		$database = new Database();
		$pdo = $database->getConnection();

		$stmt = $pdo->prepare(
			'SELECT id
			FROM users
			WHERE username = :username
			AND id != :id'
		);

		$stmt->execute([
			'username' => $username,
			'id' => $userId
		]);

		if ($stmt->fetch() !== false)
		{
			http_response_code(409);
			echo 'Username already exists.';
			exit;
		}

		$stmt = $pdo->prepare(
			'UPDATE users
			SET username = :username
			WHERE id = :id'
		);

		$stmt->execute([
			'username' => $username,
			'id' => $userId
		]);

		session_start();

		$_SESSION['username'] = $username;

		header('Location: /profile');
		exit;
	}

	public function updateEmail(): void
	{
		$userId = $this->requireAuthentication();

		$email = trim($_POST['email'] ?? '');

		if ($email === '')
		{
			http_response_code(400);
			echo 'Email is required.';
			exit;
		}

		if (!filter_var($email, FILTER_VALIDATE_EMAIL))
		{
			http_response_code(400);
			echo 'Invalid email address.';
			exit;
		}

		$database = new Database();
		$pdo = $database->getConnection();

		$stmt = $pdo->prepare(
			'SELECT id
			FROM users
			WHERE email = :email
			AND id != :id'
		);

		$stmt->execute([
			'email' => $email,
			'id' => $userId
		]);

		if ($stmt->fetch() !== false)
		{
			http_response_code(409);
			echo 'Email address already exists.';
			exit;
		}

		$verificationToken = bin2hex(random_bytes(32));

		$verificationExpiresAt = date(
			'Y-m-d H:i:s',
			time() + 86400
		);

		$stmt = $pdo->prepare(
			'UPDATE users
			SET email = :email,
			email_verified = FALSE,
			verification_token = :verification_token,
			verification_expires_at = :verification_expires_at
			WHERE id = :id'
		);

		$stmt->execute([
			'email' => $email,
			'verification_token' => $verificationToken,
			'verification_expires_at' => $verificationExpiresAt,
			'id' => $userId
		]);

		$verificationUrl =
			'http://localhost:8080/verify?token='
			. urlencode($verificationToken);

		echo '<h1>Email address updated.</h1>';

		echo '<p>';
		echo 'Please verify your new email address.';
		echo '</p>';

		echo '<p>';
		echo '<a href="'
			. htmlspecialchars(
			$verificationUrl,
			ENT_QUOTES,
			'UTF-8'
			)
			. '">';
		echo 'Verify your new email address';
		echo '</a>';
		echo '</p>';

		echo '<p>';
		echo '<a href="/profile">Back to profile</a>';
		echo '</p>';

		exit;
	}

	public function updatePassword(): void
	{
		$userId = $this->requireAuthentication();

		$currentPassword = $_POST['current_password'] ?? '';
		$newPassword = $_POST['password'] ?? '';
		$passwordConfirmation = $_POST['password_confirmation'] ?? '';

		if ($currentPassword === '')
		{
			http_response_code(400);
			echo 'Current password is required.';
			exit;
		}

		if ($newPassword === '')
		{
			http_response_code(400);
			echo 'New password is required.';
			exit;
		}

		if (strlen($newPassword) < 8)
		{
			http_response_code(400);
			echo 'New password must contain at least 8 characters.';
			exit;
		}

		if ($newPassword !== $passwordConfirmation)
		{
			http_response_code(400);
			echo 'Passwords do not match.';
			exit;
		}

		$database = new Database();
		$pdo = $database->getConnection();

		$stmt = $pdo->prepare(
			'SELECT password
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

		if (!password_verify($currentPassword, $user['password']))
		{
			http_response_code(401);
			echo 'Current password is incorrect.';
			exit;
		}

		$passwordHash = password_hash(
			$newPassword,
			PASSWORD_DEFAULT
		);

		$stmt = $pdo->prepare(
			'UPDATE users
			SET password = :password
			WHERE id = :id'
		);

		$stmt->execute([
			'password' => $passwordHash,
			'id' => $userId
		]);

		header('Location: /profile');
		exit;
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