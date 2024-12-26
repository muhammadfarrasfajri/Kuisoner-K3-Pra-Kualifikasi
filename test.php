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
} else {
    $dataPerusahaan = null;
    $direksi = [];
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
    :root {
        --pusri-blue: #004aad;
        --pusri-light-blue: #e3f2fd;
        --pusri-dark-blue: #002f6c;
        --pusri-text-light: #ffffff;
    }

    .navbar {
        background-color: var(--pusri-blue);
        height: 60px;
    }

    .sidebar {
        background-color: var(--pusri-dark-blue);
        height: 100vh;
        transition: transform 0.3s ease;
        position: fixed;
        top: 60px;
        left: 0;
        width: 250px;
        overflow-y: auto;
    }

    .sidebar.collapsed {
        transform: translateX(-100%);
    }

    .sidebar .nav-link {
        color: var(--pusri-text-light);
    }

    .sidebar .nav-link:hover {
        background-color: var(--pusri-blue);
        color: var(--pusri-light-blue);
    }

    .content {
        margin-left: 250px;
        /* Default saat sidebar ditampilkan */
        transition: margin-left 0.3s ease;
        padding: 20px;
        max-width: calc(100% - 250px);
        /* Mencegah konten melebar di luar layar */
        overflow-x: hidden;
        /* Hindari scroll horizontal */
    }

    .content.collapsed {
        margin-left: 0;
        max-width: 100%;
        /* Pastikan lebar konten mengikuti layar saat sidebar tertutup */
    }


    footer {
        background-color: var(--pusri-blue);
        color: var(--pusri-text-light);
        position: fixed;
        bottom: 0;
        width: 100%;
        text-align: center;
        padding: 10px 0;
    }
</style>

<body>
    <?php include '../layouts/header2.php'; ?>
    <div class="container-fluid">
        <div class="row">
            <!-- Sidebar -->
            <nav class="sidebar" id="sidebar">
                <?php include '../layouts/sidebar.php'; ?>
            </nav>
            <!-- Konten Utama -->
            <main class="content" id="content">
                <div class="container mt-5">
                    <!-- Dropdown untuk memilih nama perusahaan -->
                    <form method="POST" action="">
                        <div class="mb-3">
                            <label for="nama_perusahaan" class="form-label">Pilih Nama Perusahaan</label>
                            <select name="nama_perusahaan" id="nama_perusahaan" class="form-select">
                                <option value="">-- Pilih Nama Perusahaan --</option>
                                <?php foreach ($perusahaanList as $perusahaan) : ?>
                                    <option value="<?php echo htmlspecialchars($perusahaan['nama_perusahaan']); ?>"
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
                        <h3 class="mt-5">Data Perusahaan: <?php echo htmlspecialchars($dataPerusahaan['nama_perusahaan']); ?></h3>

                        <!-- Tabel Data Perusahaan -->
                        <table class="table mt-3">
                            <thead>
                                <tr>
                                    <th>Nama Perusahaan</th>
                                    <th>Alamat</th>
                                    <th>Email</th>
                                    <th>Pekerjaan</th>
                                    <th>Username</th>
                                </tr>
                            </thead>
                            <tbody>
                                <tr>
                                    <td><?php echo htmlspecialchars($dataPerusahaan['nama_perusahaan']); ?></td>
                                    <td><?php echo htmlspecialchars($dataPerusahaan['alamat_pos']); ?></td>
                                    <td><?php echo htmlspecialchars($dataPerusahaan['email']); ?></td>
                                    <td><?php echo htmlspecialchars($dataPerusahaan['pekerjaan']); ?></td>
                                    <td><?php echo htmlspecialchars($dataPerusahaan['username']); ?></td>
                                </tr>
                            </tbody>
                        </table>

                        <!-- Tabel Direksi -->
                        <h4>Direksi Perusahaan</h4>
                        <table class="table mt-3">
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
                            <h4>Direksi Perusahaan</h4>
                        </table>
                        <table class="table mt-3">
                            <thead>
                                <tr>
                                    <th>Berdiri Tahun</th>
                                    <th>Alamat</th>
                                    <th>Email</th>
                                    <th>Pekerjaan</th>
                                    <th>Username</th>
                                    <th>Berdiri Tahun</th>
                                    <th>Alamat</th>
                                    <th>Email</th>
                                    <th>Pekerjaan</th>
                                    <th>Username</th>
                                    <th>Berdiri Tahun</th>
                                    <th>Alamat</th>
                                    <th>Email</th>
                                    <th>Pekerjaan</th>
                                    <th>Username</th>
                                </tr>
                            </thead>
                            <tbody>
                                <tr>
                                    <td><?php echo htmlspecialchars($dataPerusahaan['berdiri_tahun']); ?></td>
                                    <td><?php echo htmlspecialchars($dataPerusahaan['manajemen_sejak']); ?></td>
                                    <td><?php echo htmlspecialchars($dataPerusahaan['bentuk_usaha']); ?></td>
                                    <td><?php echo htmlspecialchars($dataPerusahaan['perusahaan_induk']); ?></td>
                                    <td><?php echo htmlspecialchars($dataPerusahaan['pos_induk']); ?></td>
                                    <td><?php echo htmlspecialchars($dataPerusahaan['kota_induk']); ?></td>
                                    <td><?php echo htmlspecialchars($dataPerusahaan['negara_induk']); ?></td>
                                    <td><?php echo htmlspecialchars($dataPerusahaan['anak_perusahaan']); ?></td>
                                    <td><?php echo htmlspecialchars($dataPerusahaan['pos_anak']); ?></td>
                                    <td><?php echo htmlspecialchars($dataPerusahaan['kota_anak']); ?></td>
                                    <td><?php echo htmlspecialchars($dataPerusahaan['negara_anak']); ?></td>
                                    <td><?php echo htmlspecialchars($dataPerusahaan['perusahaan_prinsipal']); ?></td>
                                    <td><?php echo htmlspecialchars($dataPerusahaan['kota_prinsipal']); ?></td>
                                    <td><?php echo htmlspecialchars($dataPerusahaan['negara_prinsipal']); ?></td>
                                    <td><?php echo htmlspecialchars($dataPerusahaan['email_telepon_prinsipal']); ?></td>
                                </tr>
                            </tbody>
                        </table>
                        <h4>Direksi Perusahaan</h4>
                        <table class="table mt-3">
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
                        <h4>Direksi Perusahaan</h4>
                        <table class="table mt-3">
                            <thead>
                                <tr>
                                    <th>Nama Perusahaan</th>
                                    <th>Alamat</th>
                                </tr>
                            </thead>


                            <tbody>
                                <tr>
                                    <td><?php echo htmlspecialchars($dataPerusahaan['insurance_option']); ?></td>
                                    <td><?php echo htmlspecialchars($dataPerusahaan['insurance_reason']); ?></td>
                                </tr>
                            </tbody>
                        </table>
                    <?php else : ?>
                        <p class="mt-3">Tidak ada data untuk perusahaan yang dipilih.</p>
                    <?php endif; ?>

                    <!-- Kembali ke Dashboard -->
                </div>
            </main>
        </div>
    </div>

    <?php include '../layouts/footer.php'; ?>

    <!-- Bootstrap JS -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha1/dist/js/bootstrap.bundle.min.js"></script>
    <script>
        const sidebar = document.getElementById('sidebar');
        const content = document.getElementById('content');
        const toggleButton = document.getElementById('sidebarToggle');

        toggleButton.addEventListener('click', () => {
            sidebar.classList.toggle('collapsed');
            content.classList.toggle('collapsed');
        });
    </script>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>

</html>