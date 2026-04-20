<!DOCTYPE html>
<html>
<head>
<title>Bebop Terminal</title>

<style>

body{
margin:0;
background:black;
font-family:Courier New;
color:#00ffd0;
display:flex;
justify-content:center;
align-items:center;
height:100vh;
}

.container{
background:rgba(0,0,0,0.9);
padding:40px;
border:2px solid #00ffd0;
box-shadow:0 0 20px #00ffd0;
text-align:center;
width:350px;
}

h1{
color:#ffcc00;
letter-spacing:3px;
}

input{
width:100%;
padding:12px;
margin:10px 0;
background:black;
border:1px solid #00ffd0;
color:#00ffd0;
}

button{
width:100%;
padding:12px;
background:#ffcc00;
border:none;
font-weight:bold;
cursor:pointer;
}

button:hover{
background:#00ffd0;
}

</style>
</head>

<body>

<div class="container">

<h1>BEBOP TERMINAL</h1>

<form action="login.php" method="POST">

<input type="text" name="username" placeholder="USERNAME">

<input type="password" name="password" placeholder="PASSWORD">

<button type="submit">ACCESS SYSTEM</button>

</form>

</div>

</body>
</html>