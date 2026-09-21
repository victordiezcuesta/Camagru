<?php

declare(strict_types=1);

/*PDO ES QUIEN HACE LA COMUNICACION ENTRE PHP Y MARIADB, ES COMO EL TRADUCTOR ENTRE PHP Y MARIA DB
PDO es una interfaz de acceso a bases de datos
*/

class Database
{
    private PDO $connection; 
    /*$connection debe contener un objeto de tipo PDO*/

    public function __construct()
    {
        $host = getenv('DB_HOST');
        $port = getenv('DB_PORT');
        $name = getenv('DB_NAME');
        $user = getenv('DB_USER');
        $password = getenv('DB_PASSWORD');

        $dsn = "mysql:host=$host;port=$port;dbname=$name;charset=utf8mb4";
	  /*Data Source Name
	  	Es la información que PDO necesita para saber a qué base de datos conectarse
		el host, el puerto, el nombre
		charset=utf8mb4 es el conjunto de caracteres que permite mariadb
		*/

        $this->connection = new PDO(
		$dsn,
		$user,
		$password,
		[
			PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION, //Si ocurre un error con la base de datos, lanza una excepción.
			PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC, //Cuando obtenga datos de MariaDB, quiero recibirlos como arrays asociativos.
			PDO::ATTR_EMULATE_PREPARES => false //PDO que no emule los prepared statements, sino que utilice prepared statements reales cuando el driver lo soporte.
		]
	);
    }

    public function getConnection(): PDO
    {
        return $this->connection;
    }
}