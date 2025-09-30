<?php
    session_start();
    include '../Auth/connect.php';

    if (!isset($_SESSION['id_user']) || $_SESSION['role'] !== 'admin') {
        header("Location: ../Auth/login.php");
        exit();
    }

    // Mengambil data user berdasarkan ID
    $users = [];
    $sql = "SELECT * FROM user ORDER BY id_user ASC";
    $result = $conn->query($sql);
    if ($result->num_rows > 0) {
        while ($row = $result->fetch_assoc()) {
            $users[] = $row;
        }
    }
?>

<!DOCTYPE html>
<html lang="id">
    <head>
        <meta charset="UTF-8">
        <title>Data User</title>
        <script src="https://cdn.tailwindcss.com"></script>
        <script>
            window.onload = () => {
                const params = new URLSearchParams(window.location.search);
                const success = params.get('success');
                
                if (success === 'add') {
                    alert('User berhasil ditambahkan!');
                    window.history.replaceState({}, document.title, window.location.pathname);
                } else if (success === 'edit') {
                    alert('User berhasil diedit!');
                    window.history.replaceState({}, document.title, window.location.pathname);
                } else if (success === 'delete') {
                    alert('User berhasil dihapus!');
                    window.history.replaceState({}, document.title, window.location.pathname);
                }
            };
        </script>
    </head>
    <body class="bg-purple-50 min-h-screen">

        <!-- Navbar -->
        <nav class="flex items-center justify-between bg-purple-300 text-purple-900 px-6 py-4 shadow-md">
            <h2 class="text-xl font-bold">Admin Panel - User</h2>
            <div class="space-x-4">
                <a href="dashboard_admin.php" class="hover:underline font-medium">Dashboard</a>
                <a href="../Auth/logout.php" class="hover:underline font-medium">Logout</a>
            </div>
        </nav>

        <div class="max-w-6xl mx-auto p-6">
            <div class="flex items-center justify-between mb-4">
                <h1 class="text-2xl font-bold text-purple-800">Data User</h1>
                <a href="tambah_user.php" class="bg-green-500 hover:bg-green-600 text-white px-4 py-2 rounded">Tambah User</a>
            </div>

            <div class="overflow-x-auto bg-white rounded shadow">
                <table class="min-w-full table-auto border border-gray-200">
                    <thead class="bg-purple-100 text-purple-800">
                        <tr>
                            <th class="px-4 py-2 border">No</th>
                            <th class="px-4 py-2 border">Username</th>
                            <th class="px-4 py-2 border">Nama</th>
                            <th class="px-4 py-2 border">Role</th>
                            <th class="px-4 py-2 border">Aksi</th>
                        </tr>
                    </thead>
                    <tbody>
                        <!-- Looping untuk mengambil dan menampilkan data user (username, nama, role) -->
                        <?php foreach ($users as $index => $u): ?>
                            <tr class="hover:bg-purple-50">
                                <td class="px-4 py-2 border text-center"><?= $index + 1; ?></td>
                                <td class="px-4 py-2 border"><?= htmlspecialchars($u['username']); ?></td>
                                <td class="px-4 py-2 border"><?= htmlspecialchars($u['nama']); ?></td>
                                <td class="px-4 py-2 border capitalize"><?= htmlspecialchars($u['role']); ?></td>
                                <td class="px-4 py-2 border text-center space-x-2">
                                    <a href="edit_user.php?id=<?= urlencode($u['id_user']); ?>" class="inline-block bg-blue-500 hover:bg-blue-600 text-white px-3 py-1 rounded">Edit</a>
                                    <a href="hapus_user.php?id=<?= urlencode($u['id_user']); ?>" onclick="return confirm('Yakin ingin hapus?')" class="inline-block bg-red-500 hover:bg-red-600 text-white px-3 py-1 rounded">Hapus</a>
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