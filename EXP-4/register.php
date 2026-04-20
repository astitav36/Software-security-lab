<?php
$conn = mysqli_connect("localhost","root","","secure_lab");

$username = "admin";
$password = password_hash("1234", PASSWORD_BCRYPT);

$query = "INSERT INTO users(username,password) VALUES('$username','$password')";
mysqli_query($conn,$query);

echo "User registered securely";
?>