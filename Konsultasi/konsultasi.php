<?php
    session_start();
    include '../Auth/connect.php';

    if (!isset($_SESSION['id_user']) || $_SESSION['role'] !== 'user') {
        header("Location: ../Auth/login.php");
        exit();
    }

    $nama = $_SESSION['nama'];

    // Mengambil data gejala
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
        <meta name="viewport" content="width=device-width, initial-scale=1.0">
        <title>Konsultasi - Sistem Pakar Penyakit Dada</title>
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
            <!-- Header -->
            <div class="mb-8 text-center">
                <h2 class="text-2xl font-bold text-primary-dark mb-2">Konsultasi Penyakit Dada</h2>
                <p class="text-gray-600 max-w-2xl mx-auto">Silakan pilih gejala-gejala yang Anda alami saat ini untuk mendapatkan diagnosa awal penyakit dada.</p>
            </div>

            <div class="text-start mt-8 mb-6">
                <a href="../Home/dashboard.php" class="inline-flex items-center px-5 py-2 bg-gray-100 hover:bg-gray-200 text-gray-700 rounded-lg transition">
                    <i class="fas fa-arrow-left mr-2"></i> Kembali ke Dashboard
                </a>
            </div>

            <!-- Form -->
            <div class="bg-white rounded-lg shadow-sm mb-8">
                <form action="proses_konsultasi.php" method="post" onsubmit="return validasiForm();" class="p-6">
                    <div class="overflow-x-auto">
                        <table class="min-w-full text-sm text-left" id="tabelGejala">
                            <thead class="bg-primary-light text-primary-dark">
                                <tr>
                                    <th class="px-4 py-3 rounded-tl-lg">No</th>
                                    <th class="px-4 py-3">Kode</th>
                                    <th class="px-4 py-3">Gejala</th>
                                    <th class="px-4 py-3 rounded-tr-lg text-center">Pilih</th>
                                </tr>
                            </thead>
                            <tbody class="divide-y divide-gray-100">
                                <!-- Menampilkan data gejala (kode dan nama) -->
                                <?php foreach ($gejala as $index => $g) : ?>
                                <tr class="hover:bg-gray-50 gejala-row">
                                    <td class="px-4 py-3"><?= $index + 1; ?></td>
                                    <td class="px-4 py-3 font-medium"><?= htmlspecialchars($g['kode_gejala']); ?></td>
                                    <td class="px-4 py-3 gejala-nama"><?= htmlspecialchars($g['nama_gejala']); ?></td>
                                    <td class="px-4 py-3 text-center">
                                        <label class="inline-flex items-center justify-center">
                                            <input type="checkbox" name="gejala[]" value="<?= htmlspecialchars($g['kode_gejala']); ?>" class="h-5 w-5 text-primary focus:ring-primary rounded">
                                        </label>
                                    </td>
                                </tr>
                                <?php endforeach; ?>
                            </tbody>
                        </table>
                    </div>

                    <div class="flex flex-col sm:flex-row items-center justify-between mt-8 gap-4">
                        <div class="bg-primary-light bg-opacity-40 p-3 rounded-lg text-sm text-gray-700 flex items-start">
                            <i class="fas fa-info-circle text-primary-dark mt-0.5 mr-2"></i>
                            <p>Pilih semua gejala yang Anda alami saat ini. Semakin lengkap gejala yang dipilih, semakin akurat diagnosis yang dihasilkan.</p>
                        </div>
                        <div>
                            <span id="selectedCount" class="text-sm text-gray-600 block mb-2 text-center">0 gejala dipilih</span>
                            <button type="submit" class="w-full sm:w-auto px-6 py-2.5 bg-primary hover:bg-primary-dark text-white font-medium rounded-lg transition flex items-center justify-center">
                                <i class="fas fa-stethoscope mr-2"></i>
                                Proses Diagnosa
                            </button>
                        </div>
                    </div>
                </form>
            </div>
        </main>

        <script>
        // Validasi form saat submit
        function validasiForm() {
            const checkboxes = document.querySelectorAll('input[name="gejala[]"]:checked');
            if (checkboxes.length === 0) {
                alert("Silakan pilih minimal satu gejala.");
                return false;
            }
            return true;
        }

        // Live counter untuk jumlah gejala yang dipilih
        document.addEventListener('DOMContentLoaded', function() {
            const checkboxes = document.querySelectorAll('input[name="gejala[]"]');
            const countElement = document.getElementById('selectedCount');
            
            checkboxes.forEach(checkbox => {
                checkbox.addEventListener('change', updateCount);
            });
            
            function updateCount() {
                const checked = document.querySelectorAll('input[name="gejala[]"]:checked').length;
                countElement.textContent = checked + ' gejala dipilih';
                
                // Update warna teks berdasarkan jumlah yang dipilih
                if (checked > 0) {
                    countElement.classList.add('text-primary-dark');
                    countElement.classList.remove('text-gray-600');
                } else {
                    countElement.classList.remove('text-primary-dark');
                    countElement.classList.add('text-gray-600');
                }
            }
            
            // Search functionality 
            const searchInput = document.getElementById('searchGejala');
            searchInput.addEventListener('keyup', function() {
                const searchTerm = this.value.toLowerCase();
                const rows = document.querySelectorAll('.gejala-row');
                
                rows.forEach(row => {
                    const gejalaName = row.querySelector('.gejala-nama').textContent.toLowerCase();
                    if (gejalaName.includes(searchTerm)) {
                        row.style.display = '';
                    } else {
                        row.style.display = 'none';
                    }
                });
            });
        });
        </script>
    </body>
</html>

<?php $conn->close(); ?>