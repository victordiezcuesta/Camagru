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
			echo 'Invalid CSRF token.';
			exit;
		}

		if (!isset($_FILES['image'])) //$_FILES =Es un array especial de PHP con información sobre el archivo
		{
			http_response_code(400);
			echo 'Image is required.';
			exit;
		}

		$overlay = $_POST['overlay'] ?? ''; //busca el overlay seleccionado desde el js y el html
		/*if ($overlay === '')
		{
			http_response_code(400);
			echo 'Overlay is required.';
			exit;
		}*/

		$imageService = new ImageService();

		try
		{
			$filename = $imageService->saveUploadedImage($_FILES['image'], $overlay);
		}
		catch (RuntimeException $exception)
		{
			http_response_code(400);
			echo htmlspecialchars($exception->getMessage(), ENT_QUOTES, 'UTF-8');
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
			$filepath =
				__DIR__
				. '/../../public/uploads/'
				. $filename;

			if (is_file($filepath))
				unlink($filepath); //borra el archivo que acabamos de subir.

			http_response_code(500);
			echo 'Unable to save image.';
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
			echo 'Invalid CSRF token.';
			exit;
		}

		$imageId = filter_var(
			$_POST['image_id'] ?? null,
			FILTER_VALIDATE_INT
		);

		if ($imageId === false || $imageId <= 0)
		{
			http_response_code(400);
			echo 'Invalid image.';
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
			echo 'Image not found.';
			exit;
		}

		$filename = $image['filename'];

		if (basename($filename) !== $filename) //evita aceptar un nombre que intente salir de public/uploads/, por ejemplo mediante una ruta manipulada
		{
			http_response_code(500);
			echo 'Invalid image filename.';
			exit;
		}

		$filepath = __DIR__ . '/../../public/uploads/' . $filename;

		if (is_file($filepath) && !unlink($filepath))
		{
			http_response_code(500);
			echo 'Unable to delete image.';
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