<?php
require './includes/config.php';

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_FILES['sertifikat'])) {
    $errors = [];
    $file = $_FILES['sertifikat'];

    // Ambil nilai selectedPerusahaan dari form
    if (isset($_POST['selectedPerusahaan'])) {
        $selectedPerusahaan = $_POST['selectedPerusahaan'];
    } else {
        // Jika tidak ada nilai selectedPerusahaan, tampilkan error atau set default
        $errors[] = 'Nama perusahaan tidak ditemukan.';
    }

    // Lanjutkan dengan proses upload file dan update database
    if (empty($errors)) {
        // Cek apakah file ada dan tidak ada error
        if ($file['error'] === 0) {
            // Tentukan direktori penyimpanan file
            $uploadDir = 'uploads/'; // Ganti dengan direktori sesuai kebutuhan
            $fileName = basename($file['name']);
            $filePath = $uploadDir . $fileName;

            // Cek apakah file sudah ada
            if (file_exists($filePath)) {
                $errors[] = 'File sudah ada.';
            }

            // Tentukan jenis file yang diizinkan
            $allowedTypes = ['application/pdf', 'image/jpeg', 'image/png'];
            if (!in_array($file['type'], $allowedTypes)) {
                $errors[] = 'Jenis file tidak valid.';
            }

            // Jika tidak ada error, lakukan upload dan update database
            if (empty($errors)) {
                if (move_uploaded_file($file['tmp_name'], $filePath)) {
                    // Update kolom sertifikat di tabel profil_kontraktor berdasarkan nama perusahaan
                    $query = "UPDATE profil_kontraktor SET sertifikat = :sertifikat WHERE nama_perusahaan = :nama_perusahaan";
                    $stmt = $pdo->prepare($query);
                    $stmt->execute([
                        'sertifikat' => $filePath,
                        'nama_perusahaan' => $selectedPerusahaan
                    ]);
                    header('Location: ../MAGANG/page/respon_kuisoner.php');
                } else {
                    echo "Gagal meng-upload sertifikat.";
                }
            } else {
                // Tampilkan error
                foreach ($errors as $error) {
                    echo $error . "<br>";
                }
            }
        } else {
            echo "Tidak ada file yang diupload atau terjadi kesalahan pada file.";
        }
    }
}
