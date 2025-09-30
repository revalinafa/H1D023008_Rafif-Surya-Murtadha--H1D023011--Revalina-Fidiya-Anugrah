    <?php
session_start();
include '../Auth/connect.php';

if (!isset($_SESSION['id_user']) || $_SESSION['role'] !== 'pakar') {
    header("Location: ../Auth/login.php");
    exit();
}

$message = '';
$messageType = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    if (isset($_POST['action'])) {
        switch ($_POST['action']) {
            // Menambahkan relasi penyakit-gejala
            case 'add':
                $kode_penyakit = $_POST['kode_penyakit'];
                $kode_gejala = $_POST['kode_gejala'];
                
                // Mengecek apakah relasi sudah ada
                $checkQuery = "SELECT * FROM penyakit_gejala WHERE kode_penyakit = ? AND kode_gejala = ?";
                $checkStmt = $conn->prepare($checkQuery);
                $checkStmt->bind_param("ss", $kode_penyakit, $kode_gejala);
                $checkStmt->execute();
                $checkResult = $checkStmt->get_result();
                
                if ($checkResult->num_rows > 0) {
                    $message = "Relasi penyakit-gejala sudah ada!";
                    $messageType = "error";
                } else {
                    // Jika belum ada, tambahkan relasi baru
                    $insertQuery = "INSERT INTO penyakit_gejala (kode_penyakit, kode_gejala) VALUES (?, ?)";
                    $insertStmt = $conn->prepare($insertQuery);
                    $insertStmt->bind_param("ss", $kode_penyakit, $kode_gejala);
                    
                    if ($insertStmt->execute()) {
                        $message = "Relasi penyakit-gejala berhasil ditambahkan!";
                        $messageType = "success";
                    } else {
                        $message = "Gagal menambahkan relasi penyakit-gejala!";
                        $messageType = "error";
                    }
                }
                break;
                
            // Mengedit relasi penyakit-gejala
            case 'edit':
                $id = $_POST['id'];
                $kode_penyakit = $_POST['kode_penyakit'];
                $kode_gejala = $_POST['kode_gejala'];
                
                // Mengecek apakah relasi sudah ada (kecuali untuk ID yang sedang diedit)
                $checkQuery = "SELECT * FROM penyakit_gejala WHERE kode_penyakit = ? AND kode_gejala = ? AND id != ?";
                $checkStmt = $conn->prepare($checkQuery);
                $checkStmt->bind_param("ssi", $kode_penyakit, $kode_gejala, $id);
                $checkStmt->execute();
                $checkResult = $checkStmt->get_result();
                
                if ($checkResult->num_rows > 0) {
                    $message = "Relasi penyakit-gejala sudah ada!";
                    $messageType = "error";
                } else {
                    // Jika belum ada, perbarui relasi
                    $updateQuery = "UPDATE penyakit_gejala SET kode_penyakit = ?, kode_gejala = ? WHERE id = ?";
                    $updateStmt = $conn->prepare($updateQuery);
                    $updateStmt->bind_param("ssi", $kode_penyakit, $kode_gejala, $id);
                    
                    if ($updateStmt->execute()) {
                        $message = "Relasi penyakit-gejala berhasil diperbarui!";
                        $messageType = "success";
                    } else {
                        $message = "Gagal memperbarui relasi penyakit-gejala!";
                        $messageType = "error";
                    }
                }
                break;
               
            // Menghapus relasi penyakit-gejala
            case 'delete':
                $id = $_POST['id'];
                $deleteQuery = "DELETE FROM penyakit_gejala WHERE id = ?";
                $deleteStmt = $conn->prepare($deleteQuery);
                $deleteStmt->bind_param("i", $id);
                
                if ($deleteStmt->execute()) {
                    $message = "Relasi penyakit-gejala berhasil dihapus!";
                    $messageType = "success";
                } else {
                    $message = "Gagal menghapus relasi penyakit-gejala!";
                    $messageType = "error";
                }
                break;
        }
    }
}

// Get all penyakit
$penyakitOptions = [];
$penyakitQuery = "SELECT kode_penyakit, nama_penyakit FROM penyakit ORDER BY kode_penyakit ASC";
$penyakitResult = $conn->query($penyakitQuery);
while ($row = $penyakitResult->fetch_assoc()) {
    $penyakitOptions[] = $row;
}

// Get all gejala
$gejalaOptions = [];
$gejalaQuery = "SELECT kode_gejala, nama_gejala FROM gejala ORDER BY kode_gejala ASC";
$gejalaResult = $conn->query($gejalaQuery);
while ($row = $gejalaResult->fetch_assoc()) {
    $gejalaOptions[] = $row;
}

// JOIN untuk Relasi antara penyakit dan gejala
$penyakitGejala = [];
$query = "
    SELECT 
        pg.id,
        pg.kode_penyakit,
        pg.kode_gejala,
        p.nama_penyakit,
        g.nama_gejala
    FROM penyakit_gejala pg
    LEFT JOIN penyakit p ON pg.kode_penyakit = p.kode_penyakit
    LEFT JOIN gejala g ON pg.kode_gejala = g.kode_gejala
    ORDER BY pg.kode_penyakit ASC, pg.kode_gejala ASC
";
$result = $conn->query($query);
while ($row = $result->fetch_assoc()) {
    $penyakitGejala[] = $row;
}

// Edit data relasi penyakit-gejala
$editData = null;
if (isset($_GET['edit_id'])) {
    $edit_id = $_GET['edit_id'];
    $editQuery = "SELECT * FROM penyakit_gejala WHERE id = ?";
    $editStmt = $conn->prepare($editQuery);
    $editStmt->bind_param("i", $edit_id);
    $editStmt->execute();
    $editResult = $editStmt->get_result();
    $editData = $editResult->fetch_assoc();
}
?>

<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Kelola Aturan Penyakit-Gejala - Sistem Pakar</title>
    <script src="https://cdn.tailwindcss.com"></script>
</head>
    <body class="bg-gray-50 text-gray-800">

        <!-- Navbar -->
        <nav class="flex items-center justify-between bg-purple-300 text-purple-900 px-6 py-4 shadow-md">
            <h2 class="text-xl font-bold">Kelola Aturan Penyakit-Gejala</h2>
            <div class="space-x-4">
                <a href="dashboard_pakar.php" class="hover:underline font-medium">Dashboard</a>
                <a href="../Auth/logout.php" class="hover:underline font-medium">Logout</a>
            </div>
        </nav>

        <!-- Statistik -->
        <div class="max-w-6xl mx-auto px-4 py-6">
            <div class="mt-6 bg-white p-4 rounded shadow-md">
                <h4 class="text-md font-bold text-purple-800 mb-2">Statistik</h4>
                <div class="grid grid-cols-1 md:grid-cols-3 gap-4 text-sm">
                    <div class="text-center">
                        <div class="text-2xl font-bold text-purple-600"><?php echo count($penyakitGejala); ?></div>
                        <div class="text-gray-600">Total Aturan</div>
                    </div>
                    <div class="text-center">
                        <div class="text-2xl font-bold text-purple-600"><?php echo count($penyakitOptions); ?></div>
                        <div class="text-gray-600">Total Penyakit</div>
                    </div>
                    <div class="text-center">
                        <div class="text-2xl font-bold text-purple-600"><?php echo count($gejalaOptions); ?></div>
                        <div class="text-gray-600">Total Gejala</div>
                    </div>
                </div>
            </div> 
        </div>      

        <div class="max-w-6xl mx-auto px-4 py-6">
            <?php if ($message): ?>
                <div class="mb-4 p-4 rounded <?php echo $messageType === 'success' ? 'bg-green-100 text-green-700 border border-green-300' : 'bg-red-100 text-red-700 border border-red-300'; ?>">
                    <?php echo htmlspecialchars($message); ?>
                </div>
            <?php endif; ?>

            <!-- Formulir tambah dan edit -->
            <div class="bg-white p-6 rounded shadow-md mb-6">
                <h3 class="text-lg font-bold text-purple-800 mb-4">
                    <?php echo $editData ? 'Edit Relasi Penyakit-Gejala' : 'Tambah Relasi Penyakit-Gejala'; ?>
                </h3>
                
                <form method="POST" class="space-y-4">
                    <input type="hidden" name="action" value="<?php echo $editData ? 'edit' : 'add'; ?>">
                    <?php if ($editData): ?>
                        <input type="hidden" name="id" value="<?php echo $editData['id']; ?>">
                    <?php endif; ?>
                    
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-2">Penyakit</label>
                            <select name="kode_penyakit" required class="w-full p-2 border border-gray-300 rounded focus:ring-purple-500 focus:border-purple-500">
                                <option value="">Pilih Penyakit</option>
                                <?php foreach ($penyakitOptions as $penyakit): ?>
                                    <option value="<?php echo $penyakit['kode_penyakit']; ?>" 
                                        <?php echo ($editData && $editData['kode_penyakit'] === $penyakit['kode_penyakit']) ? 'selected' : ''; ?>>
                                        <?php echo $penyakit['kode_penyakit'] . ' - ' . $penyakit['nama_penyakit']; ?>
                                    </option>
                                <?php endforeach; ?>
                            </select>
                        </div>
                        
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-2">Gejala</label>
                            <select name="kode_gejala" required class="w-full p-2 border border-gray-300 rounded focus:ring-purple-500 focus:border-purple-500">
                                <option value="">Pilih Gejala</option>
                                <?php foreach ($gejalaOptions as $gejala): ?>
                                    <option value="<?php echo $gejala['kode_gejala']; ?>"
                                        <?php echo ($editData && $editData['kode_gejala'] === $gejala['kode_gejala']) ? 'selected' : ''; ?>>
                                        <?php echo $gejala['kode_gejala'] . ' - ' . $gejala['nama_gejala']; ?>
                                    </option>
                                <?php endforeach; ?>
                            </select>
                        </div>
                    </div>
                    
                    <div class="flex space-x-2">
                        <button type="submit" class="bg-purple-500 hover:bg-purple-600 text-white px-4 py-2 rounded shadow">
                            <?php echo $editData ? 'Perbarui' : 'Tambah'; ?>
                        </button>
                        
                        <?php if ($editData): ?>
                            <a href="penyakit_gejala.php" class="bg-gray-500 hover:bg-gray-600 text-white px-4 py-2 rounded shadow">
                                Batal
                            </a>
                        <?php endif; ?>
                    </div>
                </form>
            </div>

            <!-- Tampilan data relasi -->
            <div class="bg-white rounded shadow-md overflow-hidden">
                <div class="bg-purple-100 px-6 py-3">
                    <h3 class="text-lg font-bold text-purple-800">Data Aturan Penyakit-Gejala</h3>
                </div>
                
                <div class="overflow-x-auto">
                    <table class="min-w-full">
                        <thead class="bg-gray-50">
                            <tr>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">No</th>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Kode Penyakit</th>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Nama Penyakit</th>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Kode Gejala</th>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Nama Gejala</th>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Aksi</th>
                            </tr>
                        </thead>
                        <tbody class="bg-white divide-y divide-gray-200">
                            <?php if (empty($penyakitGejala)): ?>
                                <tr>
                                    <td colspan="6" class="px-6 py-4 text-center text-gray-500">Tidak ada data aturan penyakit-gejala</td>
                                </tr>
                            <?php else: ?>
                                <?php foreach ($penyakitGejala as $index => $row): ?>
                                    <tr class="hover:bg-gray-50">
                                        <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900"><?php echo $index + 1; ?></td>
                                        <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900"><?php echo htmlspecialchars($row['kode_penyakit']); ?></td>
                                        <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900"><?php echo htmlspecialchars($row['nama_penyakit'] ?? '-'); ?></td>
                                        <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900"><?php echo htmlspecialchars($row['kode_gejala']); ?></td>
                                        <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900"><?php echo htmlspecialchars($row['nama_gejala'] ?? '-'); ?></td>
                                        <td class="px-6 py-4 whitespace-nowrap text-sm space-x-2">
                                            <a href="?edit_id=<?php echo $row['id']; ?>" 
                                            class="bg-blue-500 hover:bg-blue-600 text-white px-3 py-1 rounded text-xs">
                                                Edit
                                            </a>
                                            <form method="POST" class="inline" onsubmit="return confirm('Apakah Anda yakin ingin menghapus relasi ini?');">
                                                <input type="hidden" name="action" value="delete">
                                                <input type="hidden" name="id" value="<?php echo $row['id']; ?>">
                                                <button type="submit" class="bg-red-500 hover:bg-red-600 text-white px-3 py-1 rounded text-xs">
                                                    Hapus
                                                </button>
                                            </form>
                                        </td>
                                    </tr>
                                <?php endforeach; ?>
                            <?php endif; ?>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>

    </body>
</html>

<?php $conn->close(); ?>