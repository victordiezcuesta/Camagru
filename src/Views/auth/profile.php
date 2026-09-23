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

        <div class="container site-header-inner">

            <a href="/" class="logo">
                Camagru
            </a>

            <nav
                class="main-nav"
                aria-label="Main navigation"
            >

                <a
                    href="/"
                    class="nav-link"
                >
                    Home
                </a>

                <a
                    href="/gallery"
                    class="nav-link"
                >
                    Gallery
                </a>

                <a
                    href="/photo/create"
                    class="nav-link"
                >
                    New photo
                </a>

                <a
                    href="/profile"
                    class="nav-link active"
                    aria-current="page"
                >
                    Profile
                </a>

                <form
                    action="/logout"
                    method="POST"
                    class="logout-form"
                >

                    <input
                        type="hidden"
                        name="csrf_token"
                        value="<?= htmlspecialchars(
                            $csrfToken,
                            ENT_QUOTES,
                            'UTF-8'
                        ) ?>"
                    >

                    <button
                        type="submit"
                        class="nav-button"
                    >
                        Logout
                    </button>

                </form>

            </nav>

        </div>

    </header>

    <main class="profile-main">

        <section class="profile-section-main">

            <div class="profile-container">

                <!-- Intro -->
                <div class="profile-intro">

                    <p class="profile-eyebrow">
                        ACCOUNT SETTINGS
                    </p>

                    <h1>
                        My profile
                    </h1>

                    <p class="profile-description">
                        Manage your Camagru account and notification preferences.
                    </p>

                </div>

                <!-- Account information -->
                <section class="profile-card">

                    <div class="profile-card-header">

                        <div>

                            <p class="profile-card-eyebrow">
                                YOUR ACCOUNT
                            </p>

                            <h2>
                                Account information
                            </h2>

                        </div>

                    </div>

                    <div class="profile-account-grid">

                        <div class="profile-account-item">

                            <span class="profile-account-label">
                                Username
                            </span>

                            <strong class="profile-account-value">
                                <?= htmlspecialchars(
                                    $user['username'],
                                    ENT_QUOTES,
                                    'UTF-8'
                                ) ?>
                            </strong>

                        </div>


                        <div class="profile-account-item">

                            <span class="profile-account-label">
                                Email
                            </span>

                            <strong class="profile-account-value">
                                <?= htmlspecialchars(
                                    $user['email'],
                                    ENT_QUOTES,
                                    'UTF-8'
                                ) ?>
                            </strong>

                        </div>

                        <div class="profile-account-item">

                            <span class="profile-account-label">
                                Email status
                            </span>

                            <?php if ($user['email_verified']): ?>

                                <span class="profile-status-badge profile-status-verified">
                                    Verified
                                </span>

                            <?php else: ?>

                                <span class="profile-status-badge profile-status-unverified">
                                    Not verified
                                </span>

                            <?php endif; ?>

                        </div>

                    </div>

                </section>

                <!-- Username and email -->
                <div class="profile-settings-grid">

                    <!-- Change username -->
                    <section class="profile-card profile-settings-card">

                        <div class="profile-card-header">

                            <div>

                                <p class="profile-card-eyebrow">
                                    PROFILE
                                </p>

                                <h2>
                                    Change username
                                </h2>

                            </div>

                        </div>

                        <p class="profile-card-description">
                            Choose the username that will be displayed to other users.
                        </p>

                        <form
                            action="/profile/username"
                            method="POST"
                            class="profile-form"
                        >

                            <input
                                type="hidden"
                                name="csrf_token"
                                value="<?= htmlspecialchars(
                                    $csrfToken,
                                    ENT_QUOTES,
                                    'UTF-8'
                                ) ?>"
                            >

                            <div class="profile-field">

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
                                class="profile-button"
                            >
                                Change username
                            </button>

                        </form>

                    </section>

                    <!-- Change email -->
                    <section class="profile-card profile-settings-card">

                        <div class="profile-card-header">

                            <div>

                                <p class="profile-card-eyebrow">
                                    SECURITY
                                </p>

                                <h2>
                                    Change email
                                </h2>

                            </div>

                        </div>

                        <p class="profile-card-description">
                            Your new email must be verified before it becomes active.
                        </p>

                        <form
                            action="/profile/email"
                            method="POST"
                            class="profile-form"
                        >

                            <input
                                type="hidden"
                                name="csrf_token"
                                value="<?= htmlspecialchars(
                                    $csrfToken,
                                    ENT_QUOTES,
                                    'UTF-8'
                                ) ?>"
                            >

                            <div class="profile-field">

                                <label for="email">
                                    New email
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
                                class="profile-button"
                            >
                                Change email
                            </button>

                        </form>

                    </section>

                </div>

                <!-- Change password -->
                <section class="profile-card">

                    <div class="profile-card-header">

                        <div>

                            <p class="profile-card-eyebrow">
                                SECURITY
                            </p>

                            <h2>
                                Change password
                            </h2>

                        </div>

                    </div>

                    <p class="profile-card-description">
                        Use your current password to choose a new password for your account.
                    </p>

                    <form
                        action="/profile/password"
                        method="POST"
                        class="profile-form profile-password-form"
                    >

                        <input
                            type="hidden"
                            name="csrf_token"
                            value="<?= htmlspecialchars(
                                $csrfToken,
                                ENT_QUOTES,
                                'UTF-8'
                            ) ?>"
                        >

                        <div class="profile-field">

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

                        <div class="profile-field">

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

                            <p class="profile-help">
                                At least 8 characters, one uppercase letter,
                                one lowercase letter and one number.
                            </p>

                        </div>

                        <div class="profile-field">

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
                            class="profile-button"
                        >
                            Change password
                        </button>

                    </form>

                </section>

                <!-- Comment notifications -->
                <section class="profile-card">

                    <div class="profile-card-header">

                        <div>

                            <p class="profile-card-eyebrow">
                                NOTIFICATIONS
                            </p>

                            <h2>
                                Comment notifications
                            </h2>

                        </div>

                    </div>

                    <p class="profile-card-description">
                        Receive an email when another user comments on one of your photos.
                    </p>

                    <form
                        action="/profile/comment-notifications"
                        method="POST"
                        class="profile-form"
                    >

                        <input
                            type="hidden"
                            name="csrf_token"
                            value="<?= htmlspecialchars(
                                $csrfToken,
                                ENT_QUOTES,
                                'UTF-8'
                            ) ?>"
                        >

                        <label
                            for="comment_notifications"
                            class="profile-notification-option"
                        >

                            <input
                                type="checkbox"
                                id="comment_notifications"
                                name="comment_notifications"
                                value="1"
                                <?= $user['comment_notifications'] ? 'checked' : '' ?>
                            >

                            <span class="profile-notification-content">

                                <strong>
                                    Email notifications
                                </strong>

                                <span>
                                    Send me an email when someone comments on my photos.
                                </span>

                            </span>

                        </label>

                        <button
                            type="submit"
                            class="profile-button"
                        >
                            Save notification settings
                        </button>

                    </form>

                </section>

                <!-- Navigation -->
                <div class="profile-actions">

                    <a
                        href="/"
                        class="profile-secondary-button"
                    >
                        Back to home
                    </a>

                    <form
                        action="/logout"
                        method="POST"
                    >

                        <input
                            type="hidden"
                            name="csrf_token"
                            value="<?= htmlspecialchars(
                                $csrfToken,
                                ENT_QUOTES,
                                'UTF-8'
                            ) ?>"
                        >

                        <button
                            type="submit"
                            class="profile-logout-button"
                        >
                            Logout
                        </button>

                    </form>

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