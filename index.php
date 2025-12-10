<?php
session_start();
// ==========================================
// 1. CONFIG & BACKEND LOGIC
// ==========================================
$host = "localhost"; $user = "root"; $pass = ""; $db = "db_alat_online";
error_reporting(E_ALL); ini_set('display_errors', 0);

$conn = new mysqli($host, $user, $pass, $db);

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
        $bmi = round($w / (($h/100)*($h/100)), 2);
        $cat = ($bmi<18.5)?'Kurus':(($bmi<25)?'Normal':'Gemuk');
        logToDB('BMI', "$h cm, $w kg", "BMI: $bmi");
        sendJson(['bmi'=>$bmi, 'category'=>$cat]);
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
        $loPath = __DIR__ . '/external_bin/libreoffice/program/soffice.exe'; 
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
        // ... (validasi file upload sama seperti sebelumnya) ...
        if(!isset($_FILES['file'])) sendJson(['error'=>'File MP4 tidak ditemukan'], 400);
        $f = $_FILES['file'];
        $ext = strtolower(pathinfo($f['name'], PATHINFO_EXTENSION));

        if ($ext != 'mp4' && $ext != 'mkv') sendJson(['error'=>'Format harus MP4 atau MKV'], 400);

        $base = 'uploads/';
        $dirMedia = $base.'media/'; 
        $dirTmp = $base.'temp/';
        if(!is_dir($dirMedia)) mkdir($dirMedia,0777,true); 
        if(!is_dir($dirTmp)) mkdir($dirTmp,0777,true);

        $cleanName = time().'_'.preg_replace('/[^A-Za-z0-9]/', '_', pathinfo($f['name'], PATHINFO_FILENAME));
        // Gunakan realpath untuk folder agar path absolut dan aman
        $tmpVidPath = realpath($dirTmp) . DIRECTORY_SEPARATOR . $cleanName . '.' . $ext;
        $rawAudioPath = realpath($dirTmp) . DIRECTORY_SEPARATOR . $cleanName . '_raw.mp3';
        $finalEncryptedPath = realpath($dirMedia) . DIRECTORY_SEPARATOR . $cleanName . '.mp3';

        if (!move_uploaded_file($f['tmp_name'], $tmpVidPath)) {
             sendJson(['error'=>'Gagal mengupload file ke folder tempSS'], 500);
        }
        
        // --- Perintah FFmpeg ---
        // PATH FFmpeg: Pastikan path ini benar di komputer Anda!
        // Jika ffmpeg sudah ada di environment variable path, cukup 'ffmpeg'
        // Jika belum, gunakan full path seperti 'C:\\ffmpeg\\bin\\ffmpeg.exe'
        $ffmpeg_binary = __DIR__ . '/external_bin/ffmpeg/bin/ffmpeg.exe'; // Atau sesuaikan path-nya
        if (!file_exists($tmpVidPath)) {
            sendJson(['error' => 'File input hilang sebelum diproses.'], 500);
        }

        // Command optimasi:
        // -y : Overwrite output files without asking
        // -vn : Disable video recording (hanya ambil audio)
        // -acodec libmp3lame : Codec MP3 standar
        // -q:a 2 : Variable Bit Rate (VBR) kualitas tinggi (sekitar 190kbps), lebih cepat dari CBR kadang-kadang
        $ffmpeg_command = "\"$ffmpeg_binary\" -y -i \"$tmpVidPath\" -vn -acodec libmp3lame -q:a 2 \"$rawAudioPath\" 2>&1";
        
        // Jalankan perintah
        $output = shell_exec($ffmpeg_command);

        // Cek keberhasilan
        if(!file_exists($rawAudioPath) || filesize($rawAudioPath) == 0) {
            @unlink($tmpVidPath);
            // Log error untuk debugging (opsional, bisa dihapus di production)
            // file_put_contents('ffmpeg_debug.log', $output); 
            sendJson([
                'error' => 'Gagal konversi FFmpeg.',
                'debug_command' => $ffmpeg_command, // Perintah yang dijalankan
                'debug_output' => $output           // Apa balasan error dari FFmpeg
            ], 500);
        }

        $iv = enkripsiDanSimpan($rawAudioPath, $finalEncryptedPath, $key, $algo);
        if ($iv === false) {
            @unlink($tmpVidPath); @unlink($rawAudioPath);
            sendJson(['error'=>'Gagal mengenkripsi file audio.'], 500);
        }
        

        // --- Simpan ke Database ---
        $uid=$_SESSION['user_id'];
        $stmt=$conn->prepare("INSERT INTO file_storage (user_id, filename, file_type, file_path, iv_file) VALUES (?, ?, ?, ?, ?)");
        
        $t='mp3'; 
        $n=$cleanName.'.mp3'; 
        // Gunakan path relatif untuk disimpan di DB agar mudah di-link
        $dbPath = 'uploads/media/' . $cleanName . '.mp3';
        
        $stmt->bind_param("issss", $uid, $n, $t, $dbPath, $iv); 
        $stmt->execute();
        $lastId=$stmt->insert_id;

        // Bersihkan file sementara
        @unlink($tmpVidPath);
        @unlink($rawAudioPath);
        
        logToDB('MP4->MP3', $f['name'], 'Success');
        sendJson(['downloadUrl'=>'download.php?id='.$lastId, 'fileName'=>$n]);
    }

    exit;
}

// ==========================================
// 2. FETCH DATA
// ==========================================
$uid = $_SESSION['user_id'];
$files = [];
$qFile = $conn->query("SELECT * FROM file_storage WHERE user_id='$uid' AND file_type IN ('docx','pdf') ORDER BY id DESC");
while($r=$qFile->fetch_assoc()){
    $base = pathinfo($r['filename'], PATHINFO_FILENAME);
    $files[$base][$r['file_type']] = $r;
}
?>
<!DOCTYPE html>
<html lang="id" class="dark">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Dashboard - SecureTools</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <script>
        tailwind.config = {
            darkMode: 'class',
            theme: {
                extend: {
                    fontFamily: {
                        'economica': ['Economica', 'sans-serif'],
                        'inter': ['Inter', 'sans-serif'],
                    }
                }
            }
        }
    </script>
    <link href="https://fonts.googleapis.com/css?family=Economica:400,700&display=swap" rel="stylesheet">
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;600&display=swap" rel="stylesheet">
    
    <style>
        .animated-bg {
            position: fixed;
            top: 0;
            left: 0;
            width: 100%;
            height: 100vh;
            z-index: -50;
            background-color: #34495e;
            overflow: hidden;
        }

        .animated-bg::after {
            content: "";
            position: absolute;
            top: 0;
            left: 0;
            width: 100%;
            height: 1000%; /* Very tall to allow scrolling animation */
            /* Gradient colors from prompt */
            background: linear-gradient(to bottom, 
                rgb(101,91,239) 0%, 
                rgb(250,40,191) 20%, 
                rgb(255,70,101) 40%, 
                rgb(251,222,78) 60%, 
                rgb(0,251,234) 80%, 
                rgb(85,93,239) 100%
            );
            /* Animation properties */
            animation: anime 20s linear infinite alternate;
        }

        @keyframes anime {
            0% { transform: translateY(0); }
            50% { transform: translateY(-50%); } /* Move halfway up */
            100% { transform: translateY(0); }
        }

        /* * GLASSMORPHISM STYLES 
         * Adapted to work on top of the colorful background
         */
        body { 
            font-family: 'Inter', sans-serif; 
            color: white; 
        }

        .glass-panel {
            backdrop-filter: blur(16px); 
            -webkit-backdrop-filter: blur(16px);
            transition: all 0.3s ease;
            border: 1px solid rgba(255, 255, 255, 0.2);
        }

        /* Dark Mode: Transparan gelap */
        .dark .glass-panel {
            background: rgba(0, 0, 0, 0.4); 
            color: white;
        }

        /* Light Mode: Transparan putih (seperti es) */
        html:not(.dark) .glass-panel {
            background: rgba(255, 255, 255, 0.65);
            color: #1f2937; /* Dark text for contrast */
            border-color: rgba(255, 255, 255, 0.6);
        }

        /* Inputs */
        .glass-input { 
            width: 100%; padding: 0.75rem 1rem; border-radius: 0.75rem; 
            transition: all 0.2s; outline: none;
        }
        .dark .glass-input { 
            background: rgba(0,0,0,0.3); border: 1px solid rgba(255,255,255,0.2); color: white; 
        }
        .dark .glass-input:focus { border-color: #fbd38d; background: rgba(0,0,0,0.5); }
        
        html:not(.dark) .glass-input { 
            background: rgba(255,255,255,0.8); border: 1px solid rgba(0,0,0,0.1); color: #333; 
        }
        html:not(.dark) .glass-input:focus { border-color: #6366f1; background: white; }

        /* Sidebar Items */
        .nav-item.active {
            background: rgba(255, 255, 255, 0.2);
            font-weight: 700;
            border-left: 4px solid #fbde4e; /* Yellow accent from gradient */
        }
        html:not(.dark) .nav-item.active {
            background: rgba(255, 255, 255, 0.8);
            color: #4f46e5;
        }

        /* Scrollbar */
        ::-webkit-scrollbar { width: 6px; }
        ::-webkit-scrollbar-thumb { background: rgba(255,255,255,0.3); border-radius: 3px; }
    </style>
</head>
<body class="overflow-hidden">

    <div class="animated-bg"></div>

    <div class="flex h-screen relative z-10">
        <div id="sidebar-overlay" onclick="toggleSidebar()" class="fixed inset-0 bg-black/60 z-20 hidden lg:hidden backdrop-blur-sm"></div>
        
        <aside id="sidebar" class="fixed lg:static inset-y-0 left-0 z-30 w-64 transform -translate-x-full lg:translate-x-0 transition-transform duration-300 glass-panel border-r border-white/10 flex flex-col h-full">
            <div class="h-20 flex items-center px-6 border-b border-white/10">
                <div class="w-10 h-10 rounded-xl bg-gradient-to-br from-yellow-300 to-pink-500 flex items-center justify-center text-white font-bold text-2xl mr-3 shadow-lg">S</div>
                <span class="text-2xl font-['Economica'] tracking-wider font-bold">SecureTools</span>
            </div>

            <nav class="flex-1 px-4 py-6 space-y-2 overflow-y-auto">
                <button onclick="switchTab('dashboard')" id="nav-dashboard" class="nav-item active w-full flex items-center gap-3 px-4 py-3 rounded-xl transition-all hover:bg-white/10 text-left text-sm">
                    <span>🏠</span> Dashboard
                </button>
                <button onclick="switchTab('docx')" id="nav-docx" class="nav-item w-full flex items-center gap-3 px-4 py-3 rounded-xl transition-all hover:bg-white/10 text-left text-sm">
                    <span>📄</span> DOCX to PDF
                </button>
                <button onclick="switchTab('image')" id="nav-image" class="nav-item w-full flex items-center gap-3 px-4 py-3 rounded-xl transition-all hover:bg-white/10 text-left text-sm">
                    <span>🖼️</span> Image Compress
                </button>
                <button onclick="switchTab('qr')" id="nav-qr" class="nav-item w-full flex items-center gap-3 px-4 py-3 rounded-xl transition-all hover:bg-white/10 text-left text-sm">
                    <span>🔗</span> QR Generator
                </button>
                <button onclick="switchTab('media')" id="nav-media" class="nav-item w-full flex items-center gap-3 px-4 py-3 rounded-xl transition-all hover:bg-white/10 text-left text-sm">
                    <span>🎵</span> MP4 to MP3
                </button>
                <button onclick="switchTab('bmi')" id="nav-bmi" class="nav-item w-full flex items-center gap-3 px-4 py-3 rounded-xl transition-all hover:bg-white/10 text-left text-sm">
                    <span>⚖️</span> BMI Calculator
                </button>
                
            </nav>

            <div class="p-4 border-t border-white/10 bg-black/10">
                <div class="flex items-center gap-3">
                    <div class="w-10 h-10 rounded-full bg-white/20 flex items-center justify-center font-bold">
                        <?= strtoupper(substr($_SESSION['username'], 0, 1)) ?>
                    </div>
                    <div class="flex-1 min-w-0">
                        <p class="text-sm font-bold truncate"><?= htmlspecialchars($_SESSION['username']) ?></p>
                        <p class="text-xs opacity-70">Online</p>
                    </div>
                </div>
            </div>
        </aside>

        <div class="flex-1 flex flex-col h-screen overflow-hidden">
            <header class="h-20 flex items-center justify-between px-6 glass-panel border-b border-white/10 z-10 shrink-0">
                <button onclick="toggleSidebar()" class="lg:hidden p-2 rounded-lg hover:bg-white/10">
                    <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6h16M4 12h16M4 18h16"></path></svg>
                </button>
                <h2 id="page-title" class="text-xl font-['Economica'] font-bold tracking-wide text-white drop-shadow-md hidden md:block">DASHBOARD</h2>

                <div class="flex items-center gap-4">
                    <button onclick="toggleTheme()" class="p-2 rounded-full hover:bg-white/20 transition-colors backdrop-blur-md bg-white/10 shadow-sm border border-white/20">
                        <svg id="icon-sun" class="w-5 h-5 hidden dark:block text-yellow-300" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 3v1m0 16v1m9-9h-1M4 12H3m15.364 6.364l-.707-.707M6.343 6.343l-.707-.707m12.728 0l-.707.707M6.343 17.657l-.707.707M16 12a4 4 0 11-8 0 4 4 0 018 0z" /></svg>
                        <svg id="icon-moon" class="w-5 h-5 block dark:hidden text-indigo-600" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M20.354 24.354A9 9 0 018.646 3.646 9.003 9.003 0 0012 21a9.003 9.003 0 008.354-5.646z" /></svg>
                    </button>
                    <a href="?logout=true" class="bg-red-500 hover:bg-red-600 text-white border border-red-400 px-5 py-2 rounded-full text-xs font-bold transition-all shadow-lg hover:shadow-red-500/50">LOGOUT</a>
                </div>
            </header>

            <main class="flex-1 overflow-y-auto p-4 lg:p-8 relative">
                <div class="max-w-5xl mx-auto space-y-6">

                    <div id="view-dashboard" class="view-section">
                        <div class="glass-panel rounded-2xl p-8 mb-6 text-center">
                            <h3 class="text-3xl font-['Economica'] font-bold mb-2">Welcome, <?= htmlspecialchars($_SESSION['username']) ?>!</h3>
                            <p class="opacity-80">Your encrypted workspace is ready.</p>
                        </div>
                        <div class="glass-panel rounded-2xl overflow-hidden">
                            <div class="px-6 py-4 border-b border-white/10 flex justify-between items-center bg-black/20">
                                <h4 class="font-bold font-['Economica'] text-xl">Recent Files</h4>
                                <button onclick="location.reload()" class="text-xs px-3 py-1 rounded border border-white/30 hover:bg-white/20">Refresh</button>
                            </div>
                            <div class="overflow-x-auto">
                                <table class="w-full text-sm text-left">
                                    <thead class="text-xs uppercase bg-black/10">
                                        <tr><th class="px-6 py-3">Time</th><th class="px-6 py-3">Original</th><th class="px-6 py-3">Result</th></tr>
                                    </thead>
                                    <tbody class="divide-y divide-white/10">
                                        <?php if(empty($files)): ?>
                                            <tr><td colspan="3" class="px-6 py-8 text-center opacity-50">No activity yet.</td></tr>
                                        <?php else: foreach($files as $base=>$pair): 
                                            $d=$pair['docx']??null; $p=$pair['pdf']??null;
                                            if(!$d && !$p) continue;
                                            $ts = explode('_',$base)[0]; 
                                            $date = is_numeric($ts) ? date("d M H:i", $ts) : "-";
                                        ?>
                                        <tr class="hover:bg-white/5">
                                            <td class="px-6 py-4 font-mono text-xs opacity-70"><?= $date ?></td>
                                            <td class="px-6 py-4"><?= $d ? htmlspecialchars($d['filename']) : '-' ?></td>
                                            <td class="px-6 py-4">
                                                <?php if($p): ?><a href="download.php?id=<?= $p['id'] ?>" class="text-xs bg-green-500 hover:bg-green-600 text-white px-2 py-1 rounded">Download PDF</a><?php else: echo "Pending"; endif; ?>
                                            </td>
                                        </tr>
                                        <?php endforeach; endif; ?>
                                    </tbody>
                                </table>
                            </div>
                        </div>
                    </div>

                    <div id="view-docx" class="view-section hidden">
                        <div class="glass-panel rounded-3xl p-8 max-w-xl mx-auto">
                            <h3 class="text-2xl font-bold mb-6 flex items-center gap-3"><span class="text-3xl">📄</span> DOCX to PDF</h3>
                            <form id="formDoc" class="space-y-6">
                                <input type="file" id="fileDoc" accept=".docx" class="glass-input file:mr-4 file:py-2 file:px-4 file:rounded-full file:border-0 file:text-xs file:font-semibold file:bg-yellow-400 file:text-black hover:file:bg-yellow-300 cursor-pointer text-white dark:text-white text-gray-800">
                                <button type="submit" class="w-full py-3 rounded-xl bg-gradient-to-r from-blue-600 to-indigo-600 hover:from-blue-500 hover:to-indigo-500 text-white font-bold shadow-lg transform hover:-translate-y-1 transition-all">CONVERT NOW</button>
                            </form>
                            <div id="resDoc" class="mt-6"></div>
                        </div>
                    </div>

                    <div id="view-image" class="view-section hidden">
                        <div class="glass-panel rounded-3xl p-8 max-w-xl mx-auto">
                            <h3 class="text-2xl font-bold mb-6 flex items-center gap-3"><span class="text-3xl">🖼️</span> Image Compress</h3>
                            <form id="formImg" class="space-y-6">
                                <input type="file" id="fileImg" accept="image/*" class="glass-input file:mr-4 file:py-2 file:px-4 file:rounded-full file:border-0 file:text-xs file:font-semibold file:bg-pink-500 file:text-white hover:file:bg-pink-400 cursor-pointer">
                                <button type="submit" class="w-full py-3 rounded-xl bg-gradient-to-r from-pink-600 to-rose-600 hover:from-pink-500 hover:to-rose-500 text-white font-bold shadow-lg transform hover:-translate-y-1 transition-all">COMPRESS NOW</button>
                            </form>
                            <div id="resImg" class="mt-6"></div>
                        </div>
                    </div>

                    <div id="view-qr" class="view-section hidden">
                        <div class="glass-panel rounded-3xl p-8 max-w-xl mx-auto">
                            <h3 class="text-2xl font-bold mb-6 flex items-center gap-3"><span class="text-3xl">🔗</span> QR Generator</h3>
                            <form id="formQr" class="space-y-6">
                                <input type="url" id="inpUrl" placeholder="https://..." class="glass-input">
                                <button type="submit" class="w-full py-3 rounded-xl bg-gradient-to-r from-green-500 to-teal-500 hover:from-green-400 hover:to-teal-400 text-white font-bold shadow-lg transform hover:-translate-y-1 transition-all">GENERATE QR</button>
                            </form>
                            <div id="resQr" class="mt-6 text-center"></div>
                        </div>
                    </div>

                    <div id="view-bmi" class="view-section hidden">
                        <div class="glass-panel rounded-3xl p-8 max-w-xl mx-auto">
                            <h3 class="text-2xl font-bold mb-6 flex items-center gap-3"><span class="text-3xl">⚖️</span> BMI Calculator</h3>
                            <form id="formBmi" class="space-y-6">
                                <div class="grid grid-cols-2 gap-4">
                                    <input type="number" id="bH" placeholder="Height (cm)" class="glass-input">
                                    <input type="number" id="bW" placeholder="Weight (kg)" class="glass-input">
                                </div>
                                <button type="submit" class="w-full py-3 rounded-xl bg-gradient-to-r from-orange-500 to-red-500 hover:from-orange-400 hover:to-red-400 text-white font-bold shadow-lg transform hover:-translate-y-1 transition-all">CALCULATE</button>
                            </form>
                            <div id="resBmi" class="mt-6"></div>
                        </div>
                    </div>

                    <div id="view-media" class="view-section hidden">
                        <div class="glass-panel rounded-3xl p-8 max-w-xl mx-auto">
                            <h3 class="text-2xl font-bold mb-6 flex items-center gap-3"><span class="text-3xl">🎵</span> MP4 to MP3</h3>
                            <form id="formMedia" class="space-y-6">
                                <input type="file" id="fileMedia" accept="video/mp4, video/x-m4v, video/quicktime, video/x-matroska" class="glass-input file:mr-4 file:py-2 file:px-4 file:rounded-full file:border-0 file:text-xs file:font-semibold file:bg-yellow-400 file:text-black hover:file:bg-yellow-300 cursor-pointer text-white dark:text-white text-gray-800">
                                <button type="submit" class="w-full py-3 rounded-xl bg-gradient-to-r from-purple-600 to-pink-600 hover:from-purple-500 hover:to-pink-500 text-white font-bold shadow-lg transform hover:-translate-y-1 transition-all">CONVERT TO MP3</button>
                            </form>
                            <div id="resMedia" class="mt-6"></div>
                        </div>
                    </div>

                </div>
            </main>
        </div>
    </div>

    <script>
        // 1. THEME LOGIC
        const html = document.documentElement;
        if (localStorage.theme === 'dark' || (!('theme' in localStorage) && window.matchMedia('(prefers-color-scheme: dark)').matches)) {
            html.classList.add('dark');
        } else {
            html.classList.remove('dark');
        }
        function toggleTheme() {
            html.classList.toggle('dark');
            localStorage.theme = html.classList.contains('dark') ? 'dark' : 'light';
        }

        // 2. TABS & SIDEBAR
        const sidebar = document.getElementById('sidebar');
        const overlay = document.getElementById('sidebar-overlay');
        const pageTitle = document.getElementById('page-title');

        function toggleSidebar() {
            sidebar.classList.toggle('-translate-x-full');
            overlay.classList.toggle('hidden');
        }

        function switchTab(id) {
            document.querySelectorAll('.view-section').forEach(el => el.classList.add('hidden'));
            document.getElementById('view-'+id).classList.remove('hidden');
            
            document.querySelectorAll('.nav-item').forEach(el => el.classList.remove('active'));
            document.getElementById('nav-'+id).classList.add('active');

            const titles = {'dashboard':'DASHBOARD','docx':'DOCX CONVERTER','image':'IMAGE COMPRESSOR','qr':'QR GENERATOR','bmi':'BMI CALCULATOR', 'media':'MP4 TO MP3'};
            pageTitle.innerText = titles[id];
            
            if(window.innerWidth < 1024) toggleSidebar();
        }

        // 3. API LOGIC
        const API = "?endpoint=";
        const msg = (el, txt, err=false) => el.innerHTML = `<div class="p-4 rounded-xl text-center font-bold ${err?'bg-red-500/20 text-red-200 border border-red-500/30':'bg-green-500/20 text-white border border-green-500/30'}">${txt}</div>`;

        document.getElementById('formDoc').onsubmit = async (e) => {
            e.preventDefault(); const res=document.getElementById('resDoc');
            const f=document.getElementById('fileDoc').files[0]; if(!f) return;
            const fd=new FormData(); fd.append('file', f); res.innerText="Converting...";
            try {
                const r=await fetch(API+'/doc/convert',{method:'POST',body:fd}); const d=await r.json();
                if(d.error) throw d.error; msg(res, `<a href="${d.downloadUrl}" class="underline">Success! Download PDF</a>`);
            } catch(e){ msg(res, e, true); }
        };

        document.getElementById('formImg').onsubmit = async (e) => {
            e.preventDefault(); const res=document.getElementById('resImg');
            const f=document.getElementById('fileImg').files[0]; if(!f) return;
            const fd=new FormData(); fd.append('file', f); res.innerText="Compressing...";
            try {
                const r=await fetch(API+'/image/compress',{method:'POST',body:fd}); const d=await r.json();
                if(d.error) throw d.error; msg(res, `Saved ${((d.originalSize-d.compressedSize)/1024).toFixed(1)} KB! <a href="${d.downloadUrl}" class="underline ml-2">Download</a>`);
            } catch(e){ msg(res, e, true); }
        };

        document.getElementById('formQr').onsubmit = async (e) => {
            e.preventDefault(); const res=document.getElementById('resQr'); res.innerText="Generating...";
            try {
                const r=await fetch(API+'/url/qr',{method:'POST',body:JSON.stringify({url:document.getElementById('inpUrl').value})}); const d=await r.json();
                if(d.error) throw d.error; res.innerHTML=`<img src="${d.downloadUrl}" class="w-32 mx-auto rounded mb-2 shadow-lg"><a href="${d.downloadUrl}" download="${d.fileName}" class="bg-white text-black px-3 py-1 rounded text-xs font-bold">DOWNLOAD</a>`;
            } catch(e){ msg(res, e, true); }
        };

        document.getElementById('formBmi').onsubmit = async (e) => {
            e.preventDefault(); const res=document.getElementById('resBmi'); res.innerText="Calculating...";
            try {
                const r=await fetch(API+'/calc/bmi',{method:'POST',body:JSON.stringify({heightCm:document.getElementById('bH').value, weightKg:document.getElementById('bW').value})});
                const d=await r.json(); msg(res, `BMI: ${d.bmi} (${d.category})`);
            } catch(e){ msg(res, "Error", true); }
        };

        // --- Media Conversion JS Logic ---
        document.getElementById('formMedia').onsubmit = async (e) => {
            e.preventDefault(); 
            const res = document.getElementById('resMedia');
            const btn = document.querySelector('#formMedia button'); // Ambil tombol submit
            const f = document.getElementById('fileMedia').files[0]; 

            if(!f) return;
            
            // Validasi ukuran file di sisi klien (opsional, misal max 50MB)
            if(f.size > 50 * 1024 * 1024) {
                msg(res, "File terlalu besar! Maksimal 50MB.", true);
                return;
            }

            const fd = new FormData(); 
            fd.append('file', f); 

            // Tampilan Loading
            btn.disabled = true; // Matikan tombol biar gak diklik berkali-kali
            btn.innerHTML = "⏳ Converting... Please Wait";
            btn.classList.add("opacity-50", "cursor-not-allowed");
            res.innerHTML = `
                <div class="p-4 rounded-xl text-center bg-blue-500/20 text-blue-200 border border-blue-500/30 animate-pulse">
                    <p class="font-bold">Sedang memproses file...</p>
                    <p class="text-xs mt-1">File besar mungkin butuh waktu beberapa menit.</p>
                </div>`;

            try {
                const r = await fetch(API+'/media/convert', { method:'POST', body:fd }); 
                const d = await r.json();

                if(d.error) throw d.error; 
                msg(res, `✅ Berhasil! Audio siap. <a href="${d.downloadUrl}" class="underline font-bold text-green-400">Download MP3</a>`);
            } catch(e) { 
                msg(res, `Gagal: ${e}`, true); 
            } finally {
                // Kembalikan tombol ke keadaan semula
                btn.disabled = false;
                btn.innerHTML = "CONVERT TO MP3";
                btn.classList.remove("opacity-50", "cursor-not-allowed");
            }
        };

    </script>
</body>
</html>
