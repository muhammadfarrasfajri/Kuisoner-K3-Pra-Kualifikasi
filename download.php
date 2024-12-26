<?php
// Cek apakah parameter file tersedia
if (isset($_GET['file']) && isset($_GET['column'])) {
    // Ambil nama kolom dan path file dari parameter
    $column = $_GET['column'];
    $filePath = $_GET['file'];

    // Validasi nama kolom untuk keamanan
    $allowedColumns = [
        'kebijakan_k3_path',
        'sosialisasi_absen_path',
        'job_description_path',
        'program_inspeksi_path',
        'bukti_tindak_lanjut_inspeksi_path',
        'bukti_rapat_path',
        'bukti_tindak_lanjut_rapat_path',
        'panduan_k3_path',
        'daftar_buku_k3_path',
        'prosedur_kecelakaan_path',
        'prosedur_bahaya_path',
        'program_pelatihan_path',
        'jadwal_pelatihan_path',
        'sertifikat_ahli_k3_path',
        'jenis_apd_path',
        'prosedur_pemeliharaan_apd_path',
        'sertifikat_peralatan_kerja_path',
        'prosedur_material_b3_path',
        'bukti_pemeriksaan_kesehatan_path',
        'bukti_pemantauan_kesehatan_path',
        'bukti_program_pengendalian_path',
        'bukti_larangan_obat_path',
        'bukti_kebijakan_5s_path',
        'bukti_pengukuran_lingkungan_path',
        'bukti_kinerja_k3_path',
        'bukti_evaluasi_k3_path',
        'bukti_investigasi_kecelakaan_path',
        'bukti_saran_investigasi_path'
    ]; // Tambahkan nama kolom yang diperbolehkan
    if (!in_array($column, $allowedColumns)) {
        die('Akses tidak diizinkan.');
    }

    // Validasi file path untuk mencegah directory traversal
    $allowedPath = realpath('../uploads/');
    $fileRealPath = realpath($filePath);

    if ($fileRealPath === false || strpos($fileRealPath, $allowedPath) !== 0) {
        die('File tidak valid atau akses tidak diizinkan.');
    }

    // Periksa apakah file ada
    if (file_exists($filePath)) {
        // Nama file untuk diunduh
        $fileName = basename($filePath);

        // Header untuk memaksa download
        header('Content-Description: File Transfer');
        header('Content-Type: application/octet-stream');
        header('Content-Disposition: attachment; filename="' . $fileName . '"');
        header('Expires: 0');
        header('Cache-Control: must-revalidate');
        header('Pragma: public');
        header('Content-Length: ' . filesize($filePath));

        // Baca dan kirim file
        readfile($filePath);
        exit;
    } else {
        echo "File tidak ditemukan.";
    }
} else {
    echo "Parameter file atau kolom tidak valid.";
}
