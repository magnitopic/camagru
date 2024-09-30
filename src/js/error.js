const showError = (message) => {
	const errorMessage = document.getElementById("error-message");
	errorMessage.textContent = message;
	errorMessage.style.display = "block";
	setTimeout(() => {
		errorMessage.style.display = "none";
	}, 5000); // Hide after 5 seconds
};

const showFormError = (form) => {
	form.addEventListener("submit", async (event) => {
		event.preventDefault();

		const formData = new FormData(form);
		const response = await fetch(form.action, {
			method: "POST",
			body: formData,
		});

		const result = await response.json();

		const errorMessage = document.getElementById("form-error-message");
		if (!result.success) {
			errorMessage.innerHTML = result.errors
				.map((error) => `<p>${error}</p>`)
				.join("");
			errorMessage.style.display = "block";
		} else {
			window.location.href = "/camera.php";
		}
	});
};
