document.addEventListener("DOMContentLoaded", () => {
	const userDropdown = document.querySelector(".userDropdown details");
	const dropdownContent = document.getElementById("dropdownContent");

	userDropdown.addEventListener("toggle", () => {
		if (userDropdown.open) {
			dropdownContent.style.display = "flex";
		} else {
			dropdownContent.style.display = "none";
		}
	});

	window.addEventListener("click", (event) => {
		if (!userDropdown.contains(event.target)) {
			userDropdown.open = false;
			dropdownContent.style.display = "none";
		}
	});
});
