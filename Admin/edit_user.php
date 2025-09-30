<?php
    session_start();
    include '../Auth/connect.php';

    if (!isset($_SESSION['id_user']) || $_SESSION['role'] !== 'admin') {
        header("Location: ../Auth/login.php");
        exit();
    }

    // Mengambil data user berdasarkan ID
    $id = $_GET['id'] ?? null;
    if (!$id) {
        echo "ID tidak valid.";
        exit();
    }

    $stmt = $conn->prepare("SELECT * FROM user WHERE id_user = ?");
    $stmt->bind_param("i", $id);
    $stmt->execute();
    $data = $stmt->get_result()->fetch_assoc();

    if (!$data) {
        echo "User tidak ditemukan.";
        exit();
    }

    // Proses update data user (apabila berhasil diarahkan ke halaman user.php)
    if ($_SERVER['REQUEST_METHOD'] === 'POST') {
        $nama = $_POST['nama'];
        $username = $_POST['username'];
        $role = $_POST['role'];

        if (!empty($_POST['password'])) {
            $password = password_hash($_POST['password'], PASSWORD_DEFAULT);
            $stmt = $conn->prepare("UPDATE user SET nama=?, username=?, password=?, role=? WHERE id_user=?");
            $stmt->bind_param("ssssi", $nama, $username, $password, $role, $id);
        } else {
            $stmt = $conn->prepare("UPDATE user SET nama=?, username=?, role=? WHERE id_user=?");
            $stmt->bind_param("sssi", $nama, $username, $role, $id);
        }

        if ($stmt->execute()) {
            // Redirect ke user.php dengan parameter success
            header("Location: user.php?success=edit");
            exit();
        } else {
            echo "Gagal update user: " . $conn->error;
        }
    }
?>

<!DOCTYPE html>
<html lang="id">
    <head>
        <meta charset="UTF-8">
        <title>Edit User</title>
        <script src="https://cdn.tailwindcss.com"></script>
    </head>
    <body class="bg-purple-50 min-h-screen">
        <div class="max-w-xl mx-auto mt-8 bg-white p-6 rounded shadow">
            <h2 class="text-2xl font-bold mb-4 text-purple-800">Edit User</h2>
            <form method="POST" class="space-y-4">
                <div>
                    <label class="block text-sm font-medium text-gray-700">Nama:</label>
                    <input type="text" name="nama" value="<?= htmlspecialchars($data['nama']); ?>" required class="w-full border border-gray-300 px-3 py-2 rounded">
                </div>
                <div>
                    <label class="block text-sm font-medium text-gray-700">Username:</label>
                    <input type="text" name="username" value="<?= htmlspecialchars($data['username']); ?>" required class="w-full border border-gray-300 px-3 py-2 rounded">
                </div>
                <div>
                    <label class="block text-sm font-medium text-gray-700">Password (opsional):</label>
                    <input type="password" name="password" placeholder="Kosongkan jika tidak ingin mengganti" class="w-full border border-gray-300 px-3 py-2 rounded">
                </div>
                <div>
                    <label class="block text-sm font-medium text-gray-700">Role:</label>
                    <select name="role" required class="w-full border border-gray-300 px-3 py-2 rounded">
                        <option value="admin" <?= $data['role'] === 'admin' ? 'selected' : ''; ?>>Admin</option>
                        <option value="user" <?= $data['role'] === 'user' ? 'selected' : ''; ?>>User</option>
                        <option value="pakar" <?= $data['role'] === 'pakar' ? 'selected' : ''; ?>>Pakar</option>
                    </select>
                </div>
                <div class="flex gap-2">
                    <button type="submit" class="bg-green-500 hover:bg-green-600 text-white px-4 py-2 rounded">Simpan Perubahan</button>
                    <a href="user.php" class="bg-gray-500 hover:bg-gray-600 text-white px-4 py-2 rounded">Kembali</a>
                </div>
            </form>
        </div>

    </body>
</html>