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

/* Editing images logic */

const enableSaveButton = () => {
    saveButton.disabled = false;
    saveButton.style.background = "lightgreen";
}

const editImages = () => {
    defaultImages.forEach((img) => {
        img.addEventListener("click", () => {
            selectedImg = img.src;
            img.style.background = "lightyellow";
            if (previousSelectedImage != null)
                previousSelectedImage.style.background = "rgb(235, 172, 132)";
            previousSelectedImage = img
        })
    })
}

/* Drawing on the canvas */

const drawBackground = () => {
    ctx.clearRect(0, 0, canvas.width, canvas.height);
    ctx.drawImage(video, 0, 0, canvas.width, canvas.height);
    ctx.stroke();
    if (userImage != null)
        userImage.onload = () => ctx.drawImage(userImage, 0, 0, canvas.width, canvas.height);
}

const drawSelectedImage = () => {
    // Get the bounding box of the canvas element
    const rect = canvas.getBoundingClientRect();

    const height = 100;
    const width = 100;

    // Calculate the x and y coordinates relative to the canvas
    const x = event.clientX - rect.left;
    const y = event.clientY - rect.top;
    if (selectedImg === null)
        return;

    const img = new Image();
    img.src = selectedImg;
    img.onload = () => ctx.drawImage(img, x - (height / 2), y - (width / 2), height, width);
    enableSaveButton();
}

const drawEntireScene = () => {
    drawBackground();
    drawSelectedImage();
}

/* Camera and taking picture logic */

const initialSetup = () => {
    navigator.mediaDevices
        .getUserMedia({ video: true, audio: false })
        .then((stream) => {
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

                // Firefox currently has a bug where the height can't be read from
                // the video, so we will make assumptions if this happens.

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
        false,
    );
    resetPhoto();
}

const resetPhoto = () => {
    ctx.clearRect(0, 0, canvas.width, canvas.height);
    userImage = null;
}

const flashEffect = () => {
    let opacity = 0;
    let direction = 1; // 1 for fade-in, -1 for fade-out

    function animateFlash() {
        drawBackground();

        // Set the white rectangle's opacity
        ctx.fillStyle = `rgba(255, 255, 255, ${opacity})`;
        ctx.fillRect(0, 0, canvas.width, canvas.height);

        // Update opacity
        opacity += 0.05 * direction;

        // Reverse direction when fully opaque or fully transparent
        if (opacity >= 1) {
            direction = -1;
        } else if (opacity <= 0) {
            // Stop the animation once the flash is done
            return;
        }

        requestAnimationFrame(animateFlash);
    }

    animateFlash();
    editImages();
}

const getFrame = () => {
    const offscreenCanvas = document.createElement('canvas');
    const offscreenCtx = offscreenCanvas.getContext('2d');
    offscreenCanvas.width = video.videoWidth;
    offscreenCanvas.height = video.videoHeight;

    // Draw the current frame from the video onto the offscreen canvas
    offscreenCtx.drawImage(video, 0, 0, offscreenCanvas.width, offscreenCanvas.height);

    // Convert the offscreen canvas to an Image object
    const image = new Image();
    image.src = offscreenCanvas.toDataURL(); // Converts the canvas content to a data URL (base64 string)

    return image;
}

const takePicture = () => {
    canvas.width = width;
    canvas.height = height;
    ctx.fillStyle = "black";
    drawBackground();
    userImage = getFrame();

    video.style.display = "none";
    canvas.style.display = "block";
    document.querySelector(".imgContainer").style.display = "flex";
    startButton.style.display = "none";
    retakeButton.style.display = "block";
    saveButton.style.display = "block";

    flashEffect();
}

const resetPicture = () => {
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
}

/* Listeners and initial function */
startButton.addEventListener(
    "click",
    (ev) => {
        takePicture();
        ev.preventDefault();
    }, false
);

retakeButton.addEventListener(
    "click",
    (ev) => {
        resetPicture();
        ev.preventDefault();
    }, false
)

canvas.addEventListener('click', drawEntireScene);

initialSetup();
