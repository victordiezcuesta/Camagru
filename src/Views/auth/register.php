<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">

    <title>Camagru - Register</title>

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

            <h1>Create account</h1>

            <p class="auth-subtitle">
                Create your Camagru account.
            </p>

            <form action="/register" method="POST" class="auth-form">

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
                    <label for="email">Email</label>

                    <input
                        type="email"
                        id="email"
                        name="email"
                        autocomplete="email"
                        required
                    >
                </div>

                <div class="form-group">
                    <label for="password">Password</label>

                    <input
                        type="password"
                        id="password"
                        name="password"
                        autocomplete="new-password"
                        required
                    >
                </div>

                <button type="submit" class="btn-primary">
                    Create account
                </button>

            </form>

            <div class="auth-links">
                <p>
                    Already have an account?
                    <a href="/login">Login</a>
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