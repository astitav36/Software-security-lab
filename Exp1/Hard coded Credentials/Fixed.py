import os

print("=== Secure Login (Env) ===")

stored_user = os.getenv("APP_USER")
stored_pass = os.getenv("APP_PASS")

user = input("Username: ")
pwd = input("Password: ")

if user == stored_user and pwd == stored_pass:
    print("Login Successful!")
else:
    print("Access Denied!")

# use this to store the username and the password and then use the user input
# export APP_USER=admin
# export APP_PASS=kali123