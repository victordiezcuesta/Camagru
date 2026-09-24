<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">

    <title>
        Camagru - <?= htmlspecialchars($errorTitle, ENT_QUOTES, 'UTF-8') ?>
    </title>

    <link rel="stylesheet" href="/assets/css/style.css">
</head>

<body>

    <header class="site-header">

        <div class="container site-header-inner">

            <a href="/" class="logo">
                Camagru
            </a>

            <nav class="main-nav" aria-label="Main navigation">

                <a href="/" class="nav-link">
                    Home
                </a>

                <a href="/gallery" class="nav-link">
                    Gallery
                </a>

                <a href="/login" class="nav-link active" aria-current="page">
                    Login
                </a>

                <a href="/register" class="nav-register">
                    Sign up
                </a>

            </nav>

        </div>

    </header>

    <main class="login-main">

        <section class="login-section">

            <div class="login-container">

                <div class="login-intro">

                    <p class="login-eyebrow">
                        CAMAGRU
                    </p>

                    <h1>
                        <?= htmlspecialchars($errorTitle, ENT_QUOTES, 'UTF-8') ?>
                    </h1>

                    <p class="login-description">
                        <?= htmlspecialchars($errorMessage, ENT_QUOTES, 'UTF-8') ?>
                    </p>

                </div>

                <div class="login-card">

                    <p class="login-register">
                        If you need to continue, return to the previous page
                        or go back to the login page.
                    </p>

                    <a href="/login" class="login-button">
                        Back to login
                    </a>

                </div>

            </div>

        </section>

    </main>

    <footer class="site-footer">

        <div class="container footer-inner">

            <div>
                <strong>Camagru</strong>
                <span>Create. Capture. Share.</span>
            </div>

            <p>
                &copy; 2026 Camagru
            </p>

        </div>

    </footer>

</body>

</html>