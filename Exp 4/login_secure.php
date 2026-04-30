<?php
include "db_connect.php";

$message = "";

if(isset($_POST['login'])){

    $username = $_POST['username'];
    $password = $_POST['password'];

    // Prepared statement to prevent SQL Injection
    $stmt = $conn->prepare("SELECT * FROM users WHERE username = ? AND password = ?");

    $stmt->bind_param("ss", $username, $password);

    $stmt->execute();

    $result = $stmt->get_result();

    if($result->num_rows > 0){
        $message = "Login Successful";
    }
    else{
        $message = "Invalid Username or Password";
    }

    $stmt->close();
}

?>

<!DOCTYPE html>
<html>
<head>
<title>Secure Login Page</title>
</head>

<body>

<h2>Secure Login Form</h2>

<?php
if($message != ""){
    echo "<h3>$message</h3>";
}
?>

<form method="POST">

<label>Username:</label><br>
<input type="text" name="username" required><br><br>

<label>Password:</label><br>
<input type="password" name="password" required><br><br>

<input type="submit" name="login" value="Login">

</form>

</body>
</html>
