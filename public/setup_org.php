<?php
session_start();
require_once '../config/db.php';

// 1. Security Checks
// Must be logged in
if (!isset($_SESSION['user_id'])) { 
    header("Location: login.php"); 
    exit(); 
}

// Must NOT already have an organization
if (!empty($_SESSION['org_id'])) { 
    header("Location: index.php"); 
    exit(); 
}

$error = '';


// LOGIC 1: CREATE ORG (CEO Path)

if (isset($_POST['create_org'])) {
    $org_name = trim($_POST['org_name']);
    
    try {
        $pdo->beginTransaction();

        // Step A: Create the Organization
        $stmt = $pdo->prepare("INSERT INTO organizations (name, created_by) VALUES (?, ?)");
        $stmt->execute([$org_name, $_SESSION['user_id']]);
        $org_id = $pdo->lastInsertId();

        // Step B: Update the User to be Owner of this Org
        $stmt = $pdo->prepare("UPDATE users SET org_id = ?, role = 'owner' WHERE id = ?");
        $stmt->execute([$org_id, $_SESSION['user_id']]);

        $pdo->commit();

        // Step C: Update Session & Redirect
        $_SESSION['org_id'] = $org_id;
        $_SESSION['role'] = 'owner';
        header("Location: index.php");
        exit();

    } catch (Exception $e) {
        $pdo->rollBack();
        $error = "Error creating workspace: " . $e->getMessage();
    }
}


// LOGIC 2: JOIN ORG (Employee Path)

if (isset($_POST['join_org'])) {
    $target_id = $_POST['target_org_id'];

    // Step A: Check if Org ID exists
    $stmt = $pdo->prepare("SELECT id FROM organizations WHERE id = ?");
    $stmt->execute([$target_id]);
    
    if ($stmt->fetch()) {
        // Step B: Update User to join as Member
        $stmt = $pdo->prepare("UPDATE users SET org_id = ?, role = 'member' WHERE id = ?");
        $stmt->execute([$target_id, $_SESSION['user_id']]);

        // Step C: Update Session & Redirect
        $_SESSION['org_id'] = $target_id;
        $_SESSION['role'] = 'member';
        header("Location: index.php");
        exit();
    } else {
        $error = "Organization ID not found! Please check with your manager.";
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Setup Workspace - ScrumFlow</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css" rel="stylesheet">
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;600;800&display=swap" rel="stylesheet">
    
    <style>
        body {
            font-family: 'Inter', sans-serif;
            background-color: #0f172a;
         
            background-image: radial-gradient(at 0% 0%, hsla(253,16%,7%,1) 0, transparent 50%), 
                              radial-gradient(at 50% 0%, hsla(225,39%,30%,1) 0, transparent 50%), 
                              radial-gradient(at 100% 0%, hsla(339,49%,30%,1) 0, transparent 50%);
            height: 100vh;
            display: flex;
            align-items: center;
            justify-content: center;
            color: white;
        }

        .setup-card {
            background: rgba(30, 41, 59, 0.7); 
            backdrop-filter: blur(10px);
            border: 1px solid rgba(255, 255, 255, 0.1);
            border-radius: 16px;
            padding: 3rem;
            width: 100%;
            max-width: 900px;
            box-shadow: 0 25px 50px -12px rgba(0, 0, 0, 0.5);
        }

        .divider {
            border-left: 1px solid rgba(255, 255, 255, 0.1);
        }
       
        @media (max-width: 768px) {
            .divider { border-left: none; border-top: 1px solid rgba(255, 255, 255, 0.1); padding-top: 2rem; margin-top: 2rem; }
        }

        .form-control {
            background: rgba(15, 23, 42, 0.6) !important;
            border: 1px solid rgba(255, 255, 255, 0.1) !important;
            color: #fff !important;
            padding: 0.8rem 1rem;
        }
        
        .form-control:focus {
            background: rgba(15, 23, 42, 0.8) !important;
            border-color: #6366f1 !important;
            box-shadow: 0 0 0 4px rgba(99, 102, 241, 0.1);
        }

        .btn-primary {
            background: linear-gradient(135deg, #6366f1, #4338ca);
            border: none;
            padding: 0.8rem;
            font-weight: 600;
        }

        .btn-success {
            background: linear-gradient(135deg, #10b981, #059669);
            border: none;
            padding: 0.8rem;
            font-weight: 600;
        }
    </style>
</head>
<body>

<div class="container">
    <div class="setup-card shadow-lg mx-auto">
        <h2 class="text-center mb-5 fw-bold">
            Welcome, <?= htmlspecialchars($_SESSION['username']) ?>!
        </h2>
        
        <?php if($error): ?>
            <div class="alert alert-danger border-0 bg-danger bg-opacity-25 text-white mb-4">
                <i class="fas fa-exclamation-triangle me-2"></i> <?= $error ?>
            </div>
        <?php endif; ?>
        
        <div class="row g-5">
            <div class="col-md-6">
                <h4 class="text-primary mb-3"><i class="fas fa-rocket me-2"></i>Create New Workspace</h4>
                <p class="text-secondary small mb-4">You will become the <strong>Owner (CEO)</strong> of this new organization. Perfect for starting a new project.</p>
                
                <form method="POST">
                    <label class="small text-uppercase fw-bold text-secondary mb-2">Organization Name</label>
                    <input type="text" name="org_name" class="form-control mb-3" placeholder="e.g. Stark Industries" required>
                    <button name="create_org" class="btn btn-primary w-100 rounded-pill">
                        Launch Workspace <i class="fas fa-arrow-right ms-2"></i>
                    </button>
                </form>
            </div>

            <div class="col-md-6 divider">
                <h4 class="text-success mb-3"><i class="fas fa-users me-2"></i>Join Existing Team</h4>
                <p class="text-secondary small mb-4">Enter the <strong>Organization ID</strong> provided by your manager to join their board instantly.</p>
                
                <form method="POST">
                    <label class="small text-uppercase fw-bold text-secondary mb-2">Organization ID</label>
                    <input type="number" name="target_org_id" class="form-control mb-3" placeholder="e.g. 1" required>
                    <button name="join_org" class="btn btn-success w-100 rounded-pill">
                        Join Team <i class="fas fa-sign-in-alt ms-2"></i>
                    </button>
                </form>
            </div>
        </div>

        <div class="text-center mt-5">
            <a href="logout.php" class="text-secondary text-decoration-none small">
                <i class="fas fa-sign-out-alt me-1"></i> Cancel & Logout
            </a>
        </div>
    </div>
</div>

</body>
</html>