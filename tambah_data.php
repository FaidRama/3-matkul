<link rel="stylesheet" href="assets/style.css">

<form action="proses_tambah.php" method="POST">
    <label>Nama Barang:</label>
    <input type="text" name="nama_barang" required>
    
    <label>Harga:</label>
    <input type="number" name="harga" required>
    
    <button type="submit" name="simpan">Simpan</button>
</form>