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
        <div class="container">
            <a href="/" class="logo">Camagru</a>
        </div>
    </header>

    <main class="main-content">

        <section class="auth-card">

            <h1>Forgot password?</h1>

            <p class="auth-subtitle">
                Enter your email address and we will send you a password reset link.
            </p>

            <form action="/forgot-password" method="POST" class="auth-form">

                <input
                    type="hidden"
                    name="csrf_token"
                    value="<?= htmlspecialchars($csrfToken, ENT_QUOTES, 'UTF-8') ?>"
                >

                <div class="form-group">

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

                <button type="submit" class="btn-primary">
                    Send reset link
                </button>

            </form>

            <div class="auth-links">

                <p>
                    Remember your password?
                    <a href="/login">Back to login</a>
                </p>

            </div>

        </section>

    </main>

    <footer class="site-footer">
        <div class="container">
            <p>&copy; 2026 Camagru</p>
        </div>
    </footer>

</body>

</html>