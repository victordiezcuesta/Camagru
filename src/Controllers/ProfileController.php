<?php

declare(strict_types=1);

require_once __DIR__ . '/../config/Database.php';
require_once __DIR__ . '/../Security/Csrf.php';
require_once __DIR__ . '/../Security/Session.php';

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
            'SELECT username, email, email_verified
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
            echo 'Invalid CSRF token.';
            exit;
        }

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
            'id' => $_SESSION['user_id']
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

        if (strlen($email) > 255)
        {
            http_response_code(400);
            echo 'Email address is too long.';
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
            'id' => $_SESSION['user_id']
        ]);

        if ($stmt->fetch() !== false)
        {
            http_response_code(409);
            echo 'Email already exists.';
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
            'id' => $_SESSION['user_id']
        ]);

        echo '<h1>Email updated.</h1>';

        echo '<p>Please verify your new email address.</p>';

        $verificationUrl =
			getenv('APP_URL')
			. '/verify?token='
			. urlencode($verificationToken);

        echo '<p><a href="'
            . htmlspecialchars(
                $verificationUrl,
                ENT_QUOTES,
                'UTF-8'
            )
            . '">Verify your new email</a></p>';

        echo '<p><a href="/profile">Back to profile</a></p>';

        exit;
    }

    public function updatePassword(): void
    {
        $this->requireAuthentication();

        if (!Csrf::validate($_POST['csrf_token'] ?? null))
        {
            http_response_code(403);
            echo 'Invalid CSRF token.';
            exit;
        }

        $currentPassword = $_POST['current_password'] ?? '';
        $password = $_POST['password'] ?? '';
        $passwordConfirmation = $_POST['password_confirmation'] ?? '';

        if ($currentPassword === '')
        {
            http_response_code(400);
            echo 'Current password is required.';
            exit;
        }

        if ($password === '')
        {
            http_response_code(400);
            echo 'New password is required.';
            exit;
        }

        if (strlen($password) < 8)
        {
            http_response_code(400);
            echo 'Password must contain at least 8 characters.';
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
            $password,
            PASSWORD_DEFAULT
        );

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
}