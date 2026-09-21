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
        <div class="container">
            <a href="/" class="logo">Camagru</a>
        </div>
    </header>

    <main class="main-content">
        <section class="auth-card">

            <h1>Welcome back</h1>

            <p class="auth-subtitle">
                Sign in to continue to Camagru.
            </p>

            <form action="/login" method="POST" class="auth-form">

                <input
                    type="hidden"
                    name="csrf_token"
                    value="<?= htmlspecialchars($csrfToken, ENT_QUOTES, 'UTF-8') ?>"
                >

                <div class="form-group">
                    <label for="username">Username</label>

                    <input
                        type="text"
                        id="username"
                        name="username"
                        autocomplete="username"
                        required
                    >
                </div>

                <div class="form-group">
                    <label for="password">Password</label>

                    <input
                        type="password"
                        id="password"
                        name="password"
                        autocomplete="current-password"
                        required
                    >
                </div>

                <button type="submit" class="btn-primary">
                    Login
                </button>

            </form>

            <div class="auth-links">
                <a href="/forgot-password">
                    Forgot your password?
                </a>

                <p>
                    Don't have an account?
                    <a href="/register">Create an account</a>
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