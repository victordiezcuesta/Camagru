<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">

    <title>Camagru - Profile</title>

    <link rel="stylesheet" href="/assets/css/style.css">
</head>

<body>

    <header class="site-header">

        <div class="container">

            <a href="/" class="logo">
                Camagru
            </a>

        </div>

    </header>

    <main class="main-content">

        <section class="auth-card">

            <h1>My profile</h1>

            <p class="auth-subtitle">
                Manage your Camagru account.
            </p>

            <div class="form-group">

                <label for="username">
                    Username
                </label>

                <input
                    type="text"
                    id="username"
                    value="<?= htmlspecialchars($user['username'], ENT_QUOTES, 'UTF-8') ?>"
                    disabled
                >

            </div>

            <div class="form-group">

                <label for="email">
                    Email
                </label>

                <input
                    type="email"
                    id="email"
                    value="<?= htmlspecialchars($user['email'], ENT_QUOTES, 'UTF-8') ?>"
                    disabled
                >

            </div>

            <div class="auth-links">

                <p>
                    <a href="/">
                        Back to home
                    </a>
                </p>

                <p>
                    <a href="/logout">
                        Logout
                    </a>
                </p>

            </div>

        </section>

    </main>

    <footer class="site-footer">

        <div class="container">

            <p>
                &copy; 2026 Camagru
            </p>

        </div>

    </footer>

</body>

</html>