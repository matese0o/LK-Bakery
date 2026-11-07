<?php
$admin_only = true;
require '../includes/auth_check.php';
include '../includes/db_connect.php';

if (isset($_GET['toggle_status'])) {
    $user_id = (int)$_GET['id'];
    try {
        $stmt = $pdo->prepare("UPDATE users 
                             SET is_active = NOT is_active 
                             WHERE id = ?");
        $stmt->execute([$user_id]);
        
        $_SESSION['success'] = "User status updated successfully!";
    } catch (PDOException $e) {
        $_SESSION['error'] = "Error updating user status: " . $e->getMessage();
    }
    header("Location: manage_users.php");
    exit();
}

try {
    $stmt = $pdo->query("SELECT COLUMN_NAME 
                       FROM INFORMATION_SCHEMA.COLUMNS 
                       WHERE TABLE_NAME = 'users' 
                       AND COLUMN_NAME IN ('is_active', 'created_at')");
    $existing_columns = $stmt->fetchAll(PDO::FETCH_COLUMN);
    
    if (!in_array('is_active', $existing_columns)) {
        $pdo->exec("ALTER TABLE users ADD COLUMN is_active BOOLEAN DEFAULT 1");
    }
    
    if (!in_array('created_at', $existing_columns)) {
        $pdo->exec("ALTER TABLE users ADD COLUMN created_at DATETIME DEFAULT CURRENT_TIMESTAMP");
    }
} catch (PDOException $e) {
    $_SESSION['error'] = "Database configuration error: " . $e->getMessage();
}
?>

<!DOCTYPE html>
<html>
<head>
    <title>Manage Users - LK Bakery</title>
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

        .action-bar {
            display: flex;
            justify-content: space-between;
            margin: 20px 0;
            flex-wrap: wrap;
            gap: 15px;
        }

        .btn {
            display: inline-flex;
            align-items: center;
            gap: 8px;
            padding: 10px 20px;
            border-radius: 6px;
            font-weight: 500;
            text-decoration: none;
            transition: all 0.3s;
        }

        .btn-secondary {
            background-color: var(--primary-color);
            color: white;
        }

        .btn-secondary:hover {
            background-color: var(--primary-hover);
            transform: translateY(-2px);
            box-shadow: var(--card-shadow);
        }

        .search-box {
            position: relative;
            min-width: 250px;
        }

        .search-box input {
            padding: 10px 15px 10px 35px;
            border: 1px solid var(--border-color);
            border-radius: 6px;
            width: 100%;
            font-size: 16px;
            transition: all 0.3s;
        }

        .search-box input:focus {
            outline: none;
            border-color: var(--primary-color);
            box-shadow: 0 0 0 3px rgba(40, 86, 112, 0.1);
        }

        .search-icon {
            position: absolute;
            left: 10px;
            top: 50%;
            transform: translateY(-50%);
            color: #777;
        }

        .user-cards {
            display: grid;
            grid-template-columns: repeat(auto-fill, minmax(300px, 1fr));
            gap: 20px;
            margin-top: 20px;
        }

        .user-card {
            background: white;
            border-radius: 8px;
            overflow: hidden;
            box-shadow: var(--card-shadow);
            transition: transform 0.3s;
        }

        .user-card:hover {
            transform: translateY(-5px);
        }

        .user-header {
            background-color: var(--primary-color);
            color: white;
            padding: 15px;
            display: flex;
            justify-content: space-between;
            align-items: center;
        }

        .user-name {
            font-weight: 600;
            font-size: 1.1rem;
        }

        .user-role {
            background: rgba(255,255,255,0.2);
            padding: 3px 8px;
            border-radius: 20px;
            font-size: 0.8rem;
            text-transform: capitalize;
        }

        .user-details {
            padding: 15px;
        }

        .user-detail {
            display: flex;
            margin-bottom: 10px;
        }

        .detail-label {
            font-weight: 500;
            color: var(--primary-color);
            min-width: 100px;
        }

        .status-badge {
            display: inline-block;
            padding: 5px 10px;
            border-radius: 20px;
            font-size: 0.8rem;
            font-weight: 500;
        }

        .active {
            background-color: rgba(46, 125, 50, 0.1);
            color: var(--success-color);
            border: 1px solid rgba(46, 125, 50, 0.2);
        }

        .inactive {
            background-color: rgba(158, 158, 158, 0.1);
            color: #757575;
            border: 1px solid rgba(158, 158, 158, 0.2);
        }

        .user-actions {
            display: flex;
            gap: 8px;
            margin-top: 15px;
        }

        .action-btn {
            padding: 8px 12px;
            border-radius: 4px;
            font-size: 14px;
            text-decoration: none;
            transition: all 0.2s;
            display: inline-flex;
            align-items: center;
            gap: 5px;
            flex: 1;
            justify-content: center;
        }

        .toggle-btn {
            background-color: var(--warning-color);
            color: white;
        }

        .toggle-btn:hover {
            background-color: #f57c00;
        }

        @media (max-width: 768px) {
            .user-cards {
                grid-template-columns: 1fr;
            }
            
            .action-bar {
                flex-direction: column;
            }
            
            .search-box {
                width: 100%;
            }
        }
    </style>
</head>
<body>
    
    <div class="admin-container">
        <h1>Manage Users</h1>
        
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
            <a href="dashboard.php" class="btn btn-secondary">
                <i class="fas fa-arrow-left"></i> Back to Dashboard
            </a>
            <div class="search-box">
                <i class="fas fa-search search-icon"></i>
                <input type="text" id="userSearch" placeholder="Search users..." onkeyup="filterUsers()">
            </div>
        </div>

        <div class="user-cards">
            <?php
            $stmt = $pdo->query("SELECT * FROM users ORDER BY created_at DESC");
            while ($user = $stmt->fetch(PDO::FETCH_ASSOC)):
            ?>
            <div class="user-card">
                <div class="user-header">
                    <div class="user-name"><?= htmlspecialchars($user['username']) ?></div>
                    <div class="user-role"><?= ucfirst($user['role']) ?></div>
                </div>
                <div class="user-details">
                    <div class="user-detail">
                        <span class="detail-label">Email:</span>
                        <span><?= htmlspecialchars($user['email']) ?></span>
                    </div>
                    <div class="user-detail">
                        <span class="detail-label">Registered:</span>
                        <span><?= date('M j, Y', strtotime($user['created_at'])) ?></span>
                    </div>
                    <div class="user-detail">
                        <span class="detail-label">Status:</span>
                        <span class="status-badge <?= $user['is_active'] ? 'active' : 'inactive' ?>">
                            <?= $user['is_active'] ? 'Active' : 'Inactive' ?>
                        </span>
                    </div>
                    <div class="user-actions">
                        <a href="?toggle_status&id=<?= $user['id'] ?>" 
                           class="action-btn toggle-btn">
                            <i class="fas <?= $user['is_active'] ? 'fa-eye-slash' : 'fa-eye' ?>"></i>
                            <?= $user['is_active'] ? 'Disable' : 'Enable' ?>
                        </a>
                    </div>
                </div>
            </div>
            <?php endwhile; ?>
        </div>
    </div>

    <script>
        function filterUsers() {
            const input = document.getElementById('userSearch');
            const filter = input.value.toLowerCase();
            const cards = document.querySelectorAll('.user-card');
            
            cards.forEach(card => {
                const text = card.textContent.toLowerCase();
                if (text.includes(filter)) {
                    card.style.display = 'block';
                } else {
                    card.style.display = 'none';
                }
            });
        }
    </script>
</body>
</html>