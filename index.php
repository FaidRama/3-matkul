<?php
session_start();
// ==========================================
// 1. CONFIG & DEBUGGING
// ==========================================
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

define('INDEX_LOADED', true);

$host = "localhost"; $user = "root"; $pass = ""; $db = "db_alat_online";

$conn = new mysqli($host, $user, $pass, $db);
if ($conn->connect_error) { die("<h3>Koneksi Database Gagal:</h3> " . $conn->connect_error); }

if (!isset($_SESSION['user_id'])) { header("Location: login.php"); exit; }
if (isset($_GET['logout'])) { session_destroy(); header("Location: login.php"); exit; }

// --- Helper Functions ---
function sendJson($data, $code=200) {
    header('Content-Type: application/json'); http_response_code($code); echo json_encode($data); exit;
}
function logToDB($tool, $input, $output) {
    global $conn; $uid = $_SESSION['user_id'];
    $stmt = $conn->prepare("INSERT INTO history_penggunaan (user_id, tool_name, input_detail, result_detail) VALUES (?, ?, ?, ?)");
    $stmt->bind_param("isss", $uid, $tool, $input, $output); $stmt->execute();
}
function enkripsiDanSimpan($source, $dest, $kunci, $metode) {
    $data = file_get_contents($source);
    $ivLen = openssl_cipher_iv_length($metode); $iv = openssl_random_pseudo_bytes($ivLen);
    $enc = openssl_encrypt($data, $metode, $kunci, 0, $iv);
    if(file_put_contents($dest, $enc)) return bin2hex($iv);
    return false;
}

// --- API HANDLERS ---
if (isset($_GET['endpoint'])) {
    $ep = $_GET['endpoint']; $method = $_SERVER['REQUEST_METHOD'];
    $key = "KelompokKamiPalingKeren2025!!"; $algo = "AES-256-CBC";
    $jsonInput = json_decode(file_get_contents('php://input'), true);

    // 1. BMI 
    if ($ep == '/calc/bmi' && $method == 'POST') {
        $h = $jsonInput['heightCm'] ?? 0; $w = $jsonInput['weightKg'] ?? 0;
        if (!$h || !$w) sendJson(['error'=>'Input tidak valid'], 400);
        
        $h_meter = $h / 100;
        $bmi = round($w / ($h_meter * $h_meter), 2);
        
        $cat = "";
        $msg = "";
        $color = "";

        if ($bmi < 18.5) {
            $cat = 'Kurus (Underweight)';
            $msg = "Berat badan Anda kurang. Anda perlu meningkatkan asupan kalori dan protein sehat. Konsultasikan dengan ahli gizi untuk program penambahan berat badan yang aman.";
            $color = "text-yellow-400";
        } elseif ($bmi >= 18.5 && $bmi <= 24.9) {
            $cat = 'Normal (Ideal)';
            $msg = "Selamat! Berat badan Anda ideal. Terus pertahankan pola makan seimbang dan rutin berolahraga untuk menjaga kesehatan jangka panjang.";
            $color = "text-green-400";
        } elseif ($bmi >= 25 && $bmi <= 29.9) {
            $cat = 'Gemuk (Overweight)';
            $msg = "Berat badan Anda sedikit berlebih. Disarankan untuk mulai mengurangi konsumsi gula/lemak jenuh dan meningkatkan aktivitas kardio ringan.";
            $color = "text-orange-400";
        } else {
            $cat = 'Obesitas (Obesity)';
            $msg = "Perhatian: Anda dalam kategori obesitas. Kondisi ini meningkatkan risiko masalah kesehatan. Sangat disarankan berkonsultasi dengan dokter segera.";
            $color = "text-red-500";
        }

        logToDB('BMI', "$h cm, $w kg", "BMI: $bmi ($cat)");
        sendJson(['bmi'=>$bmi, 'category'=>$cat, 'message'=>$msg, 'color'=>$color]);
    }

    // 2. QR
    if ($ep == '/url/qr' && $method == 'POST') {
        $url = $jsonInput['url'] ?? ''; 
        if(!$url) sendJson(['error'=>'URL Kosong'], 400);
        $qrRaw = file_get_contents("https://api.qrserver.com/v1/create-qr-code/?size=300x300&data=".urlencode($url));
        if(!$qrRaw) sendJson(['error'=>'Gagal fetch API QR'], 500);

        $dir = 'uploads/qr/'; if(!is_dir($dir)) mkdir($dir, 0777, true);
        $fname = time().'_qr.png'; $path = $dir.$fname;

        $ivLen = openssl_cipher_iv_length($algo); $iv = openssl_random_pseudo_bytes($ivLen);
        $encData = openssl_encrypt($qrRaw, $algo, $key, 0, $iv);
        file_put_contents($path, $encData);

        $uid = $_SESSION['user_id']; $ivHex = bin2hex($iv);
        $stmt = $conn->prepare("INSERT INTO file_storage (user_id, filename, file_type, file_path, iv_file) VALUES (?, ?, 'png', ?, ?)");
        $stmt->bind_param("isss", $uid, $fname, $path, $ivHex); $stmt->execute();
        logToDB('QR', $url, 'Generated');
        sendJson(['downloadUrl'=>'download.php?id='.$stmt->insert_id, 'fileName'=>'qr_code.png']);
    }

    // 3. Compress
    if ($ep == '/image/compress' && $method == 'POST') {
        if(!isset($_FILES['file'])) sendJson(['error'=>'File missing'], 400);
        $f = $_FILES['file'];
        $ext = pathinfo($f['name'], PATHINFO_EXTENSION);
        $src = null;
        if(preg_match('/jpg|jpeg/i', $ext)) $src = @imagecreatefromjpeg($f['tmp_name']);
        elseif(preg_match('/png/i', $ext)) $src = @imagecreatefrompng($f['tmp_name']);
        
        if(!$src) sendJson(['error'=>'Format harus JPG/PNG'], 400);
        ob_start(); imagejpeg($src, null, 60); $compRaw = ob_get_clean(); imagedestroy($src);
        
        $dir = 'uploads/img/'; if(!is_dir($dir)) mkdir($dir, 0777, true);
        $fname = time().'_comp_'.$f['name'].'.jpg'; $path = $dir.$fname;

        $ivLen = openssl_cipher_iv_length($algo); $iv = openssl_random_pseudo_bytes($ivLen);
        $encData = openssl_encrypt($compRaw, $algo, $key, 0, $iv);
        file_put_contents($path, $encData);

        $uid=$_SESSION['user_id']; $ivHex=bin2hex($iv);
        $stmt=$conn->prepare("INSERT INTO file_storage (user_id, filename, file_type, file_path, iv_file) VALUES (?, ?, 'jpg', ?, ?)");
        $stmt->bind_param("isss", $uid, $fname, $path, $ivHex); $stmt->execute();
        
        logToDB('Compress', $f['name'], round(strlen($compRaw)/1024)."KB");
        sendJson(['downloadUrl'=>'download.php?id='.$stmt->insert_id, 'originalSize'=>$f['size'], 'compressedSize'=>strlen($compRaw), 'fileName'=>'compressed.jpg']);
    }

    // 4. DOCX
    if ($ep == '/doc/convert' && $method == 'POST') {
        if(!isset($_FILES['file'])) sendJson(['error'=>'File missing'], 400);
        $f = $_FILES['file'];
        $base = 'uploads/'; 
        $dirDoc=$base.'docx/'; $dirPdf=$base.'pdf/'; $dirTmp=$base.'temp/';
        if(!is_dir($dirDoc)) mkdir($dirDoc,0777,true); if(!is_dir($dirPdf)) mkdir($dirPdf,0777,true); if(!is_dir($dirTmp)) mkdir($dirTmp,0777,true);

        $cleanName = time().'_'.preg_replace('/[^A-Za-z0-9]/', '_', pathinfo($f['name'], PATHINFO_FILENAME));
        $tmpDoc = $dirTmp.$cleanName.'.docx';
        move_uploaded_file($f['tmp_name'], $tmpDoc);
        
        // Convert
        $loPath = __DIR__ . '/external_bin/LibreOffice/program/soffice.exe'; 
        shell_exec("$loPath --headless --convert-to pdf --outdir \"".realpath($dirTmp)."\" \"".realpath($tmpDoc)."\"");
        
        $tmpPdf = $dirTmp.$cleanName.'.pdf';
        if(!file_exists($tmpPdf)) sendJson(['error'=>'Gagal Convert LibreOffice'], 500);

        // Enkripsi
        $pathDoc = $dirDoc.$cleanName.'.docx'; $ivDoc = enkripsiDanSimpan($tmpDoc, $pathDoc, $key, $algo);
        $pathPdf = $dirPdf.$cleanName.'.pdf';  $ivPdf = enkripsiDanSimpan($tmpPdf, $pathPdf, $key, $algo);
        
        $uid=$_SESSION['user_id'];
        $stmt=$conn->prepare("INSERT INTO file_storage (user_id, filename, file_type, file_path, iv_file) VALUES (?, ?, ?, ?, ?)");
        $t='docx'; $n=$cleanName.'.docx'; $stmt->bind_param("issss",$uid,$n,$t,$pathDoc,$ivDoc); $stmt->execute();
        $t='pdf'; $n=$cleanName.'.pdf'; $stmt->bind_param("issss",$uid,$n,$t,$pathPdf,$ivPdf); $stmt->execute();
        $lastId=$stmt->insert_id;

        @unlink($tmpDoc); @unlink($tmpPdf);
        logToDB('DOCX', $f['name'], 'Converted');
        sendJson(['downloadUrl'=>'download.php?id='.$lastId, 'fileName'=>$cleanName.'.pdf']);
    }

    // 5. MP4 to MP3 Converter
    if ($ep == '/media/convert' && $method == 'POST') {
        if(!isset($_FILES['file'])) sendJson(['error'=>'File MP4 tidak ditemukan'], 400);
        $f = $_FILES['file'];
        $ext = strtolower(pathinfo($f['name'], PATHINFO_EXTENSION));

        if ($ext != 'mp4' && $ext != 'mkv') sendJson(['error'=>'Format harus MP4 atau MKV'], 400);

        $base = 'uploads/'; $dirMedia = $base.'media/'; $dirTmp = $base.'temp/';
        if(!is_dir($dirMedia)) mkdir($dirMedia,0777,true); if(!is_dir($dirTmp)) mkdir($dirTmp,0777,true);

        $cleanName = time().'_'.preg_replace('/[^A-Za-z0-9]/', '_', pathinfo($f['name'], PATHINFO_FILENAME));
        $tmpVidPath = realpath($dirTmp) . DIRECTORY_SEPARATOR . $cleanName . '.' . $ext;
        $rawAudioPath = realpath($dirTmp) . DIRECTORY_SEPARATOR . $cleanName . '_raw.mp3';
        $finalEncryptedPath = realpath($dirMedia) . DIRECTORY_SEPARATOR . $cleanName . '.mp3';

        if (!move_uploaded_file($f['tmp_name'], $tmpVidPath)) { sendJson(['error'=>'Gagal upload'], 500); }
        
        $ffmpeg_binary = __DIR__ . '/external_bin/ffmpeg/bin/ffmpeg.exe';
        $ffmpeg_command = "\"$ffmpeg_binary\" -y -i \"$tmpVidPath\" -vn -acodec libmp3lame -q:a 2 \"$rawAudioPath\" 2>&1";
        shell_exec($ffmpeg_command);

        if(!file_exists($rawAudioPath) || filesize($rawAudioPath) == 0) {
            @unlink($tmpVidPath);
            sendJson(['error' => 'Gagal konversi FFmpeg.'], 500);
        }

        $iv = enkripsiDanSimpan($rawAudioPath, $finalEncryptedPath, $key, $algo);
        if ($iv === false) { @unlink($tmpVidPath); @unlink($rawAudioPath); sendJson(['error'=>'Gagal enkripsi audio.'], 500); }

        $uid=$_SESSION['user_id'];
        $stmt=$conn->prepare("INSERT INTO file_storage (user_id, filename, file_type, file_path, iv_file) VALUES (?, ?, ?, ?, ?)");
        $t='mp3'; $n=$cleanName.'.mp3'; $dbPath = 'uploads/media/' . $cleanName . '.mp3';
        $stmt->bind_param("issss", $uid, $n, $t, $dbPath, $iv); $stmt->execute();
        $lastId=$stmt->insert_id;

        @unlink($tmpVidPath); @unlink($rawAudioPath);
        logToDB('MP4->MP3', $f['name'], 'Success');
        sendJson(['downloadUrl'=>'download.php?id='.$lastId, 'fileName'=>$n]);
    }
    exit;
}

$uid = $_SESSION['user_id'];
$files = [];
$qFile = $conn->query("SELECT * FROM file_storage WHERE user_id='$uid' AND file_type IN ('docx','pdf') ORDER BY id DESC");
if ($qFile) { while($r=$qFile->fetch_assoc()){ $base = pathinfo($r['filename'], PATHINFO_FILENAME); $files[$base][$r['file_type']] = $r; } }

$viewFile = 'dashboard_view.php';
if (file_exists($viewFile)) { require_once $viewFile; } else { die("File View Hilang"); }
?>