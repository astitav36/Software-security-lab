<?php
include "db_connect.php";

if(isset($_POST['login'])){

    $username = $_POST['username'];
    $password = $_POST['password'];

    $sql = "SELECT * FROM users WHERE username='$username' AND password='$password'";

    $result = mysqli_query($conn, $sql);

    if(mysqli_num_rows($result) > 0){
        echo "<h3>Login Successful</h3>";
    }
    else{
        echo "<h3>Invalid Username or Password</h3>";
    }

}
?>

<!DOCTYPE html>
<html>
<head>
<title>Login Page</title>
</head>

<body>

<h2>Login Form</h2>

<form method="POST">

Username:<br>
<input type="text" name="username"><br><br>

Password:<br>
<input type="password" name="password"><br><br>

<input type="submit" name="login" value="Login">

</form>

</body>
</html>
