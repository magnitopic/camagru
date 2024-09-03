const posts = document.querySelectorAll(".postContainer");
const postInfo = document.querySelector(".postInfoContainer");

let selectedPost = null;

posts.forEach((post) => {
	post.addEventListener("click", () => {
		postInfo.style.display = "flex";
		selectedPost = post;
	});
});

postInfo.addEventListener("click", () => {
	postInfo.style.display = "none";
	selectedPost = null;
});
