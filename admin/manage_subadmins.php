<?php
$page_title = "Manage Sub-Admins";
include '../config/db.php';
include 'includes/admin_layout.php';

// Handle form submissions
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    if (isset($_POST['create_subadmin'])) {
        // Create new sub-admin
        $name = $_POST['name'];
        $email = $_POST['email'];
        $password = password_hash($_POST['password'], PASSWORD_DEFAULT);
        
        try {
            $stmt = $pdo->prepare("INSERT INTO sub_admins (name, email, password) VALUES (?, ?, ?)");
            $stmt->execute([$name, $email, $password]);
            $success = "Sub-admin created successfully!";
        } catch (PDOException $e) {
            $error = "Error creating sub-admin: " . $e->getMessage();
        }
    } elseif (isset($_POST['update_permissions'])) {
        // Update permissions
        $sub_admin_id = $_POST['sub_admin_id'];
        $permissions = $_POST['permissions'] ?? [];
        
        try {
            // Delete existing permissions
            $stmt = $pdo->prepare("DELETE FROM sub_admin_permissions WHERE sub_admin_id = ?");
            $stmt->execute([$sub_admin_id]);
            
            // Insert new permissions
            $stmt = $pdo->prepare("INSERT INTO sub_admin_permissions (sub_admin_id, permission, allowed) VALUES (?, ?, ?)");
            foreach ([
                'upload_offer',
                'manage_credit_cards',
                'pending_withdraw_requests',
                'pending_wallet_approvals',
                'all_categories',
                'add_new_category',
                'contact_messages'
            ] as $permission) {
                $allowed = in_array($permission, $permissions) ? 1 : 0;
                $stmt->execute([$sub_admin_id, $permission, $allowed]);
            }
            
            $success = "Permissions updated successfully!";
        } catch (PDOException $e) {
            $error = "Error updating permissions: " . $e->getMessage();
        }
    } elseif (isset($_POST['toggle_status'])) {
        // Toggle sub-admin status
        $sub_admin_id = $_POST['sub_admin_id'];
        $current_status = $_POST['current_status'];
        $new_status = $current_status === 'active' ? 'inactive' : 'active';
        
        try {
            $stmt = $pdo->prepare("UPDATE sub_admins SET status = ? WHERE id = ?");
            $stmt->execute([$new_status, $sub_admin_id]);
            $success = "Sub-admin status updated successfully!";
        } catch (PDOException $e) {
            $error = "Error updating status: " . $e->getMessage();
        }
    }
}

// Fetch all sub-admins
$sub_admins = [];
try {
    $stmt = $pdo->query("SELECT * FROM sub_admins ORDER BY created_at DESC");
    $sub_admins = $stmt->fetchAll(PDO::FETCH_ASSOC);
} catch (PDOException $e) {
    $error = "Error fetching sub-admins: " . $e->getMessage();
}

// Fetch permissions for a specific sub-admin (if requested)
$sub_admin_permissions = [];
if (isset($_GET['sub_admin_id'])) {
    try {
        $stmt = $pdo->prepare("SELECT permission, allowed FROM sub_admin_permissions WHERE sub_admin_id = ?");
        $stmt->execute([$_GET['sub_admin_id']]);
        $permissions = $stmt->fetchAll(PDO::FETCH_ASSOC);
        foreach ($permissions as $permission) {
            $sub_admin_permissions[$permission['permission']] = $permission['allowed'];
        }
    } catch (PDOException $e) {
        $error = "Error fetching permissions: " . $e->getMessage();
    }
}
?>

<div class="container-fluid">
    <h2>Manage Sub-Admins</h2>
    
    <?php if (isset($success)): ?>
        <div class="alert alert-success"><?php echo $success; ?></div>
    <?php endif; ?>
    
    <?php if (isset($error)): ?>
        <div class="alert alert-danger"><?php echo $error; ?></div>
    <?php endif; ?>
    
    <!-- Create Sub-Admin Form -->
    <div class="card mb-4">
        <div class="card-header">
            <h5>Create New Sub-Admin</h5>
        </div>
        <div class="card-body">
            <form method="POST">
                <input type="hidden" name="create_subadmin" value="1">
                <div class="row">
                    <div class="col-md-4">
                        <div class="mb-3">
                            <label for="name" class="form-label">Full Name</label>
                            <input type="text" class="form-control" id="name" name="name" required>
                        </div>
                    </div>
                    <div class="col-md-4">
                        <div class="mb-3">
                            <label for="email" class="form-label">Email</label>
                            <input type="email" class="form-control" id="email" name="email" required>
                        </div>
                    </div>
                    <div class="col-md-4">
                        <div class="mb-3">
                            <label for="password" class="form-label">Password</label>
                            <input type="password" class="form-control" id="password" name="password" required>
                        </div>
                    </div>
                </div>
                <button type="submit" class="btn btn-primary">Create Sub-Admin</button>
            </form>
        </div>
    </div>
    
    <!-- Sub-Admins List -->
    <div class="card">
        <div class="card-header">
            <h5>Sub-Admins</h5>
        </div>
        <div class="card-body">
            <?php if (empty($sub_admins)): ?>
                <p>No sub-admins found.</p>
            <?php else: ?>
                <div class="table-responsive">
                    <table class="table table-striped">
                        <thead>
                            <tr>
                                <th>Name</th>
                                <th>Email</th>
                                <th>Status</th>
                                <th>Created At</th>
                                <th>Actions</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php foreach ($sub_admins as $sub_admin): ?>
                                <tr>
                                    <td><?php echo htmlspecialchars($sub_admin['name']); ?></td>
                                    <td><?php echo htmlspecialchars($sub_admin['email']); ?></td>
                                    <td>
                                        <span class="badge bg-<?php echo $sub_admin['status'] === 'active' ? 'success' : 'danger'; ?>">
                                            <?php echo ucfirst($sub_admin['status']); ?>
                                        </span>
                                    </td>
                                    <td><?php echo $sub_admin['created_at']; ?></td>
                                    <td>
                                        <div class="btn-group" role="group">
                                            <a href="?sub_admin_id=<?php echo $sub_admin['id']; ?>" class="btn btn-sm btn-primary">Manage Permissions</a>
                                            <form method="POST" class="d-inline">
                                                <input type="hidden" name="toggle_status" value="1">
                                                <input type="hidden" name="sub_admin_id" value="<?php echo $sub_admin['id']; ?>">
                                                <input type="hidden" name="current_status" value="<?php echo $sub_admin['status']; ?>">
                                                <button type="submit" class="btn btn-sm btn-<?php echo $sub_admin['status'] === 'active' ? 'warning' : 'success'; ?>">
                                                    <?php echo $sub_admin['status'] === 'active' ? 'Deactivate' : 'Activate'; ?>
                                                </button>
                                            </form>
                                        </div>
                                    </td>
                                </tr>
                            <?php endforeach; ?>
                        </tbody>
                    </table>
                </div>
            <?php endif; ?>
        </div>
    </div>
    
    <!-- Permissions Management -->
    <?php if (isset($_GET['sub_admin_id'])): ?>
        <?php
        // Get sub-admin details
        $sub_admin_detail = null;
        try {
            $stmt = $pdo->prepare("SELECT * FROM sub_admins WHERE id = ?");
            $stmt->execute([$_GET['sub_admin_id']]);
            $sub_admin_detail = $stmt->fetch(PDO::FETCH_ASSOC);
        } catch (PDOException $e) {
            $error = "Error fetching sub-admin: " . $e->getMessage();
        }
        ?>
        
        <?php if ($sub_admin_detail): ?>
            <div class="card mt-4">
                <div class="card-header">
                    <h5>Manage Permissions for <?php echo htmlspecialchars($sub_admin_detail['name']); ?></h5>
                </div>
                <div class="card-body">
                    <form method="POST">
                        <input type="hidden" name="update_permissions" value="1">
                        <input type="hidden" name="sub_admin_id" value="<?php echo $sub_admin_detail['id']; ?>">
                        
                        <div class="row">
                            <div class="col-md-6">
                                <div class="form-check mb-3">
                                    <input class="form-check-input" type="checkbox" name="permissions[]" value="upload_offer" id="upload_offer" <?php echo isset($sub_admin_permissions['upload_offer']) && $sub_admin_permissions['upload_offer'] ? 'checked' : ''; ?>>
                                    <label class="form-check-label" for="upload_offer">
                                        Upload Offer
                                    </label>
                                </div>
                                
                                <div class="form-check mb-3">
                                    <input class="form-check-input" type="checkbox" name="permissions[]" value="manage_credit_cards" id="manage_credit_cards" <?php echo isset($sub_admin_permissions['manage_credit_cards']) && $sub_admin_permissions['manage_credit_cards'] ? 'checked' : ''; ?>>
                                    <label class="form-check-label" for="manage_credit_cards">
                                        Manage Credit Cards
                                    </label>
                                </div>
                                
                                <div class="form-check mb-3">
                                    <input class="form-check-input" type="checkbox" name="permissions[]" value="pending_withdraw_requests" id="pending_withdraw_requests" <?php echo isset($sub_admin_permissions['pending_withdraw_requests']) && $sub_admin_permissions['pending_withdraw_requests'] ? 'checked' : ''; ?>>
                                    <label class="form-check-label" for="pending_withdraw_requests">
                                        Pending Withdraw Requests
                                    </label>
                                </div>
                                
                                <div class="form-check mb-3">
                                    <input class="form-check-input" type="checkbox" name="permissions[]" value="pending_wallet_approvals" id="pending_wallet_approvals" <?php echo isset($sub_admin_permissions['pending_wallet_approvals']) && $sub_admin_permissions['pending_wallet_approvals'] ? 'checked' : ''; ?>>
                                    <label class="form-check-label" for="pending_wallet_approvals">
                                        Pending Wallet Approvals
                                    </label>
                                </div>
                                
                                <div class="form-check mb-3">
                                    <input class="form-check-input" type="checkbox" name="permissions[]" value="manage_offers" id="manage_offers" <?php echo isset($sub_admin_permissions['manage_offers']) && $sub_admin_permissions['manage_offers'] ? 'checked' : ''; ?>>
                                    <label class="form-check-label" for="manage_offers">
                                        Manage Offers
                                    </label>
                                </div>
                            </div>
                            
                            <div class="col-md-6">
                                <div class="form-check mb-3">
                                    <input class="form-check-input" type="checkbox" name="permissions[]" value="all_categories" id="all_categories" <?php echo isset($sub_admin_permissions['all_categories']) && $sub_admin_permissions['all_categories'] ? 'checked' : ''; ?>>
                                    <label class="form-check-label" for="all_categories">
                                        All Categories
                                    </label>
                                </div>
                                
                                <div class="form-check mb-3">
                                    <input class="form-check-input" type="checkbox" name="permissions[]" value="add_new_category" id="add_new_category" <?php echo isset($sub_admin_permissions['add_new_category']) && $sub_admin_permissions['add_new_category'] ? 'checked' : ''; ?>>
                                    <label class="form-check-label" for="add_new_category">
                                        Add New Category
                                    </label>
                                </div>
                                
                                <div class="form-check mb-3">
                                    <input class="form-check-input" type="checkbox" name="permissions[]" value="contact_messages" id="contact_messages" <?php echo isset($sub_admin_permissions['contact_messages']) && $sub_admin_permissions['contact_messages'] ? 'checked' : ''; ?>>
                                    <label class="form-check-label" for="contact_messages">
                                        Contact Messages
                                    </label>
                                </div>
                            </div>
                        </div>
                        
                        <button type="submit" class="btn btn-primary">Update Permissions</button>
                        <a href="manage_subadmins.php" class="btn btn-secondary">Cancel</a>
                    </form>
                </div>
            </div>
        <?php endif; ?>
    <?php endif; ?>
</div>