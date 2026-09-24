//lo que hace es buscar en los archivos html del proyecto(que en este caso lo hace en el create.php porque es la que esta cargando)
// lo que haya de camera o start-camera y luego les asigna una variable en el archivo .js que es camera o startCameraButton
const camera = document.getElementById('camera');
const liveOverlayCanvas = document.getElementById('live-overlay-canvas');
const startCameraButton = document.getElementById('start-camera');
const takePictureButton = document.getElementById('take-picture');
const stopCameraButton = document.getElementById('stop-camera');
const uploadImageButton = document.getElementById('upload-image');
const cameraMessage = document.getElementById('camera-message');
const cameraError = document.getElementById('camera-error');
const canvas = document.getElementById('photo-canvas');
const imageInput = document.getElementById('image');
const previewCanvas = document.getElementById('photo-preview-canvas');
const previewSection = document.getElementById('photo-preview-section');
const submitPhotoButton = document.getElementById('submit-photo');
const closePhotoPreviewButton = document.getElementById('close-photo-preview');
const photoForm = document.getElementById('photo-form');
const selectedOverlaysInput = document.getElementById('selected-overlays');
const overlayOptions = document.querySelectorAll('.overlay-option');

const MAX_RANDOM_OVERLAYS = 4;
const RANDOM_OVERLAY_IDS = [
	'01', '02', '03', '04', '05',
	'06', '07', '08', '09', '10',
	'11', '12', '13', '14', '15',
	'16', '17', '18', '19', '20'
];

const FULL_IMAGE_OVERLAY_IDS = [
	'21', '22', '23',
	'101', '102', '103', '104', '105'
];

const RANDOM_OVERLAY_POSITIONS = [
	'top-left',
	'bottom-left',
	'top-right',
	'bottom-right'
];

// variables que podemos editar porque no son const
let cameraStream = null;
let selectedOverlays = [];
let selectedImage = null;
let livePreviewAnimationFrame = null;
let previousFocusedElement = null;

function showError(message)
{
	cameraError.textContent = message;
	cameraError.hidden = false;
}

function clearError()
{
	cameraError.textContent = '';
	cameraError.hidden = true;
}

function clearLiveOverlay()
{
	const context = liveOverlayCanvas.getContext('2d');

	if (context === null)
		return;

	context.clearRect(0, 0, liveOverlayCanvas.width, liveOverlayCanvas.height);
}

function getProcessedCameraDimensions()
{
	const originalWidth = camera.videoWidth;
	const originalHeight = camera.videoHeight;

	const scale = Math.min(1920 / originalWidth, 1440 / originalHeight, 1);

	return {
		width: Math.round(originalWidth * scale),
		height: Math.round(originalHeight * scale)
	};
}

function getRandomOverlayLayout(width, height)
{
	const maxOverlaySize = 250;
	const margin = Math.min(10, Math.floor(width / 2), Math.floor(height / 2));

	//Dividimos la imagen en cuatro zonas para garantizar que cuatro stickers nunca se superpongan.
	const maxSizeForFour = Math.floor(Math.min((width - (margin * 2)) / 2, (height - (margin * 2)) / 2));
	const size = Math.max(0, Math.min(maxOverlaySize, width, height, maxSizeForFour));

	return {
		size: size,
		margin: margin
	};
}

function getRandomOverlayPosition(position, width, height)
{
	const layout = getRandomOverlayLayout(width, height);

	const size = layout.size;
	const margin = layout.margin;

	if (position === 'top-left')
	{
		return {
			x: margin,
			y: margin
		};
	}

	if (position === 'bottom-left')
	{
		return {
			x: margin,
			y: height - size - margin
		};
	}

	if (position === 'top-right')
	{
		return {
			x: width - size - margin,
			y: margin
		};
	}

	return {
		x: width - size - margin,
		y: height - size - margin
	};
}

function getOverlayNumber(overlay)
{
	return parseInt(overlay, 10);
}

function isRandomOverlay(overlay)
{
	return RANDOM_OVERLAY_IDS.includes(overlay);
}

function isFullImageOverlay(overlay)
{
	return FULL_IMAGE_OVERLAY_IDS.includes(overlay);
}

function getSelectedRandomOverlayCount()
{
	return selectedOverlays.filter(function (item)
	{
		return isRandomOverlay(item.id);
	}).length;
}

function hasFullImageOverlay()
{
	return selectedOverlays.some(function (item)
	{
		return isFullImageOverlay(item.id);
	});
}

function updateSelectedOverlaysInput()
{
	selectedOverlaysInput.value = JSON.stringify(
		selectedOverlays
	);
}

function updateOverlayAvailability()
{
	const randomCount = getSelectedRandomOverlayCount();
	const fullImageSelected = hasFullImageOverlay();

	overlayOptions.forEach(function (button)
	{
		const overlay = button.dataset.overlay;

		const isSelected = selectedOverlays.some(
			function (item)
			{
				return item.id === overlay;
			}
		);

		let unavailable = false;

		if (!isSelected)
		{
			if (isRandomOverlay(overlay) && randomCount >= MAX_RANDOM_OVERLAYS)
				unavailable = true;

			if (isFullImageOverlay(overlay) && fullImageSelected)
				unavailable = true;
		}

		button.classList.toggle(
			'selected',
			isSelected
		);

		button.classList.toggle(
			'unavailable',
			unavailable
		);

		button.setAttribute(
			'aria-disabled',
			unavailable ? 'true' : 'false'
		);

		button.setAttribute(
			'aria-pressed',
			isSelected ? 'true' : 'false'
		);
	});
}

function updateCameraButtons()
{
	const cameraActive = cameraStream !== null;

	startCameraButton.disabled = cameraActive;

	stopCameraButton.disabled = !cameraActive;

	takePictureButton.disabled = !cameraActive;

	submitPhotoButton.disabled = selectedImage === null;
}

function removeSelectedOverlay(overlay)
{
	selectedOverlays = selectedOverlays.filter(
		function (item)
		{
			return item.id !== overlay;
		}
	);

	updateSelectedOverlaysInput();
	updateOverlayAvailability();
	updatePreview();
	updateLiveOverlay();
}

function selectOverlay(button)
{
	const overlay = button.dataset.overlay;
	const selected = selectedOverlays.find(
		function (item)
		{
			return item.id === overlay;
		}
	);

	if (selected !== undefined)
	{
		removeSelectedOverlay(overlay);
		clearError();
		return;
	}

	const randomCount = getSelectedRandomOverlayCount();

	if (isRandomOverlay(overlay) && randomCount >= MAX_RANDOM_OVERLAYS)
		return;

	if (isFullImageOverlay(overlay) && hasFullImageOverlay())
		return;

	let position = null;

	if (isRandomOverlay(overlay))
	{
		const usedPositions = selectedOverlays
			.filter(function (item)
			{
				return isRandomOverlay(item.id);
			})
			.map(function (item)
			{
				return item.position;
			});

		const availablePositions = RANDOM_OVERLAY_POSITIONS.filter(
			function (candidate)
			{
				return !usedPositions.includes(candidate);
			}
		);

		position = availablePositions[
			Math.floor(
				Math.random() * availablePositions.length
			)
		];
	}

	selectedOverlays.push({
		id: overlay,
		position: position
	});

	updateSelectedOverlaysInput();
	updateOverlayAvailability();

	clearError();

	updateLiveOverlay();
	updatePreview();
}

function drawOverlays(context, overlays, width, height, scale = 1, offsetX = 0, offsetY = 0)
{
	const layout = getRandomOverlayLayout(width, height);

	overlays.forEach(function (item)
	{
		const overlayImage = item.image;
		if (overlayImage === null || !overlayImage.complete)
			return;

		if (isRandomOverlay(item.id))
		{
			const position = getRandomOverlayPosition(item.position, width, height);
			context.drawImage(overlayImage, offsetX + position.x * scale, offsetY + position.y * scale, layout.size * scale, layout.size * scale);
		}
		else
		{
			context.drawImage(overlayImage, offsetX, offsetY, width * scale, height * scale);
		}
	});
}

function loadOverlayImages(overlays, callback)
{
	if (overlays.length === 0)
	{
		callback();
		return;
	}

	let remaining = overlays.length;
	let failed = false;

	overlays.forEach(function (item)
	{
		const image = new Image();
		image.onload = function ()
		{
			item.image = image;
			remaining--;
			if (remaining === 0 && !failed)
				callback();
		};

		image.onerror = function ()
		{
			failed = true;
			showError('Unable to load the selected overlay.');
		};
		image.src = '/assets/overlays/' + getOverlayFilename(item.id);
	});
}

function updateLiveOverlay()
{
	if (cameraStream === null || camera.videoWidth === 0 || camera.videoHeight === 0 || selectedOverlays.length === 0)
	{
		clearLiveOverlay();
		if (cameraStream !== null)
			livePreviewAnimationFrame = requestAnimationFrame(updateLiveOverlay);

		return;
	}

	const containerWidth = liveOverlayCanvas.clientWidth;
	const containerHeight = liveOverlayCanvas.clientHeight;
	if (containerWidth === 0 || containerHeight === 0)
	{
		livePreviewAnimationFrame = requestAnimationFrame(updateLiveOverlay);
		return;
	}

	liveOverlayCanvas.width = containerWidth;
	liveOverlayCanvas.height = containerHeight;
	const context = liveOverlayCanvas.getContext('2d');

	if (context === null)
	{
		livePreviewAnimationFrame = requestAnimationFrame(updateLiveOverlay);
		return;
	}

	context.clearRect(0, 0, containerWidth, containerHeight);
	const cameraDimensions = getProcessedCameraDimensions();
	const videoWidth = cameraDimensions.width;
	const videoHeight = cameraDimensions.height;
	const scale = Math.max(containerWidth / videoWidth, containerHeight / videoHeight);
	const displayedWidth = videoWidth * scale;
	const displayedHeight = videoHeight * scale;
	const offsetX = (containerWidth - displayedWidth) / 2;
	const offsetY = (containerHeight - displayedHeight) / 2;
	const overlaysToDraw = [];

	selectedOverlays.forEach(function (item)
	{
		const overlayImage = new Image();
		overlayImage.src = '/assets/overlays/' + getOverlayFilename(item.id);
		overlaysToDraw.push({
			id: item.id,
			position: item.position,
			image: overlayImage
		});
	});

	drawOverlays(context, overlaysToDraw, videoWidth, videoHeight, scale, offsetX, offsetY);

	livePreviewAnimationFrame = requestAnimationFrame(updateLiveOverlay);
}

function startLiveOverlayPreview()
{
	if (livePreviewAnimationFrame !== null)
		return;

	livePreviewAnimationFrame = requestAnimationFrame(updateLiveOverlay);
}

function stopLiveOverlayPreview()
{
	if (livePreviewAnimationFrame !== null)
	{
		cancelAnimationFrame(livePreviewAnimationFrame);
		livePreviewAnimationFrame = null;
	}

	clearLiveOverlay();
}

function openPhotoPreview()
{
	previousFocusedElement = document.activeElement;

	previewSection.hidden = false;
	previewSection.setAttribute('aria-hidden', 'false');

	updateCameraButtons();
}

function closePhotoPreview()
{
	if (previousFocusedElement instanceof HTMLElement)
		previousFocusedElement.focus();

	previewSection.hidden = true;
	previewSection.setAttribute('aria-hidden', 'true');

	selectedImage = null;
	imageInput.value = '';

	clearError();

	const context = previewCanvas.getContext('2d');
	if (context !== null)
		context.clearRect(0, 0, previewCanvas.width, previewCanvas.height);

	updateCameraButtons();
}

function updatePreview()
{
	if (selectedImage === null)
	{
		previewSection.hidden = true;
		updateCameraButtons();
		return;
	}

	const imageUrl = URL.createObjectURL(selectedImage);
	const image = new Image();

	image.onload = function ()
	{
		URL.revokeObjectURL(imageUrl);

		const originalWidth = image.naturalWidth;
		const originalHeight = image.naturalHeight;
		const scale = Math.min(1920 / originalWidth, 1440 / originalHeight, 1);
		const width = Math.round(originalWidth * scale);
		const height = Math.round(originalHeight * scale);

		previewCanvas.width = width;
		previewCanvas.height = height;

		const context = previewCanvas.getContext('2d');
		if (context === null)
		{
			showError('Unable to create the preview.');
			return;
		}

		context.clearRect(0, 0, width, height);
		context.drawImage(image, 0, 0, width, height);
		const overlaysToDraw = [];

		selectedOverlays.forEach(function (item)
		{
			const overlayImage = new Image();

			overlayImage.onload = function ()
			{
				drawOverlays(
					context,
					[{
						id: item.id,
						position: item.position,
						image: overlayImage
					}],
					width,
					height
				);
			};

			overlayImage.src = '/assets/overlays/' + getOverlayFilename(item.id);
		});
		openPhotoPreview();
	};

	image.onerror = function ()
	{
		URL.revokeObjectURL(imageUrl);
		showError('Unable to create the preview.');
	};

	image.src = imageUrl;
	updateCameraButtons();
}

function getOverlayFilename(overlay)
{
	const overlays = {
		'01': '01_laptop_programacion.png',
		'02': '02_42_madrid.png',
		'03': '03_devs_no_duermen.png',
		'04': '04_gaming.png',
		'05': '05_viajes_montana.png',
		'06': '06_cafe_programador.png',
		'07': '07_minecraft_pixel.png',
		'08': '08_linux_forever.png',
		'09': '09_ramen.png',
		'10': '10_tu_puedes.png',
		'11': '11_tiburon_good_vibes.png',
		'12': '12_astroespacio.png',
		'13': '13_terminal_keep_going.png',
		'14': '14_good_boy_42.png',
		'15': '15_disciplina_montana.png',
		'16': '16_coder_sonoliento.png',
		'17': '17_banana_lets_go.png',
		'18': '18_42_cursor.png',
		'19': '19_pizza.png',
		'20': '20_cactus.png',
		'21': '21_dog.png',
		'22': '22_gafas_bigote.png',
		'23': '23_ojos.png',
		'101': '101_playa_tropical.png',
		'102': '102_romantico_kawaii.png',
		'103': '103_cine_film.png',
		'104': '104_aventura_montana.png',
		'105': '105_halloween.png'
	};

	return overlays[overlay];
}

async function startCamera()
{
	clearError();

	if (cameraStream !== null)
		return;

	if (!navigator.mediaDevices || !navigator.mediaDevices.getUserMedia)
	{
		showError('Your browser does not support camera access.');
		return;
	}

	try
	{
		cameraStream = await navigator.mediaDevices.getUserMedia({
				video: true,
				audio: false
			});

		camera.srcObject = cameraStream;

		cameraMessage.classList.add('is-hidden');
		startLiveOverlayPreview();

		updateCameraButtons();
	}
	catch (error)
	{
		cameraStream = null;
		camera.srcObject = null;

		cameraMessage.classList.remove('is-hidden');

		showError('Unable to access the camera. Please allow camera access.');
		updateCameraButtons();
	}
}

function stopCamera()
{
	clearError();

	if (cameraStream === null)
		return;

	cameraStream.getTracks().forEach(
		function (track)
		{
			track.stop();
		}
	);

	camera.srcObject = null;
	cameraStream = null;
	cameraMessage.classList.remove('is-hidden');

	stopLiveOverlayPreview();

	updateCameraButtons();
}

function takePicture()
{
	if (cameraStream === null)
		return;

	const width = camera.videoWidth;
	const height = camera.videoHeight;

	if (width === 0 || height === 0)
	{
		showError('The camera is not ready yet.');
		return;
	}

	clearError();

	canvas.width = width;
	canvas.height = height;
	const context = canvas.getContext('2d');
	if (context === null)
	{
		showError('Unable to create the photo.');
		return;
	}

	context.drawImage(camera, 0, 0, width, height);

	canvas.toBlob(
		function (blob)
		{
			if (blob === null)
			{
				showError('Unable to create the photo.');
				return;
			}

			const file = new File(
				[blob],
				'camagru-photo.jpg',
				{
					type: 'image/jpeg'
				}
			);

			const dataTransfer = new DataTransfer();
			dataTransfer.items.add(file);
			imageInput.files = dataTransfer.files;
			selectedImage = file;
			updatePreview();
		},
		'image/jpeg',
		0.9
	);
}

function uploadImage()
{
	clearError();
	imageInput.click();
}

imageInput.addEventListener(
	'change',
	function ()
	{
		if (imageInput.files.length === 0)
			return;

		const file = imageInput.files[0];
		if (file.type !== 'image/jpeg' && file.type !== 'image/png')
		{
			showError('Please select a JPEG or PNG image.');
			imageInput.value = '';
			selectedImage = null;
			updatePreview();
			return;
		}

		clearError();
		selectedImage = file;
		updatePreview();
	}
);

submitPhotoButton.addEventListener(
	'click',
	function ()
	{
		clearError();
		if (selectedImage === null)
		{
			showError('Please take or upload an image.');
			return;
		}

		if (imageInput.files.length === 0)
		{
			showError('Please select an image.');
			return;
		}
		photoForm.submit();
	}
);

startCameraButton.addEventListener(
	'click',
	startCamera
);

takePictureButton.addEventListener(
	'click',
	takePicture
);

stopCameraButton.addEventListener(
	'click',
	stopCamera
);

uploadImageButton.addEventListener(
	'click',
	uploadImage
);

closePhotoPreviewButton.addEventListener(
	'click',
	closePhotoPreview
);

overlayOptions.forEach(function (button)
{
	button.addEventListener(
		'click',
		function ()
		{
			selectOverlay(button);
		}
	);
});

updateSelectedOverlaysInput();
updateOverlayAvailability();
updateCameraButtons();

window.addEventListener(
	'beforeunload',
	function ()
	{
		stopLiveOverlayPreview();
		if (cameraStream !== null)
		{
			cameraStream.getTracks().forEach(
				function (track)
				{
					track.stop();
				}
			);
		}
	}
);