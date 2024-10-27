# Camagru

This project challenges you to make a webpage from which to upload, edit and share pictures with other users. You can choose to take a picture with your webcam or upload an image directly from your computer. You can also add stickers, change their size, rotation and position, and save the final image. The project is written in PHP, HTML, CSS and JS.

There is a public gallery where the images published by all users are displayed. You can like and comment on the images, and the author of the image will receive an email notification, if he wants.

<div align="center">
<img alt="Camagru project view" src="https://github.com/user-attachments/assets/2fd0293f-78d5-403a-8352-3716624982d1">
</div>

## How to run

```bash
cp .env.example .env

# Edit the .env file with your database credentials
vim .env

make
```

> **_MacOS:_** You may need to also run this command for the .env variables to work properly

```bash
export $(grep -v '^#' .env | xargs)
```

## Docker containers

This projects uses three docker containers with the following services:

-   **nginx** - Used as a proxy server to serve the PHP application
-   **php** - PHP server responding to the user's requests and interacting with the database. Backend is in pure PHP and frontend is in HTML, CSS and JS
-   **mysql** - Database to store the application's data. It has the structure shown below

## Database structure

<div align="center">
<img alt="database structure" src="https://github.com/user-attachments/assets/acb18354-2784-45e8-9429-d6d8df24fac3">
</div>
