# Hashing and Salting Demo

This is a Python Flask web app for a software security lab. It demonstrates:

- Registration using hashing only
- Registration using hashing with a unique salt
- Login verification for both methods
- Viewing MySQL records to show that the real password is never stored
- Deleting users from the database

## Technology

- Python
- Flask
- MySQL from XAMPP

## Database setup

Start Apache and MySQL in XAMPP first.

Default MySQL settings used by the app:

- Host: `127.0.0.1`
- Port: `3306`
- User: `root`
- Password: empty
- Database: `security_lab_hashing`

You can change these with environment variables:

- `DB_HOST`
- `DB_PORT`
- `DB_USER`
- `DB_PASSWORD`
- `DB_NAME`
- `FLASK_SECRET_KEY`

You can either:

1. Open the app and click `Initialize Database`
2. Or import/run `schema.sql` in phpMyAdmin

## Run the app

```bash
pip install -r requirements.txt
python app.py
```

Then open:

```text
http://127.0.0.1:5000
```

The app opens on the first registration page. The MySQL view and explanation dashboard is at:

```text
http://127.0.0.1:5000/dashboard
```

## Demo flow for lab

1. Initialize the database.
2. Register one user with `Register Hash`.
3. Show in MySQL that only `password_hash` is stored.
4. Explain that if two users use the same password, hash-only mode can produce the same stored hash.
5. Register another user with `Register Salted`.
6. Show in MySQL that both `salt` and `password_hash` are stored.
7. Explain that the salt improves security because the same password can lead to different hashes for different users.
