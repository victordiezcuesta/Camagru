<!DOCTYPE html>
<html lang="en">

<head>

    <meta charset="UTF-8">

    <meta
        name="viewport"
        content="width=device-width, initial-scale=1.0"
    >

    <title>Camagru - Profile</title>

    <link
        rel="stylesheet"
        href="/assets/css/style.css"
    >

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


            <!-- Current account information -->

            <div class="profile-section">

                <h2>Account information</h2>

                <div class="form-group">

                    <label for="current_username">
                        Current username
                    </label>

                    <input
                        type="text"
                        id="current_username"
                        value="<?= htmlspecialchars(
                            $user['username'],
                            ENT_QUOTES,
                            'UTF-8'
                        ) ?>"
                        disabled
                    >

                </div>

                <div class="form-group">

                    <label for="current_email">
                        Current email
                    </label>

                    <input
                        type="email"
                        id="current_email"
                        value="<?= htmlspecialchars(
                            $user['email'],
                            ENT_QUOTES,
                            'UTF-8'
                        ) ?>"
                        disabled
                    >

                </div>

                <p class="profile-status">

                    Email status:

                    <?php if ($user['email_verified']): ?>

                        <strong>Verified</strong>

                    <?php else: ?>

                        <strong>Not verified</strong>

                    <?php endif; ?>

                </p>

            </div>


            <!-- Change username -->

            <div class="profile-section">

                <h2>Change username</h2>

                <form
                    action="/profile/username"
                    method="POST"
                    class="auth-form"
                >

                <input
                    type="hidden"
                    name="csrf_token"
                    value="<?= htmlspecialchars($csrfToken, ENT_QUOTES, 'UTF-8') ?>"
                >

                    <div class="form-group">

                        <label for="username">
                            New username
                        </label>

                        <input
                            type="text"
                            id="username"
                            name="username"
                            maxlength="50"
                            autocomplete="username"
                            required
                        >

                    </div>

                    <button
                        type="submit"
                        class="btn-primary"
                    >
                        Change username
                    </button>

                </form>

            </div>


            <!-- Change email -->

            <div class="profile-section">

                <h2>Change email</h2>

                <form
                    action="/profile/email"
                    method="POST"
                    class="auth-form"
                >

                <input
                    type="hidden"
                    name="csrf_token"
                    value="<?= htmlspecialchars($csrfToken, ENT_QUOTES, 'UTF-8') ?>"
                >

                    <div class="form-group">

                        <label for="email">
                            New email
                        </label>

                        <input
                            type="email"
                            id="email"
                            name="email"
                            autocomplete="email"
                            required
                        >

                    </div>

                    <button
                        type="submit"
                        class="btn-primary"
                    >
                        Change email
                    </button>

                </form>

            </div>


            <!-- Change password -->

            <div class="profile-section">

                <h2>Change password</h2>

                <form
                    action="/profile/password"
                    method="POST"
                    class="auth-form"
                >

                <input
                    type="hidden"
                    name="csrf_token"
                    value="<?= htmlspecialchars($csrfToken, ENT_QUOTES, 'UTF-8') ?>"
                >

                    <div class="form-group">

                        <label for="current_password">
                            Current password
                        </label>

                        <input
                            type="password"
                            id="current_password"
                            name="current_password"
                            autocomplete="current-password"
                            required
                        >

                    </div>

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

                    <button
                        type="submit"
                        class="btn-primary"
                    >
                        Change password
                    </button>

                </form>

            </div>


            <div class="auth-links">

                <p>
                    <a href="/">
                        Back to home
                    </a>
                </p>

                <p>
                    <form action="/logout" method="POST">

                        <input
                            type="hidden"
                            name="csrf_token"
                            value="<?= htmlspecialchars($csrfToken, ENT_QUOTES, 'UTF-8') ?>"
                        >

                        <button type="submit" class="btn-primary">
                            Logout
                        </button>

                    </form>
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