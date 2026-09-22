//lo que hace es buscar en los archivos html del proyecto(que en este caso lo hace en el create.php porque es la que esta cargando)
// lo que haya de camera o start-camera y luego les asigna una variable en el archivo .js que es camera o startCameraButton
const camera = document.getElementById('camera');
const startCameraButton = document.getElementById('start-camera');
const takePictureButton = document.getElementById('take-picture');
const uploadImageButton = document.getElementById('upload-image');
const cameraMessage = document.getElementById('camera-message');
const cameraError = document.getElementById('camera-error');
const canvas = document.getElementById('photo-canvas');
const imageInput = document.getElementById('image');
const photoForm = document.getElementById('photo-form');
const selectedOverlayInput = document.getElementById('selected-overlay');
const overlayOptions = document.querySelectorAll('.overlay-option');

// variables que podemos editar porque no son const
let cameraStream = null;
let selectedOverlay = null;

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

function updateTakePictureButton()
{
	takePictureButton.disabled = cameraStream === null || selectedOverlay === null; //El botón "Take Picture" solamente puede utilizarse cuando tengo cámara + overlay.
}

async function startCamera() //función asíncrona: El navegador tiene que: 1. pedir permiso; 2. acceder al hardware; 3. obtener el stream; 4. devolverlo.
{
	clearError();

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

		cameraMessage.hidden = true; //Ocultar el mensaje Start the Camera
		startCameraButton.disabled = true; // desabilitamos el boton

		updateTakePictureButton();
	}
	catch (error)
	{
		showError('Unable to access the camera. Please allow camera access.');
	}
}

function selectOverlay(button) //función se ejecuta cuando seleccionas un overlay
{
	overlayOptions.forEach(function (option) //Primero deselecciona todos(overlayOptions, todos los overlays) | forEach(Ejecuta esta función para cada elemento)
	{
		option.classList.remove('selected');
	});

	/* asigna el overlay que has pulsado*/
	button.classList.add('selected');
	selectedOverlay = button.dataset.overlay;
	selectedOverlayInput.value = selectedOverlay;

	updateTakePictureButton();
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

			photoForm.submit();
		},
		'image/jpeg',
		0.9 //la calidad de la imagen, es hasta 1 asique es muy bunea calidad
	);
}

function uploadImage() //subir una foto existente
{
	clearError();

	if (selectedOverlay === null)
	{
		showError('Please choose an overlay first.');

		return;
	}

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
			return;
		}

		clearError();

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

uploadImageButton.addEventListener(
	'click',
	uploadImage
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

/*si abandona la pagina el usario verifica que la camara no este activada mediante cameraStream y
si esta activada que la desactiva para no ocupar un recursos del sistema*/
window.addEventListener(
	'beforeunload',
	function ()
	{
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