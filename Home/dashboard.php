<?php
    session_start();

    if (!isset($_SESSION['id_user'])) {
        header("Location: ../Auth/login.php");
        exit();
    }

    $nama = $_SESSION['nama'];
?>

<!DOCTYPE html>
<html lang="id">
    <head>
        <meta charset="UTF-8">
        <meta name="viewport" content="width=device-width, initial-scale=1.0">
        <title>Dashboard - Sistem Pakar Penyakit Dada</title>
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
        <!-- Navbar -->
        <nav class="bg-white shadow-md">
            <div class="container mx-auto px-4 py-3 flex justify-between items-center">
                <div class="flex items-center">
                    <img src="../Images/logo1.png" alt="Logo" class="h-8 mr-2">
                    <h1 class="text-primary-dark text-lg font-bold">Sistem Pakar Penyakit Dada</h1>
                </div>
                <div class="flex items-center gap-5">
                    <div class="flex items-center text-gray-700">
                        <i class="fas fa-user-circle mr-2"></i>
                        <span><?= htmlspecialchars($nama); ?></span>
                    </div>
                    <a href="biodata.php" class="text-gray-600 hover:text-primary-dark transition">
                        <i class="fas fa-id-card"></i>
                    </a>
                    <a href="../Auth/logout.php" class="text-gray-600 hover:text-red-500 transition">
                        <i class="fas fa-sign-out-alt"></i>
                    </a>
                </div>
            </div>
        </nav>

        <main class="flex-grow container mx-auto px-4 py-8">
            <div class="mb-8 text-center">
                <h2 class="text-2xl font-bold text-primary-dark mb-2">Selamat Datang di Sistem Pakar Penyakit Dada</h2>
                <p class="text-gray-600 max-w-2xl mx-auto">Sistem ini akan membantu Anda melakukan diagnosa awal penyakit dada. Pilih salah satu menu di bawah untuk memulai.</p>
            </div>
            
            <!-- Menu Dashboard -->
            <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6 mb-8">
                <!-- Diagnosa -->
                <div class="bg-white rounded-lg shadow-sm hover:shadow-md transition p-6 flex flex-col items-center">
                    <div class="rounded-full bg-primary-light p-4 mb-4">
                        <i class="fas fa-stethoscope text-primary-dark text-xl"></i>
                    </div>
                    <h3 class="text-lg font-semibold text-primary-dark mb-3">Mulai Diagnosa</h3>
                    <p class="text-gray-600 text-center mb-5 text-sm">Jawab beberapa pertanyaan untuk mendapatkan diagnosis awal penyakit dada</p>
                    <a href="../konsultasi/konsultasi.php" 
                       class="mt-auto w-full py-2 text-center bg-primary hover:bg-primary-dark text-white rounded-md transition">
                        Mulai Sekarang
                    </a>
                </div>
                
                <!-- Riwayat -->
                <div class="bg-white rounded-lg shadow-sm hover:shadow-md transition p-6 flex flex-col items-center">
                    <div class="rounded-full bg-primary-light p-4 mb-4">
                        <i class="fas fa-clipboard-list text-primary-dark text-xl"></i>
                    </div>
                    <h3 class="text-lg font-semibold text-primary-dark mb-3">Riwayat Konsultasi</h3>
                    <p class="text-gray-600 text-center mb-5 text-sm">Lihat riwayat konsultasi dan diagnosis Anda sebelumnya</p>
                    <a href="../konsultasi/riwayat.php" 
                       class="mt-auto w-full py-2 text-center bg-primary hover:bg-primary-dark text-white rounded-md transition">
                        Lihat Riwayat
                    </a>
                </div>
                
                <!-- Petunjuk Penggunaan Website -->
                <div class="bg-white rounded-lg shadow-sm hover:shadow-md transition p-6 flex flex-col items-center">
                    <div class="rounded-full bg-primary-light p-4 mb-4">
                        <i class="fas fa-question-circle text-primary-dark text-xl"></i>
                    </div>
                    <h3 class="text-lg font-semibold text-primary-dark mb-3">Petunjuk Penggunaan</h3>
                    <p class="text-gray-600 text-center mb-5 text-sm">Pelajari cara menggunakan website Sistem Pakar untuk Mendiagnosa Penyakit Dada</p>
                    <a href="../Petunjuk/index.php" 
                       class="mt-auto w-full py-2 text-center bg-primary hover:bg-primary-dark text-white rounded-md transition">
                        Lihat Petunjuk
                    </a>
                </div>
            </div>
            
            <!-- Info Box -->
            <div class="bg-white border-l-4 border-primary rounded-r-lg p-4 flex items-start shadow-sm max-w-4xl mx-auto">
                <div class="text-primary-dark mr-3">
                    <i class="fas fa-info-circle text-xl"></i>
                </div>
                <div>
                    <h4 class="font-medium text-primary-dark">Catatan Penting</h4>
                    <p class="text-sm text-gray-600">Sistem pakar ini hanya memberikan diagnosis awal berdasarkan gejala yang Anda alami. Untuk diagnosis yang akurat, selalu konsultasikan dengan dokter spesialis.</p>
                </div>
            </div>
        </main>

        <!-- Footer -->
        <footer class="py-4 bg-white shadow-inner">
            <div class="container mx-auto text-center text-xs text-gray-500">
                © <?= date("Y"); ?> Sistem Pakar Penyakit Dada. All rights reserved.
            </div>
        </footer>
    </body>
</html>