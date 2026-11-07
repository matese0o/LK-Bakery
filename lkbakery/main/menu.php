<?php
session_start();
require '../includes/db_connect.php';


$grouped_items = [];
$categories = [];
$category_order = []; 

try {

    $categories_stmt = $pdo->query("
        SELECT mc.id AS main_id, mc.name AS main_name, 
               sc.id AS sub_id, sc.name AS sub_name
        FROM main_categories mc
        LEFT JOIN sub_categories sc ON mc.id = sc.main_category_id
        ORDER BY mc.id, sc.id
    ");
    
    while ($row = $categories_stmt->fetch(PDO::FETCH_ASSOC)) {
        if (!isset($categories[$row['main_name']])) {
            $categories[$row['main_name']] = [];
            $category_order[] = ['type' => 'main', 'name' => $row['main_name'], 'id' => $row['main_id']];
        }
        if ($row['sub_name']) {
            $categories[$row['main_name']][] = $row['sub_name'];
            $category_order[] = ['type' => 'sub', 'name' => $row['sub_name'], 'id' => $row['sub_id'], 'main_id' => $row['main_id']];
        }
    }

    $stmt = $pdo->query("
        SELECT mi.*, mc.name AS main_category_name, sc.name AS sub_category_name,
               mc.id AS main_category_id, sc.id AS sub_category_id
        FROM menu_items mi
        LEFT JOIN main_categories mc ON mi.main_category_id = mc.id
        LEFT JOIN sub_categories sc ON mi.sub_category_id = sc.id
        WHERE mi.is_available = 1 
        ORDER BY mi.main_category_id, mi.sub_category_id, mi.id
    ");
    $menu_items = $stmt->fetchAll(PDO::FETCH_ASSOC);
    

    foreach ($menu_items as $item) {
        $grouped_items[$item['main_category_name']][$item['sub_category_name']][] = $item;
    }

} catch (PDOException $e) {
    error_log("Database error: " . $e->getMessage());
    $_SESSION['error'] = "Error loading menu. Please try again later.";
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>LK Bakery Menu</title>
    <link rel="stylesheet" href="../css/menu.css">
</head>
<body>
<header>
    <nav class="main-nav">
        <div class="nav-group logo-group">
            <div class="logo">
                <a href="../main/home.php">
                    <img src="../imgs/logo.png" alt="LK Bakery Logo">
                </a>
            </div>
            <div class="textlogo">
                <a href="../main/home.php">
                    <img src="../imgs/logotext.jpg" alt="LK Bakery Logo Text">
                </a>
            </div>
        </div>

        <div class="nav-group links-group">
            <ul class="nav-links">
                <li><a href="../main/menu.php" class="active">Menu</a></li>
                <li><a href="../main/contact.php">Contact</a></li>
                <li><a href="../main/about.php">About</a></li>
            </ul>
        </div>

        <div class="nav-group auth-group">
            <?php if(isset($_SESSION['user_id'])): ?>
                <div class="auth-container">
                    <?php if($_SESSION['role'] !== 'admin'): ?>
                        <span class="welcome-msg">Welcome, <?= htmlspecialchars($_SESSION['username']) ?></span>
                    <?php endif; ?>
                    <?php if($_SESSION['role'] === 'admin'): ?>
                        <a href="../admin/dashboard.php" class="admin-btn">Dashboard</a>
                    <?php endif; ?>
                    <a href="../auth/logout.php" class="auth-link">Logout</a>
                </div>
            <?php else: ?>
                <div class="auth-container">
                    <a href="../auth/login.php" class="auth-link">Login</a>
                    <a href="../auth/signup.php" class="auth-link">Sign Up</a>
                </div>
            <?php endif; ?>
        </div>
    </nav>
    
</header>

    <section class="eheader">
        <div class="banner">
            <img src="../imgs/menubanner.png" alt="Header Background" class="header-bg">
            <h1>Made Fresh Daily!</h1>
            <p>LK Bakery digital menu now up!</p>
            <button onclick="location.href='../main/menu.php'">Check out the flavors</button>
        </div>
    </section>
    <nav>
        <div class="menu-bar">
            <label for="search" class="menu-label">Menu</label>
            <div class="search-container">
                <input type="text" id="search" placeholder="Search..." onkeyup="filterMenu()">
                <i class="fas fa-search"></i>
            </div>
        </div>
    </nav>    
    
    <main>
        <aside>
        <?php foreach ($categories as $main_category => $sub_categories): ?>
            <div class="main-category <?= strtolower(str_replace(' ', '-', $main_category)) ?>-category">
                <a href="#<?= strtolower(str_replace(' ', '-', $main_category)) ?>">
                    <?= htmlspecialchars($main_category) ?>
                </a>
                <?php foreach ($sub_categories as $sub_category): ?>
                    <a href="#<?= strtolower(str_replace(' ', '-', $sub_category)) ?>">
                        <?= htmlspecialchars($sub_category) ?>
                    </a>
                <?php endforeach; ?>
            </div>
        <?php endforeach; ?>
    </aside>

        <section class="menu-items">
            <?php if (empty($grouped_items)): ?>
                <div class="empty-menu">
                    <p>Our menu is currently being updated. Please check back soon!</p>
                </div>
            <?php else: ?>
                <?php 

                foreach ($category_order as $cat): 
                    if ($cat['type'] == 'main' && isset($grouped_items[$cat['name']])):
                ?>
                    <h2 id="<?= strtolower(str_replace(' ', '-', $cat['name'])) ?>">
                        <?= htmlspecialchars($cat['name']) ?>
                    </h2>
                    <?php 

                        foreach ($category_order as $sub_cat): 
                            if ($sub_cat['type'] == 'sub' && $sub_cat['main_id'] == $cat['id'] && 
                                isset($grouped_items[$cat['name']][$sub_cat['name']])):
                    ?>
                        <h3 id="<?= strtolower(str_replace(' ', '-', $sub_cat['name'])) ?>">
                            <?= htmlspecialchars($sub_cat['name']) ?>
                        </h3>
                        <div class="grid">
                            <?php foreach ($grouped_items[$cat['name']][$sub_cat['name']] as $item): ?>
                                <div class="item">
                                    <div class="item-img-container">
                                        <?php if (!empty($item['image_path'])): ?>
                                            <img src="../imgs/<?= htmlspecialchars($item['image_path']) ?>" alt="<?= htmlspecialchars($item['item_name']) ?>">
                                        <?php endif; ?>
                                        <div class="item-hover-content">
                                            <p class="ingredient"><?= htmlspecialchars($item['description']) ?></p>
                                            <p class="price"><?= $item['price'] > 0 ? '₱' . number_format($item['price'], 2) : '-' ?></p>
                                        </div>
                                    </div>
                                    <div class="item-name"><?= htmlspecialchars($item['item_name']) ?></div>
                                </div>
                            <?php endforeach; ?>
                        </div>
                    <?php 
                            endif;
                        endforeach; 
                    endif;
                endforeach; 
                ?>
            <?php endif; ?>
        </section>
    </main>
    <footer>
        <div class="footer-container">
            <div class="footer-section">
                <h4><a href="../main/menu.php">Menu</a></h4>
                <ul>
                    <li><a href="#food">Food</a></li>
                    <li><a href="#drinks">Drinks</a></li>
                    <li><a href="#box-options">Box Options</a></li>
                </ul>
            </div>
            <div class="footer-section">
                <h4><a href="../main/about.php">About</a></h4>
                <ul>
                    <li><a href="#">Our Bakery</a></li>
                    <li><a href="#">History</a></li>
                    <li><a href="#">Stories and News</a></li>
                    <li><a href="#">Customer Service</a></li>
                </ul>
            </div>
            <div class="footer-section">
                <h4><a href="../main/contact.php">Contact</a></h4>
                <ul>
                    <li><a href="#">Order on the App</a></li>
                    <li><a href="#">My Account</a></li>
                    <li><a href="#">View Transactions</a></li>
                    <li><a href="#">Reviews</a></li>
                    <li><a href="#">Send eGifts</a></li>
                </ul>
            </div>
            <div class="footer-section">
                <h4><a href="#find-us">Find Us</a></h4>
            </div>
        </div>

        <hr class="footer-divider"> 

        <div class="footer-bottom">
            <div class="social-icons">
                <a href="#"><img src="../imgs/Facebook.png" alt="Facebook" class="social-icon"></a>
                <a href="#"><img src="../imgs/Instagram.png" alt="Instagram" class="social-icon"></a>
            </div>
            <div class="footer-links">
                <a href="#">Web Accessibility</a>
                <a href="#">Privacy Policy</a>
                <a href="#">Terms of Use</a>
                <a href="#">Site Map</a>
                <a href="#">Cookie Preferences</a>
            </div>
            <p>© 2025 Frelicki Mates Tangub Villaver. All rights reserved.</p>
        </div>
    </footer>

    <script>
        function filterMenu() {
            const input = document.getElementById('search');
            const filter = input.value.toUpperCase();
            const items = document.querySelectorAll('.item');
            
            items.forEach(item => {
                const text = item.textContent || item.innerText;
                if (text.toUpperCase().indexOf(filter) > -1) {
                    item.style.display = "";
                } else {
                    item.style.display = "none";
                }
            });
        }
    </script>
</body>
</html>