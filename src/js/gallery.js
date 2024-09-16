const posts = document.querySelectorAll("#postContainer");
const postInfo = document.querySelector(".postInfoContainer");
const galleryContainer = document.querySelector("#galleryContainer");
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

	if (page === 1 && posts.length === 0) {
		galleryContainer.innerHTML = 
		"<center>\
		<h1>Gallery is empty for now :(</h1>\
		<h3>Be the first to publish a picture!</h3>\
		</center>";
	}
	if (posts.length === 0) {
		observer.unobserve(document.querySelector("footer"));
		return;
	}

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

const updateLikes = (newLikes) => {
	// update selectedPost likes
	selectedPost.likes = newLikes.likes;

	// update postInfo likes
	postInfo.querySelector("#postInfoLikes").textContent = selectedPost.likes;

	// remove all elements from galleryContainer
	while (galleryContainer.firstChild) {
		galleryContainer.removeChild(galleryContainer.firstChild);
	}
	// fetch and load posts
	page = 1;
	fetchPosts();
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
			.then((response) => response.json())
			.then((data) => {
				updateLikes(data);
			})
			.catch((error) => console.error("Error:", error));
	}
};

likeButton.addEventListener("click", likePost);
window.addEventListener("resize", checkPageFilled);
// create observer to fetch more posts when user scrolls to the bottom of the page
const observer = new IntersectionObserver(handleIntersect, options);
observer.observe(document.querySelector("footer"));
