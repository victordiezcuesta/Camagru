<?php

declare(strict_types=1);

require_once __DIR__ . '/../config/Database.php';
require_once __DIR__ . '/../Security/Csrf.php';
require_once __DIR__ . '/../Security/Session.php';
require_once __DIR__ . '/../Services/Mailer.php';

class GalleryInteractionController
{
	private function requireAuthentication(): void
	{
		Session::start();

		if (!isset($_SESSION['user_id']))
		{
			http_response_code(403);

			$errorTitle = 'Authentication required';
			$errorMessage = 'You must be logged in to perform this action.';

			require __DIR__ . '/../Views/error.php';
			exit;
		}
	}

	public function toggleLike(): void
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

		$imageId = filter_input(INPUT_POST, 'image_id', FILTER_VALIDATE_INT);
		if ($imageId === false || $imageId === null || $imageId < 1)
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
			'SELECT id
			FROM images
			WHERE id = :id'
		);

		$stmt->execute([
			'id' => $imageId
		]);

		if ($stmt->fetch() === false)
		{
			http_response_code(404);

			$errorTitle = 'Image not found';
			$errorMessage = 'The requested image could not be found.';

			require __DIR__ . '/../Views/error.php';
			exit;
		}

		$stmt = $pdo->prepare(
			'SELECT id
			FROM likes
			WHERE user_id = :user_id
			AND image_id = :image_id'
		);

		$stmt->execute([
			'user_id' => $_SESSION['user_id'],
			'image_id' => $imageId
		]);

		$like = $stmt->fetch();

		if ($like !== false)
		{
			$stmt = $pdo->prepare(
				'DELETE FROM likes
				WHERE user_id = :user_id
				AND image_id = :image_id'
			);

			$stmt->execute([
				'user_id' => $_SESSION['user_id'],
				'image_id' => $imageId
			]);
		}
		else
		{
			$stmt = $pdo->prepare(
				'INSERT INTO likes (user_id, image_id)
				VALUES (:user_id, :image_id)'
			);

			$stmt->execute([
				'user_id' => $_SESSION['user_id'],
				'image_id' => $imageId
			]);
		}

		$page = filter_input(INPUT_POST, 'page', FILTER_VALIDATE_INT);

		if ($page === false || $page === null || $page < 1)
			$page = 1;

		header('Location: /gallery?page=' . $page);
		exit;
	}

	public function addComment(): void
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

		$imageId = filter_input(INPUT_POST, 'image_id', FILTER_VALIDATE_INT);
		$content = trim($_POST['content'] ?? '');
		if ($imageId === false || $imageId === null || $imageId < 1)
		{
			http_response_code(400);

			$errorTitle = 'Invalid image';
			$errorMessage = 'The selected image is not valid.';

			require __DIR__ . '/../Views/error.php';
			exit;
		}
		if ($content === '')
		{
			http_response_code(400);

			$errorTitle = 'Invalid comment';
			$errorMessage = 'The comment cannot be empty.';

			require __DIR__ . '/../Views/error.php';
			exit;
		}

		if (mb_strlen($content) > 1000)
		{
			http_response_code(400);

			$errorTitle = 'Invalid comment';
			$errorMessage = 'The comment must contain at most 1000 characters.';

			require __DIR__ . '/../Views/error.php';
			exit;
		}

		$database = new Database();
		$pdo = $database->getConnection();

		$stmt = $pdo->prepare(
			'SELECT
				images.id,
				users.email,
				users.username,
				users.comment_notifications
			FROM images
			INNER JOIN users
				ON users.id = images.user_id
			WHERE images.id = :image_id'
		);

		$stmt->execute([
			'image_id' => $imageId
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

		$stmt = $pdo->prepare(
			'INSERT INTO comments (user_id, image_id, content)
			VALUES (:user_id, :image_id, :content)'
		);

		$stmt->execute([
			'user_id' => $_SESSION['user_id'],
			'image_id' => $imageId,
			'content' => $content
		]);

		$stmt = $pdo->prepare(
			'SELECT username
			FROM users
			WHERE id = :id'
		);

		$stmt->execute([
			'id' => $_SESSION['user_id']
		]);

		$commenter = $stmt->fetch();

		if ($image['comment_notifications'] && $commenter !== false) // && (int) $_SESSION['user_id'] !== $this->getImageOwnerId($pdo, $imageId))
		{
			$imageUrl = rtrim(getenv('APP_URL') ?: 'http://localhost:8080', '/') . '/gallery';

			$mailer = new Mailer();
			$mailer->sendCommentNotificationEmail($image['email'], $image['username'], $commenter['username'], $imageUrl, $content);
		}
		$page = filter_input(INPUT_POST, 'page', FILTER_VALIDATE_INT);

		if ($page === false || $page === null || $page < 1)
			$page = 1;

		header('Location: /gallery?page=' . $page);
		exit;
	}

	/*private function getImageOwnerId(PDO $pdo, int $imageId): int
	{
		$stmt = $pdo->prepare(
			'SELECT user_id
			FROM images
			WHERE id = :image_id'
		);

		$stmt->execute([
			'image_id' => $imageId
		]);

		$userId = $stmt->fetchColumn();
		return $userId === false ? 0 : (int) $userId;
	}*/
}