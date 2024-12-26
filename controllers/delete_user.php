<?php
include '../includes/config.php'; // Pastikan path benar

if (isset($_GET['id'])) {
    $id = $_GET['id'];

    try {
        $sql = "DELETE FROM users WHERE id = :id";
        $stmt = $pdo->prepare($sql);
        $stmt->execute([':id' => $id]);

        // Redirect ke halaman daftar pengguna
        header('Location: ../page/manage_users.php');
        exit;
    } catch (PDOException $e) {
        die("Error: " . $e->getMessage());
    }
} else {
    die("ID pengguna tidak ditemukan.");
}
?>
