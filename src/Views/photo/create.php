<!DOCTYPE html>
<html lang="en">

<head>

    <meta charset="UTF-8">

    <meta
        name="viewport"
        content="width=device-width, initial-scale=1.0"
    >

    <title>Camagru - New photo</title>

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

            <h1>New photo</h1>

            <p class="auth-subtitle">
                Upload an image to Camagru.
            </p>

            <form
                action="/photo"
                method="POST"
                enctype="multipart/form-data"
                class="auth-form"
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

                <div class="form-group">

                    <label for="image">
                        Image
                    </label>

                    <input
                        type="file"
                        id="image"
                        name="image"
                        accept="image/jpeg,image/png"
                        required
                    >

                    <p class="profile-status">
                        JPEG or PNG. Maximum size: 5 MB.
                    </p>

                </div>

                <button
                    type="submit"
                    class="btn-primary"
                >
                    Upload image
                </button>

            </form>

            <div class="auth-links">

                <p>
                    <a href="/gallery">
                        Back to gallery
                    </a>
                </p>

                <p>
                    <a href="/">
                        Back to home
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