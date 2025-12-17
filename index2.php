<?php
session_start();

// --- [FIX] INI TIKET MASUKNYA ---
// Baris ini wajib ada supaya dashboard_view.php mau terbuka
define('INDEX_LOADED', true); 
// --------------------------------

// ==========================================
// 1. CONFIG & INSECURE SETUP
// ==========================================
ini_set('display_errors', 1); 
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

$host = "localhost"; $user = "root"; $pass = ""; $db = "db_alat_online";
$conn = new mysqli($host, $user, $pass, $db);

// Cek Login: Kalau belum login, lempar balik ke login2.php
if (!isset($_SESSION['user_id'])) { header("Location: login2.php"); exit; }
if (isset($_GET['logout'])) { session_destroy(); header("Location: login2.php"); exit; }

// --- Helper Functions (UNSAFE) ---
function sendJson($data, $code=200) {
    header('Content-Type: application/json'); http_response_code($code); echo json_encode($data); exit;
}

function logToDB($tool, $input, $output) {
    global $conn; $uid = $_SESSION['user_id'];
    // INSECURE LOG: SQL Injection Vulnerable
    $sql = "INSERT INTO history_penggunaan (user_id, tool_name, input_detail, result_detail) VALUES ('$uid', '$tool', '$input', '$output')";
    $conn->query($sql); 
}

function enkripsiDanSimpan($source, $dest, $kunci, $metode) {
    $data = file_get_contents($source);
    $ivLen = openssl_cipher_iv_length($metode); $iv = openssl_random_pseudo_bytes($ivLen);
    $enc = openssl_encrypt($data, $metode, $kunci, 0, $iv);
    if(file_put_contents($dest, $enc)) return bin2hex($iv);
    return false;
}

// --- API HANDLERS (UNSAFE) ---
if (isset($_GET['endpoint'])) {
    $ep = $_GET['endpoint']; $method = $_SERVER['REQUEST_METHOD'];
    $key = "KelompokKamiPalingKeren2025!!"; $algo = "AES-256-CBC";
    $jsonInput = json_decode(file_get_contents('php://input'), true);

    // 5. MP4 to MP3 Converter (UNSAFE VERSION)
    if ($ep == '/media/convert' && $method == 'POST') {
        if(!isset($_FILES['file'])) sendJson(['error'=>'File not found'], 400);
        $f = $_FILES['file'];

        // VULNERABILITY: Hanya cek ekstensi, tanpa cek MIME type
        $ext = strtolower(pathinfo($f['name'], PATHINFO_EXTENSION));
        if ($ext != 'mp4' && $ext != 'mkv') sendJson(['error'=>'Format must be MP4'], 400);

        $base = 'uploads/'; $dirMedia = $base.'media/'; $dirTmp = $base.'temp/';
        if(!is_dir($dirMedia)) mkdir($dirMedia,0777,true); if(!is_dir($dirTmp)) mkdir($dirTmp,0777,true);

        $cleanName = pathinfo($f['name'], PATHINFO_FILENAME) . '_' . time(); 
        $tmpVidPath = $dirTmp . $cleanName . '.' . $ext;
        $rawAudioPath = $dirTmp . $cleanName . '_raw.mp3';
        $finalEncryptedPath = $dirMedia . $cleanName . '.mp3';

        if (!move_uploaded_file($f['tmp_name'], $tmpVidPath)) sendJson(['error'=>'Upload failed'], 500); 
        
        // EXEC FFMPEG
        $ffmpeg_binary = __DIR__ . '/external_bin/ffmpeg/bin/ffmpeg.exe';
        $ffmpeg_command = "\"$ffmpeg_binary\" -y -i \"$tmpVidPath\" -vn -acodec libmp3lame -q:a 2 \"$rawAudioPath\" 2>&1";
        shell_exec($ffmpeg_command);

        logToDB('MP4->MP3 (Unsafe)', $f['name'], 'Converted');
        sendJson(['downloadUrl'=>'#', 'fileName'=>$cleanName.'.mp3']);
    }
    exit;
}

// ==========================================
// 2. FETCH DATA & VIEW
// ==========================================
$uid = $_SESSION['user_id'];
// INSECURE QUERY
$qFile = $conn->query("SELECT * FROM file_storage WHERE user_id='$uid' ORDER BY id DESC LIMIT 20");
$files = [];
if ($qFile) { 
    while($r=$qFile->fetch_assoc()){ 
        $base = pathinfo($r['filename'], PATHINFO_FILENAME); 
        $files[$base][$r['file_type']] = $r; 
    } 
}

// Load View
$viewFile = 'dashboard_view.php';
if (file_exists($viewFile)) { require_once $viewFile; } 
?>