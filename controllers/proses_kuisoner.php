<?php
session_start();
// Menginclude file koneksi database
include '../includes/config.php';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    try {
        $inputFields = [
            'komitmenA',
            'komitmenB',
            'organisasiA',
            'person_bertanggung_jawab',
            'inspeksiA',
            'inspeksiB',
            'rapatA',
            'rapatB',
            'daruratA', // Tambahkan field daruratA
            'prosedurK3A', // Tambahkan field prosedurK3A
            'prosedurK3B', // Tambahkan field prosedurK3B
            'kecelakaanA',
            'kecelakaanB',
            'pelatihanA',
            'pelatihanB',
            'pelatihanC',
            'apdA',
            'apdB',
            'apdC',
            'b3A',
            //
            'higieneA',
            'higieneB',
            'higieneC',
            'laranganObat',
            'pengelolaan_lingkungan',
            'pengukuran_lingkungan',
            'catatan_kinerja',
            'evaluasi_k3',
            'investigasi_kecelakaan',
            'saran_investigasi'


        ];
        $data = [];
        foreach ($inputFields as $field) {
            $data[$field] = $_POST[$field] ?? null;
        }

        $user_id = $_SESSION['user_id'];

        // Validasi User ID
        $sqlCheckUser = "SELECT COUNT(*) FROM users WHERE id = :user_id";
        $stmtCheckUser = $pdo->prepare($sqlCheckUser);
        $stmtCheckUser->execute([':user_id' => $user_id]);
        $isUserValid = $stmtCheckUser->fetchColumn() > 0;

        if (!$isUserValid) {
            throw new Exception("User ID tidak valid. Pastikan User ID ada di tabel users.");
        }

        // Fungsi upload file
        function uploadFile($fileKey, $uploadDir)
        {
            if (isset($_FILES[$fileKey]) && $_FILES[$fileKey]['error'] === 0) {
                $fileName = uniqid() . '-' . basename($_FILES[$fileKey]['name']);
                $filePath = $uploadDir . $fileName;
                if (!move_uploaded_file($_FILES[$fileKey]['tmp_name'], $filePath)) {
                    throw new Exception("Gagal mengunggah file: $fileKey.");
                }
                return 'uploads/' . $fileName;
            }
            return null;
        }

        $uploadDir = __DIR__ . '/../uploads/';
        if (!is_dir($uploadDir)) mkdir($uploadDir, 0777, true);

        $filePaths = [
            'kebijakan_k3_path' => uploadFile('kebijakan_k3', $uploadDir),
            'sosialisasi_absen_path' => uploadFile('sosialisasi_absen', $uploadDir),
            'job_description_path' => uploadFile('job_description', $uploadDir),
            'program_inspeksi_path' => uploadFile('program_inspeksi', $uploadDir),
            'bukti_tindak_lanjut_inspeksi_path' => uploadFile('bukti_tindak_lanjut_inspeksi', $uploadDir),
            'bukti_rapat_path' => uploadFile('bukti_rapat', $uploadDir),
            'bukti_tindak_lanjut_rapat_path' => uploadFile('bukti_tindak_lanjut_rapat', $uploadDir),
            'bukti_darurat_path' => uploadFile('bukti_darurat', $uploadDir), // Untuk bukti darurat
            'panduan_k3_path' => uploadFile('panduan_k3', $uploadDir), // Untuk bukti panduan K3
            'daftar_buku_k3_path' => uploadFile('daftar_buku_k3', $uploadDir), // Untuk bukti daftar buku K3
            'prosedur_kecelakaan_path' => uploadFile('prosedur_kecelakaan', $uploadDir),
            'prosedur_bahaya_path' => uploadFile('prosedur_bahaya', $uploadDir),
            'program_pelatihan_path' => uploadFile('program_pelatihan', $uploadDir),
            'jadwal_pelatihan_path' => uploadFile('jadwal_pelatihan', $uploadDir),
            'sertifikat_ahli_k3_path' => uploadFile('sertifikat_ahli_k3', $uploadDir),
            'jenis_apd_path' => uploadFile('jenis_apd', $uploadDir),
            'prosedur_pemeliharaan_apd_path' => uploadFile('prosedur_pemeliharaan_apd', $uploadDir),
            'sertifikat_peralatan_kerja_path' => uploadFile('sertifikat_peralatan_kerja', $uploadDir),
            'prosedur_material_b3_path' => uploadFile('prosedur_material_b3', $uploadDir),
            //
            'bukti_pemeriksaan_kesehatan_path' => uploadFile('bukti_pemeriksaan_kesehatan', $uploadDir),
            'bukti_pemantauan_kesehatan_path' => uploadFile('bukti_pemantauan_kesehatan', $uploadDir),
            'bukti_program_pengendalian_path' => uploadFile('bukti_program_pengendalian', $uploadDir),
            'bukti_larangan_obat_path' => uploadFile('bukti_larangan_obat', $uploadDir),
            'bukti_kebijakan_5s_path' => uploadFile('bukti_kebijakan_5s', $uploadDir),
            'bukti_pengukuran_lingkungan_path' => uploadFile('bukti_pengukuran_lingkungan', $uploadDir),
            'bukti_kinerja_k3_path' => uploadFile('bukti_kinerja_k3', $uploadDir),
            'bukti_evaluasi_k3_path' => uploadFile('bukti_evaluasi_k3', $uploadDir),
            'bukti_investigasi_kecelakaan_path' => uploadFile('bukti_investigasi_kecelakaan', $uploadDir),
            'bukti_saran_investigasi_path' => uploadFile('bukti_saran_investigasi', $uploadDir)
        ];

        // Periksa keberadaan user_id di tabel kuisoner
        $sqlCheck = "SELECT COUNT(*) FROM kuisoner WHERE user_id = :user_id";
        $stmtCheck = $pdo->prepare($sqlCheck);
        $stmtCheck->execute([':user_id' => $user_id]);
        $isExist = $stmtCheck->fetchColumn() > 0;

        $sql = $isExist
            ? "UPDATE kuisoner
                  SET komitmenA = :komitmenA,
                  komitmenB = :komitmenB,
                  organisasiA = :organisasiA,
                  person_bertanggung_jawab = :person_bertanggung_jawab,
                  inspeksiA = :inspeksiA,
                  inspeksiB = :inspeksiB,
                  program_inspeksi_path = :program_inspeksi_path,
                  bukti_tindak_lanjut_inspeksi_path = :bukti_tindak_lanjut_inspeksi_path,
                  rapatA = :rapatA,
                  rapatB = :rapatB,
                  bukti_rapat_path = :bukti_rapat_path,
                  bukti_tindak_lanjut_rapat_path = :bukti_tindak_lanjut_rapat_path,
                  kebijakan_k3_path = :kebijakan_k3_path,
                  sosialisasi_absen_path = :sosialisasi_absen_path,
                  job_description_path = :job_description_path,
                  daruratA = :daruratA,
                  prosedurK3A = :prosedurK3A,
                  prosedurK3B = :prosedurK3B,
                  bukti_darurat_path = :bukti_darurat_path,
                  panduan_k3_path = :panduan_k3_path,
                  daftar_buku_k3_path = :daftar_buku_k3_path,
                  kecelakaanA = :kecelakaanA,
                  prosedur_kecelakaan_path = :prosedur_kecelakaan_path,
                  kecelakaanB = :kecelakaanB,
                  prosedur_bahaya_path = :prosedur_bahaya_path,
                  pelatihanA = :pelatihanA,
                  pelatihanB = :pelatihanB,
                  pelatihanC = :pelatihanC,
                  program_pelatihan_path = :program_pelatihan_path,
                  jadwal_pelatihan_path = :jadwal_pelatihan_path,
                  sertifikat_ahli_k3_path = :sertifikat_ahli_k3_path,
                  apdA = :apdA,
                  apdB = :apdB,
                  apdC = :apdC,
                  jenis_apd_path = :jenis_apd_path,
                  prosedur_pemeliharaan_apd_path = :prosedur_pemeliharaan_apd_path,
                  sertifikat_peralatan_kerja_path = :sertifikat_peralatan_kerja_path,
                  b3A = :b3A,
                  prosedur_material_b3_path = :prosedur_material_b3_path,
                  higieneA = :higieneA,
                  higieneB = :higieneB,
                  higieneC = :higieneC,
                  laranganObat = :laranganObat,
                  bukti_pemeriksaan_kesehatan_path = :bukti_pemeriksaan_kesehatan_path,
                  bukti_pemantauan_kesehatan_path = :bukti_pemantauan_kesehatan_path,
                  bukti_program_pengendalian_path = :bukti_program_pengendalian_path,
                  bukti_larangan_obat_path = :bukti_larangan_obat_path,
                  pengelolaan_lingkungan = :pengelolaan_lingkungan,
                  pengukuran_lingkungan = :pengukuran_lingkungan,
                  bukti_kebijakan_5s_path = :bukti_kebijakan_5s_path,
                  bukti_pengukuran_lingkungan_path = :bukti_pengukuran_lingkungan_path,
                  catatan_kinerja = :catatan_kinerja,
                  evaluasi_k3 = :evaluasi_k3,
                  investigasi_kecelakaan = :investigasi_kecelakaan,
                  saran_investigasi = :saran_investigasi,
                  bukti_kinerja_k3_path = :bukti_kinerja_k3_path,
                  bukti_evaluasi_k3_path = :bukti_evaluasi_k3_path,
                  bukti_investigasi_kecelakaan_path = :bukti_investigasi_kecelakaan_path,
                  bukti_saran_investigasi_path = :bukti_saran_investigasi_path
                  WHERE user_id = :user_id;
                  "
            : "INSERT INTO kuisoner (
    user_id,
    komitmenA,
    komitmenB,
    organisasiA,
    person_bertanggung_jawab,
    inspeksiA,
    inspeksiB,
    program_inspeksi_path,
    bukti_tindak_lanjut_inspeksi_path,
    rapatA,
    rapatB,
    bukti_rapat_path,
    bukti_tindak_lanjut_rapat_path,
    kebijakan_k3_path,
    sosialisasi_absen_path,
    job_description_path,
    daruratA,
    prosedurK3A,
    prosedurK3B,
    bukti_darurat_path,
    panduan_k3_path,
    daftar_buku_k3_path,
    kecelakaanA,
    prosedur_kecelakaan_path,
    kecelakaanB,
    prosedur_bahaya_path,
    pelatihanA,
    pelatihanB,
    pelatihanC,
    program_pelatihan_path,
    jadwal_pelatihan_path,
    sertifikat_ahli_k3_path,
    apdA,
    apdB,
    apdC,
    jenis_apd_path,
    prosedur_pemeliharaan_apd_path,
    sertifikat_peralatan_kerja_path,
    b3A,
    prosedur_material_b3_path,
    higieneA,
    higieneB,
    higieneC,
    laranganObat,
    bukti_pemeriksaan_kesehatan_path,
    bukti_pemantauan_kesehatan_path,
    bukti_program_pengendalian_path,
    bukti_larangan_obat_path,
    pengelolaan_lingkungan,
    pengukuran_lingkungan,
    bukti_kebijakan_5s_path,
    bukti_pengukuran_lingkungan_path,
    catatan_kinerja,
    evaluasi_k3,
    investigasi_kecelakaan,
    saran_investigasi,
    bukti_kinerja_k3_path,
    bukti_evaluasi_k3_path,
    bukti_investigasi_kecelakaan_path,
    bukti_saran_investigasi_path

) VALUES (
    :user_id,
    :komitmenA,
    :komitmenB,
    :organisasiA,
    :person_bertanggung_jawab,
    :inspeksiA,
    :inspeksiB,
    :program_inspeksi_path,
    :bukti_tindak_lanjut_inspeksi_path,
    :rapatA,
    :rapatB,
    :bukti_rapat_path,
    :bukti_tindak_lanjut_rapat_path,
    :kebijakan_k3_path,
    :sosialisasi_absen_path,
    :job_description_path,
    :daruratA,
    :prosedurK3A,
    :prosedurK3B,
    :bukti_darurat_path,
    :panduan_k3_path,
    :daftar_buku_k3_path,
    :kecelakaanA,
    :prosedur_kecelakaan_path,
    :kecelakaanB,
    :prosedur_bahaya_path,
    :pelatihanA,
    :pelatihanB,
    :pelatihanC,
    :program_pelatihan_path,
    :jadwal_pelatihan_path,
    :sertifikat_ahli_k3_path,
    :apdA,
    :apdB,
    :apdC,
    :jenis_apd_path,
    :prosedur_pemeliharaan_apd_path,
    :sertifikat_peralatan_kerja_path,
    :b3A,
    :prosedur_material_b3_path,
    :higieneA,
    :higieneB,
    :higieneC,
    :laranganObat,
    :bukti_pemeriksaan_kesehatan_path,
    :bukti_pemantauan_kesehatan_path,
    :bukti_program_pengendalian_path,
    :bukti_larangan_obat_path,
    :pengelolaan_lingkungan,
    :pengukuran_lingkungan,
    :bukti_kebijakan_5s_path,
    :bukti_pengukuran_lingkungan_path,
    :catatan_kinerja,
    :evaluasi_k3,
    :investigasi_kecelakaan,
    :saran_investigasi,
    :bukti_kinerja_k3_path,
    :bukti_evaluasi_k3_path,
    :bukti_investigasi_kecelakaan_path,
    :bukti_saran_investigasi_path
);
";
        $stmt = $pdo->prepare($sql);
        $stmt->execute(array_merge(
            [
                ':user_id' => $user_id,
                ':komitmenA' => $data['komitmenA'],
                ':komitmenB' => $data['komitmenB'],
                ':organisasiA' => $data['organisasiA'],
                ':person_bertanggung_jawab' => $data['person_bertanggung_jawab'],
                ':inspeksiA' => $data['inspeksiA'],
                ':inspeksiB' => $data['inspeksiB'],
                ':rapatA' => $data['rapatA'],
                ':rapatB' => $data['rapatB'],
                ':daruratA' => $data['daruratA'],
                ':prosedurK3A' => $data['prosedurK3A'],
                ':prosedurK3B' => $data['prosedurK3B'],
                ':kecelakaanA' => $data['kecelakaanA'],
                ':kecelakaanB' => $data['kecelakaanB'],
                ':pelatihanA' => $data['pelatihanA'],
                ':pelatihanB' => $data['pelatihanB'],
                ':pelatihanC' => $data['pelatihanC'],
                ':apdA' => $data['apdA'],
                ':apdB' => $data['apdB'],
                ':apdC' => $data['apdC'],
                ':b3A' => $data['b3A'],
                ':higieneA' => $data['higieneA'],
                ':higieneB' => $data['higieneB'],
                ':higieneC' => $data['higieneC'],
                ':laranganObat' => $data['laranganObat'],
                ':pengelolaan_lingkungan' => $data['pengelolaan_lingkungan'],
                ':pengukuran_lingkungan' => $data['pengukuran_lingkungan'],
                ':catatan_kinerja' => $data['catatan_kinerja'],
                ':evaluasi_k3' => $data['evaluasi_k3'],
                ':investigasi_kecelakaan' => $data['investigasi_kecelakaan'],
                ':saran_investigasi' => $data['saran_investigasi']
            ],
            [
                ':program_inspeksi_path' => $filePaths['program_inspeksi_path'],
                ':bukti_tindak_lanjut_inspeksi_path' => $filePaths['bukti_tindak_lanjut_inspeksi_path'],
                ':bukti_rapat_path' => $filePaths['bukti_rapat_path'],
                ':bukti_tindak_lanjut_rapat_path' => $filePaths['bukti_tindak_lanjut_rapat_path'],
                ':kebijakan_k3_path' => $filePaths['kebijakan_k3_path'],
                ':sosialisasi_absen_path' => $filePaths['sosialisasi_absen_path'],
                ':job_description_path' => $filePaths['job_description_path'],
                ':bukti_darurat_path' => $filePaths['bukti_darurat_path'],
                ':panduan_k3_path' => $filePaths['panduan_k3_path'],
                ':daftar_buku_k3_path' => $filePaths['daftar_buku_k3_path'],
                ':prosedur_kecelakaan_path' => $filePaths['prosedur_kecelakaan_path'],
                ':prosedur_bahaya_path' => $filePaths['prosedur_bahaya_path'],
                ':program_pelatihan_path' => $filePaths['program_pelatihan_path'],
                ':jadwal_pelatihan_path' => $filePaths['jadwal_pelatihan_path'],
                ':sertifikat_ahli_k3_path' => $filePaths['sertifikat_ahli_k3_path'],
                ':jenis_apd_path' => $filePaths['jenis_apd_path'],
                ':prosedur_pemeliharaan_apd_path' => $filePaths['prosedur_pemeliharaan_apd_path'],
                ':sertifikat_peralatan_kerja_path' => $filePaths['sertifikat_peralatan_kerja_path'],
                ':prosedur_material_b3_path' => $filePaths['prosedur_material_b3_path'],
                ':bukti_pemeriksaan_kesehatan_path' => $filePaths['bukti_pemeriksaan_kesehatan_path'],
                ':bukti_pemantauan_kesehatan_path' => $filePaths['bukti_pemantauan_kesehatan_path'],
                ':bukti_program_pengendalian_path' => $filePaths['bukti_program_pengendalian_path'],
                ':bukti_larangan_obat_path' => $filePaths['bukti_larangan_obat_path'],
                ':bukti_kebijakan_5s_path' => $filePaths['bukti_kebijakan_5s_path'],
                ':bukti_pengukuran_lingkungan_path' => $filePaths['bukti_pengukuran_lingkungan_path'],
                ':bukti_kinerja_k3_path' => $filePaths['bukti_kinerja_k3_path'],
                ':bukti_evaluasi_k3_path' => $filePaths['bukti_evaluasi_k3_path'],
                ':bukti_investigasi_kecelakaan_path' => $filePaths['bukti_investigasi_kecelakaan_path'],
                ':bukti_saran_investigasi_path' => $filePaths['bukti_saran_investigasi_path']
            ]
        ));
        header('Location: ../page/kuisoner.php');
        echo "Data berhasil disimpan!";
    } catch (Exception $e) {
        echo "Terjadi kesalahan: " . $e->getMessage();
    }
}
