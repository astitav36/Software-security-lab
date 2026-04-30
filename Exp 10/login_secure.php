<?php
include "db_connect.php";

$message = "";

// Function to get client IP
function getUserIP() {
    if (!empty($_SERVER['HTTP_CLIENT_IP'])) {
        return $_SERVER['HTTP_CLIENT_IP'];
    } elseif (!empty($_SERVER['HTTP_X_FORWARDED_FOR'])) {
        return $_SERVER['HTTP_X_FORWARDED_FOR'];
    } else {
        return $_SERVER['REMOTE_ADDR'];
    }
}

// Function to detect anomaly
function detectAnomaly($username, $password) {
    $anomaly = [];

    // Simple checks
    if (strlen($password) < 4) {
        $anomaly[] = "Weak Password Attempt";
    }

    if (preg_match("/('|--|#|=)/", $username)) {
        $anomaly[] = "SQL Injection Pattern";
    }

    if (preg_match("/<script>/i", $username)) {
        $anomaly[] = "XSS Attempt";
    }

    return empty($anomaly) ? "None" : implode(", ", $anomaly);
}

// Function to write log
function writeLog($username, $status, $anomaly) {
    $logFile = "logs.txt";

    $timestamp = date("Y-m-d H:i:s");
    $ip = getUserIP();
    $userAgent = $_SERVER['HTTP_USER_AGENT'];
    $method = $_SERVER['REQUEST_METHOD'];

    $logEntry = "[$timestamp] | USER: $username | IP: $ip | STATUS: $status | METHOD: $method | AGENT: $userAgent | ANOMALY: $anomaly" . PHP_EOL;

    file_put_contents($logFile, $logEntry, FILE_APPEND);
}

if(isset($_POST['login'])){

    $username = $_POST['username'];
    $password = $_POST['password'];

    $anomaly = detectAnomaly($username, $password);

    // Secure query
    $stmt = $conn->prepare("SELECT * FROM users WHERE username = ? AND password = ?");
    $stmt->bind_param("ss", $username, $password);
    $stmt->execute();
    $result = $stmt->get_result();

    if($result->num_rows > 0){
        $message = "Login Successful";
        writeLog($username, "SUCCESS", $anomaly);
    }
    else{
        $message = "Invalid Username or Password";
        writeLog($username, "FAILED", $anomaly);
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
