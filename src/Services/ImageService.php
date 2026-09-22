<?php

declare(strict_types=1);

class ImageService
{
	private const UPLOAD_DIRECTORY = __DIR__ . '/../../public/uploads/';
	private const OVERLAY_DIRECTORY = __DIR__ . '/../../public/assets/overlays/'; //__DIR__ representa la carpeta donde esta el php
	private const MAX_FILE_SIZE = 5 * 1024 * 1024; // maximo 5 MB

	private const ALLOWED_MIME_TYPES = [
		'image/jpeg' => 'jpg',
		'image/png' => 'png'
	];

	private const ALLOWED_OVERLAYS = [
		'overlay1' => 'overlay1.png',
		'overlay2' => 'overlay2.png',
		'overlay3' => 'overlay3.png'
	];

	public function saveUploadedImage(array $file, string $overlay): string
	{
		if (!isset($file['error']) || $file['error'] !== UPLOAD_ERR_OK)
			throw new RuntimeException('Unable to upload image.');

		if (!isset($file['tmp_name']) || !is_uploaded_file($file['tmp_name']))
			throw new RuntimeException('Invalid uploaded file.');

		if ($file['size'] > self::MAX_FILE_SIZE)
			throw new RuntimeException('Image is too large.');

		$finfo = new finfo(FILEINFO_MIME_TYPE); //naliza realmente el contenido del archivo por seguridad
		$mimeType = $finfo->file($file['tmp_name']);

		if (!isset(self::ALLOWED_MIME_TYPES[$mimeType])) //self::ALLOWED_MIME_TYPES se refiere a las variables que hemos declarado arriba
			throw new RuntimeException('Invalid image type.');

		$imageInfo = getimagesize($file['tmp_name']); //comprobamos que realmente sea una imagen

		if ($imageInfo === false)
			throw new RuntimeException('Invalid image.');

		if (!isset(self::ALLOWED_OVERLAYS[$overlay]))
			throw new RuntimeException('Invalid overlay.');

		$overlayPath = self::OVERLAY_DIRECTORY . self::ALLOWED_OVERLAYS[$overlay]; //ruta exacta de los overlays

		if (!is_file($overlayPath))
			throw new RuntimeException('Overlay not found.');

		$sourceImage = $this->createImageFromFile($file['tmp_name'], $mimeType);

		$overlayImage = imagecreatefrompng($overlayPath); //Lee el overlay y crea en memoria una representación de esa imagen que PHP puede modificar y sea transparente

		if ($overlayImage === false)
		{
			imagedestroy($sourceImage); //liberamos la memoria de la funcion imagecreatefrompng
			throw new RuntimeException('Unable to load overlay.');
		}

		$width = imagesx($sourceImage); //calcula los tamaños de las fotos
		$height = imagesy($sourceImage);

		$overlayWidth = imagesx($overlayImage);
		$overlayHeight = imagesy($overlayImage);

		$resizedOverlay = imagecreatetruecolor($width, $height); //creamos una imagen nueva de overlay vacia del tamaño de la foto principal

		if ($resizedOverlay === false)
		{
			imagedestroy($sourceImage);
			imagedestroy($overlayImage);
			throw new RuntimeException('Unable to create image.');
		}

		imagealphablending($resizedOverlay, false); //Vamos a trabajar con el canal transparencia de forma explícita
		imagesavealpha($resizedOverlay, true); //Conserva la información de transparencia
		$transparent = imagecolorallocatealpha($resizedOverlay, 0, 0, 0, 127); //creamos un color transparente, 0 0 0 RGB, 127=transparencia máxima en GD
		imagefill($resizedOverlay, 0, 0, $transparent); //rellena toda la imagen con ese color transparente
		imagecopyresampled($resizedOverlay, $overlayImage, 0, 0, 0, 0, $width, $height, $overlayWidth, $overlayHeight); //Redimensionamos el overlay con el otro overlay copia
		imagealphablending($sourceImage, true); //le decimos a GD que cuando coloquemos el overlay sobre la fotografía, debe respetar la transparencia
		imagecopy($sourceImage, $resizedOverlay, 0, 0, 0, 0, $width, $height); //ponemos el overlay redimensionado encima de la fotografia principal

		$filename = bin2hex(random_bytes(32)) . '.jpg'; //cambiamos el nombre a la foto por si suben dos fotos con el mismo nobre

		$destination = self::UPLOAD_DIRECTORY . $filename;

		if (!imagejpeg($sourceImage, $destination, 90)) //convierte la imagen final en memoria en un archivo JPEG | 90=calidad JPEG
		{
			imagedestroy($sourceImage);
			imagedestroy($overlayImage);
			imagedestroy($resizedOverlay);

			throw new RuntimeException('Unable to save image.');
		}

		imagedestroy($sourceImage);
		imagedestroy($overlayImage);
		imagedestroy($resizedOverlay);

		return $filename;
	}

	private function createImageFromFile(string $filepath, string $mimeType)
	{
		if ($mimeType === 'image/jpeg')
			$image = imagecreatefromjpeg($filepath); //Lee el archivo JPEG y crea en memoria una representación de esa imagen que PHP puede modificar
		else
			$image = imagecreatefrompng($filepath);

		if ($image === false)
			throw new RuntimeException('Unable to load image.');

		return $image;
	}
}