<?php
$conn = mysqli_connect("localhost","root","","secure_lab");

$username = $_POST['username'];
$password = $_POST['password'];

$stmt = $conn->prepare("SELECT * FROM users WHERE username=?");
$stmt->bind_param("s",$username);
$stmt->execute();

$result = $stmt->get_result();

if($row = $result->fetch_assoc()){
    if(password_verify($password, $row['password'])){
        echo "Login Successful (Secure)";
    } else {
        echo "Invalid Password";
    }
} else {
    echo "User Not Found";
}
?>