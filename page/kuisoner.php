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
    $sql = "SELECT * FROM kuisoner WHERE user_id = :user_id";
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


?>
<!DOCTYPE html>
<html lang="id">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Form Kuisioner K3</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
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
</style>

<body>
    <nav class="navbar navbar-dark bg-primary fixed-top text-white d-flex align-items-center justify-content-center">
        <h3 class="m-0 text-center w-100">Form Kuisioner K3</h3>
        <a href="../page/dashboard.php" class="btn btn-secondary position-absolute end-0 me-3">Kembali</a>
    </nav>
    <div class="container mt-5">
        <form action="../controllers/proses_kuisoner.php" method="POST" enctype="multipart/form-data">
            <!-- KOMITMEN MANAJEMEN -->

            <h3 class="text-primary border-bottom border-primary pb-2">
                <i class="fas fa-tools"></i> I FAKTOR UTAMA
            </h3>
            <div class="form-section">
                <div class="section-header">
                    <h5 class="mb-0">1. KOMITMEN MANAJEMEN</h5>
                </div>
                <div class="mb-3 section-body">
                    <label>Apakah perusahaan saudara memiliki kebijakan K3?</label>
                    <div>
                        <div class="form-check form-check-inline">
                            <input
                                class="form-check-input"
                                type="radio"
                                name="komitmenA"
                                value="Ya"
                                id="komitmenA-ya"
                                <?php echo (isset($result['komitmenA']) && $result['komitmenA'] === 'Ya') ? 'checked' : ''; ?> />
                            <label class="form-check-label" for="komitmenA-ya">Ya</label>
                        </div>
                        <div class="form-check form-check-inline">
                            <input
                                class="form-check-input"
                                type="radio"
                                name="komitmenA"
                                value="Tidak"
                                id="komitmenA-tidak"
                                <?php echo (isset($result['komitmenA']) && $result['komitmenA'] === 'Tidak') ? 'checked' : ''; ?> />
                            <label class="form-check-label" for="komitmenA-tidak">Tidak</label>
                        </div>
                    </div>
                    <label class="form-label mt-2">Lampirkan kebijakan K3:</label>
                    <input
                        type="file"
                        class="form-control"
                        name="kebijakan_k3"
                        id="kebijakan_k3"
                        <?php echo (isset($result['kebijakan_k3_path'])) ? 'value="' . htmlspecialchars($result['kebijakan_k3_path']) . '"' : ''; ?> />
                </div>

                <!-- Sosialisasi Kebijakan K3 -->
                <div class="mb-3 section-body">
                    <label>Apakah kebijakan K3 sudah disosialisasikan dan dipahami oleh seluruh pekerja serta ditinjau ulang secara berkala?</label>
                    <div>
                        <div class="form-check form-check-inline">
                            <input
                                class="form-check-input"
                                type="radio"
                                name="komitmenB"
                                value="Ya"
                                id="komitmenB-ya"
                                <?php echo (isset($result['komitmenB']) && $result['komitmenB'] === 'Ya') ? 'checked' : ''; ?> />
                            <label class="form-check-label" for="komitmenB-ya">Ya</label>
                        </div>
                        <div class="form-check form-check-inline">
                            <input
                                class="form-check-input"
                                type="radio"
                                name="komitmenB"
                                value="Tidak"
                                id="komitmenB-tidak"
                                <?php echo (isset($result['komitmenB']) && $result['komitmenB'] === 'Tidak') ? 'checked' : ''; ?> />
                            <label class="form-check-label" for="komitmenB-tidak">Tidak</label>
                        </div>
                    </div>
                    <label class="form-label mt-2">Lampirkan program sosialisasi dan absen peserta:</label>
                    <input
                        type="file"
                        class="form-control"
                        name="sosialisasi_absen"
                        <?php echo (isset($result['sosialisasi_absen_path'])) ? 'value="' . htmlspecialchars($result['sosialisasi_absen_path']) . '"' : ''; ?> />
                </div>
            </div>
            <!-- ORGANISASI K3 -->

            <div class="form-section">
                <div class="section-header">
                    <h4 class="mb-0">2. ORGANISASI K3</h4>
                </div>
                <div class="mb-3 section-body">
                    <label>Apakah Perusahaan Saudara mempunyai organisasi K3?</label>
                    <div>
                        <div class="form-check form-check-inline">
                            <input class="form-check-input" type="radio" name="organisasiA" value="Ya" id="organisasiA-ya"
                                <?php echo (isset($result['organisasiA']) && $result['organisasiA'] === 'Ya') ? 'checked' : ''; ?> />
                            <label class="form-check-label" for="organisasiA-ya">Ya</label>
                        </div>
                        <div class="form-check form-check-inline">
                            <input class="form-check-input" type="radio" name="organisasiA" value="Tidak" id="organisasiA-tidak"
                                <?php echo (isset($result['organisasiA']) && $result['organisasiA'] === 'Tidak') ? 'checked' : ''; ?> />
                            <label class="form-check-label" for="organisasiA-tidak">Tidak</label>
                        </div>
                    </div>
                    <label class="form-label mt-2">Lampirkan uraian kerja (Job Description):</label>
                    <input type="file" class="form-control" name="job_description">
                </div>
                <div class="mb-3 section-body">
                    <label>Adakah person dalam organisasi yang bertanggung jawab terhadap kebijakan K3?</label>
                    <input type="text" class="form-control" name="person_bertanggung_jawab" placeholder="Sebutkan level jabatan masing-masing">
                </div>
            </div>


            <!-- PROGRAM INSPEKSI K3 -->
            <div class="form-section">
                <div class="section-header">
                    <h4 class="mb-0">3. PROGRAM INSPEKSI K3</h4>
                </div>
                <div class="mb-3 section-body">
                    <label>Apakah perusahaan saudara memiliki program Inspeksi yang dilakukan oleh Manajemen?</label>
                    <div>
                        <div class="form-check form-check-inline">
                            <input class="form-check-input" type="radio" name="inspeksiA" value="Ya" id="inspeksiA-ya"
                                <?php echo (isset($result['inspeksiA']) && $result['inspeksiA'] === 'Ya') ? 'checked' : ''; ?> />
                            <label class="form-check-label" for="inspeksiA-ya">Ya</label>
                        </div>
                        <div class="form-check form-check-inline">
                            <input class="form-check-input" type="radio" name="inspeksiA" value="Tidak" id="inspeksiA-tidak"
                                <?php echo (isset($result['inspeksiA']) && $result['inspeksiA'] === 'Tidak') ? 'checked' : ''; ?> />
                            <label class="form-check-label" for="inspeksiA-tidak">Tidak</label>
                        </div>
                    </div>
                    <label class="form-label mt-2">Lampirkan program tersebut:</label>
                    <input type="file" class="form-control" name="program_inspeksi">
                </div>
                <div class="mb-3 section-body">
                    <label>Apakah hasil temuan Inspeksi Manajemen selalu ditindak lanjuti?</label>
                    <div>
                        <div class="form-check form-check-inline">
                            <input class="form-check-input" type="radio" name="inspeksiB" value="Ya" id="inspeksiB-ya"
                                <?php echo (isset($result['inspeksiB']) && $result['inspeksiB'] === 'Ya') ? 'checked' : ''; ?> />
                            <label class="form-check-label" for="inspeksiB-ya">Ya</label>
                        </div>
                        <div class="form-check form-check-inline">
                            <input class="form-check-input" type="radio" name="inspeksiB" value="Tidak" id="inspeksiB-tidak"
                                <?php echo (isset($result['inspeksiB']) && $result['inspeksiB'] === 'Tidak') ? 'checked' : ''; ?> />
                            <label class="form-check-label" for="inspeksiB-tidak">Tidak</label>
                        </div>
                    </div>
                    <label class="form-label mt-2">Lampirkan bukti tindak lanjut:</label>
                    <input type="file" class="form-control" name="bukti_tindak_lanjut_inspeksi">
                </div>
            </div>
            <!-- PROGRAM RAPAT K3 -->
            <div class="form-section">
                <div class="section-header">
                    <h4 class="mb-0">4. PROGRAM RAPAT K3</h4>
                </div>
                <div class="mb-3 section-body">
                    <label>Apakah perusahaan saudara menyelenggarakan rapat–rapat rutin tentang K3 dan dihadiri oleh Manajemen?</label>
                    <div>
                        <div class="form-check form-check-inline">
                            <input class="form-check-input" type="radio" name="rapatA" value="Ya" id="rapatA-ya"
                                <?php echo (isset($result['rapatA']) && $result['rapatA'] === 'Ya') ? 'checked' : ''; ?> />
                            <label class="form-check-label" for="rapatA-ya">Ya</label>
                        </div>
                        <div class="form-check form-check-inline">
                            <input class="form-check-input" type="radio" name="rapatA" value="Tidak" id="rapatA-tidak"
                                <?php echo (isset($result['rapatA']) && $result['rapatA'] === 'Tidak') ? 'checked' : ''; ?> />
                            <label class="form-check-label" for="rapatA-tidak">Tidak</label>
                        </div>
                    </div>
                    <label class="form-label mt-2">Lampirkan bukti rapat:</label>
                    <input type="file" class="form-control" name="bukti_rapat">
                </div>
                <div class="mb-3 section-body">
                    <label>Apakah hasil rapat K3 ditindak lanjuti?</label>
                    <div>
                        <div class="form-check form-check-inline">
                            <input class="form-check-input" type="radio" name="rapatB" value="Ya" id="rapatB-ya"
                                <?php echo (isset($result['rapatB']) && $result['rapatB'] === 'Ya') ? 'checked' : ''; ?> />
                            <label class="form-check-label" for="rapatB-ya">Ya</label>
                        </div>
                        <div class="form-check form-check-inline">
                            <input class="form-check-input" type="radio" name="rapatB" value="Tidak" id="rapatB-tidak"
                                <?php echo (isset($result['rapatB']) && $result['rapatB'] === 'Tidak') ? 'checked' : ''; ?> />
                            <label class="form-check-label" for="rapatB-tidak">Tidak</label>
                        </div>
                    </div>
                    <label class="form-label mt-2">Lampirkan bukti tindak lanjut rapat:</label>
                    <input type="file" class="form-control" name="bukti_tindak_lanjut_rapat">
                </div>
            </div>

            <div class="form-section">
                <div class="section-header">
                    <h4 class="mb-0">5. PERENCANAAN KEADAAN DARURAT</h4>
                </div>
                <div class="mb-3 section-body">
                    <label>Apakah Perusahaan saudara mempunyai prosedur Penanggulangan keadaan darurat dan melakukan latihan berkala?</label>
                    <div>
                        <div class="form-check form-check-inline">
                            <input class="form-check-input" type="radio" name="daruratA" value="Ya" id="daruratA-ya"
                                <?php echo (isset($result['daruratA']) && $result['daruratA'] === 'Ya') ? 'checked' : ''; ?> />
                            <label class="form-check-label" for="daruratA-ya">Ya</label>
                        </div>
                        <div class="form-check form-check-inline">
                            <input class="form-check-input" type="radio" name="daruratA" value="Tidak" id="daruratA-tidak"
                                <?php echo (isset($result['daruratA']) && $result['daruratA'] === 'Tidak') ? 'checked' : ''; ?> />
                            <label class="form-check-label" for="daruratA-tidak">Tidak</label>
                        </div>
                    </div>
                    <label class="form-label mt-2">Lampirkan bukti:</label>
                    <input type="file" class="form-control" name="bukti_darurat">
                </div>
            </div>

            <!-- PROSEDUR K3 -->
            <div class="form-section">
                <div class="section-header">
                    <h4 class="mb-0">6. PROSEDUR K3</h4>
                </div>
                <div class="mb-3 section-body">
                    <label>Apakah perusahaan saudara mempunyai prosedur / buku panduan Keselamatan dan kesehatan kerja?</label>
                    <div>
                        <div class="form-check form-check-inline">
                            <input class="form-check-input" type="radio" name="prosedurK3A" value="Ya" id="prosedurK3A-ya"
                                <?php echo (isset($result['prosedurK3A']) && $result['prosedurK3A'] === 'Ya') ? 'checked' : ''; ?> />
                            <label class="form-check-label" for="prosedurK3A-ya">Ya</label>
                        </div>
                        <div class="form-check form-check-inline">
                            <input class="form-check-input" type="radio" name="prosedurK3A" value="Tidak" id="prosedurK3A-tidak"
                                <?php echo (isset($result['prosedurK3A']) && $result['prosedurK3A'] === 'Tidak') ? 'checked' : ''; ?> />
                            <label class="form-check-label" for="prosedurK3A-tidak">Tidak</label>
                        </div>
                    </div>
                    <label class="form-label mt-2">Lampirkan prosedur / buku panduan:</label>
                    <input type="file" class="form-control" name="panduan_k3">
                </div>
                <div class="mb-3 section-body">
                    <label>Apakah perusahaan saudara memiliki buku/referensi (standar, kumpulan peraturan perundangan) tentang K3?</label>
                    <div>
                        <div class="form-check form-check-inline">
                            <input class="form-check-input" type="radio" name="prosedurK3B" value="Ya" id="prosedurK3B-ya"
                                <?php echo (isset($result['prosedurK3B']) && $result['prosedurK3B'] === 'Ya') ? 'checked' : ''; ?> />
                            <label class="form-check-label" for="prosedurK3B-ya">Ya</label>
                        </div>
                        <div class="form-check form-check-inline">
                            <input class="form-check-input" type="radio" name="prosedurK3B" value="Tidak" id="prosedurK3B-tidak"
                                <?php echo (isset($result['prosedurK3B']) && $result['prosedurK3B'] === 'Tidak') ? 'checked' : ''; ?> />
                            <label class="form-check-label" for="prosedurK3B-tidak">Tidak</label>
                        </div>
                    </div>
                    <label class="form-label mt-2">Lampirkan daftar buku:</label>
                    <input type="file" class="form-control" name="daftar_buku_k3">
                </div>
            </div>
            <!-- PROSEDUR KECELAKAAN KERJA -->
            <div class="form-section">
                <div class="section-header">
                    <h4 class="mb-0">7. PROSEDUR KECELAKAAN KERJA</h4>
                </div>
                <div class="mb-3 section-body">
                    <label>Apakah perusahaan saudara mempunyai prosedur pelaporan kecelakaan kerja dan investigasi?</label>
                    <div>
                        <div class="form-check form-check-inline">
                            <input class="form-check-input" type="radio" name="kecelakaanA" value="Ya" id="kecelakaanA-ya"
                                <?php echo (isset($result['kecelakaanA']) && $result['kecelakaanA'] === 'Ya') ? 'checked' : ''; ?> />
                            <label class="form-check-label" for="kecelakaanA-ya">Ya</label>
                        </div>
                        <div class="form-check form-check-inline">
                            <input class="form-check-input" type="radio" name="kecelakaanA" value="Tidak" id="kecelakaanA-tidak"
                                <?php echo (isset($result['kecelakaanA']) && $result['kecelakaanA'] === 'Tidak') ? 'checked' : ''; ?> />
                            <label class="form-check-label" for="kecelakaanA-tidak">Tidak</label>
                        </div>
                    </div>
                    <label class="form-label mt-2">Lampirkan prosedur:</label>
                    <input type="file" class="form-control" name="prosedur_kecelakaan">
                </div>
                <div class="mb-3 section-body">
                    <label>Apakah ada prosedur atau teknik untuk mengidentifikasi, menilai, mengawasi, dan mengurangi dampak bahaya?</label>
                    <div>
                        <div class="form-check form-check-inline">
                            <input class="form-check-input" type="radio" name="kecelakaanB" value="Ya" id="kecelakaanB-ya"
                                <?php echo (isset($result['kecelakaanB']) && $result['kecelakaanB'] === 'Ya') ? 'checked' : ''; ?> />
                            <label class="form-check-label" for="kecelakaanB-ya">Ya</label>
                        </div>
                        <div class="form-check form-check-inline">
                            <input class="form-check-input" type="radio" name="kecelakaanB" value="Tidak" id="kecelakaanB-tidak"
                                <?php echo (isset($result['kecelakaanB']) && $result['kecelakaanB'] === 'Tidak') ? 'checked' : ''; ?> />
                            <label class="form-check-label" for="kecelakaanB-tidak">Tidak</label>
                        </div>
                    </div>
                    <label class="form-label mt-2">Lampirkan prosedur:</label>
                    <input type="file" class="form-control" name="prosedur_bahaya">
                </div>
            </div>

            <!-- PROGRAM PELATIHAN K3 -->
            <div class="form-section">
                <div class="section-header">
                    <h4 class="mb-0">8. PROGRAM PELATIHAN K3</h4>
                </div>
                <div class="mb-3 section-body">
                    <label>Apakah perusahaan saudara mempunyai program pelatihan (teori & praktek) tentang K3?</label>
                    <div>
                        <div class="form-check form-check-inline">
                            <input class="form-check-input" type="radio" name="pelatihanA" value="Ya" id="pelatihanA-ya"
                                <?php echo (isset($result['pelatihanA']) && $result['pelatihanA'] === 'Ya') ? 'checked' : ''; ?> />
                            <label class="form-check-label" for="pelatihanA-ya">Ya</label>
                        </div>
                        <div class="form-check form-check-inline">
                            <input class="form-check-input" type="radio" name="pelatihanA" value="Tidak" id="pelatihanA-tidak"
                                <?php echo (isset($result['pelatihanA']) && $result['pelatihanA'] === 'Tidak') ? 'checked' : ''; ?> />
                            <label class="form-check-label" for="pelatihanA-tidak">Tidak</label>
                        </div>
                    </div>
                    <label class="form-label mt-2">Lampirkan program:</label>
                    <input type="file" class="form-control" name="program_pelatihan">
                </div>
                <div class="mb-3 section-body">
                    <label>Apakah para penanggung jawab K3 telah mendapatkan pelatihan sesuai tanggung jawabnya?</label>
                    <div>
                        <div class="form-check form-check-inline">
                            <input class="form-check-input" type="radio" name="pelatihanB" value="Ya" id="pelatihanB-ya"
                                <?php echo (isset($result['pelatihanB']) && $result['pelatihanB'] === 'Ya') ? 'checked' : ''; ?> />
                            <label class="form-check-label" for="pelatihanB-ya">Ya</label>
                        </div>
                        <div class="form-check form-check-inline">
                            <input class="form-check-input" type="radio" name="pelatihanB" value="Tidak" id="pelatihanB-tidak"
                                <?php echo (isset($result['pelatihanB']) && $result['pelatihanB'] === 'Tidak') ? 'checked' : ''; ?> />
                            <label class="form-check-label" for="pelatihanB-tidak">Tidak</label>
                        </div>
                    </div>
                    <label class="form-label mt-2">Lampirkan program dan jadwal pelatihan:</label>
                    <input type="file" class="form-control" name="jadwal_pelatihan">
                </div>
                <div class="mb-3 section-body">
                    <label>Apakah perusahaan Saudara mempunyai petugas yang berkualifikasi ahli K3?</label>
                    <div>
                        <div class="form-check form-check-inline">
                            <input class="form-check-input" type="radio" name="pelatihanC" value="Ya" id="pelatihanC-ya"
                                <?php echo (isset($result['pelatihanC']) && $result['pelatihanC'] === 'Ya') ? 'checked' : ''; ?> />
                            <label class="form-check-label" for="pelatihanC-ya">Ya</label>
                        </div>
                        <div class="form-check form-check-inline">
                            <input class="form-check-input" type="radio" name="pelatihanC" value="Tidak" id="pelatihanC-tidak"
                                <?php echo (isset($result['pelatihanC']) && $result['pelatihanC'] === 'Tidak') ? 'checked' : ''; ?> />
                            <label class="form-check-label" for="pelatihanC-tidak">Tidak</label>
                        </div>
                    </div>
                    <label class="form-label mt-2">Lampirkan daftar nama dan sertifikat:</label>
                    <input type="file" class="form-control" name="sertifikat_ahli_k3">
                </div>
            </div>
            <!-- 9. ALAT PELINDUNG DIRI -->
            <div class="form-section">
                <div class="section-header">
                    <h4 class="mb-0">9. ALAT PELINDUNG DIRI</h4>
                </div>
                <div class="mb-3 section-body">
                    <label>Apakah perusahaan saudara memberikan alat pelindung diri pada setiap karyawan yang akan melaksanakan pekerjaan?</label>
                    <div>
                        <div class="form-check form-check-inline">
                            <input class="form-check-input" type="radio" name="apdA" value="Ya" id="apdA-ya"
                                <?php echo (isset($result['apdA']) && $result['apdA'] === 'Ya') ? 'checked' : ''; ?> />
                            <label class="form-check-label" for="apdA-ya">Ya</label>
                        </div>
                        <div class="form-check form-check-inline">
                            <input class="form-check-input" type="radio" name="apdA" value="Tidak" id="apdA-tidak"
                                <?php echo (isset($result['apdA']) && $result['apdA'] === 'Tidak') ? 'checked' : ''; ?> />
                            <label class="form-check-label" for="apdA-tidak">Tidak</label>
                        </div>
                    </div>
                    <label class="form-label mt-2">Jika ya, sebutkan jenis APD yang diberikan:</label>
                    <input type="file" class="form-control" name="jenis_apd">
                </div>

                <!-- 9.B -->
                <div class="mb-3 section-body">
                    <label>Apakah ada prosedur pemeriksaan dan pemeliharaan alat pelindung diri khusus?</label>
                    <div>
                        <div class="form-check form-check-inline">
                            <input class="form-check-input" type="radio" name="apdB" value="Ya" id="apdB-ya"
                                <?php echo (isset($result['apdB']) && $result['apdB'] === 'Ya') ? 'checked' : ''; ?> />
                            <label class="form-check-label" for="apdB-ya">Ya</label>
                        </div>
                        <div class="form-check form-check-inline">
                            <input class="form-check-input" type="radio" name="apdB" value="Tidak" id="apdB-tidak"
                                <?php echo (isset($result['apdB']) && $result['apdB'] === 'Tidak') ? 'checked' : ''; ?> />
                            <label class="form-check-label" for="apdB-tidak">Tidak</label>
                        </div>
                    </div>
                    <label class="form-label mt-2">Jika ya, lampirkan prosedur tersebut:</label>
                    <input type="file" class="form-control" name="prosedur_pemeliharaan_apd">
                </div>

                <!-- 9.C -->
                <div class="mb-3 section-body">
                    <label>Apakah perusahaan saudara selalu memeriksa dan mensertifikasi secara rutin semua peralatan kerja yang digunakan?</label>
                    <div>
                        <div class="form-check form-check-inline">
                            <input class="form-check-input" type="radio" name="apdC" value="Ya" id="apdC-ya"
                                <?php echo (isset($result['apdC']) && $result['apdC'] === 'Ya') ? 'checked' : ''; ?> />
                            <label class="form-check-label" for="apdC-ya">Ya</label>
                        </div>
                        <div class="form-check form-check-inline">
                            <input class="form-check-input" type="radio" name="apdC" value="Tidak" id="apdC-tidak"
                                <?php echo (isset($result['apdC']) && $result['apdC'] === 'Tidak') ? 'checked' : ''; ?> />
                            <label class="form-check-label" for="apdC-tidak">Tidak</label>
                        </div>
                    </div>
                    <label class="form-label mt-2">Jika ya, lampirkan sertifikat atau hasil pemeriksaan:</label>
                    <input type="file" class="form-control" name="sertifikat_peralatan_kerja">
                </div>
            </div>

            <!-- 10. PENGELOLAAN MATERIAL B3 -->
            <div class="form-section">
                <div class="section-header">
                    <h4 class="mb-0">10. PENGELOLAAN MATERIAL B3</h4>
                </div>
                <div class="mb-3 section-body">
                    <!-- 10.A -->
                    <label>Apakah perusahaan saudara memiliki prosedur penanganan, pengangkutan, dan penyimpanan bahan berbahaya dan beracun (B3)?</label>
                    <div>
                        <div class="form-check form-check-inline">
                            <input class="form-check-input" type="radio" name="b3A" value="Ya" id="b3A-ya"
                                <?php echo (isset($result['b3A']) && $result['b3A'] === 'Ya') ? 'checked' : ''; ?> />
                            <label class="form-check-label" for="b3A-ya">Ya</label>
                        </div>
                        <div class="form-check form-check-inline">
                            <input class="form-check-input" type="radio" name="b3A" value="Tidak" id="b3A-tidak"
                                <?php echo (isset($result['b3A']) && $result['b3A'] === 'Tidak') ? 'checked' : ''; ?> />
                            <label class="form-check-label" for="b3A-tidak">Tidak</label>
                        </div>
                    </div>
                    <label class="form-label mt-2">Jika ya, lampirkan prosedur tersebut:</label>
                    <input type="file" class="form-control" name="prosedur_material_b3">
                </div>
            </div>


            <!-- 1. HIGIENE INDUSTRI -->
            <h3 class="text-primary border-bottom border-primary pb-2">
                <i class="fas fa-tools"></i> 2. FAKTOR PENDUKUNG
            </h3>
            <div class="form-section">
                <div class="section-header">
                    <h4 class="mb-0">1. HIGIENE INDUSTRI</h4>
                </div>
                <div class="mb-3 section-body">
                    <label>Apakah Perusahaan Saudara melakukan pemeriksaan kesehatan terhadap calon pekerja serta melakukan pemeriksaan berkala?</label>
                    <div>
                        <div class="form-check form-check-inline">
                            <input class="form-check-input" type="radio" name="higieneA" value="Ya" id="higieneA-ya"
                                <?php echo (isset($result['higieneA']) && $result['higieneA'] === 'Ya') ? 'checked' : ''; ?> />
                            <label class="form-check-label" for="higieneA-ya">Ya</label>
                        </div>
                        <div class="form-check form-check-inline">
                            <input class="form-check-input" type="radio" name="higieneA" value="Tidak" id="higieneA-tidak"
                                <?php echo (isset($result['higieneA']) && $result['higieneA'] === 'Tidak') ? 'checked' : ''; ?> />
                            <label class="form-check-label" for="higieneA-tidak">Tidak</label>
                        </div>
                    </div>
                    <label class="form-label mt-2">Jika ya, lampirkan bukti pemeriksaan:</label>
                    <input type="file" class="form-control" name="bukti_pemeriksaan_kesehatan">
                </div>

                <!-- 1.B -->
                <div class="mb-3 section-body">
                    <label>Apakah perusahaan saudara melakukan pemantauan kesehatan tenaga kerja yang bekerja di lokasi yang mengandung bahaya dan risiko kesehatan?</label>
                    <div>
                        <div class="form-check form-check-inline">
                            <input class="form-check-input" type="radio" name="higieneB" value="Ya" id="higieneB-ya"
                                <?php echo (isset($result['higieneB']) && $result['higieneB'] === 'Ya') ? 'checked' : ''; ?> />
                            <label class="form-check-label" for="higieneB-ya">Ya</label>
                        </div>
                        <div class="form-check form-check-inline">
                            <input class="form-check-input" type="radio" name="higieneB" value="Tidak" id="higieneB-tidak"
                                <?php echo (isset($result['higieneB']) && $result['higieneB'] === 'Tidak') ? 'checked' : ''; ?> />
                            <label class="form-check-label" for="higieneB-tidak">Tidak</label>
                        </div>
                    </div>
                    <label class="form-label mt-2">Jika ya, lampirkan bukti pemantauan:</label>
                    <input type="file" class="form-control" name="bukti_pemantauan_kesehatan">
                </div>

                <!-- 1.C -->
                <div class="mb-3 section-body">
                    <label>Apakah ada program monitoring dan pengendalian bahaya kesehatan di tempat kerja?</label>
                    <div>
                        <div class="form-check form-check-inline">
                            <input class="form-check-input" type="radio" name="higieneC" value="Ya" id="higieneC-ya"
                                <?php echo (isset($result['higieneC']) && $result['higieneC'] === 'Ya') ? 'checked' : ''; ?> />
                            <label class="form-check-label" for="higieneC-ya">Ya</label>
                        </div>
                        <div class="form-check form-check-inline">
                            <input class="form-check-input" type="radio" name="higieneC" value="Tidak" id="higieneC-tidak"
                                <?php echo (isset($result['higieneC']) && $result['higieneC'] === 'Tidak') ? 'checked' : ''; ?> />
                            <label class="form-check-label" for="higieneC-tidak">Tidak</label>
                        </div>
                    </div>
                    <label class="form-label mt-2">Jika ya, lampirkan bukti program:</label>
                    <input type="file" class="form-control" name="bukti_program_pengendalian">
                </div>
                <!-- 1.D -->
                <div class="mb-3 section-body">
                    <label>Apakah perusahaan saudara memiliki prosedur/peraturan larangan pemakaian obat-obat terlarang & minuman keras?</label>
                    <div>
                        <div class="form-check form-check-inline">
                            <input class="form-check-input" type="radio" name="laranganObat" value="Ya" id="laranganObat-ya"
                                <?php echo (isset($result['laranganObat']) && $result['laranganObat'] === 'Ya') ? 'checked' : ''; ?> />
                            <label class="form-check-label" for="laranganObat-ya">Ya</label>
                        </div>
                        <div class="form-check form-check-inline">
                            <input class="form-check-input" type="radio" name="laranganObat" value="Tidak" id="laranganObat-tidak"
                                <?php echo (isset($result['laranganObat']) && $result['laranganObat'] === 'Tidak') ? 'checked' : ''; ?> />
                            <label class="form-check-label" for="laranganObat-tidak">Tidak</label>
                        </div>
                    </div>
                    <label class="form-label mt-2">Jika ya, lampirkan prosedur tersebut:</label>
                    <input type="file" class="form-control" name="bukti_larangan_obat">
                </div>
            </div>

            <!-- 2. PENGELOLAAN LINGKUNGAN KERJA -->
            <div class="form-section">
                <div class="section-header">
                    <h4 class="mb-0">2. PENGELOLAAN LINGKUNGAN KERJA</h4>
                </div>
                <div class="mb-3 section-body">
                    <label>Apakah perusahaan memiliki prosedur terkait pengelolaan lingkungan kerja (5S / 5R)?</label>
                    <div>
                        <div class="form-check form-check-inline">
                            <input class="form-check-input" type="radio" name="pengelolaan_lingkungan" value="Ya" id="pengelolaanLingkungan-ya"
                                <?php echo (isset($result['pengelolaan_lingkungan']) && $result['pengelolaan_lingkungan'] === 'Ya') ? 'checked' : ''; ?> />
                            <label class="form-check-label" for="pengelolaanLingkungan-ya">Ya</label>
                        </div>
                        <div class="form-check form-check-inline">
                            <input class="form-check-input" type="radio" name="pengelolaan_lingkungan" value="Tidak" id="pengelolaanLingkungan-tidak"
                                <?php echo (isset($result['pengelolaan_lingkungan']) && $result['pengelolaan_lingkungan'] === 'Tidak') ? 'checked' : ''; ?> />
                            <label class="form-check-label" for="pengelolaanLingkungan-tidak">Tidak</label>
                        </div>
                    </div>
                    <label class="form-label mt-2">Jika ya, lampirkan kebijakan tersebut:</label>
                    <input type="file" class="form-control" name="bukti_kebijakan_5s">
                </div>

                <!-- 2.B -->
                <div class="mb-3 section-body">
                    <label>Apakah perusahaan saudara melakukan pengukuran lingkungan kerja sesuai Permenaker No. 05 Tahun 2018?</label>
                    <div>
                        <div class="form-check form-check-inline">
                            <input class="form-check-input" type="radio" name="pengukuran_lingkungan" value="Ya" id="pengukuranLingkungan-ya"
                                <?php echo (isset($result['pengukuran_lingkungan']) && $result['pengukuran_lingkungan'] === 'Ya') ? 'checked' : ''; ?> />
                            <label class="form-check-label" for="pengukuranLingkungan-ya">Ya</label>
                        </div>
                        <div class="form-check form-check-inline">
                            <input class="form-check-input" type="radio" name="pengukuran_lingkungan" value="Tidak" id="pengukuranLingkungan-tidak"
                                <?php echo (isset($result['pengukuran_lingkungan']) && $result['pengukuran_lingkungan'] === 'Tidak') ? 'checked' : ''; ?> />
                            <label class="form-check-label" for="pengukuranLingkungan-tidak">Tidak</label>
                        </div>
                    </div>
                    <label class="form-label mt-2">Jika ya, lampirkan prosedur tersebut:</label>
                    <input type="file" class="form-control" name="bukti_pengukuran_lingkungan">
                </div>
            </div>

            <!-- 3. DATA KINERJA K3 -->
            <div class="form-section">
                <div class="section-header">
                    <h4 class="mb-0">3. DATA KINERJA K3</h4>
                </div>
                <div class="mb-3 section-body">
                    <label>Apakah Perusahaan saudara menyimpan catatan kinerja K3LH untuk 3 tahun terakhir?</label>
                    <div>
                        <div class="form-check form-check-inline">
                            <input class="form-check-input" type="radio" name="catatan_kinerja" value="Ya" id="catatanKinerja-ya"
                                <?php echo (isset($result['catatan_kinerja']) && $result['catatan_kinerja'] === 'Ya') ? 'checked' : ''; ?> />
                            <label class="form-check-label" for="catatanKinerja-ya">Ya</label>
                        </div>
                        <div class="form-check form-check-inline">
                            <input class="form-check-input" type="radio" name="catatan_kinerja" value="Tidak" id="catatanKinerja-tidak"
                                <?php echo (isset($result['catatan_kinerja']) && $result['catatan_kinerja'] === 'Tidak') ? 'checked' : ''; ?> />
                            <label class="form-check-label" for="catatanKinerja-tidak">Tidak</label>
                        </div>
                    </div>
                    <label class="form-label mt-2">Jika ya, lampirkan data kinerja K3 tersebut:</label>
                    <input type="file" class="form-control" name="bukti_kinerja_k3">
                </div>

                <!-- 3.B -->
                <div class="mb-3 section-body">
                    <label>Apakah dilakukan evaluasi terhadap sasaran dan program K3 tahunan?</label>
                    <div>
                        <div class="form-check form-check-inline">
                            <input class="form-check-input" type="radio" name="evaluasi_k3" value="Ya" id="evaluasiK3-ya"
                                <?php echo (isset($result['evaluasi_k3']) && $result['evaluasi_k3'] === 'Ya') ? 'checked' : ''; ?> />
                            <label class="form-check-label" for="evaluasiK3-ya">Ya</label>
                        </div>
                        <div class="form-check form-check-inline">
                            <input class="form-check-input" type="radio" name="evaluasi_k3" value="Tidak" id="evaluasiK3-tidak"
                                <?php echo (isset($result['evaluasi_k3']) && $result['evaluasi_k3'] === 'Tidak') ? 'checked' : ''; ?> />
                            <label class="form-check-label" for="evaluasiK3-tidak">Tidak</label>
                        </div>
                    </div>
                    <label class="form-label mt-2">Jika ya, lampirkan dokumen evaluasi tersebut:</label>
                    <input type="file" class="form-control" name="bukti_evaluasi_k3">
                </div>
            </div>

            <!-- 4. INVESTIGASI KECELAKAAN -->
            <div class="form-section">
                <div class="section-header">
                    <h4 class="mb-0">4. INVESTIGASI KECELAKAAN</h4>
                </div>
                <div class="mb-3 section-body">
                    <label>Apakah dilakukan investigasi pada setiap kecelakaan kerja?</label>
                    <div>
                        <div class="form-check form-check-inline">
                            <input class="form-check-input" type="radio" name="investigasi_kecelakaan" value="Ya" id="investigasiKecelakaan-ya"
                                <?php echo (isset($result['investigasi_kecelakaan']) && $result['investigasi_kecelakaan'] === 'Ya') ? 'checked' : ''; ?> />
                            <label class="form-check-label" for="investigasiKecelakaan-ya">Ya</label>
                        </div>
                        <div class="form-check form-check-inline">
                            <input class="form-check-input" type="radio" name="investigasiKecelakaan" value="Tidak" id="investigasiKecelakaan-tidak"
                                <?php echo (isset($result['investigasi_kecelakaan']) && $result['investigasi_kecelakaan'] === 'Tidak') ? 'checked' : ''; ?> />
                            <label class="form-check-label" for="investigasiKecelakaan-tidak">Tidak</label>
                        </div>
                    </div>
                    <label class="form-label mt-2">Jika ya, lampirkan laporan tersebut:</label>
                    <input type="file" class="form-control" name="bukti_investigasi_kecelakaan">
                </div>
                <!-- 4.B -->
                <div class="mb-3 section-body">
                    <label>Apakah hasil investigasi berisi saran dan ditindaklanjuti?</label>
                    <div>
                        <div class="form-check form-check-inline">
                            <input class="form-check-input" type="radio" name="saran_investigasi" value="Ya" id="saranInvestigasi-ya"
                                <?php echo (isset($result['saran_investigasi']) && $result['saran_investigasi'] === 'Ya') ? 'checked' : ''; ?> />
                            <label class="form-check-label" for="saranInvestigasi-ya">Ya</label>
                        </div>
                        <div class="form-check form-check-inline">
                            <input class="form-check-input" type="radio" name="saran_investigasi" value="Tidak" id="saranInvestigasi-tidak"
                                <?php echo (isset($result['saran_investigasi']) && $result['saran_investigasi'] === 'Tidak') ? 'checked' : ''; ?> />
                            <label class="form-check-label" for="saranInvestigasi-tidak">Tidak</label>
                        </div>
                    </div>
                    <label class="form-label mt-2">Jika ya, lampirkan prosedur tersebut:</label>
                    <input type="file" class="form-control" name="bukti_saran_investigasi">
                </div>
            </div>
            <?php
            if ($isExistingUser) {
            ?>
                <button type="submit" class="btn btn-warning mt-3 mb-3">Edit</button>
            <?php
            } else {
            ?>
                <button type="submit" class="btn btn-primary mt-3 mb-3">Submit</button>

            <?php
            }
            ?>
            <a href="../page/dashboard.php" class="btn btn-secondary">Kembali</a>
        </form>
    </div>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>

</html>