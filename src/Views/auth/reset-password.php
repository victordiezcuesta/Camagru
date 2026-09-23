<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">

    <title>Camagru - Reset Password</title>

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

    <main class="reset-password-main">

        <section class="reset-password-section">

            <div class="reset-password-container">

                <div class="reset-password-intro">

                    <p class="reset-password-eyebrow">
                        ACCOUNT RECOVERY
                    </p>

                    <h1>
                        Reset your password
                    </h1>

                    <p class="reset-password-description">
                        Choose a new password for your Camagru account.
                    </p>

                </div>

                <div class="reset-password-card">

                    <form
                        action="/reset-password"
                        method="POST"
                        class="reset-password-form"
                    >

                        <input
                            type="hidden"
                            name="token"
                            value="<?= htmlspecialchars($token, ENT_QUOTES, 'UTF-8') ?>"
                        >

                        <input
                            type="hidden"
                            name="csrf_token"
                            value="<?= htmlspecialchars($csrfToken, ENT_QUOTES, 'UTF-8') ?>"
                        >

                        <div class="reset-password-field">

                            <label for="password">
                                New password
                            </label>

                            <input
                                type="password"
                                id="password"
                                name="password"
                                autocomplete="new-password"
                                required
                            >

                            <p class="reset-password-help">
                                At least 8 characters, one uppercase letter,
                                one lowercase letter and one number.
                            </p>

                        </div>

                        <div class="reset-password-field">

                            <label for="password_confirmation">
                                Confirm new password
                            </label>

                            <input
                                type="password"
                                id="password_confirmation"
                                name="password_confirmation"
                                autocomplete="new-password"
                                required
                            >

                        </div>

                        <button
                            type="submit"
                            class="reset-password-button"
                        >
                            Reset password
                        </button>

                    </form>

                    <div class="reset-password-divider">
                        <span>or</span>
                    </div>

                    <p class="reset-password-login">
                        Remember your password?
                        <a href="/login">
                            Back to login
                        </a>
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

            <p>
                &copy; 2026 Camagru
            </p>

        </div>

    </footer>

</body>
</html>