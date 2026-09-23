<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">

    <title>Camagru - Sign up</title>

    <link rel="stylesheet" href="/assets/css/style.css">
</head>

<body>

    <header class="site-header">
        <div class="container site-header-inner">

            <a href="/" class="logo">Camagru</a>

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

                <a
                    href="/register"
                    class="nav-register"
                    aria-current="page"
                >
                    Sign up
                </a>

            </nav>

        </div>
    </header>


    <main class="register-main">

        <section class="register-section">

            <div class="register-container">

                <div class="register-intro">

                    <p class="register-eyebrow">
                        JOIN CAMAGRU
                    </p>

                    <h1>
                        Create your account
                    </h1>

                    <p class="register-description">
                        Create your account and start capturing,
                        customizing and sharing your photos.
                    </p>

                </div>


                <div class="register-card">

                    <form
                        action="/register"
                        method="POST"
                        class="register-form"
                    >

                        <input
                            type="hidden"
                            name="csrf_token"
                            value="<?= htmlspecialchars($csrfToken, ENT_QUOTES, 'UTF-8') ?>"
                        >


                        <div class="register-field">

                            <label for="username">
                                Username
                            </label>

                            <input
                                type="text"
                                id="username"
                                name="username"
                                autocomplete="username"
                                required
                            >

                        </div>


                        <div class="register-field">

                            <label for="email">
                                Email
                            </label>

                            <input
                                type="email"
                                id="email"
                                name="email"
                                autocomplete="email"
                                required
                            >

                        </div>


                        <div class="register-field">

                            <label for="password">
                                Password
                            </label>

                            <input
                                type="password"
                                id="password"
                                name="password"
                                autocomplete="new-password"
                                required
                            >

                            <p class="register-help">
                                At least 8 characters, one uppercase
                                letter, one lowercase letter and one number.
                            </p>

                        </div>


                        <button
                            type="submit"
                            class="register-button"
                        >
                            Create account
                        </button>

                    </form>


                    <div class="register-divider">
                        <span>or</span>
                    </div>


                    <p class="register-login">

                        Already have an account?

                        <a href="/login">
                            Login
                        </a>

                    </p>

                </div>

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
                    Create. Capture. Share.
                </span>

            </div>

            <p>
                &copy; 2026 Camagru
            </p>

        </div>

    </footer>

</body>
</html>