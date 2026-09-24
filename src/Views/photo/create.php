<!DOCTYPE html>
<html lang="en">

<head>

    <meta charset="UTF-8">

    <meta
        name="viewport"
        content="width=device-width, initial-scale=1.0"
    >

    <link rel="icon" href="/favicon.ico">

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

                        <canvas
                            id="live-overlay-canvas"
                            aria-hidden="true"
                        ></canvas>

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

                </section>

                <!-- PHOTO PREVIEW -->

                <div
                    id="photo-preview-section"
                    class="photo-preview-modal"
                    hidden
                    aria-hidden="true"
                >
                    <div
                        class="photo-preview-modal-content"
                        role="dialog"
                        aria-modal="true"
                        aria-labelledby="photo-preview-title"
                    >

                        <button
                            type="button"
                            id="close-photo-preview"
                            class="photo-preview-close"
                            aria-label="Discard photo"
                        >
                            &times;
                        </button>

                        <div class="photo-preview-modal-header">

                            <p>
                                PREVIEW
                            </p>

                            <h2 id="photo-preview-title">
                                Your photo
                            </h2>

                        </div>

                        <div class="photo-preview-container">

                            <canvas
                                id="photo-preview-canvas"
                            ></canvas>

                        </div>

                        <button
                            type="button"
                            id="submit-photo"
                            class="photo-submit-button"
                            disabled
                        >
                            Upload photo
                        </button>

                        <p class="photo-preview-help">
                            If you do not like the result, close the preview and take another photo.
                        </p>

                    </div>
                </div>

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
                                data-overlay="01"
                                aria-label="Select overlay 01"
                            >

                                <span class="photo-overlay-image">

                                    <img
                                        src="/assets/overlays/01_laptop_programacion.png"
                                        alt="Laptop programming"
                                    >

                                </span>

                                <span class="photo-overlay-name">
                                    Laptop programming
                                </span>

                            </button>

                            <button
                                type="button"
                                class="photo-overlay-option overlay-option"
                                data-overlay="02"
                                aria-label="Select overlay 02"
                            >

                                <span class="photo-overlay-image">

                                    <img
                                        src="/assets/overlays/02_42_madrid.png"
                                        alt="42 Madrid"
                                    >

                                </span>

                                <span class="photo-overlay-name">
                                    42 Madrid
                                </span>

                            </button>

                            <button
                                type="button"
                                class="photo-overlay-option overlay-option"
                                data-overlay="03"
                                aria-label="Select overlay 03"
                            >

                                <span class="photo-overlay-image">

                                    <img
                                        src="/assets/overlays/03_devs_no_duermen.png"
                                        alt="Devs no duermen"
                                    >

                                </span>

                                <span class="photo-overlay-name">
                                    Devs no duermen
                                </span>

                            </button>

                            <button
                                type="button"
                                class="photo-overlay-option overlay-option"
                                data-overlay="04"
                                aria-label="Select overlay 04"
                            >

                                <span class="photo-overlay-image">

                                    <img
                                        src="/assets/overlays/04_gaming.png"
                                        alt="Gaming"
                                    >

                                </span>

                                <span class="photo-overlay-name">
                                    Gaming
                                </span>

                            </button>

                            <button
                                type="button"
                                class="photo-overlay-option overlay-option"
                                data-overlay="05"
                                aria-label="Select overlay 05"
                            >

                                <span class="photo-overlay-image">

                                    <img
                                        src="/assets/overlays/05_viajes_montana.png"
                                        alt="Mountain travel"
                                    >

                                </span>

                                <span class="photo-overlay-name">
                                    Mountain travel
                                </span>

                            </button>

                            <button
                                type="button"
                                class="photo-overlay-option overlay-option"
                                data-overlay="06"
                                aria-label="Select overlay 06"
                            >

                                <span class="photo-overlay-image">

                                    <img
                                        src="/assets/overlays/06_cafe_programador.png"
                                        alt="Programmer coffee"
                                    >

                                </span>

                                <span class="photo-overlay-name">
                                    Programmer coffee
                                </span>

                            </button>

                            <button
                                type="button"
                                class="photo-overlay-option overlay-option"
                                data-overlay="07"
                                aria-label="Select overlay 07"
                            >

                                <span class="photo-overlay-image">

                                    <img
                                        src="/assets/overlays/07_minecraft_pixel.png"
                                        alt="Minecraft pixel"
                                    >

                                </span>

                                <span class="photo-overlay-name">
                                    Minecraft pixel
                                </span>

                            </button>

                            <button
                                type="button"
                                class="photo-overlay-option overlay-option"
                                data-overlay="08"
                                aria-label="Select overlay 08"
                            >

                                <span class="photo-overlay-image">

                                    <img
                                        src="/assets/overlays/08_linux_forever.png"
                                        alt="Linux forever"
                                    >

                                </span>

                                <span class="photo-overlay-name">
                                    Linux forever
                                </span>

                            </button>

                            <button
                                type="button"
                                class="photo-overlay-option overlay-option"
                                data-overlay="09"
                                aria-label="Select overlay 09"
                            >

                                <span class="photo-overlay-image">

                                    <img
                                        src="/assets/overlays/09_ramen.png"
                                        alt="Ramen"
                                    >

                                </span>

                                <span class="photo-overlay-name">
                                    Ramen
                                </span>

                            </button>

                            <button
                                type="button"
                                class="photo-overlay-option overlay-option"
                                data-overlay="10"
                                aria-label="Select overlay 10"
                            >

                                <span class="photo-overlay-image">

                                    <img
                                        src="/assets/overlays/10_tu_puedes.png"
                                        alt="Tu puedes"
                                    >

                                </span>

                                <span class="photo-overlay-name">
                                    Tu puedes
                                </span>

                            </button>

                            <button
                                type="button"
                                class="photo-overlay-option overlay-option"
                                data-overlay="11"
                                aria-label="Select overlay 11"
                            >

                                <span class="photo-overlay-image">

                                    <img
                                        src="/assets/overlays/11_tiburon_good_vibes.png"
                                        alt="Good vibes"
                                    >

                                </span>

                                <span class="photo-overlay-name">
                                    Good vibes
                                </span>

                            </button>

                            <button
                                type="button"
                                class="photo-overlay-option overlay-option"
                                data-overlay="12"
                                aria-label="Select overlay 12"
                            >

                                <span class="photo-overlay-image">

                                    <img
                                        src="/assets/overlays/12_astroespacio.png"
                                        alt="Astro space"
                                    >

                                </span>

                                <span class="photo-overlay-name">
                                    Astro space
                                </span>

                            </button>

                            <button
                                type="button"
                                class="photo-overlay-option overlay-option"
                                data-overlay="13"
                                aria-label="Select overlay 13"
                            >

                                <span class="photo-overlay-image">

                                    <img
                                        src="/assets/overlays/13_terminal_keep_going.png"
                                        alt="Terminal keep going"
                                    >

                                </span>

                                <span class="photo-overlay-name">
                                    Terminal keep going
                                </span>

                            </button>

                            <button
                                type="button"
                                class="photo-overlay-option overlay-option"
                                data-overlay="14"
                                aria-label="Select overlay 14"
                            >

                                <span class="photo-overlay-image">

                                    <img
                                        src="/assets/overlays/14_good_boy_42.png"
                                        alt="Good boy 42"
                                    >

                                </span>

                                <span class="photo-overlay-name">
                                    Good boy 42
                                </span>

                            </button>

                            <button
                                type="button"
                                class="photo-overlay-option overlay-option"
                                data-overlay="15"
                                aria-label="Select overlay 15"
                            >

                                <span class="photo-overlay-image">

                                    <img
                                        src="/assets/overlays/15_disciplina_montana.png"
                                        alt="Mountain discipline"
                                    >

                                </span>

                                <span class="photo-overlay-name">
                                    Mountain discipline
                                </span>

                            </button>

                            <button
                                type="button"
                                class="photo-overlay-option overlay-option"
                                data-overlay="16"
                                aria-label="Select overlay 16"
                            >

                                <span class="photo-overlay-image">

                                    <img
                                        src="/assets/overlays/16_coder_sonoliento.png"
                                        alt="Sleepy coder"
                                    >

                                </span>

                                <span class="photo-overlay-name">
                                    Sleepy coder
                                </span>

                            </button>

                            <button
                                type="button"
                                class="photo-overlay-option overlay-option"
                                data-overlay="17"
                                aria-label="Select overlay 17"
                            >

                                <span class="photo-overlay-image">

                                    <img
                                        src="/assets/overlays/17_banana_lets_go.png"
                                        alt="Banana let's go"
                                    >

                                </span>

                                <span class="photo-overlay-name">
                                    Banana let's go
                                </span>

                            </button>

                            <button
                                type="button"
                                class="photo-overlay-option overlay-option"
                                data-overlay="18"
                                aria-label="Select overlay 18"
                            >

                                <span class="photo-overlay-image">

                                    <img
                                        src="/assets/overlays/18_42_cursor.png"
                                        alt="42 cursor"
                                    >

                                </span>

                                <span class="photo-overlay-name">
                                    42 cursor
                                </span>

                            </button>

                            <button
                                type="button"
                                class="photo-overlay-option overlay-option"
                                data-overlay="19"
                                aria-label="Select overlay 19"
                            >

                                <span class="photo-overlay-image">

                                    <img
                                        src="/assets/overlays/19_pizza.png"
                                        alt="Pizza"
                                    >

                                </span>

                                <span class="photo-overlay-name">
                                    Pizza
                                </span>

                            </button>

                            <button
                                type="button"
                                class="photo-overlay-option overlay-option"
                                data-overlay="20"
                                aria-label="Select overlay 20"
                            >

                                <span class="photo-overlay-image">

                                    <img
                                        src="/assets/overlays/20_cactus.png"
                                        alt="Cactus"
                                    >

                                </span>

                                <span class="photo-overlay-name">
                                    Cactus
                                </span>

                            </button>

                            <button
                                type="button"
                                class="photo-overlay-option overlay-option"
                                data-overlay="21"
                                aria-label="Select overlay 21"
                            >

                                <span class="photo-overlay-image">

                                    <img
                                        src="/assets/overlays/21_dog.png"
                                        alt="Dog"
                                    >

                                </span>

                                <span class="photo-overlay-name">
                                    Dog
                                </span>

                            </button>

                            <button
                                type="button"
                                class="photo-overlay-option overlay-option"
                                data-overlay="22"
                                aria-label="Select overlay 22"
                            >

                                <span class="photo-overlay-image">

                                    <img
                                        src="/assets/overlays/22_gafas_bigote.png"
                                        alt="Glasses and moustache"
                                    >

                                </span>

                                <span class="photo-overlay-name">
                                    Glasses and moustache
                                </span>

                            </button>

                            <button
                                type="button"
                                class="photo-overlay-option overlay-option"
                                data-overlay="23"
                                aria-label="Select overlay 23"
                            >

                                <span class="photo-overlay-image">

                                    <img
                                        src="/assets/overlays/23_ojos.png"
                                        alt="Eyes"
                                    >

                                </span>

                                <span class="photo-overlay-name">
                                    Eyes
                                </span>

                            </button>

                            <button
                                type="button"
                                class="photo-overlay-option overlay-option"
                                data-overlay="101"
                                aria-label="Select overlay 101"
                            >

                                <span class="photo-overlay-image">

                                    <img
                                        src="/assets/overlays/101_playa_tropical.png"
                                        alt="Tropical beach"
                                    >

                                </span>

                                <span class="photo-overlay-name">
                                    Tropical beach
                                </span>

                            </button>

                            <button
                                type="button"
                                class="photo-overlay-option overlay-option"
                                data-overlay="102"
                                aria-label="Select overlay 102"
                            >

                                <span class="photo-overlay-image">

                                    <img
                                        src="/assets/overlays/102_romantico_kawaii.png"
                                        alt="Romantic kawaii"
                                    >

                                </span>

                                <span class="photo-overlay-name">
                                    Romantic kawaii
                                </span>

                            </button>

                            <button
                                type="button"
                                class="photo-overlay-option overlay-option"
                                data-overlay="103"
                                aria-label="Select overlay 103"
                            >

                                <span class="photo-overlay-image">

                                    <img
                                        src="/assets/overlays/103_cine_film.png"
                                        alt="Cinema film"
                                    >

                                </span>

                                <span class="photo-overlay-name">
                                    Cinema film
                                </span>

                            </button>

                            <button
                                type="button"
                                class="photo-overlay-option overlay-option"
                                data-overlay="104"
                                aria-label="Select overlay 104"
                            >

                                <span class="photo-overlay-image">

                                    <img
                                        src="/assets/overlays/104_aventura_montana.png"
                                        alt="Mountain adventure"
                                    >

                                </span>

                                <span class="photo-overlay-name">
                                    Mountain adventure
                                </span>

                            </button>

                            <button
                                type="button"
                                class="photo-overlay-option overlay-option"
                                data-overlay="105"
                                aria-label="Select overlay 105"
                            >

                                <span class="photo-overlay-image">

                                    <img
                                        src="/assets/overlays/105_halloween.png"
                                        alt="Halloween"
                                    >

                                </span>

                                <span class="photo-overlay-name">
                                    Halloween
                                </span>

                            </button>

                        </div>

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
        type="hidden"
        id="overlay-x"
        name="overlay_x"
        value=""
    >

    <input
        type="hidden"
        id="overlay-y"
        name="overlay_y"
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