CREATE TABLE user (
    id INT AUTO_INCREMENT PRIMARY KEY,
    username VARCHAR(50) NOT NULL,
    email VARCHAR(100) NOT NULL,
    password VARCHAR(255) NOT NULL,
    emailCommentPreference BOOLEAN NOT NULL DEFAULT 0,
    emailConfirmed BOOLEAN NOT NULL DEFAULT 0
);

CREATE TABLE post (
    id INT AUTO_INCREMENT PRIMARY KEY,
    posterId INT NOT NULL,
    date DATETIME NOT NULL,
    imagePath VARCHAR(255),
    FOREIGN KEY (posterId) REFERENCES user(id)
);

CREATE TABLE comment (
    commentId INT AUTO_INCREMENT PRIMARY KEY,
    postId INT NOT NULL,
    commenterId INT NOT NULL,
    message TEXT NOT NULL,
    FOREIGN KEY (postId) REFERENCES post(id),
    FOREIGN KEY (commenterId) REFERENCES user(id)
);

CREATE TABLE `like` (
    likeId INT AUTO_INCREMENT PRIMARY KEY,
    userId INT NOT NULL,
    postId INT NOT NULL,
    FOREIGN KEY (userId) REFERENCES user(id),
    FOREIGN KEY (postId) REFERENCES post(id)
);
