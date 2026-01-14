<?php
require_once '../config/db.php';
$error = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $username = trim($_POST['username']);
    $email = trim($_POST['email']);
    $password = $_POST['password'];
    $confirm_pass = $_POST['confirm_password'];

    if ($password !== $confirm_pass) {
        $error = "Passwords do not match!";
    } else {
        $stmt = $pdo->prepare("SELECT id FROM users WHERE email = ?");
        $stmt->execute([$email]);
        if ($stmt->fetch()) {
            $error = "Email already registered.";
        } else {
            $hashed_pass = password_hash($password, PASSWORD_DEFAULT);
            try {
                $stmt = $pdo->prepare("INSERT INTO users (username, email, password, role, org_id) VALUES (?, ?, ?, 'member', NULL)");
                $stmt->execute([$username, $email, $hashed_pass]);
                header("Location: login.php?msg=registered");
                exit();
            } catch (PDOException $e) {
                $error = "Registration failed.";
            }
        }
    }
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Join ScrumFlow</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css" rel="stylesheet">
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;600;800&display=swap" rel="stylesheet">
    <style>
        body {
            font-family: 'Inter', sans-serif;
            background-color: #0f172a;
            background-image: radial-gradient(at 0% 0%, hsla(253,16%,7%,1) 0, transparent 50%), 
                              radial-gradient(at 50% 0%, hsla(225,39%,30%,1) 0, transparent 50%);
            height: 100vh;
            display: flex;
            align-items: center;
            justify-content: center;
            color: #fff;
        }
        .register-card {
            background: rgba(30, 41, 59, 0.7);
            backdrop-filter: blur(10px);
            border: 1px solid rgba(255, 255, 255, 0.1);
            border-radius: 16px;
            padding: 2.5rem;
            width: 100%;
            max-width: 450px;
            box-shadow: 0 25px 50px -12px rgba(0, 0, 0, 0.5);
        }
        
        .input-group-text {
            background: rgba(15, 23, 42, 0.6);
            border: 1px solid rgba(255, 255, 255, 0.1);
            border-right: none;
            color: #94a3b8;
        }
        
        .form-control {
            background: rgba(15, 23, 42, 0.6) !important;
            border: 1px solid rgba(255, 255, 255, 0.1) !important;
            border-left: none !important;
            color: #fff !important;
            padding: 0.8rem 1rem;
        }
        .form-control:focus {
            background: rgba(15, 23, 42, 0.8) !important;
            border-color: #6366f1 !important;
            box-shadow: none;
        }
        .form-control:focus + .input-group-text, 
        .input-group:focus-within .input-group-text {
            border-color: #6366f1;
            color: #6366f1;
        }

        input:-webkit-autofill, input:-webkit-autofill:hover, input:-webkit-autofill:focus {
            -webkit-box-shadow: 0 0 0 30px #0f172a inset !important;
            -webkit-text-fill-color: white !important;
        }
        .btn-primary {
            background: linear-gradient(135deg, #6366f1, #4338ca);
            border: none;
            padding: 0.8rem;
            font-weight: 600;
        }
        .text-label { color: #cbd5e1; font-size: 0.85rem; font-weight: 600; text-transform: uppercase; margin-bottom: 5px; display: block; }
    </style>
</head>
<body>

    <div class="register-card">
        <h3 class="text-center mb-4 fw-bold"><i class="fas fa-layer-group text-primary"></i> ScrumFlow</h3>
        
        <?php if($error): ?>
            <div class="alert alert-danger border-0 bg-danger bg-opacity-25 text-white small mb-4"><?= $error ?></div>
        <?php endif; ?>

        <form method="POST">
            <div class="mb-3">
                <span class="text-label">Username</span>
                <div class="input-group">
                    <span class="input-group-text"><i class="fas fa-user"></i></span>
                    <input type="text" name="username" class="form-control" required>
                </div>
            </div>
            
            <div class="mb-3">
                <span class="text-label">Email Address</span>
                <div class="input-group">
                    <span class="input-group-text"><i class="fas fa-envelope"></i></span>
                    <input type="email" name="email" class="form-control" required>
                </div>
            </div>

            <div class="mb-3">
                <span class="text-label">Password</span>
                <div class="input-group">
                    <span class="input-group-text"><i class="fas fa-lock"></i></span>
                    <input type="password" name="password" class="form-control" required>
                </div>
            </div>
            
            <div class="mb-4">
                <span class="text-label">Confirm Password</span>
                <div class="input-group">
                    <span class="input-group-text"><i class="fas fa-check"></i></span>
                    <input type="password" name="confirm_password" class="form-control" required>
                </div>
            </div>

            <button type="submit" class="btn btn-primary w-100 rounded-pill">Create Account</button>
            <div class="text-center mt-3"><a href="login.php" class="text-primary text-decoration-none">Login</a></div>
        </form>
    </div>

</body>
</html>