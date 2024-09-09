const canvas = document.querySelector("canvas");
const video = document.querySelector("#video");
const startButton = document.querySelector("#startButton");
const retakeButton = document.querySelector("#retakeButton");
const saveButton = document.querySelector("#saveButton");
const defaultImages = document.querySelectorAll(".defaultImgs");
const sizeSlider = document.querySelector("#size");
const rotationSlider = document.querySelector("#rotation");
const sliderContainer = document.querySelectorAll(".sliderContainer");
const postMsg = document.querySelector("#postMsg");
const imgFileInput = document.querySelector("#imgFileInput");
const imgFile = document.querySelector("#imgFile");
const ctx = canvas.getContext("2d");

let imgWidth = 1000;
let imgHeight = 0; // Will be computed based on the aspect ratio of the video
let reader;
let postMsgValue = "";
let streaming = false;
let videoStream = null;

class BackgroundImage {
	constructor(width, height) {
		this.position = { x: 0, y: 0 };
		this.size = { width: width, height: height };
		this.src = null;
	}
}
let backgroundImage;

class SelectedImg {
	constructor() {
		this.position = { x: 0, y: 0 };
		this.size = { width: 100, height: 100 };
		this.rotation = 1;
		this.src = null;
		this.previousSelectedImage = null;
	}
}
let selectedImg;

/* Editing images logic */
const enableSaveButton = () => {
	if (postMsgValue.trim().length > 0 && selectedImg.src != null) {
		saveButton.disabled = false;
		saveButton.style.cursor = "pointer";
	} else {
		saveButton.disabled = true;
		saveButton.style.cursor = "not-allowed";
	}
};

const editImages = () => {
	defaultImages.forEach((img) => {
		img.addEventListener("click", () => {
			selectedImg.src = img.src;
			img.style.background = "#b57410";
			if (
				selectedImg.previousSelectedImage != null &&
				selectedImg.previousSelectedImage.src != selectedImg.src
			)
				selectedImg.previousSelectedImage.style.background = "#1051B5";
			selectedImg.previousSelectedImage = img;
		});
	});
};

/* Drawing on the canvas */
const drawBackground = () => {
	ctx.clearRect(
		0,
		0,
		backgroundImage.size.width,
		backgroundImage.size.height
	);

	if (backgroundImage.src !== null) {
		try {
			ctx.drawImage(
				backgroundImage.src,
				0,
				0,
				backgroundImage.size.width,
				backgroundImage.size.height
			);
		} catch (error) {
			// an error occurred when drawing file src to canvas. Probably not a valid image file
			resetPicture();
		}
	} else {
		ctx.drawImage(
			video,
			0,
			0,
			backgroundImage.size.width,
			backgroundImage.size.height
		);
	}
	ctx.stroke();
};

const drawSelectedImage = () => {
	if (selectedImg.src === null) return;

	const img = new Image();
	img.src = selectedImg.src;
	img.onload = () => {
		ctx.save();
		ctx.translate(selectedImg.position.x, selectedImg.position.y);
		ctx.rotate((selectedImg.rotation * Math.PI) / 180); // Rotate the image

		// Draw the image, adjusting the position to account for the translation
		ctx.drawImage(
			img,
			-(selectedImg.size.width / 2),
			-(selectedImg.size.height / 2),
			selectedImg.size.width,
			selectedImg.size.height
		);

		ctx.restore(); // Restore the canvas state
	};
	enableSaveButton();
};

const drawEntireScene = () => {
	drawBackground();
	drawSelectedImage();
};

/* Camera and taking picture logic */
const initialSetup = () => {
	// Get the camera stream
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

	// Set the video dimensions
	video.addEventListener(
		"canplay",
		(ev) => {
			if (!streaming) {
				imgHeight = video.videoHeight / (video.videoWidth / imgWidth);
				if (isNaN(imgHeight)) {
					imgHeight = imgWidth / (4 / 3);
				}
				backgroundImage = new BackgroundImage(imgWidth, imgHeight);

				video.setAttribute("width", imgWidth);
				video.setAttribute("height", imgHeight);
				canvas.setAttribute("width", imgWidth);
				canvas.setAttribute("height", imgHeight);
				streaming = true;
			}
		},
		false
	);

	selectedImg = new SelectedImg();
	reader = new FileReader();

	ctx.clearRect(0, 0, canvas.width, canvas.height);
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

const takePicture = (file = false) => {
	canvas.width = imgWidth;
	canvas.height = imgHeight;
	const validImageTypes = ["image/png", "image/jpeg", "image/jpg"];

	if (file != false) {
		if (!validImageTypes.includes(file.type)) {
			return;
		}
		reader.readAsDataURL(file);
		reader.onload = function (e) {
			backgroundImage.src = new Image();
			backgroundImage.src.src = e.target.result;
		};
	} else backgroundImage.src = getFrame();
	drawBackground();

	// Stop the camera stream
	if (videoStream) {
		videoStream.getTracks().forEach((track) => track.stop());
	}

	video.style.display = "none";
	canvas.style.display = "block";
	document.querySelector(".imgContainer").style.display = "flex";
	document.querySelector(".postInfo").style.display = "flex";
	startButton.style.display = "none";
	retakeButton.style.display = "block";
	imgFile.style.display = "none";
	sliderContainer.forEach((slider) => {
		slider.style.display = "flex";
	});
	flashEffect();
	editImages();
};

const resetPicture = () => {
	if (selectedImg.previousSelectedImage != null)
		selectedImg.previousSelectedImage.style.background = "#1051B5";
	initialSetup(); // Reinitialize the camera when resetting the picture

	video.style.display = "block";
	canvas.style.display = "none";
	document.querySelector(".imgContainer").style.display = "none";
	document.querySelector(".postInfo").style.display = "none";
	startButton.style.display = "block";
	retakeButton.style.display = "none";
	imgFile.style.display = "flex";

	rotationSlider.value = 1;
	sizeSlider.value = 100;

	saveButton.disabled = true;
	saveButton.style.cursor = "not-allowed";
	sliderContainer.forEach((slider) => {
		slider.style.display = "none";
	});
	backgroundImage = new BackgroundImage(imgWidth, imgHeight);
	streaming = false;
};

const savePost = () => {
	const formData = new FormData();

	try {
		// Convert the images to base64 strings and append them to the FormData
		if (backgroundImage.src && backgroundImage.src.src) {
			formData.append("backgroundImage", backgroundImage.src.src);
		} else {
			throw new Error("Invalid background image source");
		}

		if (selectedImg.src) {
			formData.append("selectedImg", selectedImg.src);
		} else {
			throw new Error("Invalid selected image source");
		}

		// Append the post message
		formData.append("postMsg", postMsg.value);

		// Log FormData entries
		for (let pair of formData.entries()) {
			console.log(pair[0] + ": " + pair[1]);
		}

		fetch("php/generatePostImage.php", {
			method: "POST",
			body: formData,
		})
			.then((response) => response.text()) // Get the response as text
			.then((text) => {
				console.log("Response text:", text); // Log the response text
				const data = JSON.parse(text); // Parse the JSON
				console.log("Success:", data);
			})
			.catch((error) => {
				console.error("Error:", error);
			});
	} catch (error) {
		console.error("Error preparing images for upload:", error);
	}
};

/* Helper functions */

const debounce = (func, delay) => {
	let timeout;
	return function (...args) {
		const context = this;
		clearTimeout(timeout);
		timeout = setTimeout(() => func.apply(context, args), delay);
	};
};

const debouncedDraw = debounce(() => {
	drawEntireScene();
}, 100);

/* Listeners and initial function */
startButton.addEventListener(
	"click",
	(ev) => {
		if (!streaming) return;
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

sizeSlider.addEventListener("input", (ev) => {
	selectedImg.size.width = ev.target.value;
	selectedImg.size.height = ev.target.value;
	debouncedDraw();
	ev.preventDefault();
});

rotationSlider.addEventListener("input", (ev) => {
	selectedImg.rotation = ev.target.value;
	debouncedDraw();
	ev.preventDefault();
});

canvas.addEventListener("click", () => {
	const rect = canvas.getBoundingClientRect();
	selectedImg.position.x = event.clientX - rect.left;
	selectedImg.position.y = event.clientY - rect.top;
	drawEntireScene();
});

postMsg.addEventListener("input", (ev) => {
	postMsgValue = ev.target.value;
	enableSaveButton();
	ev.preventDefault();
});

imgFile.addEventListener("click", () => {
	imgFileInput.click();
});

imgFileInput.addEventListener("change", () => {
	const fileInput = event.target.files[0];
	takePicture(fileInput);
});

saveButton.addEventListener("click", savePost);

initialSetup();
