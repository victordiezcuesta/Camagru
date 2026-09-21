<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">

    <title>Camagru</title>

    <link rel="stylesheet" href="/assets/css/style.css">
</head>

<body>

    <header class="site-header">
        <div class="container">

            <a href="/" class="logo">Camagru</a>

            <?php if ($isAuthenticated): ?>

                <a href="/profile">
                    Profile
                </a>

                <form action="/logout" method="POST">

                    <input
                        type="hidden"
                        name="csrf_token"
                        value="<?= htmlspecialchars(
                            Csrf::token(),
                            ENT_QUOTES,
                            'UTF-8'
                        ) ?>"
                    >

                    <button type="submit">
                        Logout
                    </button>

                </form>

            <?php else: ?>

                <a href="/login">
                    Login
                </a>

            <?php endif; ?>

        </div>
    </header>

    <main class="main-content">

        <section class="auth-card">

            <?php if ($isAuthenticated): ?>

                <h1>Welcome, <?= htmlspecialchars($username, ENT_QUOTES, 'UTF-8') ?></h1>

                <p class="auth-subtitle">
                    You are logged in.
                </p>

            <?php else: ?>

                <h1>Welcome to Camagru</h1>

                <p class="auth-subtitle">
                    Please log in to continue.
                </p>

            <?php endif; ?>

        </section>

    </main>

    <footer class="site-footer">
        <div class="container">
            <p>&copy; 2026 Camagru</p>
        </div>
    </footer>

</body>
</html>