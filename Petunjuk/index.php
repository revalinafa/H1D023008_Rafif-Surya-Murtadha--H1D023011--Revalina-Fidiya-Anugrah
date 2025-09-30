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
        <title>Petunjuk Penggunaan - Sistem Pakar Penyakit Dada</title>
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
                <h2 class="text-2xl font-bold text-primary-dark mb-2">Petunjuk Penggunaan Sistem</h2>
                <p class="text-gray-600 max-w-2xl mx-auto">Berikut adalah panduan lengkap cara menggunakan Sistem Pakar Penyakit Dada</p>
            </div>
            
            <div class="text-start mt-8 mb-6">
                <a href="../Home/dashboard.php" class="inline-flex items-center px-5 py-2 bg-gray-100 hover:bg-gray-200 text-gray-700 rounded-lg transition">
                    <i class="fas fa-arrow-left mr-2"></i> Kembali ke Dashboard
                </a>
            </div>
            
            <div class="bg-white rounded-lg shadow-md p-6 mb-8">
                <!-- Section 1: Pendahuluan -->
                <div class="mb-8">
                    <div class="flex items-center mb-4">
                        <div class="bg-primary-light rounded-full p-3 mr-3">
                            <i class="fas fa-info-circle text-primary-dark"></i>
                        </div>
                        <h3 class="text-xl font-semibold text-primary-dark">Tentang Sistem Pakar Penyakit Dada</h3>
                    </div>
                    <p class="text-gray-600 mb-4">
                        Sistem Pakar Penyakit Dada adalah aplikasi berbasis website yang dirancang untuk membantu pengguna melakukan diagnosa awal terhadap kemungkinan penyakit dada berdasarkan gejala yang dialami. Sistem ini menggunakan metode Naive Bayes untuk mencocokkan gejala yang diinput dengan basis pengetahuan yang telah divalidasi oleh pakar kesehatan.
                    </p>
                    <div class="bg-gray-50 border-l-4 border-primary-light p-4 rounded">
                        <p class="text-sm text-gray-600">
                            <span class="font-medium text-primary-dark">Catatan:</span> Hasil diagnosa dari sistem ini hanya bersifat informasi awal dan tidak menggantikan diagnosa yang dilakukan oleh dokter. Selalu konsultasikan dengan dokter atau tenaga medis profesional untuk penanganan lebih lanjut.
                        </p>
                    </div>
                </div>
                
                <!-- Section 2: Cara Menggunakan Menu Diagnosa -->
                <div class="mb-8">
                    <div class="flex items-center mb-4">
                        <div class="bg-primary-light rounded-full p-3 mr-3">
                            <i class="fas fa-stethoscope text-primary-dark"></i>
                        </div>
                        <h3 class="text-xl font-semibold text-primary-dark">Cara Menggunakan Menu Diagnosa</h3>
                    </div>
                    <ol class="list-decimal list-inside space-y-3 text-gray-600">
                        <li>
                            <span class="font-medium">Akses Menu Diagnosa</span>
                            <p class="ml-6 mt-1">Dari dashboard utama, klik tombol "Mulai Sekarang" pada Menu Diagnosa.</p>
                        </li>
                        <li>
                            <span class="font-medium">Pilih Gejala</span>
                            <p class="ml-6 mt-1">Sistem akan menampilkan daftar gejala yang mungkin Anda alami. Pilih gejala dengan mencentang kotak di sebelah gejala tersebut.</p>
                        </li>
                        <li>
                            <span class="font-medium">Kirim Diagnosa</span>
                            <p class="ml-6 mt-1">Sistem akan memproses diagnosa penyakit berdasarkan gejala yang telah Anda pilih.</p>
                        </li>
                        <li>
                            <span class="font-medium">Hasil Diagnosa</span>
                            <p class="ml-6 mt-1">Setelah mengirim diagnosa, sistem akan menampilkan hasil diagnosa beserta informasi tentang penyakit yang mungkin Anda alami, termasuk penjelasan, probabilitas, dan saran penanganan awal.</p>
                        </li>
                    </ol>
                </div>
                
                <!-- Section 3: Cara Melihat Riwayat Konsultasi -->
                <div class="mb-8">
                    <div class="flex items-center mb-4">
                        <div class="bg-primary-light rounded-full p-3 mr-3">
                            <i class="fas fa-clipboard-list text-primary-dark"></i>
                        </div>
                        <h3 class="text-xl font-semibold text-primary-dark">Cara Melihat Riwayat Konsultasi</h3>
                    </div>
                    <ol class="list-decimal list-inside space-y-3 text-gray-600">
                        <li>
                            <span class="font-medium">Akses Menu Riwayat</span>
                            <p class="ml-6 mt-1">Dari dashboard utama, klik tombol "Lihat Riwayat" pada Menu Riwayat Konsultasi.</p>
                        </li>
                        <li>
                            <span class="font-medium">Lihat Daftar Riwayat</span>
                            <p class="ml-6 mt-1">Sistem akan menampilkan daftar konsultasi yang telah Anda lakukan sebelumnya dengan informasi tanggal dan hasil diagnosa.</p>
                        </li>
                        <li>
                            <span class="font-medium">Lihat Detail Riwayat</span>
                            <p class="ml-6 mt-1">Klik tombol "Detail" pada riwayat yang ingin Anda lihat untuk melihat informasi lengkap hasil diagnosa.</p>
                        </li>
                        <li>
                            <span class="font-medium">Cetak Riwayat</span>
                            <p class="ml-6 mt-1">Anda dapat mencetak riwayat konsultasi dengan mengklik tombol "Cetak" pada halaman detail riwayat.</p>
                        </li>
                    </ol>
                </div>
                
                <!-- Section 4: Informasi Akun -->
                <div class="mb-8">
                    <div class="flex items-center mb-4">
                        <div class="bg-primary-light rounded-full p-3 mr-3">
                            <i class="fas fa-user-circle text-primary-dark"></i>
                        </div>
                        <h3 class="text-xl font-semibold text-primary-dark">Mengelola Informasi Akun</h3>
                    </div>
                    <ol class="list-decimal list-inside space-y-3 text-gray-600">
                        <li>
                            <span class="font-medium">Akses Biodata</span>
                            <p class="ml-6 mt-1">Klik ikon ID Card <i class="fas fa-id-card"></i> pada navbar untuk melihat dan mengedit informasi biodata Anda.</p>
                        </li>
                        <li>
                            <span class="font-medium">Ubah Informasi Pribadi</span>
                            <p class="ml-6 mt-1">Pada halaman biodata, Anda dapat mengubah informasi pribadi seperti nama lengkap dan username.</p>
                        </li>
                        <li>
                            <span class="font-medium">Ubah Password</span>
                            <p class="ml-6 mt-1">Untuk alasan keamanan, ubah password Anda secara berkala dengan mengakses menu ubah password pada halaman biodata (apabila diperlukan).</p>
                        </li>
                        <li>
                            <span class="font-medium">Logout</span>
                            <p class="ml-6 mt-1">Untuk keluar dari sistem, klik ikon logout <i class="fas fa-sign-out-alt"></i> pada navbar.</p>
                        </li>
                    </ol>
                </div>
                
                <!-- Section 5: FAQ -->
                <div>
                    <div class="flex items-center mb-4">
                        <div class="bg-primary-light rounded-full p-3 mr-3">
                            <i class="fas fa-question-circle text-primary-dark"></i>
                        </div>
                        <h3 class="text-xl font-semibold text-primary-dark">Pertanyaan Umum (FAQ)</h3>
                    </div>
                    <div class="space-y-4 text-gray-600">
                        <div>
                            <h4 class="font-medium text-primary-dark">Apakah hasil diagnosa dari sistem ini akurat?</h4>
                            <p class="ml-6 mt-1">Sistem ini memberikan diagnosa awal berdasarkan gejala yang Anda input. Tingkat akurasinya tergantung pada gejala yang Anda pilih. Namun, hasil diagnosa dari sistem tidak menggantikan diagnosa dokter.</p>
                        </div>
                        <div>
                            <h4 class="font-medium text-primary-dark">Apakah data saya aman?</h4>
                            <p class="ml-6 mt-1">Ya, kami menjaga kerahasiaan data pengguna. Data yang Anda input hanya digunakan untuk keperluan diagnosa dan tidak dibagikan kepada pihak ketiga tanpa persetujuan Anda.</p>
                        </div>
                        <div>
                            <h4 class="font-medium text-primary-dark">Apakah saya bisa menggunakan sistem ini untuk orang lain?</h4>
                            <p class="ml-6 mt-1">Ya, Anda dapat menggunakan sistem ini untuk membantu orang lain. Namun, pastikan informasi yang diinput sesuai dengan kondisi orang tersebut untuk hasil yang lebih akurat.</p>
                        </div>
                    </div>
                </div>
            </div>
        </main>
    </body>
</html>