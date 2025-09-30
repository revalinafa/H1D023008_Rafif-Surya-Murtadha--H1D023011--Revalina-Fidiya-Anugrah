<?php
    session_start();
    include '../Auth/connect.php';

    if (!isset($_SESSION['id_user']) || $_SESSION['role'] !== 'pakar') {
        header("Location: ../Auth/login.php");
        exit();
    }

    // Mengambil data penyakit berdasarkan kode
    $penyakit = [];
    $sql = "SELECT * FROM penyakit ORDER BY kode_penyakit ASC";
    $result = $conn->query($sql);
    while ($row = $result->fetch_assoc()) {
        $penyakit[] = $row;
    }
?>

<!DOCTYPE html>
<html lang="id">
    <head>
        <meta charset="UTF-8">
        <title>Data Penyakit</title>
        <script src="https://cdn.tailwindcss.com"></script>

        <!-- JS untuk menampilkan alert berdasarkan parameter success -->
        <script>
            window.onload = () => {
                const params = new URLSearchParams(window.location.search);
                const success = params.get('success');
                
                if (success === 'tambah') {
                    alert('Penyakit berhasil ditambahkan!');
                } else if (success === 'edit') {
                    alert('Penyakit berhasil diedit!');
                } else if (success === 'hapus') {
                    alert('Penyakit berhasil dihapus!');
                }
                
                // Hapus parameter dari URL setelah menampilkan alert
                if (success) {
                    window.history.replaceState({}, document.title, window.location.pathname);
                }
            };
        </script>
    </head>
    <body class="bg-gray-50 text-gray-800">
        
        <!-- Navbar -->
        <nav class="flex items-center justify-between bg-purple-300 text-purple-900 px-6 py-4 shadow-md">
            <h2 class="text-xl font-bold">Pakar Panel - Penyakit</h2>
            <div class="space-x-4">
                <a href="dashboard_pakar.php" class="hover:underline font-medium">Dashboard</a>
                <a href="../Auth/logout.php" class="hover:underline font-medium">Logout</a>
            </div>
        </nav>

        <div class="max-w-5xl mx-auto px-4 py-6">
            <div class="flex items-center justify-between mb-4">
                <h1 class="text-2xl font-semibold text-purple-800">Data Penyakit</h1>
                <a href="tambah_penyakit.php" class="bg-green-500 hover:bg-green-600 text-white px-4 py-2 rounded">Tambah Penyakit</a>
            </div>

            <div class="overflow-x-auto bg-white shadow rounded">
                <table class="min-w-full table-auto">
                    <thead class="bg-purple-100 text-purple-800">
                        <tr>
                            <th class="text-left px-4 py-2 border-b text-sm">No</th>
                            <th class="text-left px-4 py-2 border-b text-sm">Kode Penyakit</th>
                            <th class="text-left px-4 py-2 border-b text-sm">Nama Penyakit</th>
                            <th class="text-left px-4 py-2 border-b text-sm">Aksi</th>
                        </tr>
                    </thead>
                    <tbody>
                        <!-- Looping untuk mengambil dan menampilkan data penyakit (kode dan nama) -->
                        <?php foreach ($penyakit as $index => $p): ?>
                            <tr class="hover:bg-purple-50">
                                <td class="px-4 py-2 border-b text-sm"><?= $index + 1; ?></td>
                                <td class="px-4 py-2 border-b text-sm"><?= htmlspecialchars($p['kode_penyakit']); ?></td>
                                <td class="px-4 py-2 border-b text-sm"><?= htmlspecialchars($p['nama_penyakit']); ?></td>
                                <td class="px-4 py-2 border-b text-sm space-x-2">
                                    <a href="edit_penyakit.php?kode=<?= urlencode($p['kode_penyakit']); ?>" class="bg-blue-500 hover:bg-blue-600 text-white px-3 py-1 rounded text-sm">Edit</a>
                                    <a href="hapus_penyakit.php?kode=<?= urlencode($p['kode_penyakit']); ?>" onclick="return confirm('Yakin ingin hapus?')" class="bg-red-500 hover:bg-red-600 text-white px-3 py-1 rounded text-sm">Hapus</a>
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