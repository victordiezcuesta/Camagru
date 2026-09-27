# Camagru

> A secure, containerized photo-sharing web application built as part of the 42 Madrid Common Core.

<p align="center">
  <strong>📸 Capture · 🎨 Edit · 🌐 Share · 💬 Interact</strong>
</p>

<p align="center">
  <img src="https://img.shields.io/badge/42-Madrid-000000?style=for-the-badge&logo=42&logoColor=white" alt="42 Madrid">
  <img src="https://img.shields.io/badge/PHP-8.3-777BB4?style=for-the-badge&logo=php&logoColor=white" alt="PHP 8.3">
  <img src="https://img.shields.io/badge/MariaDB-11-003545?style=for-the-badge&logo=mariadb&logoColor=white" alt="MariaDB 11">
  <img src="https://img.shields.io/badge/Docker-Containerized-2496ED?style=for-the-badge&logo=docker&logoColor=white" alt="Docker">
  <img src="https://img.shields.io/badge/Nginx-Web_Server-009639?style=for-the-badge&logo=nginx&logoColor=white" alt="Nginx">
</p>

## 📖 About

**Camagru** is a full-stack web application developed for the **42 School Camagru v4.1** project.

The goal is to build a photo-sharing platform where authenticated users can capture or upload images, combine them with predefined transparent overlays, and publish the resulting photos to a public gallery.

The project focuses not only on the final product, but also on understanding the fundamentals behind a secure web application: authentication, sessions, password hashing, CSRF protection, input validation, file validation, SQL queries, email verification, password recovery, and containerized deployment.

The official subject requires the application to provide public, likeable and commentable images, authenticated photo editing, server-side image composition, user management and a secure implementation. fileciteturn0file0L55-L67

---

## ✨ Features

### 🔐 Authentication & Account Management

- User registration with:
  - Username validation
  - Email validation
  - Password complexity requirements
- Password hashing using PHP password hashing APIs
- Email account verification through a unique token
- Login / logout
- Session-based authentication
- Password recovery by email
- Password reset using a time-limited token
- Profile management
- Username changes
- Email changes with re-verification
- Password changes
- CSRF protection on state-changing forms

The authentication flow covers the main user-management requirements of the Camagru subject. fileciteturn0file0L119-L128

### 📸 Photo Creation

Authenticated users can access a dedicated creation area to:

- Activate their webcam
- Capture a photo directly from the browser
- Select a predefined overlay
- Compose the final image
- Upload an image when a webcam is unavailable
- View previously created photos
- Delete their own edited images

The subject specifically requires webcam capture, selectable overlays, server-side image composition, upload support and ownership-based deletion. fileciteturn0file0L140-L156

### 🖼️ Public Gallery

- Public gallery of user-created images
- Images ordered by creation date
- Pagination
- At least 5 images per page
- Individual image interactions
- Like system
- Comment system
- User notifications for new comments
- Email notification support

These features follow the gallery requirements defined by the project subject. fileciteturn0file0L129-L136

### 🛡️ Security

Security is treated as a core part of the application rather than an afterthought.

Implemented protections include:

- Password hashing
- Prepared SQL statements through PDO
- Server-side input validation
- CSRF tokens
- Secure session configuration
- Authentication and authorization checks
- File MIME/type validation
- Image validation before processing
- File size restrictions
- Ownership checks before deleting content
- Environment variables for credentials
- `.env` excluded from Git
- Output escaping to prevent HTML/JavaScript injection

The project specification explicitly requires secure forms and protection against issues such as stored passwords, HTML/JavaScript injection, malicious uploads, SQL injection and unauthorized manipulation of private data. fileciteturn0file0L103-L115

---

## 🏗️ Architecture

The application follows an MVC-oriented structure to keep responsibilities separated between routing, business logic and presentation.

```text
                    ┌──────────────────────┐
                    │       Browser        │
                    │ HTML / CSS / JS      │
                    └──────────┬───────────┘
                               │
                               │ HTTP
                               ▼
                    ┌──────────────────────┐
                    │        Nginx         │
                    │    Web Server /      │
                    │   FastCGI Gateway    │
                    └──────────┬───────────┘
                               │
                               ▼
                    ┌──────────────────────┐
                    │      PHP-FPM         │
                    │   Application Layer  │
                    │                      │
                    │ Controllers          │
                    │ Services             │
                    │ Models / Database    │
                    │ Security             │
                    └───────┬───────┬──────┘
                            │       │
                 ┌──────────┘       └──────────┐
                 ▼                             ▼
        ┌─────────────────┐           ┌─────────────────┐
        │     MariaDB     │           │   SMTP / Mail   │
        │     Database    │           │ Verification &  │
        │                 │           │ Password Reset  │
        └─────────────────┘           └─────────────────┘
```

### Main layers

**Routing**

The front controller receives requests and dispatches them to the appropriate controller.

**Controllers**

Handle HTTP requests, validate input and coordinate application services.

**Services**

Contain reusable business logic such as image processing, email delivery and authentication-related operations.

**Security**

Centralizes functionality such as sessions and CSRF protection.

**Views**

PHP templates generate the HTML presented to the user.

**Database**

MariaDB stores users, images and social interactions.

---

## 🧰 Tech Stack

| Layer | Technology |
|---|---|
| Backend | PHP 8.3 |
| Database | MariaDB 11 |
| Database access | PDO / MySQL |
| Web server | Nginx |
| Frontend | HTML5, CSS3, JavaScript |
| Image processing | PHP image APIs |
| Authentication | PHP Sessions |
| Email | SMTP |
| Containerization | Docker + Docker Compose |
| Version control | Git |
| Development environment | Linux / Ubuntu |

The project constraints require HTML, CSS and JavaScript on the client side, allow server-side languages subject to the PHP standard-library constraint, and require containerized deployment. fileciteturn0file0L70-L90 fileciteturn0file0L161-L170

---

## 🐳 Docker Architecture

The application is split into independent containers:

```text
┌───────────────────────────────────────────────┐
│                 Docker Compose                │
│                                               │
│   ┌─────────────┐   ┌─────────────┐          │
│   │    Nginx    │──▶│  PHP-FPM    │          │
│   │   :8080     │   │   :9000     │          │
│   └─────────────┘   └──────┬──────┘          │
│                            │                  │
│                            ▼                  │
│                    ┌─────────────┐            │
│                    │   MariaDB   │            │
│                    │    :3306    │            │
│                    └─────────────┘            │
└───────────────────────────────────────────────┘
```

This makes the development environment reproducible and keeps the web server, application runtime and database separated.

---

## 📂 Project Structure

```text
├── database
│   └── schema.sql
├── docker
│   ├── nginx
│   │   └── default.conf
│   └── php
│       ├── Dockerfile
│       └── php.ini
├── docker-compose.yml
├── Makefile
├── public
│   ├── assets
│   │   ├── css
│   │   │   └── style.css
│   │   ├── js
│   │   │   └── photo-editor.js
│   │   └── overlays
│   │       ├── 01_laptop_programacion.png
│   │       ├── 02_42_madrid.png
│   │       ├── 03_devs_no_duermen.png
│   │       ├── 04_gaming.png
│   │       ├── 05_viajes_montana.png
│   │       ├── 06_cafe_programador.png
│   │       ├── 07_minecraft_pixel.png
│   │       ├── 08_linux_forever.png
│   │       ├── 09_ramen.png
│   │       ├── 101_playa_tropical.png
│   │       ├── 102_romantico_kawaii.png
│   │       ├── 103_cine_film.png
│   │       ├── 104_aventura_montana.png
│   │       ├── 105_halloween.png
│   │       ├── 10_tu_puedes.png
│   │       ├── 11_tiburon_good_vibes.png
│   │       ├── 12_astroespacio.png
│   │       ├── 13_terminal_keep_going.png
│   │       ├── 14_good_boy_42.png
│   │       ├── 15_disciplina_montana.png
│   │       ├── 16_coder_sonoliento.png
│   │       ├── 17_banana_lets_go.png
│   │       ├── 18_42_cursor.png
│   │       ├── 19_pizza.png
│   │       ├── 20_cactus.png
│   │       ├── 21_dog.png
│   │       ├── 22_gafas_bigote.png
│   │       └── 23_ojos.png
│   ├── favicon.ico
│   ├── index.php
│   └── uploads
├── README.md
└── src
    ├── config
    │   └── Database.php
    ├── Controllers
    │   ├── AuthController.php
    │   ├── GalleryController.php
    │   ├── GalleryInteractionController.php
    │   ├── HomeController.php
    │   ├── PhotoController.php
    │   └── ProfileController.php
    ├── Security
    │   ├── Csrf.php
    │   ├── PasswordValidator.php
    │   └── Session.php
    ├── Services
    │   ├── ImageService.php
    │   └── Mailer.php
    └── Views
        ├── auth
        │   ├── forgot-password.php
        │   ├── login.php
        │   ├── profile.php
        │   ├── register.php
        │   ├── registration-success.php
        │   └── reset-password.php
        ├── error.php
        ├── gallery-cards.php
        ├── gallery.php
        ├── home.php
        ├── photo
        │   └── create.php
        └── success.php
```

> The exact directory contents may evolve during development; the structure above describes the application's main architectural organization.

---

## 🚀 Getting Started

### Requirements

- Docker
- Docker Compose
- Git

### 1. Clone the repository

```bash
git clone <your-repository-url>
cd Camagru
```

### 2. Configure environment variables

Create the local `.env` file with the database and mail configuration required by the application.

```env
DB_HOST=db
DB_PORT=3306
DB_NAME=camagru
DB_USER=...
DB_PASSWORD=...

MAIL_HOST=...
MAIL_PORT=...
MAIL_USERNAME=...
MAIL_PASSWORD=...
MAIL_FROM=...
MAIL_ENCRYPTION=...
```

**Never commit `.env` or real credentials to Git.**

The Camagru subject explicitly requires credentials, API keys and environment variables to remain local and excluded from Git. fileciteturn0file0L92-L97

### 3. Start the application

```bash
make
```

Or, depending on the Makefile target:

```bash
docker compose up --build
```

### 4. Open the application

```text
http://localhost:8080
```

---

## 🧪 Development

Useful Makefile commands:

```bash
make
make down
make logs
make clean
make fclean
make re
```

To inspect the running containers:

```bash
docker compose ps
```

To follow application logs:

```bash
docker compose logs -f
```

---

## 🔒 Security Design

One of the main objectives of this project is learning how to build a web application without relying on frameworks that hide the underlying mechanisms.

### Password security

Passwords are never stored as plaintext.

```php
$passwordHash = password_hash($password, PASSWORD_DEFAULT);
```

During login:

```php
password_verify($password, $passwordHash);
```

### SQL injection protection

Database operations use PDO prepared statements rather than concatenating user input into SQL queries.

```php
$stmt = $pdo->prepare(
    'SELECT id, password FROM users WHERE username = :username'
);

$stmt->execute([
    'username' => $username
]);
```

### CSRF protection

State-changing forms include CSRF tokens that are generated server-side and validated before processing the request.

### Session security

Authenticated users are identified through server-side sessions rather than trusting client-provided user IDs.

### File validation

Uploaded images are validated before being accepted, including file size and detected MIME/image information.

This is particularly important because the subject explicitly considers unrestricted malicious uploads and unsafe user-controlled data to be security failures. fileciteturn0file0L109-L115

---

## 🧠 What I Learned

Camagru is focused on understanding the fundamentals behind a web application rather than hiding them behind a large framework.

### Backend

- PHP application architecture
- MVC concepts
- HTTP request/response flow
- Sessions and authentication
- Password hashing
- PDO and prepared statements
- Server-side validation
- File uploads
- Image processing
- SMTP communication

### Frontend

- Semantic HTML
- CSS layouts and responsive design
- DOM manipulation
- Browser APIs
- Webcam access with `getUserMedia()`
- Client-side validation
- Form interaction

### Database

- Relational database design
- Primary and foreign keys
- Constraints
- Cascading deletes
- Unique relationships
- Pagination queries
- User/content relationships

### Security

- CSRF
- XSS prevention
- SQL injection prevention
- Secure password storage
- Session management
- Authorization
- Secure file handling

### Infrastructure

- Docker
- Docker Compose
- Nginx
- PHP-FPM
- MariaDB
- Container networking
- Environment-based configuration

The subject itself highlights responsive design, DOM manipulation, SQL debugging, CSRF and CORS as part of the concepts introduced by the project. fileciteturn0file0L39-L52

---

## 🎯 Project Requirements

Camagru's mandatory specification is centered around four areas:

| Area | Main requirement |
|---|---|
| Common | Secure, validated, responsive web application |
| Users | Registration, verification, login, password reset and profile management |
| Gallery | Public images, pagination, likes, comments and notifications |
| Editing | Webcam/upload, overlays, server-side composition and image deletion |

The project specification also requires the application to be deployable through containerization. fileciteturn0file0L101-L115 fileciteturn0file0L119-L156

---

## 🎓 42 Madrid

This project was developed as part of the **42 Madrid Outer Core** curriculum.

42 projects are designed around practical development, peer evaluation and progressively more complex technical challenges.

Camagru focuses particularly on:

```text
Web Development
       │
       ├── Backend
       ├── Frontend
       ├── Databases
       ├── Authentication
       ├── Security
       ├── File Handling
       └── Docker
```

---

## 👨‍💻 Author

**Víctor Díez Cuesta**

Software Developer · 42 Madrid

- GitHub: `github.com/victordiezcuesta`
- LinkedIn: `linkedin.com/in/victor-diez-cuesta/`

---

## 📄 License

This repository was created as part of my academic work at **42 Madrid**.

The project specification and evaluation criteria belong to 42. This repository contains my own implementation of the project.
