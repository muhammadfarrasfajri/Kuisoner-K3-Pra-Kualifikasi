<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Login CSMS</title>

    <!-- Link ke Bootstrap CSS -->
    <link href="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css" rel="stylesheet">
</head>

<body class="bg-light">
    <div class="container d-flex justify-content-center align-items-center" style="min-height: 100vh;">
        <!-- Logo -->
        <div class="card shadow-lg p-4 d-flex flex-column align-items-center" style="width: 100%; max-width: 400px;">
            <img src="/MAGANG/assets/img/logo.png" alt="" style="width: 100px; height: auto;">
            <h2 class="text-center mb-4">Login</h2>
            <!-- Menampilkan error jika ada -->
            <?php
            if (isset($_GET['error'])) {
                echo "<div class='alert alert-danger' role='alert'>" . htmlspecialchars($_GET['error']) . "</div>";
            }
            ?>
            <form method="POST" action="../controllers/process_login.php">
                <div class="mb-3">
                    <label for="username" class="form-label">Username</label>
                    <input type="text" class="form-control" id="username" name="username" required>
                </div>
                <div class="mb-3">
                    <label for="password" class="form-label">Password</label>
                    <input type="password" class="form-control" id="password" name="password" required>
                </div>
                <div class="text-center">
                    <button type="submit" class="btn btn-primary w-100">Login</button>
                </div>
            </form>
        </div>

    </div>

    </div>

    <!-- Link ke Bootstrap JS -->
    <script src="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/js/bootstrap.bundle.min.js"></script>
</body>

</html>