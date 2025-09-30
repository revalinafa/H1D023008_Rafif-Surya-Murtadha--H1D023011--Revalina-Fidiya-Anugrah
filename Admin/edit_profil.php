<?php
    session_start();
    include '../Auth/connect.php';

    if (!isset($_SESSION['id_user']) || $_SESSION['role'] !== 'admin') {
        header("Location: ../Auth/login.php");
        exit();
    }

    $id_user = $_SESSION['id_user'];
    $message = '';
    $messageType = '';

    // Ambil data admin saat ini
    $sql = "SELECT nama, username FROM user WHERE id_user = ?";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("i", $id_user);
    $stmt->execute();
    $result = $stmt->get_result();
    $admin = $result->fetch_assoc();
    $stmt->close();

    // Proses form submission
    if ($_SERVER['REQUEST_METHOD'] === 'POST') {
        $nama = trim($_POST['nama']);
        $username = trim($_POST['username']);
        $password_baru = $_POST['password_baru'] ?? '';
        $konfirmasi_password = $_POST['konfirmasi_password'] ?? '';

        // Validasi input
        if (empty($nama) || empty($username)) {
            $message = 'Nama dan username tidak boleh kosong!';
            $messageType = 'error';
        } else {
            // Cek apakah username sudah digunakan oleh user lain
            $checkUsername = $conn->prepare("SELECT id_user FROM user WHERE username = ? AND id_user != ?");
            $checkUsername->bind_param("si", $username, $id_user);
            $checkUsername->execute();
            $result = $checkUsername->get_result();
            
            if ($result->num_rows > 0) {
                $message = 'Username sudah digunakan oleh pengguna lain!';
                $messageType = 'error';
            } else {
                // Update nama dan username
                $updateProfile = $conn->prepare("UPDATE user SET nama = ?, username = ? WHERE id_user = ?");
                $updateProfile->bind_param("ssi", $nama, $username, $id_user);
                
                if ($updateProfile->execute()) {
                    $message = 'Profil berhasil diperbarui!';
                    $messageType = 'success';
                    
                    // Update data admin untuk ditampilkan
                    $admin['nama'] = $nama;
                    $admin['username'] = $username;
                } else {
                    $message = 'Gagal memperbarui profil!';
                    $messageType = 'error';
                }
                
                // Jika ada password baru
                if (!empty($password_baru)) {
                    if ($password_baru === $konfirmasi_password) {
                        if (strlen($password_baru) >= 6) {
                            $hashedPassword = password_hash($password_baru, PASSWORD_DEFAULT);
                            $updatePassword = $conn->prepare("UPDATE user SET password = ? WHERE id_user = ?");
                            $updatePassword->bind_param("si", $hashedPassword, $id_user);
                            
                            if ($updatePassword->execute()) {
                                $message .= ' Password berhasil diubah!';
                            } else {
                                $message .= ' Namun gagal mengubah password!';
                                $messageType = 'warning';
                            }
                        } else {
                            $message .= ' Password baru minimal 6 karakter!';
                            $messageType = 'warning';
                        }
                    } else {
                        $message .= ' Konfirmasi password tidak cocok!';
                        $messageType = 'warning';
                    }
                }
            }
            $checkUsername->close();
        }
    }
?>

<!DOCTYPE html>
<html lang="id">
    <head>
        <meta charset="UTF-8">
        <meta name="viewport" content="width=device-width, initial-scale=1.0">
        <title>Edit Profil Admin - Sistem Pakar</title>
        <script src="https://cdn.tailwindcss.com"></script>
    </head>
    <body class="bg-gray-50 text-gray-800">

        <div class="max-w-2xl mx-auto px-4 py-6">
            <div class="flex items-center mb-6">
                <h1 class="text-2xl font-semibold">Edit Profil Admin</h1>
            </div>

            <!-- Alert Message -->
            <?php if (!empty($message)): ?>
                <div class="mb-6 p-4 rounded-lg <?php 
                    echo $messageType === 'success' ? 'bg-green-100 text-green-800 border border-green-200' : 
                        ($messageType === 'warning' ? 'bg-yellow-100 text-yellow-800 border border-yellow-200' : 
                        'bg-red-100 text-red-800 border border-red-200'); 
                ?>">
                    <?= htmlspecialchars($message) ?>
                </div>
            <?php endif; ?>

            <!-- Edit Profile Form -->
            <div class="bg-white rounded-lg shadow-sm border border-gray-200 p-6">
                <form method="POST" class="space-y-6">
                    <div>
                        <label for="nama" class="block text-sm font-medium text-gray-700 mb-2">
                            Nama Lengkap *
                        </label>
                        <input type="text" id="nama" name="nama" 
                               value="<?= htmlspecialchars($admin['nama']) ?>"
                               class="w-full px-3 py-2 border border-gray-300 rounded-md focus:ring-purple-500 focus:border-purple-500"
                               required>
                    </div>

                    <div>
                        <label for="username" class="block text-sm font-medium text-gray-700 mb-2">
                            Username *
                        </label>
                        <input type="text" id="username" name="username" 
                               value="<?= htmlspecialchars($admin['username']) ?>"
                               class="w-full px-3 py-2 border border-gray-300 rounded-md focus:ring-purple-500 focus:border-purple-500"
                               required>
                    </div>

                    <hr class="border-gray-200">

                    <div>
                        <h3 class="text-md font-medium text-gray-900 mb-3">Ubah Password (Opsional)</h3>
                        <p class="text-sm text-gray-600 mb-4">Kosongkan jika tidak ingin mengubah password</p>
                        
                        <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                            <div>
                                <label for="password_baru" class="block text-sm font-medium text-gray-700 mb-2">
                                    Password Baru
                                </label>
                                <input type="password" id="password_baru" name="password_baru"
                                       class="w-full px-3 py-2 border border-gray-300 rounded-md focus:ring-purple-500 focus:border-purple-500"
                                       minlength="6">
                                <p class="text-xs text-gray-500 mt-1">Minimal 6 karakter</p>
                            </div>

                            <div>
                                <label for="konfirmasi_password" class="block text-sm font-medium text-gray-700 mb-2">
                                    Konfirmasi Password
                                </label>
                                <input type="password" id="konfirmasi_password" name="konfirmasi_password"
                                       class="w-full px-3 py-2 border border-gray-300 rounded-md focus:ring-purple-500 focus:border-purple-500">
                            </div>
                        </div>
                    </div>

                    <!-- Action Buttons -->
                    <div class="flex items-center justify-end space-x-4 pt-4">
                        <a href="profil.php" 
                           class="px-4 py-2 text-gray-600 hover:text-gray-800">
                            Batal
                        </a>
                        
                        <button type="submit" 
                                class="px-6 py-2 bg-purple-600 hover:bg-purple-700 text-white rounded-md">
                            Simpan Perubahan
                        </button>
                    </div>
                </form>
            </div>
        </div>

    </body>
</html>

<?php $conn->close(); ?>