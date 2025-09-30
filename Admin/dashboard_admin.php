<?php
    session_start();
    include '../Auth/connect.php';

    if (!isset($_SESSION['id_user']) || $_SESSION['role'] !== 'admin') {
        header("Location: ../Auth/login.php");
        exit();
    }

    // Mengambil data user
    $user = [];
    $resultUser = $conn->query("SELECT * FROM user ORDER BY id_user ASC");
    while ($row = $resultUser->fetch_assoc()) {
        $user[] = $row;
    }

    // Mengambil statistik riwayat konsultasi
    $totalKonsultasi = 0;
    $resultKonsultasi = $conn->query("SELECT COUNT(*) as total FROM riwayat_konsultasi");
    if ($resultKonsultasi) {
        $row = $resultKonsultasi->fetch_assoc();
        $totalKonsultasi = $row['total'];
    }


?>

<!DOCTYPE html>
<html lang="id">
    <head>
        <meta charset="UTF-8">
        <title>Dashboard Admin - Sistem Pakar</title>
        <script src="https://cdn.tailwindcss.com"></script>
    </head>
    <body class="bg-gray-50 text-gray-800">

        <!-- Navbar -->
        <nav class="flex items-center justify-between bg-purple-300 text-purple-900 px-6 py-4 shadow-md">
            <h2 class="text-xl font-bold">Admin Panel - Sistem Pakar Diagnosa Penyakit Dada</h2>
            <div class="space-x-4">
                <a href="riwayat_konsultasi.php" class="hover:underline font-medium">Riwayat Konsultasi</a>
                <a href="profil.php" class="hover:underline font-medium">Profil</a>
                <a href="../Auth/logout.php" class="hover:underline font-medium">Logout</a>
            </div>
        </nav>

        <div class="max-w-6xl mx-auto px-4 py-6">
            <h1 class="text-2xl font-semibold mb-6">Dashboard Admin</h1>

            <!-- Statistik Card -->
            <div class="grid grid-cols-1 md:grid-cols-2 gap-6 mb-8">
                <div class="bg-white p-6 rounded-lg shadow-sm border border-gray-200">
                    <div class="flex items-center">
                        <div class="bg-purple-100 p-3 rounded-full">
                            <svg class="w-6 h-6 text-purple-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4.354a4 4 0 110 5.292M15 21H3v-1a6 6 0 0112 0v1zm0 0h6v-1a6 6 0 00-9-5.197m13.5-9a2.5 2.5 0 11-5 0 2.5 2.5 0 015 0z"></path>
                            </svg>
                        </div>
                        <div class="ml-4">
                            <p class="text-sm font-medium text-gray-600">Total User</p>
                            <p class="text-2xl font-semibold text-gray-900"><?= count($user) ?></p>
                        </div>
                    </div>
                </div>
                
                <div class="bg-white p-6 rounded-lg shadow-sm border border-gray-200">
                    <div class="flex items-center">
                        <div class="bg-purple-100 p-3 rounded-full">
                            <svg class="w-6 h-6 text-purple-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"></path>
                            </svg>
                        </div>
                        <div class="ml-4">
                            <p class="text-sm font-medium text-gray-600">Total Konsultasi</p>
                            <p class="text-2xl font-semibold text-gray-900"><?= $totalKonsultasi ?></p>
                        </div>
                    </div>
                </div>
            </div>

            <?php
            // Fungsi render untuk data user
            function render_section($title, $data, $headers, $link, $columns) {
                echo '
                <div class="mb-10">
                    <div class="flex items-center justify-between mb-3">
                        <h2 class="text-lg font-bold text-purple-800">' . $title . '</h2>
                        <a href="' . $link . '" class="bg-purple-400 hover:bg-purple-500 text-white px-4 py-1 rounded shadow text-sm">Kelola</a>
                    </div>
                    <div class="overflow-x-auto">
                        <table class="min-w-full bg-white border border-gray-300 shadow-sm rounded">
                            <thead class="bg-purple-100 text-purple-800">
                                <tr>';
                foreach ($headers as $head) {
                    echo '<th class="p-2 border-b text-left text-sm">' . $head . '</th>';
                }
                echo '</tr></thead><tbody>';
                foreach ($data as $i => $row) {
                    echo '<tr class="hover:bg-purple-50">';
                    echo '<td class="p-2 border-b text-sm">' . ($i + 1) . '</td>';
                    foreach ($columns as $col) {
                        echo '<td class="p-2 border-b text-sm">' . htmlspecialchars($row[$col]) . '</td>';
                    }
                    echo '</tr>';
                }
                echo '</tbody></table></div></div>';
            }

            // Render section untuk data user
            render_section('Data User', $user, ['No', 'Nama', 'Username', 'Role'], 'user.php', ['nama', 'username', 'role']);
            ?>
        </div>

    </body>
</html>

<?php $conn->close(); ?>