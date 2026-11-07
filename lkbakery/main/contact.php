<?php
session_start();
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Contact Us - LK Bakery</title>
    <link rel="stylesheet" href="../css/contact.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
    <style>
        .form-group.required label::after {
            content: " *";
            color: #dc3545;
        }
        
        .alert {
            padding: 15px;
            margin: 20px auto 0;
            border-radius: 5px;
            font-weight: 500;
            max-width: 900px;
            transition: opacity 0.5s ease;
        }
    </style>
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
                <li><a href="../main/contact.php" class="active">Contact</a></li>
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

    <main class="contact-main">
        <?php if(isset($_SESSION['success'])): ?>
            <div class="alert alert-success">
                <?= $_SESSION['success']; unset($_SESSION['success']); ?>
            </div>
        <?php endif; ?>

        <?php if(isset($_SESSION['error'])): ?>
            <div class="alert alert-error">
                <?= $_SESSION['error']; unset($_SESSION['error']); ?>
            </div>
        <?php endif; ?>
        
        <section class="contact-hero">
            <div class="hero-content">
                <h1>CONTACT</h1>
                <p>We're here to help with any questions or special requests</p>
            </div>
        </section>

        <div class="contact-container">
            <div class="contact-wrapper">
                <div class="contact-details">
                    <div class="contact-card">
                        <i class="fas fa-map-marker-alt"></i>
                        <h3>Visit Us</h3>
                        <p>Betty Lu Compound<br>F. Manaio Extension<br>6000 Cebu City, Philippines</p>
                    </div>
                    
                    <div class="contact-card">
                        <i class="fas fa-clock"></i>
                        <h3>Opening Hours</h3>
                        <p>Monday - Saturday: 7AM - 9PM<br>Sunday: 8AM - 8PM</p>
                    </div>

                    <div class="contact-card">
                        <i class="fas fa-phone"></i>
                        <h3>Call Us</h3>
                        <p>+63 917 315 2441<br>+63 32 234 5678</p>
                    </div>

                    <div class="contact-card">
                        <i class="fas fa-envelope"></i>
                        <h3>Email Us</h3>
                        <p>Elkaybakery.ph@gmail.com</p>
                    </div>
                </div>

                <div class="contact-map">
                    <iframe 
                        src="https://www.google.com/maps/embed?pb=!1m18!1m12!1m3!1d3925.234569987454!2d123.89327731526094!3d10.315518192660385!2m3!1f0!2f0!3f0!3m2!1i1024!2i768!4f13.1!3m3!1m2!1s0x33a999fc8b610edd%3A0xc254ef5ad37bd3f4!2sLK%20Bakery!5e0!3m2!1sen!2sph!4v1716639872305!5m2!1sen!2sph"
                        allowfullscreen="" 
                        loading="lazy" 
                        referrerpolicy="no-referrer-when-downgrade"
                        title="LK Bakery Location Map">
                    </iframe>
                </div>
            </div>

            <section class="contact-form">
                <div class="form-tabs">
                    <button class="tab-btn active" data-tab="messageTab">Send Message</button>
                    <button class="tab-btn" data-tab="reviewTab">Submit Review</button>
                </div>

                <div id="messageTab" class="tab-content active">
                    <h2>Send Us a Message</h2>
                    <form id="contactForm" action="process_contact.php" method="POST">
                        <input type="hidden" name="form_type" value="message">
                        <div class="form-grid">
                            <div class="form-group required">
                                <input type="text" id="name" name="name" placeholder=" " required>
                                <label for="name">Full Name</label>
                            </div>
                            <div class="form-group required">
                                <input type="email" id="email" name="email" placeholder=" " required>
                                <label for="email">Email Address</label>
                            </div>
                            <div class="form-group">
                                <input type="tel" id="phone" name="phone" placeholder=" ">
                                <label for="phone">Phone Number</label>
                            </div>
                            <div class="form-group full-width required">
                                <textarea id="message" name="message" rows="5" placeholder=" " required></textarea>
                                <label for="message">Your Message</label>
                            </div>
                        </div>
                        <button type="submit" class="submit-btn">
                            Send Message <i class="fas fa-paper-plane"></i>
                        </button>
                    </form>
                </div>

                <div id="reviewTab" class="tab-content">
                    <h2>Share Your Bakery Experience</h2>
                    <p class="form-description">We'd love to hear about your visit! Your review helps us improve.</p>
                    
                    <form id="reviewForm" action="process_contact.php" method="POST">
                        <input type="hidden" name="form_type" value="review">
                        <div class="form-grid">
                            <div class="form-group required">
                                <input type="text" id="review_name" name="name" placeholder=" " required>
                                <label for="review_name">Your Name</label>
                            </div>
                            <div class="form-group">
                                <input type="email" id="review_email" name="email" placeholder=" ">
                                <label for="review_email">Email (Optional - for follow-up)</label>
                            </div>
                            <div class="form-group full-width required">
                                <div class="rating-container">
                                    <span>Rate Your Experience</span>
                                    <div class="star-rating">
                                        <input type="radio" id="star5" name="rating" value="5" required>
                                        <label for="star5" title="Excellent"><i class="fas fa-star"></i></label>
                                        
                                        <input type="radio" id="star4" name="rating" value="4">
                                        <label for="star4" title="Very Good"><i class="fas fa-star"></i></label>
                                        
                                        <input type="radio" id="star3" name="rating" value="3">
                                        <label for="star3" title="Good"><i class="fas fa-star"></i></label>
                                        
                                        <input type="radio" id="star2" name="rating" value="2">
                                        <label for="star2" title="Fair"><i class="fas fa-star"></i></label>
                                        
                                        <input type="radio" id="star1" name="rating" value="1">
                                        <label for="star1" title="Poor"><i class="fas fa-star"></i></label>
                                    </div>
                                </div>
                                <div class="error-message" style="display: none;">Please select a rating</div>
                            </div>
                            <div class="form-group full-width required">
                                <textarea id="review" name="review" rows="5" placeholder=" " required></textarea>
                                <label for="review">Tell us about your experience</label>
                                <small>What did you enjoy most? Any suggestions for improvement?</small>
                            </div>
                            <div class="form-group full-width">
                                <input type="text" id="item_reviewed" name="item_reviewed" placeholder=" ">
                                <label for="item_reviewed">Favorite Item (Optional)</label>
                                <small>Which pastry or bread did you love the most?</small>
                            </div>
                        </div>
                        <button type="submit" class="submit-btn">
                            <i class="fas fa-star"></i> Submit Review
                        </button>
                    </form>
                </div>
            </section>
        </div>
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
                <a href="#"><img src="../imgs/Instagram.png" alt="Instagram" class="social-icon"> </a>
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

        document.addEventListener('DOMContentLoaded', function() {

            const tabButtons = document.querySelectorAll('.tab-btn');
            tabButtons.forEach(button => {
                button.addEventListener('click', function() {

                    const tabId = this.getAttribute('data-tab');
                    openTab(tabId);
                });
            });


            const starRating = document.querySelector('.star-rating');
            if (starRating) {
                const stars = starRating.querySelectorAll('input[type="radio"]');
                const starLabels = starRating.querySelectorAll('label');
                
                starLabels.forEach(label => {
                    label.addEventListener('click', function() {
                        const inputId = this.getAttribute('for');
                        const input = document.getElementById(inputId);
                        

                        starLabels.forEach(s => {
                            const starValue = parseInt(s.getAttribute('for').replace('star', ''));
                            if (starValue <= parseInt(input.value)) {
                                s.classList.add('selected');
                                s.querySelector('i').style.color = '#efd075';
                            } else {
                                s.classList.remove('selected');
                                s.querySelector('i').style.color = '#ddd';
                            }
                        });
                        

                        document.querySelector('.error-message').style.display = 'none';
                    });
                    
                    label.addEventListener('mouseover', function() {
                        const hoverValue = parseInt(this.getAttribute('for').replace('star', ''));
                        starLabels.forEach(s => {
                            const starValue = parseInt(s.getAttribute('for').replace('star', ''));
                            if (starValue <= hoverValue) {
                                s.querySelector('i').style.color = '#f8d64e';
                            }
                        });
                    });
                    
                    label.addEventListener('mouseout', function() {
                        const checked = starRating.querySelector('input[type="radio"]:checked');
                        starLabels.forEach(s => {
                            if (!checked || parseInt(s.getAttribute('for').replace('star', '')) > parseInt(checked.value)) {
                                s.querySelector('i').style.color = '#ddd';
                            }
                        });
                    });
                });
            }
            
            const reviewForm = document.getElementById('reviewForm');
            if (reviewForm) {
                reviewForm.addEventListener('submit', function(event) {
                    const rating = document.querySelector('input[name="rating"]:checked');
                    const reviewText = document.getElementById('review').value.trim();
                    const reviewName = document.getElementById('review_name').value.trim();
                    
                    let isValid = true;
                    
                    if (!rating) {
                        document.querySelector('.error-message').style.display = 'block';
                        isValid = false;
                    } else {
                        document.querySelector('.error-message').style.display = 'none';
                    }
                    
                    if (!reviewText || !reviewName) {
                        isValid = false;
                    }
                    
                    if (!isValid) {
                        event.preventDefault();
                        if (!rating) {
                            document.querySelector('.rating-container').scrollIntoView({ behavior: 'smooth', block: 'center' });
                        } else if (!reviewName) {
                            document.getElementById('review_name').scrollIntoView({ behavior: 'smooth', block: 'center' });
                        } else {
                            document.getElementById('review').scrollIntoView({ behavior: 'smooth', block: 'center' });
                        }
                    }
                });
            }


            const urlParams = new URLSearchParams(window.location.search);
            const tab = urlParams.get('tab');
            
            if (tab === 'review') {
                openTab('reviewTab');
            }
        });

        function openTab(tabId) {

            const tabContents = document.querySelectorAll('.tab-content');
            tabContents.forEach(tab => tab.classList.remove('active'));
            

            const tabButtons = document.querySelectorAll('.tab-btn');
            tabButtons.forEach(button => button.classList.remove('active'));
            
 
            document.getElementById(tabId).classList.add('active');
            

            document.querySelector(`.tab-btn[data-tab="${tabId}"]`).classList.add('active');
        }
    </script>
</body>
</html>