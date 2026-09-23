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

    <section class="photo-editor">

        <div class="photo-editor-main">

            <h1>New photo</h1>

            <p class="auth-subtitle">
                Take a photo with your webcam or upload an image.
            </p>

            <div class="camera-container">

                <video
                    id="camera"
                    autoplay
                    playsinline
                ></video>

                <div
                    id="camera-message"
                    class="camera-message"
                >
                    Camera is not active.
                </div>

            </div>

            <div class="overlay-section">

                <h2>Choose an overlay</h2>

                <div class="overlay-list">

                    <button
                        type="button"
                        class="overlay-option"
                        data-overlay=""
                    >
                        None
                    </button>

                    <button
                        type="button"
                        class="overlay-option"
                        data-overlay="overlay1"
                    >
                        Overlay 1
                    </button>

                    <button
                        type="button"
                        class="overlay-option"
                        data-overlay="overlay2"
                    >
                        Overlay 2
                    </button>

                    <button
                        type="button"
                        class="overlay-option"
                        data-overlay="overlay3"
                    >
                        Overlay 3
                    </button>

                </div>

            </div>

            <div class="photo-editor-actions">

                <button
                    type="button"
                    id="start-camera"
                    class="btn-primary"
                >
                    Start camera
                </button>

                <button
                    type="button"
                    id="take-picture"
                    class="btn-primary"
                    disabled
                >
                    Take picture
                </button>

                <button
                    type="button"
                    id="upload-image"
                    class="btn-primary"
                >
                    Upload image
                </button>

            </div>

            <canvas
                id="photo-canvas"
                hidden
            ></canvas>

            <form
                id="photo-form"
                action="/photo"
                method="POST"
                enctype="multipart/form-data"
                class="photo-form"
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

                <input
                    type="hidden"
                    id="selected-overlay"
                    name="overlay"
                    value=""
                >

                <input
                    type="file"
                    id="image"
                    name="image"
                    accept="image/jpeg,image/png"
                    hidden
                >

            </form>

            <p
                id="camera-error"
                class="form-error"
                hidden
            ></p>

        </div>

        <aside class="photo-editor-sidebar">

            <h2>My previous photos</h2>

            <?php if (empty($previousImages)): ?>

                <p class="profile-status">
                    You have not created any photos yet.
                </p>

            <?php else: ?>

                <div
                    id="previous-photos"
                    class="previous-photos"
                >

                    <?php foreach ($previousImages as $image): ?>

                        <div class="previous-photo">

                            <img
                                src="/uploads/<?= htmlspecialchars(
                                    $image['filename'],
                                    ENT_QUOTES,
                                    'UTF-8'
                                ) ?>"
                                alt="Previous photo"
                            >

                        </div>

                    <?php endforeach; ?>

                </div>

            <?php endif; ?>

        </aside>

    </section>

</main>

<footer class="site-footer">

    <div class="container">

        <p>
            &copy; 2026 Camagru
        </p>

    </div>

</footer>

<script src="/assets/js/photo-editor.js"></script>

</body>

</html>