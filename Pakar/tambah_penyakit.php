<?php
    session_start();
    include '../Auth/connect.php';

    if (!isset($_SESSION['id_user']) || $_SESSION['role'] !== 'pakar') {
        header("Location: ../Auth/login.php");
        exit();
    }

    // Mengambil data penyakit (kode, nama, deskripsi, solusi) dari form yang diisi
    if ($_SERVER['REQUEST_METHOD'] == 'POST') {
        $kode = $_POST['kode_penyakit'];
        $nama = $_POST['nama_penyakit'];
        $deskripsi = $_POST['deskripsi'];
        $solusi = $_POST['solusi'];

        // Insert data penyakit (kode, nama, deskripsi, solusi) ke dalam database
        $sql = "INSERT INTO penyakit (kode_penyakit, nama_penyakit, deskripsi, solusi) VALUES (?, ?, ?, ?)";
        $stmt = $conn->prepare($sql);
        $stmt->bind_param("ssss", $kode, $nama, $deskripsi, $solusi);

        if ($stmt->execute()) {
            // Mengarahkan ke penyakit.php dengan parameter success
            header("Location: penyakit.php?success=tambah");
            exit();
        } else {
            echo "Gagal tambah penyakit: " . $conn->error;
        }
    }
?>

<!DOCTYPE html>
<html lang="id">
    <head>
        <meta charset="UTF-8">
        <title>Tambah Penyakit</title>
        <script src="https://cdn.tailwindcss.com"></script>
    </head>
    <body class="bg-purple-50 min-h-screen">

        <!-- Form Tambah Penyakit -->
        <div class="max-w-2xl mx-auto mt-8 bg-white p-6 rounded shadow">
            <h2 class="text-2xl font-bold mb-4 text-purple-800">Tambah Penyakit</h2>
            <form method="POST" class="space-y-4">
                <div>
                    <label class="block text-gray-700 font-medium">Kode Penyakit:</label>
                    <input type="text" name="kode_penyakit" required class="w-full border border-gray-300 px-3 py-2 rounded">
                </div>
                <div>
                    <label class="block text-gray-700 font-medium">Nama Penyakit:</label>
                    <input type="text" name="nama_penyakit" required class="w-full border border-gray-300 px-3 py-2 rounded">
                </div>
                <div>
                    <label class="block text-gray-700 font-medium">Deskripsi:</label>
                    <textarea name="deskripsi" rows="3" required class="w-full border border-gray-300 px-3 py-2 rounded"></textarea>
                </div>
                <div>
                    <label class="block text-gray-700 font-medium">Solusi:</label>
                    <textarea name="solusi" rows="3" required class="w-full border border-gray-300 px-3 py-2 rounded"></textarea>
                </div>
                <div class="flex gap-2">
                    <button type="submit" class="bg-green-500 hover:bg-purple-600 text-white px-4 py-2 rounded">Simpan</button>
                    <a href="penyakit.php" class="bg-gray-500 hover:bg-purple-600 text-white px-4 py-2 rounded">Kembali</a>
                </div>
            </form>
        </div>

    </body>
</html>