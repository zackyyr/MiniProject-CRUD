<?php
$host = "localhost"; // Server database (default: localhost)
$user = "root"; // Username MySQL (default: root)
$pass = ""; // Password MySQL (kosong kalau di XAMPP)
$dbname = ""; // Nama database kalian

// Buat koneksi
$db = mysqli_connect($host, $user, $pass, $dbname);

// Cek koneksi
if(!$db) { 
    die("Koneksi Gagal : ". mysqli_connect_error());
}
?>
