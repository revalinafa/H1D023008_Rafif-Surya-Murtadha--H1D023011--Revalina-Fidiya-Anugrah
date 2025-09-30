<?php
    session_start();
    include '../Auth/connect.php';

    if (!isset($_SESSION['id_user']) || $_SESSION['role'] !== 'pakar') {
        header("Location: ../Auth/login.php");
        exit();
    }

    // Mengambil data gejala
    $gejala = [];
    $resultGejala = $conn->query("SELECT * FROM gejala ORDER BY kode_gejala ASC");
    while ($row = $resultGejala->fetch_assoc()) {
        $gejala[] = $row;
    }

    // Mengambil data penyakit
    $penyakit = [];
    $resultPenyakit = $conn->query("SELECT * FROM penyakit ORDER BY kode_penyakit ASC");
    while ($row = $resultPenyakit->fetch_assoc()) {
        $penyakit[] = $row;
    }

    // JOIN untuk Relasi antara penyakit dan gejala
    $penyakitGejala = [];
    $resultPenyakitGejala = $conn->query("
        SELECT 
            pg.id,
            pg.kode_penyakit,
            pg.kode_gejala,
            p.nama_penyakit,
            g.nama_gejala
        FROM penyakit_gejala pg
        LEFT JOIN penyakit p ON pg.kode_penyakit = p.kode_penyakit
        LEFT JOIN gejala g ON pg.kode_gejala = g.kode_gejala
        ORDER BY pg.kode_penyakit ASC, pg.kode_gejala ASC
    ");
    while ($row = $resultPenyakitGejala->fetch_assoc()) {
        $penyakitGejala[] = $row;
    }

    // Mengambil statistik untuk dashboard
    $totalGejala = count($gejala);
    $totalPenyakit = count($penyakit);
    $totalAturan = count($penyakitGejala);

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
        <title>Dashboard Pakar - Sistem Pakar</title>
        <script src="https://cdn.tailwindcss.com"></script>
    </head>
    <body class="bg-gray-50 text-gray-800">

        <!-- Navbar -->
        <nav class="flex items-center justify-between bg-purple-300 text-purple-900 px-6 py-4 shadow-md">
            <h2 class="text-xl font-bold">Pakar Panel - Sistem Pakar Diagnosa Penyakit Dada</h2>
            <div class="space-x-4">
                <a href="riwayat_konsultasi.php" class="hover:underline font-medium">Riwayat Konsultasi</a>
                <a href="profil.php" class="hover:underline font-medium">Profil</a>
                <a href="../Auth/logout.php" class="hover:underline font-medium">Logout</a>
            </div>
        </nav>

        <div class="max-w-6xl mx-auto px-4 py-6">
            <h1 class="text-2xl font-semibold mb-6">Dashboard Pakar</h1>

            <!-- Statistik Card -->
            <div class="grid grid-cols-1 md:grid-cols-4 gap-6 mb-8">
                <div class="bg-white p-6 rounded-lg shadow-sm border border-gray-200">
                    <div class="flex items-center">
                        <div class="bg-purple-100 p-3 rounded-full">
                            <svg class="w-6 h-6 text-purple-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v10a2 2 0 002 2h8a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2"></path>
                            </svg>
                        </div>
                        <div class="ml-4">
                            <p class="text-sm font-medium text-gray-600">Total Gejala</p>
                            <p class="text-2xl font-semibold text-gray-900"><?= $totalGejala ?></p>
                        </div>
                    </div>
                </div>
                
                <div class="bg-white p-6 rounded-lg shadow-sm border border-gray-200">
                    <div class="flex items-center">
                        <div class="bg-purple-100 p-3 rounded-full">
                            <svg class="w-6 h-6 text-purple-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19.428 15.428a2 2 0 00-1.022-.547l-2.387-.477a6 6 0 00-3.86.517l-.318.158a6 6 0 01-3.86.517L6.05 15.21a2 2 0 00-1.806.547M8 4h8l-1 1v5.172a2 2 0 00.586 1.414l5 5c1.26 1.26.367 3.414-1.415 3.414H4.828c-1.782 0-2.674-2.154-1.414-3.414l5-5A2 2 0 009 10.172V5L8 4z"></path>
                            </svg>
                        </div>
                        <div class="ml-4">
                            <p class="text-sm font-medium text-gray-600">Total Penyakit</p>
                            <p class="text-2xl font-semibold text-gray-900"><?= $totalPenyakit ?></p>
                        </div>
                    </div>
                </div>

                <div class="bg-white p-6 rounded-lg shadow-sm border border-gray-200">
                    <div class="flex items-center">
                        <div class="bg-purple-100 p-3 rounded-full">
                            <svg class="w-6 h-6 text-purple-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13.828 10.172a4 4 0 00-5.656 0l-4 4a4 4 0 105.656 5.656l1.102-1.101m-.758-4.899a4 4 0 005.656 0l4-4a4 4 0 00-5.656-5.656l-1.1 1.1"></path>
                            </svg>
                        </div>
                        <div class="ml-4">
                            <p class="text-sm font-medium text-gray-600">Total Aturan</p>
                            <p class="text-2xl font-semibold text-gray-900"><?= $totalAturan ?></p>
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
            // Render untuk data gejala, penyakit
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

            // Render untuk data relasi penyakit-gejala
            function render_penyakit_gejala_section($title, $data, $headers, $link) {
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
                    echo '<td class="p-2 border-b text-sm">' . htmlspecialchars($row['kode_penyakit']) . '</td>';
                    echo '<td class="p-2 border-b text-sm">' . htmlspecialchars($row['nama_penyakit'] ?? '-') . '</td>';
                    echo '<td class="p-2 border-b text-sm">' . htmlspecialchars($row['kode_gejala']) . '</td>';
                    echo '<td class="p-2 border-b text-sm">' . htmlspecialchars($row['nama_gejala'] ?? '-') . '</td>';
                    echo '</tr>';
                }
                echo '</tbody></table></div></div>';
            }

            // Memanggil fungsi render_section untuk setiap bagian agar ditampilkan
            render_section('Data Gejala', $gejala, ['No', 'Kode Gejala', 'Nama Gejala'], 'gejala.php', ['kode_gejala', 'nama_gejala']);
            render_section('Data Penyakit', $penyakit, ['No', 'Kode Penyakit', 'Nama Penyakit'], 'penyakit.php', ['kode_penyakit', 'nama_penyakit']);
            render_penyakit_gejala_section('Data Aturan Penyakit-Gejala', $penyakitGejala, ['No', 'Kode Penyakit', 'Nama Penyakit', 'Kode Gejala', 'Nama Gejala'], 'penyakit_gejala.php');
            ?>
        </div>

    </body>
</html>

<?php $conn->close(); ?>