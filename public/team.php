<?php
session_start();
require_once '../config/db.php';

// Security: Only Managers/Owners
if (!isset($_SESSION['user_id']) || $_SESSION['role'] === 'member') {
    die("Access Denied.");
}

$org_id = $_SESSION['org_id'];

// Handle Role Updates
if (isset($_POST['update_role']) && $_SESSION['role'] === 'owner') {
    $target_uid = $_POST['user_id'];
    $new_role = $_POST['new_role'];
    
    // Safety: Don't demote yourself
    if ($target_uid != $_SESSION['user_id']) {
        $stmt = $pdo->prepare("UPDATE users SET role = ? WHERE id = ? AND org_id = ?");
        $stmt->execute([$new_role, $target_uid, $org_id]);
    }
}

// Fetch Users
$stmt = $pdo->prepare("SELECT * FROM users WHERE org_id = ? ORDER BY role ASC");
$stmt->execute([$org_id]);
$users = $stmt->fetchAll();

include '../templates/header.php';
?>

<div class="container py-5">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <h2 class="text-white fw-bold">Team Management</h2>
        <span class="badge bg-primary fs-6">Org ID: <?= $org_id ?></span>
    </div>
    
    <div class="card border-0 shadow-lg" style="background: rgba(30, 41, 59, 0.6); backdrop-filter: blur(10px);">
        <div class="card-body p-0">
            <table class="table table-dark table-hover mb-0 align-middle">
                <thead class="bg-dark text-secondary text-uppercase small">
                    <tr>
                        <th class="p-3 ps-4">User</th>
                        <th class="p-3">Email</th>
                        <th class="p-3">Current Role</th>
                        <?php if($_SESSION['role'] === 'owner'): ?>
                            <th class="p-3 text-end pe-4">Actions</th>
                        <?php endif; ?>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach ($users as $u): ?>
                    <tr>
                        <td class="p-3 ps-4 fw-bold">
                            <i class="fas fa-user-circle me-2 text-secondary"></i>
                            <?= htmlspecialchars($u['username']) ?>
                        </td>
                        <td class="p-3 text-secondary"><?= htmlspecialchars($u['email']) ?></td>
                        <td class="p-3">
                            <?php 
                                $badgeColor = match($u['role']) {
                                    'owner' => 'bg-info text-dark',
                                    'manager' => 'bg-success',
                                    default => 'bg-secondary'
                                };
                            ?>
                            <span class="badge <?= $badgeColor ?>"><?= ucfirst($u['role']) ?></span>
                        </td>
                        
                        <?php if($_SESSION['role'] === 'owner'): ?>
                        <td class="p-3 text-end pe-4">
                            <?php if($u['role'] !== 'owner'): ?>
                                <form method="POST" class="d-flex justify-content-end gap-2">
                                    <input type="hidden" name="user_id" value="<?= $u['id'] ?>">
                                    <select name="new_role" class="form-select form-select-sm bg-dark text-white border-secondary" style="width: 120px;">
                                        <option value="member" <?= $u['role']=='member'?'selected':'' ?>>Member</option>
                                        <option value="manager" <?= $u['role']=='manager'?'selected':'' ?>>Manager</option>
                                    </select>
                                    <button name="update_role" class="btn btn-sm btn-outline-light">Save</button>
                                </form>
                            <?php else: ?>
                                <span class="text-muted small">Owner</span>
                            <?php endif; ?>
                        </td>
                        <?php endif; ?>
                    </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
        </div>
    </div>
</div>

<?php include '../templates/footer.php'; ?>