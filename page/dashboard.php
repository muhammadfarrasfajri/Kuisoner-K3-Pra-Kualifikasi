<?php
session_start();
if (!isset($_SESSION['user'])) {
    header('Location: ../page/login.php');
    exit;
}

$user = $_SESSION['user'];
?>
<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Reusable Layout</title>
    <!-- Bootstrap CSS -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha1/dist/css/bootstrap.min.css" rel="stylesheet">
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
            width: 200px;
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
</head>

<body>
    <?php include '../layouts/header.php'; ?>
    <div class="container-fluid">
        <div class="row">
            <!-- Sidebar -->
            <nav class="sidebar" id="sidebar">
                <?php include '../layouts/sidebar.php'; ?>
            </nav>
            <!-- Konten Utama -->
            <main class="content" id="content">
                <h1>Dashboard</h1>
                <h1>Selamat datang, <?php echo htmlspecialchars($user['username']); ?>!</h1>
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
</body>

</html>