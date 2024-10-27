# Camagru

This web project is challenging you to create a small web application allowing you to
make basic photo and video editing using your webcam and some predefined images

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
<img src="https://github.com/user-attachments/assets/acb18354-2784-45e8-9429-d6d8df24fac3">
</div>
