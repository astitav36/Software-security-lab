<?php
session_start();

// Hide errors in production
error_reporting(0);

$conn = mysqli_connect("localhost", "root", "", "secure_login");
$message = "";

// CSRF Token generate
if(empty($_SESSION['csrf_token'])){
    $_SESSION['csrf_token'] = bin2hex(random_bytes(32));
}

// Brute force protection
if(!isset($_SESSION['attempts'])){
    $_SESSION['attempts'] = 0;
}

if (isset($_POST['submit'])) {

    // CSRF check
   if(!isset($_POST['csrf_token']) || $_POST['csrf_token'] !== $_SESSION['csrf_token']){
    die("CSRF attack detected");
}

    if($_SESSION['attempts'] >= 5){
        die("Too many attempts. Try later.");
    }

    $username = trim($_POST['username']);
    $password_input = $_POST['password'];

    // Input validation
    if(strlen($username) < 3 || strlen($password_input) < 6){
        $message = "INVALID INPUT ❌";
    } else {

        // FETCH USER
        $stmt = mysqli_prepare($conn, "SELECT password FROM users WHERE username = ?");
        mysqli_stmt_bind_param($stmt, "s", $username);
        mysqli_stmt_execute($stmt);
        $result = mysqli_stmt_get_result($stmt);

        if ($user = mysqli_fetch_assoc($result)) {

            // VERIFY PASSWORD (bcrypt)
            if (password_verify($password_input, $user['password'])) {

                session_regenerate_id(true);
                $_SESSION['user'] = $username;
                $_SESSION['attempts'] = 0;

                $message = "ACCESS GRANTED 🔓";

            } else {
                $_SESSION['attempts']++;
                $message = "INVALID CREDENTIALS ❌";
            }

        } else {
            $_SESSION['attempts']++;
            $message = "INVALID CREDENTIALS ❌";
        }

        mysqli_stmt_close($stmt);
    }

    // Logging
    file_put_contents("log.txt",
        "Login: $username | Time: ".date("Y-m-d H:i:s")."\n",
        FILE_APPEND);
}
?>
<!DOCTYPE html>
<html>-
<head>
<title>AAA Hacker Terminal</title>
<style>
*{margin:0;padding:0;box-sizing:border-box;}

body{
    height:100vh;
    overflow:hidden;
    background:black;
    font-family:Courier New, monospace;
    color:#00ff00;
}

/* Matrix Canvas */
canvas{
    position:fixed;
    top:0;
    left:0;
    z-index:-3;
}

/* Fake hacker silhouette */
.hacker{
    position:fixed;
    bottom:0;
    left:50%;
    transform:translateX(-50%);
    font-size:140px;
    opacity:0.05;
    animation:glow 2s infinite alternate;
    z-index:-2;
}

@keyframes glow{
    from{opacity:0.03;}
    to{opacity:0.08;}
}

/* Floating fake code windows */
.window{
    position:absolute;
    width:200px;
    height:120px;
    background:rgba(0,0,0,0.7);
    border:1px solid #00ff00;
    font-size:11px;
    padding:5px;
    animation:move 12s linear infinite;
}

@keyframes move{
    0%{transform:translateY(0);}
    50%{transform:translateY(-20px);}
    100%{transform:translateY(0);}
}

/* Login Card */
.card{
    position:absolute;
    top:50%;
    left:50%;
    transform:translate(-50%,-50%);
    width:380px;
    padding:40px;
    background:rgba(0,0,0,0.9);
    border-radius:15px;
    box-shadow:0 0 25px #00ff00;
    text-align:center;
}

input{
    width:100%;
    padding:10px;
    margin:10px 0;
    border:1px solid #00ff00;
    background:black;
    color:#00ff00;
}

input[type="submit"]:hover{
    background:#00ff00;
    color:black;
}

.message{
    margin-top:15px;
    font-weight:bold;
}
</style>
</head>
<body>

<canvas id="matrix"></canvas>

<div class="hacker">🧑‍💻</div>

<div class="window" style="top:10%; left:5%;">Initializing payload...<br>Encrypting data...<br>Accessing root...</div>
<div class="window" style="top:20%; right:5%;">Bypassing firewall...<br>Scanning ports...<br>Injection ready...</div>
<div class="window" style="bottom:20%; left:10%;">Decrypting hashes...<br>Admin found...<br>Privilege escalation...</div>

<div class="card">
    <h2>AAA Secure Terminal</h2>

    <form method="POST">
        <input type="text" name="username" placeholder="Username" required>
        <input type="password" name="password" placeholder="Password" required>
        <input type="hidden" name="csrf_token" value="<?php echo $_SESSION['csrf_token']; ?>">

        <input type="submit" name="submit" value="EXECUTE ACCESS">
    </form>

    <?php
    if($message != ""){
        echo "<div class='message'>".htmlspecialchars($message)."</div>";
    }
    ?>
</div>

<script>
// MATRIX EFFECT
const canvas = document.getElementById("matrix");
const ctx = canvas.getContext("2d");
canvas.height = window.innerHeight;
canvas.width = window.innerWidth;

const letters = "01ABCDEFGHIJKLMNOPQRSTUVWXYZ";
const fontSize = 14;
const columns = canvas.width / fontSize;
const drops = [];

for(let x=0;x<columns;x++) drops[x]=1;

function draw(){
    ctx.fillStyle="rgba(0,0,0,0.05)";
    ctx.fillRect(0,0,canvas.width,canvas.height);
    ctx.fillStyle="#00ff00";
    ctx.font=fontSize+"px monospace";

    for(let i=0;i<drops.length;i++){
        const text=letters.charAt(Math.floor(Math.random()*letters.length));
        ctx.fillText(text,i*fontSize,drops[i]*fontSize);

        if(drops[i]*fontSize>canvas.height && Math.random()>0.975)
            drops[i]=0;
        drops[i]++;
    }
}
setInterval(draw,35);
</script>

</body>
</html>
