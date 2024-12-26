<?php
session_start();
require '../includes/config.php';

// Pastikan user login dan role adalah admin
if (!isset($_SESSION['user'])) {
    header('Location: ../page/login.php');
    exit;
}

if ($_SESSION['user']['role'] !== 'admin') {
    http_response_code(403);
    echo "Anda tidak memiliki izin untuk mengakses halaman ini.";
    exit;
}
// Ambil semua data perusahaan
$query = "SELECT * FROM profil_kontraktor";
$stmt = $pdo->prepare($query);
$stmt->execute();

// Ambil semua data sebagai array asosiasi
$dataPerusahaan = $stmt->fetchAll(PDO::FETCH_ASSOC);

// Query untuk nama perusahaan untuk dropdown
$query = "SELECT DISTINCT nama_perusahaan FROM profil_kontraktor";
$stmt = $pdo->query($query);
$perusahaanList = $stmt->fetchAll(PDO::FETCH_ASSOC);

// Jika nama perusahaan dipilih sebelumnya
$selectedPerusahaan = isset($_POST['nama_perusahaan']) ? $_POST['nama_perusahaan'] : '';

// Jika perusahaan dipilih
if ($selectedPerusahaan) {
    // Ambil data perusahaan berdasarkan nama perusahaan
    $query = "SELECT p.*, u.username
              FROM profil_kontraktor p
              JOIN users u ON p.user_id = u.id
              WHERE p.nama_perusahaan = :nama_perusahaan";
    $stmt = $pdo->prepare($query);
    $stmt->execute(['nama_perusahaan' => $selectedPerusahaan]);
    $dataPerusahaan = $stmt->fetch(PDO::FETCH_ASSOC);

    // Mengambil data direksi (jika ada)
    if (!empty($dataPerusahaan['direksi'])) {
        $direksi = json_decode($dataPerusahaan['direksi'], true); // Decoding JSON
    } else {
        $direksi = []; // Jika tidak ada data direksi
    }

    // Mengambil data work history (jika ada)
    if (!empty($dataPerusahaan['work_history'])) {
        $work_history = json_decode($dataPerusahaan['work_history'], true); // Decoding JSON
    } else {
        $work_history = []; // Jika tidak ada data work history
    }

    // Jika data perusahaan ditemukan, jalankan query untuk kuisoner
    if ($dataPerusahaan) {
        $query = "SELECT k.*, u.username
                  FROM kuisoner k
                  JOIN users u ON k.user_id = u.id
                  WHERE k.user_id = :user_id";
        $stmt = $pdo->prepare($query);
        $stmt->execute(['user_id' => $dataPerusahaan['user_id']]);
        $dataKuisoner = $stmt->fetchAll(PDO::FETCH_ASSOC);
    } else {
        $dataKuisoner = [];
    }
} else {
    $dataPerusahaan = null;
    $direksi = [];
    $work_history = [];
    $dataKuisoner = [];
}
?>

<!DOCTYPE html>
<html lang="id">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Informasi Perusahaan</title>
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css">
</head>
<style>
    body {
        background-color: #f4f4f4;
    }

    .form-section {
        margin-bottom: 2rem;
        background-color: white;
        border-radius: 8px;
        box-shadow: 0 4px 6px rgba(0, 0, 0, 0.1);
    }

    .section-header {
        background-color: #007bff;
        color: white;
        padding: 1rem;
        border-top-left-radius: 8px;
        border-top-right-radius: 8px;
    }

    .section-body {
        padding: 1rem;
    }

    .form-group {
        margin-bottom: 1rem;
    }

    .data-section label {
        font-weight: bold;
        display: block;
        margin-bottom: 5px;
        /* Memberi jarak kecil dengan elemen berikutnya */
    }

    .data-section p {
        margin-bottom: 15px;
        /* Memberi jarak antar kelompok label dan konten */
    }
</style>

<body>
    <div class="container mt-5">
        <!-- Dropdown untuk memilih nama perusahaan -->
        <form method="POST" action="">
            <div class="mb-3">
                <label for="nama_perusahaan" class="form-label">Pilih Nama Perusahaan</label>
                <select name="nama_perusahaan" id="nama_perusahaan" class="form-select">
                    <option value="">-- Pilih Nama Perusahaan --</option>
                    <?php foreach ($perusahaanList as $perusahaan) : ?>
                        <option value="<?php echo htmlspecialchars($perusahaan['nama_perusahaan']);  ?>"
                            <?php echo $selectedPerusahaan === $perusahaan['nama_perusahaan'] ? 'selected' : ''; ?>>
                            <?php echo htmlspecialchars($perusahaan['nama_perusahaan']); ?>
                        </option>
                    <?php endforeach; ?>
                </select>
            </div>
            <button type="submit" class="btn btn-primary">Tampilkan Data</button>
        </form>

        <!-- Menampilkan Data Perusahaan jika ada -->
        <?php if ($dataPerusahaan) : ?>
            <div class="form-section mt-4">
                <div class="section-header">
                    <h3 class="mt-1">Data Perusahaan: <?php echo htmlspecialchars($dataPerusahaan['nama_perusahaan']); ?></h3>
                </div>
                <div class="section-body">
                    <div class="table-responsive">
                        <table class="table table-bordered">
                            <thead>
                                <tr>
                                    <th>Nama Perusahaan</th>
                                    <th>Alamat</th>
                                    <th>Email</th>
                                    <th>Pekerjaan</th>
                                </tr>
                            </thead>
                            <tbody>
                                <tr>
                                    <td><?php echo htmlspecialchars($dataPerusahaan['nama_perusahaan']); ?></td>
                                    <td><?php echo htmlspecialchars($dataPerusahaan['alamat_pos']); ?></td>
                                    <td><?php echo htmlspecialchars($dataPerusahaan['email']); ?></td>
                                    <td><?php echo htmlspecialchars($dataPerusahaan['pekerjaan']); ?></td>
                                </tr>
                            <tbody>
                        </table>
                    </div>
                </div>
            </div>
            <!-- Tabel Direksi -->

            <div class="form-section mt-4">
                <div class="section-header">
                    <h4>Anggota Direksi</h4>
                </div>
                <div class="section-body">
                    <div class="table-responsive">
                        <table class="table table-bordered">
                            <thead>
                                <tr>
                                    <th>No</th>
                                    <th>Jabatan</th>
                                    <th>Nama</th>
                                    <th>Pendidikan Terakhir</th>
                                    <th>Masa Kerja</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php if (!empty($direksi)) : ?>
                                    <?php foreach ($direksi as $index => $row) : ?>
                                        <tr>
                                            <td><?php echo $index + 1; ?></td>
                                            <td><?php echo htmlspecialchars($row['jabatan']); ?></td>
                                            <td><?php echo htmlspecialchars($row['nama']); ?></td>
                                            <td><?php echo htmlspecialchars($row['pendidikan']); ?></td>
                                            <td><?php echo htmlspecialchars($row['masa_kerja']); ?></td>
                                        </tr>
                                    <?php endforeach; ?>
                                <?php else : ?>
                                    <tr>
                                        <td colspan="5">Tidak ada data direksi yang tersedia.</td>
                                    </tr>
                                <?php endif; ?>
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
            <div class="form-section mt-4">
                <div class="section-header">
                    <h4>Riwayat Perusahaan</h4>
                </div>
                <div class="section-body">
                    <div class="table-responsive">
                        <div class="data-section">
                            <label>Berdiri Tahun</label>
                            <p><?php echo htmlspecialchars($dataPerusahaan['berdiri_tahun']); ?></p>
                            <label>Manajemen Sejak</label>
                            <p><?php echo htmlspecialchars($dataPerusahaan['manajemen_sejak']); ?></p>
                            <label>Bentuk Usaha</label>
                            <p><?php echo htmlspecialchars($dataPerusahaan['bentuk_usaha']); ?></p>
                            <label>Perusahaan Induk</label>
                        </div>
                        <table class="table table-bordered">
                            <thead>
                                <tr>
                                    <th>Perusahaan Induk</th>
                                    <th>Kode Pos Induk Perusahaan</th>
                                    <th>Kota Induk Perusahaan</th>
                                    <th>Negara Induk Perusahaan</th>
                                    <th>Email/Telepon Induk</th>
                                </tr>
                            </thead>
                            <tbody>
                                <tr>
                                    <td><?php echo htmlspecialchars($dataPerusahaan['perusahaan_induk']); ?></td>
                                    <td><?php echo htmlspecialchars($dataPerusahaan['pos_induk']); ?></td>
                                    <td><?php echo htmlspecialchars($dataPerusahaan['kota_induk']); ?></td>
                                    <td><?php echo htmlspecialchars($dataPerusahaan['negara_induk']); ?></td>
                                    <td><?php echo htmlspecialchars($dataPerusahaan['email_telepon_induk']); ?></td>
                                </tr>
                            </tbody>
                        </table>
                    </div>
                    <div class="table-responsive">
                        <table class="table table-bordered">

                            <thead>
                                <tr>
                                    <th>Anak Perusahaan</th>
                                    <th>Kode Pos Anak Perusahaan</th>
                                    <th>Kota Anak Perusahaan</th>
                                    <th>Negara Anak Perusahaan</th>
                                    <th>Email/Telepon Anak</th>
                                </tr>
                            </thead>
                            <tbody>
                                <tr>
                                    <td><?php echo htmlspecialchars($dataPerusahaan['anak_perusahaan']); ?></td>
                                    <td><?php echo htmlspecialchars($dataPerusahaan['pos_anak']); ?></td>
                                    <td><?php echo htmlspecialchars($dataPerusahaan['kota_anak']); ?></td>
                                    <td><?php echo htmlspecialchars($dataPerusahaan['negara_anak']); ?></td>
                                    <td><?php echo htmlspecialchars($dataPerusahaan['email_telepon_anak']); ?></td>
                                </tr>
                            </tbody>
                        </table>
                    </div>
                    <div class="table-responsive">
                        <table class="table table-bordered">
                            <thead>
                                <tr>
                                    <th>Perusahaan Prinsipal</th>
                                    <th>Pos Prinsipal</th>
                                    <th>Kota Prinsipal</th>
                                    <th>Negara Prinsipal</th>
                                    <th>Email/Telepon Prinsipal</th>
                                </tr>
                            </thead>
                            <tbody>
                                <tr>
                                    <td><?php echo htmlspecialchars($dataPerusahaan['perusahaan_prinsipal']); ?></td>
                                    <td><?php echo htmlspecialchars($dataPerusahaan['pos_prinsipal']); ?></td>
                                    <td><?php echo htmlspecialchars($dataPerusahaan['kota_prinsipal']); ?></td>
                                    <td><?php echo htmlspecialchars($dataPerusahaan['negara_prinsipal']); ?></td>
                                    <td><?php echo htmlspecialchars($dataPerusahaan['email_telepon_prinsipal']); ?></td>
                                </tr>
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
            <div class="form-section mt-4">
                <div class="section-header">
                    <h4>Asuransi</h4>
                </div>
                <div class="section-body">
                    <div class="table-responsive">
                        <table class="table table-bordered">
                            <thead>
                                <tr>
                                    <th>Nama Perusahaan</th>
                                    <th>Alamat</th>
                                    <th>Email</th>
                                    <th>Pekerjaan</th>
                                </tr>
                            </thead>
                            <tbody>
                                <tr>
                                    <td><?php echo htmlspecialchars($dataPerusahaan['penanggung']); ?></td>
                                    <td><?php echo htmlspecialchars($dataPerusahaan['asuransi_pos']); ?></td>
                                    <td><?php echo htmlspecialchars($dataPerusahaan['telepon_email_asuransi']); ?></td>
                                    <td><?php echo htmlspecialchars($dataPerusahaan['jenis_jaminan']); ?></td>
                                </tr>
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
            <div class="form-section mt-4">
                <div class="section-header">
                    <h4>Apakah semua karyawan diasuransikan ?</h4>
                </div>
                <div class="section-body">
                    <div class="table-responsive">
                        <table class="table table-bordered">
                            <thead>
                                <tr>
                                    <th>Jawaban</th>
                                    <th>Alasan</th>
                                </tr>
                            </thead>
                            <tbody>
                                <tr>
                                    <td><?php echo htmlspecialchars($dataPerusahaan['insurance_option']); ?></td>
                                    <td><?php echo htmlspecialchars($dataPerusahaan['insurance_reason']); ?></td>
                                </tr>
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
            <div class="form-section mt-4">
                <div class="section-header">
                    <h4>Riwayat Pekerjaan </h4>
                </div>
                <div class="section-body">
                    <div class="table-responsive">
                        <table class="table table-bordered">
                            <thead>
                                <tr>
                                    <th>No</th>
                                    <th>Nama Perusahaan Pemberi Pekerjaan</th>
                                    <th>Jenis Pekerjaan</th>
                                    <th>Nilai Kontrak</th>
                                    <th>Telepon</th>
                                    <th>Email</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php if (!empty($work_history)) : ?>
                                    <?php foreach ($work_history as $index => $row) : ?>
                                        <tr>
                                            <td><?php echo $index + 1; ?></td>
                                            <td><?php echo htmlspecialchars($row['nama_perusahaan_riwayat']); ?></td>
                                            <td><?php echo htmlspecialchars($row['jenis_pekerjaan']); ?></td>
                                            <td><?php echo htmlspecialchars($row['nilai_kontrak']); ?></td>
                                            <td><?php echo htmlspecialchars($row['telp_fax']); ?></td>
                                            <td><?php echo htmlspecialchars($row['email_history']); ?></td>
                                        </tr>
                                    <?php endforeach; ?>
                                <?php else : ?>
                                    <tr>
                                        <td colspan="5">Tidak ada data direksi yang tersedia.</td>
                                    </tr>
                                <?php endif; ?>
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
            <div class="form-section mt-4">
                <div class="section-header">
                    <h4>Apakah perusahaan saudara sedang berurusan dengan pengadilan, klaim atau tuntutan pihak lain ?</h4>
                </div>
                <div class="section-body">
                    <div class="table-responsive">
                        <table class="table table-bordered">
                            <thead>
                                <tr>
                                    <th>Jawaban</th>
                                    <th>Alasan</th>
                                </tr>
                            </thead>
                            <tbody>
                                <tr>
                                    <td><?php echo htmlspecialchars($dataPerusahaan['legal_issue_option']); ?></td>
                                    <td><?php echo htmlspecialchars($dataPerusahaan['alasan_pengadilan']); ?></td>
                                </tr>
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
            <h3 class="text-primary border-bottom border-primary pb-2">
                <i class="fas fa-tools"></i> I FAKTOR UTAMA
            </h3>
            <div class="container mt-5">
                <div class="form-section">
                    <div class="section-header">
                        <h4 class="mb-0">1. KOMITMEN MANAJEMEN</h4>
                    </div>
                    <div class="mb-3 section-body">
                        <table class="table table-bordered">
                            <thead>
                                <tr>
                                    <th>No</th>
                                    <th>Apakah perusahaan saudara memiliki kebijakan K3?</th>
                                    <th>Lampiran</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php if (!empty($dataKuisoner)) : ?>
                                    <?php foreach ($dataKuisoner as $index => $row) : ?>
                                        <tr>
                                            <td><?php echo $index + 1; ?></td>
                                            <td><?php echo htmlspecialchars($row['komitmenA']); ?></td>
                                            <td> <?php if (!empty($row['kebijakan_k3_path'])): ?>
                                                    <a href="../download.php?file=<?= urlencode($row['kebijakan_k3_path']) ?>&column=kebijakan_k3_path" class="btn btn-primary">
                                                        Download Lampiran
                                                    </a>
                                                <?php endif; ?>
                                            </td>
                                        </tr>
                                    <?php endforeach; ?>
                                <?php else : ?>
                                    <tr>
                                        <td colspan="5">Tidak ada data direksi yang tersedia.</td>
                                    </tr>
                                <?php endif; ?>
                            </tbody>
                        </table>
                        <table class="table table-bordered">
                            <thead>
                                <tr>
                                    <th>No</th>
                                    <th>Apakah kebijakan K3 sudah disosialisasikan dan dipahami oleh seluruh pekerja serta ditinjau ulang secara berkala?</th>
                                    <th>Lampiran</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php if (!empty($dataKuisoner)) : ?>
                                    <?php foreach ($dataKuisoner as $index => $row) : ?>
                                        <tr>
                                            <td><?php echo $index + 1; ?></td>
                                            <td><?php echo htmlspecialchars($row['komitmenB']); ?></td>
                                            <td> <?php if (!empty($row['sosialisasi_absen_path'])): ?>
                                                    <a href="../download.php?file=<?= urlencode($row['sosialisasi_absen_path']) ?>&column=sosialisasi_absen_path" class="btn btn-primary">
                                                        Download Lampiran
                                                    </a>
                                                <?php endif; ?>
                                            </td>
                                        </tr>
                                    <?php endforeach; ?>
                                <?php else : ?>
                                    <tr>
                                        <td colspan="5">Tidak ada data direksi yang tersedia.</td>
                                    </tr>
                                <?php endif; ?>
                            </tbody>
                        </table>
                    </div>
                </div>
                <div class="form-section">
                    <div class="section-header">
                        <h4 class="mb-0">2. ORGANISASI K3</h4>
                    </div>
                    <div class="mb-3 section-body">
                        <table class="table table-bordered">
                            <thead>
                                <tr>
                                    <th>No</th>
                                    <th>Apakah Perusahaan Saudara mempunyai organisasi K3?</th>
                                    <th>Lampiran</th>
                                    <th>Alasan</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php if (!empty($dataKuisoner)) : ?>
                                    <?php foreach ($dataKuisoner as $index => $row) : ?>
                                        <tr>
                                            <td><?php echo $index + 1; ?></td>
                                            <td><?php echo htmlspecialchars($row['organisasiA']); ?></td>
                                            <td> <?php if (!empty($row['job_description_path'])): ?>
                                                    <a href="../download.php?file=<?= urlencode($row['job_description_path']) ?>&column=job_description_path" class="btn btn-primary">
                                                        Download Lampiran
                                                    </a>
                                                <?php endif; ?>
                                            </td>
                                            <td><?php echo htmlspecialchars($row['person_bertanggung_jawab']); ?></td>
                                        </tr>
                                    <?php endforeach; ?>
                                <?php else : ?>
                                    <tr>
                                        <td colspan="5">Tidak ada data direksi yang tersedia.</td>
                                    </tr>
                                <?php endif; ?>
                            </tbody>
                        </table>
                    </div>
                </div>
                <div class="form-section">
                    <div class="section-header">
                        <h4 class="mb-0">3. PROGRAM INSPEKSI K3</h4>
                    </div>
                    <div class="mb-3 section-body">
                        <table class="table table-bordered">
                            <thead>
                                <tr>
                                    <th>No</th>
                                    <th>Apakah perusahaan saudara memiliki program Inspeksi yang dilakukan oleh Manajemen?</th>
                                    <th>Lampiran</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php if (!empty($dataKuisoner)) : ?>
                                    <?php foreach ($dataKuisoner as $index => $row) : ?>
                                        <tr>
                                            <td><?php echo $index + 1; ?></td>
                                            <td><?php echo htmlspecialchars($row['inspeksiA']); ?></td>
                                            <td> <?php if (!empty($row['program_inspeksi_path'])): ?>
                                                    <a href="../download.php?file=<?= urlencode($row['program_inspeksi_path']) ?>&column=program_inspeksi_path" class="btn btn-primary">
                                                        Download Lampiran
                                                    </a>
                                                <?php endif; ?>
                                            </td>
                                        </tr>
                                    <?php endforeach; ?>
                                <?php else : ?>
                                    <tr>
                                        <td colspan="5">Tidak ada data direksi yang tersedia.</td>
                                    </tr>
                                <?php endif; ?>
                            </tbody>
                        </table>
                        <table class="table table-bordered">
                            <thead>
                                <tr>
                                    <th>No</th>
                                    <th>Apakah hasil temuan Inspeksi Manajemen selalu ditindak lanjuti?</th>
                                    <th>Lampiran</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php if (!empty($dataKuisoner)) : ?>
                                    <?php foreach ($dataKuisoner as $index => $row) : ?>
                                        <tr>
                                            <td><?php echo $index + 1; ?></td>
                                            <td><?php echo htmlspecialchars($row['inspeksiB']); ?></td>
                                            <td> <?php if (!empty($row['bukti_tindak_lanjut_inspeksi_path'])): ?>
                                                    <a href="../download.php?file=<?= urlencode($row['bukti_tindak_lanjut_inspeksi_path']) ?>&column=bukti_tindak_lanjut_inspeksi_path" class="btn btn-primary">
                                                        Download Lampiran
                                                    </a>
                                                <?php endif; ?>
                                            </td>
                                        </tr>
                                    <?php endforeach; ?>
                                <?php else : ?>
                                    <tr>
                                        <td colspan="5">Tidak ada data direksi yang tersedia.</td>
                                    </tr>
                                <?php endif; ?>
                            </tbody>
                        </table>
                    </div>
                </div>
                <div class="form-section">
                    <div class="section-header">
                        <h4 class="mb-0">4. PROGRAM RAPAT K3</h4>
                    </div>
                    <div class="mb-3 section-body">
                        <table class="table table-bordered">
                            <thead>
                                <tr>
                                    <th>No</th>
                                    <th>Apakah hasil temuan Inspeksi Manajemen selalu ditindak lanjuti?</th>
                                    <th>Lampiran</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php if (!empty($dataKuisoner)) : ?>
                                    <?php foreach ($dataKuisoner as $index => $row) : ?>
                                        <tr>
                                            <td><?php echo $index + 1; ?></td>
                                            <td><?php echo htmlspecialchars($row['rapatA']); ?></td>
                                            <td> <?php if (!empty($row['bukti_rapat_path'])): ?>
                                                    <a href="../download.php?file=<?= urlencode($row['bukti_rapat_path']) ?>&column=bukti_rapat_path" class="btn btn-primary">
                                                        Download Lampiran
                                                    </a>
                                                <?php endif; ?>
                                            </td>
                                        </tr>
                                    <?php endforeach; ?>
                                <?php else : ?>
                                    <tr>
                                        <td colspan="5">Tidak ada data direksi yang tersedia.</td>
                                    </tr>
                                <?php endif; ?>
                            </tbody>
                        </table>
                        <table class="table table-bordered">
                            <thead>
                                <tr>
                                    <th>No</th>
                                    <th>Apakah hasil rapat K3 ditindak lanjuti?</th>
                                    <th>Lampiran</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php if (!empty($dataKuisoner)) : ?>
                                    <?php foreach ($dataKuisoner as $index => $row) : ?>
                                        <tr>
                                            <td><?php echo $index + 1; ?></td>
                                            <td><?php echo htmlspecialchars($row['rapatB']); ?></td>
                                            <td> <?php if (!empty($row['bukti_tindak_lanjut_rapat_path'])): ?>
                                                    <a href="../download.php?file=<?= urlencode($row['bukti_tindak_lanjut_rapat_path']) ?>&column=bukti_tindak_lanjut_rapat_path" class="btn btn-primary">
                                                        Download Lampiran
                                                    </a>
                                                <?php endif; ?>
                                            </td>
                                        </tr>
                                    <?php endforeach; ?>
                                <?php else : ?>
                                    <tr>
                                        <td colspan="5">Tidak ada data direksi yang tersedia.</td>
                                    </tr>
                                <?php endif; ?>
                            </tbody>
                        </table>
                    </div>
                </div>
                <div class="form-section">
                    <div class="section-header">
                        <h4 class="mb-0">5. PERENCANAAN KEADAAN DARURAT</h4>
                    </div>
                    <div class="mb-3 section-body">
                        <table class="table table-bordered">
                            <thead>
                                <tr>
                                    <th>No</th>
                                    <th>Apakah Perusahaan saudara mempunyai prosedur Penanggulangan keadaan darurat dan melakukan latihan berkala?</th>
                                    <th>Lampiran</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php if (!empty($dataKuisoner)) : ?>
                                    <?php foreach ($dataKuisoner as $index => $row) : ?>
                                        <tr>
                                            <td><?php echo $index + 1; ?></td>
                                            <td><?php echo htmlspecialchars($row['daruratA']); ?></td>
                                            <td> <?php if (!empty($row['bukti_darurat_path'])): ?>
                                                    <a href="../download.php?file=<?= urlencode($row['bukti_darurat_path']) ?>&column=bukti_darurat_path" class="btn btn-primary">
                                                        Download Lampiran
                                                    </a>
                                                <?php endif; ?>
                                            </td>
                                        </tr>
                                    <?php endforeach; ?>
                                <?php else : ?>
                                    <tr>
                                        <td colspan="5">Tidak ada data direksi yang tersedia.</td>
                                    </tr>
                                <?php endif; ?>
                            </tbody>
                        </table>
                    </div>
                </div>
                <div class="form-section">
                    <div class="section-header">
                        <h4 class="mb-0">6. PROSEDUR K3</h4>
                    </div>
                    <div class="mb-3 section-body">
                        <table class="table table-bordered">
                            <thead>
                                <tr>
                                    <th>No</th>
                                    <th>Apakah perusahaan saudara mempunyai prosedur / buku panduan Keselamatan dan kesehatan kerja?</th>
                                    <th>Lampiran</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php if (!empty($dataKuisoner)) : ?>
                                    <?php foreach ($dataKuisoner as $index => $row) : ?>
                                        <tr>
                                            <td><?php echo $index + 1; ?></td>
                                            <td><?php echo htmlspecialchars($row['prosedurK3A']); ?></td>
                                            <td> <?php if (!empty($row['panduan_k3_path'])): ?>
                                                    <a href="../download.php?file=<?= urlencode($row['panduan_k3_path']) ?>&column=panduan_k3_path" class="btn btn-primary">
                                                        Download Lampiran
                                                    </a>
                                                <?php endif; ?>
                                            </td>
                                        </tr>
                                    <?php endforeach; ?>
                                <?php else : ?>
                                    <tr>
                                        <td colspan="5">Tidak ada data direksi yang tersedia.</td>
                                    </tr>
                                <?php endif; ?>
                            </tbody>
                        </table>
                    </div>
                    <div class="mb-3 section-body">
                        <table class="table table-bordered">
                            <thead>
                                <tr>
                                    <th>No</th>
                                    <th>Apakah perusahaan saudara memiliki buku/referensi (standar, kumpulan peraturan perundangan) tentang K3?</th>
                                    <th>Lampiran</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php if (!empty($dataKuisoner)) : ?>
                                    <?php foreach ($dataKuisoner as $index => $row) : ?>
                                        <tr>
                                            <td><?php echo $index + 1; ?></td>
                                            <td><?php echo htmlspecialchars($row['prosedurK3B']); ?></td>
                                            <td> <?php if (!empty($row['daftar_buku_k3_path'])): ?>
                                                    <a href="../download.php?file=<?= urlencode($row['daftar_buku_k3_path']) ?>&column=daftar_buku_k3_path" class="btn btn-primary">
                                                        Download Lampiran
                                                    </a>
                                                <?php endif; ?>
                                            </td>
                                        </tr>
                                    <?php endforeach; ?>
                                <?php else : ?>
                                    <tr>
                                        <td colspan="5">Tidak ada data direksi yang tersedia.</td>
                                    </tr>
                                <?php endif; ?>
                            </tbody>
                        </table>
                    </div>
                </div>
                <div class="form-section">
                    <div class="section-header">
                        <h4 class="mb-0">7. PROSEDUR KECELAKAAN KERJA</h4>
                    </div>
                    <div class="mb-3 section-body">
                        <table class="table table-bordered">
                            <thead>
                                <tr>
                                    <th>No</th>
                                    <th>Apakah perusahaan saudara mempunyai prosedur pelaporan kecelakaan kerja dan investigasi?</th>
                                    <th>Lampiran</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php if (!empty($dataKuisoner)) : ?>
                                    <?php foreach ($dataKuisoner as $index => $row) : ?>
                                        <tr>
                                            <td><?php echo $index + 1; ?></td>
                                            <td><?php echo htmlspecialchars($row['kecelakaanA']); ?></td>
                                            <td> <?php if (!empty($row['prosedur_kecelakaan_path'])): ?>
                                                    <a href="../download.php?file=<?= urlencode($row['prosedur_kecelakaan_path']) ?>&column=prosedur_kecelakaan_path" class="btn btn-primary">
                                                        Download Lampiran
                                                    </a>
                                                <?php endif; ?>
                                            </td>
                                        </tr>
                                    <?php endforeach; ?>
                                <?php else : ?>
                                    <tr>
                                        <td colspan="5">Tidak ada data direksi yang tersedia.</td>
                                    </tr>
                                <?php endif; ?>
                            </tbody>
                        </table>
                    </div>
                    <div class="mb-3 section-body">
                        <table class="table table-bordered">
                            <thead>
                                <tr>
                                    <th>No</th>
                                    <th>Apakah ada prosedur atau teknik untuk mengidentifikasi, menilai, mengawasi, dan mengurangi dampak bahaya?</th>
                                    <th>Lampiran</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php if (!empty($dataKuisoner)) : ?>
                                    <?php foreach ($dataKuisoner as $index => $row) : ?>
                                        <tr>
                                            <td><?php echo $index + 1; ?></td>
                                            <td><?php echo htmlspecialchars($row['kecelakaanB']); ?></td>
                                            <td> <?php if (!empty($row['prosedur_bahaya_path'])): ?>
                                                    <a href="../download.php?file=<?= urlencode($row['prosedur_bahaya_path']) ?>&column=prosedur_bahaya_path" class="btn btn-primary">
                                                        Download Lampiran
                                                    </a>
                                                <?php endif; ?>
                                            </td>
                                        </tr>
                                    <?php endforeach; ?>
                                <?php else : ?>
                                    <tr>
                                        <td colspan="5">Tidak ada data direksi yang tersedia.</td>
                                    </tr>
                                <?php endif; ?>
                            </tbody>
                        </table>
                    </div>
                </div>
                <div class="form-section">
                    <div class="section-header">
                        <h4 class="mb-0">8. PROGRAM PELATIHAN K3</h4>
                    </div>
                    <div class="mb-3 section-body">
                        <table class="table table-bordered">
                            <thead>
                                <tr>
                                    <th>No</th>
                                    <th>Apakah perusahaan saudara mempunyai program pelatihan (teori & praktek) tentang K3?</th>
                                    <th>Lampiran</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php if (!empty($dataKuisoner)) : ?>
                                    <?php foreach ($dataKuisoner as $index => $row) : ?>
                                        <tr>
                                            <td><?php echo $index + 1; ?></td>
                                            <td><?php echo htmlspecialchars($row['pelatihanA']); ?></td>
                                            <td> <?php if (!empty($row['program_pelatihan_path'])): ?>
                                                    <a href="../download.php?file=<?= urlencode($row['program_pelatihan_path']) ?>&column=program_pelatihan_path" class="btn btn-primary">
                                                        Download Lampiran
                                                    </a>
                                                <?php endif; ?>
                                            </td>
                                        </tr>
                                    <?php endforeach; ?>
                                <?php else : ?>
                                    <tr>
                                        <td colspan="5">Tidak ada data direksi yang tersedia.</td>
                                    </tr>
                                <?php endif; ?>
                            </tbody>
                        </table>
                    </div>
                    <div class="mb-3 section-body">
                        <table class="table table-bordered">
                            <thead>
                                <tr>
                                    <th>No</th>
                                    <th>Apakah para penanggung jawab K3 telah mendapatkan pelatihan sesuai tanggung jawabnya?</th>
                                    <th>Lampiran</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php if (!empty($dataKuisoner)) : ?>
                                    <?php foreach ($dataKuisoner as $index => $row) : ?>
                                        <tr>
                                            <td><?php echo $index + 1; ?></td>
                                            <td><?php echo htmlspecialchars($row['pelatihanB']); ?></td>
                                            <td> <?php if (!empty($row['jadwal_pelatihan_path'])): ?>
                                                    <a href="../download.php?file=<?= urlencode($row['jadwal_pelatihan_path']) ?>&column=jadwal_pelatihan_path" class="btn btn-primary">
                                                        Download Lampiran
                                                    </a>
                                                <?php endif; ?>
                                            </td>
                                        </tr>
                                    <?php endforeach; ?>
                                <?php else : ?>
                                    <tr>
                                        <td colspan="5">Tidak ada data direksi yang tersedia.</td>
                                    </tr>
                                <?php endif; ?>
                            </tbody>
                        </table>
                    </div>
                    <div class="mb-3 section-body">
                        <table class="table table-bordered">
                            <thead>
                                <tr>
                                    <th>No</th>
                                    <th>Apakah perusahaan Saudara mempunyai petugas yang berkualifikasi ahli K3?</th>
                                    <th>Lampiran</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php if (!empty($dataKuisoner)) : ?>
                                    <?php foreach ($dataKuisoner as $index => $row) : ?>
                                        <tr>
                                            <td><?php echo $index + 1; ?></td>
                                            <td><?php echo htmlspecialchars($row['pelatihanC']); ?></td>
                                            <td> <?php if (!empty($row['sertifikat_ahli_k3_path'])): ?>
                                                    <a href="../download.php?file=<?= urlencode($row['sertifikat_ahli_k3_path']) ?>&column=sertifikat_ahli_k3_path" class="btn btn-primary">
                                                        Download Lampiran
                                                    </a>
                                                <?php endif; ?>
                                            </td>
                                        </tr>
                                    <?php endforeach; ?>
                                <?php else : ?>
                                    <tr>
                                        <td colspan="5">Tidak ada data direksi yang tersedia.</td>
                                    </tr>
                                <?php endif; ?>
                            </tbody>
                        </table>
                    </div>
                </div>
                <div class="form-section">
                    <div class="section-header">
                        <h4 class="mb-0">9. ALAT PELINDUNG DIRI</h4>
                    </div>
                    <div class="mb-3 section-body">
                        <table class="table table-bordered">
                            <thead>
                                <tr>
                                    <th>No</th>
                                    <th>Apakah perusahaan saudara memberikan alat pelindung diri pada setiap karyawan yang akan melaksanakan pekerjaan?</th>
                                    <th>Lampiran</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php if (!empty($dataKuisoner)) : ?>
                                    <?php foreach ($dataKuisoner as $index => $row) : ?>
                                        <tr>
                                            <td><?php echo $index + 1; ?></td>
                                            <td><?php echo htmlspecialchars($row['apdA']); ?></td>
                                            <td> <?php if (!empty($row['jenis_apd_path'])): ?>
                                                    <a href="../download.php?file=<?= urlencode($row['jenis_apd_path']) ?>&column=jenis_apd_path" class="btn btn-primary">
                                                        Download Lampiran
                                                    </a>
                                                <?php endif; ?>
                                            </td>
                                        </tr>
                                    <?php endforeach; ?>
                                <?php else : ?>
                                    <tr>
                                        <td colspan="5">Tidak ada data direksi yang tersedia.</td>
                                    </tr>
                                <?php endif; ?>
                            </tbody>
                        </table>
                    </div>
                    <div class="mb-3 section-body">
                        <table class="table table-bordered">
                            <thead>
                                <tr>
                                    <th>No</th>
                                    <th>Apakah ada prosedur pemeriksaan dan pemeliharaan alat pelindung diri khusus?</th>
                                    <th>Lampiran</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php if (!empty($dataKuisoner)) : ?>
                                    <?php foreach ($dataKuisoner as $index => $row) : ?>
                                        <tr>
                                            <td><?php echo $index + 1; ?></td>
                                            <td><?php echo htmlspecialchars($row['apdB']); ?></td>
                                            <td> <?php if (!empty($row['prosedur_pemeliharaan_apd_path'])): ?>
                                                    <a href="../download.php?file=<?= urlencode($row['prosedur_pemeliharaan_apd_path']) ?>&column=prosedur_pemeliharaan_apd_path" class="btn btn-primary">
                                                        Download Lampiran
                                                    </a>
                                                <?php endif; ?>
                                            </td>
                                        </tr>
                                    <?php endforeach; ?>
                                <?php else : ?>
                                    <tr>
                                        <td colspan="5">Tidak ada data direksi yang tersedia.</td>
                                    </tr>
                                <?php endif; ?>
                            </tbody>
                        </table>
                    </div>
                    <div class="mb-3 section-body">
                        <table class="table table-bordered">
                            <thead>
                                <tr>
                                    <th>No</th>
                                    <th>Apakah perusahaan saudara selalu memeriksa dan mensertifikasi secara rutin semua peralatan kerja yang digunakan?</th>
                                    <th>Lampiran</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php if (!empty($dataKuisoner)) : ?>
                                    <?php foreach ($dataKuisoner as $index => $row) : ?>
                                        <tr>
                                            <td><?php echo $index + 1; ?></td>
                                            <td><?php echo htmlspecialchars($row['apdC']); ?></td>
                                            <td> <?php if (!empty($row['sertifikat_peralatan_kerja_path'])): ?>
                                                    <a href="../download.php?file=<?= urlencode($row['sertifikat_peralatan_kerja_path']) ?>&column=sertifikat_peralatan_kerja_path" class="btn btn-primary">
                                                        Download Lampiran
                                                    </a>
                                                <?php endif; ?>
                                            </td>
                                        </tr>
                                    <?php endforeach; ?>
                                <?php else : ?>
                                    <tr>
                                        <td colspan="5">Tidak ada data direksi yang tersedia.</td>
                                    </tr>
                                <?php endif; ?>
                            </tbody>
                        </table>
                    </div>
                </div>
                <div class="form-section">
                    <div class="section-header">
                        <h4 class="mb-0">10. PENGELOLAAN MATERIAL B3</h4>
                    </div>
                    <div class="mb-3 section-body">
                        <table class="table table-bordered">
                            <thead>
                                <tr>
                                    <th>No</th>
                                    <th>Apakah perusahaan saudara memiliki prosedur penanganan, pengangkutan, dan penyimpanan bahan berbahaya dan beracun (B3)?</th>
                                    <th>Lampiran</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php if (!empty($dataKuisoner)) : ?>
                                    <?php foreach ($dataKuisoner as $index => $row) : ?>
                                        <tr>
                                            <td><?php echo $index + 1; ?></td>
                                            <td><?php echo htmlspecialchars($row['b3A']); ?></td>
                                            <td> <?php if (!empty($row['prosedur_material_b3_path'])): ?>
                                                    <a href="../download.php?file=<?= urlencode($row['prosedur_material_b3_path']) ?>&column=prosedur_material_b3_path" class="btn btn-primary">
                                                        Download Lampiran
                                                    </a>
                                                <?php endif; ?>
                                            </td>
                                        </tr>
                                    <?php endforeach; ?>
                                <?php else : ?>
                                    <tr>
                                        <td colspan="5">Tidak ada data direksi yang tersedia.</td>
                                    </tr>
                                <?php endif; ?>
                            </tbody>
                        </table>
                    </div>
                </div>
                <h3 class="text-primary border-bottom border-primary pb-2">
                    <i class="fas fa-tools"></i> 2. FAKTOR PENDUKUNG
                </h3>
                <div class="form-section">
                    <div class="section-header">
                        <h4 class="mb-0">1. HIGIENE INDUSTRI</h4>
                    </div>
                    <div class="mb-3 section-body">
                        <table class="table table-bordered">
                            <thead>
                                <tr>
                                    <th>No</th>
                                    <th>Apakah Perusahaan Saudara melakukan pemeriksaan kesehatan terhadap calon pekerja serta melakukan pemeriksaan berkala?</th>
                                    <th>Lampiran</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php if (!empty($dataKuisoner)) : ?>
                                    <?php foreach ($dataKuisoner as $index => $row) : ?>
                                        <tr>
                                            <td><?php echo $index + 1; ?></td>
                                            <td><?php echo htmlspecialchars($row['higieneA']); ?></td>
                                            <td> <?php if (!empty($row['bukti_pemeriksaan_kesehatan_path'])): ?>
                                                    <a href="../download.php?file=<?= urlencode($row['bukti_pemeriksaan_kesehatan_path']) ?>&column=bukti_pemeriksaan_kesehatan_path" class="btn btn-primary">
                                                        Download Lampiran
                                                    </a>
                                                <?php endif; ?>
                                            </td>
                                        </tr>
                                    <?php endforeach; ?>
                                <?php else : ?>
                                    <tr>
                                        <td colspan="5">Tidak ada data direksi yang tersedia.</td>
                                    </tr>
                                <?php endif; ?>
                            </tbody>
                        </table>
                    </div>
                    <div class="mb-3 section-body">
                        <table class="table table-bordered">
                            <thead>
                                <tr>
                                    <th>No</th>
                                    <th>Apakah perusahaan saudara melakukan pemantauan kesehatan tenaga kerja yang bekerja di lokasi yang mengandung bahaya dan risiko kesehatan?</th>
                                    <th>Lampiran</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php if (!empty($dataKuisoner)) : ?>
                                    <?php foreach ($dataKuisoner as $index => $row) : ?>
                                        <tr>
                                            <td><?php echo $index + 1; ?></td>
                                            <td><?php echo htmlspecialchars($row['higieneB']); ?></td>
                                            <td> <?php if (!empty($row['bukti_pemantauan_kesehatan_path'])): ?>
                                                    <a href="../download.php?file=<?= urlencode($row['bukti_pemantauan_kesehatan_path']) ?>&column=bukti_pemantauan_kesehatan_path" class="btn btn-primary">
                                                        Download Lampiran
                                                    </a>
                                                <?php endif; ?>
                                            </td>
                                        </tr>
                                    <?php endforeach; ?>
                                <?php else : ?>
                                    <tr>
                                        <td colspan="5">Tidak ada data direksi yang tersedia.</td>
                                    </tr>
                                <?php endif; ?>
                            </tbody>
                        </table>
                    </div>
                    <div class="mb-3 section-body">
                        <table class="table table-bordered">
                            <thead>
                                <tr>
                                    <th>No</th>
                                    <th>Apakah ada program monitoring dan pengendalian bahaya kesehatan di tempat kerja?</th>
                                    <th>Lampiran</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php if (!empty($dataKuisoner)) : ?>
                                    <?php foreach ($dataKuisoner as $index => $row) : ?>
                                        <tr>
                                            <td><?php echo $index + 1; ?></td>
                                            <td><?php echo htmlspecialchars($row['higieneC']); ?></td>
                                            <td> <?php if (!empty($row['bukti_program_pengendalian_path'])): ?>
                                                    <a href="../download.php?file=<?= urlencode($row['bukti_program_pengendalian_path']) ?>&column=bukti_program_pengendalian_path" class="btn btn-primary">
                                                        Download Lampiran
                                                    </a>
                                                <?php endif; ?>
                                            </td>
                                        </tr>
                                    <?php endforeach; ?>
                                <?php else : ?>
                                    <tr>
                                        <td colspan="5">Tidak ada data direksi yang tersedia.</td>
                                    </tr>
                                <?php endif; ?>
                            </tbody>
                        </table>
                    </div>
                    <div class="mb-3 section-body">
                        <table class="table table-bordered">
                            <thead>
                                <tr>
                                    <th>No</th>
                                    <th>Apakah perusahaan saudara memiliki prosedur/peraturan larangan pemakaian obat-obat terlarang & minuman keras?</th>
                                    <th>Lampiran</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php if (!empty($dataKuisoner)) : ?>
                                    <?php foreach ($dataKuisoner as $index => $row) : ?>
                                        <tr>
                                            <td><?php echo $index + 1; ?></td>
                                            <td><?php echo htmlspecialchars($row['laranganObat']); ?></td>
                                            <td> <?php if (!empty($row['bukti_larangan_obat_path'])): ?>
                                                    <a href="../download.php?file=<?= urlencode($row['bukti_larangan_obat_path']) ?>&column=bukti_larangan_obat_path" class="btn btn-primary">
                                                        Download Lampiran
                                                    </a>
                                                <?php endif; ?>
                                            </td>
                                        </tr>
                                    <?php endforeach; ?>
                                <?php else : ?>
                                    <tr>
                                        <td colspan="5">Tidak ada data direksi yang tersedia.</td>
                                    </tr>
                                <?php endif; ?>
                            </tbody>
                        </table>
                    </div>
                </div>
                <div class="form-section">
                    <div class="section-header">
                        <h4 class="mb-0">2. PENGELOLAAN LINGKUNGAN KERJA</h4>
                    </div>
                    <div class="mb-3 section-body">
                        <table class="table table-bordered">
                            <thead>
                                <tr>
                                    <th>No</th>
                                    <th>Apakah perusahaan memiliki prosedur terkait pengelolaan lingkungan kerja (5S / 5R)?</th>
                                    <th>Lampiran</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php if (!empty($dataKuisoner)) : ?>
                                    <?php foreach ($dataKuisoner as $index => $row) : ?>
                                        <tr>
                                            <td><?php echo $index + 1; ?></td>
                                            <td><?php echo htmlspecialchars($row['pengelolaan_lingkungan']); ?></td>
                                            <td> <?php if (!empty($row['bukti_kebijakan_5s_path'])): ?>
                                                    <a href="../download.php?file=<?= urlencode($row['bukti_kebijakan_5s_path']) ?>&column=bukti_kebijakan_5s_path" class="btn btn-primary">
                                                        Download Lampiran
                                                    </a>
                                                <?php endif; ?>
                                            </td>
                                        </tr>
                                    <?php endforeach; ?>
                                <?php else : ?>
                                    <tr>
                                        <td colspan="5">Tidak ada data direksi yang tersedia.</td>
                                    </tr>
                                <?php endif; ?>
                            </tbody>
                        </table>
                    </div>
                    <div class="mb-3 section-body">
                        <table class="table table-bordered">
                            <thead>
                                <tr>
                                    <th>No</th>
                                    <th>Apakah perusahaan saudara melakukan pengukuran lingkungan kerja sesuai Permenaker No. 05 Tahun 2018?</th>
                                    <th>Lampiran</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php if (!empty($dataKuisoner)) : ?>
                                    <?php foreach ($dataKuisoner as $index => $row) : ?>
                                        <tr>
                                            <td><?php echo $index + 1; ?></td>
                                            <td><?php echo htmlspecialchars($row['pengukuran_lingkungan']); ?></td>
                                            <td> <?php if (!empty($row['bukti_pengukuran_lingkungan_path'])): ?>
                                                    <a href="../download.php?file=<?= urlencode($row['bukti_pengukuran_lingkungan_path']) ?>&column=bukti_pengukuran_lingkungan_path" class="btn btn-primary">
                                                        Download Lampiran
                                                    </a>
                                                <?php endif; ?>
                                            </td>
                                        </tr>
                                    <?php endforeach; ?>
                                <?php else : ?>
                                    <tr>
                                        <td colspan="5">Tidak ada data direksi yang tersedia.</td>
                                    </tr>
                                <?php endif; ?>
                            </tbody>
                        </table>
                    </div>
                </div>
                <div class="form-section">
                    <div class="section-header">
                        <h4 class="mb-0">3. DATA KINERJA K3</h4>
                    </div>
                    <div class="mb-3 section-body">
                        <table class="table table-bordered">
                            <thead>
                                <tr>
                                    <th>No</th>
                                    <th>Apakah Perusahaan saudara menyimpan catatan kinerja K3LH untuk 3 tahun terakhir?</th>
                                    <th>Lampiran</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php if (!empty($dataKuisoner)) : ?>
                                    <?php foreach ($dataKuisoner as $index => $row) : ?>
                                        <tr>
                                            <td><?php echo $index + 1; ?></td>
                                            <td><?php echo htmlspecialchars($row['catatan_kinerja']); ?></td>
                                            <td> <?php if (!empty($row['bukti_kinerja_k3_path'])): ?>
                                                    <a href="../download.php?file=<?= urlencode($row['bukti_kinerja_k3_path']) ?>&column=bukti_kinerja_k3_path" class="btn btn-primary">
                                                        Download Lampiran
                                                    </a>
                                                <?php endif; ?>
                                            </td>
                                        </tr>
                                    <?php endforeach; ?>
                                <?php else : ?>
                                    <tr>
                                        <td colspan="5">Tidak ada data direksi yang tersedia.</td>
                                    </tr>
                                <?php endif; ?>
                            </tbody>
                        </table>
                    </div>
                    <div class="mb-3 section-body">
                        <table class="table table-bordered">
                            <thead>
                                <tr>
                                    <th>No</th>
                                    <th>Apakah dilakukan evaluasi terhadap sasaran dan program K3 tahunan?</th>
                                    <th>Lampiran</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php if (!empty($dataKuisoner)) : ?>
                                    <?php foreach ($dataKuisoner as $index => $row) : ?>
                                        <tr>
                                            <td><?php echo $index + 1; ?></td>
                                            <td><?php echo htmlspecialchars($row['evaluasi_k3']); ?></td>
                                            <td> <?php if (!empty($row['bukti_evaluasi_k3_path'])): ?>
                                                    <a href="../download.php?file=<?= urlencode($row['bukti_evaluasi_k3_path']) ?>&column=bukti_evaluasi_k3_path" class="btn btn-primary">
                                                        Download Lampiran
                                                    </a>
                                                <?php endif; ?>
                                            </td>
                                        </tr>
                                    <?php endforeach; ?>
                                <?php else : ?>
                                    <tr>
                                        <td colspan="5">Tidak ada data direksi yang tersedia.</td>
                                    </tr>
                                <?php endif; ?>
                            </tbody>
                        </table>
                    </div>
                </div>
                <div class="form-section">
                    <div class="section-header">
                        <h4 class="mb-0">4. INVESTIGASI KECELAKAAN</h4>
                    </div>
                    <div class="mb-3 section-body">
                        <table class="table table-bordered">
                            <thead>
                                <tr>
                                    <th>No</th>
                                    <th>Apakah dilakukan investigasi pada setiap kecelakaan kerja?</th>
                                    <th>Lampiran</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php if (!empty($dataKuisoner)) : ?>
                                    <?php foreach ($dataKuisoner as $index => $row) : ?>
                                        <tr>
                                            <td><?php echo $index + 1; ?></td>
                                            <td><?php echo htmlspecialchars($row['investigasi_kecelakaan']); ?></td>
                                            <td> <?php if (!empty($row['bukti_investigasi_kecelakaan_path'])): ?>
                                                    <a href="../download.php?file=<?= urlencode($row['bukti_investigasi_kecelakaan_path']) ?>&column=bukti_investigasi_kecelakaan_path" class="btn btn-primary">
                                                        Download Lampiran
                                                    </a>
                                                <?php endif; ?>
                                            </td>
                                        </tr>
                                    <?php endforeach; ?>
                                <?php else : ?>
                                    <tr>
                                        <td colspan="5">Tidak ada data direksi yang tersedia.</td>
                                    </tr>
                                <?php endif; ?>
                            </tbody>
                        </table>
                    </div>
                    <div class="mb-3 section-body">
                        <table class="table table-bordered">
                            <thead>
                                <tr>
                                    <th>No</th>
                                    <th>Apakah hasil investigasi berisi saran dan ditindaklanjuti?</th>
                                    <th>Lampiran</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php if (!empty($dataKuisoner)) : ?>
                                    <?php foreach ($dataKuisoner as $index => $row) : ?>
                                        <tr>
                                            <td><?php echo $index + 1; ?></td>
                                            <td><?php echo htmlspecialchars($row['saran_investigasi']); ?></td>
                                            <td> <?php if (!empty($row['bukti_saran_investigasi_path'])): ?>
                                                    <a href="../download.php?file=<?= urlencode($row['bukti_saran_investigasi_path']) ?>&column=bukti_saran_investigasi_path" class="btn btn-primary">
                                                        Download Lampiran
                                                    </a>
                                                <?php endif; ?>
                                            </td>
                                        </tr>
                                    <?php endforeach; ?>
                                <?php else : ?>
                                    <tr>
                                        <td colspan="5">Tidak ada data direksi yang tersedia.</td>
                                    </tr>
                                <?php endif; ?>
                            </tbody>
                        </table>
                    </div>
                </div>
                <form action="../upload_sertifikat.php" method="POST" enctype="multipart/form-data">
                    <input type="hidden" name="selectedPerusahaan" value="<?php echo htmlspecialchars($selectedPerusahaan); ?>">
                    <label for="sertifikat">Upload Sertifikat:</label>
                    <input type="file" name="sertifikat" id="sertifikat" required>
                    <button type="submit">Upload</button>
                </form>
            <?php else : ?>
                <p class="mt-3">Tidak ada data untuk perusahaan yang dipilih.</p>
            <?php endif; ?>
            <!-- Kembali ke Dashboard -->
            <div>
                <div class="text-center mt-4">
                    <a href="../page/dashboard.php" class="btn btn-secondary">Kembali</a>
                </div>
            </div>
            </div>
            <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>

</html>