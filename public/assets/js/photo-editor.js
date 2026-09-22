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
	takePictureButton.disabled =
		cameraStream === null ||
		selectedOverlay === null;
}

async function startCamera()
{
	clearError();

	if (!navigator.mediaDevices ||
		!navigator.mediaDevices.getUserMedia)
	{
		showError(
			'Your browser does not support camera access.'
		);

		return;
	}

	try
	{
		cameraStream = await navigator.mediaDevices.getUserMedia({
			video: true,
			audio: false
		});

		camera.srcObject = cameraStream;

		cameraMessage.hidden = true;
		startCameraButton.disabled = true;

		updateTakePictureButton();
	}
	catch (error)
	{
		showError(
			'Unable to access the camera. Please allow camera access.'
		);
	}
}

function selectOverlay(button)
{
	overlayOptions.forEach(function (option)
	{
		option.classList.remove('selected');
	});

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
		showError(
			'The camera is not ready yet.'
		);

		return;
	}

	clearError();

	canvas.width = width;
	canvas.height = height;

	const context = canvas.getContext('2d');

	if (context === null)
	{
		showError(
			'Unable to create the photo.'
		);

		return;
	}

	context.drawImage(
		camera,
		0,
		0,
		width,
		height
	);

	canvas.toBlob(
		function (blob)
		{
			if (blob === null)
			{
				showError(
					'Unable to create the photo.'
				);

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

			photoForm.submit();
		},
		'image/jpeg',
		0.9
	);
}

function uploadImage()
{
	clearError();

	if (selectedOverlay === null)
	{
		showError(
			'Please choose an overlay first.'
		);

		return;
	}

	imageInput.click();
}

imageInput.addEventListener(
	'change',
	function ()
	{
		if (imageInput.files.length === 0)
			return;

		const file = imageInput.files[0];

		if (file.type !== 'image/jpeg' &&
			file.type !== 'image/png')
		{
			showError(
				'Please select a JPEG or PNG image.'
			);

			imageInput.value = '';

			return;
		}

		clearError();

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

uploadImageButton.addEventListener(
	'click',
	uploadImage
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