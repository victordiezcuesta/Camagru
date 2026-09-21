<?php

declare(strict_types=1);

require_once __DIR__ . '/../config/Database.php';

class AuthController
{
	public function login(): void
	{
		require __DIR__ . '/../Views/auth/login.php';
	}

	public function loginPost(): void
	{
		$username = trim($_POST['username'] ?? '');
		$password = $_POST['password'] ?? '';

		$errors = [];

		if ($username === '')
		{
			$errors[] = 'Username is required.';
		}

		if ($password === '')
		{
			$errors[] = 'Password is required.';
		}

		if (!empty($errors))
		{
			http_response_code(400);

			foreach ($errors as $error)
			{
				echo '<p>' . htmlspecialchars($error, ENT_QUOTES, 'UTF-8') . '</p>';
			}

			exit;
		}

		$database = new Database();
		$pdo = $database->getConnection();

		$stmt = $pdo->prepare(
			'SELECT id, username, email, password, email_verified
			FROM users
			WHERE username = :username'
		); //aqui busca el usuario.

		$stmt->execute([
			'username' => $username
		]);

		$user = $stmt->fetch();

		if ($user === false)
		{
			http_response_code(401);
			echo 'Invalid username or password.';
			exit;
		}

		if (!password_verify($password, $user['password']))
		{
			http_response_code(401);
			echo 'Invalid username or password.';
			exit;
		}

		session_set_cookie_params([
			'lifetime' => 0,
			'path' => '/',
			'secure' => false,
			'httponly' => true,  //HttpOnly no significa que la cookie esté cifrada. Simplemente impide que JavaScript acceda a ella.
			'samesite' => 'Lax'  //Limita el envío de la cookie desde otros sitios
		]);

		session_start();

		session_regenerate_id(true);

		$_SESSION['user_id'] = $user['id'];
		$_SESSION['username'] = $user['username'];

		header('Location: /');
		exit;
	}

	public function register(): void
	{
		require __DIR__ . '/../Views/auth/register.php';
	}

	public function registerPost(): void
	{
		$username = trim($_POST['username'] ?? ''); //Busca dentro de $_POST el campo llamado username. Si no existe, utiliza ''.
		$email = trim($_POST['email'] ?? '');
		$password = $_POST['password'] ?? '';

		$errors = [];

		if ($username === '')
		{
			$errors[] = 'Username is required.';
		}

		if ($email === '')
		{
			$errors[] = 'Email is required.';
		}
		elseif (!filter_var($email, FILTER_VALIDATE_EMAIL))
		{
			$errors[] = 'Invalid email address.';
		}

		if ($password === '')
		{
			$errors[] = 'Password is required.';
		}
		elseif (strlen($password) < 8)
		{
			$errors[] = 'Password must contain at least 8 characters.';
		}

		if (!empty($errors))
		{
			http_response_code(400);

			foreach ($errors as $error)
			{
				echo '<p>' . htmlspecialchars($error, ENT_QUOTES, 'UTF-8') . '</p>';
			}

			exit;
		}

		$database = new Database();
		$pdo = $database->getConnection();

		$stmt = $pdo->prepare(
			'SELECT id FROM users WHERE username = :username OR email = :email'
		);

		$stmt->execute([
			'username' => $username,
			'email' => $email
		]);

		if ($stmt->fetch() !== false)
		{
			http_response_code(409);
			echo 'Username or email already exists.';
			exit;
		}

		$passwordHash = password_hash($password, PASSWORD_DEFAULT);

		$stmt = $pdo->prepare(
			'INSERT INTO users (username, email, password)
			VALUES (:username, :email, :password)'
		);

		$stmt->execute([
			'username' => $username,
			'email' => $email,
			'password' => $passwordHash
		]);

		echo 'Registration successful.';
	}

	public function logout(): void
	{
		session_start();

		$_SESSION = [];

		if (ini_get('session.use_cookies'))
		{
			$params = session_get_cookie_params();

			setcookie(
				session_name(),
				'',
				time() - 42000,
				$params['path'],
				$params['domain'],
				$params['secure'],
				$params['httponly']
			);
		}

		session_destroy();

		header('Location: /login');
		exit;
	}
}