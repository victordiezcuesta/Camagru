<?php

declare(strict_types=1);

require_once __DIR__ . '/../config/Database.php';
require_once __DIR__ . '/../Security/Session.php';
require_once __DIR__ . '/../Security/Csrf.php';

class GalleryController
{
	private const IMAGES_PER_PAGE = 5;

	public function index(): void //obtenemos las imagenes para la pagina solicitada
	{
		Session::start();

		$csrfToken = Csrf::token();

		$database = new Database();
		$pdo = $database->getConnection();

		$page = filter_input(INPUT_GET, 'page', FILTER_VALIDATE_INT); //cogemos la pagina que estamos solicitando mediante la url buscando en la url la palabra page y verificando que es un numero

		if ($page === false || $page === null || $page < 1)
			$page = 1;

		$totalImages = $this->getTotalImages($pdo);

		$totalPages = max(1, (int) ceil($totalImages / self::IMAGES_PER_PAGE));

		$invalidPage = $page > $totalPages;
		if ($invalidPage)
			$images = [];
		else
			$images = $this->getImages($pdo, $page);

		require __DIR__ . '/../Views/gallery.php';
	}

	public function loadMore(): void //cargamos mas imagenes para la paginacion infinita
	{
		Session::start();

		$csrfToken = Csrf::token();

		$database = new Database();
		$pdo = $database->getConnection();

		$page = filter_input(INPUT_GET, 'page', FILTER_VALIDATE_INT);

		if ($page === false || $page === null || $page < 1)
			$page = 1;

		$totalImages = $this->getTotalImages($pdo);

		$totalPages = max(1, (int) ceil($totalImages / self::IMAGES_PER_PAGE));
		if ($page > $totalPages)
			$page = $totalPages;

		$images = $this->getImages($pdo, $page);

		ob_start(); //activamos el output buffering: todo lo que se imprima a partir de aquí no va al navegador, se guarda en memoria

		require __DIR__ . '/../Views/gallery-cards.php';

		$html = ob_get_clean(); //metemos todo lo anterior ,lo que renderizariamos pero al activar el buffer no lo hacemos y lo metemos todo el en buffer

		header('Content-Type: application/json; charset=UTF-8'); //avisamos al navegador de que la respuesta va a ser un json

		echo json_encode([ //json_encode: convierte el array de PHP a un string JSON
			'html' => $html,
			'hasMore' => $page < $totalPages,
			'nextPage' => $page + 1
		]);

		exit;
	}

	private function getTotalImages(PDO $pdo): int
	{
		$stmt = $pdo->query(
			'SELECT COUNT(*)
			FROM images'
		);

		return (int) $stmt->fetchColumn();
	}

	private function getImages(PDO $pdo, int $page): array
	{
		$offset = ($page - 1) * self::IMAGES_PER_PAGE;

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

		$stmt->bindValue(
			':user_id',
			$userId,
			$userId === null
				? PDO::PARAM_NULL
				: PDO::PARAM_INT
		);

		$stmt->bindValue( //en el hueco de limit de la preparacion de la base de datos mete el valor 5 y tratalo commo entero
			':limit',
			self::IMAGES_PER_PAGE,
			PDO::PARAM_INT
		);

		$stmt->bindValue(
			':offset',
			$offset,
			PDO::PARAM_INT
		);

		$stmt->execute();

		$images = $stmt->fetchAll(); //coge todos los datos que tenemos ahora en el stmt de la base de datos y lo mete en un array como por ejemplo ['id' => 12, 'filename' => 'a.png', 'username' => 'victor', 'like_count' => 3, 'user_liked' => 1]

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

		unset($image); //Después de un foreach por referencia, $image sigue siendo una referencia al último elemento del array. Si la reutilizas más adelante, puedes sobrescribir ese elemento sin querer. unset rompe esa referencia

		return $images;
	}
}