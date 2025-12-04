<?php
session_start();
// ==========================================
// 1. KONEKSI DATABASE (Manual)
// ==========================================
$host = "localhost";
$user = "root";
$pass = "";
$db   = "db_alat_online"; // Pastikan DB ini sudah kamu buat manual!

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

if ($conn->connect_error) {
    die("Error: Lupa bikin database ya? " . $conn->connect_error);
}

// Helper: Simpan ke DB
function logToDB($tool, $input, $output) {
    global $conn;
    $uid = $_SESSION['user_id'];
    $stmt = $conn->prepare("INSERT INTO history_penggunaan (tool_name, input_detail, result_detail) VALUES (?, ?, ?)");
    $stmt->bind_param("sss", $tool, $input, $output);
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
// 2. API ENDPOINTS (Logic)
// ==========================================
if (isset($_GET['endpoint'])) {
    $ep = $_GET['endpoint'];
    $method = $_SERVER['REQUEST_METHOD'];
    $input = json_decode(file_get_contents('php://input'), true);

    // --- BMI TOOL ---
    if ($ep == '/calc/bmi' && $method == 'POST') {
        $h = $input['heightCm'] ?? 0;
        $w = $input['weightKg'] ?? 0;
        if (!$h || !$w) sendJson(['error' => 'Input salah'], 400);

        $bmi = round($w / (($h/100) * ($h/100)), 2);
        $cat = ($bmi < 18.5) ? 'Underweight' : (($bmi < 25) ? 'Normal' : 'Overweight');
        
        logToDB('BMI', "H:$h W:$w", "BMI:$bmi");
        sendJson(['bmi' => $bmi, 'category' => $cat, 'message' => 'Hitungan selesai']);
    }

    // --- QR TOOL ---
    if ($ep == '/url/qr' && $method == 'POST') {
        $url = $input['url'] ?? '';
        if (!$url) sendJson(['error' => 'URL kosong'], 400);

        $qrApi = "https://api.qrserver.com/v1/create-qr-code/?size=300x300&data=" . urlencode($url);
        $b64 = base64_encode(file_get_contents($qrApi));
        
        logToDB('QR Code', $url, 'Generated');
        sendJson(['dataUrl' => 'data:image/png;base64,'.$b64, 'fileName' => 'qr.png']);
    }

    // --- COMPRESS IMAGE ---
    if ($ep == '/image/compress' && $method == 'POST') {
        if (!isset($_FILES['file'])) sendJson(['error' => 'File tidak ada'], 400);
        
        $f = $_FILES['file'];
        $ext = pathinfo($f['name'], PATHINFO_EXTENSION);
        $src = null;

        if (strtolower($ext) == 'jpg' || strtolower($ext) == 'jpeg') $src = imagecreatefromjpeg($f['tmp_name']);
        elseif (strtolower($ext) == 'png') $src = imagecreatefrompng($f['tmp_name']);
        
        if (!$src) sendJson(['error' => 'Format file tidak didukung server'], 400);

        ob_start();
        imagejpeg($src, null, 70); // Quality 70
        $data = ob_get_clean();
        $b64 = base64_encode($data);
        
        $ratio = round((strlen($data) / $f['size']) * 100, 1);
        logToDB('Compress', $f['name'], "Ratio: $ratio%");
        
        sendJson([
            'dataUrl' => 'data:image/jpeg;base64,'.$b64, 
            'originalSize' => $f['size'], 
            'compressedSize' => strlen($data),
            'fileName' => 'compressed.jpg'
        ]);
    }
    exit;
}
?>

<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Multitools Utility</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <style>
        /* Styling tambahan biar mirip React App.css temanmu */
        body { background-color: #f8f9fa; }
        .card { margin-bottom: 20px; border-radius: 8px; box-shadow: 0 2px 4px rgba(0,0,0,0.1); }
        .card-header { font-weight: bold; background-color: #fff; border-bottom: 1px solid #eee; padding: 15px; }
        .card-body { padding: 20px; }
    </style>
</head>
<body>

    <nav class="navbar navbar-dark bg-dark mb-4">
        <div class="container">
            <span class="navbar-brand">Multitools Utility</span>
            <div class="d-flex text-white align-items-center">
                <span class="me-3">Halo, <?php echo $_SESSION['username']; ?></span>
                <a href="?logout=true" class="btn btn-sm btn-danger">Logout</a>
            </div>
        </div>
    </nav>

    <div class="container mb-5">
        
        <div class="row">
            <div class="col-lg-6">
                <div class="card h-100">
                    <div class="card-header">DOCX ↔ PDF Converter</div>
                    <div class="card-body">
                        <div class="alert alert-secondary">
                            Fitur ini butuh konfigurasi Server VPS (LibreOffice).
                        </div>
                        <input type="file" class="form-control mb-3" disabled>
                        <button class="btn btn-primary disabled">Convert</button>
                    </div>
                </div>
            </div>

            <div class="col-lg-6">
                <div class="card h-100">
                    <div class="card-header">Image Compression</div>
                    <div class="card-body">
                        <form id="formCompress">
                            <div class="mb-3">
                                <input type="file" id="fileImg" class="form-control" accept="image/jpeg,image/png">
                            </div>
                            <button type="submit" class="btn btn-primary">Compress</button>
                        </form>
                        <div id="resCompress" class="mt-3"></div>
                    </div>
                </div>
            </div>
        </div>

        <div class="row">
            <div class="col-lg-6">
                <div class="card h-100">
                    <div class="card-header">URL → QR Code</div>
                    <div class="card-body">
                        <form id="formQr">
                            <div class="mb-3">
                                <input type="url" id="inpUrl" class="form-control" placeholder="https://example.com" required>
                            </div>
                            <button type="submit" class="btn btn-primary">Generate QR</button>
                        </form>
                        <div id="resQr" class="mt-3 text-center"></div>
                    </div>
                </div>
            </div>

            <div class="col-lg-6">
                <div class="card h-100">
                    <div class="card-header">Audio Conversion</div>
                    <div class="card-body">
                        <div class="alert alert-secondary">
                            Fitur ini butuh konfigurasi Server VPS (FFMpeg).
                        </div>
                        <input type="file" class="form-control mb-3" disabled>
                        <div class="row mb-3">
                            <div class="col-6"><select class="form-select" disabled><option>MP3</option></select></div>
                            <div class="col-6"><select class="form-select" disabled><option>192kbps</option></select></div>
                        </div>
                        <button class="btn btn-primary disabled">Convert</button>
                    </div>
                </div>
            </div>
        </div>

        <div class="row">
            <div class="col-lg-6">
                <div class="card h-100">
                    <div class="card-header">BMI Calculator</div>
                    <div class="card-body">
                        <form id="formBmi">
                            <div class="row g-3 mb-3">
                                <div class="col-md-6">
                                    <label class="form-label">Height (cm)</label>
                                    <input type="number" id="bHeight" class="form-control" required>
                                </div>
                                <div class="col-md-6">
                                    <label class="form-label">Weight (kg)</label>
                                    <input type="number" id="bWeight" class="form-control" required>
                                </div>
                            </div>
                            <button type="submit" class="btn btn-primary">Calculate</button>
                        </form>
                        <div id="resBmi" class="mt-3"></div>
                    </div>
                </div>
            </div>
        </div>

        <div class="row mt-4">
            <div class="col-12">
                <div class="card">
                    <div class="card-header">Riwayat Penggunaan (Database)</div>
                    <div class="card-body">
                        <table class="table table-striped">
                            <thead><tr><th>ID</th><th>Tool</th><th>Input</th><th>Hasil</th></tr></thead>
                            <tbody>
                                <?php
                                $hist = $conn->query("SELECT * FROM history_penggunaan ORDER BY id DESC LIMIT 5");
                                while($row = $hist->fetch_assoc()) {
                                    echo "<tr>
                                        <td>{$row['id']}</td>
                                        <td>{$row['tool_name']}</td>
                                        <td>{$row['input_detail']}</td>
                                        <td>{$row['result_detail']}</td>
                                    </tr>";
                                }
                                ?>
                            </tbody>
                        </table>
                        <small class="text-muted">*Refresh halaman untuk update tabel</small>
                    </div>
                </div>
            </div>
        </div>

    </div>

    <script>
    const API = "?endpoint=";

    // 1. Logic BMI
    document.getElementById('formBmi').onsubmit = async (e) => {
        e.preventDefault();
        const h = document.getElementById('bHeight').value;
        const w = document.getElementById('bWeight').value;
        
        const req = await fetch(API + '/calc/bmi', {
            method: 'POST', body: JSON.stringify({heightCm: h, weightKg: w})
        });
        const data = await req.json();
        document.getElementById('resBmi').innerHTML = 
            `<div class="alert alert-info">BMI: <b>${data.bmi}</b> (${data.category})</div>`;
    };

    // 2. Logic QR
    document.getElementById('formQr').onsubmit = async (e) => {
        e.preventDefault();
        const u = document.getElementById('inpUrl').value;
        document.getElementById('resQr').innerText = "Loading...";
        
        const req = await fetch(API + '/url/qr', {
            method: 'POST', body: JSON.stringify({url: u})
        });
        const data = await req.json();
        document.getElementById('resQr').innerHTML = 
            `<img src="${data.dataUrl}" style="max-width:200px" class="img-thumbnail"><br>
             <a href="${data.dataUrl}" download="${data.fileName}" class="btn btn-sm btn-success mt-2">Download</a>`;
    };

    // 3. Logic Compress
    document.getElementById('formCompress').onsubmit = async (e) => {
        e.preventDefault();
        const f = document.getElementById('fileImg').files[0];
        if(!f) return alert("Pilih file!");
        
        const fd = new FormData();
        fd.append('file', f);
        document.getElementById('resCompress').innerText = "Processing...";

        try {
            const req = await fetch(API + '/image/compress', { method: 'POST', body: fd });
            const data = await req.json();
            
            if(data.error) throw new Error(data.error);
            document.getElementById('resCompress').innerHTML = 
                `<div class="alert alert-success">
                    Original: ${(data.originalSize/1024).toFixed(1)} KB <br>
                    Compressed: ${(data.compressedSize/1024).toFixed(1)} KB
                 </div>
                 <img src="${data.dataUrl}" style="max-height:150px" class="img-thumbnail"><br>
                 <a href="${data.dataUrl}" download="${data.fileName}" class="btn btn-sm btn-success mt-2">Download</a>`;
        } catch(err) {
            document.getElementById('resCompress').innerText = "Error: " + err.message;
        }
    };
    </script>
</body>
</html>