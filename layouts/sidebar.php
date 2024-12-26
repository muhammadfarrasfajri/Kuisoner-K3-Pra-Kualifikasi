<nav class="sidebar bg-dark" id="sidebar">
    <div class="d-flex flex-column p-2 text-white" style="width: 200px; height: 100vh;">
        <!-- Sidebar Menu -->
        <ul class="nav flex-column">
            <li class="nav-item">
                <a class="nav-link text-white active" href="../page/dashboard.php">
                    <i class="bi bi-house-door"></i> Dashboard
                </a>
            </li>
            <li class="nav-item">
                <?php if ($user['role'] === 'user'): ?>
                    <a class="nav-link text-white" href="../page/datapengguna.php">
                        <i class="bi bi-box"></i> Data Pengguna
                    </a>
                <?php endif; ?>
            </li>
            <li class="nav-item">
                <?php if ($user['role'] === 'user'): ?>
                    <a class="nav-link text-white" href="../page/kuisoner.php">
                        <i class="bi bi-bag"></i> Kuisoner
                    </a>
                <?php endif; ?>
            </li>
            <li class="nav-item">
                <?php if ($user['role'] === 'user'): ?>
                    <a class="nav-link text-white" href="../page/sertifikat.php">
                        <i class="bi bi-bag"></i> Sertifikat
                    </a>
                <?php endif; ?>
            </li>
            <li class="nav-item">
                <?php if ($user['role'] === 'admin'): ?>
                    <a class="nav-link text-white" href="../page/respon_kuisoner.php">
                        <i class="bi bi-person"></i>Respon Kuisoner
                    </a>
                <?php endif; ?>
            </li>
            <li class="nav-item">
                <?php if ($user['role'] === 'admin'): ?>
                    <a class="nav-link text-white" href="../page/manage_users.php">
                        <i class="bi bi-person"></i>Manage Users
                    </a>
                <?php endif; ?>
            </li>
        </ul>
    </div>
</nav>