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
            $message = "Registrasi berhasil! Silakan login.";
        } else {
            $message = "Username sudah digunakan!";
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
        $message = "Username atau Password salah!";
    }
}
?>

<!DOCTYPE html>
<html>
<head>
    <title>Cyber Login</title>
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;600&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="login.css">

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
<h2> Selamat Datang</h2>
<p class="msg"><?= $message ?></p>

<form method="POST" id="loginForm">
    <input type="hidden" name="action" value="login">
    <h3>Login</h3>
    <input type="text" name="username" placeholder="Username" required>
    <input type="password" name="password" placeholder="Password" required>
    <button type="submit">Masuk</button>
    <p style="text-align:center;">Belum punya akun? <a onclick="toggleForm()">Daftar</a></p>
</form>

<form method="POST" id="registerForm" class="hidden">
    <input type="hidden" name="action" value="register">
    <h3>Buat Akun</h3>
    <input type="text" name="username" placeholder="Username Baru" required>
    <input type="password" name="password" placeholder="Password Baru" required>
    <button type="submit">Daftar Akun</button>
    <p style="text-align:center;">Sudah punya akun? <a onclick="toggleForm()">Login</a></p>
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
            <p>Akses fitur hanya dengan login atau buat akun baru</p>
        </div>
</div>
</div>
</body>
</html>