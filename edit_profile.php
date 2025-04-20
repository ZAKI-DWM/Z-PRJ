<?php
session_start();
require 'config.php';

if (!isset($_SESSION['user_id'])) {
    header('Location: login.php');
    exit;
}

// جلب بيانات المستخدم الحالية
$stmt = $pdo->prepare("SELECT * FROM users WHERE id = ?");
$stmt->execute([$_SESSION['user_id']]);
$user = $stmt->fetch();

$error = '';
$success = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $username = $_POST['username'];
    $email = $_POST['email'];
    $current_password = $_POST['current_password'];

    // التحقق من كلمة المرور الحالية
    if (!password_verify($current_password, $user['password'])) {
        $error = 'كلمة المرور الحالية غير صحيحة';
    } else {
        try {
            // التحقق من عدم تكرار اسم المستخدم أو البريد الإلكتروني
            $stmt = $pdo->prepare("SELECT id FROM users WHERE (username = ? OR email = ?) AND id != ?");
            $stmt->execute([$username, $email, $_SESSION['user_id']]);
            $existing_user = $stmt->fetch();

            if ($existing_user) {
                $error = 'اسم المستخدم أو البريد الإلكتروني موجود بالفعل';
            } elseif (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
                $error = 'البريد الإلكتروني غير صالح';
            } else {
                // تحديث البيانات
                $stmt = $pdo->prepare("UPDATE users SET username = ?, email = ? WHERE id = ?");
                $stmt->execute([$username, $email, $_SESSION['user_id']]);

                // تحديث بيانات الجلسة
                $_SESSION['username'] = $username;
                
                $success = 'تم تحديث الملف الشخصي بنجاح.';
            }
        } catch (PDOException $e) {
            $error = 'حدث خطأ أثناء تحديث البيانات: ' . $e->getMessage();
        }
    }
}

// جلب البيانات المحدثة
$stmt = $pdo->prepare("SELECT * FROM users WHERE id = ?");
$stmt->execute([$_SESSION['user_id']]);
$user = $stmt->fetch();
?>

<!DOCTYPE html>
<html lang="ar" dir="rtl">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>تعديل الملف الشخصي</title>
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
        .profile-container {
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
        input[type="text"],
        input[type="email"],
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
        .verification-status {
            font-size: 14px;
            margin-top: 5px;
        }
        .verified {
            color: #5cb85c;
        }
        .not-verified {
            color: #d9534f;
        }
    </style>
</head>
<body>
    <div class="profile-container">
        <h2><i class="fas fa-user-edit"></i> تعديل الملف الشخصي</h2>
        
        <?php if ($error): ?>
            <div class="error"><?php echo $error; ?></div>
        <?php endif; ?>
        
        <?php if ($success): ?>
            <div class="success"><?php echo $success; ?></div>
        <?php endif; ?>
        
        <form action="edit_profile.php" method="post">
            <div class="form-group">
                <label for="username">اسم المستخدم</label>
                <input type="text" id="username" name="username" value="<?php echo htmlspecialchars($user['username']); ?>" required>
            </div>
            
            <div class="form-group">
                <label for="email">البريد الإلكتروني</label>
                <input type="email" id="email" name="email" value="<?php echo htmlspecialchars($user['email']); ?>" required>
            </div>
            
            <div class="form-group password-toggle">
                <label for="current_password">كلمة المرور الحالية (مطلوبة للتأكيد)</label>
                <input type="password" id="current_password" name="current_password" required>
                <i class="fas fa-eye toggle-icon" onclick="togglePassword('current_password')"></i>
            </div>
            
            <button type="submit" class="btn">حفظ التغييرات</button>
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