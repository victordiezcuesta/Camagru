<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">

    <title>Camagru - Forgot Password</title>

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

<main class="forgot-password-main">

    <section class="forgot-password-section">

        <div class="forgot-password-container">

            <div class="forgot-password-intro">

                <p class="forgot-password-eyebrow">
                    ACCOUNT RECOVERY
                </p>

                <h1>
                    <?= isset($message) ? 'Password reset' : 'Forgot your password?' ?>
                </h1>

                <p class="forgot-password-description">

                    <?php if (isset($message)): ?>

                        Your password reset request has been processed.

                    <?php else: ?>

                        Enter the email address associated with your account
                        and we will send you a password reset link.

                    <?php endif; ?>

                </p>
            </div>

            <div class="forgot-password-card">

                <?php if (isset($message)): ?>

                    <p class="forgot-password-description">
                        <?= htmlspecialchars($message, ENT_QUOTES, 'UTF-8') ?>
                    </p>

                    <p class="forgot-password-login">

                        <a href="/login">
                            Back to login
                        </a>

                    </p>

                <?php else: ?>

                    <form
                        action="/forgot-password"
                        method="POST"
                        class="forgot-password-form"
                    >

                        <input
                            type="hidden"
                            name="csrf_token"
                            value="<?= htmlspecialchars($csrfToken, ENT_QUOTES, 'UTF-8') ?>"
                        >

                        <div class="forgot-password-field">

                            <label for="email">
                                Email address
                            </label>

                            <input
                                type="email"
                                id="email"
                                name="email"
                                autocomplete="email"
                                placeholder="you@example.com"
                                required
                            >

                        </div>

                        <button
                            type="submit"
                            class="forgot-password-button"
                        >
                            Send reset link
                        </button>

                    </form>

                    <div class="forgot-password-divider">
                        <span>or</span>
                    </div>

                    <p class="forgot-password-login">

                        Remember your password?

                        <a href="/login">
                            Back to login
                        </a>

                    </p>

                <?php endif; ?>

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