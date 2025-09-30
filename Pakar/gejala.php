<?php
    session_start();
    include '../Auth/connect.php';

    if (!isset($_SESSION['id_user']) || $_SESSION['role'] !== 'pakar') {
        header("Location: ../Auth/login.php");
        exit();
    }

    // Mengambil data gejala berasarkan kode
    $gejala = [];
    $sql = "SELECT * FROM gejala ORDER BY kode_gejala ASC";
    $result = $conn->query($sql);
    if ($result->num_rows > 0) {
        while ($row = $result->fetch_assoc()) {
            $gejala[] = $row;
        }
    }
?>

<!DOCTYPE html>
<html lang="id">
    <head>
        <meta charset="UTF-8">
        <title>Data Gejala</title>
        <script src="https://cdn.tailwindcss.com"></script>
    </head>
    <body class="bg-gray-100">
        <!-- Navbar -->
        <nav class="flex items-center justify-between bg-purple-300 text-purple-900 px-6 py-4 shadow-md">
            <h2 class="text-xl font-bold">Pakar Panel - Gejala</h2>
            <div class="space-x-4">
                <a href="dashboard_pakar.php" class="hover:underline font-medium">Dashboard</a>
                <a href="../Auth/logout.php" class="hover:underline font-medium">Logout</a>
            </div>
        </nav>

        <div class="max-w-5xl mx-auto px-4 py-6">
            <div class="flex justify-between items-center mb-4">
                <h1 class="text-2xl font-semibold text-purple-700">Data Gejala</h1>
                <a href="tambah_gejala.php" class="bg-green-500 hover:bg-green-600 text-white px-4 py-2 rounded">Tambah Gejala</a>
            </div>

            <div class="overflow-x-auto">
                <table class="w-full text-left table-auto border border-gray-200">
                    <thead class="bg-purple-100 text-purple-800">
                        <tr>
                            <th class="px-4 py-2 border">No</th>
                            <th class="px-4 py-2 border">Kode Gejala</th>
                            <th class="px-4 py-2 border">Nama Gejala</th>
                            <th class="px-4 py-2 border">Aksi</th>
                        </tr>
                    </thead>
                    <tbody>
                        <!-- Memanggil dan menampilkan data gejala (kode dan nama) -->
                        <?php foreach ($gejala as $index => $g): ?>
                            <tr class="hover:bg-gray-50">
                                <td class="px-4 py-2 border"><?= $index + 1; ?></td>
                                <td class="px-4 py-2 border"><?= htmlspecialchars($g['kode_gejala']); ?></td>
                                <td class="px-4 py-2 border"><?= htmlspecialchars($g['nama_gejala']); ?></td>
                                <td class="px-4 py-2 border space-x-2">
                                    <a href="edit_gejala.php?kode=<?= urlencode($g['kode_gejala']); ?>" class="bg-blue-500 hover:bg-blue-600 text-white px-3 py-1 rounded">Edit</a>
                                    <a href="hapus_gejala.php?kode=<?= urlencode($g['kode_gejala']); ?>" onclick="return confirm('Yakin ingin hapus?')" class="bg-red-500 hover:bg-red-600 text-white px-3 py-1 rounded">Hapus</a>
                                </td>
                            </tr>
                        <?php endforeach; ?>
                    </tbody>
                </table>
            </div>
        </div>
    </body>
</html>

<?php $conn->close(); ?>
