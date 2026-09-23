<!DOCTYPE html>
<html lang="en">
<head>
	<meta charset="UTF-8">
	<meta name="viewport" content="width=device-width, initial-scale=1.0">

	<title>Camagru</title>

	<link rel="stylesheet" href="/assets/css/style.css">
</head>

<body>

	<header class="site-header">
		<div class="container site-header-inner">

			<a href="/" class="logo">
				Camagru
			</a>

			<nav class="main-nav" aria-label="Main navigation">

				<a href="/" class="nav-link active">
					Home
				</a>

				<a href="/gallery" class="nav-link">
					Gallery
				</a>

				<?php if ($isAuthenticated): ?>

					<a href="/photo/create" class="nav-link">
						Create
					</a>

					<a href="/profile" class="nav-link">
						Profile
					</a>

					<form action="/logout" method="POST" class="logout-form">

						<input
							type="hidden"
							name="csrf_token"
							value="<?= htmlspecialchars(
								$csrfToken,
								ENT_QUOTES,
								'UTF-8'
							) ?>"
						>

						<button type="submit" class="nav-button">
							Logout
						</button>

					</form>

				<?php else: ?>

					<a href="/login" class="nav-link">
						Login
					</a>

					<a href="/register" class="nav-register">
						Sign up
					</a>

				<?php endif; ?>

			</nav>

		</div>
	</header>


	<main class="home-main">

		<section class="home-hero">

			<div class="home-hero-content">

				<?php if ($isAuthenticated): ?>

					<p class="home-eyebrow">
						WELCOME BACK
					</p>

					<h1>
						Welcome, <?= htmlspecialchars(
							$username,
							ENT_QUOTES,
							'UTF-8'
						) ?>
					</h1>

					<p class="home-hero-text">
						Capture your moments, add a creative touch
						and share them with the Camagru community.
					</p>

					<div class="home-hero-actions">

						<a href="/photo/create" class="home-primary-button">
							Create a photo
						</a>

						<a href="/gallery" class="home-secondary-button">
							Explore gallery
						</a>

					</div>

				<?php else: ?>

					<p class="home-eyebrow">
						CAMAGRU COMMUNITY
					</p>

					<h1>
						Create. Capture. Share.
					</h1>

					<p class="home-hero-text">
						Create unique photos with your webcam,
						add creative overlays and share your
						creations with the community.
					</p>

					<div class="home-hero-actions">

						<a href="/register" class="home-primary-button">
							Create an account
						</a>

						<a href="/gallery" class="home-secondary-button">
							Explore gallery
						</a>

					</div>

				<?php endif; ?>

			</div>

		</section>


		<section class="home-gallery-section">

			<div class="home-section-header">

				<div>
					<p class="home-section-eyebrow">
						COMMUNITY
					</p>

					<h2>
						Recent photos
					</h2>
				</div>

				<a href="/gallery" class="view-gallery-link">
					View all
				</a>

			</div>


			<?php if (count($recentImages) > 0): ?>

				<div class="home-photo-grid">

					<?php foreach ($recentImages as $image): ?>

						<article class="home-photo-card">

							<div class="home-photo-wrapper">

								<img
									src="/uploads/<?= htmlspecialchars(
										$image['filename'],
										ENT_QUOTES,
										'UTF-8'
									) ?>"
									alt="Photo by <?= htmlspecialchars(
										$image['username'],
										ENT_QUOTES,
										'UTF-8'
									) ?>"
								>

							</div>

							<div class="home-photo-info">

								<div class="home-photo-user">

									<div class="home-avatar">
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

									<div>

										<strong>
											<?= htmlspecialchars(
												$image['username'],
												ENT_QUOTES,
												'UTF-8'
											) ?>
										</strong>

										<span>
											Camagru community
										</span>

									</div>

								</div>

							</div>

						</article>

					<?php endforeach; ?>

				</div>

			<?php else: ?>

				<div class="home-empty-state">

					<div class="home-empty-icon">
						+
					</div>

					<h3>
						No photos yet
					</h3>

					<p>
						Be the first person to create and share
						a photo with the community.
					</p>

					<?php if ($isAuthenticated): ?>

						<a
							href="/photo/create"
							class="home-primary-button"
						>
							Create the first photo
						</a>

					<?php else: ?>

						<a
							href="/register"
							class="home-primary-button"
						>
							Join Camagru
						</a>

					<?php endif; ?>

				</div>

			<?php endif; ?>

		</section>


		<section class="home-feature-section">

			<div class="home-feature">

				<div class="home-feature-icon">
					01
				</div>

				<div>
					<h3>
						Create
					</h3>

					<p>
						Use your webcam or upload an image
						and combine it with one of the available
						overlays.
					</p>
				</div>

			</div>


			<div class="home-feature">

				<div class="home-feature-icon">
					02
				</div>

				<div>
					<h3>
						Customize
					</h3>

					<p>
						Choose the overlay that best fits your
						photo and create your final composition.
					</p>
				</div>

			</div>


			<div class="home-feature">

				<div class="home-feature-icon">
					03
				</div>

				<div>
					<h3>
						Share
					</h3>

					<p>
						Your creations become part of the public
						Camagru gallery where users can interact
						with them.
					</p>
				</div>

			</div>

		</section>

	</main>


	<footer class="site-footer">
		<div class="container footer-inner">

			<div>
				<strong>Camagru</strong>
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
