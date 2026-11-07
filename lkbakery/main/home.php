<?php
session_start();
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Home - LK Bakery</title>
    <link rel="stylesheet" href="../css/home.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
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

    <main>
        <!-- Hero Banner -->
        <section class="hero-banner">
            <div class="banner-content">
                <h1>Freshly Baked Happiness</h1>
                <p>Start your day with our artisan pastries and handcrafted drinks</p>
                <a href="../main/menu.php" class="cta-button">Explore Menu</a>
            </div>
    
        </section>
        <section class="featured-section">
            <div class="featured-grid">

                <div class="featured-card">
                    <div class="featured-badge">New Product</div>
                    <div class="card-image-container">
                        <img src="../imgs/AlmondCroissant.png" alt="New Almond Croissant">
                    </div>
                    <div class="card-content">
                        <h3>Introducing Our Almond Croissant</h3>
                        <p class="card-date">June 15, 2025</p>
                        <p>Our newest creation features buttery layers filled with premium almond paste.</p>
                        <a href="../main/menu.php" class="read-more">Read More →</a>
                    </div>
                </div>

                <div class="featured-card">
                    <div class="featured-badge announcement">Announcement</div>
                    <div class="card-image-container">
                        <img src="../imgs/itpark.jpg" alt="New Bakery Location">
                    </div>
                    <div class="card-content">
                        <h3>Grand Opening: IT Park Branch</h3>
                        <p class="card-date">June 1, 2025</p>
                        <p>We're excited to announce our new location in IT Park.</p>   
                        <a href="../main/contact.php" class="read-more">Read More →</a>
                    </div>
                </div>

                <div class="featured-card">
                    <div class="featured-badge story">Our Story</div>
                    <div class="card-image-container">
                        <img src="../imgs/chef.jpg" alt="LK Bakery Team">
                    </div>
                    <div class="card-content">
                        <h3>Meet Our Head Baker</h3>
                        <p class="card-date">May 20, 2025</p>
                        <p>Discover the passion behind our recipes with Chef Maria.</p>
                        <a href="../main/about.php" class="read-more">Read More →</a>
                    </div>
                </div>
            </div>
        </section>

        <!-- Promo Banner -->
        <section class="promo-banner">
            <div class="promo-content">
                <h2>New Seasonal Flavors</h2>
                <p>Our seasonal creation brings a burst of summer flavor to every bite, available for a limited time only.</p>
                <a href="menu.php" class="cta-button secondary">Discover More</a>
            </div>
        </section>

        <!-- Testimonials -->
        <section class="testimonials">
            <h2 class="section-title">What Our Customers Say</h2>
            <div class="testimonial-grid">
                <div class="testimonial-card">
                    <div class="stars">
                        <i class="fas fa-star"></i>
                        <i class="fas fa-star"></i>
                        <i class="fas fa-star"></i>
                        <i class="fas fa-star"></i>
                        <i class="fas fa-star"></i>
                    </div>
                    <p>"The best croissants I've had outside of Paris! Perfectly crisp outside and soft inside."</p>
                    <span class="customer">- Maria S.</span>
                </div>
                <div class="testimonial-card">
                    <div class="stars">
                        <i class="fas fa-star"></i>
                        <i class="fas fa-star"></i>
                        <i class="fas fa-star"></i>
                        <i class="fas fa-star"></i>
                        <i class="fas fa-star"></i>
                    </div>
                    <p>"My weekly coffee meetings always include LK's almond croissants. Absolute perfection!"</p>
                    <span class="customer">- James L.</span>
                </div>
            </div>
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
</body>
</html>