<?php
require '../includes/config.php';
$user = $_SESSION['user'];
?>
<nav class="navbar navbar-expand-lg navbar-dark bg-primary">
    <div class="container-fluid d-flex align-items-center">
        <!-- Sidebar Toggle Button -->
        <img src="/MAGANG/assets/img/logopusri.png" class="m-4" alt="" style="width: 100px; height: auto;">
        <button class="btn btn-light" id="sidebarToggle">☰</button>
        <!-- Navbar Links (Logout and Add User) -->
        <div class="ms-auto dropdown">
            <button class="btn btn-primary dropdown-toggle me-2" type="button" id="dropdownMenuButton" data-bs-toggle="dropdown" aria-expanded="false">
                <?php echo htmlspecialchars($user['username']); ?>
            </button>
            <ul class="dropdown-menu dropdown-menu-end" aria-labelledby="dropdownMenuButton" style="transform: translateX(-20px);">
                <li>
                    <a href="../controllers/logout.php" class="dropdown-item">Logout</a>
            </ul>
        </div>
    </div>
</nav>