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
const selectedOverlayInput = document.getElementById('selected-overlay');
const overlayXInput = document.getElementById('overlay-x');
const overlayYInput = document.getElementById('overlay-y');
const overlayOptions = document.querySelectorAll('.overlay-option');

// variables que podemos editar porque no son const
let cameraStream = null;
let selectedOverlay = null;
let selectedImage = null;
let selectedOverlayPosition = null;
let liveOverlayImage = null;
let livePreviewAnimationFrame = null;

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

function updateLiveOverlay()
{
	if (cameraStream === null || camera.videoWidth === 0 || camera.videoHeight === 0 || selectedOverlay === null || liveOverlayImage === null || !liveOverlayImage.complete)
	{
		clearLiveOverlay();

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

	const videoWidth = camera.videoWidth;
	const videoHeight = camera.videoHeight;

	const scale = Math.max(containerWidth / videoWidth, containerHeight / videoHeight);

	const displayedWidth = videoWidth * scale;
	const displayedHeight = videoHeight * scale;

	const offsetX = (containerWidth - displayedWidth) / 2;
	const offsetY = (containerHeight - displayedHeight) / 2;

	const overlayNumber = parseInt(selectedOverlay, 10);

	if (overlayNumber >= 1 && overlayNumber <= 20)
	{
		const overlayWidth = 500;
		const overlayHeight = 500;

		if (selectedOverlayPosition === null)
		{
			const margin = 300;
			const positions = [
				{
					x: margin,
					y: margin
				},
				{
					x: margin,
					y: Math.max(0, videoHeight - overlayHeight - margin)
				},
				{
					x: Math.max(0, videoWidth - overlayWidth - margin),
					y: margin
				},
				{
					x: Math.max(0, videoWidth - overlayWidth - margin),
					y: Math.max(0, videoHeight - overlayHeight - margin)
				}
			];

			selectedOverlayPosition = positions[
			Math.floor(
				Math.random() * positions.length
			)
			];

			overlayXInput.value = selectedOverlayPosition.x;
			overlayYInput.value = selectedOverlayPosition.y;
		}

		const x = offsetX + selectedOverlayPosition.x * scale;
		const y = offsetY + selectedOverlayPosition.y * scale;

		const width = overlayWidth * scale;
		const height = overlayHeight * scale;

		context.drawImage(liveOverlayImage, x, y, width, height);
	}
	else
		context.drawImage(liveOverlayImage, offsetX, offsetY, displayedWidth, displayedHeight);

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
	previewSection.hidden = false;
	previewSection.setAttribute('aria-hidden', 'false');

	updateCameraButtons();
}

function closePhotoPreview()
{
	previewSection.hidden = true;
	previewSection.setAttribute('aria-hidden', 'true');

	selectedImage = null;
	imageInput.value = '';

	selectedOverlayPosition = null;

	overlayXInput.value = '';
	overlayYInput.value = '';

	clearLiveOverlay();

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

		//Dibujamos la imagen exactamente con las mismas dimensiones que utilizará ImageService.
		context.drawImage(image, 0, 0, width, height);

		if (selectedOverlay === null)
		{
			openPhotoPreview();
			return;
		}

		const overlayImage = new Image();

		overlayImage.onload = function ()
		{
			const overlayNumber = parseInt(selectedOverlay, 10);

			if (overlayNumber >= 1 && overlayNumber <= 20)
			{
				const overlayWidth = 500;
				const overlayHeight = 500;
				const margin = 300;

				if (selectedOverlayPosition === null)
				{
					const positions = [
						{
							x: margin,
							y: margin
						},
						{
							x: margin,
							y: Math.max(0, height - overlayHeight - margin)
						},
						{
							x: Math.max(0, width - overlayWidth - margin),
							y: margin
						},
						{
							x: Math.max(0, width - overlayWidth - margin),
							y: Math.max(0, height - overlayHeight - margin)
						}
					];

					selectedOverlayPosition = positions[
						Math.floor(
							Math.random() * positions.length
						)
					];
				}

				//Guardamos la posición para enviársela posteriormente a PHP.
				overlayXInput.value = selectedOverlayPosition.x;

				overlayYInput.value = selectedOverlayPosition.y;

				context.drawImage(overlayImage, 0, 0, overlayWidth, overlayHeight, selectedOverlayPosition.x, selectedOverlayPosition.y, overlayWidth, overlayHeight);
			}
			else
			{
				//Los overlays 21-23 y 101-105 ocupan toda la imagen, igual que en PHP.
				overlayXInput.value = '';
				overlayYInput.value = '';

				context.drawImage(overlayImage, 0, 0, width, height);
			}

			openPhotoPreview();
		};

		overlayImage.onerror = function ()
		{
			showError('Unable to load the selected overlay.');
		};

		overlayImage.src = '/assets/overlays/' + getOverlayFilename(selectedOverlay);
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

function updateCameraButtons()
{
	const cameraActive = cameraStream !== null;

	startCameraButton.disabled = cameraActive;

	stopCameraButton.disabled = !cameraActive;

	takePictureButton.disabled = !cameraActive || selectedOverlay === null;

	submitPhotoButton.disabled = selectedImage === null || selectedOverlay === null;//El botón "Take Picture" solamente puede utilizarse cuando tengo cámara + overlay.
}

async function startCamera() //función asíncrona: El navegador tiene que: 1. pedir permiso; 2. acceder al hardware; 3. obtener el stream; 4. devolverlo.
{
	clearError();

	if (cameraStream !== null)
		return;

	if (!navigator.mediaDevices || !navigator.mediaDevices.getUserMedia) //Comprobar si el navegador permite usar la cámara
	{
		showError('Your browser does not support camera access.');
		return;
	}

	try
	{
		cameraStream = await navigator.mediaDevices.getUserMedia({ //await: Espera a que el navegador termine de obtener la cámara
			video: true,
			audio: false
		}); //Quiero acceder a la cámara, pero no al micrófono
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

function selectOverlay(button) //función se ejecuta cuando seleccionas un overlay
{
	const overlay = button.dataset.overlay;

	//Si el overlay pulsado ya era el seleccionado, lo deseleccionamos.
	if (selectedOverlay === overlay)
	{
		button.classList.remove('selected');
		button.setAttribute('aria-pressed', 'false');

		selectedOverlay = null;
		selectedOverlayInput.value = '';

		selectedOverlayPosition = null;

		overlayXInput.value = '';
		overlayYInput.value = '';

		liveOverlayImage = null;

		clearLiveOverlay();

		clearError();
		updatePreview();

		return;
	}

	// Si hemos pulsado un overlay diferente, primero quitamos la selección anterior.
	overlayOptions.forEach(function (option)
	{
		option.classList.remove('selected');
		option.setAttribute('aria-pressed', 'false');
	});

	//Seleccionamos el nuevo overlay.
	button.classList.add('selected');
	button.setAttribute('aria-pressed', 'true');

	selectedOverlay = overlay;
	selectedOverlayInput.value = selectedOverlay;

	selectedOverlayPosition = null;

	overlayXInput.value = '';
	overlayYInput.value = '';

	clearError();

	liveOverlayImage = new Image();

	liveOverlayImage.onload = function ()
	{
		startLiveOverlayPreview();
		updatePreview();
	};

	liveOverlayImage.onerror = function ()
	{
		liveOverlayImage = null;

		clearLiveOverlay();

		showError('Unable to load the selected overlay.');
	};

	liveOverlayImage.src = '/assets/overlays/' + getOverlayFilename(selectedOverlay);
}

function takePicture()
{
	if (cameraStream === null)
		return;

	if (selectedOverlay === null)
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

	const context = canvas.getContext('2d'); //Esto obtiene el objeto que permite dibujar en el canvas.

	if (context === null)
	{
		showError('Unable to create the photo.');
		return;
	}

	context.drawImage(camera, 0, 0, width, height); //copia un frame de la cámara al canvas

	canvas.toBlob(function (blob) //Esto convierte el contenido del canvas en un archivo/Blob JPEG
		{
			if (blob === null)
			{
				showError('Unable to create the photo.');
				return;
			}

			const file = new File([blob], 'camagru-photo.jpg',
				{
					type: 'image/jpeg'
				}
			);
			
			/*esto lo hacemos para engañar al html y marcamos como que hemos selecionado esta foto*/
			const dataTransfer = new DataTransfer();
			dataTransfer.items.add(file);
			imageInput.files = dataTransfer.files;
			selectedImage = file;
			updatePreview();
		},
		'image/jpeg',
		0.9 //la calidad de la imagen, es hasta 1 asique es muy bunea calidad
	);
}

function uploadImage() //subir una foto existente
{
	clearError();

	imageInput.click();
}

imageInput.addEventListener(
	'change', //Cuando cambie el archivo seleccionado, ejecuta esta función
	function ()
	{
		if (imageInput.files.length === 0) //Comprobar si hay archivo
			return;

		const file = imageInput.files[0]; //Obtienes el primer archivo seleccionado

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

		if (selectedOverlay === null)
		{
			showError('Please choose an overlay first.');
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

/*REgistramos los botones, es decir cuando hagamos click
 en el de start camara o el de hacer foto o subir foto activamos las funcoines de arriba*/
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

/*por cada vez que seleciones un overlay llama a la funcion selectOverlat*/
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

updateCameraButtons();

/*si abandona la pagina el usario verifica que la camara no este activada mediante cameraStream y
si esta activada que la desactiva para no ocupar un recursos del sistema*/
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