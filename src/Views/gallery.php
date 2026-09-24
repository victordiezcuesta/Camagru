<!DOCTYPE html>
<html lang="en">

<head>

    <meta charset="UTF-8">

    <meta
        name="viewport"
        content="width=device-width, initial-scale=1.0"
    >

    <link rel="icon" href="/favicon.ico">

    <title>Camagru - Gallery</title>

    <link
        rel="stylesheet"
        href="/assets/css/style.css"
    >

</head>

<body>

    <header class="site-header">

        <div class="container site-header-inner">

            <a href="/" class="logo">
                Camagru
            </a>

            <nav class="main-nav" aria-label="Main navigation">

                <a
                    href="/"
                    class="nav-link"
                >
                    Home
                </a>

                <a
                    href="/gallery"
                    class="nav-link active"
                    aria-current="page"
                >
                    Gallery
                </a>

                <?php if (isset($_SESSION['user_id'])): ?>

                    <a
                        href="/photo/create"
                        class="nav-link"
                    >
                        New photo
                    </a>

                    <a
                        href="/profile"
                        class="nav-link"
                    >
                        Profile
                    </a>

                    <form
                        action="/logout"
                        method="POST"
                        class="logout-form"
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

                        <button
                            type="submit"
                            class="nav-button"
                        >
                            Logout
                        </button>

                    </form>

                <?php else: ?>

                    <a
                        href="/login"
                        class="nav-link"
                    >
                        Login
                    </a>

                    <a
                        href="/register"
                        class="nav-register"
                    >
                        Sign up
                    </a>

                <?php endif; ?>

            </nav>

        </div>

    </header>

    <main class="gallery-main">

        <section class="gallery-hero">

            <div class="gallery-container">

                <div class="gallery-intro">

                    <p class="gallery-eyebrow">
                        COMMUNITY
                    </p>

                    <h1>
                        Gallery
                    </h1>

                    <p class="gallery-description">
                        Discover photos created by the Camagru community.
                    </p>

                </div>

            </div>

        </section>

        <section class="gallery-content">

            <div class="gallery-container">

                <?php if (empty($images)): ?>

                    <div class="gallery-empty-state">

                        <div class="gallery-empty-icon">
                            +
                        </div>

                        <h2>
                            No photos yet
                        </h2>

                        <p>
                            There are no photos in the gallery yet.
                            Create your first photo and share it with the community.
                        </p>

                        <?php if (isset($_SESSION['user_id'])): ?>

                            <a
                                href="/photo/create"
                                class="gallery-primary-button"
                            >
                                Create photo
                            </a>

                        <?php else: ?>

                            <a
                                href="/login"
                                class="gallery-primary-button"
                            >
                                Login
                            </a>

                        <?php endif; ?>

                    </div>

                <?php else: ?>

                    <div class="gallery-grid">

                        <?php foreach ($images as $image): ?>

                            <article class="gallery-card">

                                <div class="gallery-photo-wrapper">

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

                                </div>

                                <div class="gallery-card-content">

                                    <div class="gallery-photo-info">

                                        <div class="gallery-avatar">
                                            <?= htmlspecialchars(
                                                strtoupper(
                                                    substr(
                                                        $image['username'],
                                                        0,
                                                        1
                                                    )
                                                ),
                                                ENT_QUOTES,
                                                'UTF-8'
                                            ) ?>
                                        </div>

                                        <div class="gallery-photo-user">

                                            <strong>
                                                <?= htmlspecialchars(
                                                    $image['username'],
                                                    ENT_QUOTES,
                                                    'UTF-8'
                                                ) ?>
                                            </strong>

                                            <span>
                                                <?= htmlspecialchars(
                                                    $image['created_at'],
                                                    ENT_QUOTES,
                                                    'UTF-8'
                                                ) ?>
                                            </span>

                                        </div>

                                    </div>

                                    <!-- COMMENTS TOGGLE -->
                                    <input
                                        type="checkbox"
                                        id="comments-<?= (int) $image['id'] ?>"
                                        class="gallery-comments-toggle"
                                    >

                                    <!-- SOCIAL ACTIONS -->
                                    <div class="gallery-social-actions">

                                        <!-- LIKE -->
                                        <?php if (isset($_SESSION['user_id'])): ?>

                                            <form
                                                action="/gallery/like"
                                                method="POST"
                                                class="gallery-like-form-new"
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
                                                    class="gallery-heart-button <?= $image['user_liked'] ? 'is-liked' : '' ?>"
                                                    aria-label="<?= $image['user_liked'] ? 'Remove like' : 'Like photo' ?>"
                                                >

                                                    <?php if ($image['user_liked']): ?>

                                                        <svg
                                                            class="gallery-heart-icon"
                                                            viewBox="0 0 24 24"
                                                            aria-hidden="true"
                                                        >
                                                            <path
                                                                d="M20.84 4.61a5.5 5.5 0 0 0-7.78 0L12 5.67l-1.06-1.06a5.5 5.5 0 0 0-7.78 7.78L12 21.23l8.84-8.84a5.5 5.5 0 0 0 0-7.78z"
                                                            />
                                                        </svg>

                                                    <?php else: ?>

                                                        <svg
                                                            class="gallery-heart-icon"
                                                            viewBox="0 0 24 24"
                                                            aria-hidden="true"
                                                        >
                                                            <path
                                                                d="M20.84 4.61a5.5 5.5 0 0 0-7.78 0L12 5.67l-1.06-1.06a5.5 5.5 0 0 0-7.78 7.78L12 21.23l8.84-8.84a5.5 5.5 0 0 0 0-7.78z"
                                                            />
                                                        </svg>

                                                    <?php endif; ?>

                                                </button>

                                            </form>

                                        <?php else: ?>

                                            <div
                                                class="gallery-heart-display"
                                                aria-label="Likes"
                                            >

                                                <svg
                                                    class="gallery-heart-icon"
                                                    viewBox="0 0 24 24"
                                                    aria-hidden="true"
                                                >
                                                    <path
                                                        d="M20.84 4.61a5.5 5.5 0 0 0-7.78 0L12 5.67l-1.06-1.06a5.5 5.5 0 0 0-7.78 7.78L12 21.23l8.84-8.84a5.5 5.5 0 0 0 0-7.78z"
                                                    />
                                                </svg>

                                            </div>

                                        <?php endif; ?>

                                        <span class="gallery-like-number">
                                            <?= (int) $image['like_count'] ?>
                                        </span>

                                        <label
                                            for="comments-<?= (int) $image['id'] ?>"
                                            class="gallery-comments-button"
                                            aria-label="Open comments"
                                        >

                                            <svg
                                                class="gallery-comment-icon"
                                                viewBox="0 0 24 24"
                                                aria-hidden="true"
                                            >
                                                <path
                                                    d="M21 11.5a8.38 8.38 0 0 1-.9 3.8 8.5 8.5 0 0 1-7.6 4.7 8.38 8.38 0 0 1-3.8-.9L3 21l1.9-5.7a8.38 8.38 0 0 1-.9-3.8 8.5 8.5 0 0 1 4.7-7.6 8.38 8.38 0 0 1 3.8-.9h.5a8.48 8.48 0 0 1 8 8v.5z"
                                                />
                                            </svg>

                                        </label>

                                        <span class="gallery-comment-number">
                                            <?= count($image['comments']) ?>
                                        </span>

                                    </div>

                                    <!-- COMMENTS POPUP -->

                                    <div class="gallery-comments-overlay">

                                        <label
                                            for="comments-<?= (int) $image['id'] ?>"
                                            class="gallery-comments-overlay-close"
                                            aria-label="Close comments"
                                        >
                                        </label>

                                        <div
                                            class="gallery-comments-popup"
                                            role="dialog"
                                            aria-modal="true"
                                            aria-label="Comments"
                                        >

                                            <div class="gallery-comments-popup-header">

                                                <div>

                                                    <h2>
                                                        Comments
                                                    </h2>

                                                    <span>
                                                        <?= count($image['comments']) ?>
                                                        <?= count($image['comments']) === 1 ? 'comment' : 'comments' ?>
                                                    </span>

                                                </div>

                                                <label
                                                    for="comments-<?= (int) $image['id'] ?>"
                                                    class="gallery-comments-close"
                                                    aria-label="Close comments"
                                                >
                                                    &times;
                                                </label>

                                            </div>

                                            <div class="gallery-comments-popup-list">

                                                <?php if (empty($image['comments'])): ?>

                                                    <div class="gallery-comments-empty">

                                                        <svg
                                                            class="gallery-empty-comment-icon"
                                                            viewBox="0 0 24 24"
                                                            aria-hidden="true"
                                                        >
                                                            <path
                                                                d="M21 11.5a8.38 8.38 0 0 1-.9 3.8 8.5 8.5 0 0 1-7.6 4.7 8.38 8.38 0 0 1-3.8-.9L3 21l1.9-5.7a8.38 8.38 0 0 1-.9-3.8 8.5 8.5 0 0 1 4.7-7.6 8.38 8.38 0 0 1 3.8-.9h.5a8.48 8.48 0 0 1 8 8v.5z"
                                                            />
                                                        </svg>

                                                        <p>
                                                            No comments yet.
                                                        </p>

                                                    </div>

                                                <?php else: ?>

                                                    <?php foreach ($image['comments'] as $comment): ?>

                                                        <div class="gallery-popup-comment">

                                                            <div class="gallery-popup-comment-header">

                                                                <strong>
                                                                    <?= htmlspecialchars(
                                                                        $comment['username'],
                                                                        ENT_QUOTES,
                                                                        'UTF-8'
                                                                    ) ?>
                                                                </strong>

                                                                <span>
                                                                    <?= htmlspecialchars(
                                                                        $comment['created_at'],
                                                                        ENT_QUOTES,
                                                                        'UTF-8'
                                                                    ) ?>
                                                                </span>

                                                            </div>

                                                            <p>
                                                                <?= nl2br(
                                                                    htmlspecialchars(
                                                                        $comment['content'],
                                                                        ENT_QUOTES,
                                                                        'UTF-8'
                                                                    )
                                                                ) ?>
                                                            </p>

                                                        </div>

                                                    <?php endforeach; ?>

                                                <?php endif; ?>

                                            </div>

                                            <?php if (isset($_SESSION['user_id'])): ?>

                                                <form
                                                    action="/gallery/comment"
                                                    method="POST"
                                                    class="gallery-popup-comment-form"
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

                                                    <label
                                                        for="popup-comment-<?= (int) $image['id'] ?>"
                                                        class="gallery-popup-comment-label"
                                                    >
                                                        Add a comment
                                                    </label>

                                                    <div class="gallery-popup-comment-input">

                                                        <textarea
                                                            id="popup-comment-<?= (int) $image['id'] ?>"
                                                            name="content"
                                                            maxlength="1000"
                                                            required
                                                            placeholder="Write a comment..."
                                                        ></textarea>

                                                        <button
                                                            type="submit"
                                                            class="gallery-popup-comment-submit"
                                                            aria-label="Send comment"
                                                        >

                                                            <svg
                                                                viewBox="0 0 24 24"
                                                                aria-hidden="true"
                                                            >
                                                                <path
                                                                    d="M22 2L11 13"
                                                                />
                                                                <path
                                                                    d="M22 2l-7 20-4-9-9-4z"
                                                                />
                                                            </svg>

                                                        </button>

                                                    </div>

                                                </form>

                                            <?php else: ?>

                                                <div class="gallery-popup-login-message">

                                                    <p>
                                                        <a href="/login">
                                                            Login
                                                        </a>
                                                        to leave a comment.
                                                    </p>

                                                </div>

                                            <?php endif; ?>

                                        </div>

                                    </div>

                                </div>

                            </article>

                        <?php endforeach; ?>

                    </div>

                    <?php if ($totalPages > 1): ?>

                        <nav
                            class="gallery-pagination"
                            aria-label="Gallery pages"
                        >

                            <?php if ($page > 1): ?>

                                <a
                                    href="/gallery?page=<?= $page - 1 ?>"
                                    class="gallery-pagination-link"
                                >
                                    Previous
                                </a>

                            <?php endif; ?>

                            <div class="gallery-pagination-numbers">

                                <?php for (
                                    $pageNumber = 1;
                                    $pageNumber <= $totalPages;
                                    $pageNumber++
                                ): ?>

                                    <?php if ($pageNumber === $page): ?>

                                        <strong
                                            class="gallery-pagination-current"
                                            aria-current="page"
                                        >
                                            <?= $pageNumber ?>
                                        </strong>

                                    <?php else: ?>

                                        <a
                                            href="/gallery?page=<?= $pageNumber ?>"
                                            class="gallery-pagination-number"
                                        >
                                            <?= $pageNumber ?>
                                        </a>

                                    <?php endif; ?>

                                <?php endfor; ?>

                            </div>

                            <?php if ($page < $totalPages): ?>

                                <a
                                    href="/gallery?page=<?= $page + 1 ?>"
                                    class="gallery-pagination-link"
                                >
                                    Next
                                </a>

                            <?php endif; ?>

                        </nav>

                    <?php endif; ?>

                <?php endif; ?>

            </div>

        </section>

    </main>

    <footer class="site-footer">

        <div class="container footer-inner">

            <div>

                <strong>
                    Camagru
                </strong>

                <span>
                    Create. Share. Connect.
                </span>

            </div>

            <p>
                &copy; 2026 Camagru
            </p>

        </div>

    </footer>

</body>

</html>