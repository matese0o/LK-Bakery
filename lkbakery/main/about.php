<?php 
session_start();
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>About</title>
    <link rel="stylesheet" href="../css/about.css">
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
                    <li><a href="../main/menu.php">Menu</a></li>
                    <li><a href="../main/contact.php">Contact</a></li>
                    <li><a href="../main/about.php" class="active">About</a></li>
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

      <main class="contact-main">
        <section class="contact-hero">
            <div class="hero-content">
                <h1>ABOUT US</h1>
                <p>Discover the passion behind every pastry</p>
            </div>
            
        </section>
    <section class="ourStory">
        <div>
            <h1>Our Story</h1>
            <img src="../imgs/group.jpg" alt="Our Story Image" class="story-img">
            <p>Founded on <a>March 31, 2024,</a>  LK Bakery was born from a love for high-quality, accessible pastries. Our goal is to bring people together through the simple joy of fresh, flaky croissants and comforting beverages. </p>
            <p>At LK Bakery, we believe that quality pastries should be accessible to everyone. Whether you're a food enthusiast, a casual pastry lover, or someone looking for the next big trend, our doors are open to serve you.</p>
        </div>
    </section>
    <section class="aboutOwner">
        <div>
            <h1>About the Owner</h1>
            <img src="../imgs/owner.jpg" alt="Owner Image" class="owner-img">
            <p>Meet Kim, the heart and soul behind LK Bakery. With years of experience in the baking industry, Kim brings a wealth of knowledge and passion to every creation.</p>
            <p>What started as a childhood fascination with baking grew into a lifelong dedication to perfecting the art of pastry-making. Kim believes in using only the finest ingredients and traditional techniques to create unforgettable flavors.</p>
            <p>"Baking isn't just my profession - it's my way of sharing joy with the community," says Kim. This philosophy is evident in every product that comes out of our ovens.</p>
        </div>
    </section>
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
</body>
</html>
