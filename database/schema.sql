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

	verification_token VARCHAR(64) DEFAULT NULL,
	verification_expires_at DATETIME DEFAULT NULL,

	password_reset_token VARCHAR(64) DEFAULT NULL,
	password_reset_expires_at DATETIME DEFAULT NULL,

	created_at TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP
);

CREATE TABLE images (
	id INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,

	user_id INT UNSIGNED NOT NULL,

	filename VARCHAR(255) NOT NULL,

	created_at TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP,

	CONSTRAINT fk_images_user
		FOREIGN KEY (user_id) /*La columna user_id de la tabla images será una clave foránea, es decir que dice que imagen pertenece a cada usuario y relaciona las dos tablas*/
		REFERENCES users(id) /*El user_id de images debe corresponder a un id existente en users*/
		ON DELETE CASCADE /*Si se elimina un usuario, se eliminarán automáticamente todas las imágenes que pertenecen a ese usuario*/
);