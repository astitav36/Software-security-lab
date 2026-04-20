<?php

include "db.php";

$username = $_POST['username'];
$password = $_POST['password'];

$query = "SELECT * FROM users WHERE username='$username' AND password='$password'";

echo $query;   // DEBUG LINE

$result = mysqli_query($conn,$query);

if($result && mysqli_num_rows($result) > 0){
    echo "<h1>ACCESS GRANTED</h1>";
}
else{
    echo "<h1>ACCESS DENIED</h1>";
}

?>