<?php
$admin_only = true;
require '../includes/auth_check.php';
include '../includes/db_connect.php';

if (isset($_GET['toggle_availability'])) {
    $item_id = (int)$_GET['id'];
    try {
        $stmt = $pdo->prepare("UPDATE menu_items 
                             SET is_available = NOT is_available 
                             WHERE id = ?");
        $stmt->execute([$item_id]);
        $_SESSION['success'] = "Item availability updated!";
    } catch (PDOException $e) {
        $_SESSION['error'] = "Error updating availability: " . $e->getMessage();
    }
    header("Location: manage_menu.php");
    exit();
}

if (isset($_GET['delete'])) {
    $item_id = (int)$_GET['id'];
    try {
        $stmt = $pdo->prepare("SELECT image_path FROM menu_items WHERE id = ?");
        $stmt->execute([$item_id]);
        $image = $stmt->fetchColumn();
        
        if ($image && file_exists("../imgs/$image")) {
            unlink("../imgs/$image");
        }
        $stmt = $pdo->prepare("DELETE FROM menu_items WHERE id = ?");
        $stmt->execute([$item_id]);
        $_SESSION['success'] = "Item deleted successfully!";
    } catch (PDOException $e) {
        $_SESSION['error'] = "Error deleting item: " . $e->getMessage();
    }
    header("Location: manage_menu.php");
    exit();
}

$grouped_items = [];
$categories = $pdo->query("
    SELECT mi.*, mc.name AS main_category_name, mc.id AS main_category_id, 
           sc.name AS sub_category_name 
    FROM menu_items mi
    LEFT JOIN main_categories mc ON mi.main_category_id = mc.id
    LEFT JOIN sub_categories sc ON mi.sub_category_id = sc.id
    ORDER BY mc.id, sc.name, mi.item_name
")->fetchAll();

$ordered_categories = [];
foreach ($categories as $item) {
    if (!isset($ordered_categories[$item['main_category_id']])) {
        $ordered_categories[$item['main_category_id']] = [
            'name' => $item['main_category_name'],
            'items' => []
        ];
    }
    $ordered_categories[$item['main_category_id']]['items'][] = $item;
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <title>Manage Menu - LK Bakery</title>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" href="../css/admin.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
    <style>
        :root {
            --primary-color: #285670;
            --primary-hover: #1d4255;
            --secondary-color: #9c785b;
            --secondary-hover: #b08a6a;
            --success-color: #2e7d32;
            --warning-color: #ff9800;
            --danger-color: #d32f2f;
            --light-gray: #f5f5f5;
            --border-color: #e0e0e0;
            --card-shadow: 0 4px 12px rgba(0,0,0,0.1);
        }

        .admin-container {
            max-width: 1400px;
            margin: 0 auto;
            padding: 20px;
        }

        h1 {
            color: var(--primary-color);
            margin-bottom: 1.5rem;
            font-size: 2rem;
            border-bottom: 2px solid var(--primary-color);
            padding-bottom: 10px;
        }

        .alert {
            padding: 15px;
            border-radius: 5px;
            margin-bottom: 20px;
            position: relative;
            animation: fadeIn 0.3s ease-in;
            display: flex;
            align-items: center;
            gap: 10px;
        }

        @keyframes fadeIn {
            from { opacity: 0; transform: translateY(-10px); }
            to { opacity: 1; transform: translateY(0); }
        }

        .success {
            background-color: rgba(46, 125, 50, 0.1);
            border-left: 4px solid var(--success-color);
            color: var(--success-color);
        }

        .error {
            background-color: rgba(211, 47, 47, 0.1);
            border-left: 4px solid var(--danger-color);
            color: var(--danger-color);
        }

        .action-bar {
            display: flex;
            flex-wrap: wrap;
            gap: 15px;
            margin: 20px 0;
            align-items: center;
        }

        .btn {
            display: inline-flex;
            align-items: center;
            text-decoration: none;
            padding: 10px 20px;
            border-radius: 5px;
            font-weight: 500;
            transition: all 0.2s ease;
            border: none;
            cursor: pointer;
        }

        .btn i {
            margin-right: 8px;
        }

        .btn-primary {
            background-color: var(--primary-color);
            color: white;
        }

        .btn-primary:hover {
            background-color: var(--primary-hover);
            transform: translateY(-2px);
            box-shadow: var(--card-shadow);
        }

        .btn-secondary {
            background-color: var(--primary-color);
        }
        .btn-secondary:hover {
            background-color: var(--primary-hover);
            transform: translateY(-2px);
            box-shadow: var(--card-shadow);
        }

        .category-group {
            margin-bottom: 40px;
            border-radius: 8px;
            overflow: hidden;
        }

        .category-header {
            background-color: var(--primary-color);
            color: white;
            padding: 15px 20px;
            font-size: 18px;
            font-weight: bold;
            display: flex;
            justify-content: space-between;
            align-items: center;
            border-radius: 8px 8px 0 0;
        }

        .category-count {
            background: rgba(255,255,255,0.2);
            padding: 3px 8px;
            border-radius: 20px;
            font-size: 14px;
        }

        .items-grid {
            display: grid;
            grid-template-columns: repeat(auto-fill, minmax(280px, 1fr));
            gap: 20px;
            margin-top: 15px;
            padding: 5px;
        }

        .item-card {
            background: white;
            border-radius: 8px;
            overflow: hidden;
            box-shadow: var(--card-shadow);
            transition: transform 0.3s, box-shadow 0.3s;
            position: relative;
        }

        .item-card:hover {
            transform: translateY(-5px);
            box-shadow: 0 8px 16px rgba(0,0,0,0.15);
        }

        .item-image-container {
            height: 200px;
            overflow: hidden;
            position: relative;
        }

        .item-image {
            width: 100%;
            height: 100%;
            object-fit: cover;
            transition: transform 0.3s;
        }

        .item-card:hover .item-image {
            transform: scale(1.05);
        }

        .item-details {
            padding: 15px;
        }

        .item-name {
            font-weight: bold;
            margin: 0 0 5px 0;
            color: #333;
            font-size: 16px;
        }

        .item-category {
            color: var(--primary-color);
            font-size: 14px;
            margin: 0 0 5px 0;
        }

        .item-price {
            color: var(--primary-color);
            font-weight: bold;
            margin: 0 0 10px 0;
            font-size: 18px;
        }

        .item-actions {
            display: flex;
            gap: 8px;
            margin-top: 10px;
        }

        .action-btn {
            padding: 6px 12px;
            border-radius: 4px;
            font-size: 14px;
            text-decoration: none;
            display: inline-flex;
            align-items: center;
            transition: all 0.2s;
            border: none;
            cursor: pointer;
        }

        .edit-btn {
            background-color: #2196f3;
            color: white;
        }

        .edit-btn:hover {
            background-color: #1976d2;
        }

        .toggle-btn {
            background-color: var(--warning-color);
            color: white;
        }

        .toggle-btn:hover {
            background-color: #f57c00;
        }

        .delete-btn {
            background-color: var(--danger-color);
            color: white;
        }

        .delete-btn:hover {
            background-color: #c62828;
        }

        .status-badge {
            position: absolute;
            top: 10px;
            right: 10px;
            padding: 5px 10px;
            border-radius: 20px;
            font-size: 12px;
            font-weight: 500;
            z-index: 1;
        }

        .active {
            background-color: rgba(46, 125, 50, 0.9);
            color: white;
        }

        .inactive {
            background-color: rgba(158, 158, 158, 0.9);
            color: white;
        }

        .empty-state {
            text-align: center;
            padding: 40px 20px;
            background-color: var(--light-gray);
            border-radius: 8px;
            margin: 20px 0;
        }

        .empty-state h3 {
            color: #555;
            margin-bottom: 10px;
        }

        .search-box {
            margin-left: auto;
            position: relative;
        }

        .search-box input {
            padding: 10px 15px 10px 35px;
            border-radius: 5px;
            border: 1px solid var(--border-color);
            width: 250px;
            font-size: 16px;
            transition: all 0.3s;
        }

        .search-box input:focus {
            outline: none;
            border-color: var(--primary-color);
            box-shadow: 0 0 0 2px rgba(40, 86, 112, 0.2);
        }

        .search-icon {
            position: absolute;
            left: 10px;
            top: 50%;
            transform: translateY(-50%);
            color: #777;
        }

        @media (max-width: 900px) {
            .items-grid {
                grid-template-columns: repeat(auto-fill, minmax(240px, 1fr));
            }
        }

        @media (max-width: 600px) {
            .action-bar {
                flex-direction: column;
                align-items: stretch;
            }
            
            .btn {
                text-align: center;
                justify-content: center;
            }
            
            .search-box {
                width: 100%;
            }
            
            .search-box input {
                width: 100%;
            }
            
            .items-grid {
                grid-template-columns: 1fr;
            }
        }

        .modal {
            display: none;
            position: fixed;
            top: 0;
            left: 0;
            right: 0;
            bottom: 0;
            background: rgba(0,0,0,0.5);
            z-index: 1000;
            align-items: center;
            justify-content: center;
        }

        .modal-content {
            background: white;
            width: 90%;
            max-width: 400px;
            padding: 25px;
            border-radius: 8px;
            box-shadow: 0 4px 20px rgba(0,0,0,0.2);
        }

        .modal-actions {
            display: flex;
            justify-content: flex-end;
            gap: 10px;
            margin-top: 20px;
        }

        .modal-btn {
            padding: 8px 16px;
            border: none;
            border-radius: 4px;
            cursor: pointer;
            font-weight: 500;
        }

        .modal-cancel {
            background: #e0e0e0;
        }

        .modal-confirm {
            background: var(--danger-color);
            color: white;
        }
    </style>
</head>
<body>
    <?php include '../includes/header.php'; ?>
    
    <div class="admin-container">
        <h1>Manage Menu Items</h1>
        
        <?php if (isset($_SESSION['success'])): ?>
            <div class="alert success">
                <i class="fas fa-check-circle"></i>
                <?= $_SESSION['success'] ?>
            </div>
            <?php unset($_SESSION['success']); ?>
        <?php endif; ?>
        
        <?php if (isset($_SESSION['error'])): ?>
            <div class="alert error">
                <i class="fas fa-exclamation-circle"></i>
                <?= $_SESSION['error'] ?>
            </div>
            <?php unset($_SESSION['error']); ?>
        <?php endif; ?>

        <div class="action-bar">
            <a href="add_item.php" class="btn btn-primary">
                <i class="fas fa-plus"></i>
                Add New Item
            </a>
            <a href="manage_categories.php" class="btn btn-secondary">
                <i class="fas fa-tags"></i>
                Manage Categories
            </a>
            <div class="search-box">
                <i class="fas fa-search search-icon"></i>
                <input type="text" id="menuSearch" placeholder="Search items..." onkeyup="filterItems()">
            </div>
        </div>

        <?php if (empty($ordered_categories)): ?>
            <div class="empty-state">
                <h3>No menu items found</h3>
                <p>Start by adding some menu items to your catalog.</p>
                <a href="add_item.php" class="btn btn-primary">
                    <i class="fas fa-plus"></i>
                    Add First Item
                </a>
            </div>
        <?php else: ?>
            <?php foreach ($ordered_categories as $category_id => $category_data): ?>
            <div class="category-group">
                <div class="category-header">
                    <span><?= htmlspecialchars($category_data['name']) ?></span>
                    <span class="category-count"><?= count($category_data['items']) ?> items</span>
                </div>
                
                <div class="items-grid">
                    <?php foreach ($category_data['items'] as $item): ?>
                        <div class="item-card menu-item" 
                             data-name="<?= strtolower(htmlspecialchars($item['item_name'])) ?>" 
                             data-category="<?= strtolower(htmlspecialchars($item['sub_category_name'])) ?>">
                            
                            <div class="item-image-container">
                                <?php if ($item['image_path']): ?>
                                <img src="../imgs/<?= htmlspecialchars($item['image_path']) ?>" 
                                    class="item-image" 
                                    alt="<?= htmlspecialchars($item['item_name']) ?>">
                                <?php else: ?>
                                <div style="background:#eee;width:100%;height:100%;display:flex;align-items:center;justify-content:center;color:#aaa;">
                                    <i class="fas fa-image fa-3x"></i>
                                </div>
                                <?php endif; ?>
                                
                                <span class="status-badge <?= $item['is_available'] ? 'active' : 'inactive' ?>">
                                    <?= $item['is_available'] ? 'Available' : 'Hidden' ?>
                                </span>
                            </div>
                            
                            <div class="item-details">
                                <h3 class="item-name"><?= htmlspecialchars($item['item_name']) ?></h3>
                                <p class="item-category"><?= htmlspecialchars($item['sub_category_name']) ?></p>
                                <p class="item-price">₱<?= number_format($item['price'], 2) ?></p>
                                
                                <div class="item-actions">
                                    <a href="edit_item.php?id=<?= $item['id'] ?>" class="action-btn edit-btn">
                                        <i class="fas fa-edit"></i>
                                        Edit
                                    </a>
                                    
                                    <a href="?toggle_availability&id=<?= $item['id'] ?>" class="action-btn toggle-btn">
                                        <?php if ($item['is_available']): ?>
                                        <i class="fas fa-eye-slash"></i>
                                        Hide
                                        <?php else: ?>
                                        <i class="fas fa-eye"></i>
                                        Show
                                        <?php endif; ?>
                                    </a>
                                    
                                    <button class="action-btn delete-btn" 
                                        onclick="confirmDelete(<?= $item['id'] ?>, '<?= htmlspecialchars(addslashes($item['item_name'])) ?>')">
                                        <i class="fas fa-trash"></i>
                                        Delete
                                    </button>
                                </div>
                            </div>
                        </div>
                    <?php endforeach; ?>
                </div>
            </div>
            <?php endforeach; ?>
        <?php endif; ?>
    </div>

    <div id="confirmModal" class="modal">
        <div class="modal-content">
            <h3 style="margin-top:0;">Confirm Deletion</h3>
            <p>Are you sure you want to delete "<span id="itemName"></span>"? This action cannot be undone.</p>
            <div class="modal-actions">
                <button id="cancelBtn" class="modal-btn modal-cancel">Cancel</button>
                <button id="confirmBtn" class="modal-btn modal-confirm">Delete</button>
            </div>
        </div>
    </div>
    
    <script>

        function filterItems() {
            const searchInput = document.getElementById('menuSearch');
            const filter = searchInput.value.toLowerCase();
            const items = document.querySelectorAll('.menu-item');
            
            items.forEach(item => {
                const name = item.getAttribute('data-name');
                const category = item.getAttribute('data-category');
                
                if (name.includes(filter) || category.includes(filter)) {
                    item.style.display = 'block';
                } else {
                    item.style.display = 'none';
                }
            });

            document.querySelectorAll('.category-group').forEach(group => {
                const visibleItems = Array.from(group.querySelectorAll('.menu-item')).filter(item => item.style.display !== 'none');
                if (visibleItems.length === 0) {
                    group.style.display = 'none';
                } else {
                    group.style.display = 'block';
                }
            });
        }

        function confirmDelete(itemId, itemName) {
            const modal = document.getElementById('confirmModal');
            const nameSpan = document.getElementById('itemName');
            const confirmBtn = document.getElementById('confirmBtn');
            const cancelBtn = document.getElementById('cancelBtn');
            
            nameSpan.textContent = itemName;
            modal.style.display = 'flex';
            
            confirmBtn.onclick = function() {
                window.location.href = `?delete&id=${itemId}`;
            };
            
            cancelBtn.onclick = function() {
                modal.style.display = 'none';
            };

            modal.onclick = function(event) {
                if (event.target === modal) {
                    modal.style.display = 'none';
                }
            };
        }

        document.addEventListener('DOMContentLoaded', function() {
            const alerts = document.querySelectorAll('.alert');
            alerts.forEach(alert => {
                setTimeout(() => {
                    alert.style.opacity = '0';
                    alert.style.transform = 'translateY(-10px)';
                    alert.style.transition = 'opacity 0.5s, transform 0.5s';
                    
                    setTimeout(() => {
                        alert.style.display = 'none';
                    }, 500);
                }, 5000);
            });
        });
    </script>
</body>
</html>