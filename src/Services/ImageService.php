<?php

declare(strict_types=1);

class ImageService
{
	private const UPLOAD_DIRECTORY = __DIR__ . '/../../public/uploads/';
	private const MAX_FILE_SIZE = 5 * 1024 * 1024; // maximo 5 MB

	private const ALLOWED_MIME_TYPES = [
		'image/jpeg' => 'jpg',
		'image/png' => 'png'
	];

	public function saveUploadedImage(array $file): string
	{
		if (!isset($file['error']) || $file['error'] !== UPLOAD_ERR_OK)
			throw new RuntimeException('Unable to upload image.');
		if (!isset($file['tmp_name']) || !is_uploaded_file($file['tmp_name']))
			throw new RuntimeException('Invalid uploaded file.');
		if ($file['size'] > self::MAX_FILE_SIZE)
			throw new RuntimeException('Image is too large.');

		$finfo = new finfo(FILEINFO_MIME_TYPE); //naliza realmente el contenido del archivo por seguridad
		$mimeType = $finfo->file($file['tmp_name']);

		if (!isset(self::ALLOWED_MIME_TYPES[$mimeType]))
			throw new RuntimeException('Invalid image type.');

		$imageInfo = getimagesize($file['tmp_name']); //comprobamos que realmente sea una imagen
		if ($imageInfo === false)
			throw new RuntimeException('Invalid image.');

		$extension = self::ALLOWED_MIME_TYPES[$mimeType];

		$filename = bin2hex(random_bytes(32)) . '.' . $extension; //cambiamos el nombre a la foto por si suben dos fotos con el mismo nobre

		$destination = self::UPLOAD_DIRECTORY . $filename;
		if (!move_uploaded_file($file['tmp_name'], $destination))
			throw new RuntimeException('Unable to save image.');

		return $filename;
	}
}