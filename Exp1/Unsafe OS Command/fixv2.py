import subprocess

user = input("Enter your name: ")

subprocess.run(["echo", "Hello", user])

# now it will not allow any special characters to be executed as commands.