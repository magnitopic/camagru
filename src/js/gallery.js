const posts = document.querySelectorAll("#postContainer");
const postInfo = document.querySelector(".postInfoContainer");
const galleryContainer = document.querySelector("#galleryContainer");
const postTemplate = document.querySelector("#postContainer");
const likeButton = document.querySelector("#likePostButton");
const newCommentForm = postInfo.querySelector("#newCommentForm");
const shareButton = document.querySelector(".sharePost");
const shareDropdown = document.querySelector(".shareDropdown");

let page = 0;
let selectedPost = null;

// hide postInfo
postInfo.addEventListener("click", () => {
	if (event.target === event.currentTarget) {
		postInfo.style.display = "none";
		selectedPost = null;
		document.body.style.overflow = "visible";
		shareDropdown.classList.remove("show");
	}
});

// Add event listener for keydown to hide postInfo on Escape key press
document.addEventListener("keydown", (event) => {
	if (event.key === "Escape" && postInfo.style.display === "flex") {
		postInfo.style.display = "none";
		selectedPost = null;
		document.body.style.overflow = "visible";
		shareDropdown.classList.remove("show");
	}
});

// fetch and load posts
const fetchPosts = async () => {
	page++;
	const response = await fetch(
		`/php/getPosts.php?page=${page}&user_id=${user_id}`
	);
	let posts;
	try {
		posts = await response.json();
	} catch (error) {
		showError("Failed to fetch posts");
		showGalleryEmptyMgs();
		return;
	}

	if (page === 1 && posts.length === 0) showGalleryEmptyMgs();
	if (posts.length === 0) {
		observer.unobserve(document.querySelector("footer"));
		return;
	} else observer.observe(document.querySelector("footer"));

	posts.forEach((post) => {
		const postElement = postTemplate.cloneNode(true);
		postElement.style.display = "block";
		// load post card element data
		loadPostInfo(postElement, post);

		// load post info data
		postElement.addEventListener("click", () => {
			handlePostInfo(post);
			// load post comments
			loadComments(post.comments);
		});

		// check post liked by user
		if (post.liked)
			postElement.querySelector("#like").classList.toggle("likedPost");
	});
};

const showGalleryEmptyMgs = () => {
	galleryContainer.innerHTML =
		"<center>\
		<h1>Gallery is empty for now :(</h1>\
		<h3>Be the first to publish a picture!</h3>\
		</center>";
};

const loadPostInfo = (postElement, post) => {
	postElement.querySelector("#postImg").src = "php/" + post.imagePath;
	postElement.querySelector("#postImg").alt = post.title;
	postElement.querySelector("#postLikes").textContent = post.likes;
	postElement.querySelector("#postComments").textContent =
		post.comments.length;
	postElement.classList.remove("postTemplate");
	galleryContainer.appendChild(postElement);
};

const handlePostInfo = (post) => {
	postInfo.style.display = "flex";
	selectedPost = post;
	postInfo.querySelector("#postInfoImg").src = "php/" + post.imagePath;
	postInfo.querySelector("#postInfoTitle").textContent = post.title;
	postInfo.querySelector("#postInfoAuthor").textContent = post.author;
	postInfo.querySelector("#postInfoLikes").textContent = post.likes;

	// Format the post date
	const postDate = new Date(post.date);
	const formattedDate = postDate.toLocaleDateString("en-US", {
		year: "numeric",
		month: "long",
		day: "numeric",
	});
	postInfo.querySelector("#postDate").textContent = formattedDate;

	if (post.liked) likeButton.classList.add("likedPost");
	else likeButton.classList.remove("likedPost");
	document.body.style.overflow = "hidden";

	const postUrl = window.location.href;
	const postText = `Check out this post by ${post.author} in the Camagru project: "${post.title}"`;

	// update share links
	let shareLinks = [
		`https://twitter.com/intent/tweet?url=${encodeURIComponent(
			postUrl
		)}&text=${encodeURIComponent(postText)}`,
		`https://www.facebook.com/sharer/sharer.php?u=${encodeURIComponent(
			postUrl
		)}`,
		`https://www.linkedin.com/feed/?shareActive=true&text=${
			encodeURIComponent(postText) + " " + encodeURIComponent(postUrl)
		}`,
	];
	shareDropdown.querySelectorAll("a").forEach((link, index) => {
		link.href = shareLinks[index];
		link.target = "_blank";
	});
};

const loadComments = (comments) => {
	const commentsContainer = postInfo.querySelector("#commentsContainer");

	// remove all previous comments
	while (commentsContainer.firstChild)
		commentsContainer.removeChild(commentsContainer.firstChild);

	if (comments.length === 0) {
		const noComments = document.createElement("h3");
		noComments.textContent = "No comments yet!";
		noComments.style.margin = "auto";
		noComments.style.color = "gray";
		commentsContainer.appendChild(noComments);
		return;
	}

	const commentTemplate = postInfo.querySelector("#fullComment");
	comments.forEach((comment) => {
		const commentElement = commentTemplate.cloneNode(true);
		commentElement.style.display = "block";

		const authorElement = commentElement.querySelector(".commentAuthor");

		authorElement.textContent = comment.author;

		const msgElement = commentElement.querySelector("#commentMsg");
		msgElement.textContent = comment.message;

		commentsContainer.appendChild(commentElement);
	});
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
	page = 0;
	fetchPosts();

	// update like button color
	likeButton.classList.toggle("likedPost");
};

const handleNewComment = (event) => {
	event.preventDefault();
	const comment = newCommentForm.querySelector("#newComment").value;
	if (comment === "") return;

	const data = new FormData();
	data.append("postId", selectedPost.id);
	data.append("userId", user_id);
	data.append("comment", comment);

	fetch("php/addComment.php", {
		method: "POST",
		body: data,
	})
		.then((response) => {
			if (!response.ok) {
				if (response.status === 401) throw new Error("Unauthorized");
				else throw new Error("Failed to like post");
			}
			return response.json();
		})
		.then((newCommentList) => {
			loadComments(newCommentList);
			newCommentForm.querySelector("#newComment").value = "";
			// remove all elements from galleryContainer
			while (galleryContainer.firstChild) {
				galleryContainer.removeChild(galleryContainer.firstChild);
			}
			// fetch and load posts
			page = 0;
			fetchPosts();
		})
		.catch((error) => {
			if (error.message === "Unauthorized")
				window.location.href = "/login.php";
			else showError(error.message);
		});
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
			.then((response) => {
				if (!response.ok) {
					if (response.status === 401)
						throw new Error("Unauthorized");
					else throw new Error("Failed to like post");
				}
				return response.json();
			})
			.then((data) => {
				updateLikes(data);
			})
			.catch((error) => {
				if (error.message === "Unauthorized")
					window.location.href = "/login.php";
				else showError(error.message);
			});
	}
};

const processChange = debounce(() => {
	observer.unobserve(document.querySelector("footer"));
	while (galleryContainer.firstChild)
		galleryContainer.removeChild(galleryContainer.firstChild);
	page = 0;
	fetchPosts();
	observer.observe(document.querySelector("footer"));
});

window.addEventListener("resize", () => processChange());
likeButton.addEventListener("click", likePost);
shareButton.addEventListener("click", () =>
	shareDropdown.classList.toggle("show")
);
// create observer to fetch more posts when user scrolls to the bottom of the page
const observer = new IntersectionObserver(handleIntersect, options);
newCommentForm.addEventListener("submit", handleNewComment);
fetchPosts();
