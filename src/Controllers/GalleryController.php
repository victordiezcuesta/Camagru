<?php

declare(strict_types=1);

require_once __DIR__ . '/../config/Database.php';
require_once __DIR__ . '/../Security/Session.php';
require_once __DIR__ . '/../Security/Csrf.php';

class GalleryController
{
	public function index(): void
	{
		Session::start();

		$csrfToken = Csrf::token();

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

		$userId = $_SESSION['user_id'] ?? null;

		$stmt = $pdo->prepare(
			'SELECT
				images.id,
				images.filename,
				images.created_at,
				users.username,

				(
					SELECT COUNT(*)
					FROM likes
					WHERE likes.image_id = images.id
				) AS like_count,

				EXISTS (
					SELECT 1
					FROM likes
					WHERE likes.image_id = images.id
					AND likes.user_id = :user_id
				) AS user_liked

			FROM images

			INNER JOIN users
				ON users.id = images.user_id

			ORDER BY images.created_at DESC

			LIMIT :limit OFFSET :offset'
		);

		$stmt->bindValue(':user_id', $userId, $userId === null ? PDO::PARAM_NULL : PDO::PARAM_INT);
		$stmt->bindValue(':limit', $imagesPerPage, PDO::PARAM_INT);
		$stmt->bindValue(':offset', $offset, PDO::PARAM_INT);
		$stmt->execute();

		$images = $stmt->fetchAll();

		foreach ($images as &$image)
		{
			$stmt = $pdo->prepare(
				'SELECT
					comments.id,
					comments.content,
					comments.created_at,
					users.username
				FROM comments
				INNER JOIN users
					ON users.id = comments.user_id
				WHERE comments.image_id = :image_id
				ORDER BY comments.created_at ASC'
			);

			$stmt->execute([
				'image_id' => $image['id']
			]);

			$image['comments'] = $stmt->fetchAll();
		}

		unset($image);

		require __DIR__ . '/../Views/gallery.php';
	}
}