<?php
session_start();
if (isset($_SESSION['user_id'])) { header("Location: index.php"); exit(); }
require_once '../config/db.php';

$error = '';
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $email = $_POST['email'];
    $password = $_POST['password'];

    $stmt = $pdo->prepare("SELECT * FROM users WHERE email = ?");
    $stmt->execute([$email]);
    $user = $stmt->fetch();

    if ($user && password_verify($password, $user['password'])) {
        $_SESSION['user_id'] = $user['id'];
        $_SESSION['username'] = $user['username'];
        $_SESSION['role'] = $user['role'];
        $_SESSION['org_id'] = $user['org_id'];
        
        if (empty($user['org_id'])) {
            header("Location: setup_org.php");
        } else {
            header("Location: index.php");
        }
        exit();
    } else {
        $error = "Invalid credentials.";
    }
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Login - ScrumFlow</title>
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
            color: #fff;
        }
        .login-card {
            background: rgba(30, 41, 59, 0.7);
            backdrop-filter: blur(10px);
            border: 1px solid rgba(255, 255, 255, 0.1);
            border-radius: 16px;
            padding: 3rem;
            width: 100%;
            max-width: 420px;
            box-shadow: 0 25px 50px -12px rgba(0, 0, 0, 0.5);
        }
        
        /* ICON STYLING */
        .input-group-text {
            background: rgba(15, 23, 42, 0.6);
            border: 1px solid rgba(255, 255, 255, 0.1);
            border-right: none;
            color: #94a3b8; /* Subtle icon color */
        }
        
        /* INPUT STYLING */
        .form-control {
            background: rgba(15, 23, 42, 0.6) !important;
            border: 1px solid rgba(255, 255, 255, 0.1) !important;
            border-left: none !important; /* Remove border between icon and input */
            color: #fff !important; /* Force White Text */
            padding: 0.8rem 1rem;
        }
        .form-control:focus {
            background: rgba(15, 23, 42, 0.8) !important;
            border-color: #6366f1 !important;
            box-shadow: none;
        }
        /* Highlight icon when input is focused */
        .form-control:focus + .input-group-text, 
        .input-group:focus-within .input-group-text {
            border-color: #6366f1;
            color: #6366f1;
        }

        /* CHROME AUTOFILL FIX */
        input:-webkit-autofill,
        input:-webkit-autofill:hover, 
        input:-webkit-autofill:focus, 
        input:-webkit-autofill:active {
            -webkit-box-shadow: 0 0 0 30px #0f172a inset !important;
            -webkit-text-fill-color: white !important;
        }

        .btn-primary {
            background: linear-gradient(135deg, #6366f1, #4338ca);
            border: none;
            padding: 0.8rem;
            font-weight: 600;
        }
        .brand-logo { font-size: 2rem; font-weight: 800; text-align: center; margin-bottom: 2rem; }
        .text-label { color: #cbd5e1; font-size: 0.85rem; font-weight: 600; text-transform: uppercase; margin-bottom: 5px; display: block; }
    </style>
</head>
<body>

    <div class="login-card">
        <div class="brand-logo">
            <i class="fas fa-layer-group text-primary"></i> ScrumFlow
        </div>
        
        <?php if($error): ?>
            <div class="alert alert-danger border-0 bg-danger bg-opacity-25 text-white small mb-4"><?= $error ?></div>
        <?php endif; ?>

        <form method="POST">
            <div class="mb-4">
                <span class="text-label">Email Address</span>
                <div class="input-group">
                    <span class="input-group-text"><i class="fas fa-envelope"></i></span>
                    <input type="email" name="email" class="form-control" placeholder="name@company.com" required>
                </div>
            </div>

            <div class="mb-4">
                <span class="text-label">Password</span>
                <div class="input-group">
                    <span class="input-group-text"><i class="fas fa-lock"></i></span>
                    <input type="password" name="password" class="form-control" placeholder="••••••••" required>
                </div>
            </div>

            <button type="submit" class="btn btn-primary w-100 rounded-pill mb-3">
                Sign In
            </button>
            
            <div class="text-center">
                <span class="text-muted small">New to ScrumFlow?</span>
                <a href="register.php" class="text-primary text-decoration-none fw-bold ms-1">Create Account</a>
            </div>
        </form>
    </div>

</body>
</html>