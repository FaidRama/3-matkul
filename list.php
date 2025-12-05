<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Daftar File Konversi</title>
    <style>
        body { font-family: sans-serif; padding: 20px; background: #f4f4f4; }
        .container { max-width: 900px; margin: 0 auto; background: white; padding: 20px; border-radius: 8px; box-shadow: 0 0 10px rgba(0,0,0,0.1); }
        h2 { border-bottom: 2px solid #ddd; padding-bottom: 10px; }
        table { width: 100%; border-collapse: collapse; margin-top: 20px; }
        th, td { padding: 12px; text-align: left; border-bottom: 1px solid #ddd; }
        th { background-color: #007bff; color: white; }
        tr:hover { background-color: #f1f1f1; }
        .btn { text-decoration: none; padding: 8px 12px; border-radius: 4px; font-size: 14px; margin-right: 5px; }
        .btn-blue { background: #007bff; color: white; }
        .btn-green { background: #28a745; color: white; }
        .btn-grey { background: #6c757d; color: white; pointer-events: none; } /* Untuk tombol mati */
        .header-nav { display: flex; justify-content: space-between; align-items: center; }
    </style>
</head>
<body>

<div class="container">
    <div class="header-nav">
        <h2>📂 Riwayat Konversi</h2>
        <a href="index.php" class="btn btn-blue">⬅ Upload Baru</a>
    </div>

    <table>
        <thead>
            <tr>
                <th>No</th>
                <th>Nama File Asli</th>
                <th>Status</th>
                <th>Aksi</th>
            </tr>
        </thead>
        <tbody>
            <?php
            // Folder penyimpanan
            $uploadDir = 'uploads/';
            $resultDir = 'results/';

            // Cek apakah folder ada, jika tidak buat dulu biar gak error
            if (!is_dir($uploadDir)) mkdir($uploadDir);
            if (!is_dir($resultDir)) mkdir($resultDir);

            // Ambil semua file di folder uploads
            $files = scandir($uploadDir);
            
            // Hapus '.' dan '..' dari hasil scan
            $files = array_diff($files, array('.', '..'));

            $no = 1;
            // Jika folder kosong
            if (count($files) < 1) {
                echo "<tr><td colspan='4' style='text-align:center;'>Belum ada file yang diupload.</td></tr>";
            } else {
                foreach ($files as $file) {
                    // Dapatkan nama file tanpa ekstensi (misal: "dokumen" dari "dokumen.docx")
                    $filenameWithoutExt = pathinfo($file, PATHINFO_FILENAME);
                    
                    // Prediksi nama file hasil (PDF)
                    $pdfFile = $filenameWithoutExt . ".pdf";
                    $pdfPath = $resultDir . $pdfFile;

                    echo "<tr>";
                    echo "<td>" . $no++ . "</td>";
                    echo "<td><strong>" . htmlspecialchars($file) . "</strong></td>";
                    
                    // Cek apakah file PDF hasil convert sudah ada di folder results
                    if (file_exists($pdfPath)) {
                        echo "<td style='color: green;'>✅ Selesai</td>";
                        echo "<td>
                                <a href='$uploadDir$file' class='btn btn-blue' download>⬇ Asli</a>
                                <a href='$pdfPath' class='btn btn-green' download>⬇ PDF</a>
                              </td>";
                    } else {
                        echo "<td style='color: orange;'>⏳ Pending / Gagal</td>";
                        echo "<td>
                                <a href='$uploadDir$file' class='btn btn-blue' download>⬇ Asli</a>
                                <span class='btn btn-grey'>Belum ada PDF</span>
                              </td>";
                    }
                    echo "</tr>";
                }
            }
            ?>
        </tbody>
    </table>
</div>

</body>
</html>