CREATE TABLE users (
    uuid TEXT PRIMARY KEY,
    username TEXT NOT NULL,
    first_name TEXT NOT NULL,
    last_name TEXT NOT NULL
);

CREATE TABLE posts (
    uuid TEXT PRIMARY KEY,
    author_uuid TEXT NOT NULL,
    title TEXT NOT NULL,
    text TEXT NOT NULL,
    FOREIGN KEY(author_uuid) REFERENCES users(uuid)
);

CREATE TABLE comments (
    uuid TEXT PRIMARY KEY,
    post_uuid TEXT,
    author_uuid TEXT,
    text TEXT NOT NULL,
    FOREIGN KEY(post_uuid) REFERENCES posts(uuid)
);

CREATE TABLE post_likes (
    uuid TEXT PRIMARY KEY,
    post_uuid TEXT NOT NULL,
    user_uuid TEXT NOT NULL,
    FOREIGN KEY (post_uuid) REFERENCES posts(uuid),
    FOREIGN KEY (user_uuid) REFERENCES users(uuid)
);

CREATE TABLE comment_likes (
    uuid TEXT PRIMARY KEY,
    comment_uuid TEXT NOT NULL,
    user_uuid TEXT NOT NULL,
    FOREIGN KEY (comment_uuid) REFERENCES comments(uuid),
    FOREIGN KEY (user_uuid) REFERENCES users(uuid)
);

CREATE TABLE tokens
(
    token TEXT PRIMARY KEY,
    user_uuid TEXT NOT NULL,
    expires_on TEXT NOT NULL,
    FOREIGN KEY (user_uuid) REFERENCES users(uuid)
);