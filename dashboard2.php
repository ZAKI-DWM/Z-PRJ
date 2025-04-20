<?php
// ملف: dashboard2.php
session_start();
require 'config.php';

// التحقق من تسجيل الدخول
if (!isset($_SESSION['user_id'])) {
    header('Location: login.php');
    exit;
}

// Get the contentpage parameter from the URL
$contentpage = isset($_GET['contentpage']) ? $_GET['contentpage'] : 'Tableau_de_Bord.php';
$user_id = $_SESSION['user_id'];
?>

<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Tableau de Bord</title>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
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
            overflow-x: hidden;
        }
        
        /* Header Styles */
        .header {
            background-color: #2c3e50;
            color: white;
            padding: 15px 20px;
            display: flex;
            justify-content: space-between;
            align-items: center;
            box-shadow: 0 2px 5px rgba(0,0,0,0.1);
            position: fixed;
            top: 0;
            left: 0;
            right: 0;
            z-index: 1000;
            height: 60px;
        }
        
        .header .logo {
            font-size: 22px;
            font-weight: bold;
        }
        
        .header .user-info {
            display: flex;
            align-items: center;
            gap: 15px;
        }
        
        /* Sidebar Styles */
        .sidebar {
            background-color: #34495e;
            color: white;
            width: 250px;
            height: calc(100vh - 60px);
            position: fixed;
            top: 60px;
            left: 0;
            transition: all 0.3s;
            overflow-y: auto;
            z-index: 999;
        }
        
        .sidebar.collapsed {
            left: -250px;
        }
        
        .sidebar-menu {
            list-style: none;
            padding: 20px 0;
        }
        
        .sidebar-menu li {
            margin-bottom: 5px;
        }
        
        .sidebar-menu a {
            color: #ecf0f1;
            text-decoration: none;
            display: flex;
            align-items: center;
            padding: 12px 20px;
            transition: all 0.3s;
        }
        
        .sidebar-menu a:hover, .sidebar-menu a.active {
            background-color: #2c3e50;
            border-left: 4px solid #3498db;
        }
        
        .sidebar-menu i {
            margin-right: 10px;
            font-size: 18px;
        }
        
        /* Main Content */
        .main-content {
            margin-left: 250px;
            margin-top: 60px;
            padding: 20px;
            transition: all 0.3s;
            min-height: calc(100vh - 60px);
        }
        
        .main-content.expanded {
            margin-left: 0;
        }
        
        /* Toggle Button */
        .toggle-btn {
            background: none;
            border: none;
            color: white;
            font-size: 20px;
            cursor: pointer;
            display: none;
        }
        
        /* Content Area */
        #contentpage {
            min-height: calc(100vh - 100px);
        }
        
        /* Loader Animation */
        .loader {
            border: 5px solid #f3f3f3;
            border-top: 5px solid #3498db;
            border-radius: 50%;
            width: 50px;
            height: 50px;
            animation: spin 1s linear infinite;
            margin: 20px auto;
        }
        
        @keyframes spin {
            0% { transform: rotate(0deg); }
            100% { transform: rotate(360deg); }
        }
        
        /* Responsive Styles */
        @media (max-width: 768px) {
            .sidebar {
                left: -250px;
            }
            
            .sidebar.show {
                left: 0;
            }
            
            .main-content {
                margin-left: 0;
            }
            
            .toggle-btn {
                display: block;
            }
        }

        .user-info {
            display: flex;
            align-items: center;
            gap: 5px;
            flex-direction: column;
        }

        .user-info span {
            font-weight: bold;
        }

        .user-info small {
            color: rgb(182, 182, 182);
            opacity: 0.8;
            font-size: 0.8em;
            display: block;
        }
    </style>
</head>
<body>
    <!-- Header Section -->
    <header class="header">
        <div class="logo">Tableau de Bord</div>
        <button class="toggle-btn" id="toggleBtn">
            <i class="fas fa-bars"></i>
        </button>
        <div class="user-info">
            <?php
            // جلب بيانات المستخدم المسجل دخوله
            $stmt = $pdo->prepare("SELECT username, email FROM users WHERE id = ?");
            $stmt->execute([$user_id]);
            $current_user = $stmt->fetch();
            
            if ($current_user) {
                echo '<span>' . htmlspecialchars($current_user['username']) . '</span>';
                echo '<small>' . htmlspecialchars($current_user['email']) . '</small>';
            }
            ?>
        </div>
    </header>
    
    <!-- Sidebar Section -->
    <aside class="sidebar" id="sidebar">
        <ul class="sidebar-menu">
            <li>
                <a href="Tableau_de_Bord.php?user_id=<?php echo $user_id; ?>" class="active load-page-link">
                    <i class="fas fa-tachometer-alt"></i>
                    <span>Tableau de Bord</span>
                </a>
            </li>
            <li>
                <a href="users_stats.php?user_id=<?php echo $user_id; ?>" class="load-page-link">
                    <i class="fas fa-users"></i>
                    <span>Gestion des Utilisateurs</span>
                </a>
            </li>
            <li>
                <a href="content_management.php?user_id=<?php echo $user_id; ?>" class="load-page-link">
                    <i class="fas fa-edit"></i>
                    <span>Publication de Contenu</span>
                </a>
            </li>
            <li>
                <a href="profile.php?user_id=<?php echo $user_id; ?>" class="load-page-link">
                    <i class="fas fa-cog"></i>
                    <span>Paramètres</span>
                </a>
            </li>
            <li>
                <a href="#" onclick="logout()">
                    <i class="fas fa-sign-out-alt"></i>
                    <span>Déconnexion</span>
                </a>
            </li>
        </ul>
    </aside>
      
    <!-- Main Content Section -->
    <main class="main-content" id="mainContent">
        <div class="px-6 py-8 mt-20" id="contentpage">
            <!-- Content will be loaded here dynamically -->
            <?php 
            // Load initial content
            if(file_exists($contentpage)) {
                include $contentpage;
            } else {
                echo '<div class="alert alert-danger">Page not found: ' . htmlspecialchars($contentpage) . '</div>';
            }
            ?>
        </div>
    </main>

    <!-- jQuery and Bootstrap JS -->
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    
    <script>
        // Toggle Sidebar Functionality
        const toggleBtn = document.getElementById('toggleBtn');
        const sidebar = document.getElementById('sidebar');
        const mainContent = document.getElementById('mainContent');
        
        toggleBtn.addEventListener('click', () => {
            sidebar.classList.toggle('show');
            mainContent.classList.toggle('expanded');
            
            // Change icon on toggle
            const icon = toggleBtn.querySelector('i');
            if (sidebar.classList.contains('show')) {
                icon.classList.remove('fa-bars');
                icon.classList.add('fa-times');
            } else {
                icon.classList.remove('fa-times');
                icon.classList.add('fa-bars');
            }
        });
        
        // Close menu when clicking outside (for mobile)
        document.addEventListener('click', (e) => {
            if (window.innerWidth <= 768) {
                if (!sidebar.contains(e.target) && e.target !== toggleBtn) {
                    sidebar.classList.remove('show');
                    mainContent.classList.remove('expanded');
                    const icon = toggleBtn.querySelector('i');
                    icon.classList.remove('fa-times');
                    icon.classList.add('fa-bars');
                }
            }
        });

        // Function to load pages dynamically
        function loadPage(page) {
            // Show loading animation
            $("#contentpage").html('<div class="loader"></div>');

            // Save the current page in sessionStorage
            sessionStorage.setItem("currentPage", page);

            // Load the page content
            $.ajax({
                url: page,
                type: 'GET',
                success: function(data) {
                    $("#contentpage").html(data);
                    setActiveLink();
                    history.pushState({ page: page }, "", "dashboard2.php?contentpage=" + page);
                    
                    // Load specific JS for each page
                    loadPageScript(page);
                },
                error: function(xhr, status, error) {
                    $("#contentpage").html('<div class="alert alert-danger">Error loading page: ' + error + '</div>');
                }
            });
        }

        // Load specific JS for each page
        function loadPageScript(page) {
            const scripts = {
                'Tableau_de_Bord.php': 'js/Tableau_de_Bord.js',
                'users_stats.php': 'js/users_stats.js',
                'profile.php': 'js/profile.js',
            };

            for (const key in scripts) {
                if (page.includes(key)) {
                    $.getScript(scripts[key])
                        .fail(function(jqxhr, settings, exception) {
                            console.error('Failed to load script: ' + scripts[key], exception);
                        });
                    break;
                }
            }
        }

        // Set the active link in the sidebar
        function setActiveLink() {
            $(".sidebar-menu a").removeClass("active");
            const currentPage = sessionStorage.getItem("currentPage") || 'Tableau_de_Bord.php';
            
            $(".sidebar-menu a").each(function() {
                if ($(this).attr('href').includes(currentPage.split('?')[0])) {
                    $(this).addClass("active");
                }
            });
        }

        // Logout function
        function logout() {
            if (confirm("Êtes-vous sûr de vouloir vous déconnecter ?")) {
                sessionStorage.clear();
                window.location.href = "logout.php";
            }
        }

        // Initialize the page
        $(document).ready(function() {
            // Load the current page from sessionStorage or URL parameter
            const urlParams = new URLSearchParams(window.location.search);
            const contentPage = urlParams.get('contentpage') || sessionStorage.getItem("currentPage") || 'Tableau_de_Bord.php?user_id=<?php echo $user_id; ?>';
            
            if (!urlParams.get('contentpage')) {
                loadPage(contentPage);
            }

            // Handle navigation via sidebar links
            $(document).on("click", ".load-page-link", function(e) {
                e.preventDefault();
                loadPage($(this).attr("href"));
            });

            // Handle browser back/forward buttons
            window.onpopstate = function(event) {
                if (event.state && event.state.page) {
                    loadPage(event.state.page);
                }
            };
        });
    </script>
</body>
</html>