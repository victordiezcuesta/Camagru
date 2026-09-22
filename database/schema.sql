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

	comment_notifications BOOLEAN NOT NULL DEFAULT TRUE,

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

CREATE TABLE likes (
	id INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,

	user_id INT UNSIGNED NOT NULL,

	image_id INT UNSIGNED NOT NULL,

	created_at TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP,

	CONSTRAINT fk_likes_user
		FOREIGN KEY (user_id)
		REFERENCES users(id)
		ON DELETE CASCADE,

	CONSTRAINT fk_likes_image
		FOREIGN KEY (image_id)
		REFERENCES images(id)
		ON DELETE CASCADE,

	CONSTRAINT unique_user_image_like
		UNIQUE (user_id, image_id) /*Es para que las fotos solo puedan tener un like por usuario*/
);

CREATE TABLE comments (
	id INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,

	user_id INT UNSIGNED NOT NULL,

	image_id INT UNSIGNED NOT NULL,

	content TEXT NOT NULL,

	created_at TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP,

	CONSTRAINT fk_comments_user
		FOREIGN KEY (user_id)
		REFERENCES users(id)
		ON DELETE CASCADE,

	CONSTRAINT fk_comments_image
		FOREIGN KEY (image_id)
		REFERENCES images(id)
		ON DELETE CASCADE
);