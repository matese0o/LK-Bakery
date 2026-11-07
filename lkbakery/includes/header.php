<?php
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}
?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?= isset($page_title) ? htmlspecialchars($page_title) : 'Admin Panel' ?></title>
    <link rel="stylesheet" href="../css/admin.css">
</head>
<body>
    <<header>
    <nav class="main-nav">
        <div class="nav-group logo-group">
            <div class="logo">
                <a href="dashboard.php">
                    <img src="../imgs/logodash.png" alt="LK Bakery Logo">
                </a>
            </div>
            <div class="textlogo">
                <a href="dashboard.php">
                    <img src="../imgs/logotextdash.png" alt="Admin Panel">
                </a>
            </div>
        </div>

        <div class="nav-group links-group">
            <ul class="nav-links">
                <li><a href="manage_menu.php">Menu</a></li>
                <li><a href="manage_users.php">Users</a></li>
            </ul>
        </div>

        <div class="nav-group auth-group">
            <div class="auth-container">
                <?php if(isset($_SESSION['username'])): ?>
                    <?php if($_SESSION['role'] === 'admin'): ?>
                        <a href="../main/home.php" class="admin-btn">Main</a>
                    <?php endif; ?>
                    <a href="../auth/logout.php" class="auth-link">Logout</a>
                <?php else: ?>
                    <a href="../auth/login.php" class="auth-link">Login</a>
                <?php endif; ?>
                
            </div>
        </div>
    </nav>
</header>

    <main class="admin-main">