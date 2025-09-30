<?php
    session_start();
    include '../Auth/connect.php';

    if (!isset($_SESSION['id_user']) || $_SESSION['role'] !== 'pakar') {
        die("Akses ditolak.");
    }

    // Membuat layout (render) untuk export ke Word
    header("Content-type: application/vnd.ms-word");
    header("Content-Disposition: attachment;Filename=riwayat_konsultasi.doc");

    echo "<html><meta charset='UTF-8'><body>";
    echo "<h2>Riwayat Konsultasi Semua User</h2>";
    echo "<table border='1' cellpadding='5' cellspacing='0'>";
    echo "<tr><th>No</th><th>Nama User</th><th>Hasil Diagnosa</th><th>Gejala Dipilih</th><th>Waktu</th></tr>";

    // Mengambil data riwayat konsultasi yang akan diexport
    $sql = "SELECT r.*, u.nama FROM riwayat_konsultasi r JOIN user u ON r.id_user = u.id_user ORDER BY r.waktu_konsultasi DESC";
    $result = $conn->query($sql);
    $no = 1;

    while ($row = $result->fetch_assoc()) {
        $gejala = implode(', ', json_decode($row['gejala_dipilih']));
        echo "<tr>
                <td>{$no}</td>
                <td>{$row['nama']}</td>
                <td>{$row['hasil_diagnosa']}</td>
                <td>{$gejala}</td>
                <td>{$row['waktu_konsultasi']}</td>
            </tr>";
        $no++;
    }
    echo "</table></body></html>";
    $conn->close();
?>
