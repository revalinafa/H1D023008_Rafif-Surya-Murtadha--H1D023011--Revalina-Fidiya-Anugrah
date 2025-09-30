<?php
    session_start();
    include '../Auth/connect.php';

    // Cek apakah user sudah login
    if (!isset($_SESSION['id_user'])) {
        header("Location: ../Auth/login.php");
        exit();
    }

    $idUser = $_SESSION['id_user'];
    $nama = $_SESSION['nama'];

    // Ambil riwayat konsultasi dari database dengan JOIN ke tabel penyakit
    $sql = "SELECT r.*, p.kode_penyakit, p.nama_penyakit, p.solusi 
            FROM riwayat_konsultasi r
            LEFT JOIN penyakit p ON r.kode_penyakit = p.kode_penyakit
            WHERE r.id_user = ? 
            ORDER BY r.waktu_konsultasi DESC";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("i", $idUser);
    $stmt->execute();
    $result = $stmt->get_result();

    // Ambil semua data gejala
    $daftarGejala = [];
    $sqlGejala = "SELECT kode_gejala, nama_gejala FROM gejala";
    $resultGejala = $conn->query($sqlGejala);
    while ($row = $resultGejala->fetch_assoc()) {
        $daftarGejala[$row['kode_gejala']] = $row['nama_gejala'];
    }

    // Membuat layout (render) untuk export ke Word
    header("Content-Type: application/vnd.ms-word");
    header("Content-Disposition: attachment;Filename=riwayat_konsultasi_" . $nama . ".doc");

    echo "<!DOCTYPE html>";
    echo "<html lang='id'>";
    echo "<head>";
    echo "<meta charset='UTF-8'>";
    echo "<title>Riwayat Konsultasi - " . htmlspecialchars($nama) . "</title>";
    echo "<style>
            body { font-family: Arial, sans-serif; }
            h1 { color: #276F55; text-align: center; }
            h2 { color: #276F55; }
            table { border-collapse: collapse; width: 100%; margin-bottom: 20px; }
            th { background-color: #BCFFBC; padding: 8px; text-align: left; }
            td { padding: 8px; }
            tr:nth-child(even) { background-color: #f2f2f2; }
            .solusi { background-color: #e8f5e9; padding: 10px; border-radius: 5px; }
          </style>";
    echo "</head>";
    echo "<body>";
    echo "<h1>Riwayat Konsultasi Penyakit Dada</h1>";
    echo "<p><strong>Nama Pasien:</strong> " . htmlspecialchars($nama) . "</p>";
    echo "<p><strong>Tanggal Cetak:</strong> " . date("d-m-Y H:i:s") . "</p>";
    
    $no = 1;
    while ($row = $result->fetch_assoc()) {
        // Hitung probabilitas (sama seperti di file riwayat.php)
        $gejalaDipilih = json_decode($row['gejala_dipilih']);
        
        echo "<hr>";
        echo "<h2>Konsultasi #" . $no . "</h2>";
        echo "<p><strong>Waktu Konsultasi:</strong> " . $row['waktu_konsultasi'] . "</p>";
        echo "<p><strong>Hasil Diagnosa:</strong> " . htmlspecialchars($row['nama_penyakit'] ?? $row['hasil_diagnosa']) . " (" . htmlspecialchars($row['kode_penyakit'] ?? 'N/A') . ")</p>";
        
        echo "<h3>Gejala yang Dipilih:</h3>";
        echo "<ul>";
        $gejalaList = json_decode($row['gejala_dipilih']);
        foreach ($gejalaList as $kodeGejala) {
            echo "<li>" . (isset($daftarGejala[$kodeGejala]) ? 
                  htmlspecialchars($daftarGejala[$kodeGejala]) : 
                  htmlspecialchars($kodeGejala)) . "</li>";
        }
        echo "</ul>";
        
        echo "<h3>Solusi/Rekomendasi:</h3>";
        echo "<div class='solusi'>";
        echo nl2br(htmlspecialchars($row['solusi'] ?? 'Solusi tidak tersedia'));
        echo "</div>";
        
        $no++;
    }
    
    if ($no == 1) {
        echo "<p>Belum ada riwayat konsultasi.</p>";
    }
    
    echo "</body>";
    echo "</html>";

    $stmt->close();
    $conn->close();
?>