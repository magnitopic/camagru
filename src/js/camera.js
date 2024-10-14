window.onload = () => {
	const canvas = document.querySelector(".final_image_canvas");
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
	const canvasContainer = document.querySelector(".canvasContainer");
	const imgFile = document.querySelector("#imgFile");
	const ctx = canvas.getContext("2d");

	let imgWidth = canvasContainer.offsetWidth;
	let imgHeight = 0; // Will be computed based on the aspect ratio of the video
	let reader;
	let retryTimeout;
	let postMsgValue = "";
	let streaming = false;
	let videoStream = null;
	let currentSelectedImg = 0;
	let currentSelectedImgIndex = null;
	let previousSelectedImage = null;
	let isWebcamImg = null;

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
			this.size = { width: 10, height: 10 };
			this.aspectRatio = 1;
			this.rotation = 1;
			this.src = null;
		}
	}
	let selectedImgs;

	/* Editing images logic */
	const checkIfPlacedSticker = () => {
		// if the image was uploaded, it's always true
		if (!isWebcamImg) return true;
		for (let i = 0; i < selectedImgs.length; i++) {
			if (selectedImgs[i].src != null) return true;
		}
		return false;
	};

	const enableSaveButton = () => {
		if (postMsgValue.trim().length > 0 && checkIfPlacedSticker()) {
			saveButton.disabled = false;
			saveButton.style.cursor = "pointer";
		} else {
			saveButton.disabled = true;
			saveButton.style.cursor = "not-allowed";
		}
	};

	const editImages = () => {
		defaultImages.forEach((img, index) => {
			img.addEventListener("click", () => {
				if (isWebcamImg) {
					// For webcam images, replace the existing sticker
					selectedImgs = [new SelectedImg()];
					selectedImgs[0].src = img;
					currentSelectedImg = 0;
					currentSelectedImgIndex = index;
				} else {
					// For uploaded images, allow multiple stickers
					let existingIndex = selectedImgs.findIndex(
						(si) => si.src === img
					);
					if (existingIndex !== -1) {
						currentSelectedImg = existingIndex;
					} else {
						let newSticker = new SelectedImg();
						newSticker.src = img;
						selectedImgs.push(newSticker);
						currentSelectedImg = selectedImgs.length - 1;
					}
					currentSelectedImgIndex = index;
				}

				updateStickerDisplay();
				drawEntireScene();
			});
		});
	};

	const updateStickerDisplay = () => {
		defaultImages.forEach((img, index) => {
			if (selectedImgs.some((si) => si.src === img)) {
				if (index === currentSelectedImgIndex) {
					img.style.background = "var(--tertiary)";
				} else {
					img.style.background = "var(--secondary)";
				}
			} else {
				img.style.background = "var(--primary)";
			}
		});

		if (currentSelectedImg !== null && selectedImgs[currentSelectedImg]) {
			sizeSlider.value = selectedImgs[currentSelectedImg].size.width;
			rotationSlider.value = selectedImgs[currentSelectedImg].rotation;
		}
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

	function selectedImgToCanvas(img) {
		const posX = (img.position.x / 100) * backgroundImage.size.width;
		const posY = (img.position.y / 100) * backgroundImage.size.height;
		const width = (img.size.width / 100) * backgroundImage.size.width;
		const height = width / img.aspectRatio; // Use aspect ratio to calculate height

		ctx.save();
		ctx.translate(posX, posY);
		ctx.rotate((img.rotation * Math.PI) / 180);

		ctx.drawImage(img.src, -(width / 2), -(height / 2), width, height);
		ctx.restore();
	}

	const drawSelectedImage = () => {
		selectedImgs.forEach((img) => {
			if (img.src === null) return;
			if (img.src.complete) {
				// Update aspect ratio when the image is loaded
				if (img.aspectRatio === 1) {
					img.aspectRatio =
						img.src.naturalWidth / img.src.naturalHeight;
					img.size.height = img.size.width / img.aspectRatio;
				}
				selectedImgToCanvas(img);
			} else {
				img.src.onload = () => {
					// Set aspect ratio when the image is loaded
					img.aspectRatio =
						img.src.naturalWidth / img.src.naturalHeight;
					img.size.height = img.size.width / img.aspectRatio;
					selectedImgToCanvas(img);
				};
			}
		});
		enableSaveButton();
	};

	const drawEntireScene = () => {
		drawBackground();
		drawSelectedImage();
	};

	const setupCamera = (retryCount = 0) => {
		navigator.mediaDevices
			.getUserMedia({ video: true, audio: false })
			.then((stream) => {
				videoStream = stream;
				video.srcObject = stream;
				var playPromise = video.play();

				if (playPromise !== undefined) {
					playPromise
						.then((_) => {
							video.style.display = "block";
						})
						.catch((error) => {
							console.log("Resizing video...");
						});
				}
			})
			.catch((err) => {
				console.warn(
					`Camera setup attempt ${retryCount + 1} failed: ${err}`
				);
				if (retryCount < 3) {
					// Retry up to 3 times
					retryTimeout = setTimeout(
						() => setupCamera(retryCount + 1),
						1000
					);
				} else {
					showError("No camera detected");
				}
			});
	};

	/* Camera and taking picture logic */
	const initialSetup = () => {
		if (retryTimeout) {
			clearTimeout(retryTimeout);
		}

		// Setup the camera
		try {
			setupCamera();
		} catch (error) {}

		// Set the video dimensions
		video.addEventListener(
			"canplay",
			(ev) => {
				if (!streaming) {
					imgHeight =
						video.videoHeight / (video.videoWidth / imgWidth);
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

		selectedImgs = [
			new SelectedImg(),
			new SelectedImg(),
			new SelectedImg(),
			new SelectedImg(),
			new SelectedImg(),
		];
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

	const getFrame = (src) => {
		const offscreenCanvas = document.createElement("canvas");
		const offscreenCtx = offscreenCanvas.getContext("2d");
		offscreenCanvas.width = 640;
		offscreenCanvas.height = 480;

		offscreenCtx.drawImage(
			src,
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
			isWebcamImg = false;
			if (!validImageTypes.includes(file.type)) {
				showError("Invalid image type");
				return;
			}
			reader.readAsDataURL(file);
			reader.onload = (e) => {
				let fileImageTmp = new Image();
				fileImageTmp.src = e.target.result;
				fileImageTmp.onload = () => {
					backgroundImage.src = getFrame(fileImageTmp);
					drawBackground();
				};
			};
			selectedImgs = [];
		} else {
			isWebcamImg = true;
			selectedImgs = [new SelectedImg()];
			backgroundImage.src = getFrame(video);
			drawBackground();
		}

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
	};

	const resetPicture = () => {
		previousSelectedImage = null;
		postMsg.value = "";
		isWebcamImg = null;
		selectedImgs = [];
		currentSelectedImg = null;
		currentSelectedImgIndex = null;
		imgFileInput.value = "";

		initialSetup(); // Reinitialize the camera when resetting the picture

		video.style.display = "block";
		canvas.style.display = "none";
		document.querySelector(".imgContainer").style.display = "none";
		document.querySelector(".postInfo").style.display = "none";
		startButton.style.display = "block";
		retakeButton.style.display = "none";
		imgFile.style.display = "flex";

		rotationSlider.value = 1;
		sizeSlider.value = 10;

		saveButton.disabled = true;
		saveButton.style.cursor = "not-allowed";
		defaultImages.forEach((img) => {
			img.style.background = "var(--primary)";
		});

		sliderContainer.forEach((slider) => {
			slider.style.display = "none";
		});
		streaming = false;
	};

	const savePost = async () => {
		let bgImage;
		let postMsg;
		try {
			bgImage = backgroundImage.src.src;
			postMsg = postMsgValue;
		} catch (e) {
			showError("Missing images");
			resetPicture();
			return;
		}

		// Convert base64 to Blob
		const base64ToBlob = (base64, mime) => {
			const byteString = atob(base64.split(",")[1]);
			const ab = new ArrayBuffer(byteString.length);
			const ia = new Uint8Array(ab);
			for (let i = 0; i < byteString.length; i++) {
				ia[i] = byteString.charCodeAt(i);
			}
			return new Blob([ab], { type: mime });
		};

		const bgImageBlob = base64ToBlob(bgImage, "image/png");

		const formData = new FormData();
		formData.append("user_id", user_id); // user_id is a global variable defined in the head of the `camera.php` file
		formData.append("postMsg", postMsg);
		formData.append("backgroundImage", bgImageBlob, "backgroundImage.png");
		for (let index = 0; index < selectedImgs.length; index++) {
			formData.append(
				`selectedImg${index}`,
				await fetchImageBlob(selectedImgs[index].src.src),
				`selectedImg${index}.png`
			);
			formData.append(`posx${index}`, selectedImgs[index].position.x);
			formData.append(`posy${index}`, selectedImgs[index].position.y);
			formData.append(`size${index}`, selectedImgs[index].size.width);
			formData.append(`rotation${index}`, selectedImgs[index].rotation);
		}

		fetch("/php/generatePostImage.php", {
			method: "POST",
			body: formData,
		})
			.then((response) => response.json())
			.then((data) => {
				if (data.status === "error") {
					showError(data.message);
					resetPicture();
					return;
				}
				loadUserPosts();
				resetPicture();
			})
			.catch((error) => {
				showError("Failed to save post");
			});
		/** TODO -> Debugging method, remove when working */
		/** ------------------------ */
		/* fetch("/php/generatePostImage.php", {
			method: "POST",
			body: formData,
		})
			.then((response) => response.text())
			.then((text) => {
				try {
					const data = JSON.parse(text);
					if (data.status === "error") {
						console.error("Error:", data.message);
					} else {
						console.log("Success:", data);
					}
				} catch (e) {
					console.error("Error parsing JSON:", text);
				}
			})
			.catch((error) => {
				console.error("Fetch error:", error);
			}); */
		/** ----------------------- */
	};

	const fetchImageBlob = async (url) => {
		const response = await fetch(url);
		const blob = await response.blob();
		return blob;
	};

	const base64ToBlob = (base64, mime) => {
		if (!base64.includes(",")) {
			throw new Error("Invalid base64 string");
		}
		const byteString = atob(base64.split(",")[1]);
		const ab = new ArrayBuffer(byteString.length);
		const ia = new Uint8Array(ab);
		for (let i = 0; i < byteString.length; i++) {
			ia[i] = byteString.charCodeAt(i);
		}
		return new Blob([ab], { type: mime });
	};

	const loadUserPosts = () => {
		const oldPostContainer = document.querySelector("#oldPosts");
		const postContainer = document.querySelector("#postContainer");

		while (oldPostContainer.firstChild)
			oldPostContainer.removeChild(oldPostContainer.firstChild);

		fetch(`/php/getUserPosts.php?user_id=${user_id}&page=1`, {
			method: "GET",
		})
			.then((response) => response.json())
			.then((data) => {
				if (data.length === 0) {
					oldPostContainer.innerHTML =
						"<p>You haven't published any posts yet</p>";
					return;
				}

				data.forEach((post) => {
					const newPost = postContainer.cloneNode(true);
					newPost.style.display = "flex";
					const postImage = newPost.querySelector("img");
					postImage.src = "php/" + post.imagePath;

					const deleteButton = newPost.querySelector(".deleteIcon");
					deleteButton.addEventListener("click", () => {
						handleDeletePost(post, oldPostContainer, newPost);
					});
					oldPostContainer.appendChild(newPost);
				});
			})
			.catch((error) => {
				showError("Failed to load posts");
			});
	};

	const handleDeletePost = (post, oldPostContainer, newPost) => {
		fetch(`/php/deletePost.php?id=${post.id}`, {
			method: "DELETE",
			headers: {
				"Content-Type": "application/json",
			},
		})
			.then((response) => response.json())
			.then((data) => {
				oldPostContainer.removeChild(newPost);
			})
			.catch((error) => {
				showError("Failed to delete post");
			});
	};

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
		const sizePercentage = ev.target.value;
		selectedImgs[currentSelectedImg].size.width = sizePercentage;
		selectedImgs[currentSelectedImg].size.height = sizePercentage;
		drawEntireScene();
		ev.preventDefault();
	});

	rotationSlider.addEventListener("input", (ev) => {
		selectedImgs[currentSelectedImg].rotation = ev.target.value;
		drawEntireScene();
		ev.preventDefault();
	});

	canvas.addEventListener("click", (event) => {
		if (currentSelectedImg === null) return;

		const rect = canvas.getBoundingClientRect();
		const scaleX = canvas.width / rect.width;
		const scaleY = canvas.height / rect.height;

		const x = (event.clientX - rect.left) * scaleX;
		const y = (event.clientY - rect.top) * scaleY;

		selectedImgs[currentSelectedImg].position.x = (x / canvas.width) * 100;
		selectedImgs[currentSelectedImg].position.y = (y / canvas.height) * 100;

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

	// Event listener for resizing the window
	const processChange = debounce(() => {
		resetPicture();
		imgWidth = canvasContainer.offsetWidth;
		initialSetup();
	});

	window.addEventListener("resize", () => {
		processChange();
	});

	initialSetup();
	editImages();
	loadUserPosts();
};
