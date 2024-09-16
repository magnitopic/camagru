const posts = document.querySelectorAll("#postContainer");
const postInfo = document.querySelector(".postInfoContainer");
const galleryContainer = document.querySelector("main");
const postTemplate = document.querySelector("#postContainer");
const likeButton = document.querySelector("#postInfoLikes");

let page = 1;
let selectedPost = null;

// hide postInfo

postInfo.addEventListener("click", () => {
	if (event.target === event.currentTarget) {
		postInfo.style.display = "none";
		selectedPost = null;
	}
});

// Add event listener for keydown to hide postInfo on Escape key press
document.addEventListener("keydown", (event) => {
	if (event.key === "Escape" && postInfo.style.display === "flex") {
		postInfo.style.display = "none";
		selectedPost = null;
	}
});

// fetch and load posts
const fetchPosts = async () => {
	const response = await fetch(`/php/getPosts.php?page=${page}`);
	const posts = await response.json();

	console.log(posts); // TODO -> remove
	console.log(page); // TODO -> remove

	posts.forEach((post) => {
		const postElement = postTemplate.cloneNode(true);
		postElement.style.display = "block";
		// load post card element data
		postElement.querySelector("#postImg").src = "php/" + post.imagePath;
		postElement.querySelector("#postImg").alt = post.title;
		postElement.querySelector("#postLikes").textContent = post.likes;
		postElement.classList.remove("postTemplate");
		galleryContainer.appendChild(postElement);

		// load post info data
		postElement.addEventListener("click", () => {
			postInfo.style.display = "flex";
			selectedPost = post;
			postInfo.querySelector("#postInfoImg").src =
				"php/" + post.imagePath;
			postInfo.querySelector("#postInfoTitle").textContent = post.title;
			postInfo.querySelector("#postInfoAuthor").textContent = post.author;
			postInfo.querySelector("#postInfoLikes").textContent = post.likes;
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

const likePost = async () => {
	if (selectedPost) {
		fetch(`php/likePost.php?postId=${selectedPost.id}&userId=${user_id}`)
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
			});
		const likes = selectedPost.likes + 1;

		postInfo.querySelector("#postInfoLikes").textContent = likes;
	}
};

likeButton.addEventListener("click", likePost);
const observer = new IntersectionObserver(handleIntersect, options);
observer.observe(document.querySelector("footer"));
window.addEventListener("resize", checkPageFilled);
document.addEventListener("DOMContentLoaded", fetchPosts);
