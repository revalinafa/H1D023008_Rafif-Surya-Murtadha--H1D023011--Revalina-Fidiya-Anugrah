<?php
    session_start();
    include '../Auth/connect.php';

    // Validasi role pakar
    if (!isset($_SESSION['id_user']) || $_SESSION['role'] !== 'pakar') {
        header("Location: ../Auth/login.php");
        exit();
    }

    // Ambil semua data penyakit
    $penyakit = [];
    $sqlPenyakit = "SELECT kode_penyakit, nama_penyakit FROM penyakit";
    $resultPenyakit = $conn->query($sqlPenyakit);
    while ($row = $resultPenyakit->fetch_assoc()) {
        $penyakit[$row['kode_penyakit']] = $row['nama_penyakit'];
    }

    // Ambil total penyakit untuk perhitungan probabilitas
    $totalPenyakit = count($penyakit);

    // Ambil semua data gejala
    $daftarGejala = [];
    $sqlGejala = "SELECT kode_gejala, nama_gejala FROM gejala";
    $resultGejala = $conn->query($sqlGejala);
    while ($row = $resultGejala->fetch_assoc()) {
        $daftarGejala[$row['kode_gejala']] = $row['nama_gejala'];
    }

    // Ambil total gejala untuk perhitungan
    $totalGejala = count($daftarGejala);

    // Ambil relasi gejala-penyakit untuk perhitungan nc
    $relasiPenyakitGejala = [];
    $sqlRelasi = "SELECT kode_penyakit, kode_gejala FROM penyakit_gejala";
    $resultRelasi = $conn->query($sqlRelasi);
    while ($row = $resultRelasi->fetch_assoc()) {
        if (!isset($relasiPenyakitGejala[$row['kode_penyakit']])) {
            $relasiPenyakitGejala[$row['kode_penyakit']] = [];
        }
        $relasiPenyakitGejala[$row['kode_penyakit']][] = $row['kode_gejala'];
    }

    // FUNGSI STANDAR UNTUK PERHITUNGAN PROBABILITAS NAIVE BAYES
    function hitungProbabilitasNaiveBayes($gejalaDipilih, $relasiPenyakitGejala, $totalPenyakit, $totalGejala, $penyakit) {
        $hasilPerhitungan = [];
        
        // Hitung probabilitas untuk setiap penyakit
        foreach ($penyakit as $kodePenyakit => $namaPenyakit) {
            // TAHAP 1: Prior probability untuk setiap penyakit (sama untuk semua penyakit)
            $priorProbability = 1 / $totalPenyakit;
            
            // TAHAP 2: Inisialisasi posterior probability dengan prior probability
            $posteriorProbability = $priorProbability;
            
            // TAHAP 3: Kalikan dengan likelihood untuk setiap gejala yang dipilih
            foreach ($gejalaDipilih as $gejala) {
                // Nilai nc (1 jika gejala ada pada penyakit, 0 jika tidak)
                $nc = in_array($gejala, $relasiPenyakitGejala[$kodePenyakit] ?? []) ? 1 : 0;
                
                // Parameter untuk perhitungan likelihood
                $n = 1;  // Selalu 1 untuk gejala biner
                $m = $totalGejala; // Total gejala dalam database
                $p = $priorProbability; // Prior probability
                
                // Hitung P(ai|vj) menggunakan Laplacian smoothing
                // Rumus: P(gejala|penyakit) = ((nc + m) * p) / (n + m)
                $likelihood = (($nc + $m) * $p) / ($n + $m);
                
                // Kalikan posterior probability dengan likelihood
                $posteriorProbability *= $likelihood;
            }
            
            $hasilPerhitungan[$kodePenyakit] = $posteriorProbability;
        }
        
        return $hasilPerhitungan;
    }

    // FUNGSI UNTUK NORMALISASI PROBABILITAS KE PERSENTASE
    function normalisasiProbabilitas($hasilPerhitungan, $kodePenyakitTarget) {
        // Hitung total probabilitas untuk normalisasi
        $totalProb = array_sum($hasilPerhitungan);
        
        // Normalisasi ke persentase
        if ($totalProb > 0 && isset($hasilPerhitungan[$kodePenyakitTarget])) {
            return ($hasilPerhitungan[$kodePenyakitTarget] / $totalProb) * 100;
        }
        
        return 0;
    }

    // Ambil riwayat konsultasi dari database beserta nama user
    $sql = "SELECT r.*, u.nama 
            FROM riwayat_konsultasi r
            JOIN user u ON r.id_user = u.id_user
            ORDER BY r.waktu_konsultasi DESC";
    $result = $conn->query($sql);

    $riwayat = [];
    if ($result->num_rows > 0) {
        while ($row = $result->fetch_assoc()) {
            // Dapatkan gejala yang dipilih
            $gejalaDipilih = json_decode($row['gejala_dipilih']);
            
            // Hitung probabilitas untuk semua penyakit menggunakan fungsi standar
            $hasilPerhitungan = hitungProbabilitasNaiveBayes(
                $gejalaDipilih, 
                $relasiPenyakitGejala, 
                $totalPenyakit, 
                $totalGejala, 
                $penyakit
            );
            
            // Normalisasi probabilitas untuk penyakit yang didiagnosis
            if (isset($row['kode_penyakit'])) {
                $probabilitasNormalisasi = normalisasiProbabilitas($hasilPerhitungan, $row['kode_penyakit']);
            } else {
                // Fallback jika kode penyakit tidak tersedia
                $totalProb = array_sum($hasilPerhitungan);
                $probabilitasNormalisasi = ($totalProb > 0) ? ($row['probabilitas'] / $totalProb) * 100 : 0;
            }
            
            // Tambahkan probabilitas ternormalisasi ke data
            $row['probabilitas_normalized'] = $probabilitasNormalisasi;
            $riwayat[] = $row;
        }
    }

    // Statistik untuk dashboard pakar
    $totalKonsultasi = count($riwayat);
    $konsultasiHariIni = 0;
    $konsultasiBulanIni = 0;

    foreach ($riwayat as $r) {
        $waktuKonsultasi = strtotime($r['waktu_konsultasi']);
        $hariIni = strtotime(date('Y-m-d'));
        $bulanIni = strtotime(date('Y-m-01'));
        
        if ($waktuKonsultasi >= $hariIni) {
            $konsultasiHariIni++;
        }
        if ($waktuKonsultasi >= $bulanIni) {
            $konsultasiBulanIni++;
        }
    }
?>

<!DOCTYPE html>
<html lang="id">
    <head>
        <meta charset="UTF-8">
        <title>Riwayat Konsultasi - Pakar Dashboard</title>
        <script src="https://cdn.tailwindcss.com"></script>
    </head>
    <body class="bg-gray-50 text-gray-800">

        <!-- Navbar -->
        <nav class="flex items-center justify-between bg-purple-300 text-purple-900 px-6 py-4 shadow-md">
            <h2 class="text-xl font-bold">Pakar Panel - Sistem Pakar Diagnosa Penyakit Dada</h2>
            <div class="space-x-4">
                <a href="dashboard_pakar.php" class="hover:underline font-medium">Dashboard</a>
                <a href="../Auth/logout.php" class="hover:underline font-medium">Logout</a>
            </div>
        </nav>

        <div class="max-w-7xl mx-auto px-4 py-6">
            <div class="flex items-center justify-between mb-6">
                <h1 class="text-2xl font-semibold">Riwayat Konsultasi Pasien</h1>
                <div class="text-sm text-gray-600">
                    Total: <?= $totalKonsultasi ?> konsultasi
                </div>
            </div>

            <!-- Statistik Ringkas -->
            <div class="grid grid-cols-1 md:grid-cols-3 gap-6 mb-4">
                <div class="bg-white p-6 rounded-lg shadow-sm border border-gray-200">
                    <div class="flex items-center">
                        <div class="bg-blue-100 p-3 rounded-full">
                            <svg class="w-6 h-6 text-blue-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"></path>
                            </svg>
                        </div>
                        <div class="ml-4">
                            <p class="text-sm font-medium text-gray-600">Total Konsultasi</p>
                            <p class="text-2xl font-semibold text-gray-900"><?= $totalKonsultasi ?></p>
                        </div>
                    </div>
                </div>
                
                <div class="bg-white p-6 rounded-lg shadow-sm border border-gray-200">
                    <div class="flex items-center">
                        <div class="bg-green-100 p-3 rounded-full">
                            <svg class="w-6 h-6 text-green-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3a4 4 0 118 0v4m-4 8a2 2 0 11-4 0m4 0a2 2 0 104 0m-5 3v6a3 3 0 106 0v-6M7 13a3 3 0 000-6M7 13l4-4M7 13l4 4"></path>
                            </svg>
                        </div>
                        <div class="ml-4">
                            <p class="text-sm font-medium text-gray-600">Konsultasi Hari Ini</p>
                            <p class="text-2xl font-semibold text-gray-900"><?= $konsultasiHariIni ?></p>
                        </div>
                    </div>
                </div>

                <div class="bg-white p-6 rounded-lg shadow-sm border border-gray-200">
                    <div class="flex items-center">
                        <div class="bg-purple-100 p-3 rounded-full">
                            <svg class="w-6 h-6 text-purple-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 19v-6a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2a2 2 0 002-2zm0 0V9a2 2 0 012-2h2a2 2 0 012 2v10m-6 0a2 2 0 002 2h2a2 2 0 002-2m0 0V5a2 2 0 012-2h2a2 2 0 012 2v14a2 2 0 01-2 2h-2a2 2 0 01-2-2z"></path>
                            </svg>
                        </div>
                        <div class="ml-4">
                            <p class="text-sm font-medium text-gray-600">Konsultasi Bulan Ini</p>
                            <p class="text-2xl font-semibold text-gray-900"><?= $konsultasiBulanIni ?></p>
                        </div>
                    </div>
                </div>
            </div>

            <a href="export_word.php" target="_blank" class="inline-flex items-center px-4 py-2 mb-3 bg-green-500 hover:bg-green-600 text-white font-medium rounded-md shadow transition-colors duration-150">
                    <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 10v6m0 0l-3-3m3 3l3-3m2 8H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"></path>
                    </svg>
                    Export ke Word
            </a>            

            <!-- Tabel Riwayat Konsultasi -->
            <?php if (count($riwayat) > 0): ?>
                <div class="bg-white rounded-lg shadow-sm border border-gray-200">
                    <div class="px-6 py-4 border-b border-gray-200">
                        <h3 class="text-lg font-semibold text-gray-900">Daftar Riwayat Konsultasi</h3>
                    </div>
                    <div class="overflow-x-auto">
                        <table class="min-w-full divide-y divide-gray-200">
                            <thead class="bg-purple-50">
                                <tr>
                                    <th class="px-6 py-3 text-left text-xs font-medium text-purple-800 uppercase tracking-wider">No</th>
                                    <th class="px-6 py-3 text-left text-xs font-medium text-purple-800 uppercase tracking-wider">Nama Pasien</th>
                                    <th class="px-6 py-3 text-left text-xs font-medium text-purple-800 uppercase tracking-wider">Hasil Diagnosa</th>
                                    <th class="px-6 py-3 text-left text-xs font-medium text-purple-800 uppercase tracking-wider">Probabilitas</th>
                                    <th class="px-6 py-3 text-left text-xs font-medium text-purple-800 uppercase tracking-wider">Gejala yang Dialami</th>
                                    <th class="px-6 py-3 text-left text-xs font-medium text-purple-800 uppercase tracking-wider">Waktu Konsultasi</th>
                                </tr>
                            </thead>
                            <tbody class="bg-white divide-y divide-gray-200">
                                <?php foreach ($riwayat as $index => $r): ?>
                                    <tr class="hover:bg-purple-25 transition-colors duration-150">
                                        <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">
                                            <?= $index + 1; ?>
                                        </td>
                                        <td class="px-6 py-4 whitespace-nowrap">
                                            <div class="text-sm font-medium text-gray-900">
                                                <?= htmlspecialchars($r['nama']); ?>
                                            </div>
                                        </td>
                                        <td class="px-6 py-4 whitespace-nowrap">
                                            <div class="text-sm text-gray-900">
                                                <?= htmlspecialchars($r['hasil_diagnosa']); ?>
                                            </div>
                                        </td>
                                        <td class="px-6 py-4 whitespace-nowrap">
                                            <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium 
                                                <?php 
                                                $prob = round($r['probabilitas_normalized'], 2);
                                                if ($prob >= 80) echo 'bg-green-100 text-green-800';
                                                elseif ($prob >= 60) echo 'bg-yellow-100 text-yellow-800';
                                                else echo 'bg-red-100 text-red-800';
                                                ?>">
                                                <?= $prob; ?>%
                                            </span>
                                        </td>
                                        <td class="px-6 py-4">
                                            <div class="text-sm text-gray-900 max-w-xs">
                                                <?php
                                                $gejalaList = json_decode($r['gejala_dipilih']);
                                                
                                                // Tampilkan nama gejala jika tersedia, jika tidak tampilkan kode gejala
                                                $namaGejalaList = [];
                                                foreach ($gejalaList as $kodeGejala) {
                                                    $namaGejalaList[] = isset($daftarGejala[$kodeGejala]) ? 
                                                        $daftarGejala[$kodeGejala] : $kodeGejala;
                                                }
                                                
                                                $gejalaText = implode(', ', $namaGejalaList);
                                                if (strlen($gejalaText) > 100) {
                                                    echo htmlspecialchars(substr($gejalaText, 0, 100)) . '<span class="text-gray-500">... dan ' . (count($namaGejalaList) - 3) . ' gejala lainnya</span>';
                                                } else {
                                                    echo htmlspecialchars($gejalaText);
                                                }
                                                ?>
                                            </div>
                                        </td>
                                        <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">
                                            <?= date('d/m/Y H:i', strtotime($r['waktu_konsultasi'])); ?>
                                        </td>
                                    </tr>
                                <?php endforeach; ?>
                            </tbody>
                        </table>
                    </div>
                </div>
            <?php else: ?>
                <div class="bg-white rounded-lg shadow-sm border border-gray-200 p-8 text-center">
                    <div class="mx-auto w-12 h-12 bg-gray-100 rounded-full flex items-center justify-center mb-4">
                        <svg class="w-6 h-6 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"></path>
                        </svg>
                    </div>
                    <h3 class="text-lg font-medium text-gray-900 mb-2">Belum Ada Riwayat Konsultasi</h3>
                    <p class="text-gray-600">Belum ada pasien yang melakukan konsultasi pada sistem ini.</p>
                </div>
            <?php endif; ?>

            <!-- Tombol Kembali -->
            <div class="mt-8">
                <a href="dashboard_pakar.php" class="inline-flex items-center px-4 py-2 bg-purple-400 hover:bg-purple-500 text-white font-medium rounded-md shadow transition-colors duration-150">
                    <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"></path>
                    </svg>
                    Kembali ke Dashboard
                </a>
            </div>
        </div>

    </body>
</html>

<?php $conn->close(); ?>