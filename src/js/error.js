const showError = (message) => {
	const errorMessage = document.getElementById("error-message");
	errorMessage.textContent = message;
	errorMessage.style.display = "block";
	setTimeout(() => {
		errorMessage.style.display = "none";
	}, 5000); // Hide after 5 seconds
};
