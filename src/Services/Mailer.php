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
			$transport = 'ssl://';

		$address = $transport
				. $this->host
				. ':'
				. $this->port;

		$errorCode = 0;
		$errorMessage = '';

		$socket = stream_socket_client($address, $errorCode, $errorMessage, 10);
		if ($socket !== false)
			stream_set_timeout($socket, 10);

		return $socket;
	}

	private function expect($socket, int $expectedCode): bool
	{
		$response = '';

		while (($line = fgets($socket, 515)) !== false)
		{
			$response .= $line; // coge lo que hay dentro de response y añade lo que hay en line, es decir que concatena los strings

			if (strlen($line) >= 4 && $line[3] === ' ')
				break;
		}

		if ($response === '')
			return false;

		$code = (int) substr($response, 0, 3);

		return $code === $expectedCode;
	}

	private function command($socket, string $command, int $expectedCode): void
	{
		$written = fwrite($socket, $command . "\r\n");

		if ($written === false)
			throw new RuntimeException('Unable to communicate with SMTP server.');

		if (!$this->expect($socket, $expectedCode))
			throw new RuntimeException('SMTP command failed.');
	}

	private function prepareMessage(string $message): string
	{
		$lines = preg_split("/\r\n|\r|\n/", $message); //dividir un texto largo (la variable $message) en un arreglo de líneas individuales

		$result = [];

		foreach ($lines as $line) //Por cada línea que haya en el conjunto de líneas, haz algo con esa línea
		{
			if (str_starts_with($line, '.')) //modifica las líneas que ya empiezan con un punto, agregándoles un segundo punto al principio
				$line = '.' . $line;

			$result[] = $line;
		}

		return implode("\r\n", $result); //junta todas las líneas modificadas de $result y las une de nuevo en un solo bloque de texto largo, separadas por saltos de línea (\r\n).
	}

	public function send(string $to, string $subject, string $message): bool
	{
		$socket = $this->connect();
		if ($socket === false)
			return false;

		try
		{
			if (!$this->expect($socket, 220)) //220 significa servidor listo
				return false;

			$this->command($socket, 'EHLO localhost', 250); //EHLO localhost es el comando inicial obligatorio que se utiliza para iniciar la conversación entre un cliente de correo y un servidor SMTP.

			if ($this->encryption === 'tls')
			{
				$this->command($socket, 'STARTTLS', 220); //STARTTLS sirve para convertir una conexión insegura de texto plano en una conexión 100% segura y cifrada

				$cryptoEnabled = stream_socket_enable_crypto($socket, true, STREAM_CRYPTO_METHOD_TLS_CLIENT);

				if ($cryptoEnabled !== true)
					return false;

				$this->command($socket, 'EHLO localhost', 250); //250 REspuesta de todo ok
			}

			if ($this->username !== '')
			{
				$this->command($socket, 'AUTH LOGIN', 334); //334 es de autentificacion de usuario y contraseña

				$this->command($socket, base64_encode($this->username), 334);

				$this->command($socket, base64_encode($this->password), 235);
			}

			$this->command($socket, 'MAIL FROM:<' . $this->from . '>', 250);

			$this->command($socket, 'RCPT TO:<' . $to . '>', 250);

			$this->command($socket, 'DATA', 354); //354 es iniciar entrada de correo

			$headers =
				'From: ' . $this->from . "\r\n"
				. 'Reply-To: ' . $this->from . "\r\n"
				. 'To: ' . $to . "\r\n"
				. 'Subject: ' . $subject . "\r\n"
				. 'MIME-Version: 1.0' . "\r\n"
				. 'Content-Type: text/plain; charset=UTF-8' . "\r\n";

			$body = $this->prepareMessage($message);

			$data =
				$headers
				. "\r\n"
				. $body
				. "\r\n.\r\n";

			$written = fwrite($socket, $data);

			if ($written === false)
				return false;

			if (!$this->expect($socket, 250))
				return false;

			$this->command($socket, 'QUIT', 221); // 221 es cierre del canal de comunicacoin

			return true;
		}
		catch (Throwable $exception)
		{
			return false;
		}
		finally
		{
			fclose($socket);
		}
	}

	public function sendVerificationEmail(string $email, string $username, string $verificationUrl): bool
	{
		$subject = 'Camagru - Verify your email address';
		$message =
			"Hello " . $username . ",\n\n"
			. "Thank you for registering on Camagru.\n\n"
			. "Please verify your email address using this link:\n\n"
			. $verificationUrl . "\n\n"
			. "This link will expire in 24 hours.\n\n"
			. "If you did not create this account, "
			. "you can ignore this email.\n\n"
			. "Camagru";

		return $this->send($email, $subject, $message);
	}

	public function sendPasswordResetEmail(string $email, string $username, string $resetUrl): bool
	{
		$subject = 'Camagru - Password reset';
		$message =
			"Hello " . $username . ",\n\n"
			. "A password reset was requested "
			. "for your Camagru account.\n\n"
			. "You can reset your password using "
			. "this link:\n\n"
			. $resetUrl . "\n\n"
			. "This link will expire in 1 hour.\n\n"
			. "If you did not request a password reset, "
			. "you can ignore this email.\n\n"
			. "Camagru";

		return $this->send($email, $subject, $message);
	}

	public function sendEmailChangeVerificationEmail(string $email, string $username, string $verificationUrl): bool
	{
		$subject = 'Camagru - Verify your new email address';
		$message =
			"Hello " . $username . ",\n\n"
			. "Your Camagru email address has been changed.\n\n"
			. "Please verify your new email address using "
			. "this link:\n\n"
			. $verificationUrl . "\n\n"
			. "This link will expire in 24 hours.\n\n"
			. "If you did not request this change, "
			. "please contact the administrator.\n\n"
			. "Camagru";

		return $this->send($email, $subject, $message);
	}

	public function sendCommentNotificationEmail(string $email, string $username, string $commenterUsername, string $imageUrl, string $comment): bool
	{
		$subject = 'Camagru - New comment on your photo';
		$message =
			"Hello " . $username . ",\n\n"
			. $commenterUsername . " commented on your photo.\n\n"
			. "Comment:\n"
			. $comment . "\n\n"
			. "View your gallery:\n"
			. $imageUrl . "\n\n"
			. "Camagru";

		return $this->send($email, $subject, $message);
	}
}