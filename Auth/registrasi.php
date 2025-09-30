<?php
    include 'connect.php';

    $success = false;
    $error = "";

    if ($_SERVER['REQUEST_METHOD'] == 'POST') {
        $nama = trim($_POST['nama']);
        $username = trim($_POST['username']);
        $password = $_POST['password'];
        $role = 'user'; // Tetap diset sebagai user meskipun ditampilkan

        // Cek apakah username sudah digunakan
        $cek = $conn->prepare("SELECT id_user FROM user WHERE username = ?");
        $cek->bind_param("s", $username);
        $cek->execute();
        $cek->store_result();

        if ($cek->num_rows > 0) {
            $error = "Username sudah digunakan!";
        } else {
            $passwordHash = password_hash($password, PASSWORD_DEFAULT);
            $sql = "INSERT INTO user (nama, username, password, role) VALUES (?, ?, ?, ?)";

            if ($stmt = $conn->prepare($sql)) {
                $stmt->bind_param("ssss", $nama, $username, $passwordHash, $role);
                if ($stmt->execute()) {
                    $success = true;
                } else {
                    $error = "Error saat registrasi: " . $stmt->error;
                }
                $stmt->close();
            } else {
                $error = "Error pada database: " . $conn->error;
            }
        }
        $cek->close();
    }
?>

<!DOCTYPE html>
<html lang="id">
    <head>
        <meta charset="UTF-8">
        <meta name="viewport" content="width=device-width, initial-scale=1.0">
        <title>Registrasi - Sistem Pakar Penyakit Dada</title>
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
    <body class="bg-gradient-to-tr from-white via-emerald-200 to-white min-h-screen flex items-center justify-center">
        <div class="container mx-auto px-4 py-8">
            <div class="flex flex-col md:flex-row rounded-xl overflow-hidden shadow-lg max-w-4xl mx-auto bg-white">
                <!-- Bagian Kiri - LOGO -->
                <div class="md:w-5/12 bg-primary-dark flex flex-col justify-center p-8 text-white">
                    <div class="mb-6 text-center">
                        <img src="../Images/logo2.png" alt="Sispak Penyakit Dada" class="h-30 mx-auto"/>
                        <p class="text-primary-light text-sm mt-2">Diagnosis awal untuk kesehatan Anda</p>
                    </div>
                    
                    <div class="bg-white/10 backdrop-blur-sm p-5 rounded-lg text-white">
                        <h3 class="text-lg font-medium mb-3 text-center">Mengapa Bergabung?</h3>
                        <ul class="space-y-3">
                            <li class="flex items-start">
                                <div class="flex-shrink-0 h-5 w-5 rounded-full bg-primary-light flex items-center justify-center mr-2 mt-0.5">
                                    <i class="fas fa-check text-primary-dark text-xs"></i>
                                </div>
                                <p class="text-sm">Akses ke diagnosa awal penyakit dada</p>
                            </li>
                            <li class="flex items-start">
                                <div class="flex-shrink-0 h-5 w-5 rounded-full bg-primary-light flex items-center justify-center mr-2 mt-0.5">
                                    <i class="fas fa-check text-primary-dark text-xs"></i>
                                </div>
                                <p class="text-sm">Informasi medis terpercaya</p>
                            </li>
                            <li class="flex items-start">
                                <div class="flex-shrink-0 h-5 w-5 rounded-full bg-primary-light flex items-center justify-center mr-2 mt-0.5">
                                    <i class="fas fa-check text-primary-dark text-xs"></i>
                                </div>
                                <p class="text-sm">Riwayat konsultasi tersimpan</p>
                            </li>
                        </ul>
                    </div>
                </div>
                
                <!-- Bagian Kanan - Form Registrasi -->
                <div class="md:w-7/12 p-6 md:p-8 flex flex-col">
                    <div class="mb-6">
                        <h3 class="text-primary-dark text-xl font-bold">Buat Akun Baru</h3>
                        <p class="text-gray-500 text-sm">Lengkapi data berikut untuk mengakses sistem</p>
                    </div>
                    
                    <?php if (!empty($error)): ?>
                        <div class="mb-4 text-red-600 bg-red-50 border-l-4 border-red-500 p-3 rounded-r text-sm">
                            <p><?= htmlspecialchars($error); ?></p>
                        </div>
                    <?php endif; ?>

                    <form action="registrasi.php" method="POST" class="space-y-4 flex-grow" id="registerForm">
                        <div>
                            <label for="nama" class="block text-gray-600 text-sm mb-1.5">Nama Lengkap</label>
                            <div class="relative">
                                <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
                                    <i class="fas fa-user text-gray-400"></i>
                                </div>
                                <input type="text" id="nama" name="nama" required
                                    class="w-full pl-10 pr-3 py-2.5 border border-gray-200 rounded-lg focus:outline-none focus:ring-1 focus:ring-primary-dark focus:border-primary-dark text-sm transition">
                            </div>
                        </div>
                        
                        <div>
                            <label for="username" class="block text-gray-600 text-sm mb-1.5">Username</label>
                            <div class="relative">
                                <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
                                    <i class="fas fa-at text-gray-400"></i>
                                </div>
                                <input type="text" id="username" name="username" required
                                    class="w-full pl-10 pr-3 py-2.5 border border-gray-200 rounded-lg focus:outline-none focus:ring-1 focus:ring-primary-dark focus:border-primary-dark text-sm transition">
                            </div>
                        </div>
                        
                        <div>
                            <label for="password" class="block text-gray-600 text-sm mb-1.5">Password</label>
                            <div class="relative">
                                <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
                                    <i class="fas fa-lock text-gray-400"></i>
                                </div>
                                <input type="password" id="password" name="password" required
                                    class="w-full pl-10 pr-10 py-2.5 border border-gray-200 rounded-lg focus:outline-none focus:ring-1 focus:ring-primary-dark focus:border-primary-dark text-sm transition">
                                <button type="button" onclick="togglePassword()" class="absolute inset-y-0 right-0 pr-3 flex items-center">
                                    <i id="eyeIcon" class="fas fa-eye text-gray-400 hover:text-primary-dark"></i>
                                </button>
                            </div>
                        </div>
                        
                        <div>
                            <label for="role" class="block text-gray-600 text-sm mb-1.5">Role</label>
                            <div class="relative">
                                <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
                                    <i class="fas fa-user-tag text-gray-400"></i>
                                </div>
                                <select id="role_display" disabled
                                        class="w-full pl-10 pr-3 py-2.5 bg-gray-50 text-gray-500 border border-gray-200 rounded-lg appearance-none text-sm">
                                    <option selected>User</option>
                                </select>
                                <div class="absolute inset-y-0 right-0 pr-3 flex items-center pointer-events-none">
                                    <i class="fas fa-lock text-gray-400"></i>
                                </div>
                                <input type="hidden" name="role" value="user">
                            </div>
                            <p class="mt-1 text-xs text-gray-500">Role hanya dapat diubah oleh administrator</p>
                        </div>
                        
                        <button type="submit"
                                class="w-full bg-primary hover:bg-primary-dark text-white font-medium py-2.5 px-4 rounded-lg transition duration-200 shadow-sm mt-4">
                            Daftar Sekarang
                        </button>
                    </form>

                    <div class="mt-6 text-center text-sm">
                        <p class="text-gray-600">
                            Sudah punya akun? 
                            <a href="login.php" class="text-primary-dark hover:underline font-medium">
                                Login di sini
                            </a>
                        </p>
                    </div>

                    <div class="mt-auto pt-4 border-t border-gray-100 text-center text-xs text-gray-500">
                        © <?= date("Y"); ?> Sistem Pakar Penyakit Dada. All rights reserved.
                    </div>
                </div>
            </div>
        </div>

        <!-- Script untuk redirect setelah registrasi -->
        <?php if ($success): ?>
        <script>
            setTimeout(function() {
                alert("Registrasi berhasil! Silakan login.");
                window.location.href = "login.php";
            }, 500);
        </script>
        <?php endif; ?>

        <!-- Script untuk toggle password visibility -->
        <script>
            function togglePassword() {
                const input = document.getElementById("password");
                const icon = document.getElementById("eyeIcon");
                
                if (input.type === "password") {
                    input.type = "text";
                    icon.classList.remove("fa-eye");
                    icon.classList.add("fa-eye-slash");
                } else {
                    input.type = "password";
                    icon.classList.remove("fa-eye-slash");
                    icon.classList.add("fa-eye");
                }
            }
        </script>
    </body>
</html>

<?php $conn->close(); ?>