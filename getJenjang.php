<?php
// Aktifkan error reporting untuk debugging
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

// Include file konfigurasi koneksi
require 'config.php';

// Cek koneksi ke database
if (!$conn) {
    die("Koneksi ke database gagal: " . mysqli_connect_error());
}

// SQL query
$sql = "
    SELECT jenjang_program_studi 
    FROM program_studi_dibukas 
    GROUP BY jenjang_program_studi
";

// Jalankan query
$result = mysqli_query($conn, $sql);

// Cek apakah query berhasil
if ($result) {
    // Ambil hasil sebagai array asosiatif
    $rows = mysqli_fetch_all($result, MYSQLI_ASSOC);
    
    // Check if S3 exists
    $hasS3 = false;
    foreach ($rows as $row) {
        if ($row['jenjang_program_studi'] === 'S3') {
            $hasS3 = true;
            break;
        }
    }
    
    // Add S3 manually if not exists
    if (!$hasS3) {
        $rows[] = ['jenjang_program_studi' => 'S3'];
    }

    // Tampilkan hasil dalam format JSON
    echo json_encode($rows);
} else {
    // Tampilkan pesan error jika query gagal
    echo "Query Error: " . mysqli_error($conn);
}

// Tutup koneksi
mysqli_close($conn);
?>
