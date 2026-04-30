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
