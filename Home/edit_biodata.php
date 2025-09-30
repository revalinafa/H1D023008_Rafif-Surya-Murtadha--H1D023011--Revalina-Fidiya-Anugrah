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

    $success = $error = "";

    // Mengambil data user (nama, username, role)
    if ($_SERVER['REQUEST_METHOD'] == 'POST') {
        $nama = trim($_POST['nama']);
        $username = trim($_POST['username']);
        $password = $_POST['password'];
        $confirm = $_POST['confirm_password'];

        if (empty($nama) || empty($username)) {
            $error = "Nama dan Username tidak boleh kosong.";
        } else {
            // Cek jika username sudah digunakan user lain
            $sql = "SELECT id_user FROM user WHERE username = ? AND id_user != ?";
            $stmt = $conn->prepare($sql);
            $stmt->bind_param("si", $username, $id_user);
            $stmt->execute();
            $stmt->store_result();

            if ($stmt->num_rows > 0) {
                $error = "Username sudah digunakan.";
            } else {
                if (!empty($password)) {
                    if ($password !== $confirm) {
                        $error = "Konfirmasi password tidak cocok.";
                    } else {
                        $hashed = password_hash($password, PASSWORD_DEFAULT);
                        $update = $conn->prepare("UPDATE user SET nama = ?, username = ?, password = ? WHERE id_user = ?");
                        $update->bind_param("sssi", $nama, $username, $hashed, $id_user);
                    }
                } else {
                    $update = $conn->prepare("UPDATE user SET nama = ?, username = ? WHERE id_user = ?");
                    $update->bind_param("ssi", $nama, $username, $id_user);
                }

                if ($update->execute()) {
                    $_SESSION['nama'] = $nama;
                    $_SESSION['username'] = $username;
                    $success = "Biodata berhasil diperbarui.";
                } else {
                    $error = "Gagal memperbarui biodata.";
                }

                $update->close();
            }
            $stmt->close();
        }
    }
?>

<!DOCTYPE html>
<html lang="id">
    <head>
        <meta charset="UTF-8">
        <meta name="viewport" content="width=device-width, initial-scale=1.0">
        <title>Edit Biodata - Sistem Pakar Penyakit Dada</title>
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
            <div class="bg-white rounded-xl shadow-lg w-full max-w-4xl mx-auto overflow-hidden">
                <div class="flex flex-col md:flex-row">
                    <!-- Sidebar -->
                    <div class="md:w-1/3 bg-gradient-to-b from-primary-dark to-emerald-800 text-white p-8 flex flex-col items-center justify-center">
                        <div class="w-24 h-24 rounded-full bg-white/20 flex items-center justify-center text-white mb-6">
                            <i class="fas fa-user-edit text-5xl"></i>
                        </div>
                        <h3 class="text-xl font-bold mb-2"><?= htmlspecialchars($user['nama']); ?></h3>
                        <span class="inline-flex items-center px-3 py-1 rounded-full text-xs font-medium bg-white/20 text-white mb-6">
                            <?= htmlspecialchars($user['role']); ?>
                        </span>
                        <p class="text-white/80 text-center text-sm mb-4">Perbarui informasi akun Anda untuk menjaga data tetap akurat dan terkini.</p>
                    </div>
                    
                    <!-- Formulir Edit Profil -->
                    <div class="md:w-2/3 p-8">
                        <h2 class="text-xl font-semibold text-primary-dark mb-6 flex items-center">
                            <i class="fas fa-pen-to-square mr-3"></i>
                            <span>Form Edit Biodata</span>
                        </h2>
                        
                        <?php if ($success): ?>
                            <div class="mb-6 bg-green-50 border-l-4 border-green-500 text-green-700 p-4 rounded-r">
                                <div class="flex items-center">
                                    <i class="fas fa-check-circle mr-3"></i>
                                    <p><?= $success ?></p>
                                </div>
                            </div>
                        <?php elseif ($error): ?>
                            <div class="mb-6 bg-red-50 border-l-4 border-red-500 text-red-700 p-4 rounded-r">
                                <div class="flex items-center">
                                    <i class="fas fa-exclamation-circle mr-3"></i>
                                    <p><?= $error ?></p>
                                </div>
                            </div>
                        <?php endif; ?>

                        <form method="POST" onsubmit="return validateForm();" class="space-y-5">
                            <div class="form-group">
                                <label class="block text-sm font-medium text-gray-700 mb-1">
                                    <i class="fas fa-id-card text-primary-dark mr-2"></i>Nama Lengkap
                                </label>
                                <input type="text" name="nama" required value="<?= htmlspecialchars($user['nama']); ?>"
                                    class="w-full border border-gray-300 px-4 py-3 rounded-lg focus:outline-none focus:ring-2 focus:ring-primary-dark focus:border-transparent transition">
                            </div>

                            <div class="form-group">
                                <label class="block text-sm font-medium text-gray-700 mb-1">
                                    <i class="fas fa-user text-primary-dark mr-2"></i>Username
                                </label>
                                <input type="text" name="username" required value="<?= htmlspecialchars($user['username']); ?>"
                                    class="w-full border border-gray-300 px-4 py-3 rounded-lg focus:outline-none focus:ring-2 focus:ring-primary-dark focus:border-transparent transition">
                            </div>

                            <div class="form-group">
                                <label class="block text-sm font-medium text-gray-700 mb-1">
                                    <i class="fas fa-key text-primary-dark mr-2"></i>Password Baru (opsional)
                                </label>
                                <div class="relative">
                                    <input type="password" name="password" id="password"
                                        class="w-full border border-gray-300 px-4 py-3 rounded-lg focus:outline-none focus:ring-2 focus:ring-primary-dark focus:border-transparent transition">
                                    <button type="button" onclick="togglePassword('password', 'eye1')" class="absolute right-3 top-3 text-gray-500">
                                        <i id="eye1" class="fas fa-eye"></i>
                                    </button>
                                </div>
                            </div>

                            <div class="form-group">
                                <label class="block text-sm font-medium text-gray-700 mb-1">
                                    <i class="fas fa-check-double text-primary-dark mr-2"></i>Konfirmasi Password
                                </label>
                                <div class="relative">
                                    <input type="password" name="confirm_password" id="confirm_password"
                                        class="w-full border border-gray-300 px-4 py-3 rounded-lg focus:outline-none focus:ring-2 focus:ring-primary-dark focus:border-transparent transition">
                                    <button type="button" onclick="togglePassword('confirm_password', 'eye2')" class="absolute right-3 top-3 text-gray-500">
                                        <i id="eye2" class="fas fa-eye"></i>
                                    </button>
                                </div>
                            </div>

                            <div class="flex justify-between pt-4 mt-4">
                                <a href="biodata.php" 
                                   class="px-6 py-3 bg-gray-100 hover:bg-gray-200 text-gray-700 rounded-lg transition flex items-center">
                                    <i class="fas fa-arrow-left mr-2"></i> Kembali
                                </a>
                                <button type="submit" 
                                        class="px-6 py-3 bg-primary hover:bg-primary-dark text-white rounded-lg transition flex items-center">
                                    <i class="fas fa-save mr-2"></i> Simpan Perubahan
                                </button>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
        </main>

        <script>
            function togglePassword(inputId, eyeId) {
                const input = document.getElementById(inputId);
                const eye = document.getElementById(eyeId);
                
                if (input.type === "password") {
                    input.type = "text";
                    eye.classList.remove("fa-eye");
                    eye.classList.add("fa-eye-slash");
                    eye.classList.add("text-primary-dark");
                } else {
                    input.type = "password";
                    eye.classList.remove("fa-eye-slash");
                    eye.classList.add("fa-eye");
                    eye.classList.remove("text-primary-dark");
                }
            }

            function validateForm() {
                const password = document.getElementById("password").value.trim();
                const confirm = document.getElementById("confirm_password").value.trim();

                if (password && !confirm) {
                    alert("Harap isi konfirmasi password.");
                    return false;
                }

                if (password && confirm && password !== confirm) {
                    alert("Konfirmasi password tidak cocok.");
                    return false;
                }

                return true;
            }
        </script>
    </body>
</html>

<?php $conn->close(); ?>