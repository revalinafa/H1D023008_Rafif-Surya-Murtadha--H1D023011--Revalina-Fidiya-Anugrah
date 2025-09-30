<?php
    session_start();
    include '../Auth/connect.php';

    if (!isset($_SESSION['id_user'])) {
        header("Location: ../Auth/login.php");
        exit();
    }

    $idUser = $_SESSION['id_user'];
    $nama = $_SESSION['nama'];

    // Ambil riwayat konsultasi dari database dengan JOIN ke tabel penyakit
    $sql = "SELECT r.*, p.kode_penyakit, p.nama_penyakit, p.solusi 
            FROM riwayat_konsultasi r
            LEFT JOIN penyakit p ON r.kode_penyakit = p.kode_penyakit
            WHERE r.id_user = ? 
            ORDER BY r.waktu_konsultasi DESC";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("i", $idUser);
    $stmt->execute();
    $result = $stmt->get_result();

    // Ambil semua data gejala
    $daftarGejala = [];
    $sqlGejala = "SELECT kode_gejala, nama_gejala FROM gejala";
    $resultGejala = $conn->query($sqlGejala);
    while ($row = $resultGejala->fetch_assoc()) {
        $daftarGejala[$row['kode_gejala']] = $row['nama_gejala'];
    }

    // Ambil informasi semua penyakit
    $penyakit = [];
    $solusiPenyakit = []; // Array untuk menyimpan solusi penyakit
    $sqlPenyakit = "SELECT kode_penyakit, nama_penyakit, solusi FROM penyakit";
    $resultPenyakit = $conn->query($sqlPenyakit);
    while ($row = $resultPenyakit->fetch_assoc()) {
        $penyakit[$row['kode_penyakit']] = $row['nama_penyakit'];
        $solusiPenyakit[$row['kode_penyakit']] = $row['solusi']; // Simpan solusi berdasarkan kode penyakit
    }

    // Ambil relasi gejala-penyakit untuk perhitungan probabilitas
    $relasiPenyakitGejala = [];
    $sqlRelasi = "SELECT kode_penyakit, kode_gejala FROM penyakit_gejala";
    $resultRelasi = $conn->query($sqlRelasi);
    while ($row = $resultRelasi->fetch_assoc()) {
        if (!isset($relasiPenyakitGejala[$row['kode_penyakit']])) {
            $relasiPenyakitGejala[$row['kode_penyakit']] = [];
        }
        $relasiPenyakitGejala[$row['kode_penyakit']][] = $row['kode_gejala'];
    }

    // Ambil total penyakit dan gejala untuk perhitungan probabilitas
    $totalPenyakit = count($penyakit);
    $totalGejala = count($daftarGejala);

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

    $riwayat = [];
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

        // Tambahkan ke array riwayat
        $row['probabilitas_normalized'] = $probabilitasNormalisasi;
        $riwayat[] = $row;
    }

    $stmt->close();
?>

<!DOCTYPE html>
<html lang="id">
    <head>
        <meta charset="UTF-8">
        <meta name="viewport" content="width=device-width, initial-scale=1.0">
        <title>Riwayat Konsultasi - Sistem Pakar Penyakit Dada</title>
        <script src="https://cdn.tailwindcss.com"></script>
        <script>
            tailwind.config = {
                theme: {
                    extend: {
                        colors: {
                            primary: {
                                light: '#BCFFBC',
                                DEFAULT: '#1EFEA5',
                                dark: '#276F55',
                            },
                            white: '#FFFFFF',
                        }
                    }
                }
            }
        </script>
        <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.2/css/all.min.css">
    </head>
    <body class="bg-gradient-to-tr from-white via-emerald-200 to-white min-h-screen flex flex-col">

        <main class="flex-grow container mx-auto px-4 py-8">
            <div class="mb-8 text-center">
                <h2 class="text-2xl font-bold text-primary-dark mb-2">Riwayat Konsultasi</h2>
                <p class="text-gray-600 max-w-2xl mx-auto">Berikut adalah daftar konsultasi yang telah Anda lakukan sebelumnya beserta hasil diagnosa dan solusinya.</p>
            </div>

            <div class="flex justify-between items-center mt-8 mb-6">
                <a href="../Home/dashboard.php" class="inline-flex items-center px-5 py-2 bg-gray-100 hover:bg-gray-200 text-gray-700 rounded-lg transition">
                    <i class="fas fa-arrow-left mr-2"></i> Kembali ke Dashboard
                </a>
                
                <?php if (count($riwayat) > 0): ?>
                <a href="export_word.php" class="inline-flex items-center px-5 py-2 bg-primary hover:bg-primary-dark text-white rounded-lg transition">
                    <i class="fas fa-file-word mr-2"></i> Cetak Riwayat
                </a>
                <?php endif; ?>
            </div>

            <!-- Menampilkan riwayat -->
            <div class="bg-white rounded-lg shadow-sm p-6 mb-8">
                <?php if (count($riwayat) > 0): ?>
                    <div class="overflow-x-auto">
                        <table class="min-w-full text-sm text-left">
                            <thead class="bg-primary-light text-primary-dark">
                                <tr>
                                    <th class="px-4 py-3 rounded-tl-lg">No</th>
                                    <th class="px-4 py-3">Kode Penyakit</th>
                                    <th class="px-4 py-3">Hasil Diagnosa</th>
                                    <th class="px-4 py-3">Probabilitas</th>
                                    <th class="px-4 py-3">Waktu Konsultasi</th>
                                    <th class="px-4 py-3 rounded-tr-lg">Detail</th>
                                </tr>
                            </thead>
                            <tbody class="divide-y divide-gray-100">
                                <?php foreach ($riwayat as $index => $r): ?>
                                    <tr class="hover:bg-gray-50">
                                        <td class="px-4 py-3"><?= $index + 1; ?></td>
                                        <td class="px-4 py-3 font-medium"><?= htmlspecialchars($r['kode_penyakit'] ?? 'N/A'); ?></td>
                                        <td class="px-4 py-3"><?= htmlspecialchars($r['nama_penyakit'] ?? $r['hasil_diagnosa']); ?></td>
                                        <td class="px-4 py-3">
                                            <div class="flex items-center">
                                                <div class="w-full bg-gray-200 rounded-full h-2.5 mr-2">
                                                    <div class="bg-primary h-2.5 rounded-full" style="width: <?= min(100, round($r['probabilitas_normalized'], 2)); ?>%"></div>
                                                </div>
                                                <span class="text-xs"><?= round($r['probabilitas_normalized'], 2); ?>%</span>
                                            </div>
                                        </td>
                                        <td class="px-4 py-3 text-gray-500"><?= $r['waktu_konsultasi']; ?></td>
                                        <td class="px-4 py-3">
                                            <button 
                                                class="text-primary-dark hover:text-primary transition"
                                                onclick="toggleDetail('detail-<?= $index; ?>')">
                                                <i class="fas fa-chevron-down"></i>
                                            </button>
                                        </td>
                                    </tr>

                                    <!-- Tampilan detail untuk gejala dan solusi dari penyakit -->
                                    <tr id="detail-<?= $index; ?>" class="hidden bg-gray-50">
                                        <td colspan="6" class="px-6 py-4">
                                            <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                                                <div>
                                                    <h4 class="font-medium text-primary-dark mb-2">Gejala yang Dipilih:</h4>
                                                    <ul class="list-disc list-inside text-gray-600 pl-2">
                                                    <?php 
                                                        $gejalaList = json_decode($r['gejala_dipilih']);
                                                        foreach ($gejalaList as $kodeGejala) {
                                                            echo '<li>' . (isset($daftarGejala[$kodeGejala]) ? 
                                                                htmlspecialchars($daftarGejala[$kodeGejala]) : 
                                                                htmlspecialchars($kodeGejala)) . '</li>';
                                                        }
                                                    ?>
                                                    </ul>
                                                </div>
                                                <div>
                                                    <h4 class="font-medium text-primary-dark mb-2">Solusi/Rekomendasi:</h4>
                                                    <div class="bg-primary-light bg-opacity-30 p-3 rounded text-gray-700">
                                                        <?= nl2br(htmlspecialchars($r['solusi'] ?? 'Solusi tidak tersedia')); ?>
                                                    </div>
                                                </div>
                                            </div>
                                        </td>
                                    </tr>
                                <?php endforeach; ?>
                            </tbody>
                        </table>
                    </div>
                <?php else: ?>
                    <div class="text-center py-8">
                        <div class="text-gray-400 text-5xl mb-4">
                            <i class="fas fa-clipboard-list"></i>
                        </div>
                        <h3 class="text-lg font-medium text-primary-dark mb-2">Belum Ada Riwayat</h3>
                        <p class="text-gray-600 mb-4">Anda belum melakukan konsultasi. Mulai konsultasi untuk mendapatkan diagnosis.</p>
                        <a href="../konsultasi/konsultasi.php" class="inline-block px-5 py-2 bg-primary hover:bg-primary-dark text-white rounded transition">
                            Mulai Konsultasi
                        </a>
                    </div>
                <?php endif; ?>
            </div>
        </main>

        <!-- Script ketika ingin melihat detail perhitungan (collapse) -->
        <script>
            function toggleDetail(id) {
                const element = document.getElementById(id);
                if (element.classList.contains('hidden')) {
                    element.classList.remove('hidden');
                } else {
                    element.classList.add('hidden');
                }
            }
        </script>
    </body>
</html>

<?php $conn->close(); ?>