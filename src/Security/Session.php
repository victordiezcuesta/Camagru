<?php

declare(strict_types=1);

class Session
{
    public static function start(): void
    {
        if (session_status() === PHP_SESSION_NONE)
        {
            session_set_cookie_params([
                'lifetime' => 0,
                'path' => '/',
                'secure' => false,
                'httponly' => true, //HttpOnly no significa que la cookie esté cifrada. Simplemente impide que JavaScript acceda a ella.
                'samesite' => 'Lax' //Limita el envío de la cookie desde otros sitios
            ]);

            session_start();
        }
    }
}