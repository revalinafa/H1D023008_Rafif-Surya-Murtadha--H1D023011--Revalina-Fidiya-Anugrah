<?php
    session_start();
    include '../Auth/connect.php';

    if (!isset($_SESSION['id_user']) || $_SESSION['role'] !== 'pakar') {
        header("Location: ../Auth/login.php");
        exit();
    }

    // Ambil data pakar berdasarkan ID
    $id_user = $_SESSION['id_user'];
    $sql = "SELECT nama, username, role FROM user WHERE id_user = ?";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("i", $id_user);
    $stmt->execute();
    $result = $stmt->get_result();
    $pakar = $result->fetch_assoc();
    $stmt->close();
?>

<!DOCTYPE html>
<html lang="id">
    <head>
        <meta charset="UTF-8">
        <meta name="viewport" content="width=device-width, initial-scale=1.0">
        <title>Profil Pakar - Sistem Pakar</title>
        <script src="https://cdn.tailwindcss.com"></script>
    </head>
    <body class="bg-gray-50 text-gray-800">

        <!-- Navbar -->
        <nav class="flex items-center justify-between bg-purple-300 text-purple-900 px-6 py-4 shadow-md">
            <h2 class="text-xl font-bold">Panel Pakar - Sistem Pakar Diagnosa Penyakit Dada</h2>
            <div class="space-x-4">
                <a href="dashboard_pakar.php" class="hover:underline font-medium">Dashboard</a>
                <a href="riwayat_konsultasi.php" class="hover:underline font-medium">Riwayat Konsultasi</a>
                <a href="../Auth/logout.php" class="hover:underline font-medium">Logout</a>
            </div>
        </nav>

        <div class="max-w-6xl mx-auto px-4 py-6">
            <h1 class="text-2xl font-semibold mb-6">Profil Pakar</h1>

            <!-- Profile Card -->
            <div class="bg-white rounded-lg shadow-sm border border-gray-200 overflow-hidden">
                <div class="flex flex-col md:flex-row">
                    <!-- Profile Avatar Section -->
                    <div class="md:w-1/3 bg-gradient-to-b from-purple-600 to-purple-800 text-white p-8 flex flex-col items-center justify-center">
                        <div class="w-24 h-24 rounded-full bg-white/20 flex items-center justify-center text-white mb-6">
                            <svg class="w-12 h-12" fill="currentColor" viewBox="0 0 24 24">
                                <path d="M12 2C13.1 2 14 2.9 14 4C14 5.1 13.1 6 12 6C10.9 6 10 5.1 10 4C10 2.9 10.9 2 12 2ZM21 9V7L15 1L9 7V9C9 10.1 9.9 11 11 11V16L13 18L15 16V11C16.1 11 17 10.1 17 9H21ZM13 9H11C10.4 9 10 8.6 10 8V7.5L12 5.5L14 7.5V8C14 8.6 13.6 9 13 9Z"/>
                            </svg>
                        </div>
                        <h3 class="text-xl font-bold mb-2"><?= htmlspecialchars($pakar['nama']); ?></h3>
                        <span class="inline-flex items-center px-3 py-1 rounded-full text-xs font-medium bg-white/20 text-white mb-6">
                            <svg class="w-3 h-3 mr-1" fill="currentColor" viewBox="0 0 24 24">
                                <path d="M9.5,3A6.5,6.5 0 0,1 16,9.5C16,11.11 15.41,12.59 14.44,13.73L14.71,14H15.5L20.5,19L19,20.5L14,15.5V14.71L13.73,14.44C12.59,15.41 11.11,16 9.5,16A6.5,6.5 0 0,1 3,9.5A6.5,6.5 0 0,1 9.5,3M9.5,5C7,5 5,7 5,9.5C5,12 7,14 9.5,14C12,14 14,12 14,9.5C14,7 12,5 9.5,5Z"/>
                            </svg>
                            <?= htmlspecialchars($pakar['role']); ?>
                        </span>

                        <!-- Edit Profile Button -->
                        <div class="w-full mt-4">
                            <a href="edit_profil.php" 
                               class="w-full text-center px-5 py-2 bg-white/10 hover:bg-white/20 text-white rounded-md transition flex items-center justify-center">
                                <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"></path>
                                </svg>
                                Edit Profil
                            </a>
                        </div>
                    </div>
                    
                    <!-- Profile Details -->
                    <div class="md:w-2/3 p-8">
                        <h2 class="text-xl font-semibold text-purple-800 mb-6 flex items-center">
                            <svg class="w-6 h-6 mr-3 text-purple-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                            </svg>
                            <span>Informasi Pakar</span>                           
                        </h2>
                        
                        <div class="space-y-6">
                            <div class="flex flex-col">
                                <p class="text-sm text-gray-500 mb-1">Nama Lengkap</p>
                                <div class="flex items-center">
                                    <svg class="w-5 h-5 text-purple-600 mr-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"></path>
                                    </svg>
                                    <p class="text-gray-800 font-medium"><?= htmlspecialchars($pakar['nama']); ?></p>
                                </div>
                            </div>
                            
                            <div class="flex flex-col">
                                <p class="text-sm text-gray-500 mb-1">Username</p>
                                <div class="flex items-center">
                                    <svg class="w-5 h-5 text-purple-600 mr-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5.121 17.804A13.937 13.937 0 0112 16c2.5 0 4.847.655 6.879 1.804M15 10a3 3 0 11-6 0 3 3 0 016 0zm6 2a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                                    </svg>
                                    <p class="text-gray-800 font-medium"><?= htmlspecialchars($pakar['username']); ?></p>
                                </div>
                            </div>
                            
                            <div class="flex flex-col">
                                <p class="text-sm text-gray-500 mb-1">Hak Akses</p>
                                <div class="flex items-center">
                                    <svg class="w-5 h-5 text-purple-600 mr-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m5.618-4.016A11.955 11.955 0 0112 2.944a11.955 11.955 0 01-8.618 3.04A12.02 12.02 0 003 9c0 5.591 3.824 10.29 9 11.622 5.176-1.332 9-6.031 9-11.622 0-1.042-.133-2.052-.382-3.016z"></path>
                                    </svg>
                                    <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-purple-100 text-purple-800">
                                        <?= htmlspecialchars($pakar['role']); ?>
                                    </span>
                                </div>
                            </div>

                            <div class="flex flex-col">
                                <p class="text-sm text-gray-500 mb-1">Spesialisasi</p>
                                <div class="flex items-center">
                                    <svg class="w-5 h-5 text-purple-600 mr-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19.428 15.428a2 2 0 00-1.022-.547l-2.387-.477a6 6 0 00-3.86.517l-.318.158a6 6 0 01-3.86.517L6.05 15.21a2 2 0 00-1.806.547M8 4h8l-1 1v5.172a2 2 0 00.586 1.414l5 5c1.26 1.26.367 3.414-1.415 3.414H4.828c-1.782 0-2.674-2.154-1.414-3.414l5-5A2 2 0 009 10.172V5L8 4z"></path>
                                    </svg>
                                    <p class="text-gray-800 font-medium">Sistem Pakar Diagnosa Penyakit Dada</p>
                                </div>
                            </div>
                        </div>

                        <div class="mt-6 pt-4 border-t border-gray-100">
                            <a href="dashboard_pakar.php" 
                               class="px-5 py-2 bg-gray-100 hover:bg-gray-200 text-gray-700 rounded-md transition flex items-center w-fit">
                                <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"></path>
                                </svg>
                                Kembali ke Dashboard
                            </a>
                        </div>
                    </div>
                </div>
            </div>
        </div>

    </body>
</html>

<?php $conn->close(); ?>