<?php
session_start();
$page_title = "Manage Sliders";
include '../config/db.php';
include 'auth.php'; // Admin authentication check

// Handle form submissions
if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    if (isset($_POST['add_banner'])) {
        // Add new banner
        $title = $_POST['title'];
        $image_url = $_POST['image_url'];
        $redirect_url = $_POST['redirect_url'];
        $sequence_id = $_POST['sequence_id'] ?? 0;
        $is_active = isset($_POST['is_active']) ? 1 : 0;
        
        try {
            $stmt = $pdo->prepare("INSERT INTO banners (title, image_url, redirect_url, sequence_id, is_active) VALUES (?, ?, ?, ?, ?)");
            $stmt->execute([$title, $image_url, $redirect_url, $sequence_id, $is_active]);
            $success_message = "Banner added successfully!";
        } catch(PDOException $e) {
            $error_message = "Error adding banner: " . $e->getMessage();
        }
    } elseif (isset($_POST['update_banner'])) {
        // Update existing banner
        $id = $_POST['banner_id'];
        $title = $_POST['title'];
        $image_url = $_POST['image_url'];
        $redirect_url = $_POST['redirect_url'];
        $sequence_id = $_POST['sequence_id'] ?? 0;
        $is_active = isset($_POST['is_active']) ? 1 : 0;
        
        try {
            $stmt = $pdo->prepare("UPDATE banners SET title = ?, image_url = ?, redirect_url = ?, sequence_id = ?, is_active = ? WHERE id = ?");
            $stmt->execute([$title, $image_url, $redirect_url, $sequence_id, $is_active, $id]);
            $success_message = "Banner updated successfully!";
        } catch(PDOException $e) {
            $error_message = "Error updating banner: " . $e->getMessage();
        }
    } elseif (isset($_POST['delete_banner'])) {
        // Delete banner
        $id = $_POST['banner_id'];
        
        try {
            $stmt = $pdo->prepare("DELETE FROM banners WHERE id = ?");
            $stmt->execute([$id]);
            $success_message = "Banner deleted successfully!";
        } catch(PDOException $e) {
            $error_message = "Error deleting banner: " . $e->getMessage();
        }
    }
}

// Fetch all banners
try {
    $stmt = $pdo->query("SELECT * FROM banners ORDER BY sequence_id ASC, created_at DESC");
    $banners = $stmt->fetchAll(PDO::FETCH_ASSOC);
} catch(PDOException $e) {
    $error_message = "Error fetching banners: " . $e->getMessage();
    $banners = [];
}

// Fetch banner for editing if ID is provided
$edit_banner = null;
if (isset($_GET['edit']) && is_numeric($_GET['edit'])) {
    try {
        $stmt = $pdo->prepare("SELECT * FROM banners WHERE id = ?");
        $stmt->execute([$_GET['edit']]);
        $edit_banner = $stmt->fetch(PDO::FETCH_ASSOC);
    } catch(PDOException $e) {
        $error_message = "Error fetching banner: " . $e->getMessage();
    }
}

// Include admin layout
include 'includes/admin_layout.php';
?>

<div class="container-fluid">
    <div class="d-flex justify-content-between flex-wrap flex-md-nowrap align-items-center pt-3 pb-2 mb-3 border-bottom">
        <h1 class="h2">Manage Sliders</h1>
    </div>
    
    <?php if (isset($success_message)): ?>
        <div class="alert alert-success"><?php echo $success_message; ?></div>
    <?php endif; ?>
    
    <?php if (isset($error_message)): ?>
        <div class="alert alert-danger"><?php echo $error_message; ?></div>
    <?php endif; ?>
    
    <div class="row">
        <div class="col-md-8">
            <div class="card">
                <div class="card-header">
                    <h5 class="card-title mb-0">
                        <?php echo $edit_banner ? 'Edit Banner' : 'Add New Banner'; ?>
                    </h5>
                </div>
                <div class="card-body">
                    <form method="POST">
                        <input type="hidden" name="banner_id" value="<?php echo $edit_banner ? $edit_banner['id'] : ''; ?>">
                        
                        <div class="mb-3">
                            <label for="title" class="form-label">Banner Title</label>
                            <input type="text" class="form-control" id="title" name="title" 
                                   value="<?php echo $edit_banner ? htmlspecialchars($edit_banner['title']) : ''; ?>" required>
                        </div>
                        
                        <div class="mb-3">
                            <label for="image_url" class="form-label">Image URL</label>
                            <input type="url" class="form-control" id="image_url" name="image_url" 
                                   value="<?php echo $edit_banner ? htmlspecialchars($edit_banner['image_url']) : ''; ?>" required>
                            <div class="form-text">Enter the full URL to the banner image</div>
                        </div>
                        
                        <div class="mb-3">
                            <label for="redirect_url" class="form-label">Redirect URL</label>
                            <input type="url" class="form-control" id="redirect_url" name="redirect_url" 
                                   value="<?php echo $edit_banner ? htmlspecialchars($edit_banner['redirect_url']) : ''; ?>" required>
                            <div class="form-text">Enter the URL where users should be redirected when clicking the banner</div>
                        </div>
                        
                        <div class="mb-3">
                            <label for="sequence_id" class="form-label">Sequence ID</label>
                            <input type="number" class="form-control" id="sequence_id" name="sequence_id" 
                                   value="<?php echo $edit_banner ? $edit_banner['sequence_id'] : '0'; ?>" min="0">
                            <div class="form-text">Lower numbers appear first in the slider</div>
                        </div>
                        
                        <div class="mb-3 form-check">
                            <input type="checkbox" class="form-check-input" id="is_active" name="is_active" 
                                   <?php echo (!$edit_banner || $edit_banner['is_active']) ? 'checked' : ''; ?>>
                            <label class="form-check-label" for="is_active">Active</label>
                        </div>
                        
                        <button type="submit" name="<?php echo $edit_banner ? 'update_banner' : 'add_banner'; ?>" class="btn btn-primary">
                            <?php echo $edit_banner ? 'Update Banner' : 'Add Banner'; ?>
                        </button>
                        
                        <?php if ($edit_banner): ?>
                            <a href="manage_sliders.php" class="btn btn-secondary">Cancel</a>
                        <?php endif; ?>
                    </form>
                </div>
            </div>
        </div>
        
        <div class="col-md-4">
            <div class="card">
                <div class="card-header">
                    <h5 class="card-title mb-0">Preview</h5>
                </div>
                <div class="card-body">
                    <div id="bannerPreview" class="text-center">
                        <?php if ($edit_banner): ?>
                            <img src="<?php echo htmlspecialchars($edit_banner['image_url']); ?>" 
                                 alt="<?php echo htmlspecialchars($edit_banner['title']); ?>" 
                                 class="img-fluid" style="max-height: 200px;">
                            <p class="mt-2"><?php echo htmlspecialchars($edit_banner['title']); ?></p>
                        <?php else: ?>
                            <p class="text-muted">Fill in the form to see a preview</p>
                        <?php endif; ?>
                    </div>
                </div>
            </div>
        </div>
    </div>
    
    <div class="row mt-4">
        <div class="col-12">
            <div class="card">
                <div class="card-header">
                    <h5 class="card-title mb-0">Existing Banners</h5>
                </div>
                <div class="card-body">
                    <?php if (empty($banners)): ?>
                        <p class="text-muted">No banners found. Add your first banner using the form above.</p>
                    <?php else: ?>
                        <div class="table-responsive">
                            <table class="table table-striped">
                                <thead>
                                    <tr>
                                        <th>#</th>
                                        <th>Title</th>
                                        <th>Image</th>
                                        <th>Redirect URL</th>
                                        <th>Sequence</th>
                                        <th>Status</th>
                                        <th>Actions</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <?php foreach ($banners as $banner): ?>
                                        <tr>
                                            <td><?php echo $banner['id']; ?></td>
                                            <td><?php echo htmlspecialchars($banner['title']); ?></td>
                                            <td>
                                                <img src="<?php echo htmlspecialchars($banner['image_url']); ?>" 
                                                     alt="<?php echo htmlspecialchars($banner['title']); ?>" 
                                                     style="max-height: 50px; width: auto;">
                                            </td>
                                            <td>
                                                <a href="<?php echo htmlspecialchars($banner['redirect_url']); ?>" target="_blank">
                                                    <?php echo htmlspecialchars(substr($banner['redirect_url'], 0, 30)) . '...'; ?>
                                                </a>
                                            </td>
                                            <td><?php echo $banner['sequence_id']; ?></td>
                                            <td>
                                                <?php if ($banner['is_active']): ?>
                                                    <span class="badge bg-success">Active</span>
                                                <?php else: ?>
                                                    <span class="badge bg-secondary">Inactive</span>
                                                <?php endif; ?>
                                            </td>
                                            <td>
                                                <a href="manage_sliders.php?edit=<?php echo $banner['id']; ?>" class="btn btn-sm btn-primary">
                                                    <i class="fas fa-edit"></i>
                                                </a>
                                                <form method="POST" style="display: inline;" 
                                                      onsubmit="return confirm('Are you sure you want to delete this banner?')">
                                                    <input type="hidden" name="banner_id" value="<?php echo $banner['id']; ?>">
                                                    <button type="submit" name="delete_banner" class="btn btn-sm btn-danger">
                                                        <i class="fas fa-trash"></i>
                                                    </button>
                                                </form>
                                            </td>
                                        </tr>
                                    <?php endforeach; ?>
                                </tbody>
                            </table>
                        </div>
                    <?php endif; ?>
                </div>
            </div>
        </div>
    </div>
</div>

<script>
    // Update preview when form fields change
    document.addEventListener('DOMContentLoaded', function() {
        const titleInput = document.getElementById('title');
        const imageUrlInput = document.getElementById('image_url');
        const bannerPreview = document.getElementById('bannerPreview');
        
        function updatePreview() {
            const title = titleInput.value || 'Banner Title';
            const imageUrl = imageUrlInput.value;
            
            if (imageUrl) {
                bannerPreview.innerHTML = `
                    <img src="${imageUrl}" alt="${title}" class="img-fluid" style="max-height: 200px;">
                    <p class="mt-2">${title}</p>
                `;
            } else {
                bannerPreview.innerHTML = '<p class="text-muted">Fill in the form to see a preview</p>';
            }
        }
        
        if (titleInput && imageUrlInput) {
            titleInput.addEventListener('input', updatePreview);
            imageUrlInput.addEventListener('input', updatePreview);
        }
    });
</script>