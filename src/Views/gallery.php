<!DOCTYPE html>
<html lang="en">

<head>

    <meta charset="UTF-8">

    <meta
        name="viewport"
        content="width=device-width, initial-scale=1.0"
    >

    <link rel="icon" href="/favicon.ico">

    <title>Camagru - Gallery</title>

    <link
        rel="stylesheet"
        href="/assets/css/style.css"
    >

    <script>

        const galleryInfiniteModeFromStorage = localStorage.getItem('camagru-gallery-pagination-mode') === 'infinite';
        const galleryHasPageParameter = new URLSearchParams(window.location.search).has('page');

        if (galleryInfiniteModeFromStorage && galleryHasPageParameter)
        {
            window.location.replace('/gallery');
        }

    </script>

</head>

<body>

    <header class="site-header">

        <div class="container site-header-inner">

            <a href="/" class="logo">
                Camagru
            </a>

            <nav class="main-nav" aria-label="Main navigation">

                <a
                    href="/"
                    class="nav-link"
                >
                    Home
                </a>

                <a
                    href="/gallery"
                    class="nav-link active"
                    aria-current="page"
                >
                    Gallery
                </a>

                <?php if (isset($_SESSION['user_id'])): ?>

                    <a
                        href="/photo/create"
                        class="nav-link"
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

                <?php else: ?>

                    <a
                        href="/login"
                        class="nav-link"
                    >
                        Login
                    </a>

                    <a
                        href="/register"
                        class="nav-register"
                    >
                        Sign up
                    </a>

                <?php endif; ?>

            </nav>

        </div>

    </header>

    <main class="gallery-main">

        <section class="gallery-hero">

            <div class="gallery-container">

                <div class="gallery-intro">

                    <p class="gallery-eyebrow">
                        COMMUNITY
                    </p>

                    <h1>
                        Gallery
                    </h1>

                    <p class="gallery-description">
                        Discover photos created by the Camagru community.
                    </p>

                </div>

            </div>

        </section>

        <section class="gallery-content">

            <div class="gallery-container">

                <?php if ($invalidPage): ?>

                    <div class="gallery-empty-state">

                        <div class="gallery-empty-icon">
                            !
                        </div>

                        <h2>
                            Page not available
                        </h2>

                        <p>
                            There are not enough photos to reach that page.
                        </p>

                        <a
                            href="/gallery"
                            class="gallery-primary-button"
                        >
                            Back to gallery
                        </a>

                    </div>

                <?php elseif (empty($images)): ?>

                    <div class="gallery-empty-state">

                        <div class="gallery-empty-icon">
                            +
                        </div>

                        <h2>
                            No photos yet
                        </h2>

                        <p>
                            There are no photos in the gallery yet.
                            Create your first photo and share it with the community.
                        </p>

                        <?php if (isset($_SESSION['user_id'])): ?>

                            <a
                                href="/photo/create"
                                class="gallery-primary-button"
                            >
                                Create photo
                            </a>

                        <?php else: ?>

                            <a
                                href="/login"
                                class="gallery-primary-button"
                            >
                                Login
                            </a>

                        <?php endif; ?>

                    </div>

                <?php else: ?>

                    <div
                        class="gallery-grid"
                        id="gallery-grid"
                    >
                        <?php require __DIR__ . '/gallery-cards.php'; ?>
                    </div>

                    <div
                        id="gallery-infinite-loader"
                        class="gallery-infinite-loader"
                        aria-hidden="true"
                        hidden
                    >
                        <span class="gallery-infinite-spinner"></span>

                        <span>
                            Loading more photos...
                        </span>
                    </div>

                    <div
                        id="gallery-infinite-end"
                        class="gallery-infinite-end"
                        hidden
                    >
                        You've reached the end of the gallery.
                    </div>

                    <?php if ($totalPages > 1): ?>

                        <nav
                            class="gallery-pagination"
                            id="gallery-traditional-pagination"
                            aria-label="Gallery pages"
                        >

                            <?php if ($page > 1): ?>

                                <a
                                    href="/gallery?page=<?= $page - 1 ?>"
                                    class="gallery-pagination-link"
                                >
                                    Previous
                                </a>

                            <?php endif; ?>

                            <div class="gallery-pagination-numbers">

                                <?php for (
                                    $pageNumber = 1;
                                    $pageNumber <= $totalPages;
                                    $pageNumber++
                                ): ?>

                                    <?php if ($pageNumber === $page): ?>

                                        <strong
                                            class="gallery-pagination-current"
                                            aria-current="page"
                                        >
                                            <?= $pageNumber ?>
                                        </strong>

                                    <?php else: ?>

                                        <a
                                            href="/gallery?page=<?= $pageNumber ?>"
                                            class="gallery-pagination-number"
                                        >
                                            <?= $pageNumber ?>
                                        </a>

                                    <?php endif; ?>

                                <?php endfor; ?>

                            </div>

                            <?php if ($page < $totalPages): ?>

                                <a
                                    href="/gallery?page=<?= $page + 1 ?>"
                                    class="gallery-pagination-link"
                                >
                                    Next
                                </a>

                            <?php endif; ?>

                        </nav>

                    <?php endif; ?>

                <?php endif; ?>

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

    <script>

        const galleryGrid = document.getElementById('gallery-grid');

        const galleryTraditionalPagination = document.getElementById('gallery-traditional-pagination');

        const galleryInfiniteLoader =
            document.getElementById(
                'gallery-infinite-loader'
            );

        const galleryInfiniteEnd =
            document.getElementById(
                'gallery-infinite-end'
            );


        const galleryInfiniteMode =
            localStorage.getItem(
                'camagru-gallery-pagination-mode'
            ) === 'infinite';


        let galleryNextPage =
            <?= (int) $page + 1 ?>;

        let galleryHasMore =
            <?= $page < $totalPages ? 'true' : 'false' ?>;

        let galleryLoading = false;


        function updateGalleryPaginationMode()
        {
            if (galleryInfiniteMode)
            {
                if (galleryTraditionalPagination)
                {
                    galleryTraditionalPagination.hidden = true;
                }

                if (galleryHasMore)
                {
                    galleryInfiniteLoader.hidden = false;
                    galleryInfiniteEnd.hidden = true;
                }
                else
                {
                    galleryInfiniteLoader.hidden = true;
                    galleryInfiniteEnd.hidden = false;
                }
            }
            else
            {
                if (galleryTraditionalPagination)
                {
                    galleryTraditionalPagination.hidden = false;
                }

                galleryInfiniteLoader.hidden = true;
                galleryInfiniteEnd.hidden = true;
            }
        }


        async function loadNextGalleryPage()
        {
            if (
                galleryLoading ||
                !galleryHasMore ||
                !galleryInfiniteMode
            )
            {
                return;
            }

            galleryLoading = true;

            galleryInfiniteLoader.hidden = false;

            try
            {
                const response = await fetch(
                    '/gallery/load?page=' +
                    galleryNextPage,
                    {
                        method: 'GET',

                        headers: {
                            'Accept': 'application/json'
                        }
                    }
                );


                if (!response.ok)
                {
                    throw new Error(
                        'Unable to load more photos.'
                    );
                }


                const data =
                    await response.json();


                galleryGrid.insertAdjacentHTML(
                    'beforeend',
                    data.html
                );


                galleryHasMore =
                    data.hasMore;

                galleryNextPage =
                    data.nextPage;


                if (!galleryHasMore)
                {
                    galleryInfiniteLoader.hidden = true;
                    galleryInfiniteEnd.hidden = false;
                }
            }
            catch (error)
            {
                galleryInfiniteLoader.hidden = true;
            }
            finally
            {
                galleryLoading = false;
            }
        }


        updateGalleryPaginationMode();


        if (galleryInfiniteMode)
        {
            const galleryInfiniteObserver =
                new IntersectionObserver(
                    function (entries)
                    {
                        if (
                            entries[0].isIntersecting
                        )
                        {
                            loadNextGalleryPage();
                        }
                    },
                    {
                        rootMargin: '500px'
                    }
                );


            galleryInfiniteObserver.observe(
                galleryInfiniteLoader
            );
        }

    </script>
</body>

</html>