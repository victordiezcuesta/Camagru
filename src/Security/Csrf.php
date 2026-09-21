<?php

declare(strict_types=1);

class Csrf
{
	public function token(): string
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

		if (!isset($_SESSION['csrf_token']))
		{
			$_SESSION['csrf_token'] = bin2hex(random_bytes(32));
		}

		return $_SESSION['csrf_token'];
	}

	public function validate(?string $token): bool
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

		if ($token === null || !isset($_SESSION['csrf_token']))
		{
			return false;
		}

		return hash_equals($_SESSION['csrf_token'], $token);
	}
}