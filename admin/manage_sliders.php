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
        $video_url = $_POST['video_url'];
        $redirect_url = $_POST['redirect_url'];
        $sequence_id = $_POST['sequence_id'] ?? 0;
        $is_active = isset($_POST['is_active']) ? 1 : 0;
        $media_type = !empty($video_url) ? 'video' : 'image';
        
        try {
            $stmt = $pdo->prepare("INSERT INTO banners (title, image_url, video_url, media_type, redirect_url, sequence_id, is_active) VALUES (?, ?, ?, ?, ?, ?, ?)");
            $stmt->execute([$title, $image_url, $video_url, $media_type, $redirect_url, $sequence_id, $is_active]);
            $success_message = "Banner added successfully!";
        } catch(PDOException $e) {
            $error_message = "Error adding banner: " . $e->getMessage();
        }
    } elseif (isset($_POST['update_banner'])) {
        // Update existing banner
        $id = $_POST['banner_id'];
        $title = $_POST['title'];
        $image_url = $_POST['image_url'];
        $video_url = $_POST['video_url'];
        $redirect_url = $_POST['redirect_url'];
        $sequence_id = $_POST['sequence_id'] ?? 0;
        $is_active = isset($_POST['is_active']) ? 1 : 0;
        $media_type = !empty($video_url) ? 'video' : 'image';
        
        try {
            $stmt = $pdo->prepare("UPDATE banners SET title = ?, image_url = ?, video_url = ?, media_type = ?, redirect_url = ?, sequence_id = ?, is_active = ? WHERE id = ?");
            $stmt->execute([$title, $image_url, $video_url, $media_type, $redirect_url, $sequence_id, $is_active, $id]);
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

<style>
    :root {
        --primary-color: #6f42c1;
        --secondary-color: #5a32a3;
        --accent-color: #00c9a7;
        --light-bg: #f8f9fa;
        --dark-text: #212529;
        --light-text: #6c757d;
        --border-color: #dee2e6;
        --success-color: #20c997;
        --warning-color: #ffc107;
        --danger-color: #dc3545;
    }
    
    .slider-header {
        background: white;
        border-radius: 10px;
        padding: 20px;
        margin-bottom: 15px;
        box-shadow: 0 2px 10px rgba(0, 0, 0, 0.05);
    }
    
    .page-title {
        font-weight: 700;
        font-size: 1.75rem;
        margin-bottom: 0;
        color: var(--primary-color);
    }
    
    .card {
        border: none;
        border-radius: 10px;
        box-shadow: 0 2px 10px rgba(0, 0, 0, 0.05);
        margin-bottom: 15px;
        overflow: hidden;
    }
    
    .card-header {
        background: white;
        border-bottom: 1px solid var(--border-color);
        padding: 15px 20px;
    }
    
    .card-title {
        font-weight: 600;
        color: var(--dark-text);
        margin-bottom: 0;
        font-size: 1.25rem;
    }
    
    .form-control, .form-select {
        border-radius: 8px;
        padding: 8px 12px;
    }
    
    .form-control:focus, .form-select:focus {
        border-color: var(--primary-color);
        box-shadow: 0 0 0 0.25rem rgba(111, 66, 193, 0.25);
    }
    
    .form-label {
        font-weight: 500;
        color: var(--dark-text);
    }
    
    .form-check-input:checked {
        background-color: var(--primary-color);
        border-color: var(--primary-color);
    }
    
    .btn-primary {
        background: linear-gradient(135deg, var(--primary-color), var(--secondary-color));
        border: none;
        padding: 8px 16px;
        border-radius: 8px;
        font-weight: 500;
    }
    
    .btn-primary:hover {
        background: linear-gradient(135deg, var(--secondary-color), var(--primary-color));
    }
    
    .btn-secondary {
        background: #6c757d;
        border: none;
        padding: 8px 16px;
        border-radius: 8px;
        font-weight: 500;
    }
    
    .btn-sm {
        padding: 5px 10px;
        font-size: 0.8rem;
        border-radius: 5px;
    }
    
    .btn-danger {
        background: linear-gradient(135deg, #dc3545, #bd2130);
        border: none;
    }
    
    .badge-custom {
        padding: 0.5em 0.75em;
        font-weight: 500;
        border-radius: 20px;
        font-size: 0.8rem;
    }
    
    /* Status badges with black text */
    .badge-success {
        background-color: rgba(32, 201, 151, 0.15) !important;
        color: #000000 !important;
        border: 1px solid rgba(32, 201, 151, 0.3) !important;
    }
    
    .badge-secondary {
        background-color: rgba(108, 117, 125, 0.15) !important;
        color: #000000 !important;
        border: 1px solid rgba(108, 117, 125, 0.3) !important;
    }
    
    .table-container {
        overflow-x: auto;
    }
    
    .table {
        margin-bottom: 0;
    }
    
    .table thead {
        background-color: var(--light-bg);
    }
    
    .table th {
        font-weight: 600;
        color: var(--dark-text);
        border-bottom: 2px solid var(--border-color);
        padding: 12px 15px;
    }
    
    .table td {
        padding: 12px 15px;
        vertical-align: middle;
        border-color: var(--border-color);
    }
    
    .table-hover tbody tr:hover {
        background-color: rgba(111, 66, 193, 0.05);
    }
    
    .alert {
        border-radius: 8px;
        border: none;
        box-shadow: 0 2px 4px rgba(0, 0, 0, 0.05);
    }
    
    .alert-success {
        background-color: rgba(32, 201, 151, 0.1);
        color: var(--success-color);
    }
    
    .alert-danger {
        background-color: rgba(220, 53, 69, 0.1);
        color: var(--danger-color);
    }
    
    .preview-image {
        max-height: 200px;
        width: auto;
        border-radius: 8px;
        box-shadow: 0 2px 5px rgba(0, 0, 0, 0.1);
    }
    
    .banner-image {
        max-height: 50px;
        width: auto;
        border-radius: 4px;
    }
    
    .form-text {
        font-size: 0.8rem;
        color: var(--light-text);
    }
    
    /* Responsive styles */
    @media (max-width: 992px) {
        .page-title {
            font-size: 1.5rem;
        }
        
        .slider-header {
            padding: 15px;
        }
    }
    
    @media (max-width: 768px) {
        .slider-header, .card-header {
            padding: 12px 15px;
        }
        
        .page-title {
            font-size: 1.25rem;
            margin-bottom: 10px;
        }
        
        .card-title {
            font-size: 1.1rem;
        }
        
        .table th, .table td {
            padding: 10px 8px;
            font-size: 0.85rem;
        }
        
        .btn-sm {
            padding: 4px 8px;
            font-size: 0.7rem;
        }
        
        .form-control, .form-select {
            padding: 6px 10px;
            font-size: 0.85rem;
        }
        
        .preview-image {
            max-height: 150px;
        }
    }
    
    @media (max-width: 576px) {
        .slider-header, .card-header {
            padding: 10px 12px;
        }
        
        .page-title {
            font-size: 1.1rem;
        }
        
        .table th, .table td {
            padding: 8px 5px;
            font-size: 0.75rem;
        }
        
        .preview-image {
            max-height: 120px;
        }
        
        .form-control, .form-select {
            padding: 5px 8px;
            font-size: 0.8rem;
        }
        
        .btn-primary, .btn-secondary {
            padding: 6px 12px;
            font-size: 0.8rem;
        }
    }
    
    @media (max-width: 400px) {
        .table th, .table td {
            padding: 6px 3px;
            font-size: 0.7rem;
        }
        
        .preview-image {
            max-height: 100px;
        }
        
        .form-control, .form-select {
            padding: 4px 6px;
            font-size: 0.75rem;
        }
    }
</style>

<div class="container-fluid">
    <div class="slider-header">
        <h1 class="page-title">Manage Sliders</h1>
    </div>
    
    <?php if (isset($success_message)): ?>
        <div class="alert alert-success alert-dismissible fade show" role="alert">
            <i class="bi bi-check-circle-fill me-2"></i>
            <?php echo $success_message; ?>
            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
        </div>
    <?php endif; ?>
    
    <?php if (isset($error_message)): ?>
        <div class="alert alert-danger alert-dismissible fade show" role="alert">
            <i class="bi bi-exclamation-triangle-fill me-2"></i>
            <?php echo $error_message; ?>
            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
        </div>
    <?php endif; ?>
    
    <div class="row">
        <div class="col-md-8">
            <div class="card">
                <div class="card-header d-flex justify-content-between align-items-center">
                    <h5 class="card-title mb-0">
                        <i class="bi bi-image me-2"></i>
                        <?php echo $edit_banner ? 'Edit Banner' : 'Add New Banner'; ?>
                    </h5>
                    <?php if ($edit_banner): ?>
                        <a href="manage_sliders.php" class="btn btn-outline-secondary btn-sm">
                            <i class="bi bi-x-circle me-1"></i>Cancel
                        </a>
                    <?php endif; ?>
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
                            <label for="video_url" class="form-label">Video URL (Optional)</label>
                            <input type="url" class="form-control" id="video_url" name="video_url" 
                                   value="<?php echo $edit_banner ? htmlspecialchars($edit_banner['video_url']) : ''; ?>">
                            <div class="form-text">Enter the full URL to the banner video (MP4, WebM, etc.) - If provided, video will be used instead of image</div>
                        </div>
                        
                        <div class="mb-3">
                            <label for="redirect_url" class="form-label">Redirect URL</label>
                            <input type="url" class="form-control" id="redirect_url" name="redirect_url" 
                                   value="<?php echo $edit_banner ? htmlspecialchars($edit_banner['redirect_url']) : ''; ?>" required>
                            <div class="form-text">Enter the URL where users should be redirected when clicking the banner</div>
                        </div>
                        
                        <div class="row">
                            <div class="col-md-6 mb-3">
                                <label for="sequence_id" class="form-label">Sequence ID</label>
                                <input type="number" class="form-control" id="sequence_id" name="sequence_id" 
                                       value="<?php echo $edit_banner ? $edit_banner['sequence_id'] : '0'; ?>" min="0">
                                <div class="form-text">Lower numbers appear first in the slider</div>
                            </div>
                            <div class="col-md-6 mb-3">
                                <label class="form-label">Status</label>
                                <div class="form-check mt-2">
                                    <input type="checkbox" class="form-check-input" id="is_active" name="is_active" 
                                           <?php echo (!$edit_banner || $edit_banner['is_active']) ? 'checked' : ''; ?>>
                                    <label class="form-check-label" for="is_active">Active</label>
                                </div>
                            </div>
                        </div>
                        
                        <div class="d-flex gap-2 mt-4">
                            <button type="submit" name="<?php echo $edit_banner ? 'update_banner' : 'add_banner'; ?>" class="btn btn-primary">
                                <i class="bi bi-<?php echo $edit_banner ? 'check-circle' : 'plus-circle'; ?> me-1"></i>
                                <?php echo $edit_banner ? 'Update Banner' : 'Add Banner'; ?>
                            </button>
                            
                            <?php if ($edit_banner): ?>
                                <a href="manage_sliders.php" class="btn btn-secondary">
                                    <i class="bi bi-x-circle me-1"></i>Cancel
                                </a>
                            <?php endif; ?>
                        </div>
                    </form>
                </div>
            </div>
        </div>
        
        <div class="col-md-4">
            <div class="card">
                <div class="card-header">
                    <h5 class="card-title mb-0">
                        <i class="bi bi-eye me-2"></i>Preview
                    </h5>
                </div>
                <div class="card-body">
                    <div id="bannerPreview" class="text-center">
                        <?php if ($edit_banner): ?>
                            <img src="<?php echo htmlspecialchars($edit_banner['image_url']); ?>" 
                                 alt="<?php echo htmlspecialchars($edit_banner['title']); ?>" 
                                 class="preview-image img-fluid">
                            <p class="mt-2 fw-medium"><?php echo htmlspecialchars($edit_banner['title']); ?></p>
                        <?php else: ?>
                            <div class="py-5">
                                <i class="bi bi-image text-muted" style="font-size: 3rem;"></i>
                                <p class="text-muted mt-2 mb-0">Fill in the form to see a preview</p>
                            </div>
                        <?php endif; ?>
                    </div>
                </div>
            </div>
        </div>
    </div>
    
    <div class="row mt-4">
        <div class="col-12">
            <div class="card">
                <div class="card-header d-flex justify-content-between align-items-center">
                    <h5 class="card-title mb-0">
                        <i class="bi bi-list me-2"></i>Existing Banners
                    </h5>
                    <span class="badge bg-secondary"><?php echo count($banners); ?> banners</span>
                </div>
                <div class="card-body">
                    <?php if (empty($banners)): ?>
                        <div class="text-center py-5">
                            <i class="bi bi-images text-muted" style="font-size: 3rem;"></i>
                            <p class="mt-3 mb-0">No banners found. Add your first banner using the form above.</p>
                        </div>
                    <?php else: ?>
                        <div class="table-container">
                            <table class="table table-hover">
                                <thead>
                                    <tr>
                                        <th>#</th>
                                        <th>Title</th>
                                        <th>Media</th>
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
                                                <?php if ($banner['media_type'] === 'video' && !empty($banner['video_url'])): ?>
                                                    <video width="100" height="50" class="banner-image">
                                                        <source src="<?php echo htmlspecialchars($banner['video_url']); ?>" type="video/mp4">
                                                        Your browser does not support the video tag.
                                                    </video>
                                                    <span class="badge bg-primary">Video</span>
                                                <?php else: ?>
                                                    <img src="<?php echo htmlspecialchars($banner['image_url']); ?>" 
                                                         alt="<?php echo htmlspecialchars($banner['title']); ?>" 
                                                         class="banner-image">
                                                    <span class="badge bg-secondary">Image</span>
                                                <?php endif; ?>
                                            </td>
                                            <td>
                                                <a href="<?php echo htmlspecialchars($banner['redirect_url']); ?>" target="_blank" class="text-truncate d-inline-block" style="max-width: 150px;">
                                                    <?php echo htmlspecialchars(substr($banner['redirect_url'], 0, 30)) . '...'; ?>
                                                </a>
                                            </td>
                                            <td><?php echo $banner['sequence_id']; ?></td>
                                            <td>
                                                <?php if ($banner['is_active']): ?>
                                                    <span class="badge badge-custom badge-success">Active</span>
                                                <?php else: ?>
                                                    <span class="badge badge-custom badge-secondary">Inactive</span>
                                                <?php endif; ?>
                                            </td>
                                            <td>
                                                <div class="d-flex gap-1">
                                                    <a href="manage_sliders.php?edit=<?php echo $banner['id']; ?>" class="btn btn-primary btn-sm">
                                                        <i class="bi bi-pencil"></i>
                                                    </a>
                                                    <form method="POST" style="display: inline;" 
                                                          onsubmit="return confirm('Are you sure you want to delete this banner?')">
                                                        <input type="hidden" name="banner_id" value="<?php echo $banner['id']; ?>">
                                                        <button type="submit" name="delete_banner" class="btn btn-danger btn-sm">
                                                            <i class="bi bi-trash"></i>
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
        </div>
    </div>
</div>

<script>
    // Update preview when form fields change
    document.addEventListener('DOMContentLoaded', function() {
        const titleInput = document.getElementById('title');
        const imageUrlInput = document.getElementById('image_url');
        const videoUrlInput = document.getElementById('video_url');
        const bannerPreview = document.getElementById('bannerPreview');
        
        function updatePreview() {
            const title = titleInput.value || 'Banner Title';
            const imageUrl = imageUrlInput.value;
            const videoUrl = videoUrlInput.value;
            
            if (videoUrl) {
                // Show video preview with autoplay, loop, and muted attributes
                bannerPreview.innerHTML = `
                    <video autoplay loop muted playsinline class="preview-image img-fluid">
                        <source src="${videoUrl}" type="video/mp4">
                        Your browser does not support the video tag.
                    </video>
                    <p class="mt-2 fw-medium">${title}</p>
                    <span class="badge bg-primary">Video</span>
                `;
            } else if (imageUrl) {
                // Show image preview
                bannerPreview.innerHTML = `
                    <img src="${imageUrl}" alt="${title}" class="preview-image img-fluid">
                    <p class="mt-2 fw-medium">${title}</p>
                    <span class="badge bg-secondary">Image</span>
                `;
            } else {
                bannerPreview.innerHTML = `
                    <div class="py-5">
                        <i class="bi bi-image text-muted" style="font-size: 3rem;"></i>
                        <p class="text-muted mt-2 mb-0">Fill in the form to see a preview</p>
                    </div>
                `;
            }
        }
        
        if (titleInput && imageUrlInput && videoUrlInput) {
            titleInput.addEventListener('input', updatePreview);
            imageUrlInput.addEventListener('input', updatePreview);
            videoUrlInput.addEventListener('input', updatePreview);
        }
    });
</script>