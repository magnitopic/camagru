let isSubmitting = false;

const handleResetPasswordForm = () => {
	const resetForm = document.getElementById("resetPasswordForm");
	const messageElement = document.getElementById("reset-form-message");
	const submitButton = document.getElementById("reset-form-submit");

	if (!resetForm || !messageElement) return;

	const showResetMessage = (message, isError = false) => {
		messageElement.textContent = message;
		messageElement.className = `form-message ${
			isError ? "form-error-message" : "form-success-message"
		}`;
		messageElement.style.display = "block";
		setTimeout(() => {
			messageElement.style.display = "none";
		}, 5000);
	};

	resetForm.addEventListener("submit", async (event) => {
		event.preventDefault();
		if (isSubmitting) return;

		const formData = new FormData(resetForm);

		try {
			isSubmitting = true;
			submitButton.disabled = true;
			submitButton.textContent = "Submitting...";

			const response = await fetch(resetForm.action, {
				method: "POST",
				body: formData,
			});

			const result = await response.json();

			if (result.success) {
				showResetMessage(result.message);
				resetForm.reset();
			} else {
				showResetMessage(result.errors.join("\n"), true);
			}
		} catch (error) {
			showResetMessage(
				"An error occurred. Please try again later.",
				true
			);
		} finally {
			isSubmitting = false;
			submitButton.disabled = false;
			submitButton.textContent = "Reset Password";
		}
	});
};

document.addEventListener("DOMContentLoaded", handleResetPasswordForm);
