<?php
$admin_only = true;
$page_title = "Admin Dashboard";
require __DIR__ . '/../includes/auth_check.php';
require __DIR__ . '/../includes/db_connect.php';
require __DIR__ . '/../includes/header.php';

$menu_items_count = $pdo->query("SELECT COUNT(*) FROM menu_items")->fetchColumn();
?>

<div class="admin-container">
    <div class="dashboard-header">
        <h1>Dashboard Overview</h1>
        <div class="header-actions">
            <div class="search-box">
                <i class="fas fa-search"></i>
                <input type="text" placeholder="Search...">
            </div>
        </div>
    </div>
    
    <div class="stats-grid">
        <div class="stat-card">
            <div class="stat-content">
                <h3>Total Orders</h3>
                <p class="stat-value">1,248</p>
                <p class="stat-change positive">↑ 12% from last week</p>
            </div>
            <div class="stat-icon">
                <i class="fas fa-clipboard-list"></i>
            </div>
        </div>
        
        <div class="stat-card">
            <div class="stat-content">
                <h3>Active Users</h3>
                <p class="stat-value">562</p>
                <p class="stat-change positive">↑ 8% from last week</p>
            </div>
            <div class="stat-icon">
                <i class="fas fa-users"></i>
            </div>
        </div>
        
        <div class="stat-card">
            <div class="stat-content">
                <h3>Menu Items</h3>
                <p class="stat-value"><?= $menu_items_count ?></p>
                <p class="stat-change">No change</p>
            </div>
            <div class="stat-icon">
                <i class="fas fa-utensils"></i>
            </div>
        </div>
    </div>
    
    <div class="dashboard-content">
        <div class="main-section">
            <div class="section-header">
                <h2>Popular This Week</h2>
                <a href="#" class="view-all">View All</a>
            </div>
            
            <div class="popular-items">
                <div class="popular-item">
                    <img src="../imgs/StrawberryCroissant.png" alt="Croissant">
                    <h4>Strawberry Croissant</h4>
                    <p>32 orders</p>
                </div>
                <div class="popular-item">
                    <img src="../imgs/LeCrookie.png" alt="Coffee">
                    <h4>LeCrookie</h4>
                    <p>28 orders</p>
                </div>
                <div class="popular-item">
                    <img src="../imgs/AlmondCroissant.png" alt="Sandwich">
                    <h4>Almont Croissant</h4>
                    <p>25 orders</p>
                </div>
            </div>
        </div>
        
        <div class="sidebar">
            <div class="section-header">
                <h2>Quick Actions</h2>
            </div>
            
            <div class="quick-actions">
                <a href="add_item.php" class="action-card">
                    <i class="fas fa-plus"></i>
                    <span>Add New Item</span>
                </a>
                <a href="manage_categories.php" class="action-card">
                    <i class="fas fa-folder-plus"></i>
                    <span>Add Main Category</span>
                </a>
                <a href="manage_categories.php" class="action-card">
                    <i class="fas fa-tag"></i>
                    <span>Add Sub Category</span>
                </a>
                <a href="manage_users.php" class="action-card">
                    <i class="fas fa-user-cog"></i>
                    <span>Manage Users</span>
                </a>
            </div>
        </div>
    </div>
</div>

<?php include __DIR__ . '/../includes/footer.php'; ?>