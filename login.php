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
        <title>Login - SecureTools</title>
        <meta name="viewport" content="width=device-width, initial-scale=1.0">
        <link href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@300;400;600;700&display=swap" rel="stylesheet">
        <link rel="stylesheet" href="login.css">
        
        <!-- Load Particles.js -->
        <script src="https://cdn.jsdelivr.net/particles.js/2.0.0/particles.min.js"></script>

        <style>
            /* Fix Width Issue: Pastikan padding dan border dihitung dalam width 100% */
            * { box-sizing: border-box; }

            body { 
                font-family: 'Plus Jakarta Sans', sans-serif; 
                /* OVERRIDE Background dari file CSS lama */
                background: linear-gradient(135deg, #1e3a8a 0%, #000000 100%) !important;
                background-size: 200% 200%;
                animation: gradientMove 15s ease infinite;
                overflow: hidden;
                margin: 0;
                height: 100vh;
                display: flex;
                justify-content: center;
                align-items: center;
                color: white;
            }

            @keyframes gradientMove {
                0% { background-position: 0% 50%; }
                50% { background-position: 100% 50%; }
                100% { background-position: 0% 50%; }
            }

            /* Container untuk Particles */
            #particles-js {
                position: fixed;
                top: 0;
                left: 0;
                width: 100%;
                height: 100%;
                z-index: -1; /* Pastikan di belakang konten */
            }

            /* Memastikan konten form ada di depan */
            .container {
                position: relative;
                z-index: 10;
                /* Tambahan style agar konsisten dengan tema baru jika perlu */
                backdrop-filter: blur(10px);
                background: rgba(255, 255, 255, 0.05);
                border: 1px solid rgba(255, 255, 255, 0.1);
                box-shadow: 0 25px 50px -12px rgba(0, 0, 0, 0.5);
            }
            
            /* Paksa lebar input dan tombol sama persis */
            input, button {
                width: 100% !important;
                box-sizing: border-box !important;
            }

            /* Sembunyikan elemen lama jika mengganggu */
            .glow { display: none; }
            .overlay { display: none; }
        </style>

        <script>
        function toggleForm() {
            document.getElementById("loginForm").classList.toggle("hidden");
            document.getElementById("registerForm").classList.toggle("hidden");
        }
        </script>
    </head>

    <body>

    <!-- Elemen Background Particles -->
    <div id="particles-js"></div>

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
            <div class="overlay" style="display:block; background: rgba(0,0,0,0.4);"></div> 
                
            <div class="right-box-content">
                <h1>Login System</h1>
                <p>Access features by logging in or creating a new account</p>
            </div>
        </div>

    </div>

    <!-- Inisialisasi Particles.js -->
    <script>
        particlesJS("particles-js", {
            "particles": {
                "number": { 
                    "value": 80, 
                    "density": { "enable": true, "value_area": 800 } 
                },
                "color": { "value": "#ffffff" },
                "shape": { "type": "circle" },
                "opacity": { "value": 0.5, "random": false },
                "size": { "value": 3, "random": true },
                "line_linked": { 
                    "enable": true, 
                    "distance": 150, 
                    "color": "#ffffff", 
                    "opacity": 0.4, 
                    "width": 1 
                },
                "move": { 
                    "enable": true, 
                    "speed": 2, 
                    "direction": "none", 
                    "random": false, 
                    "straight": false, 
                    "out_mode": "out", 
                    "bounce": false 
                }
            },
            "interactivity": {
                "detect_on": "canvas",
                "events": { 
                    "onhover": { "enable": true, "mode": "grab" }, 
                    "onclick": { "enable": true, "mode": "push" }, 
                    "resize": true 
                },
                "modes": { 
                    "grab": { "distance": 140, "line_linked": { "opacity": 1 } },
                    "push": { "particles_nb": 4 } 
                }
            },
            "retina_detect": true
        });
    </script>

    </body>
    </html>