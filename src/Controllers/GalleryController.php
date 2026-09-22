<?php

declare(strict_types=1);

require_once __DIR__ . '/../config/Database.php';
require_once __DIR__ . '/../Security/Session.php';

class GalleryController
{
	public function index(): void
	{
		Session::start();

		$database = new Database();
		$pdo = $database->getConnection();

		$stmt = $pdo->prepare(
			'SELECT images.id,
					images.filename,
					images.created_at,
					users.username
			FROM images
			INNER JOIN users
				ON users.id = images.user_id
			ORDER BY images.created_at DESC'
		);

		$stmt->execute();

		$images = $stmt->fetchAll();

		require __DIR__ . '/../Views/gallery.php';
	}
}