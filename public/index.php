<?php
session_start();
require_once '../config/db.php';

// 1. Security & Traffic Cop
if (!isset($_SESSION['user_id'])) { 
    header("Location: login.php"); 
    exit(); 
}
if (empty($_SESSION['org_id'])) { 
    header("Location: setup_org.php"); 
    exit(); 
}

// 2. Setup Variables
$org_id = $_SESSION['org_id'];
$role = $_SESSION['role'];
$user_id = $_SESSION['user_id'];

// 3. Fetch Organization Details
$stmt = $pdo->prepare("SELECT name FROM organizations WHERE id = ?");
$stmt->execute([$org_id]);
$org = $stmt->fetch();
$org_name = $org['name'] ?? 'My Organization';

// 4. Define Kanban Columns
$columns = [
    'backlog'     => 'Backlog', 
    'in-progress' => 'In Progress', 
    'testing'     => 'Needs QA', 
    'completed'   => 'Done'
];

include '../templates/header.php';
?>

<div class="container-fluid py-4">
    
    <?php if ($role === 'owner'): ?>
        <div class="alert alert-info border-0 shadow-sm d-flex justify-content-between align-items-center mb-4" 
             style="background: rgba(56, 189, 248, 0.15); color: #38bdf8; border: 1px solid rgba(56, 189, 248, 0.2);">
            <span>
                <i class="fas fa-crown me-2"></i> 
                <strong>Admin Info:</strong> Your Organization ID is 
                <span class="badge bg-primary fs-6 mx-1"><?= $org_id ?></span> 
                Share this with your employees so they can join.
            </span>
        </div>
    <?php endif; ?>

    <div class="d-flex justify-content-between align-items-center mb-4">
        <div>
            <h2 class="fw-bold text-white mb-0"><?= htmlspecialchars($org_name) ?> Board</h2>
            <p class="text-secondary small">
                Logged in as: <strong><?= htmlspecialchars($_SESSION['username']) ?></strong> 
                <span class="badge bg-dark border border-secondary ms-2"><?= ucfirst($role) ?></span>
            </p>
        </div>
        
        <a href="create_task.php" class="btn btn-primary shadow-lg rounded-pill px-4">
            <i class="fas fa-plus me-2"></i> New Task
        </a>
    </div>

    <div class="row gx-3">
        <?php foreach ($columns as $statusKey => $title): ?>
            <div class="col-md-3">
                <div class="p-3 rounded-3 border border-secondary" 
                     style="background: rgba(30, 41, 59, 0.4); min-height: 70vh; backdrop-filter: blur(5px);">
                    
                    <h6 class="fw-bold text-uppercase text-secondary mb-3 small tracking-wide">
                        <?= $title ?>
                    </h6>
                    
                    <?php
                    // Fetch Tasks for this Column
                    $sql = "SELECT t.*, u.username 
                            FROM tasks t 
                            LEFT JOIN users u ON t.assigned_to = u.id 
                            WHERE t.status = ? AND t.org_id = ? 
                            ORDER BY t.created_at DESC";
                    $stmt = $pdo->prepare($sql);
                    $stmt->execute([$statusKey, $org_id]);
                    
                    while ($task = $stmt->fetch()):
                    ?>
                        <div class="modern-card p-3 mb-3 bg-white border-0 shadow-sm rounded-3">
                            
                            <div class="d-flex justify-content-between align-items-start">
                                <h6 class="fw-bold text-dark mb-1 text-truncate" style="max-width: 85%;">
                                    <?= htmlspecialchars($task['title']) ?>
                                </h6>
                                
                                <?php if ($role !== 'member' || $task['assigned_to'] == $user_id): ?>
                                    <a href="edit_task.php?id=<?= $task['id'] ?>" class="text-secondary small">
                                        <i class="fas fa-pencil-alt"></i>
                                    </a>
                                <?php endif; ?>
                            </div>

                            <small class="text-muted d-block mb-3">
                                <span class="badge bg-light text-dark border me-1">
                                    <?= $task['story_points'] ?> pts
                                </span>
                                <i class="far fa-user ms-1"></i> <?= $task['username'] ?? 'Unassigned' ?>
                            </small>
                            
                            <div class="d-grid gap-2">
                                <?php if ($statusKey == 'backlog'): ?>
                                    <a href="task_action.php?id=<?= $task['id'] ?>&action=start" class="btn btn-sm btn-outline-primary">
                                        Start Sprint
                                    </a>
                                
                                <?php elseif ($statusKey == 'in-progress'): ?>
                                    <a href="task_action.php?id=<?= $task['id'] ?>&action=mark_test" class="btn btn-sm btn-warning text-dark fw-bold">
                                        Send to QA
                                    </a>
                                
                                <?php elseif ($statusKey == 'testing'): ?>
                                    <?php if ($role == 'owner' || $role == 'manager'): ?>
                                        <div class="d-flex gap-2">
                                            <a href="task_action.php?id=<?= $task['id'] ?>&action=approve" class="btn btn-sm btn-success flex-grow-1">Approve</a>
                                            <a href="task_action.php?id=<?= $task['id'] ?>&action=reject" class="btn btn-sm btn-danger flex-grow-1">Reject</a>
                                        </div>
                                    <?php else: ?>
                                        <div class="badge bg-warning text-dark py-2 w-100">
                                            <i class="fas fa-clock me-1"></i> Under Review
                                        </div>
                                    <?php endif; ?>
                                <?php endif; ?>
                            </div>

                        </div>
                    <?php endwhile; ?>
                </div>
            </div>
        <?php endforeach; ?>
    </div>
</div>

<?php include '../templates/footer.php'; ?>