<?php

declare(strict_types=1);

require_once __DIR__ . '/Session.php';

class Csrf
{
	public static function token(): string
	{
		Session::start();

		if (!isset($_SESSION['csrf_token']))
		{
			$_SESSION['csrf_token'] = bin2hex(random_bytes(32));
		}

		return $_SESSION['csrf_token'];
	}

	public static function validate(?string $token): bool
	{
		Session::start();

		if ($token === null || !isset($_SESSION['csrf_token']))
		{
			return false;
		}

		return hash_equals(
			$_SESSION['csrf_token'],
			$token
		);
	}
}