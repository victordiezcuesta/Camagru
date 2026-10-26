<?php

declare(strict_types=1);

class Session
{
	public static function start(): void
	{
		if (session_status() === PHP_SESSION_NONE) //PHP_SESSION_NONE es una constante de PHP que indica que no hay ninguna sesión iniciada actualmente
		{
			session_set_cookie_params([
				'lifetime' => 0, //la cookie dura hasta que se cierra el navegador
				'path' => '/',
				'secure' => false,
				'httponly' => true, //HttpOnly no significa que la cookie esté cifrada. Simplemente impide que JavaScript acceda a ella.
				'samesite' => 'Lax' //Limita el envío de la cookie desde otros sitios
			]);

			session_start(); //función de PHP que inicia una sesión nueva o recupera una sesión existente
		}
	}
}