<?php
session_start();
$host = "localhost"; $user = "root"; $pass = ""; $db = "db_alat_online";
$conn = new mysqli($host, $user, $pass, $db);

// Jika sudah login, lempar langsung ke index
if (isset($_SESSION['user_id'])) {
    header("Location: index.php");
    exit;
}

$message = "";

// LOGIC REGISTER & LOGIN
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $u = $_POST['username'];
    $p = $_POST['password'];
    $action = $_POST['action'];

    if ($action == "register") {
        // Enkripsi password sebelum disimpan
        $hashed_pass = password_hash($p, PASSWORD_DEFAULT);
        
        $stmt = $conn->prepare("INSERT INTO users (username, password) VALUES (?, ?)");
        $stmt->bind_param("ss", $u, $hashed_pass);
        
        if ($stmt->execute()) {
            $message = "Registrasi berhasil! Silakan login.";
        } else {
            $message = "Username sudah terpakai.";
        }
    } 
    elseif ($action == "login") {
        $stmt = $conn->prepare("SELECT id, password FROM users WHERE username = ?");
        $stmt->bind_param("s", $u);
        $stmt->execute();
        $result = $stmt->get_result();

        if ($row = $result->fetch_assoc()) {
            // Cek kecocokan password input vs password database
            if (password_verify($p, $row['password'])) {
                $_SESSION['user_id'] = $row['id'];     // Simpan ID user
                $_SESSION['username'] = $u;            // Simpan Nama user
                header("Location: index.php");         // Pindah ke halaman utama
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
    <title>Login System</title>
</head>
<body>

    <h1>Selamat Datang</h1>
    <p style="color:red;"><?php echo $message; ?></p>

    <hr>

    <h3>Login</h3>
    <form method="POST" action="">
        <input type="hidden" name="action" value="login">
        
        <label>Username:</label><br>
        <input type="text" name="username" required><br><br>
        
        <label>Password:</label><br>
        <input type="password" name="password" required><br><br>
        
        <button type="submit">Masuk</button>
    </form>

    <hr>

    <h3>Belum punya akun? Daftar dulu</h3>
    <form method="POST" action="">
        <input type="hidden" name="action" value="register">
        
        <label>Username Baru:</label><br>
        <input type="text" name="username" required><br><br>
        
        <label>Password Baru:</label><br>
        <input type="password" name="password" required><br><br>
        
        <button type="submit">Daftar Akun</button>
    </form>

</body>
</html>