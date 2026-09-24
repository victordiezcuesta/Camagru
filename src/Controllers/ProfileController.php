<?php

declare(strict_types=1);

require_once __DIR__ . '/../config/Database.php';
require_once __DIR__ . '/../Security/Csrf.php';
require_once __DIR__ . '/../Security/Session.php';
require_once __DIR__ . '/../Security/PasswordValidator.php';
require_once __DIR__ . '/../Services/Mailer.php';

class ProfileController
{
	private function requireAuthentication(): void
	{
		Session::start();

		if (!isset($_SESSION['user_id']))
		{
			header('Location: /login');
			exit;
		}
	}

	public function index(): void
	{
		$this->requireAuthentication();

		$csrfToken = Csrf::token();
		$database = new Database();
		$pdo = $database->getConnection();

		$stmt = $pdo->prepare(
			'SELECT
				username,
				email,
				email_verified,
				comment_notifications
			FROM users
			WHERE id = :id'
		);

		$stmt->execute([
			'id' => $_SESSION['user_id']
		]);

		$user = $stmt->fetch();

		if ($user === false)
		{
			$_SESSION = [];

			session_destroy();

			header('Location: /login');
			exit;
		}

		require __DIR__ . '/../Views/auth/profile.php';
	}

	public function updateUsername(): void
	{
		$this->requireAuthentication();

		if (!Csrf::validate($_POST['csrf_token'] ?? null))
		{
			http_response_code(403);

			$errorTitle = 'Invalid request';
			$errorMessage = 'The security token is invalid or has expired. Please try again.';

			require __DIR__ . '/../Views/error.php';
			exit;
		}

		$username = trim($_POST['username'] ?? '');

		if ($username === '')
		{
			http_response_code(400);

			$errorTitle = 'Username required';
			$errorMessage = 'Please enter a username.';

			require __DIR__ . '/../Views/error.php';
			exit;
		}

		if (strlen($username) > 50)
		{
			http_response_code(400);

			$errorTitle = 'Invalid username';
			$errorMessage = 'The username must contain at most 50 characters.';

			require __DIR__ . '/../Views/error.php';
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
			'id' => $_SESSION['user_id']
		]);

		if ($stmt->fetch() !== false)
		{
			http_response_code(409);

			$errorTitle = 'Username unavailable';
			$errorMessage = 'This username is already in use. Please choose another one.';

			require __DIR__ . '/../Views/error.php';
			exit;
		}

		$stmt = $pdo->prepare(
			'UPDATE users
			SET username = :username
			WHERE id = :id'
		);

		$stmt->execute([
			'username' => $username,
			'id' => $_SESSION['user_id']
		]);

		$_SESSION['username'] = $username;

		header('Location: /profile');
		exit;
	}

	public function updateEmail(): void
	{
		$this->requireAuthentication();

		if (!Csrf::validate($_POST['csrf_token'] ?? null))
		{
			http_response_code(403);

			$errorTitle = 'Invalid request';
			$errorMessage = 'The security token is invalid or has expired. Please try again.';

			require __DIR__ . '/../Views/error.php';
			exit;
		}

		$email = trim($_POST['email'] ?? '');

		if ($email === '')
		{
			http_response_code(400);

			$errorTitle = 'Email required';
			$errorMessage = 'Please enter an email address.';

			require __DIR__ . '/../Views/error.php';
			exit;
		}

		if (!filter_var($email, FILTER_VALIDATE_EMAIL))
		{
			http_response_code(400);

			$errorTitle = 'Invalid email';
			$errorMessage = 'Please enter a valid email address.';

			require __DIR__ . '/../Views/error.php';
			exit;
		}

		if (strlen($email) > 255)
		{
			http_response_code(400);

			$errorTitle = 'Invalid email';
			$errorMessage = 'The email address is too long.';

			require __DIR__ . '/../Views/error.php';
			exit;
		}

		$database = new Database();
		$pdo = $database->getConnection();

		$stmt = $pdo->prepare(
			'SELECT email, username, email_verified
			FROM users
			WHERE id = :id'
		);

		$stmt->execute([
			'id' => $_SESSION['user_id']
		]);

		$currentUser = $stmt->fetch();
		if ($currentUser === false)
		{
			http_response_code(404);

			$errorTitle = 'User not found';
			$errorMessage = 'The requested user could not be found.';

			require __DIR__ . '/../Views/error.php';
			exit;
		}

		if ($email === $currentUser['email'])
		{
			http_response_code(400);

			$errorTitle = 'Email unchanged';
			$errorMessage = 'The new email address must be different from the current one.';

			require __DIR__ . '/../Views/error.php';
			exit;
		}

		$stmt = $pdo->prepare(
			'SELECT id
			FROM users
			WHERE email = :email
			AND id != :id'
		);

		$stmt->execute([
			'email' => $email,
			'id' => $_SESSION['user_id']
		]);

		if ($stmt->fetch() !== false)
		{
			http_response_code(409);

			$errorTitle = 'Email unavailable';
			$errorMessage = 'This email address is already in use. Please choose another one.';

			require __DIR__ . '/../Views/error.php';
			exit;
		}

		$verificationToken = bin2hex(random_bytes(32));
		$verificationExpiresAt = date('Y-m-d H:i:s', time() + 86400);

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
			'id' => $_SESSION['user_id']
		]);

		$verificationUrl =
			getenv('APP_URL')
			. '/verify?token='
			. urlencode($verificationToken);

		$mailer = new Mailer();

		if (!$mailer->sendEmailChangeVerificationEmail($email, $currentUser['username'], $verificationUrl))
		{
			$stmt = $pdo->prepare(
				'UPDATE users
				SET email = :email,
					email_verified = :email_verified,
					verification_token = NULL,
					verification_expires_at = NULL
				WHERE id = :id'
			);

			$stmt->execute([
				'email' => $currentUser['email'],
				'email_verified' => $currentUser['email_verified'],
				'id' => $_SESSION['user_id']
			]);

			http_response_code(500);

			$errorTitle = 'Email verification failed';
			$errorMessage = 'Unable to send the email verification message. Please try again later.';

			require __DIR__ . '/../Views/error.php';
			exit;
		}

		$successTitle = 'Email change requested';
		$successMessage = 'Please check your new email address and verify it. The verification link will expire in 24 hours.';

		require __DIR__ . '/../Views/success.php';
		exit;
	}

	public function updatePassword(): void
	{
		$this->requireAuthentication();

		if (!Csrf::validate($_POST['csrf_token'] ?? null))
		{
			http_response_code(403);

			$errorTitle = 'Invalid request';
			$errorMessage = 'The security token is invalid or has expired. Please try again.';

			require __DIR__ . '/../Views/error.php';
			exit;
		}

		$currentPassword = $_POST['current_password'] ?? '';
		$password = $_POST['password'] ?? '';
		$passwordConfirmation = $_POST['password_confirmation'] ?? '';

		if ($currentPassword === '')
		{
			http_response_code(400);

			$errorTitle = 'Current password required';
			$errorMessage = 'Please enter your current password.';

			require __DIR__ . '/../Views/error.php';
			exit;
		}

		if ($password === '')
		{
			http_response_code(400);

			$errorTitle = 'New password required';
			$errorMessage = 'Please enter a new password.';

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
			$errorMessage = 'The new password and its confirmation must match.';

			require __DIR__ . '/../Views/error.php';
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
			'id' => $_SESSION['user_id']
		]);

		$user = $stmt->fetch();

		if ($user === false)
		{
			http_response_code(404);

			$errorTitle = 'User not found';
			$errorMessage = 'The requested user could not be found.';

			require __DIR__ . '/../Views/error.php';
			exit;
		}

		if (!password_verify($currentPassword, $user['password']))
		{
			http_response_code(401);

			$errorTitle = 'Incorrect password';
			$errorMessage = 'The current password is incorrect.';

			require __DIR__ . '/../Views/error.php';
			exit;
		}

		$passwordHash = password_hash($password, PASSWORD_DEFAULT);

		$stmt = $pdo->prepare(
			'UPDATE users
			SET password = :password
			WHERE id = :id'
		);

		$stmt->execute([
			'password' => $passwordHash,
			'id' => $_SESSION['user_id']
		]);

		header('Location: /profile');
		exit;
	}

	public function updateCommentNotifications(): void
	{
		$this->requireAuthentication();

		if (!Csrf::validate($_POST['csrf_token'] ?? null))
		{
			http_response_code(403);

			$errorTitle = 'Invalid request';
			$errorMessage = 'The security token is invalid or has expired. Please try again.';

			require __DIR__ . '/../Views/error.php';
			exit;
		}

		$enabled = isset($_POST['comment_notifications']) && $_POST['comment_notifications'] === '1';

		$database = new Database();
		$pdo = $database->getConnection();

		$stmt = $pdo->prepare(
			'UPDATE users
			SET comment_notifications = :comment_notifications
			WHERE id = :id'
		);

		$stmt->execute([
			'comment_notifications' => $enabled ? 1 : 0,
			'id' => $_SESSION['user_id']
		]);

		header('Location: /profile');
		exit;
	}
}