<?php
    session_start();
    include '../Auth/connect.php';

    if (!isset($_SESSION['id_user']) || $_SESSION['role'] !== 'pakar') {
        header("Location: ../Auth/login.php");
        exit();
    }

    // Mengambil data penyakit berdasarkan kode
    if (!isset($_GET['kode'])) {
        echo "Kode gejala tidak ditemukan!";
        exit();
    }

    $kode = $_GET['kode'];

    // Proses hapus data gejala
    $stmt = $conn->prepare("DELETE FROM gejala WHERE kode_gejala = ?");
    $stmt->bind_param("s", $kode);
    $stmt->execute();

    echo "<script>
        alert('Gejala berhasil dihapus!');
        window.location.href = 'gejala.php';
    </script>";
    exit();
?>