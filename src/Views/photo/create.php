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

        <div class="container site-header-inner">

            <a
                href="/"
                class="logo"
            >
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
                    class="nav-link active"
                    aria-current="page"
                >
                    New photo
                </a>

                <a
                    href="/profile"
                    class="nav-link"
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

    <main class="photo-create-main">

        <section class="photo-create">

            <div class="photo-create-header">

                <p class="photo-create-eyebrow">
                    CAMERA
                </p>

                <h1>
                    Create a photo
                </h1>

                <p>
                    Take a photo or upload one and choose an overlay.
                </p>

            </div>

            <div class="photo-create-layout">

                <!-- CAMERA -->
                <section class="photo-camera-panel">

                    <div class="photo-camera-container">

                        <video
                            id="camera"
                            autoplay
                            playsinline
                        ></video>

                        <div
                            id="camera-message"
                            class="photo-camera-message"
                        >
                            Start your camera to take a photo.
                        </div>

                    </div>

                    <p
                        id="camera-error"
                        class="form-error"
                        hidden
                    ></p>

                    <!-- CAMERA CONTROLS -->
                    <div class="photo-camera-controls">

                        <button
                            type="button"
                            id="start-camera"
                            class="photo-camera-start"
                        >
                            Start camera
                        </button>

                        <button
                            type="button"
                            id="take-picture"
                            class="photo-camera-capture"
                            disabled
                            aria-label="Take picture"
                        >
                            <span class="photo-camera-capture-icon"></span>
                        </button>

                        <button
                            type="button"
                            id="stop-camera"
                            class="photo-camera-stop"
                            disabled
                        >
                            Stop camera
                        </button>

                    </div>

                </section>

                <!-- CONTROLS -->
                <aside class="photo-create-controls">

                    <section class="photo-overlay-section">

                        <div class="photo-section-heading">

                            <div>

                                <p>
                                    FILTER
                                </p>

                                <h2>
                                    Choose an overlay
                                </h2>

                            </div>

                            <span>
                                Required
                            </span>

                        </div>

                        <div class="photo-overlay-list">

                            <button
                                type="button"
                                class="photo-overlay-option overlay-option"
                                data-overlay="overlay1"
                                aria-label="Select overlay 1"
                            >

                                <span class="photo-overlay-image">

                                    <img
                                        src="/assets/overlays/overlay1.png"
                                        alt="Overlay 1"
                                    >

                                </span>

                                <span class="photo-overlay-name">
                                    Overlay 1
                                </span>

                            </button>

                            <button
                                type="button"
                                class="photo-overlay-option overlay-option"
                                data-overlay="overlay2"
                                aria-label="Select overlay 2"
                            >

                                <span class="photo-overlay-image">

                                    <img
                                        src="/assets/overlays/overlay2.png"
                                        alt="Overlay 2"
                                    >

                                </span>

                                <span class="photo-overlay-name">
                                    Overlay 2
                                </span>

                            </button>

                            <button
                                type="button"
                                class="photo-overlay-option overlay-option"
                                data-overlay="overlay3"
                                aria-label="Select overlay 3"
                            >

                                <span class="photo-overlay-image">

                                    <img
                                        src="/assets/overlays/overlay3.png"
                                        alt="Overlay 3"
                                    >

                                </span>

                                <span class="photo-overlay-name">
                                    Overlay 3
                                </span>

                            </button>

                        </div>

                    </section>

                    <!-- UPLOAD -->

                    <section class="photo-upload-section">

                        <div class="photo-upload-divider">

                            <span>
                                OR
                            </span>

                        </div>

                        <button
                            type="button"
                            id="upload-image"
                            class="photo-upload-button"
                        >
                            Upload an image
                        </button>

                        <p>
                            JPEG or PNG · Maximum 5 MB
                        </p>

                    </section>

                    <!-- PREVIOUS PHOTOS -->

                    <section class="photo-previous-section">

                        <div class="photo-section-heading">

                            <div>

                                <p>
                                    YOUR PHOTOS
                                </p>

                                <h2>
                                    Previous photos
                                </h2>

                            </div>

                        </div>

                        <?php if (empty($previousImages)): ?>

                            <div class="photo-previous-empty">

                                <p>
                                    You have not created any photos yet.
                                </p>

                            </div>

                        <?php else: ?>

                            <div
                                id="previous-photos"
                                class="photo-previous-grid"
                            >

                                <?php foreach ($previousImages as $image): ?>

                                    <div class="photo-previous-item">

                                        <img
                                            src="/uploads/<?= htmlspecialchars(
                                                $image['filename'],
                                                ENT_QUOTES,
                                                'UTF-8'
                                            ) ?>"
                                            alt="Previous photo"
                                        >

                                        <form
                                            action="/photo/delete"
                                            method="POST"
                                            class="delete-photo-form"
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
                                                name="image_id"
                                                value="<?= (int) $image['id'] ?>"
                                            >

                                            <button
                                                type="submit"
                                                class="delete-photo-button"
                                            >
                                                Delete
                                            </button>

                                        </form>

                                    </div>

                                <?php endforeach; ?>

                            </div>

                        <?php endif; ?>

                    </section>

                </aside>

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
                    Create. Share. Connect.
                </span>

            </div>

            <p>
                &copy; 2026 Camagru
            </p>

        </div>

    </footer>

<!-- CANVAS USED TO CAPTURE THE CAMERA FRAME -->
<canvas
    id="photo-canvas"
    hidden
></canvas>

<!-- PHOTO FORM -->
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

<script src="/assets/js/photo-editor.js"></script>

</body>

</html>