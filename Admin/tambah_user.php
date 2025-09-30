<?php
    session_start();
    include '../Auth/connect.php';

    if (!isset($_SESSION['id_user']) || $_SESSION['role'] !== 'admin') {
        header("Location: ../Auth/login.php");
        exit();
    }

    // Mengambil data user (nama, username, password, role) dari form yang diisi
    if ($_SERVER['REQUEST_METHOD'] === 'POST') {
        $nama = $_POST['nama'];
        $username = $_POST['username'];
        $password = password_hash($_POST['password'], PASSWORD_DEFAULT);
        $role = $_POST['role'];

        // Insert data user ke dalam database
        $stmt = $conn->prepare("INSERT INTO user (nama, username, password, role) VALUES (?, ?, ?, ?)");
        $stmt->bind_param("ssss", $nama, $username, $password, $role);

        if ($stmt->execute()) {
            // Redirect ke user.php dengan parameter success
            header("Location: user.php?success=add");
            exit();
        } else {
            echo "Gagal menambahkan user: " . $conn->error;
        }
    }
?>

<!DOCTYPE html>
<html lang="id">
    <head>
        <meta charset="UTF-8">
        <title>Tambah User</title>
        <script src="https://cdn.tailwindcss.com"></script>
    </head>
    <body class="bg-purple-50 min-h-screen">

        <!-- Form Tambah User -->
        <div class="max-w-xl mx-auto mt-8 bg-white p-6 rounded shadow">
            <h2 class="text-2xl font-bold mb-4 text-purple-800">Tambah User</h2>
            <form method="POST" class="space-y-4">
                <div>
                    <label class="block text-gray-700 font-medium">Nama:</label>
                    <input type="text" name="nama" required class="w-full border border-gray-300 px-3 py-2 rounded">
                </div>
                <div>
                    <label class="block text-gray-700 font-medium">Username:</label>
                    <input type="text" name="username" required class="w-full border border-gray-300 px-3 py-2 rounded">
                </div>
                <div>
                    <label class="block text-gray-700 font-medium">Password:</label>
                    <input type="password" name="password" required class="w-full border border-gray-300 px-3 py-2 rounded">
                </div>
                <div>
                    <label class="block text-gray-700 font-medium">Role:</label>
                    <select name="role" required class="w-full border border-gray-300 px-3 py-2 rounded">
                        <option value="admin">Admin</option>
                        <option value="user">User</option>
                        <option value="pakar">Pakar</option>
                    </select>
                </div>
                <div class="flex gap-2">
                    <button type="submit" class="bg-green-500 hover:bg-purple-600 text-white px-4 py-2 rounded">Simpan</button>
                    <a href="user.php" class="bg-gray-500 hover:bg-purple-600 text-white px-4 py-2 rounded">Kembali</a>
                </div>
            </form>
        </div>

    </body>
</html>