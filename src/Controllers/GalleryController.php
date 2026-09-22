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
		$imagesPerPage = 5;
		$page = filter_input(INPUT_GET, 'page', FILTER_VALIDATE_INT);

		if ($page === false || $page === null || $page < 1)
			$page = 1;

		$stmt = $pdo->query(
			'SELECT COUNT(*)
			FROM images'
		);

		$totalImages = (int) $stmt->fetchColumn();

		$totalPages = max(1, (int) ceil($totalImages / $imagesPerPage));
		if ($page > $totalPages)
			$page = $totalPages;

		$offset = ($page - 1) * $imagesPerPage;

		$stmt = $pdo->prepare(
			'SELECT images.id,
					images.filename,
					images.created_at,
					users.username
			FROM images
			INNER JOIN users
				ON users.id = images.user_id
			ORDER BY images.created_at DESC
			LIMIT :limit OFFSET :offset'
		);

		$stmt->bindValue(':limit', $imagesPerPage, PDO::PARAM_INT);
		$stmt->bindValue(':offset', $offset, PDO::PARAM_INT);
		$stmt->execute();

		$images = $stmt->fetchAll();

		require __DIR__ . '/../Views/gallery.php';
	}
}