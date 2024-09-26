window.onload = () => {
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
				selectedImg.src = img;
				img.style.background = "#b57410";
				console.log(selectedImg.previousSelectedImage);
				console.log(
					selectedImg.previousSelectedImage != selectedImg.src
				);

				if (
					selectedImg.previousSelectedImage != null &&
					selectedImg.previousSelectedImage.src != selectedImg.src
				)
					selectedImg.previousSelectedImage.style.background =
						"#1051B5";
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

	function drawTest() {
		ctx.save();
		ctx.translate(selectedImg.position.x, selectedImg.position.y);
		ctx.rotate((selectedImg.rotation * Math.PI) / 180); // Rotate the image

		// Draw the image, adjusting the position to account for the translation
		ctx.drawImage(
			selectedImg.src,
			-(selectedImg.size.width / 2),
			-(selectedImg.size.height / 2),
			selectedImg.size.width,
			selectedImg.size.height
		);

		ctx.restore();
	}

	const drawSelectedImage = () => {
		if (selectedImg.src === null) return;
		if (selectedImg.src.complete) drawTest();
		else {
			selectedImg.src.onload = () => {
				drawTest();
			};
		}

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
	};

	const resetPicture = () => {
		if (selectedImg.previousSelectedImage != null) {
			selectedImg.previousSelectedImage.style.background = "#1051B5";
			console.log("HERE");
			selectedImg.previousSelectedImage = null;
		}
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

	const savePost = async () => {
		const bgImage = backgroundImage.src.src;
		const selImageUrl = selectedImg.src.src; // URL to the selected image
		const postMsg = postMsgValue;

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

		// Fetch the selected image data from the URL and convert it to a blob
		const fetchImageBlob = async (url) => {
			const response = await fetch(url);
			const blob = await response.blob();
			return blob;
		};

		const bgImageBlob = base64ToBlob(bgImage, "image/png");
		const selImageBlob = await fetchImageBlob(selImageUrl);

		const formData = new FormData();
		formData.append("backgroundImage", bgImageBlob, "backgroundImage.png");
		formData.append("selectedImg", selImageBlob, "selectedImg.png");
		formData.append("posx", selectedImg.position.x);
		formData.append("posy", selectedImg.position.y);
		formData.append("size", selectedImg.size.width);
		formData.append("rotation", selectedImg.rotation);
		formData.append("postMsg", postMsg);
		formData.append("user_id", user_id); // user_id is a global variable defined in the php file head

		fetch("/php/generatePostImage.php", {
			method: "POST",
			body: formData,
		})
			.then((response) => response.json())
			.then((data) => {
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
		selectedImg.size.width = ev.target.value;
		selectedImg.size.height = ev.target.value;
		drawEntireScene();
		ev.preventDefault();
	});

	rotationSlider.addEventListener("input", (ev) => {
		selectedImg.rotation = ev.target.value;
		drawEntireScene();
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
	editImages();
	loadUserPosts();
};
