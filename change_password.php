<?php
session_start();
require 'config.php';

if (!isset($_SESSION['user_id'])) {
    header('Location: login.php');
    exit;
}

$error = '';
$success = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $current_password = $_POST['current_password'];
    $new_password = $_POST['new_password'];
    $confirm_password = $_POST['confirm_password'];

    // جلب بيانات المستخدم
    $stmt = $pdo->prepare("SELECT * FROM users WHERE id = ?");
    $stmt->execute([$_SESSION['user_id']]);
    $user = $stmt->fetch();

    // التحقق من كلمة المرور الحالية
    if (!password_verify($current_password, $user['password'])) {
        $error = 'كلمة المرور الحالية غير صحيحة';
    } 
    // التحقق من تطابق كلمتي المرور الجديدتين
    elseif ($new_password !== $confirm_password) {
        $error = 'كلمتا المرور الجديدتان غير متطابقتين';
    } 
    // التحقق من طول كلمة المرور
    elseif (strlen($new_password) < 8) {
        $error = 'كلمة المرور يجب أن تكون 8 أحرف على الأقل';
    } 
    // إذا نجحت جميع التحققات
    else {
        $hashed_password = password_hash($new_password, PASSWORD_DEFAULT);
        $stmt = $pdo->prepare("UPDATE users SET password = ? WHERE id = ?");
        $stmt->execute([$hashed_password, $_SESSION['user_id']]);
        $success = 'تم تغيير كلمة المرور بنجاح';
    }
}
?>

<!DOCTYPE html>
<html lang="ar" dir="rtl">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>تغيير كلمة المرور</title>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <style>
        body {
            font-family: Arial, sans-serif;
            background-color: #f4f4f4;
            margin: 0;
            padding: 0;
            display: flex;
            justify-content: center;
            align-items: center;
            height: 100vh;
        }
        .password-container {
            background: #fff;
            padding: 30px;
            border-radius: 8px;
            box-shadow: 0 0 20px rgba(0, 0, 0, 0.1);
            width: 400px;
        }
        h2 {
            text-align: center;
            margin-bottom: 20px;
            color: #333;
        }
        .form-group {
            margin-bottom: 20px;
        }
        label {
            display: block;
            margin-bottom: 8px;
            font-weight: bold;
        }
        input[type="password"] {
            width: 100%;
            padding: 12px;
            border: 1px solid #ddd;
            border-radius: 4px;
            font-size: 16px;
        }
        .btn {
            width: 100%;
            padding: 12px;
            background-color: #5cb85c;
            border: none;
            border-radius: 4px;
            color: white;
            font-size: 16px;
            cursor: pointer;
        }
        .btn:hover {
            background-color: #4cae4c;
        }
        .error {
            color: #d9534f;
            margin-bottom: 15px;
            text-align: center;
        }
        .success {
            color: #5cb85c;
            margin-bottom: 15px;
            text-align: center;
        }
        .back-link {
            display: block;
            text-align: center;
            margin-top: 15px;
            color: #337ab7;
            text-decoration: none;
        }
        .password-toggle {
            position: relative;
        }
        .toggle-icon {
            position: absolute;
            top: 50%;
            left: 10px;
            transform: translateY(-50%);
            cursor: pointer;
            color: #777;
        }
    </style>
</head>
<body>
    <div class="password-container">
        <h2><i class="fas fa-key"></i> تغيير كلمة المرور</h2>
        
        <?php if ($error): ?>
            <div class="error"><?php echo $error; ?></div>
        <?php endif; ?>
        
        <?php if ($success): ?>
            <div class="success"><?php echo $success; ?></div>
        <?php endif; ?>
        
        <form action="change_password.php" method="post">
            <div class="form-group password-toggle">
                <label for="current_password">كلمة المرور الحالية</label>
                <input type="password" id="current_password" name="current_password" required>
                <i class="fas fa-eye toggle-icon" onclick="togglePassword('current_password')"></i>
            </div>
            
            <div class="form-group password-toggle">
                <label for="new_password">كلمة المرور الجديدة</label>
                <input type="password" id="new_password" name="new_password" required>
                <i class="fas fa-eye toggle-icon" onclick="togglePassword('new_password')"></i>
            </div>
            
            <div class="form-group password-toggle">
                <label for="confirm_password">تأكيد كلمة المرور الجديدة</label>
                <input type="password" id="confirm_password" name="confirm_password" required>
                <i class="fas fa-eye toggle-icon" onclick="togglePassword('confirm_password')"></i>
            </div>
            
            <button type="submit" class="btn">تغيير كلمة المرور</button>
        </form>
        
        <a href="dashboard.php" class="back-link"><i class="fas fa-arrow-left"></i> العودة للوحة التحكم</a>
    </div>

    <script>
        function togglePassword(id) {
            const input = document.getElementById(id);
            const icon = input.nextElementSibling;
            
            if (input.type === 'password') {
                input.type = 'text';
                icon.classList.remove('fa-eye');
                icon.classList.add('fa-eye-slash');
            } else {
                input.type = 'password';
                icon.classList.remove('fa-eye-slash');
                icon.classList.add('fa-eye');
            }
        }
    </script>
</body>
</html>