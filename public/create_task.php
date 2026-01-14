<?php
session_start();
require_once '../config/db.php';

// Auth Check
if (!isset($_SESSION['user_id']) || empty($_SESSION['org_id'])) {
    header("Location: login.php");
    exit();
}

$org_id = $_SESSION['org_id'];

// Handle POST
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $title = trim($_POST['title']);
    $desc = trim($_POST['description']);
    $points = (int)$_POST['story_points'];
    $assigned_to = (int)$_POST['assigned_to'];

    $stmt = $pdo->prepare("INSERT INTO tasks (title, description, story_points, status, assigned_to, org_id, created_at) VALUES (?, ?, ?, 'backlog', ?, ?, NOW())");
    $stmt->execute([$title, $desc, $points, $assigned_to, $org_id]);
    
    header("Location: index.php");
    exit();
}

// Fetch Team Members
$stmt = $pdo->prepare("SELECT id, username FROM users WHERE org_id = ?");
$stmt->execute([$org_id]);
$members = $stmt->fetchAll();
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <title>New Task - ScrumFlow</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;600;800&display=swap" rel="stylesheet">
    <style>
        body {
            font-family: 'Inter', sans-serif;
            background-color: #0f172a;
            background-image: radial-gradient(at 0% 0%, hsla(253,16%,7%,1) 0, transparent 50%), 
                              radial-gradient(at 50% 0%, hsla(225,39%,30%,1) 0, transparent 50%);
            height: 100vh; display: flex; align-items: center; justify-content: center; color: white;
        }
        .form-card {
            background: rgba(30, 41, 59, 0.8); backdrop-filter: blur(10px);
            border: 1px solid rgba(255, 255, 255, 0.1); border-radius: 16px; padding: 2.5rem; width: 100%; max-width: 600px;
        }
        .form-control, .form-select {
            background: rgba(15, 23, 42, 0.6); border: 1px solid rgba(255, 255, 255, 0.1); color: white;
        }
        .form-control:focus, .form-select:focus {
            background: rgba(15, 23, 42, 0.8); border-color: #6366f1; color: white; box-shadow: none;
        }
        label { color: #94a3b8; font-size: 0.85rem; font-weight: bold; margin-bottom: 0.5rem; text-transform: uppercase; }
    </style>
</head>
<body>

<div class="form-card shadow-lg">
    <h3 class="mb-4 fw-bold">Create New Task</h3>
    <form method="POST">
        <div class="mb-3">
            <label>Task Title</label>
            <input type="text" name="title" class="form-control p-3" placeholder="e.g. Fix Login Bug" required>
        </div>

        <div class="mb-3">
            <label>Description</label>
            <textarea name="description" class="form-control p-3" rows="3" placeholder="Details..."></textarea>
        </div>

        <div class="row">
            <div class="col-md-6 mb-3">
                <label>Effort (Points)</label>
                <input type="number" name="story_points" class="form-control p-3" value="1" min="1" max="13">
            </div>
            <div class="col-md-6 mb-3">
                <label>Assignee</label>
                <select name="assigned_to" class="form-select p-3">
                    <option value="<?= $_SESSION['user_id'] ?>">Assign to Me</option>
                    <?php foreach ($members as $m): ?>
                        <option value="<?= $m['id'] ?>"><?= htmlspecialchars($m['username']) ?></option>
                    <?php endforeach; ?>
                </select>
            </div>
        </div>

        <div class="mt-4 d-flex gap-3">
            <button class="btn btn-primary flex-grow-1 py-3 fw-bold rounded-3" style="background: linear-gradient(135deg, #6366f1, #4338ca); border:none;">
                Create Task
            </button>
            <a href="index.php" class="btn btn-outline-light py-3 rounded-3 px-4">Cancel</a>
        </div>
    </form>
</div>

</body>
</html>