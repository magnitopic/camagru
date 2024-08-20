const canvas = document.querySelector("canvas");
const video = document.querySelector("#video");
const startButton = document.querySelector("#startButton");
const retakeButton = document.querySelector("#retakeButton");
const saveButton = document.querySelector("#saveButton");
const defaultImages = document.querySelectorAll(".defaultImgs");
const ctx = canvas.getContext("2d");

const width = 1000;
let height = 0;
let streaming = false;
let userImage = null;
let selectedImg = null;
let previousSelectedImage = null;
let videoStream = null; // Store the video stream

/* Editing images logic */
const enableSaveButton = () => {
	saveButton.disabled = false;
	saveButton.style.background = "lightgreen";
};

const editImages = () => {
	defaultImages.forEach((img) => {
		img.addEventListener("click", () => {
			selectedImg = img.src;
			img.style.background = "lightyellow";
			if (previousSelectedImage != null)
				previousSelectedImage.style.background = "rgb(235, 172, 132)";
			previousSelectedImage = img;
		});
	});
};

/* Drawing on the canvas */
const drawBackground = () => {
	ctx.clearRect(0, 0, canvas.width, canvas.height);

	if (userImage !== null) {
		ctx.drawImage(userImage, 0, 0, canvas.width, canvas.height);
	} else {
		ctx.drawImage(video, 0, 0, canvas.width, canvas.height);
	}
	ctx.stroke();
};

const drawSelectedImage = () => {
	const rect = canvas.getBoundingClientRect();
	const height = 100;
	const width = 100;
	const x = event.clientX - rect.left;
	const y = event.clientY - rect.top;

	if (selectedImg === null) return;

	const img = new Image();
	img.src = selectedImg;
	img.onload = () =>
		ctx.drawImage(img, x - height / 2, y - width / 2, height, width);
	enableSaveButton();
};

const drawEntireScene = () => {
	drawBackground();
	drawSelectedImage();
};

/* Camera and taking picture logic */
const initialSetup = () => {
	navigator.mediaDevices
		.getUserMedia({ video: true, audio: false })
		.then((stream) => {
			videoStream = stream; // Store the stream in a variable
			video.srcObject = stream;
			video.play();
		})
		.catch((err) => {
			console.error(`An error occurred: ${err}`);
		});

	video.addEventListener(
		"canplay",
		(ev) => {
			if (!streaming) {
				height = video.videoHeight / (video.videoWidth / width);

				if (isNaN(height)) {
					height = width / (4 / 3);
				}

				video.setAttribute("width", width);
				video.setAttribute("height", height);
				canvas.setAttribute("width", width);
				canvas.setAttribute("height", height);
				streaming = true;
			}
		},
		false
	);

	resetPhoto();
};

const resetPhoto = () => {
	ctx.clearRect(0, 0, canvas.width, canvas.height);
	userImage = null;
};

const flashEffect = () => {
	let opacity = 0;
	let direction = 1; // 1 for fade-in, -1 for fade-out

	function animateFlash() {
		drawBackground();

		ctx.fillStyle = `rgba(255, 255, 255, ${opacity})`;
		ctx.fillRect(0, 0, canvas.width, canvas.height);

		opacity += 0.05 * direction;

		if (opacity >= 1) {
			direction = -1;
		} else if (opacity <= 0) {
			return;
		}

		requestAnimationFrame(animateFlash);
	}

	animateFlash();
};

const getFrame = () => {
	const offscreenCanvas = document.createElement("canvas");
	const offscreenCtx = offscreenCanvas.getContext("2d");
	offscreenCanvas.width = video.videoWidth;
	offscreenCanvas.height = video.videoHeight;

	offscreenCtx.drawImage(
		video,
		0,
		0,
		offscreenCanvas.width,
		offscreenCanvas.height
	);

	const image = new Image();
	image.src = offscreenCanvas.toDataURL();

	return image;
};

const takePicture = () => {
	canvas.width = width;
	canvas.height = height;

	userImage = getFrame();
	drawBackground();

	// Stop the camera stream
	if (videoStream) {
		videoStream.getTracks().forEach((track) => track.stop());
	}

	video.style.display = "none";
	canvas.style.display = "block";
	document.querySelector(".imgContainer").style.display = "flex";
	startButton.style.display = "none";
	retakeButton.style.display = "block";
	saveButton.style.display = "block";

	flashEffect();
	editImages();
};

const resetPicture = () => {
	initialSetup(); // Reinitialize the camera when resetting the picture

	video.style.display = "block";
	canvas.style.display = "none";
	document.querySelector(".imgContainer").style.display = "none";
	startButton.style.display = "block";
	retakeButton.style.display = "none";
	saveButton.style.display = "none";
	saveButton.disabled = true;
	selectedImg = null;
	if (previousSelectedImage != null)
		previousSelectedImage.style.background = "rgb(235, 172, 132)";
	previousSelectedImage = null;
	resetPhoto();
};

/* Listeners and initial function */
startButton.addEventListener(
	"click",
	(ev) => {
		takePicture();
		ev.preventDefault();
	},
	false
);

retakeButton.addEventListener(
	"click",
	(ev) => {
		resetPicture();
		ev.preventDefault();
	},
	false
);

canvas.addEventListener("click", drawEntireScene);

initialSetup();
