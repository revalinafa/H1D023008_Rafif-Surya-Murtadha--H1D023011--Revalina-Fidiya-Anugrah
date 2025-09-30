<?php
    session_start();
    include '../Auth/connect.php';

    if (!isset($_SESSION['id_user']) || $_SESSION['role'] !== 'admin') {
        header("Location: ../Auth/login.php");
        exit();
    }

    // Mengambil data user berdasarkan ID
    $id = $_GET['id'] ?? null;
    if (!$id) {
        echo "ID tidak valid.";
        exit();
    }

    // Hapus semua riwayat konsultasi milik user terlebih dahulu
    $stmt1 = $conn->prepare("DELETE FROM riwayat_konsultasi WHERE id_user = ?");
    $stmt1->bind_param("i", $id);
    $stmt1->execute();
    $stmt1->close();

    // Proses hapus data user
    $stmt = $conn->prepare("DELETE FROM user WHERE id_user = ?");
    $stmt->bind_param("i", $id);

    if ($stmt->execute()) {
        // Redirect ke user.php dengan parameter success
        header("Location: user.php?success=delete");
        exit();
    } else {
        echo "Gagal menghapus user: " . $conn->error;
    }
?>