const posts = document.querySelectorAll("#postContainer");
const postInfo = document.querySelector(".postInfoContainer");

let selectedPost = null;

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

	posts.forEach((post) => {
		const postElement = postTemplate.cloneNode(true);
		postElement.style.display = "block";
		postElement.querySelector("#postImg").src = "php/" + post.imagePath;
		postElement.querySelector("#postImg").alt = post.title;
		postElement.classList.remove("postTemplate");
		galleryContainer.appendChild(postElement);
		postElement.addEventListener("click", () => {
			postInfo.style.display = "flex";
			postInfo.querySelector("#postInfoImg").src =
				"php/" + post.imagePath;
			postInfo.querySelector("#postInfoTitle").textContent = post.title;
			selectedPost = post;
		});
	});
	page++;
	checkPageFilled();
};

const checkPageFilled = () => {
	if (document.body.scrollHeight <= window.innerHeight) {
		fetchPosts();
	}
};

const options = {
	root: null,
	rootMargin: "0px",
	threshold: 0.5,
};

const handleIntersect = (entries, observer) => {
	entries.forEach((entry) => {
		if (entry.isIntersecting) {
			fetchPosts();
		}
	});
};

const observer = new IntersectionObserver(handleIntersect, options);
observer.observe(document.querySelector("footer"));
window.addEventListener("resize", checkPageFilled);
document.addEventListener("DOMContentLoaded", fetchPosts);
