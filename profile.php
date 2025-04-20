<?php
session_start();
require 'config.php';

if (!isset($_SESSION['user_id'])) {
    header('Location: login.php');
    exit;
}

// عرض رسالة النجاح إذا وجدت
$success = '';
if (isset($_SESSION['success_message'])) {
    $success = $_SESSION['success_message'];
    unset($_SESSION['success_message']);
}

// جلب بيانات المستخدم
$stmt = $pdo->prepare("SELECT * FROM users WHERE id = ?");
$stmt->execute([$_SESSION['user_id']]);
$user = $stmt->fetch();

$error = '';
$success = '';

?>

<!DOCTYPE html>
<html lang="ar" dir="rtl">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>الملف الشخصي</title>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <style>
        :root {
            --primary-color: #3498db;
            --secondary-color: #2c3e50;
            --light-color: #ecf0f1;
            --dark-color: #34495e;
            --success-color: #2ecc71;
            --danger-color: #e74c3c;
        }
        
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
            font-family: 'Tajawal', sans-serif;
        }
        
        body {
            background-color: #f5f7fa;
            color: #333;
        }
        
        .profile-container {
            max-width: 1000px;
            margin: 30px auto;
            padding: 20px;
        }
        
        .profile-header {
            text-align: center;
            margin-bottom: 30px;
        }
        
        .profile-header h1 {
            color: var(--secondary-color);
            margin-bottom: 10px;
        }
        
        .profile-card {
            background: white;
            border-radius: 10px;
            box-shadow: 0 5px 15px rgba(0,0,0,0.1);
            overflow: hidden;
        }
        
        .profile-info {
            display: flex;
            padding: 30px;
        }
        
        .profile-picture {
            width: 150px;
            height: 150px;
            border-radius: 50%;
            object-fit: cover;
            border: 5px solid var(--light-color);
            margin-left: 30px;
        }
        
        .profile-details {
            flex: 1;
        }
        
        .profile-details h2 {
            color: var(--secondary-color);
            margin-bottom: 15px;
        }
        
        .detail-item {
            margin-bottom: 10px;
            display: flex;
        }
        
        .detail-label {
            font-weight: bold;
            width: 120px;
            color: var(--dark-color);
        }
        
        .edit-form {
            padding: 30px;
            border-top: 1px solid #eee;
        }
        
        .form-group {
            margin-bottom: 20px;
        }
        
        .form-group label {
            display: block;
            margin-bottom: 8px;
            font-weight: bold;
            color: var(--dark-color);
        }
        
        .form-control {
            width: 100%;
            padding: 12px;
            border: 1px solid #ddd;
            border-radius: 5px;
            font-size: 16px;
        }
        
        textarea.form-control {
            min-height: 100px;
            resize: vertical;
        }
        
        .btn {
            padding: 12px 25px;
            border: none;
            border-radius: 5px;
            cursor: pointer;
            font-size: 16px;
            transition: all 0.3s;
        }
        
        .btn-primary {
            background-color: var(--primary-color);
            color: white;
        }
        
        .btn-primary:hover {
            background-color: #2980b9;
        }
        
        .btn-edit {
            background-color: transparent;
            color: var(--primary-color);
            border: 1px solid var(--primary-color);
            margin-left: 10px;
        }
        
        .btn-edit:hover {
            background-color: var(--primary-color);
            color: white;
        }
        
        .error-message {
            color: var(--danger-color);
            background-color: #fadbd8;
            padding: 10px;
            border-radius: 5px;
            margin-bottom: 20px;
        }
        
        .success-message {
            color: var(--success-color);
            background-color: #d5f5e3;
            padding: 10px;
            border-radius: 5px;
            margin-bottom: 20px;
        }
        
        @media (max-width: 768px) {
            .profile-info {
                flex-direction: column;
                align-items: center;
                text-align: center;
            }
            
            .profile-picture {
                margin-left: 0;
                margin-bottom: 20px;
            }
            
            .detail-item {
                flex-direction: column;
            }
            
            .detail-label {
                width: 100%;
                margin-bottom: 5px;
            }
        }
    </style>
</head>
<body>
    <div class="profile-container">
        <div class="profile-header">
            <h1><i class="fas fa-user-circle"></i> الملف الشخصي</h1>
        </div>
        
        <?php if ($error): ?>
            <div class="error-message"><?php echo $error; ?></div>
        <?php endif; ?>
        
        <?php if ($success): ?>
            <div class="success-message"><?php echo $success; ?></div>
        <?php endif; ?>
        
        <div class="profile-card">
            <div class="profile-info">
                
                
                <div class="profile-details">
                    <h2><?php echo htmlspecialchars($user['username']); ?></h2>
                    
                    <div class="detail-item">
                        <span class="detail-label">البريد الإلكتروني:</span>
                        <span><?php echo htmlspecialchars($user['email']); ?></span>
                    </div>
                    
                    <div class="detail-item">
                        <span class="detail-label">تاريخ التسجيل:</span>
                        <span><?php echo date('Y/m/d', strtotime($user['created_at'])); ?></span>
                    </div>
                    
                    
                </div>
            </div>
            
            <form action="profile.php" method="post" class="edit-form">
                
                <a href="modifinfo.php" class="btn btn-edit">تغيير معلوماتي الشخصية</a>
                <a href="modifpass.php" class="btn btn-edit">تغيير كلمة المرور</a>
            </form>
        </div>
    </div>
</body>
</html>