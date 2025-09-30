<?php
    session_start();
    include '../Auth/connect.php';

    if (!isset($_SESSION['id_user']) || $_SESSION['role'] !== 'user') {
        header("Location: ../Auth/login.php");
        exit();
    }

    // Validasi input gejala
    if (!isset($_POST['gejala']) || empty($_POST['gejala'])) {
        echo "Silakan pilih minimal satu gejala.";
        exit();
    }

    $gejalaDipilih = $_POST['gejala'];
    $idUser = $_SESSION['id_user'];

    // Ambil semua data penyakit
    $penyakit = [];
    $totalPenyakit = 0;
    $sql = "SELECT kode_penyakit, nama_penyakit FROM penyakit";
    $result = $conn->query($sql);
    
    while ($row = $result->fetch_assoc()) {
        $penyakit[$row['kode_penyakit']] = [
            'nama_penyakit' => $row['nama_penyakit'],
            'gejala' => [],
            'jumlah_gejala' => 0,
            'nc_values' => [],
            'detail_perhitungan' => []
        ];
        $totalPenyakit++;
    }

    // Ambil semua gejala yang ada dalam database
    $totalGejala = 0;
    $sql = "SELECT COUNT(*) as total_gejala FROM gejala";
    $result = $conn->query($sql);
    if ($row = $result->fetch_assoc()) {
        $totalGejala = $row['total_gejala'];
    }

    // Ambil semua gejala untuk keperluan detail output
    $daftarGejala = [];
    $sql = "SELECT kode_gejala, nama_gejala FROM gejala";
    $result = $conn->query($sql);
    while ($row = $result->fetch_assoc()) {
        $daftarGejala[$row['kode_gejala']] = $row['nama_gejala'];
    }

    // Ambil relasi gejala-penyakit
    $sqlRelasi = "SELECT kode_penyakit, kode_gejala FROM penyakit_gejala";
    $resultRelasi = $conn->query($sqlRelasi);
    while ($row = $resultRelasi->fetch_assoc()) {
        $kodePenyakit = $row['kode_penyakit'];
        $kodeGejala = $row['kode_gejala'];

        if (isset($penyakit[$kodePenyakit])) {
            $penyakit[$kodePenyakit]['gejala'][] = $kodeGejala;
            $penyakit[$kodePenyakit]['jumlah_gejala']++;
        }
    }

    // Implementasi Naive Bayes
    $hasil = [];
    $logHasil = []; // Untuk dokumentasi langkah-langkah

    // TAHAP 1: Menentukan nilai nc untuk setiap class penyakit
    foreach ($penyakit as $kodePenyakit => &$dataPenyakit) {
        $dataPenyakit['detail_perhitungan'][] = "<strong>Tahap 1: Menentukan nilai nc untuk penyakit $kodePenyakit</strong>";
        
        // Untuk setiap gejala yang dipilih oleh pengguna, periksa apakah ada dalam penyakit ini
        foreach ($gejalaDipilih as $gejala) {
            // Jika gejala ada pada penyakit, berikan nilai nc = 1, jika tidak nc = 0
            $nc = in_array($gejala, $dataPenyakit['gejala']) ? 1 : 0;
            $dataPenyakit['nc_values'][$gejala] = $nc;
            
            // Tambahkan log untuk detail perhitungan
            $namaGejala = isset($daftarGejala[$gejala]) ? $daftarGejala[$gejala] : $gejala;
            $dataPenyakit['detail_perhitungan'][] = "nc gejala $gejala ($namaGejala) untuk $kodePenyakit = $nc";
        }
    }

    // TAHAP 2: Menghitung nilai P(ai|vj) dan P(vj) untuk setiap class
    foreach ($penyakit as $kodePenyakit => &$dataPenyakit) {
        $dataPenyakit['detail_perhitungan'][] = "<strong>Tahap 2: Menghitung nilai P(ai|vj) dan P(vj) untuk penyakit $kodePenyakit</strong>";
        
        // P(vj) - prior probability untuk tiap penyakit (asumsi peluang yang sama)
        $priorProbability = 1 / $totalPenyakit;
        $dataPenyakit['prior_probability'] = $priorProbability;
        
        // Log untuk prior probability
        $dataPenyakit['detail_perhitungan'][] = "P($kodePenyakit) = 1/$totalPenyakit = " . number_format($priorProbability, 4);
        
        // Hitung likelihood P(ai|vj) untuk setiap gejala yang dipilih
        $dataPenyakit['likelihood'] = [];
        foreach ($gejalaDipilih as $gejala) {
            // Rumus: P(gejala|penyakit) = ((nc + m)*p) / (n + m)
            // nc = jumlah record pada data learning yang v = vj dan a = ai
            // n = selalu bernilai 1 (karena gejala bersifat biner)
            // m = jumlah seluruh gejala
            // p = P(vj)
            
            $nc = $dataPenyakit['nc_values'][$gejala];
            $n = 1; // Jumlah gejala pada penyakit (selalu 1 untuk gejala biner)
            $m = $totalGejala; // Total gejala yang ada dalam database
            $p = $priorProbability; // P(vj) untuk penyakit ini
            
            // Hitung P(ai|vj)
            $likelihood = (($nc + $m) * $p) / ($n + $m);
            $dataPenyakit['likelihood'][$gejala] = $likelihood;
            
            // Log detail perhitungan
            $dataPenyakit['detail_perhitungan'][] = "P($gejala|$kodePenyakit) = (($nc + $m) * $p) / ($n + $m) = " . number_format($likelihood, 4);
        }
    }

    // TAHAP 3: Menghitung P(ai|vj) x P(vj) untuk setiap penyakit
    foreach ($penyakit as $kodePenyakit => &$dataPenyakit) {
        $dataPenyakit['detail_perhitungan'][] = "<strong>Tahap 3: Menghitung P(ai|vj) x P(vj) untuk penyakit $kodePenyakit</strong>";
        
        // Inisialisasi dengan prior probability
        $posteriorProbability = $dataPenyakit['prior_probability'];
        
        // Kalikan dengan semua likelihood untuk mendapatkan posterior probability
        $calcDetail = "Probabilitas $kodePenyakit = P($kodePenyakit) = " . number_format($posteriorProbability, 6);
        
        foreach ($gejalaDipilih as $gejala) {
            $posteriorProbability *= $dataPenyakit['likelihood'][$gejala];
            $namaGejala = isset($daftarGejala[$gejala]) ? $daftarGejala[$gejala] : $gejala;
            $calcDetail .= " × P($gejala|$kodePenyakit)[" . number_format($dataPenyakit['likelihood'][$gejala], 4) . "]";
        }
        
        $calcDetail .= " = " . number_format($posteriorProbability, 8);
        $dataPenyakit['detail_perhitungan'][] = $calcDetail;
        
        // Simpan hasil akhir
        $dataPenyakit['posterior_probability'] = $posteriorProbability;
        
        // Format untuk output
        $hasil[$kodePenyakit] = [
            'kode_penyakit' => $kodePenyakit,
            'nama_penyakit' => $dataPenyakit['nama_penyakit'],
            'probabilitas' => $posteriorProbability,
            'detail' => $dataPenyakit['detail_perhitungan']
        ];
    }

    // TAHAP 4: Menentukan hasil klasifikasi - penyakit dengan probabilitas tertinggi
    $logHasil[] = "<strong>Tahap 4: Menentukan hasil klasifikasi (penyakit dengan probabilitas tertinggi)</strong>";
    
    // Urutkan berdasarkan probabilitas tertinggi
    uasort($hasil, function ($a, $b) {
        return $b['probabilitas'] <=> $a['probabilitas'];
    });

    // Ambil hasil diagnosa (penyakit dengan probabilitas tertinggi)
    $diagnosa = null;
    $probabilitasFix = 0;
    $kodePenyakitTertinggi = null; // Tambahkan variabel untuk menyimpan kode penyakit
    $totalProb = 0;
    
    // Pastikan ada hasil yang ditemukan
    if (!empty($hasil)) {
        $kodeTertinggi = array_key_first($hasil);
        $diagnosa = $hasil[$kodeTertinggi]['nama_penyakit'];
        $probabilitasFix = $hasil[$kodeTertinggi]['probabilitas'];
        $kodePenyakitTertinggi = $kodeTertinggi; // Simpan kode penyakit dengan probabilitas tertinggi
        
        // Hitung total probabilitas untuk normalisasi
        $totalProb = array_sum(array_column($hasil, 'probabilitas'));
    }

    // Simpan ke riwayat_konsultasi dengan kode penyakit
    $gejalaJson = json_encode($gejalaDipilih);

    if ($diagnosa) {
        $stmt = $conn->prepare("INSERT INTO riwayat_konsultasi (id_user, gejala_dipilih, hasil_diagnosa, probabilitas, kode_penyakit) VALUES (?, ?, ?, ?, ?)");
        $stmt->bind_param("issds", $idUser, $gejalaJson, $diagnosa, $probabilitasFix, $kodePenyakitTertinggi);
        $stmt->execute();
        $stmt->close();
    }
?>

<!DOCTYPE html>
<html lang="id">
    <head>
        <meta charset="UTF-8">
        <meta name="viewport" content="width=device-width, initial-scale=1.0">
        <title>Hasil Diagnosa - Sistem Pakar Penyakit Dada</title>
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

        <main class="flex-grow container mx-auto px-4 py-8 max-w-full">
            <div class="max-w-6xl mx-auto bg-white rounded-lg shadow-md overflow-hidden">
                <div class="bg-primary-dark text-white p-6 text-center">
                    <h2 class="text-2xl font-bold">Hasil Diagnosa</h2>
                </div>

                <?php if ($diagnosa) : ?>
                    <!-- Menampilkan hasil DIAGNOSA -->
                    <div class="p-6 border-b">
                        <div class="text-center mb-6">
                            <div class="mx-auto w-20 h-20 bg-primary-light rounded-full flex items-center justify-center mb-4">
                                <i class="fas fa-stethoscope text-primary-dark text-3xl"></i>
                            </div>
                            <p class="text-gray-600 mb-3">Berdasarkan gejala yang Anda pilih, kemungkinan Anda menderita:</p>
                            <h3 class="text-3xl font-bold text-primary-dark mb-3"><?= htmlspecialchars($diagnosa); ?> <span class="text-sm font-normal text-gray-500">(<?= htmlspecialchars($kodePenyakitTertinggi); ?>)</span></h3>
                            
                            <?php 
                                $probabilitasNormalisasi = ($totalProb > 0) ? ($probabilitasFix / $totalProb) * 100 : 0;
                            ?>
                            
                            <!-- Probability Bar -->
                            <div class="w-full bg-gray-200 rounded-full h-5 my-6 max-w-lg mx-auto">
                                <div class="bg-primary h-5 rounded-full" style="width: <?= round($probabilitasNormalisasi, 2); ?>%"></div>
                            </div>
                            <p class="text-lg text-primary-dark">Tingkat Probabilitas: <strong><?= round($probabilitasNormalisasi, 2); ?>%</strong></p>
                        </div>
                        
                        <!-- Menampilkan data gejala yang sudah dipilih -->
                        <div class="mt-8 bg-yellow-50 p-6 rounded-lg border border-yellow-200">
                            <div class="flex items-center text-primary-dark mb-4">
                                <i class="fas fa-clipboard-list mr-3 text-xl"></i>
                                <h4 class="font-semibold text-lg">Gejala yang Anda Pilih:</h4>
                            </div>
                            <ul class="list-disc pl-8 space-y-2 text-gray-700">
                                <?php foreach ($gejalaDipilih as $gejala): ?>
                                    <li><?= htmlspecialchars(isset($daftarGejala[$gejala]) ? $daftarGejala[$gejala] : $gejala); ?></li>
                                <?php endforeach; ?>
                            </ul>
                        </div>
                    </div>

                    <!-- Menampilkan detail perhitungan Naive Bayes -->
                    <div class="p-6 border-b">
                        <button id="toggleDetails" class="flex items-center justify-between w-full text-left text-primary-dark font-semibold focus:outline-none p-2 hover:bg-emerald-50 rounded">
                            <div class="flex items-center">
                                <i class="fas fa-calculator mr-3 text-xl"></i>
                                <span class="text-lg">Detail Perhitungan Naive Bayes</span>
                            </div>
                            <i id="detailsIcon" class="fas fa-chevron-down transform transition-transform duration-300"></i>
                        </button>
                        
                        <div id="detailsContent" class="mt-6 hidden">
                            <div class="overflow-x-auto rounded-lg border border-gray-200">
                                <table class="min-w-full bg-white">
                                    <thead>
                                        <tr class="bg-emerald-50 text-primary-dark">
                                            <th class="py-4 px-6 border-b text-left">Penyakit</th>
                                            <th class="py-4 px-6 border-b text-center">Probabilitas</th>
                                            <th class="py-4 px-6 border-b text-left">Detail</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        <?php foreach ($hasil as $kodePenyakit => $data): ?>
                                        <tr class="<?= ($data['nama_penyakit'] === $diagnosa) ? 'bg-emerald-50' : 'hover:bg-gray-50' ?> transition">
                                            <td class="py-4 px-6 border-b font-medium">
                                                <div class="flex items-center">
                                                    <?php if ($data['nama_penyakit'] === $diagnosa): ?>
                                                        <i class="fas fa-check-circle text-primary-dark mr-2"></i>
                                                    <?php endif; ?>
                                                    <?= htmlspecialchars($data['nama_penyakit']); ?> 
                                                    <span class="text-xs text-gray-500 ml-1">(<?= htmlspecialchars($kodePenyakit); ?>)</span>
                                                </div>
                                            </td>
                                            <td class="py-4 px-6 border-b text-center">
                                                <?php 
                                                    $normProb = ($totalProb > 0) ? ($data['probabilitas'] / $totalProb) * 100 : 0;
                                                    echo round($normProb, 2) . '%'; 
                                                ?>
                                            </td>
                                            <td class="py-4 px-6 border-b text-sm">
                                                <div class="space-y-2 text-gray-700">
                                                    <?php foreach ($data['detail'] as $detailItem): ?>
                                                        <div class="leading-relaxed"><?= $detailItem; ?></div>
                                                    <?php endforeach; ?>
                                                </div>
                                            </td>
                                        </tr>
                                        <?php endforeach; ?>
                                    </tbody>
                                </table>
                            </div>
                        </div>
                    </div>
                    
                    <!-- Information Box -->
                    <div class="p-6">
                        <div class="bg-blue-50 rounded-lg p-6 border border-blue-200">
                            <div class="flex items-center text-blue-700 mb-4">
                                <i class="fas fa-info-circle mr-3 text-xl"></i>
                                <h4 class="font-semibold text-lg">Keterangan Metode Naive Bayes:</h4>
                            </div>
                            <div class="grid md:grid-cols-2 gap-6">
                                <div>
                                    <h5 class="font-medium text-blue-700 mb-3">Tahapan Perhitungan:</h5>
                                    <ul class="list-disc pl-6 space-y-2 text-gray-700">
                                        <li><strong>Tahap 1:</strong> Menentukan nilai nc untuk setiap class penyakit</li>
                                        <li><strong>Tahap 2:</strong> Menghitung nilai P(ai|vj) dan P(vj)</li>  
                                        <li><strong>Tahap 3:</strong> Menghitung nilai P(ai|vj) × P(vj) untuk setiap penyakit</li>
                                        <li><strong>Tahap 4:</strong> Menentukan hasil klasifikasi yaitu penyakit dengan probabilitas tertinggi</li>
                                    </ul>
                                </div>
                                <div>
                                    <h5 class="font-medium text-blue-700 mb-3">Keterangan Rumus:</h5>
                                    <ul class="list-disc pl-6 space-y-2 text-gray-700">
                                        <li><strong>nc</strong> = Jumlah record pada data learning penyakit dan gejala</li>
                                        <li><strong>n</strong> = 1</li>
                                        <li><strong>m</strong> = Jumlah seluruh gejala</li>
                                        <li><strong>p</strong> = Prior probability sama dengan nilai P(vj)</li>
                                        <li><strong>P(vj)</strong> = Prior probability untuk tiap penyakit (1/jumlah penyakit)</li>
                                        <li><strong>P(ai|vj)</strong> = Likelihood gejala terhadap penyakit ((nc + m) *p) / (n + m)</li>
                                    </ul>
                                </div>
                            </div>
                        </div>
                        
                        <!-- Disclaimer -->
                        <div class="flex items-start mt-5 text-sm text-gray-600 bg-yellow-50 p-4 rounded-lg border border-yellow-200">
                            <i class="fas fa-exclamation-triangle text-yellow-500 mt-0.5 mr-3 text-lg"></i>
                            <p>Hasil diagnosa ini hanya perkiraan awal berdasarkan gejala yang Anda pilih. Untuk penanganan medis yang tepat, silakan konsultasikan dengan dokter.</p>
                        </div>
                    </div>
                <?php else : ?>
                    <!-- No Results Section -->
                    <div class="p-8 text-center">
                        <div class="mx-auto w-20 h-20 bg-red-100 rounded-full flex items-center justify-center mb-5">
                            <i class="fas fa-exclamation-triangle text-red-500 text-2xl"></i>
                        </div>
                        <h3 class="text-2xl font-semibold text-red-500 mb-3">Diagnosa Tidak Dapat Ditentukan</h3>
                        <p class="text-gray-600 mb-6 max-w-lg mx-auto">Gejala yang dipilih tidak cukup untuk menentukan penyakit. Silakan pilih lebih banyak gejala untuk hasil yang lebih akurat.</p>
                    </div>
                <?php endif; ?>

                <!-- Action Buttons -->
                <div class="bg-gray-50 p-6 flex flex-col sm:flex-row justify-center gap-4">
                    <a href="konsultasi.php" class="inline-flex items-center justify-center px-6 py-3 bg-primary hover:bg-primary-dark text-white rounded-md transition duration-200 text-lg font-medium">
                        <i class="fas fa-stethoscope mr-2"></i> Konsultasi Lagi
                    </a>
                    <a href="../Home/dashboard.php" class="inline-flex items-center justify-center px-6 py-3 bg-gray-600 hover:bg-gray-700 text-white rounded-md transition duration-200 text-lg font-medium">
                        <i class="fas fa-home mr-2"></i> Kembali ke Beranda
                    </a>
                </div>
            </div>
        </main>

        <!-- Script ketika ingin melihat detail perhitungan (collapse) -->
        <script>
            document.addEventListener('DOMContentLoaded', () => {
                const toggleBtn = document.getElementById('toggleDetails');
                const content = document.getElementById('detailsContent');
                const icon = document.getElementById('detailsIcon');
                
                if (toggleBtn && content && icon) {
                    toggleBtn.addEventListener('click', () => {
                        content.classList.toggle('hidden');
                        icon.classList.toggle('rotate-180');
                    });
                }
            });
        </script>
    </body>
</html>

<?php $conn->close(); ?>