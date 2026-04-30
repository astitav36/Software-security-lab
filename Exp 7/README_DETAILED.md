# Detailed Project Documentation

## 1. Project Overview

This document is a full handoff and explanation of the project built in this folder.

The project is a Python-based web application created for a software security lab. The purpose of the application is to demonstrate:

1. How password hashing works.
2. Why storing plain passwords in a database is unsafe.
3. What the limitation of simple hashing is.
4. How salting improves password storage security.
5. How a Python web application can interact with a MySQL database using XAMPP.

The application uses Flask for the web framework and MySQL for the database. MySQL is expected to run through XAMPP.

The app includes:

1. A registration page for hash-only registration.
2. A registration page for hashing with salt.
3. A login page that verifies stored credentials.
4. A dashboard page to inspect stored data in the database.
5. A delete action to remove users from the database.
6. Automatic database and table creation if they do not already exist.

The central educational goal of the app is to show that:

1. Passwords should not be stored directly.
2. Hashing transforms a password into a fixed-length digest.
3. Hashing alone still has weaknesses.
4. Adding a random salt makes stored password hashes stronger and more unique.

## 2. Main Security Concepts Demonstrated

### 2.1 Plain Password Storage

If a website stores passwords directly in a database, anyone with database access can read them immediately. This is insecure and should never be done.

This project does not store passwords directly.

### 2.2 Hashing

Hashing is the process of converting input data such as a password into a fixed-length output using a one-way algorithm.

In this project, the algorithm used is SHA-256.

Example idea:

```text
password -> SHA-256 -> hash value
```

This means the password itself is not stored. Instead, only the hash is stored in the database.

### 2.3 Limitation of Hashing Alone

Hashing alone still has weaknesses.

For example:

1. If two users choose the same password, they get the same hash.
2. Weak passwords can still be guessed with dictionary attacks.
3. Attackers can use precomputed hash lists or rainbow tables.

This project explains that limitation in the UI and demonstrates it by allowing a user to register with hash-only mode.

### 2.4 Salting

A salt is a random value generated for each user. The salt is combined with the password before hashing.

Example idea:

```text
salt + password -> SHA-256 -> salted hash
```

This improves security because:

1. Even if two users choose the same password, their stored hash can be different.
2. It makes precomputed attacks much less useful.
3. It adds uniqueness per user.

This project stores the salt in the database together with the salted hash so the system can verify login later.

## 3. Technology Stack

The project uses the following technologies:

1. Python
2. Flask
3. MySQL
4. XAMPP for MySQL service access
5. HTML templates through Jinja2
6. CSS for styling

## 4. Current Folder Structure

The project folder contains the following files and directories:

```text
EX 3/
├── app.py
├── README.md
├── README_DETAILED.md
├── requirements.txt
├── schema.sql
├── static/
│   └── style.css
└── templates/
    ├── base.html
    ├── index.html
    ├── login.html
    └── register.html
```

## 5. What Each File Does

### 5.1 `app.py`

This is the main backend file of the project.

It is responsible for:

1. Starting the Flask application.
2. Reading database configuration.
3. Connecting to MySQL.
4. Creating the database and table if needed.
5. Hashing passwords.
6. Generating salts.
7. Registering users.
8. Verifying logins.
9. Loading data for the dashboard.
10. Deleting users.

### 5.2 `templates/base.html`

This is the shared layout template.

It contains:

1. Common page header.
2. Navigation links.
3. Flash message area.
4. Main content block used by all child templates.

### 5.3 `templates/index.html`

This is the dashboard page.

It shows:

1. What the demo is about.
2. Current database status.
3. Hashing limitations.
4. Why salting improves security.
5. The table of stored users from MySQL.
6. A delete button for each user.

### 5.4 `templates/register.html`

This template is reused for both registration modes:

1. Hash-only registration.
2. Hash-plus-salt registration.

The template changes its explanation text depending on the `mode` variable passed from Flask.

### 5.5 `templates/login.html`

This page lets a user submit username and password for verification.

The backend checks which storage method the user was registered with and validates accordingly.

### 5.6 `static/style.css`

This file styles the pages.

It handles:

1. Layout
2. Typography
3. Buttons
4. Tables
5. Cards
6. Flash messages
7. Responsive behavior

### 5.7 `schema.sql`

This file contains SQL commands to manually create the database and table.

It can be used in phpMyAdmin or the MySQL command line.

### 5.8 `requirements.txt`

This file lists the Python packages required to run the project.

### 5.9 `README.md`

This is the shorter setup guide.

### 5.10 `README_DETAILED.md`

This file is the full detailed documentation for another developer, student, teacher, AI system, or reviewer.

## 6. Full Backend Explanation

This section explains the logic in `app.py` in detail.

### 6.1 Imports

The backend imports:

1. `hashlib` to create SHA-256 hashes.
2. `os` to read environment variables.
3. `secrets` to generate secure random salts.
4. `Flask` and helper functions from `flask`.
5. `mysql.connector` to connect Python to MySQL.
6. `Error` to catch MySQL exceptions.

### 6.2 Flask App Setup

The Flask app is created with:

```python
app = Flask(__name__)
app.config["SECRET_KEY"] = os.getenv("FLASK_SECRET_KEY", "lab-demo-secret-key")
```

The secret key is required for flash messages and session-related Flask features.

### 6.3 Database Configuration

Database configuration is stored in a dictionary called `DB_CONFIG`.

This reads values from environment variables if available, otherwise it uses default values:

1. Host: `127.0.0.1`
2. Port: `3306`
3. User: `root`
4. Password: empty string
5. Database: `security_lab_hashing`

This design makes the app easy to run with default XAMPP settings and also easy to reconfigure later.

### 6.4 Database Connection Function

The function `get_db_connection(include_database=True)` creates a connection object.

It supports two modes:

1. Connection without selecting a database, used when the database may not exist yet.
2. Connection with the database selected, used for normal app operations.

This is important because the app needs to create the database automatically when it does not exist.

### 6.5 Database Initialization

The function `init_database()` does two things:

1. Creates the database if it does not exist.
2. Creates the `users` table if it does not exist.

This prevents the application from failing when run for the first time.

### 6.6 Automatic Database Readiness

The function `ensure_database_ready()` currently just calls `init_database()`.

This means before key operations such as fetch, register, login, and delete, the app makes sure the database and table exist.

This was added after the original error where registration failed with:

```text
Unknown database 'security_lab_hashing'
```

### 6.7 Hashing Function

The function `sha256_hash(password)` takes a string password and returns a SHA-256 hexadecimal digest.

Important detail:

1. The password is encoded as UTF-8 bytes.
2. `hashlib.sha256(...)` processes the bytes.
3. `.hexdigest()` returns a 64-character hexadecimal string.

### 6.8 Salted Hashing Function

The function `hash_with_salt(password, salt)` combines the salt and password using string concatenation:

```python
f"{salt}{password}"
```

That combined string is encoded and hashed using SHA-256.

The salt is generated with:

```python
secrets.token_hex(16)
```

This produces a 32-character hexadecimal string because 16 random bytes become 32 hex characters.

### 6.9 Fetch Users Function

The function `fetch_users()` loads all users from the `users` table.

It returns:

1. `id`
2. `username`
3. `password_hash`
4. `salt`
5. `method`
6. `created_at`

Records are sorted by newest first.

### 6.10 Routes

The Flask application contains the following routes.

#### `/init-db`

Method: `POST`

Purpose:

1. Manually trigger database/table creation.
2. Show success or error flash message.

#### `/`

Method: `GET`

Purpose:

1. Redirect user to the first registration page.
2. Start the demo from hash-only registration.

#### `/dashboard`

Method: `GET`

Purpose:

1. Show educational explanation.
2. Show database status.
3. List users from MySQL.
4. Allow deleting users.

#### `/register/hash`

Methods: `GET`, `POST`

Purpose:

1. Show the hash-only registration form.
2. On submit, validate inputs.
3. Hash the password with SHA-256.
4. Store username, hash, `NULL` salt, and method `hash_only`.

#### `/register/salted`

Methods: `GET`, `POST`

Purpose:

1. Show the salted registration form.
2. On submit, validate inputs.
3. Generate a random salt.
4. Hash the salt + password.
5. Store username, salted hash, salt, and method `hash_with_salt`.

#### `/login`

Methods: `GET`, `POST`

Purpose:

1. Show login form.
2. Read the submitted username.
3. Query the matching user.
4. Check which method was used for storage.
5. Recreate the correct hash from the submitted password.
6. Compare recreated hash with stored hash.
7. Show success or failure message.

#### `/delete/<int:user_id>`

Method: `POST`

Purpose:

1. Delete a selected user from the database.
2. Show feedback with flash messages.

### 6.11 App Startup

At the end of the file:

```python
if __name__ == "__main__":
    init_database()
    app.run(debug=True)
```

This means:

1. When `app.py` is run directly, the database is initialized first.
2. Then Flask starts in debug mode.

## 7. Full Frontend Explanation

### 7.1 Base Template

`base.html` provides the common page shell used by all pages.

It defines:

1. The HTML document structure.
2. The page title.
3. The CSS import.
4. A top navigation bar.
5. A flash-message display section.
6. A Jinja block called `content`.

### 7.2 Dashboard Template

`index.html` is the main explanation and inspection page.

It includes:

1. A project summary card.
2. A database status card.
3. A section about hashing limitations.
4. A section about salting improvements.
5. A database table of current users.

The dashboard is important because it lets the teacher or student directly observe the stored values.

### 7.3 Register Template

`register.html` is reused for two related pages.

If the mode is `hash_only`, the page explains:

1. Password is hashed directly.
2. Same password can produce same hash.

If the mode is `hash_with_salt`, the page explains:

1. A salt is created for the user.
2. Password is combined with salt before hashing.
3. Same password can still produce different stored hashes.

### 7.4 Login Template

`login.html` shows a form for username and password.

After submission, it displays a message showing whether verification succeeded or failed.

### 7.5 CSS Styling

The CSS file creates a simple clean layout.

Design decisions include:

1. Card-based sections for readability.
2. Responsive grid layout.
3. Color-coded success/error states.
4. Monospace formatting for hash and salt columns.
5. Mobile-friendly layout adjustments.

## 8. Database Design

The database name is:

```text
security_lab_hashing
```

The main table is:

```text
users
```

### 8.1 Table Columns

#### `id`

Type: `INT AUTO_INCREMENT PRIMARY KEY`

Purpose:

1. Unique identifier for each user record.

#### `username`

Type: `VARCHAR(100) NOT NULL UNIQUE`

Purpose:

1. Stores the username.
2. Must be unique.

#### `password_hash`

Type: `CHAR(64) NOT NULL`

Purpose:

1. Stores the SHA-256 hexadecimal hash.
2. SHA-256 hex output length is 64 characters.

#### `salt`

Type: `CHAR(32) NULL`

Purpose:

1. Stores the generated salt for salted users.
2. Remains `NULL` for hash-only users.

#### `method`

Type: `ENUM('hash_only', 'hash_with_salt') NOT NULL`

Purpose:

1. Records how the password was stored.
2. Allows login logic to know which verification method to use.

#### `created_at`

Type: `TIMESTAMP DEFAULT CURRENT_TIMESTAMP`

Purpose:

1. Saves when the record was created.

## 9. Request Flow in Practice

### 9.1 Hash-Only Registration Flow

1. User opens `/register/hash`.
2. User enters username and password.
3. Flask receives the form submission.
4. The app checks that both fields are non-empty.
5. `sha256_hash(password)` is called.
6. The generated hash is inserted into MySQL.
7. The `salt` column is saved as `NULL`.
8. The `method` column is saved as `hash_only`.
9. User is redirected to the dashboard.

### 9.2 Salted Registration Flow

1. User opens `/register/salted`.
2. User enters username and password.
3. Flask validates inputs.
4. The app generates a random salt using `secrets.token_hex(16)`.
5. The app calls `hash_with_salt(password, salt)`.
6. The salted hash and salt are stored in MySQL.
7. The `method` column is saved as `hash_with_salt`.
8. User is redirected to the dashboard.

### 9.3 Login Flow

1. User opens `/login`.
2. User submits username and password.
3. Flask loads the matching user row from MySQL.
4. The app reads the `method` field.
5. If method is `hash_only`, it hashes the submitted password directly.
6. If method is `hash_with_salt`, it hashes `salt + password`.
7. It compares the generated hash with the stored hash.
8. It displays success or failure.

### 9.4 Dashboard Flow

1. User opens `/dashboard`.
2. The app ensures the database exists.
3. It fetches all user rows.
4. It renders them in an HTML table.
5. The user can inspect the stored hash and salt values.

## 10. Why This Project Is Useful For a Lab Demo

This project is useful in a software security lab because it makes abstract security concepts visible.

A student can demonstrate:

1. Password is not stored in plain text.
2. Hash values are visible in the database.
3. Salts can also be stored safely in the database.
4. Same-password reuse is easier to observe with hash-only mode.
5. Salting improves resistance to certain attack patterns.

It also connects theory to implementation because the user can:

1. Register through the web app.
2. Open phpMyAdmin.
3. Inspect the exact database table.
4. Compare stored records.

## 11. Important Limitations Of The Current Project

This project is good for learning, but it is still a teaching demo rather than a production-ready authentication system.

Important limitations:

1. It uses plain SHA-256 for demonstration, not a password hashing algorithm like `bcrypt`, `scrypt`, or `Argon2`.
2. There is no session-based logged-in user area.
3. There is no CSRF protection layer added manually.
4. Input validation is minimal.
5. Flash messages may expose raw database errors in development.
6. Debug mode is enabled.

For a real production system, stronger password hashing and a fuller authentication architecture should be used.

## 12. How To Run The Project

### 12.1 Start XAMPP

Start:

1. Apache
2. MySQL

Apache is not required for Flask itself, but XAMPP is being used mainly to provide MySQL and phpMyAdmin.

### 12.2 Install Python Dependencies

Run:

```bash
pip install -r requirements.txt
```

### 12.3 Start the App

Run:

```bash
python app.py
```

### 12.4 Open in Browser

Main start page:

```text
http://127.0.0.1:5000
```

Dashboard:

```text
http://127.0.0.1:5000/dashboard
```

### 12.5 Default Database Configuration

Defaults are:

```text
Host: 127.0.0.1
Port: 3306
User: root
Password: <empty>
Database: security_lab_hashing
```

If a different MySQL username or password is needed, the app supports:

1. `DB_HOST`
2. `DB_PORT`
3. `DB_USER`
4. `DB_PASSWORD`
5. `DB_NAME`
6. `FLASK_SECRET_KEY`

## 13. Manual SQL Setup

The project also includes `schema.sql` for manual setup.

You can run it in phpMyAdmin or MySQL CLI.

The file content is:

```sql
CREATE DATABASE IF NOT EXISTS security_lab_hashing;
USE security_lab_hashing;

CREATE TABLE IF NOT EXISTS users (
    id INT AUTO_INCREMENT PRIMARY KEY,
    username VARCHAR(100) NOT NULL UNIQUE,
    password_hash CHAR(64) NOT NULL,
    salt CHAR(32) NULL,
    method ENUM('hash_only', 'hash_with_salt') NOT NULL,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
);
```

## 14. Exact Source Code Snapshot

This section contains the current source code in the project so another human or AI can reconstruct or review the application easily.

### 14.1 `app.py`

```python
import hashlib
import os
import secrets

from flask import Flask, flash, redirect, render_template, request, url_for
import mysql.connector
from mysql.connector import Error


app = Flask(__name__)
app.config["SECRET_KEY"] = os.getenv("FLASK_SECRET_KEY", "lab-demo-secret-key")

DB_CONFIG = {
    "host": os.getenv("DB_HOST", "127.0.0.1"),
    "port": int(os.getenv("DB_PORT", "3306")),
    "user": os.getenv("DB_USER", "root"),
    "password": os.getenv("DB_PASSWORD", ""),
    "database": os.getenv("DB_NAME", "security_lab_hashing"),
}


def get_db_connection(include_database=True):
    config = {
        "host": DB_CONFIG["host"],
        "port": DB_CONFIG["port"],
        "user": DB_CONFIG["user"],
        "password": DB_CONFIG["password"],
    }
    if include_database:
        config["database"] = DB_CONFIG["database"]
    return mysql.connector.connect(**config)


def init_database():
    setup_connection = get_db_connection(include_database=False)
    setup_cursor = setup_connection.cursor()

    setup_cursor.execute(f"CREATE DATABASE IF NOT EXISTS `{DB_CONFIG['database']}`")
    setup_cursor.close()
    setup_connection.close()

    app_connection = get_db_connection(include_database=True)
    app_cursor = app_connection.cursor()
    app_cursor.execute(
        """
        CREATE TABLE IF NOT EXISTS users (
            id INT AUTO_INCREMENT PRIMARY KEY,
            username VARCHAR(100) NOT NULL UNIQUE,
            password_hash CHAR(64) NOT NULL,
            salt CHAR(32) NULL,
            method ENUM('hash_only', 'hash_with_salt') NOT NULL,
            created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
        )
        """
    )
    app_connection.commit()
    app_cursor.close()
    app_connection.close()


def ensure_database_ready():
    init_database()


def sha256_hash(password):
    return hashlib.sha256(password.encode("utf-8")).hexdigest()


def hash_with_salt(password, salt):
    return hashlib.sha256(f"{salt}{password}".encode("utf-8")).hexdigest()


def fetch_users():
    ensure_database_ready()
    connection = get_db_connection()
    cursor = connection.cursor(dictionary=True)
    cursor.execute(
        """
        SELECT id, username, password_hash, salt, method, created_at
        FROM users
        ORDER BY created_at DESC, id DESC
        """
    )
    users = cursor.fetchall()
    cursor.close()
    connection.close()
    return users


@app.route("/init-db", methods=["POST"])
def init_db_route():
    try:
        init_database()
        flash("Database and users table are ready.", "success")
    except Error as exc:
        flash(f"Database setup failed: {exc}", "error")
    return redirect(url_for("index"))


@app.route("/")
def home():
    return redirect(url_for("register_hash"))


@app.route("/dashboard")
def index():
    users = []
    db_error = None
    try:
        users = fetch_users()
    except Error as exc:
        db_error = str(exc)
    return render_template(
        "index.html", users=users, db_error=db_error, db_name=DB_CONFIG["database"]
    )


@app.route("/register/hash", methods=["GET", "POST"])
def register_hash():
    if request.method == "POST":
        username = request.form.get("username", "").strip()
        password = request.form.get("password", "")

        if not username or not password:
            flash("Username and password are required.", "error")
            return redirect(url_for("register_hash"))

        password_hash = sha256_hash(password)

        try:
            ensure_database_ready()
            connection = get_db_connection()
            cursor = connection.cursor()
            cursor.execute(
                """
                INSERT INTO users (username, password_hash, salt, method)
                VALUES (%s, %s, NULL, 'hash_only')
                """,
                (username, password_hash),
            )
            connection.commit()
            cursor.close()
            connection.close()
            flash("User registered with hashing only.", "success")
            return redirect(url_for("index"))
        except Error as exc:
            flash(f"Registration failed: {exc}", "error")

    return render_template("register.html", mode="hash_only")


@app.route("/register/salted", methods=["GET", "POST"])
def register_salted():
    if request.method == "POST":
        username = request.form.get("username", "").strip()
        password = request.form.get("password", "")

        if not username or not password:
            flash("Username and password are required.", "error")
            return redirect(url_for("register_salted"))

        salt = secrets.token_hex(16)
        password_hash = hash_with_salt(password, salt)

        try:
            ensure_database_ready()
            connection = get_db_connection()
            cursor = connection.cursor()
            cursor.execute(
                """
                INSERT INTO users (username, password_hash, salt, method)
                VALUES (%s, %s, %s, 'hash_with_salt')
                """,
                (username, password_hash, salt),
            )
            connection.commit()
            cursor.close()
            connection.close()
            flash("User registered with hashing + salt.", "success")
            return redirect(url_for("index"))
        except Error as exc:
            flash(f"Registration failed: {exc}", "error")

    return render_template("register.html", mode="hash_with_salt")


@app.route("/login", methods=["GET", "POST"])
def login():
    login_result = None

    if request.method == "POST":
        username = request.form.get("username", "").strip()
        password = request.form.get("password", "")

        if not username or not password:
            flash("Username and password are required.", "error")
            return redirect(url_for("login"))

        try:
            ensure_database_ready()
            connection = get_db_connection()
            cursor = connection.cursor(dictionary=True)
            cursor.execute(
                """
                SELECT username, password_hash, salt, method
                FROM users
                WHERE username = %s
                """,
                (username,),
            )
            user = cursor.fetchone()
            cursor.close()
            connection.close()

            if not user:
                login_result = {"status": "error", "message": "User not found."}
            else:
                if user["method"] == "hash_only":
                    attempted_hash = sha256_hash(password)
                    method_label = "hashing only"
                else:
                    attempted_hash = hash_with_salt(password, user["salt"])
                    method_label = "hashing + salt"

                if attempted_hash == user["password_hash"]:
                    login_result = {
                        "status": "success",
                        "message": f"Login success using {method_label} verification.",
                    }
                else:
                    login_result = {
                        "status": "error",
                        "message": f"Login failed. The stored {method_label} value did not match.",
                    }
        except Error as exc:
            login_result = {"status": "error", "message": f"Login failed: {exc}"}

    return render_template("login.html", login_result=login_result)


@app.route("/delete/<int:user_id>", methods=["POST"])
def delete_user(user_id):
    try:
        ensure_database_ready()
        connection = get_db_connection()
        cursor = connection.cursor()
        cursor.execute("DELETE FROM users WHERE id = %s", (user_id,))
        connection.commit()
        deleted = cursor.rowcount
        cursor.close()
        connection.close()

        if deleted:
            flash("User deleted from the database.", "success")
        else:
            flash("User not found.", "error")
    except Error as exc:
        flash(f"Delete failed: {exc}", "error")

    return redirect(url_for("index"))


if __name__ == "__main__":
    init_database()
    app.run(debug=True)
```

### 14.2 `templates/base.html`

```html
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Hashing and Salting Demo</title>
    <link rel="stylesheet" href="{{ url_for('static', filename='style.css') }}">
</head>
<body>
    <header class="site-header">
        <div>
            <h1>Hashing and Salting Demo</h1>
            <p>Software Security Lab mini project using Python, Flask, and MySQL.</p>
        </div>
        <nav>
            <a href="{{ url_for('register_hash') }}">Start Demo</a>
            <a href="{{ url_for('index') }}">Dashboard</a>
            <a href="{{ url_for('register_hash') }}">Register Hash</a>
            <a href="{{ url_for('register_salted') }}">Register Salted</a>
            <a href="{{ url_for('login') }}">Login</a>
        </nav>
    </header>

    <main class="container">
        {% with messages = get_flashed_messages(with_categories=true) %}
            {% if messages %}
                {% for category, message in messages %}
                    <div class="flash {{ category }}">{{ message }}</div>
                {% endfor %}
            {% endif %}
        {% endwith %}

        {% block content %}{% endblock %}
    </main>
</body>
</html>
```

### 14.3 `templates/index.html`

```html
{% extends "base.html" %}

{% block content %}
<section class="hero-grid">
    <div class="card">
        <h2>What this demo shows</h2>
        <p>First, a user can register with simple SHA-256 hashing only. In the database, the original password is not stored, only the hash value is stored.</p>
        <p>Then, another user can register with SHA-256 plus a unique salt. In that case, the database stores both the salt and the salted hash, which is safer because two users with the same password will still get different stored values.</p>
    </div>

    <div class="card accent">
        <h2>Database status</h2>
        <p>Configured database: <strong>{{ db_name }}</strong></p>
        {% if db_error %}
            <p class="warning">Current database error: {{ db_error }}</p>
            <form method="post" action="{{ url_for('init_db_route') }}">
                <button type="submit">Initialize Database</button>
            </form>
        {% else %}
            <p class="success-text">Database connection is working.</p>
            <form method="post" action="{{ url_for('init_db_route') }}">
                <button type="submit">Re-run Database Setup</button>
            </form>
        {% endif %}
    </div>
</section>

<section class="two-column">
    <div class="card">
        <h2>Hashing limitations</h2>
        <ul>
            <li>If two users choose the same password, plain hashing stores the same hash for both users.</li>
            <li>Attackers can use precomputed tables or common password lists to guess weak hashes.</li>
            <li>Hashing alone hides the password, but it does not add uniqueness per user.</li>
        </ul>
    </div>

    <div class="card">
        <h2>Why salting improves security</h2>
        <ul>
            <li>Each user gets a different random salt.</li>
            <li>The same password creates a different stored hash for different users.</li>
            <li>Salt makes reused passwords less obvious in the database.</li>
        </ul>
    </div>
</section>

<section class="card">
    <h2>Registered Users In MySQL</h2>
    {% if db_error %}
        <p>Create the database first, then register users.</p>
    {% elif users %}
        <div class="table-wrap">
            <table>
                <thead>
                    <tr>
                        <th>ID</th>
                        <th>Username</th>
                        <th>Method</th>
                        <th>Password Hash</th>
                        <th>Salt</th>
                        <th>Created</th>
                        <th>Action</th>
                    </tr>
                </thead>
                <tbody>
                    {% for user in users %}
                        <tr>
                            <td>{{ user.id }}</td>
                            <td>{{ user.username }}</td>
                            <td>{{ user.method }}</td>
                            <td class="mono">{{ user.password_hash }}</td>
                            <td class="mono">{{ user.salt or 'NULL' }}</td>
                            <td>{{ user.created_at }}</td>
                            <td>
                                <form method="post" action="{{ url_for('delete_user', user_id=user.id) }}">
                                    <button type="submit" class="danger">Delete</button>
                                </form>
                            </td>
                        </tr>
                    {% endfor %}
                </tbody>
            </table>
        </div>
    {% else %}
        <p>No users yet. Use the register pages to add test users.</p>
    {% endif %}
</section>
{% endblock %}
```

### 14.4 `templates/register.html`

```html
{% extends "base.html" %}

{% block content %}
<section class="form-card">
    {% if mode == 'hash_only' %}
        <h2>Register With Hashing Only</h2>
        <p>The password is converted to a SHA-256 hash and the hash is stored in MySQL.</p>
        <p class="note">If two users choose the same password, they will get the same stored hash.</p>
    {% else %}
        <h2>Register With Hashing + Salt</h2>
        <p>A random salt is generated for the user, then SHA-256 is applied to the salt and password together.</p>
        <p class="note">Even if two users choose the same password, the stored hashes will be different because the salts are different.</p>
    {% endif %}

    <form method="post" class="stack-form">
        <label for="username">Username</label>
        <input id="username" name="username" type="text" required>

        <label for="password">Password</label>
        <input id="password" name="password" type="password" required>

        <button type="submit">Create User</button>
    </form>
    <p><a href="{{ url_for('index') }}">Open dashboard</a></p>
</section>
{% endblock %}
```

### 14.5 `templates/login.html`

```html
{% extends "base.html" %}

{% block content %}
<section class="form-card">
    <h2>Login Verification</h2>
    <p>Enter a username and password. The app reads the user's method from MySQL and verifies the password using either plain hashing or salted hashing.</p>

    <form method="post" class="stack-form">
        <label for="username">Username</label>
        <input id="username" name="username" type="text" required>

        <label for="password">Password</label>
        <input id="password" name="password" type="password" required>

        <button type="submit">Check Login</button>
    </form>

    {% if login_result %}
        <div class="result {{ login_result.status }}">
            {{ login_result.message }}
        </div>
    {% endif %}
</section>
{% endblock %}
```

### 14.6 `static/style.css`

```css
body {
    margin: 0;
    font-family: Arial, sans-serif;
    background: #f4f7fb;
    color: #1d2433;
}

a {
    color: #0a58ca;
    text-decoration: none;
}

button {
    border: 0;
    border-radius: 8px;
    padding: 0.8rem 1rem;
    background: #1248c7;
    color: #fff;
    cursor: pointer;
    font-weight: 700;
}

button.danger {
    background: #bf2f45;
}

input {
    border: 1px solid #c9d3e5;
    border-radius: 8px;
    padding: 0.8rem;
    font-size: 1rem;
}

table {
    width: 100%;
    border-collapse: collapse;
}

th,
td {
    padding: 0.8rem;
    border-bottom: 1px solid #dde5f0;
    text-align: left;
    vertical-align: top;
}

.site-header {
    background: linear-gradient(120deg, #0e2a6d, #1248c7);
    color: #fff;
    padding: 1.5rem 2rem;
    display: flex;
    gap: 1rem;
    justify-content: space-between;
    align-items: center;
    flex-wrap: wrap;
}

.site-header nav {
    display: flex;
    gap: 1rem;
    flex-wrap: wrap;
}

.site-header nav a {
    color: #fff;
    font-weight: 700;
}

.container {
    max-width: 1150px;
    margin: 0 auto;
    padding: 1.5rem;
}

.hero-grid,
.two-column {
    display: grid;
    grid-template-columns: repeat(auto-fit, minmax(280px, 1fr));
    gap: 1rem;
    margin-bottom: 1rem;
}

.card,
.form-card {
    background: #fff;
    border-radius: 16px;
    padding: 1.25rem;
    box-shadow: 0 12px 30px rgba(18, 72, 199, 0.08);
}

.accent {
    background: #eef4ff;
}

.stack-form {
    display: grid;
    gap: 0.75rem;
    max-width: 460px;
}

.flash,
.result {
    padding: 0.9rem 1rem;
    border-radius: 10px;
    margin-bottom: 1rem;
}

.flash.success,
.result.success,
.success-text {
    background: #e6f7ea;
    color: #186a31;
}

.flash.error,
.result.error,
.warning {
    background: #fdeced;
    color: #992337;
}

.note {
    background: #fff8df;
    border-left: 4px solid #d7a300;
    padding: 0.75rem;
}

.table-wrap {
    overflow-x: auto;
}

.mono {
    font-family: "Courier New", monospace;
    word-break: break-all;
}

@media (max-width: 700px) {
    .site-header {
        padding: 1.25rem;
    }

    th,
    td {
        font-size: 0.9rem;
    }
}
```

### 14.7 `schema.sql`

```sql
CREATE DATABASE IF NOT EXISTS security_lab_hashing;
USE security_lab_hashing;

CREATE TABLE IF NOT EXISTS users (
    id INT AUTO_INCREMENT PRIMARY KEY,
    username VARCHAR(100) NOT NULL UNIQUE,
    password_hash CHAR(64) NOT NULL,
    salt CHAR(32) NULL,
    method ENUM('hash_only', 'hash_with_salt') NOT NULL,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
);
```

### 14.8 `requirements.txt`

```text
Flask==3.0.3
mysql-connector-python==9.0.0
```

## 15. Summary Of What Was Built

This project currently implements:

1. A Flask web app for a software security lab.
2. MySQL integration through XAMPP.
3. Automatic database and table creation.
4. Registration with simple hashing.
5. Registration with hashing plus salt.
6. Login verification based on stored method.
7. A dashboard to inspect hashes and salts in MySQL.
8. A delete function to remove rows.
9. A short README and a full detailed README.

## 16. Suggested Future Enhancements

If this project is extended later, useful next steps could be:

1. Add a side-by-side comparison page for same-password examples.
2. Add screenshots to documentation.
3. Add a route showing intermediate demo values safely for teaching.
4. Replace SHA-256 with `bcrypt` or `argon2` in an advanced version.
5. Add test files.
6. Add better form validation.
7. Add a real login session after verification.

## 17. Final Note

This document is intentionally detailed so that another human or AI system can understand:

1. Why the project exists.
2. How the project is structured.
3. How the backend works.
4. How the frontend works.
5. How the database works.
6. What exact code is present.
7. How to run and explain the project.

This makes it suitable as:

1. A handoff document.
2. A lab report support file.
3. A revision document.
4. A base for future extension.
