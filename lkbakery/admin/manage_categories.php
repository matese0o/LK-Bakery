<?php
$admin_only = true;
require '../includes/auth_check.php';
include '../includes/db_connect.php';

$main_categories = $pdo->query("SELECT * FROM main_categories ORDER BY id")->fetchAll();
$sub_categories = $pdo->query("
    SELECT sc.*, mc.name AS main_category_name 
    FROM sub_categories sc
    JOIN main_categories mc ON sc.main_category_id = mc.id
    ORDER BY sc.id
")->fetchAll();
?>

<!DOCTYPE html>
<html>
<head>
    <title>Manage Categories - LK Bakery</title>
    <link rel="stylesheet" href="../css/admin.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
    <?php include '../includes/header.php'; ?>
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


        .tabs {
            display: flex;
            margin-bottom: 30px;
            border-bottom: 2px solid var(--border-color);
        }

        .tab {
            padding: 12px 24px;
            cursor: pointer;
            font-weight: 500;
            color: var(--primary-color);
            border-bottom: 3px solid transparent;
            transition: all 0.3s;
            position: relative;
        }

        .tab:hover {
            background: rgba(40, 86, 112, 0.05);
        }

        .tab.active {
            border-bottom-color: var(--primary-color);
            font-weight: 600;
        }

        .category-cards {
            display: grid;
            grid-template-columns: repeat(auto-fill, minmax(300px, 1fr));
            gap: 20px;
            margin-top: 20px;
        }

        .category-card {
            background: white;
            border-radius: 8px;
            overflow: hidden;
            box-shadow: var(--card-shadow);
            transition: transform 0.3s;
        }

        .category-card:hover {
            transform: translateY(-5px);
        }

        .category-header {
            background-color: var(--primary-color);
            color: white;
            padding: 15px;
            font-weight: 600;
            display: flex;
            justify-content: space-between;
            align-items: center;
        }

        .category-actions {
            display: flex;
            gap: 8px;
        }

        .action-btn {
            padding: 6px 12px;
            border-radius: 4px;
            font-size: 14px;
            text-decoration: none;
            transition: all 0.2s;
            display: inline-flex;
            align-items: center;
            gap: 5px;
        }

        .edit-btn {
            background-color: #2196f3;
            color: white;
        }

        .edit-btn:hover {
            background-color: #1976d2;
        }

       .delete-btn {
            background-color: var(--danger-color);
            color: white;
            padding: 6px 12px;
            border-radius: 4px;
            font-size: 14px;
            text-decoration: none;
            display: inline-flex;
            align-items: center;
            gap: 5px;
            transition: all 0.2s;
            border: none;
            cursor: pointer;
        }

        .delete-btn:hover {
            background-color: #c62828;
        }


        .category-form {
            background: white;
            padding: 25px;
            border-radius: 8px;
            box-shadow: var(--card-shadow);
            margin-bottom: 30px;
            max-width: 600px;
        }

        .form-group {
            margin-bottom: 20px;
        }

        .form-group label {
            display: block;
            margin-bottom: 8px;
            font-weight: 500;
            color: var(--primary-color);
        }

        .form-control {
            width: 80%;
            padding: 12px 15px;
            border: 1px solid var(--border-color);
            border-radius: 6px;
            font-size: 1rem;
            transition: all 0.3s;
        }

        .form-control:focus {
            outline: none;
            border-color: var(--primary-color);
            box-shadow: 0 0 0 3px rgba(40, 86, 112, 0.1);
        }

        select.form-control {
            appearance: none;
            background-image: url("data:image/svg+xml;charset=UTF-8,%3csvg xmlns='http://www.w3.org/2000/svg' viewBox='0 0 24 24' fill='%23285670'%3e%3cpath d='M7 10l5 5 5-5z'/%3e%3c/svg%3e");
            background-repeat: no-repeat;
            background-position: right 15px center;
            background-size: 15px;
        }

        .btn-submit {
            background-color: var(--primary-color);
            color: white;
            border: none;
            padding: 12px 20px;
            border-radius: 6px;
            font-weight: 500;
            cursor: pointer;
            transition: all 0.3s;
            display: inline-flex;
            align-items: center;
            gap: 8px;
        }

        .btn-submit:hover {
            background-color: var(--primary-hover);
            transform: translateY(-2px);
            box-shadow: var(--card-shadow);
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
            max-width: 500px;
            padding: 25px;
            border-radius: 8px;
            box-shadow: 0 5px 20px rgba(0,0,0,0.2);
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

        @media (max-width: 768px) {
            .tabs {
                flex-direction: column;
                border-bottom: none;
            }
            
            .tab {
                border-bottom: 1px solid var(--border-color);
            }
            
            .category-cards {
                grid-template-columns: 1fr;
            }
        }
    </style>
</head>
<body>
    
    <div class="admin-container">
        <h1>Manage Categories</h1>
        
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

        <div class="tabs">
            <div class="tab active" onclick="showTab('main-categories')">Main Categories</div>
            <div class="tab" onclick="showTab('sub-categories')">Sub Categories</div>
        </div>

        <div id="main-categories" class="tab-content active">
            <div class="category-form">
                <h2 style="color: var(--primary-color); margin-top: 0;">Add New Main Category</h2>
                <form method="POST" action="save_category.php">
                    <input type="hidden" name="type" value="main">
                    <div class="form-group">
                        <label>Category Name</label>
                        <input type="text" name="name" class="form-control" placeholder="Enter category name" required>
                    </div>
                    <button type="submit" class="btn-submit">
                        <i class="fas fa-plus-circle"></i> Add Category
                    </button>
                </form>
            </div>

            <h2>Existing Main Categories</h2>
            <div class="category-cards">
                <?php foreach ($main_categories as $category): ?>
                <div class="category-card">
                    <div class="category-header">
                        <?= htmlspecialchars($category['name']) ?>
                        <div class="category-actions">
                            <a href="edit_category.php?type=main&id=<?= $category['id'] ?>" class="action-btn edit-btn">
                                <i class="fas fa-edit"></i> Edit
                            </a>
                            <button class="action-btn delete-btn" 
                                onclick="confirmDelete('main', <?= $category['id'] ?>, '<?= htmlspecialchars(addslashes($category['name'])) ?>')">
                                <i class="fas fa-trash"></i>
                                Delete
                            </button>   
                        </div>
                    </div>
                </div>
                <?php endforeach; ?>
            </div>
        </div>

        <div id="sub-categories" class="tab-content">
            <div class="category-form">
                <h2 style="color: var(--primary-color); margin-top: 0;">Add New Sub Category</h2>
                <form method="POST" action="save_category.php">
                    <input type="hidden" name="type" value="sub">
                    <div class="form-group">
                        <label>Main Category</label>
                        <select name="main_category_id" class="form-control" required>
                            <option value="" disabled selected>Select Main Category</option>
                            <?php foreach ($main_categories as $category): ?>
                                <option value="<?= $category['id'] ?>"><?= htmlspecialchars($category['name']) ?></option>
                            <?php endforeach; ?>
                        </select>
                    </div>
                    <div class="form-group">
                        <label>Sub Category Name</label>
                        <input type="text" name="name" class="form-control" placeholder="Enter sub category name" required>
                    </div>
                    <button type="submit" class="btn-submit">
                        <i class="fas fa-plus-circle"></i> Add Sub Category
                    </button>
                </form>
            </div>

            <h2>Existing Sub Categories</h2>
            <div class="category-cards">
                <?php foreach ($sub_categories as $sub): ?>
                <div class="category-card">
                    <div class="category-header">
                        <?= htmlspecialchars($sub['name']) ?>
                        <small><?= htmlspecialchars($sub['main_category_name']) ?></small>
                    </div>
                    <div style="padding: 15px;">
                        <div class="category-actions">
                            <a href="edit_category.php?type=sub&id=<?= $sub['id'] ?>" class="action-btn edit-btn">
                                <i class="fas fa-edit"></i> Edit
                            </a>
                            <button class="action-btn delete-btn" 
                                onclick="confirmDelete('sub', <?= $sub['id'] ?>, '<?= htmlspecialchars(addslashes($sub['name'])) ?>')">
                                <i class="fas fa-trash"></i>
                                Delete
                            </button>   
                        </div>
                    </div>
                </div>
                <?php endforeach; ?>
            </div>
        </div>
    </div>

    <div id="confirmModal" class="modal">
        <div class="modal-content">
            <h3 style="margin-top:0;">Confirm Deletion</h3>
            <p>Are you sure you want to delete "<span id="itemName"></span>"? This action cannot be undone.</p>
            <div class="modal-actions">
                <button id="cancelBtn" class="modal-btn modal-cancel">Cancel</button>
                <a id="confirmBtn" class="modal-btn modal-confirm">Delete</a>
            </div>
        </div>
    </div>
    
    <script>
        function showTab(tabId) {
            document.querySelectorAll('.tab-content').forEach(tab => {
                tab.style.display = 'none';
            });
            
            document.querySelectorAll('.tab').forEach(tab => {
                tab.classList.remove('active');
            });

            document.getElementById(tabId).style.display = 'block';
            event.currentTarget.classList.add('active');
        }

        function confirmDelete(type, id, name) {
            const modal = document.getElementById('confirmModal');
            const nameSpan = document.getElementById('itemName');
            const confirmBtn = document.getElementById('confirmBtn');
            const cancelBtn = document.getElementById('cancelBtn');
            
            nameSpan.textContent = name;
            confirmBtn.href = `delete_category.php?type=${type}&id=${id}`;
            modal.style.display = 'flex';
            
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
            document.getElementById('main-categories').style.display = 'block';
        });
    </script>
</body>
</html>