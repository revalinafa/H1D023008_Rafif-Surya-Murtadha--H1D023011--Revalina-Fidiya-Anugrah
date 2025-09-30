<?php
    session_start();
    include '../Auth/connect.php';

    if (!isset($_SESSION['id_user']) || $_SESSION['role'] !== 'pakar') {
        header("Location: ../Auth/login.php");
        exit();
    }

    if (!isset($_GET['kode'])) {
        echo "Kode gejala tidak ditemukan!";
        exit();
    }

    // Mengambil data gejala berdasarkan kode
    $kode = $_GET['kode'];
    $stmt = $conn->prepare("SELECT * FROM gejala WHERE kode_gejala = ?");
    $stmt->bind_param("s", $kode);
    $stmt->execute();
    $result = $stmt->get_result();
    $gejala = $result->fetch_assoc();

    if (!$gejala) {
        echo "Data gejala tidak ditemukan!";
        exit();
    }

    // Proses update data gejala (apabila berhasil diarahkan ke halaman gejala.php)
    if ($_SERVER['REQUEST_METHOD'] === 'POST') {
        $nama = $_POST['nama_gejala'];
        $stmt = $conn->prepare("UPDATE gejala SET nama_gejala=? WHERE kode_gejala=?");
        $stmt->bind_param("ss", $nama, $kode);
        $stmt->execute();

        echo "<script>
            alert('Gejala berhasil diedit!');
            window.location.href = 'gejala.php';
        </script>";
        exit();
    }
?>

<!DOCTYPE html>
<html lang="id">
    <head>
        <meta charset="UTF-8">
        <title>Edit Gejala</title>
        <script src="https://cdn.tailwindcss.com"></script>
    </head>
    <body class="bg-gray-50 p-6">
        <div class="max-w-xl mx-auto bg-white p-6 rounded shadow">
            <h2 class="text-2xl font-bold mb-4 text-purple-800">Edit Gejala</h2>
            <form method="post" class="space-y-4">
                <div>
                    <label class="block text-gray-700 font-semibold">Kode Gejala:</label>
                    <p class="text-gray-900"><?= htmlspecialchars($gejala['kode_gejala']); ?></p>
                </div>
                <div>
                    <label class="block text-gray-700 font-semibold">Nama Gejala:</label>
                    <input type="text" name="nama_gejala" value="<?= htmlspecialchars($gejala['nama_gejala']); ?>" required class="w-full border border-gray-300 px-3 py-2 rounded">
                </div>
                <div class="flex gap-2">
                    <button type="submit" class="bg-green-500 hover:bg-green-600 text-white px-4 py-2 rounded">Simpan Perubahan</button>
                    <a href="gejala.php" class="bg-gray-500 hover:bg-gray-600 text-white px-4 py-2 rounded">Kembali</a>
                </div>
            </form>
        </div>
    </body>
</html>