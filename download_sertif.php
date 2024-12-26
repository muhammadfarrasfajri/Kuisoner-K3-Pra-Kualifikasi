<?php
session_start();
$user_id = $_SESSION['user_id'] ?? null; // Ambil user_id dari sesi

if (!$user_id) {
    echo "Akses ditolak. Anda harus login.";
    exit;
}

if (isset($_GET['file'])) {
    $filePath = $_GET['file'];

    // Include file koneksi database
    require_once './includes/config.php'; // Pastikan path ini sesuai lokasi sebenarnya

    // Validasi apakah file milik user_id yang sedang login
    $query = "SELECT sertifikat FROM profil_kontraktor WHERE user_id = :user_id AND sertifikat = :sertifikat";
    $stmt = $pdo->prepare($query);
    $stmt->execute([
        'user_id' => $user_id,
        'sertifikat' => $filePath
    ]);

    $data = $stmt->fetch(PDO::FETCH_ASSOC);

    if ($data && file_exists($filePath)) {
        // Melayani file untuk diunduh
        header('Content-Description: File Transfer');
        header('Content-Type: application/octet-stream');
        header('Content-Disposition: attachment; filename="' . basename($filePath) . '"');
        header('Expires: 0');
        header('Cache-Control: must-revalidate');
        header('Pragma: public');
        header('Content-Length: ' . filesize($filePath));
        readfile($filePath);
        exit;
    } else {
        echo "File tidak ditemukan atau Anda tidak memiliki akses.";
    }
} else {
    echo "Parameter file tidak tersedia.";
}
