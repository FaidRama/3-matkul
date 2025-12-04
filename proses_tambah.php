<?php
include 'config/koneksi.php'; // Panggil koneksi

if(isset($_POST['simpan'])) {
    // Ambil data dari input yang punya name="..."
    $nama = $_POST['nama_barang'];
    $harga = $_POST['harga'];

    // Masukkan ke DB (Query SQL biasa)
    $query = "INSERT INTO tabel_barang (nama, harga) VALUES ('$nama', '$harga')";
    
    if(mysqli_query($koneksi, $query)) {
        // Jika sukses, alihkan kembali ke index
        header("Location: index.php");
    } else {
        echo "Gagal: " . mysqli_error($koneksi);
    }
}
?>