username = "admin"
password = "kali123"

print("=== Login System ===")

user = input("Username: ")
pwd = input("Password: ")

if user == username and pwd == password:
    print("Login Successful!")
else:
    print("Access Denied!")

# use cat "filename" to get the username and the password
# use input as: 
#     Username: admin
#     Password: kali123
#     Login Successful!