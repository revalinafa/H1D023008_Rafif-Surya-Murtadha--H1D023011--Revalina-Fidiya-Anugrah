<?php
    session_start();
    include 'connect.php';

    $success = false;
    $redirectTo = '';
    $error = "";

    // Mengambil data dari form login
    if ($_SERVER['REQUEST_METHOD'] == 'POST') {
        $username = $_POST['username'];
        $password = $_POST['password'];

        $sql = "SELECT id_user, nama, username, password, role FROM user WHERE username = ?";

        if ($stmt = $conn->prepare($sql)) {
            $stmt->bind_param("s", $username);
            $stmt->execute();
            $result = $stmt->get_result();

            if ($result->num_rows == 1) {
                $user = $result->fetch_assoc();

                if (password_verify($password, $user['password'])) {
                    $_SESSION['id_user'] = $user['id_user'];
                    $_SESSION['nama'] = $user['nama'];
                    $_SESSION['username'] = $user['username'];
                    $_SESSION['role'] = $user['role'];

                    $success = true;
                    echo "<script>alert('Login berhasil!');</script>";
                    
                    // Redirect berdasarkan role
                    if ($user['role'] == 'admin') {
                        $redirectTo = "../Admin/dashboard_admin.php";
                    } elseif ($user['role'] == 'pakar') {
                        $redirectTo = "../Pakar/dashboard_pakar.php";
                    } else {
                        $redirectTo = "../Home/dashboard.php";
                    }
                } else {
                    $error = "Password salah!";
                }
            } else {
                $error = "Username tidak ditemukan!";
            }

            $stmt->close();
        } else {
            $error = "Error pada database: " . $conn->error;
        }
    }
?>

<!DOCTYPE html>
<html lang="id">
    <head>
        <meta charset="UTF-8">
        <meta name="viewport" content="width=device-width, initial-scale=1.0">
        <title>Login - Sistem Pakar Penyakit Dada</title>
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
                        <p class="text-center text-sm mb-4">Sistem pakar untuk membantu diagnosis awal penyakit dada dengan pendekatan berbasis pengetahuan medis.</p>
                        
                        <div class="grid grid-cols-3 gap-2 mt-6">
                            <div class="text-center">
                                <div class="rounded-full bg-primary-light w-10 h-10 flex items-center justify-center mx-auto">
                                    <i class="fas fa-heartbeat text-primary-dark"></i>
                                </div>
                                <p class="text-xs mt-2 text-primary-light">Akurat</p>
                            </div>
                            <div class="text-center">
                                <div class="rounded-full bg-primary-light w-10 h-10 flex items-center justify-center mx-auto">
                                    <i class="fas fa-shield-alt text-primary-dark"></i>
                                </div>
                                <p class="text-xs mt-2 text-primary-light">Aman</p>
                            </div>
                            <div class="text-center">
                                <div class="rounded-full bg-primary-light w-10 h-10 flex items-center justify-center mx-auto">
                                    <i class="fas fa-user-md text-primary-dark"></i>
                                </div>
                                <p class="text-xs mt-2 text-primary-light">Terpercaya</p>
                            </div>
                        </div>
                    </div>
                </div>
                
                <!-- Bagian Kanan - Form Login -->
                <div class="md:w-7/12 p-6 md:p-8 flex flex-col">
                    <div class="mb-6">
                        <h3 class="text-primary-dark text-xl font-bold">Selamat Datang</h3>
                        <p class="text-gray-500 text-sm">Silakan login untuk mengakses sistem</p>
                    </div>
                    
                    <?php if (!empty($error)): ?>
                        <div class="mb-4 text-red-600 bg-red-50 border-l-4 border-red-500 p-3 rounded-r text-sm">
                            <p><?= htmlspecialchars($error); ?></p>
                        </div>
                    <?php endif; ?>

                    <form action="login.php" method="POST" class="space-y-5 flex-grow">
                        <div>
                            <label for="username" class="block text-gray-600 text-sm mb-1.5">Username</label>
                            <div class="relative">
                                <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
                                    <i class="fas fa-user text-gray-400"></i>
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
                        
                        <button type="submit"
                                class="w-full bg-primary hover:bg-primary-dark text-white font-medium py-2.5 px-4 rounded-lg transition duration-200 shadow-sm mt-6">
                            Login
                        </button>
                    </form>

                    <div class="mt-6 text-center text-sm">
                        <p class="text-gray-600">
                            Belum punya akun? 
                            <a href="registrasi.php" class="text-primary-dark hover:underline font-medium">
                                Daftar di sini
                            </a>
                        </p>
                    </div>

                    <div class="mt-auto pt-4 border-t border-gray-100 text-center text-xs text-gray-500">
                        © <?= date("Y"); ?> Sistem Pakar Penyakit Dada. All rights reserved.
                    </div>
                </div>
            </div>
        </div>

        <!-- Script untuk redirect setelah login -->
        <?php if ($success && !empty($redirectTo)): ?>
        <script>
            setTimeout(function() {
                window.location.href = "<?= $redirectTo ?>";
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