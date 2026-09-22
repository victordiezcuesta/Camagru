<?php

declare(strict_types=1);

class PasswordValidator
{
	public static function validate(string $password): ?string
	{
		if (strlen($password) < 8)
			return 'Password must contain at least 8 characters.';

		if (!preg_match('/[A-Z]/', $password))
			return 'Password must contain at least one uppercase letter.';

		if (!preg_match('/[a-z]/', $password))
			return 'Password must contain at least one lowercase letter.';

		if (!preg_match('/[0-9]/', $password))
			return 'Password must contain at least one number.';

		return null;
	}
}