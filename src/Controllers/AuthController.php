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

			$errorTitle = 'Security error';
			$errorMessage = 'The security token is invalid or has expired.';

			require __DIR__ . '/../Views/error.php';
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

			$errorTitle = 'Invalid login';
			$errorMessage = implode(' ', $errors);

			require __DIR__ . '/../Views/error.php';
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

			$errorTitle = 'Login failed';
			$errorMessage = 'Invalid username or password.';

			require __DIR__ . '/../Views/error.php';
			exit;
		}

		if (!$user['email_verified'])
		{
			http_response_code(403);

			$errorTitle = 'Email not verified';
			$errorMessage = 'Please verify your email address before logging in.';

			require __DIR__ . '/../Views/error.php';
			exit;
		}

		if (!password_verify($password, $user['password']))
		{
			http_response_code(401);

			$errorTitle = 'Login failed';
			$errorMessage = 'Invalid username or password.';

			require __DIR__ . '/../Views/error.php';
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

			$errorTitle = 'Security error';
			$errorMessage = 'The security token is invalid or has expired.';

			require __DIR__ . '/../Views/error.php';
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

			$errorTitle = 'Invalid registration';
			$errorMessage = implode(' ', $errors);

			require __DIR__ . '/../Views/error.php';
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

			$errorTitle = 'Account already exists';
			$errorMessage = 'The username or email address is already in use.';

			require __DIR__ . '/../Views/error.php';
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

			$errorTitle = 'Registration error';
			$errorMessage = 'Unable to send verification email.';

			require __DIR__ . '/../Views/error.php';
			exit;
		}

		require __DIR__ . '/../Views/auth/registration-success.php';
		exit;
	}

	public function verify(): void
	{
		$token = $_GET['token'] ?? '';

		if ($token === '')
		{
			http_response_code(400);

			$errorTitle = 'Invalid verification link';
			$errorMessage = 'The verification link is invalid.';

			require __DIR__ . '/../Views/error.php';
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

			$errorTitle = 'Invalid verification link';
			$errorMessage = 'The verification link is invalid or has already been used.';

			require __DIR__ . '/../Views/error.php';
			exit;
		}

		if ($user['verification_expires_at'] === null || strtotime($user['verification_expires_at']) < time())
		{
			http_response_code(400);

			$errorTitle = 'Verification link expired';
			$errorMessage = 'This verification link has expired. Please register again.';

			require __DIR__ . '/../Views/error.php';
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

			$errorTitle = 'Security error';
			$errorMessage = 'The security token is invalid or has expired.';

			require __DIR__ . '/../Views/error.php';
			exit;
		}

		$email = trim($_POST['email'] ?? '');
		if ($email === '')
		{
			http_response_code(400);

			$errorTitle = 'Invalid email address';
			$errorMessage = 'Email is required.';

			require __DIR__ . '/../Views/error.php';
			exit;
		}

		if (!filter_var($email, FILTER_VALIDATE_EMAIL))
		{
			http_response_code(400);

			$errorTitle = 'Invalid email address';
			$errorMessage = 'Please enter a valid email address.';

			require __DIR__ . '/../Views/error.php';
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

		if ($user === false || !$user['email_verified'])
		{
			Session::start();

			$csrfToken = Csrf::token();

			$message = 'If an account exists with that email address, a password reset link has been sent.';

			require __DIR__ . '/../Views/auth/forgot-password.php';
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

			$errorTitle = 'Password reset error';
			$errorMessage = 'Unable to process the password reset request.';

			require __DIR__ . '/../Views/error.php';
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

			$errorTitle = 'Password reset error';
			$errorMessage = 'Unable to send password reset email.';

			require __DIR__ . '/../Views/error.php';
			exit;
		}

		Session::start();

		$csrfToken = Csrf::token();

		$message = 'If an account exists with that email address, a password reset link has been sent.';

		require __DIR__ . '/../Views/auth/forgot-password.php';
		exit;
	}

	public function resetPassword(): void
	{
		$token = $_GET['token'] ?? '';

		if ($token === '')
		{
			http_response_code(400);

			$errorTitle = 'Invalid password reset link';
			$errorMessage = 'The password reset link is invalid.';

			require __DIR__ . '/../Views/error.php';
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

			$errorTitle = 'Invalid password reset link';
			$errorMessage = 'The password reset link is invalid or has already been used.';

			require __DIR__ . '/../Views/error.php';
			exit;
		}

		if ($user['password_reset_expires_at'] === null || strtotime($user['password_reset_expires_at']) < time())
		{
			http_response_code(400);

			$errorTitle = 'Password reset link expired';
			$errorMessage = 'This password reset link has expired. Please request a new one.';

			require __DIR__ . '/../Views/error.php';
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

			$errorTitle = 'Security error';
			$errorMessage = 'The security token is invalid or has expired.';

			require __DIR__ . '/../Views/error.php';
			exit;
		}

		$token = $_POST['token'] ?? '';
		$password = $_POST['password'] ?? '';
		$passwordConfirmation = $_POST['password_confirmation'] ?? '';

		if ($token === '')
		{
			http_response_code(400);

			$errorTitle = 'Invalid password reset link';
			$errorMessage = 'The password reset link is invalid.';

			require __DIR__ . '/../Views/error.php';
			exit;
		}

		if ($password === '')
		{
			http_response_code(400);

			$errorTitle = 'Invalid password';
			$errorMessage = 'Password is required.';

			require __DIR__ . '/../Views/error.php';
			exit;
		}
		$passwordError = PasswordValidator::validate($password);
		if ($passwordError !== null)
		{
			http_response_code(400);

			$errorTitle = 'Invalid password';
			$errorMessage = $passwordError;

			require __DIR__ . '/../Views/error.php';
			exit;
		}

		if ($password !== $passwordConfirmation)
		{
			http_response_code(400);

			$errorTitle = 'Passwords do not match';
			$errorMessage = 'The two passwords must be identical.';

			require __DIR__ . '/../Views/error.php';
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

			$errorTitle = 'Invalid password reset link';
			$errorMessage = 'The password reset link is invalid or has already been used.';

			require __DIR__ . '/../Views/error.php';
			exit;
		}

		if ($user['password_reset_expires_at'] === null || strtotime($user['password_reset_expires_at']) < time())
		{
			http_response_code(400);

			$errorTitle = 'Password reset link expired';
			$errorMessage = 'This password reset link has expired. Please request a new one.';

			require __DIR__ . '/../Views/error.php';
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

			$errorTitle = 'Security error';
			$errorMessage = 'The security token is invalid or has expired.';

			require __DIR__ . '/../Views/error.php';
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