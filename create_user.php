<?php
// ملف: create_user.php
session_start();
require 'config.php';

// التحقق من أن الطلب POST
if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    header('Location: dashboard2.php');
    exit;
}

// التحقق من تسجيل الدخول
if (!isset($_SESSION['user_id'])) {
    header('Location: login.php');
    exit;
}

// جلب البيانات من النموذج
$username = trim($_POST['username']);
$email = trim($_POST['email']);
$password = $_POST['password'];
$confirm_password = $_POST['confirm_password'];

// التحقق من صحة البيانات
$errors = [];

// التحقق من عدم وجود حقول فارغة
if (empty($username)) {
    $errors[] = "Le nom d'utilisateur est requis";
}

if (empty($email)) {
    $errors[] = "L'email est requis";
} elseif (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
    $errors[] = "Format d'email invalide";
}

if (empty($password)) {
    $errors[] = "Le mot de passe est requis";
} elseif (strlen($password) < 8) {
    $errors[] = "Le mot de passe doit contenir au moins 8 caractères";
}

if ($password !== $confirm_password) {
    $errors[] = "Les mots de passe ne correspondent pas";
}

// التحقق من عدم وجود أخطاء
if (!empty($errors)) {
    $_SESSION['create_user_errors'] = $errors;
    header('Location: dashboard2.php');
    exit;
}

try {
    // التحقق من أن اسم المستخدم أو البريد الإلكتروني غير مستخدم مسبقاً
    $stmt = $pdo->prepare("SELECT id FROM users WHERE username = ? OR email = ?");
    $stmt->execute([$username, $email]);
    
    if ($stmt->rowCount() > 0) {
        $errors[] = "Le nom d'utilisateur ou l'email est déjà utilisé";
        $_SESSION['create_user_errors'] = $errors;
        header('Location: dashboard2.php');
        exit;
    }
    
    // تشفير كلمة المرور
    $hashed_password = password_hash($password, PASSWORD_DEFAULT);
    
    // إدراج المستخدم الجديد في قاعدة البيانات
    $stmt = $pdo->prepare("INSERT INTO users (username, email, password, created_at) VALUES (?, ?, ?, NOW())");
    $stmt->execute([$username, $email, $hashed_password]);
    
    // رسالة نجاح
    $_SESSION['create_user_success'] = "L'utilisateur a été créé avec succès";
    header('Location: dashboard2.php');
    
} catch (PDOException $e) {
    // في حالة حدوث خطأ في قاعدة البيانات
    $errors[] = "Une erreur s'est produite lors de la création de l'utilisateur";
    $_SESSION['create_user_errors'] = $errors;
    header('Location: dashboard2.php');
    exit;
}