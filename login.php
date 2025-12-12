<?php
session_start();
$host = "localhost"; $user = "root"; $pass = ""; $db = "db_alat_online";
$conn = new mysqli($host, $user, $pass, $db);

if(isset($_SESSION['user_id'])) {
    header("Location: index.php");
    exit;
}

$message = "";

if($_SERVER["REQUEST_METHOD"] == "POST") {
    $u = $_POST["username"];
    $p = $_POST["password"];
    $action = $_POST["action"];

    if($action == "register") {
        $hashed = password_hash($p, PASSWORD_DEFAULT);
        $stmt = $conn->prepare("INSERT INTO users(username,password) VALUES(?,?)");
        $stmt->bind_param("ss", $u, $hashed);

        if($stmt->execute()) {
            $message = "Registration successful! Please login.";
        } else {
            $message = "Username already taken!";
        }
    }

    if($action == "login") {
        $stmt = $conn->prepare("SELECT id,password FROM users WHERE username=?");
        $stmt->bind_param("s", $u);
        $stmt->execute();
        $result = $stmt->get_result();

        if($row = $result->fetch_assoc()) {
            if(password_verify($p, $row['password'])) {
                $_SESSION['user_id'] = $row['id'];
                $_SESSION['username'] = $u;
                header("Location: index.php");
                exit;
            }
        }
        $message = "Invalid Username or Password!";
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <title>Cyber Login</title>
    <link href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@300;400;600;700&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="login.css">
    <style>
        body { font-family: 'Plus Jakarta Sans', sans-serif; }
    </style>
<script>
function toggleForm() {
    document.getElementById("loginForm").classList.toggle("hidden");
    document.getElementById("registerForm").classList.toggle("hidden");
}
</script>
</head>

<body>

<div class="overlay"></div>

<div class="glow"></div>

<div class="container">

<div class="form-container">
<h2>Welcome</h2>
<p class="msg"><?= $message ?></p>

<form method="POST" id="loginForm">
    <input type="hidden" name="action" value="login">
    <h3>Login</h3>
    <input type="text" name="username" placeholder="Username" required>
    <input type="password" name="password" placeholder="Password" required>
    <button type="submit">Sign In</button>
    <p style="text-align:center;">Don't have an account? <a onclick="toggleForm()">Sign Up</a></p>
</form>

<form method="POST" id="registerForm" class="hidden">
    <input type="hidden" name="action" value="register">
    <h3>Create Account</h3>
    <input type="text" name="username" placeholder="New Username" required>
    <input type="password" name="password" placeholder="New Password" required>
    <button type="submit">Register</button>
    <p style="text-align:center;">Already have an account? <a onclick="toggleForm()">Login</a></p>
</form>
</div>

<div class="right-box">
    <video autoplay muted loop id="right-box-video">
        <source src="rig_box.mp4" type="video/mp4">
        Your browser does not support the video tag.
    </video>
    <div class="overlay"></div> 
        
        <div class="right-box-content">
            <h1>Login System</h1>
            <p>Access features by logging in or creating a new account</p>
        </div>
</div>
</div>
</body>
</html>