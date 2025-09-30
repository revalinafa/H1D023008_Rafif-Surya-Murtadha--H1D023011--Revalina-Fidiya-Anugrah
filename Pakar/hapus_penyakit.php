<?php
    session_start();
    include '../Auth/connect.php';

    if (!isset($_SESSION['id_user']) || $_SESSION['role'] !== 'pakar') {
        header("Location: ../Auth/login.php");
        exit();
    }

    // Mengambil data penyakit berdasarkan kode
    if (!isset($_GET['kode'])) {
        echo "Kode penyakit tidak ditemukan!";
        exit();
    }

    // Proses hapus data penyakit
    $kode = $_GET['kode'];
    $stmt = $conn->prepare("DELETE FROM penyakit WHERE kode_penyakit = ?");
    $stmt->bind_param("s", $kode);
    $stmt->execute();

    // Mengarahkan ke penyakit.php dengan parameter success
    header("Location: penyakit.php?success=hapus");
    exit();
?>