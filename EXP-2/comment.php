<?php
include "db.php";

if(isset($_POST['msg'])){
    $msg = $_POST['msg'];

    // ❌ VULNERABLE (no sanitization)
    $query = "INSERT INTO comments(message) VALUES('$msg')";
    mysqli_query($conn,$query);
}
?>

<!DOCTYPE html>
<html>
<head>
<title>Bebop Chat Terminal</title>

<style>
body{
    background:black;
    color:#00ffd0;
    font-family:Courier New;
    padding:30px;
}
input,textarea{
    width:100%;
    background:black;
    color:#00ffd0;
    border:1px solid #00ffd0;
    padding:10px;
}
button{
    background:#ffcc00;
    padding:10px;
    border:none;
}
</style>

</head>
<body>

<h2>BEBOP MESSAGE TERMINAL</h2>

<form method="POST">
<textarea name="msg" placeholder="Enter message"></textarea><br><br>
<button type="submit">SEND</button>
</form>

<hr>

<h3>MESSAGES:</h3>

<?php

// ❌ VULNERABLE OUTPUT
$result = mysqli_query($conn,"SELECT * FROM comments");

while($row = mysqli_fetch_assoc($result)){
    echo $row['message']."<br>";
}

?>

</body>
</html>