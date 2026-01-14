<?php
session_start();
require_once '../config/db.php';

// 1. Validate Session
if (!isset($_SESSION['user_id']) || empty($_SESSION['org_id'])) {
    header("Location: login.php");
    exit();
}

$task_id = $_GET['id'] ?? null;
$action  = $_GET['action'] ?? null;
$org_id  = $_SESSION['org_id'];
$role    = $_SESSION['role'];
$user_id = $_SESSION['user_id'];

if (!$task_id || !$action) {
    header("Location: index.php");
    exit();
}

$sql = "";
$params = [];

// 2. Logic Switch
switch ($action) {
    case 'start':
        // Anyone in the org can start a task
        $sql = "UPDATE tasks SET status = 'in-progress' WHERE id = ? AND org_id = ?";
        $params = [$task_id, $org_id];
        break;

    case 'mark_test':
        // Anyone can mark for QA (usually the assignee)
        $sql = "UPDATE tasks SET status = 'testing' WHERE id = ? AND org_id = ?";
        $params = [$task_id, $org_id];
        break;

    case 'approve':
        // ONLY Managers/Owners
        if ($role === 'manager' || $role === 'owner') {
            $sql = "UPDATE tasks SET status = 'completed' WHERE id = ? AND org_id = ?";
            $params = [$task_id, $org_id];
        }
        break;

    case 'reject':
        // ONLY Managers/Owners
        if ($role === 'manager' || $role === 'owner') {
            $sql = "UPDATE tasks SET status = 'in-progress' WHERE id = ? AND org_id = ?";
            $params = [$task_id, $org_id];
        }
        break;

    case 'delete':
        // ONLY Managers/Owners
        if ($role === 'manager' || $role === 'owner') {
            $sql = "DELETE FROM tasks WHERE id = ? AND org_id = ?";
            $params = [$task_id, $org_id];
        }
        break;
}

// 3. Execute
if ($sql) {
    $stmt = $pdo->prepare($sql);
    $stmt->execute($params);
}

// 4. Redirect
header("Location: index.php");
exit();
?>