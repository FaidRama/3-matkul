<?php
// download.php
session_start();
// Pastikan user login biar gak sembarang orang bisa download
if (!isset($_SESSION['user_id'])) die("Akses ditolak.");

$conn = mysqli_connect("127.0.0.1", "root", "", "db_alat_online");

// KUNCI RAHASIA (Harus sama persis dengan index.php)
$kunciRahasia = "KelompokKamiPalingKeren2025!!"; 
$metode       = "AES-256-CBC";

if (isset($_GET['id'])) {
    $id = intval($_GET['id']);
    
    $query = mysqli_query($conn, "SELECT * FROM file_storage WHERE id = $id");
    $data  = mysqli_fetch_assoc($query);

    if ($data) {
        $filePath = $data['file_path'];
        $ivHex    = $data['iv_file'];
        $fileName = $data['filename'];
        $type     = $data['file_type']; // docx, pdf, png, atau jpg

        if (file_exists($filePath)) {
            // 1. Baca & Dekripsi
            $fileEncrypted = file_get_contents($filePath);
            $iv = hex2bin($ivHex);
            $fileDecrypted = openssl_decrypt($fileEncrypted, $metode, $kunciRahasia, 0, $iv);
            
            // 2. Tentukan Header Mime-Type Otomatis
            $mime = 'application/octet-stream'; // Default
            if ($type == 'pdf') $mime = 'application/pdf';
            if ($type == 'png') $mime = 'image/png';
            if ($type == 'jpg' || $type == 'jpeg') $mime = 'image/jpeg';
            if ($type == 'docx') $mime = 'application/vnd.openxmlformats-officedocument.wordprocessingml.document';

            // 3. Kirim ke Browser
            header('Content-Description: File Transfer');
            header('Content-Type: ' . $mime);
            // "inline" berarti file akan terbuka di browser (preview), kalau mau paksa download ganti jadi "attachment"
            header('Content-Disposition: inline; filename="'.basename($fileName).'"');
            header('Expires: 0');
            header('Cache-Control: must-revalidate');
            header('Pragma: public');
            header('Content-Length: ' . strlen($fileDecrypted));
            
            echo $fileDecrypted;
            exit;
        }
    }
}
http_response_code(404);
echo "File tidak ditemukan atau rusak.";
?>