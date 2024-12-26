<?php
session_start();
// Menginclude file koneksi database
include '../includes/config.php';

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    // Ambil data direksi dari form
    //1
    $nama_perusahaan = $_POST['nama_perusahaan'];
    $alamat_pos = $_POST['alamat_pos'];
    $nomor_telepon_fax = $_POST['nomor_telepon_fax'];
    $email = $_POST['email'];
    $pekerjaan = $_POST['pekerjaan'];
    $user_id = $_SESSION['user_id'];


    //2
    $jabatan = $_POST['jabatan'];
    $nama = $_POST['nama'];
    $pendidikan = $_POST['pendidikan'];
    $masa_kerja = $_POST['masa_kerja'];

    //3
    $berdiri_tahun = $_POST['berdiri_tahun'] ?? null;
    $manajemen_sejak = $_POST['manajemen_sejak'] ?? null;
    $bentuk_usaha = $_POST['bentuk_usaha'] ?? null;
    $perusahaan_induk = $_POST['perusahaan_induk'] ?? null;
    $pos_induk = $_POST['pos_induk'] ?? null;
    $kota_induk = $_POST['kota_induk'] ?? null;
    $negara_induk = $_POST['negara_induk'] ?? null;
    $email_telepon_induk = $_POST['email_telepon_induk'] ?? null;
    $anak_perusahaan = $_POST['anak_perusahaan'] ?? null;
    $pos_anak = $_POST['pos_anak'] ?? null;
    $kota_anak = $_POST['kota_anak'] ?? null;
    $negara_anak = $_POST['negara_anak'] ?? null;
    $email_telepon_anak = $_POST['email_telepon_anak'] ?? null;
    $perusahaan_prinsipal = $_POST['perusahaan_prinsipal'] ?? null;
    $pos_prinsipal = $_POST['pos_prinsipal'] ?? null;
    $kota_prinsipal = $_POST['kota_prinsipal'] ?? null;
    $negara_prinsipal = $_POST['negara_prinsipal'] ?? null;
    $email_telepon_prinsipal = $_POST['email_telepon_prinsipal'] ?? null;

    //4
    $penanggung = $_POST['penanggung'] ?? null;
    $asuransi_pos = $_POST['asuransi_pos'] ?? null;
    $telepon_email_asuransi = $_POST['telepon_email_asuransi'] ?? null;
    $jenis_jaminan = $_POST['jenis_jaminan'] ?? null;

    //5
    $insurance_option = $_POST['insuranceOption'] ?? null;
    $insurance_reason = $_POST['alasan_asuransi_karyawan'] ?? null;

    //6
    $nama_perusahaan_riwayat = $_POST['nama_perusahaan_riwayat'];
    $jenis_pekerjaan = $_POST['jenis_pekerjaan'];
    $nilai_kontrak = $_POST['nilai_kontrak'];
    $telp_fax = $_POST['telp_fax'];
    $email_history = $_POST['email_history'];

    //7
    $legal_issue_option = $_POST['legal_issue_option'] ?? null;
    $alasan_pengadilan = $_POST['alasan_pengadilan'] ?? null;

    // Buat array dari data direksi
    $direksi = [];
    foreach ($jabatan as $index => $value) {
        $direksi[] = [
            'jabatan' => $jabatan[$index],
            'nama' => $nama[$index],
            'pendidikan' => $pendidikan[$index],
            'masa_kerja' => $masa_kerja[$index],
        ];
    }

    // Ubah array menjadi JSON
    $direksi_json = json_encode($direksi);

    // Buat array dari data work_history
    $work_history = [];
    foreach ($nama_perusahaan_riwayat as $index => $value) {
        $work_history[] = [
            'nama_perusahaan_riwayat' => $nama_perusahaan_riwayat[$index],
            'jenis_pekerjaan' => $jenis_pekerjaan[$index],
            'nilai_kontrak' => $nilai_kontrak[$index],
            'telp_fax' => $telp_fax[$index],
            'email_history' => $email_history[$index],
        ];
    }
    // Ubah array menjadi JSON
    $work_history_json = json_encode($work_history);

    try {
        // Cek apakah data sudah ada untuk user_id
        $sql_check = "SELECT COUNT(*) FROM profil_kontraktor WHERE user_id = :user_id";
        $stmt_check = $pdo->prepare($sql_check);
        $stmt_check->bindParam(':user_id', $user_id);
        $stmt_check->execute();
        $count = $stmt_check->fetchColumn();

        if ($count > 0) {
            // Jika data sudah ada, lakukan UPDATE
            $sql_update = "UPDATE profil_kontraktor 
                           SET nama_perusahaan = :nama_perusahaan, 
                               alamat_pos = :alamat_pos, 
                               nomor_telepon_fax = :nomor_telepon_fax, 
                               email = :email, 
                               pekerjaan = :pekerjaan, 
                               direksi = :direksi,
                               berdiri_tahun = :berdiri_tahun,
                               manajemen_sejak = :manajemen_sejak, 
                               bentuk_usaha = :bentuk_usaha, 
                               perusahaan_induk = :perusahaan_induk, 
                               pos_induk = :pos_induk, 
                               kota_induk = :kota_induk, 
                               negara_induk = :negara_induk, 
                               email_telepon_induk = :email_telepon_induk, 
                               anak_perusahaan = :anak_perusahaan, 
                               pos_anak = :pos_anak, 
                               kota_anak = :kota_anak, 
                               negara_anak = :negara_anak, 
                               email_telepon_anak = :email_telepon_anak, 
                               perusahaan_prinsipal = :perusahaan_prinsipal, 
                               pos_prinsipal = :pos_prinsipal, 
                               kota_prinsipal = :kota_prinsipal, 
                               negara_prinsipal = :negara_prinsipal, 
                               email_telepon_prinsipal = :email_telepon_prinsipal,
                               penanggung = :penanggung,
                               asuransi_pos = :asuransi_pos,
                               telepon_email_asuransi = :telepon_email_asuransi,
                               jenis_jaminan = :jenis_jaminan,
                               insurance_option = :insurance_option,
                               insurance_reason = :insurance_reason, 
                               work_history = :work_history,
                               legal_issue_option = :legal_issue_option,
                               alasan_pengadilan = :alasan_pengadilan
                           WHERE user_id = :user_id";
            $stmt = $pdo->prepare($sql_update);
        } else {
            // Jika data belum ada, lakukan INSERT
            $sql_insert = "INSERT INTO profil_kontraktor (
    user_id, 
    nama_perusahaan, 
    alamat_pos, 
    nomor_telepon_fax, 
    email, 
    pekerjaan, 
    direksi,
    berdiri_tahun, 
    manajemen_sejak, 
    bentuk_usaha, 
    perusahaan_induk, 
    pos_induk, 
    kota_induk, 
    negara_induk, 
    email_telepon_induk, 
    anak_perusahaan, 
    pos_anak, 
    kota_anak, 
    negara_anak, 
    email_telepon_anak, 
    perusahaan_prinsipal, 
    pos_prinsipal,
    kota_prinsipal, 
    negara_prinsipal, 
    email_telepon_prinsipal, 
    penanggung, 
    asuransi_pos, 
    telepon_email_asuransi, 
    jenis_jaminan, 
    insurance_option, 
    insurance_reason, 
    work_history, 
    legal_issue_option, 
    alasan_pengadilan
) VALUES (
    :user_id, 
    :nama_perusahaan, 
    :alamat_pos, 
    :nomor_telepon_fax, 
    :email, 
    :pekerjaan, 
    :direksi, 
    :berdiri_tahun, 
    :manajemen_sejak, 
    :bentuk_usaha, 
    :perusahaan_induk, 
    :pos_induk, 
    :kota_induk, 
    :negara_induk, 
    :email_telepon_induk, 
    :anak_perusahaan, 
    :pos_anak, 
    :kota_anak, 
    :negara_anak, 
    :email_telepon_anak, 
    :perusahaan_prinsipal, 
    :pos_prinsipal,
    :kota_prinsipal, 
    :negara_prinsipal, 
    :email_telepon_prinsipal, 
    :penanggung, 
    :asuransi_pos, 
    :telepon_email_asuransi, 
    :jenis_jaminan, 
    :insurance_option, 
    :insurance_reason, 
    :work_history, 
    :legal_issue_option, 
    :alasan_pengadilan
)";

            $stmt = $pdo->prepare($sql_insert);
        }

        // Binding parameter
        $stmt->bindParam(':user_id', $user_id);
        $stmt->bindParam(':nama_perusahaan', $nama_perusahaan);
        $stmt->bindParam(':alamat_pos', $alamat_pos);
        $stmt->bindParam(':nomor_telepon_fax', $nomor_telepon_fax);
        $stmt->bindParam(':email', $email);
        $stmt->bindParam(':pekerjaan', $pekerjaan);
        $stmt->bindParam(':direksi', $direksi_json);
        $stmt->bindParam(':berdiri_tahun', $berdiri_tahun);
        $stmt->bindParam(':manajemen_sejak', $manajemen_sejak);
        $stmt->bindParam(':bentuk_usaha', $bentuk_usaha);
        $stmt->bindParam(':perusahaan_induk', $perusahaan_induk);
        $stmt->bindParam(':pos_induk', $pos_induk);
        $stmt->bindParam(':kota_induk', $kota_induk);
        $stmt->bindParam(':negara_induk', $negara_induk);
        $stmt->bindParam(':email_telepon_induk', $email_telepon_induk);
        $stmt->bindParam(':anak_perusahaan', $anak_perusahaan);
        $stmt->bindParam(':pos_anak', $pos_anak);
        $stmt->bindParam(':kota_anak', $kota_anak);
        $stmt->bindParam(':negara_anak', $negara_anak);
        $stmt->bindParam(':email_telepon_anak', $email_telepon_anak);
        $stmt->bindParam(':perusahaan_prinsipal', $perusahaan_prinsipal);
        $stmt->bindParam(':pos_prinsipal', $pos_prinsipal);
        $stmt->bindParam(':kota_prinsipal', $kota_prinsipal);
        $stmt->bindParam(':negara_prinsipal', $negara_prinsipal);
        $stmt->bindParam(':email_telepon_prinsipal', $email_telepon_prinsipal);
        $stmt->bindParam(':penanggung', $penanggung);
        $stmt->bindParam(':asuransi_pos', $asuransi_pos);
        $stmt->bindParam(':telepon_email_asuransi', $telepon_email_asuransi);
        $stmt->bindParam(':jenis_jaminan', $jenis_jaminan);
        $stmt->bindParam(':insurance_option', $insurance_option);
        $stmt->bindParam(':insurance_reason', $insurance_reason);
        $stmt->bindParam(':work_history', $work_history_json);
        $stmt->bindParam(':legal_issue_option', $legal_issue_option);
        $stmt->bindParam(':alasan_pengadilan', $alasan_pengadilan);

        // Eksekusi query
        if ($stmt->execute()) {
            header('Location: ../page/datapengguna.php');
            exit;
        } else {
            echo "Terjadi kesalahan saat menyimpan data.";
        }
    } catch (Exception $e) {
        echo "Terjadi kesalahan: " . $e->getMessage();
    }
}
