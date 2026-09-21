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
        <div class="container">
            <a href="/" class="logo">Camagru</a>
        </div>
    </header>

    <main class="main-content">

        <section class="auth-card">

            <h1>Reset password</h1>

            <p class="auth-subtitle">
                Enter your new password.
            </p>

            <form action="/reset-password" method="POST" class="auth-form">

                <input
                    type="hidden"
                    name="token"
                    value="<?= htmlspecialchars($token, ENT_QUOTES, 'UTF-8') ?>"
                >

                <div class="form-group">

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

                </div>

                <div class="form-group">

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

                <button type="submit" class="btn-primary">
                    Reset password
                </button>

            </form>

            <div class="auth-links">

                <p>
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