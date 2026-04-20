<?php

$host = "localhost";
$user = "root";
$password = "";
$db = "sqli_lab";

$conn = mysqli_connect("localhost","root","","sqli_lab");

if(!$conn){
die("Database connection failed");
}

?>