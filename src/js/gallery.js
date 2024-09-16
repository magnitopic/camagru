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

let page = 1;
const galleryContainer = document.querySelector("main");
const postTemplate = document.getElementById("postContainer");

const fetchPosts = async () => {
	const response = await fetch(`/php/getPosts.php?page=${page}`);
	const posts = await response.json();

	console.log(posts);

	posts.forEach((post) => {
		const postElement = postTemplate.cloneNode(true);
		postElement.style.display = "block";
		postElement.querySelector("#postImg").src = "php/" + post.imagePath;
		postElement.querySelector("#postImg").alt = post.title;
		postElement.classList.remove("postTemplate");
		galleryContainer.appendChild(postElement);
	});
	page++;
};

const handleScroll = () => {
	if (window.innerHeight + window.scrollY >= document.body.offsetHeight) {
		fetchPosts();
	}
};

window.addEventListener("scroll", handleScroll);
document.addEventListener("DOMContentLoaded", fetchPosts);
