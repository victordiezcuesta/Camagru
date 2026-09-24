<?php

declare(strict_types=1);

require_once __DIR__ . '/../config/Database.php';
require_once __DIR__ . '/../Security/Csrf.php';
require_once __DIR__ . '/../Security/Session.php';
require_once __DIR__ . '/../Services/ImageService.php';

class PhotoController
{
	private function requireAuthentication(): void
	{
		Session::start();

		if (!isset($_SESSION['user_id']))
		{
			header('Location: /login');
			exit;
		}
	}

	public function create(): void
	{
		$this->requireAuthentication();

		$csrfToken = Csrf::token();

		$database = new Database();
		$pdo = $database->getConnection();

		$stmt = $pdo->prepare(
			'SELECT
				id,
				filename,
				created_at
			FROM images
			WHERE user_id = :user_id
			ORDER BY created_at DESC'
		);

		$stmt->execute([
			'user_id' => $_SESSION['user_id']
		]);

		$previousImages = $stmt->fetchAll();

		require __DIR__ . '/../Views/photo/create.php'; //incluye ese archivo todas las veces que lo pongas, si pones _once solo lo incluye una vez
	}

	public function store(): void
	{
		$this->requireAuthentication();

		if (!Csrf::validate($_POST['csrf_token'] ?? null))
		{
			http_response_code(403);

			$errorTitle = 'Invalid request';
			$errorMessage = 'The security token is invalid or has expired. Please try again.';

			require __DIR__ . '/../Views/error.php';
			exit;
		}

		if (!isset($_FILES['image'])) //$_FILES =Es un array especial de PHP con información sobre el archivo
		{
			http_response_code(400);

			$errorTitle = 'Image required';
			$errorMessage = 'Please select or capture an image before continuing.';

			require __DIR__ . '/../Views/error.php';
			exit;
		}

		$overlay = $_POST['overlay'] ?? ''; //busca el overlay seleccionado desde el js y el html
		if ($overlay === '')
		{
			http_response_code(400);

			$errorTitle = 'Overlay required';
			$errorMessage = 'Please select an overlay before taking a photo.';

			require __DIR__ . '/../Views/error.php';
			exit;
		}

		$overlayX = null;
		$overlayY = null;

		if (in_array($overlay,
			[
				'01', '02', '03', '04', '05',
				'06', '07', '08', '09', '10',
				'11', '12', '13', '14', '15',
				'16', '17', '18', '19', '20'
			], true))
		{
			if (!isset($_POST['overlay_x'], $_POST['overlay_y']))
			{
				http_response_code(400);

				$errorTitle = 'Invalid overlay position';
				$errorMessage = 'The selected overlay position is invalid.';

				require __DIR__ . '/../Views/error.php';
				exit;
			}

			$overlayX = filter_var($_POST['overlay_x'], FILTER_VALIDATE_INT);
			$overlayY = filter_var($_POST['overlay_y'], FILTER_VALIDATE_INT);

			if ($overlayX === false || $overlayY === false)
			{
				http_response_code(400);

				$errorTitle = 'Invalid overlay position';
				$errorMessage = 'The selected overlay position is invalid.';

				require __DIR__ . '/../Views/error.php';
				exit;
			}
		}

		$imageService = new ImageService();

		try
		{
			$filename = $imageService->saveUploadedImage($_FILES['image'], $overlay, $overlayX, $overlayY);
		}
		catch (RuntimeException $exception)
		{
			http_response_code(400);

			$errorTitle = 'Unable to process image';
			$errorMessage = $exception->getMessage();

			require __DIR__ . '/../Views/error.php';
			exit;
		}

		$database = new Database();
		$pdo = $database->getConnection();

		try
		{
			$stmt = $pdo->prepare(
				'INSERT INTO images (
					user_id,
					filename
				)
				VALUES (
					:user_id,
					:filename
				)'
			);

			$stmt->execute([
				'user_id' => $_SESSION['user_id'],
				'filename' => $filename
			]);
		}
		catch (Throwable $exception)
		{
			$filepath = __DIR__ . '/../../public/uploads/' . $filename;
			if (is_file($filepath))
				unlink($filepath); //borra el archivo que acabmos de subir

			http_response_code(500);

			$errorTitle = 'Unable to save image';
			$errorMessage = 'The image could not be saved. Please try again.';

			require __DIR__ . '/../Views/error.php';
			exit;
		}

		header('Location: /gallery');
		exit;
	}

	public function delete(): void
	{
		$this->requireAuthentication();

		if (!Csrf::validate($_POST['csrf_token'] ?? null))
		{
			http_response_code(403);

			$errorTitle = 'Invalid request';
			$errorMessage = 'The security token is invalid or has expired. Please try again.';

			require __DIR__ . '/../Views/error.php';
			exit;
		}

		$imageId = filter_var($_POST['image_id'] ?? null, FILTER_VALIDATE_INT);
		if ($imageId === false || $imageId <= 0)
		{
			http_response_code(400);

			$errorTitle = 'Invalid image';
			$errorMessage = 'The selected image is not valid.';

			require __DIR__ . '/../Views/error.php';
			exit;
		}

		$database = new Database();
		$pdo = $database->getConnection();

		$stmt = $pdo->prepare(
			'SELECT
				id,
				filename
			FROM images
			WHERE id = :image_id
			AND user_id = :user_id'
		);

		$stmt->execute([
			'image_id' => $imageId,
			'user_id' => $_SESSION['user_id']
		]);

		$image = $stmt->fetch();

		if ($image === false)
		{
			http_response_code(404);

			$errorTitle = 'Image not found';
			$errorMessage = 'The requested image could not be found.';

			require __DIR__ . '/../Views/error.php';
			exit;
		}

		$filename = $image['filename'];

		if (basename($filename) !== $filename) //evita aceptar un nombre que intente salir de public/uploads/, por ejemplo mediante una ruta manipulada
		{
			http_response_code(500);

			$errorTitle = 'Invalid image filename';
			$errorMessage = 'The image filename is invalid.';

			require __DIR__ . '/../Views/error.php';
			exit;
		}

		$filepath = __DIR__ . '/../../public/uploads/' . $filename;

		if (is_file($filepath) && !unlink($filepath))
		{
			http_response_code(500);

			$errorTitle = 'Unable to delete image';
			$errorMessage = 'The image could not be deleted. Please try again.';

			require __DIR__ . '/../Views/error.php';
			exit;
		}

		$stmt = $pdo->prepare(
			'DELETE FROM images
			WHERE id = :image_id
			AND user_id = :user_id'
		);

		$stmt->execute([
			'image_id' => $imageId,
			'user_id' => $_SESSION['user_id']
		]);

		header('Location: /photo/create');
		exit;
	}
}