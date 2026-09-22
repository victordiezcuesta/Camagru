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

                        </article>

                    <?php endforeach; ?>

                </div>

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