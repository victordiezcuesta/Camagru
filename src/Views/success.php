<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">

    <link rel="icon" href="/favicon.ico">

    <title>
        Camagru - <?= htmlspecialchars($successTitle, ENT_QUOTES, 'UTF-8') ?>
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

                <a href="/login" class="nav-link">
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
                        <?= htmlspecialchars($successTitle, ENT_QUOTES, 'UTF-8') ?>
                    </h1>

                    <p class="login-description">
                        <?= htmlspecialchars($successMessage, ENT_QUOTES, 'UTF-8') ?>
                    </p>

                </div>

                <div class="login-card">

                    <p class="login-register">
                        You can return to your profile.
                    </p>

                    <a href="/profile" class="login-button">
                        Back to profile
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