<?php
require '../includes/config.php';
session_start();
$user_id = $_SESSION['user_id'] ?? null; // Pastikan user_id tersedia
if (!$user_id) {
    echo "Akses ditolak. Anda harus login.";
    exit;
}
$query = "SELECT sertifikat FROM profil_kontraktor WHERE user_id = :user_id";
$stmt = $pdo->prepare($query);
$stmt->execute(['user_id' => $user_id]);
$data = $stmt->fetch(PDO::FETCH_ASSOC);

$sertifikatPath = $data['sertifikat'] ?? null;
?>
<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Document</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons/font/bootstrap-icons.css" rel="stylesheet">
</head>

<body>
    <div class="container mt-4">
        <div class="card shadow-sm">
            <div class="card-body">
                <?php if ($sertifikatPath): ?>
                    <div class="text-center">
                        <h1>Unduh Sertifikat anda</h1>
                        <a href="../download_sertif.php?file=<?php echo urlencode($sertifikatPath); ?>" class="btn btn-primary btn-lg">
                            <i class="bi bi-download"></i> Unduh Sertifikat
                        </a>
                    </div>
                <?php else: ?>
                    <div class="alert alert-warning text-center" role="alert">
                        Sertifikat belum tersedia untuk pengguna ini.
                    </div>
                <?php endif; ?>
            </div>
        </div>
    </div>
    <div>
        <div class="text-center mt-4">
            <a href="../page/dashboard.php" class="btn btn-secondary">Kembali</a>
        </div>
    </div>
</body>
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>

</html>