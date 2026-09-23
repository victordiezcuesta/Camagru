<?php

declare(strict_types=1);

class ImageService
{
	private const UPLOAD_DIRECTORY = __DIR__ . '/../../public/uploads/';
	private const OVERLAY_DIRECTORY = __DIR__ . '/../../public/assets/overlays/'; //__DIR__ representa la carpeta donde esta el php
	private const MAX_FILE_SIZE = 5 * 1024 * 1024; // maximo 5 MB
	private const MAX_IMAGE_WIDTH = 1920; //limitas el ancho y el alto de la imagen por el tema de la memoria del gd
	private const MAX_IMAGE_HEIGHT = 1440;

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

		$width = $imageInfo[0];
		$height = $imageInfo[1];
		if ($width <= 0 || $height <= 0)
			throw new RuntimeException('Invalid image dimensions.');
		// Si son demasiado grandes las imagenes las redimensionamos
		if ($overlay !== '' && !isset(self::ALLOWED_OVERLAYS[$overlay]))
			throw new RuntimeException('Invalid overlay.');

		$sourceImage = $this->createImageFromFile($file['tmp_name'], $mimeType);
		$sourceImage = $this->resizeImageIfNeeded($sourceImage); //redimensionamos el tamaño de la imagen si fuera necesario

		if ($overlay !== '')
		{
			$overlayPath = self::OVERLAY_DIRECTORY . self::ALLOWED_OVERLAYS[$overlay]; //ruta exacta de los overlays

			if (!is_file($overlayPath))
			{
				imagedestroy($sourceImage);
				throw new RuntimeException('Overlay not found.');
			}

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

			imagealphablending($sourceImage, true); //le decimos a GD que cuando coloquemos el overlay sobre la fotografía, debe respetar la transparencia
			imagecopyresampled($sourceImage, $overlayImage, 0, 0, 0, 0, $width, $height, $overlayWidth, $overlayHeight); //Redimensionamos el overlay y lo colocamos directamente sobre la fotografia
			imagedestroy($overlayImage); //liberamos la memoria del overlay
		}

		$filename = bin2hex(random_bytes(32)) . '.jpg'; //cambiamos el nombre a la foto por si suben dos fotos con el mismo nobre

		$destination = self::UPLOAD_DIRECTORY . $filename;

		if (!imagejpeg($sourceImage, $destination, 90)) //convierte la imagen final en memoria en un archivo JPEG | 90=calidad JPEG
		{
			imagedestroy($sourceImage);
			throw new RuntimeException('Unable to save image.');
		}

		imagedestroy($sourceImage);
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

	private function resizeImageIfNeeded($image)
	{
		$width = imagesx($image);
		$height = imagesy($image);

		if ($width <= self::MAX_IMAGE_WIDTH && $height <= self::MAX_IMAGE_HEIGHT)
			return $image;

		$scale = min(self::MAX_IMAGE_WIDTH / $width, self::MAX_IMAGE_HEIGHT / $height);
		$newWidth = (int) round($width * $scale);
		$newHeight = (int) round($height * $scale);

		$resizedImage = imagecreatetruecolor($newWidth, $newHeight);

		if ($resizedImage === false)
		{
			imagedestroy($image);
			throw new RuntimeException('Unable to resize image.');
		}

		imagecopyresampled($resizedImage, $image, 0, 0, 0, 0, $newWidth, $newHeight, $width, $height);
		imagedestroy($image);

		return $resizedImage;
	}
}