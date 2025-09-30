<?php
    error_reporting(E_ALL & ~E_WARNING & ~E_NOTICE);

    $host = "localhost";
    $username = "root";
    $password = "";
    $database = "sispak";

    $conn = new mysqli($host, $username, $password, $database);

    if ($conn->connect_error) {
        die("Koneksi gagal: " . $conn->connect_error);
    } else {
        // echo "Koneksi berhasil!";
    }
?>
