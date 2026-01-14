<?php
if (session_status() === PHP_SESSION_NONE) { session_start(); }
$user_role = $_SESSION['role'] ?? 'member'; 
$user_name = $_SESSION['username'] ?? 'Guest';
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>ScrumFlow</title>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;600;700&display=swap" rel="stylesheet">
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    
    <style>
        /* Global Dark Theme Variables */
        :root { 
            --sidebar-bg: #0f172a; 
            --main-bg: linear-gradient(135deg, #1e293b 0%, #0f172a 100%);
        }
        
        body { 
            background: var(--main-bg); 
            font-family: 'Inter', sans-serif; 
            color: #f8fafc;
            min-height: 100vh;
        }

        /* Sidebar Styling */
        .sidebar { 
            min-height: 100vh; 
            background: var(--sidebar-bg); 
            border-right: 1px solid rgba(255,255,255,0.05);
            width: 250px; 
            position: fixed; 
            z-index: 1000;
        }

        .main-content { margin-left: 250px; padding: 2rem; }

        /* Navigation Links */
        .nav-link { color: #94a3b8; font-weight: 500; margin-bottom: 5px; border-radius: 8px; transition: 0.2s; }
        .nav-link:hover { background: rgba(255,255,255,0.05); color: #fff; transform: translateX(5px); }
        .nav-link i { width: 25px; }

        /* Modern Card Global Style */
        .modern-card { transition: transform 0.2s; }
        .modern-card:hover { transform: translateY(-3px); }
    </style>
</head>
<body>

<div class="sidebar d-flex flex-column p-4">
    <h3 class="text-center mb-4 fw-bold text-white">
        <i class="fas fa-layer-group text-primary"></i> ScrumFlow
    </h3>
    
    <hr class="border-secondary opacity-25">
    
    <ul class="nav nav-pills flex-column mb-auto">
        <li class="nav-item">
            <a href="index.php" class="nav-link">
                <i class="fas fa-columns"></i> Board
            </a>
        </li>
        
        <?php if($user_role === 'owner' || $user_role === 'manager'): ?>
            <li class="nav-item">
                <a href="team.php" class="nav-link">
                    <i class="fas fa-users-cog"></i> Team
                </a>
            </li>
        <?php endif; ?>
    </ul>
    
    <div class="mt-auto">
        <div class="p-3 rounded bg-dark bg-opacity-50 border border-secondary border-opacity-25 mb-3">
            <small class="text-muted d-block text-uppercase" style="font-size: 0.7rem;">Signed in as</small>
            <strong class="text-white"><?= htmlspecialchars($user_name); ?></strong>
        </div>
        <a href="logout.php" class="btn btn-outline-danger w-100 btn-sm">
            <i class="fas fa-sign-out-alt me-2"></i> Logout
        </a>
    </div>
</div>
<div class="main-content">