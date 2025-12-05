<?php
session_start();
// ==========================================
// 1. KONEKSI DATABASE
// ==========================================
$host = "localhost";
$user = "root";
$pass = "";
$db   = "db_alat_online"; 

$conn = new mysqli($host, $user, $pass, $db);

if (!isset($_SESSION['user_id'])) {
    header("Location: login.php");
    exit;
}

if (isset($_GET['logout'])) {
    session_destroy();
    header("Location: login.php");
    exit;
}

// Helper: Simpan Log
function logToDB($tool, $input, $output) {
    global $conn;
    $uid = $_SESSION['user_id'];
    $stmt = $conn->prepare("INSERT INTO history_penggunaan (user_id, tool_name, input_detail, result_detail) VALUES (?, ?, ?, ?)");
    $stmt->bind_param("isss", $uid, $tool, $input, $output);
    $stmt->execute();
}

// Helper: JSON Response
function sendJson($data, $code = 200) {
    header('Content-Type: application/json');
    http_response_code($code);
    echo json_encode($data);
    exit;
}

// ==========================================
// 2. API ENDPOINTS (Logic Backend)
// ==========================================
if (isset($_GET['endpoint'])) {
    $ep = $_GET['endpoint'];
    $method = $_SERVER['REQUEST_METHOD'];
    $input = json_decode(file_get_contents('php://input'), true);

    // KONFIGURASI ENKRIPSI
    $kunciRahasia = "KelompokKamiPalingKeren2025!!"; 
    $metode       = "AES-256-CBC";

    // Fungsi Enkripsi & Simpan
    function enkripsiDanSimpan($sourcePath, $destPath, $kunci, $met) {
        $isiFile = file_get_contents($sourcePath);
        $ivLength = openssl_cipher_iv_length($met);
        $iv       = openssl_random_pseudo_bytes($ivLength);
        $isiTerenkripsi = openssl_encrypt($isiFile, $met, $kunci, 0, $iv);
        if (file_put_contents($destPath, $isiTerenkripsi)) {
            return bin2hex($iv);
        }
        return false;
    }

    // --- A. BMI TOOL ---
    if ($ep == '/calc/bmi' && $method == 'POST') {
        $h = $input['heightCm'] ?? 0;
        $w = $input['weightKg'] ?? 0;
        if (!$h || !$w) sendJson(['error' => 'Input salah'], 400);

        $bmi = round($w / (($h/100) * ($h/100)), 2);
        $cat = ($bmi < 18.5) ? 'Underweight' : (($bmi < 25) ? 'Normal' : 'Overweight');
        
        logToDB('BMI', "H:$h W:$w", "BMI:$bmi");
        sendJson(['bmi' => $bmi, 'category' => $cat]);
    }

    // --- B. QR TOOL (TERENKRIPSI) ---
    if ($ep == '/url/qr' && $method == 'POST') {
        $url = $input['url'] ?? '';
        if (!$url) sendJson(['error' => 'URL kosong'], 400);

        // 1. Ambil Gambar
        $qrApi = "https://api.qrserver.com/v1/create-qr-code/?size=300x300&data=" . urlencode($url);
        $rawQrData = file_get_contents($qrApi); // Masih binary mentah

        // 2. Simpan Sementara di Memori lalu Enkripsi
        $dirQr = 'uploads/qr/';
        if (!is_dir($dirQr)) mkdir($dirQr, 0777, true);
        
        $fileName = time() . '_qr.png';
        $savePath = $dirQr . $fileName;

        // Enkripsi manual (karena datanya dari variabel, bukan file di disk)
        $ivLength = openssl_cipher_iv_length($metode);
        $iv       = openssl_random_pseudo_bytes($ivLength);
        $encryptedData = openssl_encrypt($rawQrData, $metode, $kunciRahasia, 0, $iv);
        file_put_contents($savePath, $encryptedData);

        // 3. Database
        $uid = $_SESSION['user_id'];
        $ivHex = bin2hex($iv);
        $stmt = $conn->prepare("INSERT INTO file_storage (user_id, filename, file_type, file_path, iv_file) VALUES (?, ?, 'png', ?, ?)");
        $stmt->bind_param("isss", $uid, $fileName, $savePath, $ivHex);
        $stmt->execute();
        $lastId = $stmt->insert_id;

        logToDB('QR Code', $url, 'Generated & Encrypted');
        sendJson(['downloadUrl' => 'download.php?id=' . $lastId, 'fileName' => 'qr_code.png']);
    }

    // --- C. COMPRESS IMAGE (TERENKRIPSI) ---
    if ($ep == '/image/compress' && $method == 'POST') {
        if (!isset($_FILES['file'])) sendJson(['error' => 'File tidak ada'], 400);
        $f = $_FILES['file'];
        $ext = pathinfo($f['name'], PATHINFO_EXTENSION);
        
        // Load Gambar ke RAM
        $src = null;
        if (strtolower($ext) == 'jpg' || strtolower($ext) == 'jpeg') $src = @imagecreatefromjpeg($f['tmp_name']);
        elseif (strtolower($ext) == 'png') $src = @imagecreatefrompng($f['tmp_name']);
        
        if (!$src) sendJson(['error' => 'Format salah (harus jpg/png)'], 400);

        // Kompres ke Buffer (Memori)
        ob_start();
        imagejpeg($src, null, 70); // Quality 70%
        $compressedRaw = ob_get_clean(); 
        imagedestroy($src);

        $origSize = $f['size'];
        $newSize  = strlen($compressedRaw);

        // Enkripsi & Simpan
        $dirImg = 'uploads/img/';
        if (!is_dir($dirImg)) mkdir($dirImg, 0777, true);
        $fileName = time() . '_comp_' . $f['name'] . '.jpg'; // Output paksa jadi JPG
        $savePath = $dirImg . $fileName;

        $ivLength = openssl_cipher_iv_length($metode);
        $iv       = openssl_random_pseudo_bytes($ivLength);
        $encryptedData = openssl_encrypt($compressedRaw, $metode, $kunciRahasia, 0, $iv);
        file_put_contents($savePath, $encryptedData);

        // DB
        $uid = $_SESSION['user_id'];
        $ivHex = bin2hex($iv);
        $stmt = $conn->prepare("INSERT INTO file_storage (user_id, filename, file_type, file_path, iv_file) VALUES (?, ?, 'jpg', ?, ?)");
        $stmt->bind_param("isss", $uid, $fileName, $savePath, $ivHex);
        $stmt->execute();
        $lastId = $stmt->insert_id;

        logToDB('Compress', $f['name'], "Encrypted Size: " . round($newSize/1024) . "KB");
        sendJson([
            'downloadUrl' => 'download.php?id=' . $lastId,
            'originalSize' => $origSize,
            'compressedSize' => $newSize,
            'fileName' => 'compressed.jpg'
        ]);
    }

    // --- D. DOCX CONVERT (TERENKRIPSI) ---
    if ($ep == '/doc/convert' && $method == 'POST') {
        if (!isset($_FILES['file'])) sendJson(['error' => 'File tidak ada'], 400);
        $f = $_FILES['file'];
        
        $baseDir = 'uploads/';
        $dirDoc  = $baseDir . 'docx/';
        $dirPdf  = $baseDir . 'pdf/';
        $dirTemp = $baseDir . 'temp_raw/'; 
        if (!is_dir($dirDoc)) mkdir($dirDoc, 0777, true);
        if (!is_dir($dirPdf)) mkdir($dirPdf, 0777, true);
        if (!is_dir($dirTemp)) mkdir($dirTemp, 0777, true);

        $cleanName = time() . '_' . preg_replace('/[^A-Za-z0-9]/', '_', pathinfo($f['name'], PATHINFO_FILENAME));
        $tempDocPath = $dirTemp . $cleanName . '.docx';
        $finalDocPath = $dirDoc . $cleanName . '.docx';
        $finalPdfPath = $dirPdf . $cleanName . '.pdf';

        try {
            move_uploaded_file($f['tmp_name'], $tempDocPath);
            
            // Convert LibreOffice
            $loPath = '"D:\LibreOffice\program\soffice.exe"'; // Sesuaikan Path Windows kamu
            // Kalau error path, cek path LibreOffice di komputer kamu!
            
            $cmd = "$loPath --headless --convert-to pdf --outdir \"".realpath($dirTemp)."\" \"".realpath($tempDocPath)."\"";
            shell_exec($cmd);

            $tempPdfPath = $dirTemp . $cleanName . '.pdf';
            if (!file_exists($tempPdfPath)) throw new Exception("Gagal Convert.");

            // Enkripsi
            $ivDoc = enkripsiDanSimpan($tempDocPath, $finalDocPath, $kunciRahasia, $metode);
            $ivPdf = enkripsiDanSimpan($tempPdfPath, $finalPdfPath, $kunciRahasia, $metode);

            // DB
            $uid = $_SESSION['user_id'];
            $stmt = $conn->prepare("INSERT INTO file_storage (user_id, filename, file_type, file_path, iv_file) VALUES (?, ?, ?, ?, ?)");
            // Simpan DOCX Record
            // KODE BARU (PERBAIKAN)
            $t1 = 'docx'; 
            $fn1 = $cleanName . '.docx'; // Gunakan cleanName yang ada timestamp-nya
            $stmt->bind_param("issss", $uid, $fn1, $t1, $finalDocPath, $ivDoc); 
            $stmt->execute();
            // Simpan PDF Record
            $t2 = 'pdf'; $fn2 = $cleanName.'.pdf'; $stmt->bind_param("issss", $uid, $fn2, $t2, $finalPdfPath, $ivPdf); $stmt->execute();
            $lastId = $stmt->insert_id;

            // Hapus Temp
            unlink($tempDocPath);
            unlink($tempPdfPath);

            logToDB('DOCX Convert', $f['name'], 'Success Encrypted');
            sendJson(['downloadUrl' => 'download.php?id=' . $lastId, 'fileName' => $fn2]);

        } catch (Exception $e) {
            sendJson(['error' => $e->getMessage()], 500);
        }
    }
    exit;
}
?>

<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Multitools Aman</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <style>
        body { background-color: #f0f2f5; }
        .card { margin-bottom: 20px; box-shadow: 0 4px 6px rgba(0,0,0,0.1); border:none; }
        .card-header { background: #fff; font-weight: bold; padding: 15px; border-bottom: 1px solid #eee;}
    </style>
</head>
<body>
    <nav class="navbar navbar-dark bg-dark mb-4 p-3">
        <div class="container">
            <span class="navbar-brand">🛡️ Secure Tools</span>
            <a href="?logout=true" class="btn btn-sm btn-danger">Logout</a>
        </div>
    </nav>

    <div class="container mb-5">
        <div class="row">
            <div class="col-lg-6">
                <div class="card h-100">
                    <div class="card-header">📄 DOCX to PDF (Encrypted)</div>
                    <div class="card-body">
                        <form id="formDoc">
                            <input type="file" id="fileDoc" class="form-control mb-3" accept=".docx" required>
                            <button type="submit" class="btn btn-primary w-100">Convert & Secure</button>
                        </form>
                        <div id="resDoc" class="mt-3"></div>
                    </div>
                </div>
            </div>
            <div class="col-lg-6">
                <div class="card h-100">
                    <div class="card-header">🖼️ Image Compress (Encrypted)</div>
                    <div class="card-body">
                        <form id="formCompress">
                            <input type="file" id="fileImg" class="form-control mb-3" accept="image/*">
                            <button type="submit" class="btn btn-primary w-100">Compress & Secure</button>
                        </form>
                        <div id="resCompress" class="mt-3"></div>
                    </div>
                </div>
            </div>
        </div>
        <div class="row mt-4">
            <div class="col-lg-6">
                <div class="card h-100">
                    <div class="card-header">🔗 Secure QR Generator</div>
                    <div class="card-body">
                        <form id="formQr">
                            <input type="url" id="inpUrl" class="form-control mb-3" placeholder="https://..." required>
                            <button type="submit" class="btn btn-primary w-100">Generate QR</button>
                        </form>
                        <div id="resQr" class="mt-3 text-center"></div>
                    </div>
                </div>
            </div>
            <div class="col-lg-6">
                <div class="card h-100">
                    <div class="card-header">⚖️ BMI Calculator</div>
                    <div class="card-body">
                        <form id="formBmi">
                            <div class="row g-2 mb-3">
                                <div class="col"><input type="number" id="bHeight" class="form-control" placeholder="Tinggi (cm)" required></div>
                                <div class="col"><input type="number" id="bWeight" class="form-control" placeholder="Berat (kg)" required></div>
                            </div>
                            <button type="submit" class="btn btn-success w-100">Hitung</button>
                        </form>
                        <div id="resBmi" class="mt-3"></div>
                    </div>
                </div>
            </div>
        </div>

        <div class="card mt-4 border-primary">
            <div class="card-header bg-primary text-white">📂 File Manager (Download Asli & Hasil)</div>
            <div class="card-body">
                <div class="alert alert-warning py-2 small">
                    <i class="bi bi-info-circle"></i> Refresh halaman untuk melihat file yang baru saja di-generate.
                </div>
                <table class="table table-bordered table-striped table-hover">
                    <thead class="table-light">
                        <tr>
                            <th>Waktu Upload</th>
                            <th>File Asli (DOCX)</th>
                            <th>Hasil Convert (PDF)</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php
                        // 1. Ambil data DOCX dan PDF dari database
                        // Kita ambil semua file docx/pdf, urutkan dari yang terbaru
                        $uid = $_SESSION['user_id'];
                        $qFiles = $conn->query("SELECT * FROM file_storage WHERE user_id = '$uid' AND file_type IN ('docx', 'pdf') ORDER BY id DESC");
                        
                        $groups = [];
                        while($f = $qFiles->fetch_assoc()) {
                            // Kita kelompokkan berdasarkan nama file (tanpa ekstensi)
                            // Karena format nama: TIMESTAMP_JUDUL.ext, maka base namenya sama
                            $baseName = pathinfo($f['filename'], PATHINFO_FILENAME);
                            $groups[$baseName][$f['file_type']] = $f;
                        }

                        // 2. Tampilkan datanya
                        foreach($groups as $baseName => $pair) {
                            // Ambil info DOCX jika ada
                            $docx = isset($pair['docx']) ? $pair['docx'] : null;
                            // Ambil info PDF jika ada
                            $pdf  = isset($pair['pdf']) ? $pair['pdf'] : null;

                            // Jika dua-duanya kosong (tidak mungkin terjadi jika query benar), skip
                            if(!$docx && !$pdf) continue;

                            echo "<tr>";
                            
                            // Kolom Waktu (Ambil dari timestamp di nama file)
                            // Format nama file: 17088899_NamaFile
                            $parts = explode('_', $baseName);
                            $ts = is_numeric($parts[0]) ? date("d M Y H:i", $parts[0]) : "-";
                            echo "<td><small>$ts</small></td>";

                            // Kolom DOCX
                            echo "<td>";
                            if($docx) {
                                echo "📄 " . htmlspecialchars($docx['filename']) . "<br>";
                                echo "<a href='download.php?id={$docx['id']}' class='btn btn-sm btn-outline-primary mt-1'>⬇ Download Asli</a>";
                            } else {
                                echo "<span class='text-muted'>- File hilang -</span>";
                            }
                            echo "</td>";

                            // Kolom PDF
                            echo "<td>";
                            if($pdf) {
                                echo "📕 " . htmlspecialchars($pdf['filename']) . "<br>";
                                echo "<a href='download.php?id={$pdf['id']}' class='btn btn-sm btn-success mt-1'>⬇ Download PDF</a>";
                            } else {
                                echo "<span class='badge bg-warning text-dark'>Pending / Gagal</span>";
                            }
                            echo "</td>";

                            echo "</tr>";
                        }
                        
                        if(empty($groups)) {
                            echo "<tr><td colspan='3' class='text-center'>Belum ada file konversi.</td></tr>";
                        }
                        ?>
                    </tbody>
                </table>
            </div>
        </div>
        <div class="card mt-4">
            <div class="card-header">📜 Log Sistem (Teknis)</div>
            <div class="card-body">
                <table class="table table-sm table-hover text-muted" style="font-size: 0.85rem;">
                    <thead><tr><th>ID</th><th>Tool</th><th>Input</th><th>Result</th></tr></thead>
                    <tbody>
                        <?php
                        $uid = $_SESSION['user_id'];
                        $h = $conn->query("SELECT * FROM history_penggunaan WHERE user_id = '$uid' ORDER BY id DESC LIMIT 5");
                        while($r = $h->fetch_assoc()) echo "<tr><td>{$r['id']}</td><td>{$r['tool_name']}</td><td>{$r['input_detail']}</td><td>{$r['result_detail']}</td></tr>";
                        ?>
                    </tbody>
                </table>
            </div>
        </div>
    </div>

    <script>
    const API = "?endpoint=";

    // 1. BMI JS
    document.getElementById('formBmi').onsubmit = async (e) => {
        e.preventDefault();
        const resDiv = document.getElementById('resBmi');
        resDiv.innerHTML = "Menghitung...";
        const req = await fetch(API + '/calc/bmi', {
            method: 'POST', body: JSON.stringify({heightCm: document.getElementById('bHeight').value, weightKg: document.getElementById('bWeight').value})
        });
        const data = await req.json();
        resDiv.innerHTML = `<div class="alert alert-info text-center">BMI: <b>${data.bmi}</b> (${data.category})</div>`;
    };

    // 2. QR JS (Updated for Encryption)
    document.getElementById('formQr').onsubmit = async (e) => {
        e.preventDefault();
        const resDiv = document.getElementById('resQr');
        resDiv.innerHTML = "Generating & Encrypting...";
        try {
            const req = await fetch(API + '/url/qr', {
                method: 'POST', body: JSON.stringify({url: document.getElementById('inpUrl').value})
            });
            const data = await req.json();
            if(data.error) throw new Error(data.error);
            
            // Tampilkan Gambar lewat download.php
            resDiv.innerHTML = `
                <img src="${data.downloadUrl}" style="max-width:200px" class="img-thumbnail mb-2"><br>
                <a href="${data.downloadUrl}" download="${data.fileName}" class="btn btn-success btn-sm">Download QR</a>`;
        } catch(err) { resDiv.innerHTML = err.message; }
    };

    // 3. Compress JS (Updated for Encryption)
    document.getElementById('formCompress').onsubmit = async (e) => {
        e.preventDefault();
        const f = document.getElementById('fileImg').files[0];
        if(!f) return;
        const fd = new FormData(); fd.append('file', f);
        const resDiv = document.getElementById('resCompress');
        resDiv.innerHTML = "Compressing & Encrypting...";

        try {
            const req = await fetch(API + '/image/compress', { method: 'POST', body: fd });
            const data = await req.json();
            if(data.error) throw new Error(data.error);
            
            resDiv.innerHTML = `
                <div class="alert alert-success py-1">Saved: ${((data.originalSize - data.compressedSize)/1024).toFixed(1)} KB</div>
                <img src="${data.downloadUrl}" style="max-height:150px" class="img-thumbnail"><br>
                <a href="${data.downloadUrl}" download="${data.fileName}" class="btn btn-success btn-sm mt-2">Download</a>`;
        } catch(err) { resDiv.innerHTML = err.message; }
    };

    // 4. DOCX JS (Updated for Encryption)
    document.getElementById('formDoc').onsubmit = async (e) => {
        e.preventDefault();
        const f = document.getElementById('fileDoc').files[0];
        if(!f) return;
        const fd = new FormData(); fd.append('file', f);
        const resDiv = document.getElementById('resDoc');
        resDiv.innerHTML = "Uploading, Converting & Encrypting...";

        try {
            const req = await fetch(API + '/doc/convert', { method: 'POST', body: fd });
            const data = await req.json();
            if (data.error) throw new Error(data.error);

            // Karena file terenkripsi, tidak bisa pakai <embed>, harus download
            resDiv.innerHTML = `
                <div class="alert alert-success">Berhasil Diamankan!</div>
                <a href="${data.downloadUrl}" class="btn btn-success w-100">⬇️ Download PDF (Dekripsi Otomatis)</a>`;
        } catch (err) { resDiv.innerHTML = `<div class="text-danger">${err.message}</div>`; }
    }
    </script>
</body>
</html>