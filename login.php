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

    <style>
        body {
            font-family: "Poppins", sans-serif;
            background: #0A0F1F;
            height: 100vh;
            display: flex;
            justify-content: center;
            align-items: center;
            color: white;
            overflow: hidden;
        }

        /* Cyber gradient glow effect */
        .glow {
            position: absolute;
            width: 450px;
            height: 450px;
            background: radial-gradient(circle, #0099ff55, transparent 70%);
            border-radius: 50%;
            filter: blur(90px);
            animation: glow 6s infinite alternate;
        }

        @keyframes glow {
            from { transform: translate(-50px, -60px); }
            to { transform: translate(50px, 60px); }
        }

        .container {
            width: 850px;
            height: 520px;
            background: rgba(255,255,255,0.07);
            backdrop-filter: blur(12px);
            border-radius: 20px;
            display: flex;
            overflow: hidden;
            border: 1px solid #00E5FF55;
            box-shadow: 0px 0px 25px #00E5FF33;
        }

        .form-container {
            width: 50%;
            padding: 50px;
        }

        .right-box {
            width: 50%;
            background: linear-gradient(135deg, #0039A6, #001E4D);
            display: flex;
            justify-content: center;
            flex-direction: column;
            align-items: center;
            text-align: center;
            border-left: 1px solid #00E5FF55;
        }

        input {
            width: 100%;
            padding: 12px;
            border-radius: 8px;
            background: rgba(255,255,255,0.1);
            border: 1px solid #00E5FF55;
            color: white;
            outline: none;
            margin-bottom: 18px;
        }

        input:focus {
            border-color: #00E5FF;
            box-shadow: 0px 0px 10px #00E5FF;
        }

        button {
            width: 100%;
            padding: 12px;
            background: #0099FF;
            border: none;
            border-radius: 8px;
            color: white;
            font-size: 16px;
            cursor: pointer;
            transition: .25s;
        }

        button:hover {
            background: #00E5FF;
            box-shadow: 0px 0px 12px #00E5FF;
        }

        a {
            color: #00E5FF;
            cursor: pointer;
        }

        .hidden { display: none; }

        .msg { color: #FF5555; text-align: center; }

    </style>

<script>
function toggleForm() {
    document.getElementById("loginForm").classList.toggle("hidden");
    document.getElementById("registerForm").classList.toggle("hidden");
}
</script>
</head>

<body>

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
<h1>💻 Login System</h1>
<p>Akses fitur hanya dengan login atau buat akun baru</p>
</div>
</div>
</body>
</html>