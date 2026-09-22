<!DOCTYPE html>
<html lang="en">

<head>

    <meta charset="UTF-8">

    <meta
        name="viewport"
        content="width=device-width, initial-scale=1.0"
    >

    <title>Camagru - Gallery</title>

    <link
        rel="stylesheet"
        href="/assets/css/style.css"
    >

</head>

<body>

    <header class="site-header">

        <div class="container">

            <a href="/" class="logo">
                Camagru
            </a>

            <?php if (isset($_SESSION['user_id'])): ?>

                <a href="/photo/create">
                    New photo
                </a>

                <a href="/profile">
                    Profile
                </a>

            <?php else: ?>

                <a href="/login">
                    Login
                </a>

            <?php endif; ?>

        </div>

    </header>

    <main class="main-content">

        <section class="gallery-section">

            <h1>Gallery</h1>

            <?php if (empty($images)): ?>

                <p class="auth-subtitle">
                    No photos have been uploaded yet.
                </p>

            <?php else: ?>

                <div class="gallery-grid">

                    <?php foreach ($images as $image): ?>

                        <article class="gallery-card">

                            <img
                                src="/uploads/<?= htmlspecialchars(
                                    $image['filename'],
                                    ENT_QUOTES,
                                    'UTF-8'
                                ) ?>"
                                alt="Photo uploaded by <?= htmlspecialchars(
                                    $image['username'],
                                    ENT_QUOTES,
                                    'UTF-8'
                                ) ?>"
                            >

                            <div class="gallery-card-info">

                                <p>
                                    Uploaded by
                                    <strong>
                                        <?= htmlspecialchars(
                                            $image['username'],
                                            ENT_QUOTES,
                                            'UTF-8'
                                        ) ?>
                                    </strong>
                                </p>

                                <p class="profile-status">
                                    <?= htmlspecialchars(
                                        $image['created_at'],
                                        ENT_QUOTES,
                                        'UTF-8'
                                    ) ?>
                                </p>

                            </div>

                            <div class="gallery-card-actions">

                                <?php if (isset($_SESSION['user_id'])): ?>

                                    <form
                                        action="/gallery/like"
                                        method="POST"
                                        class="like-form"
                                    >

                                        <input
                                            type="hidden"
                                            name="csrf_token"
                                            value="<?= htmlspecialchars(
                                                $csrfToken,
                                                ENT_QUOTES,
                                                'UTF-8'
                                            ) ?>"
                                        >

                                        <input
                                            type="hidden"
                                            name="image_id"
                                            value="<?= (int) $image['id'] ?>"
                                        >

                                        <input
                                            type="hidden"
                                            name="page"
                                            value="<?= (int) $page ?>"
                                        >

                                        <button
                                            type="submit"
                                            class="like-button <?= $image['user_liked'] ? 'liked' : '' ?>"
                                        >
                                            <?= $image['user_liked'] ? 'Unlike' : 'Like' ?>
                                            (<?= (int) $image['like_count'] ?>)
                                        </button>

                                    </form>

                                <?php else: ?>

                                    <p class="profile-status">
                                        Likes:
                                        <strong>
                                            <?= (int) $image['like_count'] ?>
                                        </strong>
                                    </p>

                                <?php endif; ?>

                            </div>

                            <div class="comments-section">

                                <h3>
                                    Comments
                                </h3>

                                <?php if (empty($image['comments'])): ?>

                                    <p class="profile-status">
                                        No comments yet.
                                    </p>

                                <?php else: ?>

                                    <div class="comments-list">

                                        <?php foreach ($image['comments'] as $comment): ?>

                                            <div class="comment">

                                                <p>
                                                    <strong>
                                                        <?= htmlspecialchars(
                                                            $comment['username'],
                                                            ENT_QUOTES,
                                                            'UTF-8'
                                                        ) ?>
                                                    </strong>
                                                </p>

                                                <p>
                                                    <?= nl2br(
                                                        htmlspecialchars(
                                                            $comment['content'],
                                                            ENT_QUOTES,
                                                            'UTF-8'
                                                        )
                                                    ) ?>
                                                </p>

                                                <p class="profile-status">
                                                    <?= htmlspecialchars(
                                                        $comment['created_at'],
                                                        ENT_QUOTES,
                                                        'UTF-8'
                                                    ) ?>
                                                </p>

                                            </div>

                                        <?php endforeach; ?>

                                    </div>

                                <?php endif; ?>

                                <?php if (isset($_SESSION['user_id'])): ?>

                                    <form
                                        action="/gallery/comment"
                                        method="POST"
                                        class="comment-form"
                                    >

                                        <input
                                            type="hidden"
                                            name="csrf_token"
                                            value="<?= htmlspecialchars(
                                                $csrfToken,
                                                ENT_QUOTES,
                                                'UTF-8'
                                            ) ?>"
                                        >

                                        <input
                                            type="hidden"
                                            name="image_id"
                                            value="<?= (int) $image['id'] ?>"
                                        >

                                        <input
                                            type="hidden"
                                            name="page"
                                            value="<?= (int) $page ?>"
                                        >

                                        <div class="form-group">

                                            <label for="comment-<?= (int) $image['id'] ?>">
                                                Add a comment
                                            </label>

                                            <textarea
                                                id="comment-<?= (int) $image['id'] ?>"
                                                name="content"
                                                maxlength="1000"
                                                required
                                            ></textarea>

                                        </div>

                                        <button
                                            type="submit"
                                            class="btn-primary"
                                        >
                                            Comment
                                        </button>

                                    </form>

                                <?php endif; ?>

                            </div>

                        </article>

                    <?php endforeach; ?>

                </div>

                <?php if ($totalPages > 1): ?>

                    <nav class="pagination" aria-label="Gallery pages">

                        <?php if ($page > 1): ?>

                            <a href="/gallery?page=<?= $page - 1 ?>">
                                Previous
                            </a>

                        <?php endif; ?>

                        <?php for ($pageNumber = 1; $pageNumber <= $totalPages; $pageNumber++): ?>

                            <?php if ($pageNumber === $page): ?>

                                <strong>
                                    <?= $pageNumber ?>
                                </strong>

                            <?php else: ?>

                                <a href="/gallery?page=<?= $pageNumber ?>">
                                    <?= $pageNumber ?>
                                </a>

                            <?php endif; ?>

                        <?php endfor; ?>

                        <?php if ($page < $totalPages): ?>

                            <a href="/gallery?page=<?= $page + 1 ?>">
                                Next
                            </a>

                        <?php endif; ?>

                    </nav>

                <?php endif; ?>

            <?php endif; ?>

        </section>

    </main>

    <footer class="site-footer">

        <div class="container">

            <p>
                &copy; 2026 Camagru
            </p>

        </div>

    </footer>

</body>

</html>