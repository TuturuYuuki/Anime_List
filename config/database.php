<?php
// config/database.php
$host = "localhost";
$user = "root";
$pass = "";
$db   = "anime_waifu_vault";

$conn = mysqli_connect($host, $user, $pass, $db);

if (!$conn) {
    die("Koneksi gagal: " . mysqli_connect_error());
}
?>