CREATE TABLE users (
    id INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
	/*id nombre variable
		INT tipo variable
		Unsigned sin simbolo
		auto_increment, que se va incrementando el valor solo es decir id=1 id=2 ...
		PRIMARY KEY no puede haber dos id con el mismo valor
	*/
    username VARCHAR(50) NOT NULL UNIQUE,
      /* VARCHAR(50) cadena de hasta 50 caracteres 
		NOT NULL que el campo es obligatoria
		UNIQUE que tiene que ser unico, no puede haber dos username iguales
	*/

    email VARCHAR(255) NOT NULL UNIQUE,

    password VARCHAR(255) NOT NULL,

    email_verified BOOLEAN NOT NULL DEFAULT FALSE,
    /* un booleano que por defecto es false*/

    created_at TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP
);