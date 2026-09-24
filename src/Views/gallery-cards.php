<?php foreach ($images as $image): ?>

    <article class="gallery-card">

        <div class="gallery-photo-wrapper">

            <img
                src="/uploads/<?= htmlspecialchars(
                    $image['filename'],
                    ENT_QUOTES,
                    'UTF-8'
                ) ?>"
                alt="Photo uploaded by <?= htmlspecialchars(
                    $image['username'],
                    ENT_QUOTES,
                    'UTF-8'
                ) ?>"
            >

        </div>

        <div class="gallery-card-content">

            <div class="gallery-photo-info">

                <div class="gallery-avatar">
                    <?= htmlspecialchars(
                        strtoupper(
                            substr(
                                $image['username'],
                                0,
                                1
                            )
                        ),
                        ENT_QUOTES,
                        'UTF-8'
                    ) ?>
                </div>

                <div class="gallery-photo-user">

                    <strong>
                        <?= htmlspecialchars(
                            $image['username'],
                            ENT_QUOTES,
                            'UTF-8'
                        ) ?>
                    </strong>

                    <span>
                        <?= htmlspecialchars(
                            $image['created_at'],
                            ENT_QUOTES,
                            'UTF-8'
                        ) ?>
                    </span>

                </div>

            </div>

            <!-- COMMENTS TOGGLE -->
            <input
                type="checkbox"
                id="comments-<?= (int) $image['id'] ?>"
                class="gallery-comments-toggle"
            >

            <!-- SOCIAL ACTIONS -->
            <div class="gallery-social-actions">

                <!-- LIKE -->
                <?php if (isset($_SESSION['user_id'])): ?>

                    <form
                        action="/gallery/like"
                        method="POST"
                        class="gallery-like-form-new"
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

                        <input
                            type="hidden"
                            name="page"
                            value="<?= (int) $page ?>"
                        >

                        <button
                            type="submit"
                            class="gallery-heart-button <?= $image['user_liked'] ? 'is-liked' : '' ?>"
                            aria-label="<?= $image['user_liked'] ? 'Remove like' : 'Like photo' ?>"
                        >

                            <svg
                                class="gallery-heart-icon"
                                viewBox="0 0 24 24"
                                aria-hidden="true"
                            >
                                <path
                                    d="M20.84 4.61a5.5 5.5 0 0 0-7.78 0L12 5.67l-1.06-1.06a5.5 5.5 0 0 0-7.78 7.78L12 21.23l8.84-8.84a5.5 5.5 0 0 0 0-7.78z"
                                />
                            </svg>

                        </button>

                    </form>

                <?php else: ?>

                    <div
                        class="gallery-heart-display"
                        aria-label="Likes"
                    >

                        <svg
                            class="gallery-heart-icon"
                            viewBox="0 0 24 24"
                            aria-hidden="true"
                        >
                            <path
                                d="M20.84 4.61a5.5 5.5 0 0 0-7.78 0L12 5.67l-1.06-1.06a5.5 5.5 0 0 0-7.78 7.78L12 21.23l8.84-8.84a5.5 5.5 0 0 0 0-7.78z"
                            />
                        </svg>

                    </div>

                <?php endif; ?>

                <span class="gallery-like-number">
                    <?= (int) $image['like_count'] ?>
                </span>

                <label
                    for="comments-<?= (int) $image['id'] ?>"
                    class="gallery-comments-button"
                    aria-label="Open comments"
                >

                    <svg
                        class="gallery-comment-icon"
                        viewBox="0 0 24 24"
                        aria-hidden="true"
                    >
                        <path
                            d="M21 11.5a8.38 8.38 0 0 1-.9 3.8 8.5 8.5 0 0 1-7.6 4.7 8.38 8.38 0 0 1-3.8-.9L3 21l1.9-5.7a8.38 8.38 0 0 1-.9-3.8 8.5 8.5 0 0 1 4.7-7.6 8.38 8.38 0 0 1 3.8-.9h.5a8.48 8.48 0 0 1 8 8v.5z"
                        />
                    </svg>

                </label>

                <span class="gallery-comment-number">
                    <?= count($image['comments']) ?>
                </span>

            </div>

            <!-- COMMENTS POPUP -->

            <div class="gallery-comments-overlay">

                <label
                    for="comments-<?= (int) $image['id'] ?>"
                    class="gallery-comments-overlay-close"
                    aria-label="Close comments"
                >
                </label>

                <div
                    class="gallery-comments-popup"
                    role="dialog"
                    aria-modal="true"
                    aria-label="Comments"
                >

                    <div class="gallery-comments-popup-header">

                        <div>

                            <h2>
                                Comments
                            </h2>

                            <span>
                                <?= count($image['comments']) ?>
                                <?= count($image['comments']) === 1 ? 'comment' : 'comments' ?>
                            </span>

                        </div>

                        <label
                            for="comments-<?= (int) $image['id'] ?>"
                            class="gallery-comments-close"
                            aria-label="Close comments"
                        >
                            &times;
                        </label>

                    </div>

                    <div class="gallery-comments-popup-list">

                        <?php if (empty($image['comments'])): ?>

                            <div class="gallery-comments-empty">

                                <svg
                                    class="gallery-empty-comment-icon"
                                    viewBox="0 0 24 24"
                                    aria-hidden="true"
                                >
                                    <path
                                        d="M21 11.5a8.38 8.38 0 0 1-.9 3.8 8.5 8.5 0 0 1-7.6 4.7 8.38 8.38 0 0 1-3.8-.9L3 21l1.9-5.7a8.38 8.38 0 0 1 4.7-7.6 8.5 8.5 0 0 1 4.7-7.6 8.38 8.38 0 0 1 3.8-0.9h.5a8.48 8.48 0 0 1 8 8v.5z"
                                    />
                                </svg>

                                <p>
                                    No comments yet.
                                </p>

                            </div>

                        <?php else: ?>

                            <?php foreach ($image['comments'] as $comment): ?>

                                <div class="gallery-popup-comment">

                                    <div class="gallery-popup-comment-header">

                                        <strong>
                                            <?= htmlspecialchars(
                                                $comment['username'],
                                                ENT_QUOTES,
                                                'UTF-8'
                                            ) ?>
                                        </strong>

                                        <span>
                                            <?= htmlspecialchars(
                                                $comment['created_at'],
                                                ENT_QUOTES,
                                                'UTF-8'
                                            ) ?>
                                        </span>

                                    </div>

                                    <p>
                                        <?= nl2br(
                                            htmlspecialchars(
                                                $comment['content'],
                                                ENT_QUOTES,
                                                'UTF-8'
                                            )
                                        ) ?>
                                    </p>

                                </div>

                            <?php endforeach; ?>

                        <?php endif; ?>

                    </div>

                    <?php if (isset($_SESSION['user_id'])): ?>

                        <form
                            action="/gallery/comment"
                            method="POST"
                            class="gallery-popup-comment-form"
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

                            <input
                                type="hidden"
                                name="page"
                                value="<?= (int) $page ?>"
                            >

                            <label
                                for="popup-comment-<?= (int) $image['id'] ?>"
                                class="gallery-popup-comment-label"
                            >
                                Add a comment
                            </label>

                            <div class="gallery-popup-comment-input">

                                <textarea
                                    id="popup-comment-<?= (int) $image['id'] ?>"
                                    name="content"
                                    maxlength="1000"
                                    required
                                    placeholder="Write a comment..."
                                ></textarea>

                                <button
                                    type="submit"
                                    class="gallery-popup-comment-submit"
                                    aria-label="Send comment"
                                >

                                    <svg
                                        viewBox="0 0 24 24"
                                        aria-hidden="true"
                                    >
                                        <path
                                            d="M22 2L11 13"
                                        />
                                        <path
                                            d="M22 2l-7 20-4-9-9-4z"
                                        />
                                    </svg>

                                </button>

                            </div>

                        </form>

                    <?php else: ?>

                        <div class="gallery-popup-login-message">

                            <p>
                                <a href="/login">
                                    Login
                                </a>
                                to leave a comment.
                            </p>

                        </div>

                    <?php endif; ?>

                </div>

            </div>

        </div>

    </article>

<?php endforeach; ?>