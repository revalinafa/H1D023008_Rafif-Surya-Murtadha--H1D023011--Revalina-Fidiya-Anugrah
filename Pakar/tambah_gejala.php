<?php
    session_start();
    include '../Auth/connect.php';

    if (!isset($_SESSION['id_user']) || $_SESSION['role'] !== 'pakar') {
        header("Location: ../Auth/login.php");
        exit();
    }

    // Mengambil data gejala (kode dan nama) dari form yang diisi
    if ($_SERVER['REQUEST_METHOD'] == 'POST') {
        $kode = trim($_POST['kode_gejala']);
        $nama = trim($_POST['nama_gejala']);

        // Insert data gejala (kode dan nama gejala) ke database
        $sql = "INSERT INTO gejala (kode_gejala, nama_gejala) VALUES (?, ?)";
        $stmt = $conn->prepare($sql);
        $stmt->bind_param("ss", $kode, $nama);

        if ($stmt->execute()) {
            echo "<script>
                alert('Gejala berhasil ditambahkan!');
                window.location.href = 'gejala.php';
            </script>";
            exit();
        } else {
            echo "Gagal tambah gejala: " . $conn->error;
        }
    }
?>

<!DOCTYPE html>
<html lang="id">
    <head>
        <meta charset="UTF-8">
        <title>Tambah Gejala</title>
        <script src="https://cdn.tailwindcss.com"></script>
    </head>
    <body class="bg-purple-50 min-h-screen">

        <!-- Form Tambah Gejala -->
        <div class="max-w-xl mx-auto mt-8 bg-white p-6 rounded shadow">
            <h2 class="text-2xl font-bold mb-4 text-purple-800">Tambah Gejala</h2>
            <form method="POST" class="space-y-4">
                <div>
                    <label class="block text-sm font-medium text-gray-700">Kode Gejala:</label>
                    <input type="text" name="kode_gejala" required class="w-full border border-gray-300 px-3 py-2 rounded">
                </div>
                <div>
                    <label class="block text-sm font-medium text-gray-700">Nama Gejala:</label>
                    <input type="text" name="nama_gejala" required class="w-full border border-gray-300 px-3 py-2 rounded">
                </div>
                <div class="flex gap-2">
                    <button type="submit" class="bg-green-500 hover:bg-green-600 text-white px-4 py-2 rounded">Simpan</button>
                    <a href="gejala.php" class="bg-gray-500 hover:bg-gray-600 text-white px-4 py-2 rounded">Kembali</a>
                </div>
            </form>
        </div>

    </body>
</html>