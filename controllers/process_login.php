<?php
require '../includes/config.php';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $username = $_POST['username'];
    $password = $_POST['password'];

    $query = "SELECT * FROM users WHERE username = :username";
    $stmt = $pdo->prepare($query);
    $stmt->execute([':username' => $username]);
    $user = $stmt->fetch(PDO::FETCH_ASSOC);

    if ($user && password_verify($password, $user['password'])) {
        session_start();
        $_SESSION['user'] = $user;
        $_SESSION['user_id'] = $user['id'];
        header('Location: ../page/dashboard.php');
        exit;
    } else {
        header('Location: ../page/login.php?error=Username atau password salah!');
        exit;
    }
} else {
    header('Location: ../page/login.php');
    exit;
}
