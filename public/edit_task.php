<?php
session_start();
require_once '../config/db.php';

if (!isset($_SESSION['user_id'], $_GET['id'])) { header("Location: index.php"); exit(); }

$task_id = $_GET['id'];
$org_id = $_SESSION['org_id'];
$user_id = $_SESSION['user_id'];
$role = $_SESSION['role'];

// 1. Fetch Task
$stmt = $pdo->prepare("SELECT * FROM tasks WHERE id = ? AND org_id = ?");
$stmt->execute([$task_id, $org_id]);
$task = $stmt->fetch();

if (!$task) { die("Task not found."); }

// 2. Security: Members can only edit their own tasks
if ($role === 'member' && $task['assigned_to'] != $user_id) {
    die("<div style='color:white; background:#0f172a; height:100vh; display:flex; align-items:center; justify-content:center;'><h3>Access Denied: You can only edit your own tasks.</h3></div>");
}

// 3. Handle Update
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $title = trim($_POST['title']);
    $desc = trim($_POST['description']);
    $points = (int)$_POST['points'];
    $assigned = (int)$_POST['assigned_to'];

    $stmt = $pdo->prepare("UPDATE tasks SET title=?, description=?, story_points=?, assigned_to=? WHERE id=?");
    $stmt->execute([$title, $desc, $points, $assigned, $task_id]);
    
    header("Location: index.php");
    exit();
}

// Fetch Users
$stmt = $pdo->prepare("SELECT id, username FROM users WHERE org_id = ?");
$stmt->execute([$org_id]);
$users = $stmt->fetchAll();
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <title>Edit Task</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;600;800&display=swap" rel="stylesheet">
    <style>
        body {
            font-family: 'Inter', sans-serif;
            background-color: #0f172a;
            background-image: radial-gradient(at 0% 0%, hsla(253,16%,7%,1) 0, transparent 50%), 
                              radial-gradient(at 100% 0%, hsla(339,49%,30%,1) 0, transparent 50%);
            height: 100vh; display: flex; align-items: center; justify-content: center; color: white;
        }
        .form-card {
            background: rgba(30, 41, 59, 0.9); backdrop-filter: blur(10px);
            border: 1px solid rgba(255, 255, 255, 0.1); border-radius: 16px; padding: 2.5rem; width: 100%; max-width: 600px;
        }
        .form-control, .form-select {
            background: rgba(15, 23, 42, 0.6); border: 1px solid rgba(255, 255, 255, 0.1); color: white;
        }
        label { color: #94a3b8; font-size: 0.85rem; font-weight: bold; margin-bottom: 0.5rem; text-transform: uppercase; }
    </style>
</head>
<body>

<div class="form-card shadow-lg">
    <h3 class="mb-4 fw-bold">Edit Task</h3>
    <form method="POST">
        <div class="mb-3">
            <label>Title</label>
            <input type="text" name="title" class="form-control p-2" value="<?= htmlspecialchars($task['title']) ?>" required>
        </div>

        <div class="mb-3">
            <label>Description</label>
            <textarea name="description" class="form-control p-2" rows="3"><?= htmlspecialchars($task['description']) ?></textarea>
        </div>

        <div class="row">
            <div class="col-6 mb-3">
                <label>Points</label>
                <input type="number" name="points" class="form-control p-2" value="<?= $task['story_points'] ?>">
            </div>
            <div class="col-6 mb-3">
                <label>Assignee</label>
                <select name="assigned_to" class="form-select p-2">
                    <?php foreach($users as $u): ?>
                        <option value="<?= $u['id'] ?>" <?= $task['assigned_to'] == $u['id'] ? 'selected' : '' ?>>
                            <?= htmlspecialchars($u['username']) ?>
                        </option>
                    <?php endforeach; ?>
                </select>
            </div>
        </div>

        <div class="mt-4">
            <button class="btn btn-primary w-100 py-2 mb-2" style="background: linear-gradient(135deg, #6366f1, #4338ca); border:none;">Update Task</button>
            <a href="index.php" class="btn btn-outline-light w-100 py-2 mb-2">Cancel</a>
            
            <?php if($role !== 'member'): ?>
                <a href="task_action.php?id=<?= $task_id ?>&action=delete" class="btn btn-danger w-100 py-2" onclick="return confirm('Are you sure you want to delete this task? This cannot be undone.')">
                    Delete Task
                </a>
            <?php endif; ?>
        </div>
    </form>
</div>

</body>
</html>