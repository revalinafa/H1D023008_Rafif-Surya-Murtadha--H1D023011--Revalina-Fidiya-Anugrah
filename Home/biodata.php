<?php
    session_start();
    include '../Auth/connect.php';

    if (!isset($_SESSION['id_user'])) {
        header("Location: ../Auth/login.php");
        exit();
    }

    // Ambil data user berdasarkan ID
    $id_user = $_SESSION['id_user'];
    $sql = "SELECT nama, username, role FROM user WHERE id_user = ?";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("i", $id_user);
    $stmt->execute();
    $result = $stmt->get_result();
    $user = $result->fetch_assoc();
    $stmt->close();
?>

<!DOCTYPE html>
<html lang="id">
    <head>
        <meta charset="UTF-8">
        <meta name="viewport" content="width=device-width, initial-scale=1.0">
        <title>Biodata Pengguna - Sistem Pakar Penyakit Dada</title>
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
                        <span><?= htmlspecialchars($user['nama']); ?></span>
                    </div>
                    <a href="dashboard.php" class="text-gray-600 hover:text-primary-dark transition">
                        <i class="fas fa-home"></i>
                    </a>
                    <a href="../Auth/logout.php" class="text-gray-600 hover:text-red-500 transition">
                        <i class="fas fa-sign-out-alt"></i>
                    </a>
                </div>
            </div>
        </nav>

        <main class="flex-grow container mx-auto px-4 py-8">
            <div class="bg-white rounded-xl shadow-lg w-full max-w-4xl mx-auto overflow-hidden">
                <div class="flex flex-col md:flex-row">
                    <!-- Profile Menampilkan nama dan role -->
                    <div class="md:w-1/3 bg-gradient-to-b from-primary-dark to-emerald-800 text-white p-8 flex flex-col items-center justify-center">
                        <div class="w-24 h-24 rounded-full bg-white/20 flex items-center justify-center text-white mb-6">
                            <i class="fas fa-user-circle text-5xl"></i>
                        </div>
                        <h3 class="text-xl font-bold mb-2"><?= htmlspecialchars($user['nama']); ?></h3>
                        <span class="inline-flex items-center px-3 py-1 rounded-full text-xs font-medium bg-white/20 text-white mb-6">
                            <?= htmlspecialchars($user['role']); ?>
                        </span>

                        <!-- Tombol edit profil -->
                        <div class="w-full mt-4">
                            <a href="edit_biodata.php" 
                               class="w-full text-center px-5 py-2 bg-white/10 hover:bg-white/20 text-white rounded-md transition flex items-center justify-center">
                                <i class="fas fa-pen mr-2"></i> Edit Profil
                            </a>
                        </div>
                    </div>
                    
                    <!-- Profile Details -->
                    <div class="md:w-2/3 p-8">
                        <h2 class="text-xl font-semibold text-primary-dark mb-6 flex items-center">
                            <i class="fas fa-id-card mr-3"></i>
                            <span>Biodata Pengguna</span>                           
                        </h2>
                        
                        <div class="space-y-6">
                            <div class="flex flex-col">
                                <p class="text-sm text-gray-500 mb-1">Nama Lengkap</p>
                                <div class="flex items-center">
                                    <i class="fas fa-id-card text-primary-dark mr-3"></i>
                                    <p class="text-gray-800 font-medium"><?= htmlspecialchars($user['nama']); ?></p>
                                </div>
                            </div>
                            
                            <div class="flex flex-col">
                                <p class="text-sm text-gray-500 mb-1">Username</p>
                                <div class="flex items-center">
                                    <i class="fas fa-user text-primary-dark mr-3"></i>
                                    <p class="text-gray-800 font-medium"><?= htmlspecialchars($user['username']); ?></p>
                                </div>
                            </div>
                            
                            <div class="flex flex-col">
                                <p class="text-sm text-gray-500 mb-1">Status</p>
                                <div class="flex items-center">
                                    <i class="fas fa-shield-alt text-primary-dark mr-3"></i>
                                    <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-primary-light text-primary-dark">
                                        <?= htmlspecialchars($user['role']); ?>
                                    </span>
                                </div>
                            </div>
                        </div>
                        
                        <!-- Info Box -->
                        <div class="mt-8 bg-primary-light border-l-4 border-primary rounded-r-lg p-4 flex items-start">
                            <div class="text-primary-dark mr-3">
                                <i class="fas fa-info-circle text-xl"></i>
                            </div>
                            <div>
                                <h4 class="font-medium text-primary-dark">Keamanan Akun</h4>
                                <p class="text-sm text-gray-600">Pastikan data diri Anda selalu diperbarui untuk kemudahan mengakses layanan Sistem Pakar Penyakit Dada.</p>
                            </div>
                        </div>
                        
                        <div class="mt-6 pt-4 border-t border-gray-100">
                            <a href="dashboard.php" 
                               class="px-5 py-2 bg-gray-100 hover:bg-gray-200 text-gray-700 rounded-md transition flex items-center w-fit">
                                <i class="fas fa-arrow-left mr-2"></i> Kembali ke Dashboard
                            </a>
                        </div>
                    </div>
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
<?php $conn->close(); ?>