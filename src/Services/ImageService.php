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
		'01' => '01_laptop_programacion.png',
		'02' => '02_42_madrid.png',
		'03' => '03_devs_no_duermen.png',
		'04' => '04_gaming.png',
		'05' => '05_viajes_montana.png',
		'06' => '06_cafe_programador.png',
		'07' => '07_minecraft_pixel.png',
		'08' => '08_linux_forever.png',
		'09' => '09_ramen.png',
		'10' => '10_tu_puedes.png',
		'11' => '11_tiburon_good_vibes.png',
		'12' => '12_astroespacio.png',
		'13' => '13_terminal_keep_going.png',
		'14' => '14_good_boy_42.png',
		'15' => '15_disciplina_montana.png',
		'16' => '16_coder_sonoliento.png',
		'17' => '17_banana_lets_go.png',
		'18' => '18_42_cursor.png',
		'19' => '19_pizza.png',
		'20' => '20_cactus.png',
		'21' => '21_dog.png',
		'22' => '22_gafas_bigote.png',
		'23' => '23_ojos.png',
		'101' => '101_playa_tropical.png',
		'102' => '102_romantico_kawaii.png',
		'103' => '103_cine_film.png',
		'104' => '104_aventura_montana.png',
		'105' => '105_halloween.png'
	];

		private const RANDOM_POSITION_OVERLAYS = [
		'01', '02', '03', '04', '05',
		'06', '07', '08', '09', '10',
		'11', '12', '13', '14', '15',
		'16', '17', '18', '19', '20'
	];

	private const OVERLAY_SIZE = 500;
	private const OVERLAY_MARGIN = 300;

	public function saveUploadedImage(array $file, string $overlay, ?int $overlayX = null, ?int $overlayY = null): string
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

			imagealphablending($sourceImage, true); //le decimos a GD que cuando coloquemos el overlay sobre la fotografía, debe respetar la transparencia

			if (in_array($overlay, self::RANDOM_POSITION_OVERLAYS, true))
			{
				$overlayWidth = min(self::OVERLAY_SIZE, $width, $height);
				$margin = min(self::OVERLAY_MARGIN, intdiv($width - $overlayWidth, 2), intdiv($height - $overlayWidth, 2));
				if ($margin < 0)
					$margin = 0;

				$overlayWidth = min($overlayWidth, $width - ($margin * 2), $height - ($margin * 2));

				$overlayHeight = $overlayWidth;

				$positions = [
					[
						'x' => $margin,
						'y' => $margin
					],
					[
						'x' => $margin,
						'y' => max(0, $height - $overlayHeight - $margin)
					],
					[
						'x' => max(0, $width - $overlayWidth - $margin),
						'y' => $margin
					],
					[
						'x' => max(0, $width - $overlayWidth - $margin),
						'y' => max(0, $height - $overlayHeight - $margin)
					]
				];

				if ($overlayX === null || $overlayY === null)
				{
					imagedestroy($overlayImage);
					imagedestroy($sourceImage);
					throw new RuntimeException('Invalid overlay position.');
				}

				$validPosition = false;

				foreach ($positions as $position)
				{
					if ($position['x'] === $overlayX && $position['y'] === $overlayY)
					{
						$validPosition = true;
						break;
					}
				}

				if (!$validPosition)
				{
					imagedestroy($overlayImage);
					imagedestroy($sourceImage);
					throw new RuntimeException('Invalid overlay position.');
				}

				imagecopy($sourceImage, $overlayImage, $overlayX, $overlayY, 0, 0, $overlayWidth, $overlayHeight);
			}
			else
			{
				$overlayWidth = imagesx($overlayImage);
				$overlayHeight = imagesy($overlayImage);
				imagecopyresampled($sourceImage, $overlayImage, 0, 0, 0, 0, $width, $height, $overlayWidth, $overlayHeight); //Redimensionamos el overlay y lo colocamos directamente sobre la fotografia
			}

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