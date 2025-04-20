<?php
// ملف: dashboard.php
session_start();
require 'config.php';

// التحقق من تسجيل الدخول
if (!isset($_SESSION['user_id'])) {
    header('Location: login.php');
    exit;
}

// استعلام لجلب المستخدمين
$stmt = $pdo->query("SELECT id, username, email, created_at FROM users ORDER BY created_at DESC LIMIT 5");
$users = $stmt->fetchAll(PDO::FETCH_ASSOC);
?>

<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Tableau de Bord</title>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <script src="js/users_stats.js"></script>
    
</head>
<body>
    <div class="container">
        <!-- Main Content -->
        <main>
            <div class="card">
                <h3>
                    <span>Derniers utilisateurs inscrits</span>
                    <button class="btn btn-primary" id="createUserBtn">
                        <i class="fas fa-plus"></i> Créer user
                    </button>
                </h3>
                <div class="table-responsive">
                    <table>
                        <thead>
                            <tr>
                                <th>ID</th>
                                <th>Nom d'utilisateur</th>
                                <th>Email</th>
                                <th>Date d'inscription</th>
                                <th>Actions</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php foreach ($users as $user): ?>
                            <tr>
                                <td><?php echo htmlspecialchars($user['id']); ?></td>
                                <td><?php echo htmlspecialchars($user['username']); ?></td>
                                <td><?php echo htmlspecialchars($user['email']); ?></td>
                                <td><?php echo date('d/m/Y H:i', strtotime($user['created_at'])); ?></td>
                                <td>
                                    <a href="edit_user.php?id=<?php echo $user['id']; ?>" 
                                       class="action-link"
                                       title="Modifier cet utilisateur">
                                        <i class="fas fa-edit"></i> Modifier
                                    </a>
                                </td>
                            </tr>
                            <?php endforeach; ?>
                        </tbody>
                    </table>
                </div>
            </div>
        </main>
    </div>

    <!-- Modal pour créer un nouvel utilisateur -->
    <div class="modal" id="createUserModal">
        <div class="modal-content">
            <div class="modal-header">
                <h3>Créer un nouvel utilisateur</h3>
                <button class="close-btn" id="closeModalBtn">&times;</button>
            </div>
            <form id="createUserForm" action="create_user.php" method="POST">
                <div class="form-group">
                    <label for="username">Nom d'utilisateur</label>
                    <input type="text" id="username" name="username" class="form-control" required>
                </div>
                <div class="form-group">
                    <label for="email">Email</label>
                    <input type="email" id="email" name="email" class="form-control" required>
                </div>
                <div class="form-group">
                    <label for="password">Mot de passe</label>
                    <input type="password" id="password" name="password" class="form-control" required>
                </div>
                <div class="form-group">
                    <label for="confirm_password">Confirmer le mot de passe</label>
                    <input type="password" id="confirm_password" name="confirm_password" class="form-control" required>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn" id="cancelBtn">Annuler</button>
                    <button type="submit" class="btn btn-primary">Créer</button>
                </div>
            </form>
        </div>
    </div>
                                <style>
        /* Reset CSS */
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
            font-family: 'Arial', sans-serif;
        }
        
        body {
            background-color: #f5f5f5;
            color: #333;
            display: flex;
            min-height: 100vh;
        }
        
        /* Main Container */
        .container {
            width: 1300px;
            max-width: 1300px;
            margin: 40px auto;
        }
        
        /* Card Design */
        .card {
            background-color: white;
            border-radius: 8px;
            box-shadow: 0 4px 8px rgba(0,0,0,0.1);
            padding: 45px;
            margin-bottom: 30px;
            transition: transform 0.3s ease;
        }
        
        .card h3 {
            color: #2c3e50;
            margin-bottom: 20px;
            padding-bottom: 10px;
            border-bottom: 1px solid #eee;
            font-size: 22px;
            display: flex;
            justify-content: space-between;
            align-items: center;
        }
        
        /* Table Styles */
        table {
            width: 100%;
            border-collapse: collapse;
            margin-top: 15px;
        }
        
        th, td {
            padding: 12px 15px;
            text-align: left;
            border-bottom: 1px solid #ddd;
        }
        
        th {
            background-color: #f8f9fa;
            font-weight: 600;
            color: #495057;
        }
        
        tr:hover {
            background-color: #f8f9fa;
        }
        
        /* Action Links */
        .action-link {
            color: #3498db;
            text-decoration: none;
            transition: color 0.3s;
            display: inline-flex;
            align-items: center;
            gap: 5px;
        }
        
        .action-link:hover {
            color: #2874a6;
            text-decoration: underline;
        }
        
        /* Button Styles */
        .btn {
            padding: 8px 16px;
            border-radius: 4px;
            cursor: pointer;
            font-size: 14px;
            transition: all 0.3s;
            border: none;
            display: inline-flex;
            align-items: center;
            gap: 8px;
        }
        
        .btn-primary {
            background-color: #3498db;
            color: white;
        }
        
        .btn-primary:hover {
            background-color: #2874a6;
        }
        
        /* Modal Styles */
        .modal {
            display: none;
            position: fixed;
            top: 0;
            left: 0;
            width: 100%;
            height: 100%;
            background-color: rgba(0,0,0,0.5);
            z-index: 1000;
            justify-content: center;
            align-items: center;
        }
        
        .modal-content {
            background-color: white;
            padding: 30px;
            border-radius: 8px;
            width: 500px;
            max-width: 90%;
            box-shadow: 0 4px 20px rgba(0,0,0,0.2);
        }
        
        .modal-header {
            display: flex;
            justify-content: space-between;
            align-items: center;
            margin-bottom: 20px;
            padding-bottom: 10px;
            border-bottom: 1px solid #eee;
        }
        
        .modal-header h3 {
            margin: 0;
            color: #2c3e50;
        }
        
        .close-btn {
            background: none;
            border: none;
            font-size: 24px;
            cursor: pointer;
            color: #7f8c8d;
        }
        
        .form-group {
            margin-bottom: 20px;
        }
        
        .form-group label {
            display: block;
            margin-bottom: 8px;
            font-weight: 600;
            color: #2c3e50;
        }
        
        .form-control {
            width: 100%;
            padding: 10px;
            border: 1px solid #ddd;
            border-radius: 4px;
            font-size: 14px;
        }
        
        .form-control:focus {
            border-color: #3498db;
            outline: none;
        }
        
        .modal-footer {
            display: flex;
            justify-content: flex-end;
            gap: 10px;
            margin-top: 20px;
            padding-top: 20px;
            border-top: 1px solid #eee;
        }
        
        /* Responsive Design */
        @media (max-width: 768px) {
            .container {
                width: 95%;
            }
            
            th, td {
                padding: 8px 10px;
                font-size: 14px;
            }
            
            .modal-content {
                padding: 20px;
            }
        }
    </style>
    <script>
        // Gestion de la modal
        const createUserBtn = document.getElementById('createUserBtn');
        const createUserModal = document.getElementById('createUserModal');
        const closeModalBtn = document.getElementById('closeModalBtn');
        const cancelBtn = document.getElementById('cancelBtn');
        
        // Ouvrir la modal
        createUserBtn.addEventListener('click', () => {
            createUserModal.style.display = 'flex';
        });
        
        // Fermer la modal
        function closeModal() {
            createUserModal.style.display = 'none';
        }
        
        closeModalBtn.addEventListener('click', closeModal);
        cancelBtn.addEventListener('click', closeModal);
        
        // Fermer la modal en cliquant à l'extérieur
        window.addEventListener('click', (e) => {
            if (e.target === createUserModal) {
                closeModal();
            }
        });
        
        // Validation du formulaire
        const createUserForm = document.getElementById('createUserForm');
        
        createUserForm.addEventListener('submit', function(e) {
            const password = document.getElementById('password').value;
            const confirmPassword = document.getElementById('confirm_password').value;
            
            if (password !== confirmPassword) {
                e.preventDefault();
                alert('Les mots de passe ne correspondent pas!');
                return false;
            }
            
            // Ici vous pouvez ajouter une requête AJAX si vous voulez soumettre le formulaire sans recharger la page
            // return true pour soumettre normalement
            return true;
        });
    </script>
</body>
</html>