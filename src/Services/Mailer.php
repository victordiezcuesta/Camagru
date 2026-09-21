<?php

declare(strict_types=1);

class Mailer
{
	private string $host;
	private int $port;
	private string $username;
	private string $password;
	private string $from;
	private string $encryption;

	public function __construct()
	{
		$this->host = getenv('MAIL_HOST') ?: '';
		$this->port = (int) (getenv('MAIL_PORT') ?: 587);
		$this->username = getenv('MAIL_USERNAME') ?: '';
		$this->password = getenv('MAIL_PASSWORD') ?: '';
		$this->from = getenv('MAIL_FROM') ?: '';
		$this->encryption = getenv('MAIL_ENCRYPTION') ?: 'tls';
	}

	private function connect()
	{
		$transport = 'tcp://';

		if ($this->encryption === 'ssl')
		{
			$transport = 'ssl://';
		}

		$address = $transport . $this->host . ':' . $this->port;

		$errorCode = 0;
		$errorMessage = '';

		return stream_socket_client($address, $errorCode, $errorMessage, 10);
	}

	private function expect($socket, int $expectedCode): bool
	{
		$response = '';

		while (($line = fgets($socket, 515)) !== false)
		{
			$response .= $line;

			if (strlen($line) >= 4 && $line[3] === ' ')
			{
				break;
			}
		}

		$code = (int) substr($response, 0, 3);

		return $code === $expectedCode;
	}

	private function command($socket, string $command, int $expectedCode): void
	{
		fwrite($socket, $command . "\r\n");

		if (!$this->expect($socket, $expectedCode))
		{
			throw new RuntimeException('SMTP server returned an unexpected response.');
		}
	}

	public function send(string $to, string $asunto, string $message): bool
	{
		$socket = $this->connect();

		if ($socket === false)
		{
			return false;
		}

		try
		{
			if (!$this->expect($socket, 220))
			{
				return false;
			}

			$this->command($socket, 'HELO localhost', 250);

			if ($this->encryption === 'tls')
			{
				$this->command($socket, 'STARTTLS', 220);

				if (!stream_socket_enable_crypto($socket, true, STREAM_CRYPTO_METHOD_TLS_CLIENT))
				{
					return false;
				}

				$this->command($socket, 'HELO localhost', 250);
			}

			if ($this->username !== '')
			{
				$this->command($socket, 'AUTH LOGIN', 334);
				
				$this->command($socket, base64_encode($this->username), 334);

				$this->command($socket, base64_encode($this->password), 235);
			}

			$this->command($socket, 'MAIL FROM:<' . $this->from . '>', 250);

			$this->command($socket, 'RCPT TO:<' . $to . '>', 250);

			$this->command($socket, 'DATA', 354);

			$headers =
				'From: ' . $this->from . "\r\n"
				. 'Reply-To: ' . $this->from . "\r\n"
				. 'To: ' . $to . "\r\n"
				. 'asunto: ' . $asunto . "\r\n"
				. 'MIME-Version: 1.0' . "\r\n"
				. 'Content-Type: text/plain; charset=UTF-8' . "\r\n";

			$data =
				$headers
				. "\r\n"
				. $message
				. "\r\n.\r\n";

			fwrite($socket, $data);

			if (!$this->expect($socket, 250))
			{
				return false;
			}

			$this->command($socket, 'QUIT', 221);

			return true;
		}
		finally
		{
			fclose($socket);
		}
	}

	public function sendVerificationEmail(string $email, string $username, string $verificationUrl): bool
	{
		$asunto = 'Camagru - Verify your email address';

		$message =
			"Hello " . $username . ",\n\n"
			. "Thank you for registering on Camagru.\n\n"
			. "Please verify your email address using this link:\n\n"
			. $verificationUrl . "\n\n"
			. "This link will expire in 24 hours.\n\n"
			. "If you did not create this account, you can ignore this email.\n\n"
			. "Camagru";

		return $this->send($email, $asunto, $message);
	}

	public function sendPasswordResetEmail(string $email, string $username, string $resetUrl): bool
	{
		$asunto = 'Camagru - Password reset';

		$message =
			"Hello " . $username . ",\n\n"
			. "A password reset was requested for your Camagru account.\n\n"
			. "You can reset your password using this link:\n\n"
			. $resetUrl . "\n\n"
			. "This link will expire in 1 hour.\n\n"
			. "If you did not request a password reset, you can ignore this email.\n\n"
			. "Camagru";

		return $this->send($email, $asunto, $message);
	}
}