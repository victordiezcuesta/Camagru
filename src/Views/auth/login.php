<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">

    <title>Camagru - Login</title>

    <link rel="stylesheet" href="/assets/css/style.css">
</head>

<body>

    <header class="site-header">
        <div class="container site-header-inner">

            <a href="/" class="logo">Camagru</a>

            <nav class="main-nav" aria-label="Main navigation">
                <a href="/" class="nav-link">Home</a>
                <a href="/gallery" class="nav-link">Gallery</a>
                <a href="/login" class="nav-link active" aria-current="page">Login</a>
                <a href="/register" class="nav-register">Sign up</a>
            </nav>

        </div>
    </header>

    <main class="login-main">

        <section class="login-section">

            <div class="login-container">

                <div class="login-intro">
                    <p class="login-eyebrow">WELCOME BACK</p>

                    <h1>Sign in to Camagru</h1>

                    <p class="login-description">
                        Sign in to create, customize and share your photos
                        with the Camagru community.
                    </p>
                </div>

                <div class="login-card">

                    <form action="/login" method="POST" class="login-form">

                        <input
                            type="hidden"
                            name="csrf_token"
                            value="<?= htmlspecialchars($csrfToken, ENT_QUOTES, 'UTF-8') ?>"
                        >

                        <div class="login-field">
                            <label for="username">Username</label>

                            <input
                                type="text"
                                id="username"
                                name="username"
                                autocomplete="username"
                                required
                            >
                        </div>

                        <div class="login-field">
                            <div class="login-password-header">
                                <label for="password">Password</label>

                                <a href="/forgot-password">
                                    Forgot password?
                                </a>
                            </div>

                            <input
                                type="password"
                                id="password"
                                name="password"
                                autocomplete="current-password"
                                required
                            >
                        </div>

                        <button type="submit" class="login-button">
                            Login
                        </button>

                    </form>

                    <div class="login-divider">
                        <span>or</span>
                    </div>

                    <p class="login-register">
                        Don't have an account?
                        <a href="/register">Create an account</a>
                    </p>

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

            <p>&copy; 2026 Camagru</p>

        </div>
    </footer>

</body>
</html>