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

    $kode = $_GET['kode'];
    $stmt = $conn->prepare("SELECT * FROM penyakit WHERE kode_penyakit = ?");
    $stmt->bind_param("s", $kode);
    $stmt->execute();
    $result = $stmt->get_result();
    $penyakit = $result->fetch_assoc();

    if (!$penyakit) {
        echo "Data penyakit tidak ditemukan!";
        exit();
    }

    // Proses update data penyakit (apabila berhasil diarahkan ke halaman penyakit.php)
    if ($_SERVER['REQUEST_METHOD'] === 'POST') {
        $nama = $_POST['nama_penyakit'];
        $deskripsi = $_POST['deskripsi'];
        $solusi = $_POST['solusi'];

        $stmt = $conn->prepare("UPDATE penyakit SET nama_penyakit=?, deskripsi=?, solusi=? WHERE kode_penyakit=?");
        $stmt->bind_param("ssss", $nama, $deskripsi, $solusi, $kode);
        $stmt->execute();

        // Mengarahkan ke penyakit.php dengan parameter success
        header("Location: penyakit.php?success=edit");
        exit();
    }
?>

<!DOCTYPE html>
<html lang="id">
    <head>
        <meta charset="UTF-8">
        <title>Edit Penyakit</title>
        <script src="https://cdn.tailwindcss.com"></script>
    </head>
    <body class="bg-gray-50 p-6">
        <div class="max-w-2xl mx-auto bg-white p-6 rounded shadow border border-purple-100">
            <h2 class="text-2xl font-bold mb-4 text-purple-700">Edit Penyakit</h2>
            <form method="post" class="space-y-4">
                <div>
                    <label class="block text-purple-800 font-medium mb-1">Kode Penyakit</label>
                    <p class="text-gray-900"><?= htmlspecialchars($penyakit['kode_penyakit']); ?></p>
                </div>
                <div>
                    <label class="block text-purple-800 font-medium mb-1">Nama Penyakit</label>
                    <input type="text" name="nama_penyakit" value="<?= htmlspecialchars($penyakit['nama_penyakit']); ?>" required class="w-full border border-gray-300 px-3 py-2 rounded focus:outline-none focus:ring-2 focus:ring-purple-300">
                </div>
                <div>
                    <label class="block text-purple-800 font-medium mb-1">Deskripsi</label>
                    <textarea name="deskripsi" rows="5" required class="w-full border border-gray-300 px-3 py-2 rounded focus:outline-none focus:ring-2 focus:ring-purple-300"><?= htmlspecialchars($penyakit['deskripsi']); ?></textarea>
                </div>
                <div>
                    <label class="block text-purple-800 font-medium mb-1">Solusi</label>
                    <textarea name="solusi" rows="5" required class="w-full border border-gray-300 px-3 py-2 rounded focus:outline-none focus:ring-2 focus:ring-purple-300"><?= htmlspecialchars($penyakit['solusi']); ?></textarea>
                </div>
                <div class="flex gap-2">
                    <button type="submit" class="bg-green-500 hover:bg-purple-600 text-white px-4 py-2 rounded">Simpan Perubahan</button>
                    <a href="penyakit.php" class="bg-gray-500 hover:bg-gray-600 text-white px-4 py-2 rounded">Kembali</a>
                </div>
            </form>
        </div>
    </body>
</html>