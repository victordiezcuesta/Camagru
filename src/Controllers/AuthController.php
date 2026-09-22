<?php

declare(strict_types=1);

require_once __DIR__ . '/../config/Database.php';
require_once __DIR__ . '/../Security/Csrf.php';
require_once __DIR__ . '/../Security/Session.php';
require_once __DIR__ . '/../Services/Mailer.php';
require_once __DIR__ . '/../Security/PasswordValidator.php';

class AuthController
{
	public function login(): void
	{
		Session::start();

		$csrfToken = Csrf::token();

		require __DIR__ . '/../Views/auth/login.php';
	}

	public function loginPost(): void
	{
		if (!Csrf::validate($_POST['csrf_token'] ?? null)) //$_POST es un array especial que PHP crea automáticamente cuando recibe datos enviados mediante el método HTTP POST
		{
			http_response_code(403);
			echo 'Invalid CSRF token.';
			exit;
		}

		$username = trim($_POST['username'] ?? '');
		$password = $_POST['password'] ?? '';

		$errors = [];

		if ($username === '')
			$errors[] = 'Username is required.';

		if ($password === '')
			$errors[] = 'Password is required.';

		if (!empty($errors))
		{
			http_response_code(400);

			foreach ($errors as $error)
				echo '<p>' . htmlspecialchars($error, ENT_QUOTES, 'UTF-8') . '</p>';

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

		if (!$user['email_verified'])
		{
			http_response_code(403);
			echo 'Please verify your email address before logging in.';
			exit;
		}

		if (!password_verify($password, $user['password']))
		{
			http_response_code(401);
			echo 'Invalid username or password.';
			exit;
		}

		Session::start();

		session_regenerate_id(true);

		$_SESSION['user_id'] = $user['id'];
		$_SESSION['username'] = $user['username'];

		header('Location: /');
		exit;
	}

	public function register(): void
	{
		Session::start();

		$csrfToken = Csrf::token();

		require __DIR__ . '/../Views/auth/register.php';
	}

	public function registerPost(): void
	{
		if (!Csrf::validate($_POST['csrf_token'] ?? null))
		{
			http_response_code(403);
			echo 'Invalid CSRF token.';
			exit;
		}

		$username = trim($_POST['username'] ?? ''); //Busca dentro de $_POST el campo llamado username. Si no existe, utiliza ''.
		$email = trim($_POST['email'] ?? '');
		$password = $_POST['password'] ?? '';

		$errors = [];

		if ($username === '')
			$errors[] = 'Username is required.';

		if ($email === '')
			$errors[] = 'Email is required.';
		elseif (!filter_var($email, FILTER_VALIDATE_EMAIL))
			$errors[] = 'Invalid email address.';

		if ($password === '')
			$errors[] = 'Password is required.';
		else
		{
			$passwordError = PasswordValidator::validate($password);
			if ($passwordError !== null)
				$errors[] = $passwordError;
		}

		if (!empty($errors))
		{
			http_response_code(400);

			foreach ($errors as $error)
				echo '<p>' . htmlspecialchars($error, ENT_QUOTES, 'UTF-8') . '</p>';

			exit;
		}

		$database = new Database();
		$pdo = $database->getConnection();

		$stmt = $pdo->prepare('SELECT id FROM users WHERE username = :username OR email = :email');

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

		$verificationToken = bin2hex(random_bytes(32)); //genereamos un token a voleo

		$verificationExpiresAt = date('Y-m-d H:i:s', time() + 86400); //expira en 24 horas

		$stmt = $pdo->prepare(
			'INSERT INTO users (
				username,
				email,
				password,
				verification_token,
				verification_expires_at
			)
			VALUES (
				:username,
				:email,
				:password,
				:verification_token,
				:verification_expires_at
			)'
		);

		$stmt->execute([
			'username' => $username,
			'email' => $email,
			'password' => $passwordHash,
			'verification_token' => $verificationToken,
			'verification_expires_at' => $verificationExpiresAt
		]);

		$verificationUrl =
			getenv('APP_URL')
			. '/verify?token='
			. urlencode($verificationToken);

		$mailer = new Mailer();

		if (!$mailer->sendVerificationEmail($email, $username, $verificationUrl))
		{
			$pdo->prepare(
				'DELETE FROM users WHERE id = :id' //Si el INSERT funciona pero el SMTP falla, Eso dejaría una cuenta bloqueada, por eso lo eliminamos
			)->execute([
				'id' => $pdo->lastInsertId()
			]);

			http_response_code(500);
			echo 'Unable to send verification email.';
			exit;
		}

		echo '<h1>Registration successful.</h1>';
		echo '<p>Please check your email to verify your account.</p>';
		echo '<p><a href="/login">Back to login</a></p>';
	}

	public function verify(): void
	{
		$token = $_GET['token'] ?? '';

		if ($token === '')
		{
			http_response_code(400);
			echo 'Invalid verification link.';
			exit;
		}

		$database = new Database();
		$pdo = $database->getConnection();

		$stmt = $pdo->prepare(
			'SELECT id, verification_expires_at
			FROM users
			WHERE verification_token = :token'
		);

		$stmt->execute([
			'token' => $token
		]);

		$user = $stmt->fetch();

		if ($user === false)
		{
			http_response_code(400);
			echo 'Invalid verification link.';
			exit;
		}

		if ($user['verification_expires_at'] === null || strtotime($user['verification_expires_at']) < time())
		{
			http_response_code(400);
			echo 'Verification link has expired.';
			exit;
		}

		$stmt = $pdo->prepare(
			'UPDATE users
			SET email_verified = TRUE,
				verification_token = NULL,
				verification_expires_at = NULL
			WHERE id = :id'
		);

		$stmt->execute([
			'id' => $user['id']
		]);

		header('Location: /login');
		exit;
	}

	public function forgotPassword(): void
	{
		Session::start();

		$csrfToken = Csrf::token();

		require __DIR__ . '/../Views/auth/forgot-password.php';
	}

	public function forgotPasswordPost(): void
	{
		if (!Csrf::validate($_POST['csrf_token'] ?? null))
		{
			http_response_code(403);
			echo 'Invalid CSRF token.';
			exit;
		}

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
			'SELECT id, email_verified
			FROM users
			WHERE email = :email'
		);

		$stmt->execute([
			'email' => $email
		]);

		$user = $stmt->fetch();

		//enviamos el mismo mensaje tanto si existe el correo o no por seguiridad y no dar señales de si existe o no el correo
		$message = 'If an account exists with that email address, a password reset link has been sent.';

		if ($user === false)
		{
			echo '<h1>Password reset</h1>';
			echo '<p>' . htmlspecialchars($message, ENT_QUOTES, 'UTF-8') . '</p>';
			echo '<p><a href="/login">Back to login</a></p>';
			exit;
		}

		if (!$user['email_verified'])
		{
			echo '<h1>Password reset</h1>';
			echo '<p>' . htmlspecialchars($message, ENT_QUOTES, 'UTF-8') . '</p>';
			echo '<p><a href="/login">Back to login</a></p>';
			exit;
		}

		$resetToken = bin2hex(random_bytes(32));
		$resetExpiresAt = date('Y-m-d H:i:s', time() + 3600); //token valido una hora

		$stmt = $pdo->prepare(
			'UPDATE users
			SET password_reset_token = :token,
				password_reset_expires_at = :expires_at
			WHERE id = :id'
		);

		$stmt->execute([
			'token' => $resetToken,
			'expires_at' => $resetExpiresAt,
			'id' => $user['id']
		]);

		$resetUrl =
			getenv('APP_URL')
			. '/reset-password?token='
			. urlencode($resetToken);

		$stmt = $pdo->prepare(
			'SELECT username
			FROM users
			WHERE id = :id'
		);

		$stmt->execute([
			'id' => $user['id']
		]);

		$userData = $stmt->fetch();

		if ($userData === false)
		{
			http_response_code(500);
			echo 'Unable to process password reset.';
			exit;
		}

		$mailer = new Mailer();

		$mailSent = $mailer->sendPasswordResetEmail($email, $userData['username'], $resetUrl);

		if (!$mailSent)
		{
			$pdo->prepare(
				'UPDATE users
				SET password_reset_token = NULL,
					password_reset_expires_at = NULL
				WHERE id = :id'
			)->execute([
				'id' => $user['id']
			]);

			http_response_code(500);
			echo 'Unable to send password reset email.';
			exit;
		}

		echo '<h1>Password reset</h1>';

		echo '<p>'
			. htmlspecialchars($message, ENT_QUOTES, 'UTF-8')
			. '</p>';

		echo '<p><a href="/login">Back to login</a></p>';

		exit;
	}

	public function resetPassword(): void
	{
		$token = $_GET['token'] ?? '';

		if ($token === '')
		{
			http_response_code(400);
			echo 'Invalid password reset link.';
			exit;
		}

		$database = new Database();
		$pdo = $database->getConnection();

		$stmt = $pdo->prepare(
			'SELECT id, password_reset_expires_at
			FROM users
			WHERE password_reset_token = :token'
		);

		$stmt->execute([
			'token' => $token
		]);

		$user = $stmt->fetch();

		if ($user === false)
		{
			http_response_code(400);
			echo 'Invalid password reset link.';
			exit;
		}

		if ($user['password_reset_expires_at'] === null || strtotime($user['password_reset_expires_at']) < time())
		{
			http_response_code(400);
			echo 'Password reset link has expired.';
			exit;
		}

		Session::start();

		$csrfToken = Csrf::token();

		require __DIR__ . '/../Views/auth/reset-password.php';
	}

	public function resetPasswordPost(): void
	{
		if (!Csrf::validate($_POST['csrf_token'] ?? null))
		{
			http_response_code(403);
			echo 'Invalid CSRF token.';
			exit;
		}

		$token = $_POST['token'] ?? '';
		$password = $_POST['password'] ?? '';
		$passwordConfirmation = $_POST['password_confirmation'] ?? '';

		if ($token === '')
		{
			http_response_code(400);
			echo 'Invalid password reset link.';
			exit;
		}

		if ($password === '')
		{
			http_response_code(400);
			echo 'Password is required.';
			exit;
		}

		$passwordError = PasswordValidator::validate($password);
		if ($passwordError !== null)
		{
			http_response_code(400);
			echo htmlspecialchars($passwordError, ENT_QUOTES, 'UTF-8');
			exit;
		}

		if ($password !== $passwordConfirmation)
		{
			http_response_code(400);
			echo 'Passwords do not match.';
			exit;
		}

		$database = new Database();
		$pdo = $database->getConnection();

		$stmt = $pdo->prepare(
			'SELECT id, password_reset_expires_at
			FROM users
			WHERE password_reset_token = :token'
		);

		$stmt->execute([
			'token' => $token
		]);

		$user = $stmt->fetch();

		if ($user === false)
		{
			http_response_code(400);
			echo 'Invalid password reset link.';
			exit;
		}

		if ($user['password_reset_expires_at'] === null || strtotime($user['password_reset_expires_at']) < time())
		{
			http_response_code(400);
			echo 'Password reset link has expired.';
			exit;
		}

		$passwordHash = password_hash($password, PASSWORD_DEFAULT);

		$stmt = $pdo->prepare(
			'UPDATE users
			SET password = :password,
				password_reset_token = NULL,
				password_reset_expires_at = NULL
			WHERE id = :id'
		);

		$stmt->execute([
			'password' => $passwordHash,
			'id' => $user['id']
		]);

		header('Location: /login');
		exit;
	}

	public function logout(): void
	{
		Session::start();

		if (!Csrf::validate($_POST['csrf_token'] ?? null))
		{
			http_response_code(403);
			echo 'Invalid CSRF token.';
			exit;
		}

		$_SESSION = [];

		if (ini_get('session.use_cookies'))
		{
			$params = session_get_cookie_params();
			setcookie(session_name(), '', time() - 42000, $params['path'], $params['domain'], $params['secure'], $params['httponly']);
		}

		session_destroy();

		header('Location: /login');
		exit;
	}
}