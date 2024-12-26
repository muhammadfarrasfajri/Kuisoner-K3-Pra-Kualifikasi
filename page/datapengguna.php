<?php
session_start();
require '../includes/config.php';
$user_id = $_SESSION['user_id'];
// Pastikan user login dan role adalah user
if (!isset($_SESSION['user'])) {
    header('Location: ../page/login.php');
    exit;
}
if ($_SESSION['user']['role'] !== 'user') {
    http_response_code(403);
    echo "Anda tidak memiliki izin untuk mengakses halaman ini.";
    exit;
}

try {
    $sql = "SELECT * FROM profil_kontraktor WHERE user_id = :user_id";
    $stmt = $pdo->prepare($sql);
    $stmt->bindParam(':user_id', $user_id, PDO::PARAM_INT);
    $stmt->execute();

    $result = $stmt->fetch(PDO::FETCH_ASSOC);

    // Jika total lebih dari 0, ubah tombol menjadi "Edit"
    $isExistingUser = $result > 0;
} catch (PDOException $e) {
    echo "Terjadi kesalahan dalam mengambil data pengguna: " . htmlspecialchars($e->getMessage());
    exit;
}

echo $_SESSION['user_id'];

$direksi = [];
if (!empty($result['direksi'])) {
    $direksi = json_decode($result['direksi'], true);
}

$work_history = [];
if (!empty($result['work_history'])) {
    $work_history = json_decode($result['work_history'], true);
}
?>
<!DOCTYPE html>
<html lang="id">

<head>
    <meta charset="UTF-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1" />
    <title>Formulir Informasi Perusahaan</title>
    <!-- Bootstrap CSS -->
    <link
        href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css"
        rel="stylesheet" />
</head>

<body>
    <!DOCTYPE html>
    <html lang="id">

    <head>
        <meta charset="UTF-8" />
        <meta name="viewport" content="width=device-width, initial-scale=1.0" />
        <title>Formulir Pendaftaran Perusahaan</title>
        <link
            href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css"
            rel="stylesheet" />
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
        </style>
    </head>

    <body>
        <nav class="navbar navbar-dark bg-primary fixed-top text-white d-flex align-items-center justify-content-center">
            <h3 class="m-0 text-center w-100">Profile Kontraktor</h3>
            <a href="../page/dashboard.php" class="btn btn-secondary position-absolute end-0 me-3">Kembali</a>
        </nav>
        <div class="container mt-5">
            <form method="POST" action="../controllers/data_pengguna.php">
                <div class="form-section">
                    <div class="section-header">
                        <h5 class="mb-0">1. Informasi Perusahaan</h5>
                    </div>
                    <div class="section-body">
                        <div class="row g-3">
                            <div class="col-12 form-group">
                                <label class="form-label">Nama Perusahaan</label>
                                <input
                                    type="text"
                                    name="nama_perusahaan"
                                    class="form-control"
                                    placeholder="Masukkan nama perusahaan"
                                    value="<?php echo isset($result['nama_perusahaan']) ? htmlspecialchars($result['nama_perusahaan']) : ''; ?>" />
                            </div>
                            <div class="col-12 form-group">
                                <label class="form-label">Alamat Pos</label>
                                <input
                                    type="text"
                                    name="alamat_pos"
                                    class="form-control"
                                    placeholder="Masukkan alamat pos"
                                    value="<?php echo isset($result['alamat_pos']) ? htmlspecialchars($result['alamat_pos']) : ''; ?>" />
                            </div>
                            <div class="col-12 form-group">
                                <label class="form-label">Nomor Telepon/Fax</label>
                                <input
                                    type="text"
                                    name="nomor_telepon_fax"
                                    class="form-control"
                                    placeholder="Masukkan nomor telepon/fax"
                                    value="<?php echo isset($result['nomor_telepon_fax']) ? htmlspecialchars($result['nomor_telepon_fax']) : ''; ?>" />
                            </div>
                            <div class="col-12 form-group">
                                <label class="form-label">Email</label>
                                <input
                                    type="email"
                                    name="email"
                                    class="form-control"
                                    placeholder="Masukkan email perusahaan"
                                    value="<?php echo isset($result['email']) ? htmlspecialchars($result['email']) : ''; ?>" />
                            </div>
                            <div class="col-12 form-group">
                                <label class="form-label">Pekerjaan</label>
                                <input
                                    type="text"
                                    name="pekerjaan"
                                    class="form-control"
                                    placeholder="Masukkan jenis pekerjaan"
                                    value="<?php echo isset($result['pekerjaan']) ? htmlspecialchars($result['pekerjaan']) : ''; ?>" />
                            </div>
                        </div>
                    </div>
                </div>

                <!-- 2. Anggota Direksi -->

                <div class="form-section mt-4">
                    <div class="section-header">
                        <h5 class="mb-0">2. Anggota Direksi</h5>
                    </div>
                    <div class="section-body">
                        <div class="table-responsive">
                            <table class="table table-bordered" id="direksiTable">
                                <thead>
                                    <tr>
                                        <th>No</th>
                                        <th>Jabatan</th>
                                        <th>Nama</th>
                                        <th>Pendidikan Terakhir</th>
                                        <th>Masa Kerja</th>
                                        <th>Aksi</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <?php if (!empty($direksi)) : ?>
                                        <?php foreach ($direksi as $index => $row) : ?>
                                            <tr>
                                                <td><?php echo $index + 1; ?></td>
                                                <td>
                                                    <input type="text" name="jabatan[]" class="form-control" placeholder="Jabatan" value="<?php echo htmlspecialchars($row['jabatan']); ?>" required />
                                                </td>
                                                <td>
                                                    <input type="text" name="nama[]" class="form-control" placeholder="Nama" value="<?php echo htmlspecialchars($row['nama']); ?>" required />
                                                </td>
                                                <td>
                                                    <input type="text" name="pendidikan[]" class="form-control" placeholder="Pendidikan Terakhir" value="<?php echo htmlspecialchars($row['pendidikan']); ?>" required />
                                                </td>
                                                <td>
                                                    <input type="text" name="masa_kerja[]" class="form-control" placeholder="Masa Kerja" value="<?php echo htmlspecialchars($row['masa_kerja']); ?>" required />
                                                </td>
                                                <td>
                                                    <button type="button" class="btn btn-danger btn-sm remove-row">Hapus</button>
                                                </td>
                                            </tr>
                                        <?php endforeach; ?>
                                    <?php else : ?>
                                        <!-- Jika tidak ada data, tampilkan baris kosong -->
                                        <tr>
                                            <td>1</td>
                                            <td>
                                                <input type="text" name="jabatan[]" class="form-control" placeholder="Jabatan" required />
                                            </td>
                                            <td>
                                                <input type="text" name="nama[]" class="form-control" placeholder="Nama" required />
                                            </td>
                                            <td>
                                                <input type="text" name="pendidikan[]" class="form-control" placeholder="Pendidikan Terakhir" required />
                                            </td>
                                            <td>
                                                <input type="text" name="masa_kerja[]" class="form-control" placeholder="Masa Kerja" required />
                                            </td>
                                            <td>
                                                <button type="button" class="btn btn-danger btn-sm remove-row">Hapus</button>
                                            </td>
                                        </tr>
                                    <?php endif; ?>
                                </tbody>
                            </table>

                        </div>
                        <div class="text-center mt-3">
                            <button type="button" class="btn btn-primary me-2" id="addRow">Tambah Anggota</button>
                            <button type="button" class="btn btn-secondary" id="resetTable">Reset</button>
                        </div>
                    </div>
                </div>
                <!-- 3. Informasi Tambahan Perusahaan -->
                <div class="form-section mt-4">
                    <div class="section-header">
                        <h5 class="mb-0">3. Informasi Tambahan Perusahaan</h5>
                    </div>
                    <div class="section-body">
                        <div class="row g-3">
                            <div class="col-12 form-group">
                                <label class="form-label"><b>a. Berdiri Tahun</b></label>
                                <input
                                    type="text"
                                    name="berdiri_tahun"
                                    class="form-control"
                                    placeholder="Masukkan tahun berdiri"
                                    value="<?php echo isset($result['berdiri_tahun']) ? htmlspecialchars($result['berdiri_tahun']) : ''; ?>" />
                            </div>
                            <div class="col-12 form-group">
                                <label class="form-label"><b>b. Dibawah Manajemen Sekarang: Sejak Tahun</b></label>
                                <input
                                    type="text"
                                    name="manajemen_sejak"
                                    class="form-control"
                                    placeholder="Masukkan tahun manajemen sekarang"
                                    value="<?php echo isset($result['manajemen_sejak']) ? htmlspecialchars($result['manajemen_sejak']) : ''; ?>" />
                            </div>
                            <div class="col-12 form-group">
                                <label class="form-label"><b>c. Bentuk Usaha</b></label>
                                <select class="form-select" name="bentuk_usaha">
                                    <option value="">Pilih Bentuk Usaha</option>
                                    <option value="CV" <?php echo (isset($result['bentuk_usaha']) && $result['bentuk_usaha'] == 'CV') ? 'selected' : ''; ?>>CV</option>
                                    <option value="PT" <?php echo (isset($result['bentuk_usaha']) && $result['bentuk_usaha'] == 'PT') ? 'selected' : ''; ?>>PT</option>
                                    <option value="Lainnya" <?php echo (isset($result['bentuk_usaha']) && $result['bentuk_usaha'] == 'Lainnya') ? 'selected' : ''; ?>>Lainnya</option>
                                </select>
                            </div>
                            <div class="col-12 form-group">
                                <label class="form-label"><b>d. Nama Perusahaan Induk</b></label>
                                <input
                                    type="text"
                                    class="form-control"
                                    name="perusahaan_induk"
                                    placeholder="Masukkan nama perusahaan induk"
                                    value="<?php echo isset($result['perusahaan_induk']) ? htmlspecialchars($result['perusahaan_induk']) : ''; ?>" />
                            </div>
                            <div class="col-12 form-group">
                                <label class="form-label">Alamat Pos</label>
                                <input
                                    type="text"
                                    class="form-control"
                                    name="pos_induk"
                                    placeholder="Masukkan alamat pos"
                                    value="<?php echo isset($result['pos_induk']) ? htmlspecialchars($result['pos_induk']) : ''; ?>" />
                            </div>
                            <div class="row">
                                <div class="col-md-6 form-group">
                                    <label class="form-label">Kota</label>
                                    <input
                                        type="text"
                                        class="form-control"
                                        name="kota_induk"
                                        placeholder="Masukkan kota"
                                        value="<?php echo isset($result['kota_induk']) ? htmlspecialchars($result['kota_induk']) : ''; ?>" />
                                </div>
                                <div class="col-md-6 form-group">
                                    <label class="form-label">Negara</label>
                                    <input
                                        type="text"
                                        class="form-control"
                                        name="negara_induk"
                                        placeholder="Masukkan negara"
                                        value="<?php echo isset($result['negara_induk']) ? htmlspecialchars($result['negara_induk']) : ''; ?>" />
                                </div>
                            </div>
                            <div class="col-12 form-group">
                                <label class="form-label">E-mail / Telephone</label>
                                <input
                                    type="text"
                                    class="form-control"
                                    name="email_telepon_induk"
                                    placeholder="Masukkan email atau telepon"
                                    value="<?php echo isset($result['email_telepon_induk']) ? htmlspecialchars($result['email_telepon_induk']) : ''; ?>" />
                            </div>
                            <!-- Tambahkan bagian lainnya sesuai format ini -->
                            <div class="col-12 form-group">
                                <label class="form-label"><b>e. Nama Anak Perusahaan</b></label>
                                <input
                                    type="text"
                                    class="form-control"
                                    name="anak_perusahaan"
                                    placeholder="Masukkan nama anak perusahaan"
                                    value="<?php echo isset($result['anak_perusahaan']) ? htmlspecialchars($result['anak_perusahaan']) : ''; ?>" />
                            </div>
                            <div class="col-12 form-group">
                                <label class="form-label">Alamat Pos</label>
                                <input
                                    type="text"
                                    class="form-control"
                                    name="pos_anak"
                                    placeholder="Masukkan alamat pos"
                                    value="<?php echo isset($result['pos_anak']) ? htmlspecialchars($result['pos_anak']) : ''; ?>" />
                            </div>
                            <div class="row">
                                <div class="col-md-6 form-group">
                                    <label class="form-label">Kota</label>
                                    <input
                                        type="text"
                                        class="form-control"
                                        name="kota_anak"
                                        placeholder="Masukkan kota"
                                        value="<?php echo isset($result['kota_anak']) ? htmlspecialchars($result['kota_anak']) : ''; ?>" />
                                </div>
                                <div class="col-md-6 form-group">
                                    <label class="form-label">Negara</label>
                                    <input
                                        type="text"
                                        class="form-control"
                                        name="negara_anak"
                                        placeholder="Masukkan negara"
                                        value="<?php echo isset($result['negara_anak']) ? htmlspecialchars($result['negara_anak']) : ''; ?>" />
                                </div>
                            </div>
                            <div class="col-12 form-group">
                                <label class="form-label">E-mail / Telephone</label>
                                <input
                                    type="text"
                                    class="form-control"
                                    name="email_telepon_anak"
                                    placeholder="Masukkan email atau telepon"
                                    value="<?php echo isset($result['email_telepon_anak']) ? htmlspecialchars($result['email_telepon_anak']) : ''; ?>" />
                            </div>
                            <div class="col-12 form-group">
                                <label class="form-label"><b>f. Nama Perusahaan Prinsipal</b></label>
                                <input
                                    type="text"
                                    class="form-control"
                                    name="perusahaan_prinsipal"
                                    placeholder="Masukkan nama perusahaan prinsipal"
                                    value="<?php echo isset($result['perusahaan_prinsipal']) ? htmlspecialchars($result['perusahaan_prinsipal']) : ''; ?>" />
                            </div>
                            <div class="col-12 form-group">
                                <label class="form-label">Alamat Pos</label>
                                <input
                                    type="text"
                                    class="form-control"
                                    name="pos_prinsipal"
                                    placeholder="Masukkan alamat pos"
                                    value="<?php echo isset($result['pos_prinsipal']) ? htmlspecialchars($result['pos_prinsipal']) : ''; ?>" />
                            </div>
                            <div class="row">
                                <div class="col-md-6 form-group">
                                    <label class="form-label">Kota</label>
                                    <input
                                        type="text"
                                        class="form-control"
                                        name="kota_prinsipal"
                                        placeholder="Masukkan kota"
                                        value="<?php echo isset($result['kota_prinsipal']) ? htmlspecialchars($result['kota_prinsipal']) : ''; ?>" />
                                </div>
                                <div class="col-md-6 form-group">
                                    <label class="form-label">Negara</label>
                                    <input
                                        type="text"
                                        class="form-control"
                                        name="negara_prinsipal"
                                        placeholder="Masukkan negara"
                                        value="<?php echo isset($result['negara_prinsipal']) ? htmlspecialchars($result['negara_prinsipal']) : ''; ?>" />
                                </div>
                            </div>
                            <div class="col-12 form-group">
                                <label class="form-label">E-mail / Telephone</label>
                                <input
                                    type="text"
                                    class="form-control"
                                    name="email_telepon_prinsipal"
                                    placeholder="Masukkan email atau telepon"
                                    value="<?php echo isset($result['email_telepon_prinsipal']) ? htmlspecialchars($result['email_telepon_prinsipal']) : ''; ?>" />
                            </div>
                        </div>
                    </div>
                </div>
                <!-- 4. Asuransi Penanggung -->
                <div class="form-section mt-4">
                    <div class="section-header">
                        <h5 class="mb-0">4. Asuransi</h5>
                    </div>
                    <div class="section-body">
                        <div class="row g-3">
                            <div class="col-12 form-group">
                                <label class="form-label">Penanggung</label>
                                <input
                                    type="text"
                                    class="form-control"
                                    name="penanggung"
                                    placeholder="Masukkan Penanggung"
                                    value="<?php echo isset($result['penanggung']) ? htmlspecialchars($result['penanggung']) : ''; ?>" />
                            </div>
                            <div class="col-12 form-group">
                                <label class="form-label">Alamat Pos</label>
                                <input
                                    type="text"
                                    class="form-control"
                                    name="asuransi_pos"
                                    placeholder="Masukkan alamat pos"
                                    value="<?php echo isset($result['asuransi_pos']) ? htmlspecialchars($result['asuransi_pos']) : ''; ?>" />
                            </div>
                            <div class="col-12 form-group">
                                <label class="form-label">Telepon/E-mail</label>
                                <input
                                    type="text"
                                    class="form-control"
                                    name="telepon_email_asuransi"
                                    placeholder="Masukkan nomor telepon"
                                    value="<?php echo isset($result['telepon_email_asuransi']) ? htmlspecialchars($result['telepon_email_asuransi']) : ''; ?>" />
                            </div>
                            <div class="col-12 form-group">
                                <label class="form-label">Jenis Jaminan</label>
                                <input
                                    type="text"
                                    class="form-control"
                                    name="jenis_jaminan"
                                    placeholder="Masukkan jenis jaminan"
                                    value="<?php echo isset($result['jenis_jaminan']) ? htmlspecialchars($result['jenis_jaminan']) : ''; ?>" />
                            </div>
                        </div>
                    </div>
                </div>
                <!-- 5. Informasi Asuransi Karyawan -->
                <div class="form-section mt-4">
                    <div class="section-header">
                        <h5 class="mb-0">5. Informasi Asuransi Karyawan</h5>
                    </div>
                    <div class="section-body">
                        <div class="form-group">
                            <label class="form-label">Apakah semua karyawan diasuransikan?</label>
                            <div>
                                <div class="form-check form-check-inline">
                                    <input
                                        class="form-check-input"
                                        type="radio"
                                        name="insuranceOption"
                                        id="insuranceYes"
                                        value="ya"
                                        <?php echo (isset($result['insurance_option']) && $result['insurance_option'] === 'ya') ? 'checked' : ''; ?> />
                                    <label class="form-check-label" for="insuranceYes">Ya</label>
                                </div>
                                <div class="form-check form-check-inline">
                                    <input
                                        class="form-check-input"
                                        type="radio"
                                        name="insuranceOption"
                                        id="insuranceNo"
                                        value="tidak"
                                        <?php echo (isset($result['insurance_option']) && $result['insurance_option'] === 'tidak') ? 'checked' : ''; ?> />
                                    <label class="form-check-label" for="insuranceNo">Tidak</label>
                                </div>
                            </div>
                        </div>
                        <div
                            id="explanation-section"
                            style="display: <?php echo (isset($result['insurance_option']) && $result['insurance_option'] === 'tidak') ? 'block' : 'none'; ?>;"
                            class="mt-3">
                            <label class="form-label">Jika tidak, jelaskan alasannya:</label>
                            <textarea
                                class="form-control"
                                rows="3"
                                placeholder="Masukkan penjelasan"
                                name="alasan_asuransi_karyawan"><?php echo isset($result['insurance_reason']) ? htmlspecialchars($result['insurance_reason']) : ''; ?></textarea>
                        </div>
                    </div>
                </div>
                <!-- 6. Riwayat Pekerjaan -->
                <div class="form-section mt-4">
                    <div class="section-header">
                        <h5 class="mb-0">6. Riwayat Pekerjaan</h5>
                    </div>
                    <div class="section-body">
                        <div class="table-responsive">
                            <table class="table table-bordered" id="workHistoryTable">
                                <thead>
                                    <tr>
                                        <th>No</th>
                                        <th>Nama Perusahaan Pemberi Pekerjaan</th>
                                        <th>Jenis Pekerjaan</th>
                                        <th>Nilai Kontrak</th>
                                        <th>Telp/Fax</th>
                                        <th>Email</th>
                                        <th>Aksi</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <?php if (!empty($work_history)) : ?>
                                        <?php foreach ($work_history as $index => $row) : ?>
                                            <tr>
                                                <td><?php echo $index + 1; ?></td>
                                                <td>
                                                    <input type="text" name="nama_perusahaan_riwayat[]" class="form-control" placeholder="Nama Perusahaan" value="<?php echo htmlspecialchars($row['nama_perusahaan_riwayat']); ?>" required />
                                                </td>
                                                <td>
                                                    <input type="text" name="jenis_pekerjaan[]" class="form-control" placeholder="Jenis Pekerjaan" value="<?php echo htmlspecialchars($row['jenis_pekerjaan']); ?>" required />
                                                </td>
                                                <td>
                                                    <input type="text" name="nilai_kontrak[]" class="form-control" placeholder="Nilai Kontrak" value="<?php echo htmlspecialchars($row['nilai_kontrak']); ?>" required />
                                                </td>
                                                <td>
                                                    <input type="text" name="telp_fax[]" class="form-control" placeholder="Telp/Fax" value="<?php echo htmlspecialchars($row['telp_fax']); ?>" required />
                                                </td>
                                                <td>
                                                    <input type="text" name="email_history[]" class="form-control" placeholder="Email" value="<?php echo htmlspecialchars($row['email_history']); ?>" required />
                                                </td>
                                                <td>
                                                    <button type="button" class="btn btn-danger btn-sm remove-row">Hapus</button>
                                                </td>
                                            </tr>
                                        <?php endforeach; ?>
                                    <?php else : ?>
                                        <tr>
                                            <td>1</td>
                                            <td><input type="text" name="nama_perusahaan_riwayat[]" class="form-control" placeholder="Nama Perusahaan" required /></td>
                                            <td><input type="text" name="jenis_pekerjaan[]" class="form-control" placeholder="Jenis Pekerjaan" required /></td>
                                            <td><input type="text" name="nilai_kontrak[]" class="form-control" placeholder="Nilai Kontrak" required /></td>
                                            <td><input type="text" name="telp_fax[]" class="form-control" placeholder="Telp/Fax" required /></td>
                                            <td><input type="text" name="email_history[]" class="form-control" placeholder="Email" required /></td>
                                            <td><button type="button" class="btn btn-danger btn-sm remove-row">Hapus</button></td>
                                        </tr>
                                    <?php endif; ?>
                                </tbody>
                            </table>
                        </div>
                        <div class="text-center mt-3">
                            <button
                                type="button"
                                class="btn btn-primary me-2"
                                id="addRowBtn">
                                Tambah Baris
                            </button>
                            <button type="button" class="btn btn-secondary" id="resetBtn">
                                Reset
                            </button>
                        </div>
                    </div>
                </div>
                <!-- 7. Pengungkapan Perkara Hukum -->
                <div class="form-section mt-4">
                    <div class="section-header">
                        <h5 class="mb-0">7. Pengungkapan Perkara Hukum</h5>
                    </div>
                    <div class="section-body">
                        <div class="form-group">
                            <label class="form-label">Apakah perusahaan sedang berurusan dengan pengadilan, klaim, atau tuntutan pihak lain?</label>
                            <div>
                                <div class="form-check">
                                    <input
                                        class="form-check-input"
                                        type="radio"
                                        name="legal_issue_option"
                                        id="legalIssueYes"
                                        value="ya"
                                        <?php echo (isset($result['legal_issue_option']) && $result['legal_issue_option'] === 'ya') ? 'checked' : ''; ?> />
                                    <label class="form-check-label" for="legalIssueYes">Ya</label>
                                </div>
                                <div class="form-check">
                                    <input
                                        class="form-check-input"
                                        type="radio"
                                        name="legal_issue_option"
                                        id="legalIssueNo"
                                        value="tidak"
                                        <?php echo (isset($result['legal_issue_option']) && $result['legal_issue_option'] === 'tidak') ? 'checked' : ''; ?> />
                                    <label class="form-check-label" for="legalIssueNo">Tidak</label>
                                </div>
                            </div>
                        </div>
                        <div
                            id="detailsSection"
                            style="display: <?php echo (isset($result['legal_issue_option']) && $result['legal_issue_option'] === 'ya') ? 'block' : 'none'; ?>;"
                            class="mt-3">
                            <label class="form-label">Jika ya, jelaskan secara rinci:</label>
                            <textarea
                                class="form-control"
                                rows="4"
                                name="alasan_pengadilan"
                                id="detailsTextarea"
                                placeholder="Masukkan detail perkara hukum"><?php echo isset($result['alasan_pengadilan']) ? htmlspecialchars($result['alasan_pengadilan']) : ''; ?></textarea>
                        </div>
                    </div>
                </div>
                <?php
                if ($isExistingUser) {
                ?>
                    <button type="submit" class="btn btn-warning mt-3">Edit</button>
                <?php
                } else {
                ?>
                    <button type="submit" class="btn btn-primary mt-3">Submit</button>

                <?php
                }
                ?>
            </form>
        </div>


        <!-- Bootstrap JS (Optional) -->
        <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
        <!--anggota dieksi-->
        <script>
            document.addEventListener("DOMContentLoaded", function() {
                const table = document.getElementById("direksiTable").querySelector("tbody");
                const addRowBtn = document.getElementById("addRow");
                const resetTableBtn = document.getElementById("resetTable");
                const dataDireksi = [
                    <?php foreach ($direksi as $row) : ?> {
                            jabatan: "<?php echo htmlspecialchars($row['jabatan']); ?>",
                            nama: "<?php echo htmlspecialchars($row['nama']); ?>",
                            pendidikan: "<?php echo htmlspecialchars($row['pendidikan']); ?>",
                            masaKerja: "<?php echo htmlspecialchars($row['masa_kerja']); ?>"
                        },
                    <?php endforeach; ?>
                ];

                let dataIndex = 0;

                // Tambah baris baru
                addRowBtn.addEventListener("click", function() {
                    const rowCount = table.rows.length + 1;
                    let newRowData = dataIndex < dataDireksi.length ?
                        dataDireksi[dataIndex++] : {
                            jabatan: "",
                            nama: "",
                            pendidikan: "",
                            masaKerja: ""
                        };

                    const newRow = `
            <tr>
                <td>${rowCount}</td>
                <td><input type="text" name="jabatan[]" class="form-control" placeholder="Jabatan" value="${newRowData.jabatan}" required /></td>
                <td><input type="text" name="nama[]" class="form-control" placeholder="Nama" value="${newRowData.nama}" required /></td>
                <td><input type="text" name="pendidikan[]" class="form-control" placeholder="Pendidikan Terakhir" value="${newRowData.pendidikan}" required /></td>
                <td><input type="text" name="masa_kerja[]" class="form-control" placeholder="Masa Kerja" value="${newRowData.masaKerja}" required /></td>
                <td><button type="button" class="btn btn-danger btn-sm remove-row">Hapus</button></td>
            </tr>`;
                    table.insertAdjacentHTML("beforeend", newRow);
                });

                // Hapus baris
                table.addEventListener("click", function(e) {
                    if (e.target.classList.contains("remove-row")) {
                        e.target.closest("tr").remove();
                        updateRowNumbers();
                    }
                });

                // Reset tabel
                resetTableBtn.addEventListener("click", function() {
                    table.innerHTML = ""; // Kosongkan tabel
                    dataIndex = 0; // Reset indeks data
                    dataDireksi.forEach((data, index) => {
                        const newRow = `
                <tr>
                    <td>${index + 1}</td>
                    <td><input type="text" name="jabatan[]" class="form-control" placeholder="Jabatan" value="${data.jabatan}" required /></td>
                    <td><input type="text" name="nama[]" class="form-control" placeholder="Nama" value="${data.nama}" required /></td>
                    <td><input type="text" name="pendidikan[]" class="form-control" placeholder="Pendidikan Terakhir" value="${data.pendidikan}" required /></td>
                    <td><input type="text" name="masa_kerja[]" class="form-control" placeholder="Masa Kerja" value="${data.masaKerja}" required /></td>
                    <td><button type="button" class="btn btn-danger btn-sm remove-row">Hapus</button></td>
                </tr>`;
                        table.insertAdjacentHTML("beforeend", newRow);
                    });
                });

                // Update nomor urut
                function updateRowNumbers() {
                    Array.from(table.rows).forEach((row, index) => {
                        row.cells[0].textContent = index + 1;
                    });
                }
            });
        </script>
        <!--Data no 5-->
        <script>
            document.addEventListener("DOMContentLoaded", function() {
                // Referensi elemen
                const insuranceYes = document.getElementById("insuranceYes");
                const insuranceNo = document.getElementById("insuranceNo");
                const explanationSection = document.getElementById(
                    "explanation-section"
                );

                // Tambahkan event listener untuk radio button
                insuranceYes.addEventListener("change", function() {
                    if (insuranceYes.checked) {
                        explanationSection.style.display = "none";
                    }
                });

                insuranceNo.addEventListener("change", function() {
                    if (insuranceNo.checked) {
                        explanationSection.style.display = "block";
                    }
                });
            });
        </script>
        <!-- Riwayat Pekerjaan -->
        <script>
            document.addEventListener("DOMContentLoaded", function() {
                const table = document.getElementById("workHistoryTable").querySelector("tbody");
                const addRowBtn = document.getElementById("addRowBtn");
                const resetTableBtn = document.getElementById("resetBtn");
                const initialData = [
                    <?php if (!empty($work_history)) : ?>
                        <?php foreach ($work_history as $row) : ?> {
                                nama_perusahaan: "<?php echo htmlspecialchars($row['nama_perusahaan_riwayat']); ?>",
                                jenis_pekerjaan: "<?php echo htmlspecialchars($row['jenis_pekerjaan']); ?>",
                                nilai_kontrak: "<?php echo htmlspecialchars($row['nilai_kontrak']); ?>",
                                telp_fax: "<?php echo htmlspecialchars($row['telp_fax']); ?>",
                                email: "<?php echo htmlspecialchars($row['email_history']); ?>"
                            },
                        <?php endforeach; ?>
                    <?php endif; ?>
                ];

                let dataIndex = 0;

                // Tambah baris baru
                addRowBtn.addEventListener("click", function() {
                    const rowCount = table.rows.length + 1;
                    let newRowData = dataIndex < initialData.length ? initialData[dataIndex++] : {
                        nama_perusahaan: "",
                        jenis_pekerjaan: "",
                        nilai_kontrak: "",
                        telp_fax: "",
                        email: ""
                    };

                    const newRow = `
            <tr>
                <td>${rowCount}</td>
                <td><input type="text" name="nama_perusahaan_riwayat[]" class="form-control" placeholder="Nama Perusahaan" value="${newRowData.nama_perusahaan}" required /></td>
                <td><input type="text" name="jenis_pekerjaan[]" class="form-control" placeholder="Jenis Pekerjaan" value="${newRowData.jenis_pekerjaan}" required /></td>
                <td><input type="text" name="nilai_kontrak[]" class="form-control" placeholder="Nilai Kontrak" value="${newRowData.nilai_kontrak}" required /></td>
                <td><input type="text" name="telp_fax[]" class="form-control" placeholder="Telp/Fax" value="${newRowData.telp_fax}" required /></td>
                <td><input type="email" name="email_history[]" class="form-control" placeholder="Email" value="${newRowData.email}" required /></td>
                <td><button type="button" class="btn btn-danger btn-sm remove-row">Hapus</button></td>
            </tr>`;
                    table.insertAdjacentHTML("beforeend", newRow);
                });

                // Hapus baris
                table.addEventListener("click", function(e) {
                    if (e.target.classList.contains("remove-row")) {
                        e.target.closest("tr").remove();
                        updateRowNumbers();
                    }
                });

                // Reset tabel
                resetTableBtn.addEventListener("click", function() {
                    table.innerHTML = ""; // Kosongkan tabel
                    dataIndex = 0; // Reset indeks data
                    initialData.forEach((data, index) => {
                        const newRow = `
                <tr>
                    <td>${index + 1}</td>
                    <td><input type="text" name="nama_perusahaan_riwayat[]" class="form-control" placeholder="Nama Perusahaan" value="${data.nama_perusahaan}" required /></td>
                    <td><input type="text" name="jenis_pekerjaan[]" class="form-control" placeholder="Jenis Pekerjaan" value="${data.jenis_pekerjaan}" required /></td>
                    <td><input type="text" name="nilai_kontrak[]" class="form-control" placeholder="Nilai Kontrak" value="${data.nilai_kontrak}" required /></td>
                    <td><input type="text" name="telp_fax[]" class="form-control" placeholder="Telp/Fax" value="${data.telp_fax}" required /></td>
                    <td><input type="email" name="email_history[]" class="form-control" placeholder="Email" value="${data.email}" required /></td>
                    <td><button type="button" class="btn btn-danger btn-sm remove-row">Hapus</button></td>
                </tr>`;
                        table.insertAdjacentHTML("beforeend", newRow);
                    });
                });

                // Update nomor urut
                function updateRowNumbers() {
                    Array.from(table.rows).forEach((row, index) => {
                        row.cells[0].textContent = index + 1;
                    });
                }
            });
        </script>

        <!--Data No 7-->
        <script>
            document.addEventListener("DOMContentLoaded", function() {
                const legalIssueYes = document.getElementById("legalIssueYes");
                const legalIssueNo = document.getElementById("legalIssueNo");
                const detailsSection = document.getElementById("detailsSection");
                const detailsTextarea = document.getElementById("detailsTextarea");
                const submitBtn = document.getElementById("submitBtn");
                const resetBtn = document.getElementById("resetBtn");

                // Menampilkan/menghilangkan bagian detail berdasarkan pilihan
                function toggleDetailsSection() {
                    if (legalIssueYes.checked) {
                        detailsSection.style.display = "block";
                        detailsTextarea.required = true; // Set sebagai wajib diisi
                    } else {
                        detailsSection.style.display = "none";
                        detailsTextarea.required = false; // Tidak wajib diisi
                        detailsTextarea.value = ""; // Kosongkan textarea
                    }
                }

                legalIssueYes.addEventListener("change", toggleDetailsSection);
                legalIssueNo.addEventListener("change", toggleDetailsSection);

                // Fungsi untuk tombol kirim
                submitBtn.addEventListener("click", function() {
                    const selectedOption = document.querySelector(
                        'input[name="legalissueoption"]:checked'
                    );
                    const details = detailsTextarea.value;

                    if (!selectedOption) {
                        alert("Pilih salah satu opsi Ya atau Tidak.");
                        return;
                    }

                    if (selectedOption.value === "ya" && details.trim() === "") {
                        alert("Harap jelaskan detail perkara hukum.");
                        return;
                    }

                    // Kirim data (contoh simulasi)
                    alert(
                        `Pilihan: ${selectedOption.value}\nDetail: ${
                  selectedOption.value === "ya"
                    ? details
                    : "Tidak ada perkara hukum"
                }`
                    );

                    // Tambahkan logika untuk mengirim data ke server di sini jika diperlukan
                });

                // Fungsi untuk tombol reset
                resetBtn.addEventListener("click", function() {
                    // Reset semua input
                    document
                        .querySelectorAll('input[name="legalissueoption"]')
                        .forEach((input) => {
                            input.checked = false;
                        });
                    detailsTextarea.value = "";
                    detailsSection.style.display = "none";
                });
            });
        </script>
    </body>

    </html>
</body>

</html>