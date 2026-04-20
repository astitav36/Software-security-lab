import hashlib
import os

# storing user data
database = {}

# registration
username = input("Create username: ")
password = input("Create password: ")

salt = os.urandom(16).hex()

hashed_password = hashlib.sha256((password + salt).encode()).hexdigest()

database[username] = (hashed_password, salt)

print("\nRegistration successful")
print("Salt:", salt)
print("Stored Hash:", hashed_password)

# login
print("\nLogin Now")
login_user = input("Enter username: ")
login_pass = input("Enter password: ")

if login_user in database:
    stored_hash, stored_salt = database[login_user]

    login_hash = hashlib.sha256((login_pass + stored_salt).encode()).hexdigest()

    if login_hash == stored_hash:
        print("Login Successful")
    else:
        print("Wrong Password")
else:
    print("User not found")