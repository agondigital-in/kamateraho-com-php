<?php
// Start session first
if (session_status() == PHP_SESSION_NONE) {
    session_start();
}

$page_title = "Manage Blog";
require_once '../config/db.php';

// Initialize message variables
$success_message = '';
$error_message = '';

// Check for messages in session
if (isset($_SESSION['success_message'])) {
    $success_message = $_SESSION['success_message'];
    unset($_SESSION['success_message']);
}

if (isset($_SESSION['error_message'])) {
    $error_message = $_SESSION['error_message'];
    unset($_SESSION['error_message']);
}

// Handle delete request
if (isset($_POST['delete_post'])) {
    $post_id = (int)$_POST['post_id'];
    
    try {
        $stmt = $pdo->prepare("DELETE FROM blog_posts WHERE id = ?");
        $stmt->execute([$post_id]);
        $_SESSION['success_message'] = "Blog post deleted successfully!";
    } catch (PDOException $e) {
        $_SESSION['error_message'] = "Failed to delete blog post: " . $e->getMessage();
    }
    
    header("Location: " . $_SERVER['PHP_SELF']);
    exit();
}

// Handle edit request - load existing post data
$editing_post = null;
if (isset($_GET['edit'])) {
    $post_id = (int)$_GET['edit'];
    
    try {
        $stmt = $pdo->prepare("SELECT * FROM blog_posts WHERE id = ?");
        $stmt->execute([$post_id]);
        $editing_post = $stmt->fetch(PDO::FETCH_ASSOC);
    } catch (PDOException $e) {
        $_SESSION['error_message'] = "Failed to load blog post: " . $e->getMessage();
    }
}

// Handle form submission for creating/updating blog posts
if ($_SERVER['REQUEST_METHOD'] === 'POST' && !isset($_POST['delete_post'])) {
    $title = trim($_POST['title'] ?? '');
    $slug = trim($_POST['slug'] ?? '');
    $excerpt = trim($_POST['excerpt'] ?? '');
    $image_url = trim($_POST['image_url'] ?? '');
    $content = $_POST['content'] ?? '';
    $author = trim($_POST['author'] ?? 'Admin');
    $status = $_POST['status'] ?? 'draft';
    $post_id = isset($_POST['post_id']) ? (int)$_POST['post_id'] : 0;
    
    // Generate slug from title if not provided
    if (empty($slug)) {
        $slug = strtolower(trim(preg_replace('/[^A-Za-z0-9-]+/', '-', $title), '-'));
    }
    
    // Validate inputs
    if (!empty($title) && !empty($content)) {
        try {
            if ($post_id > 0) {
                // Update existing post
                $stmt = $pdo->prepare("UPDATE blog_posts SET title = ?, slug = ?, excerpt = ?, content = ?, image_url = ?, author = ?, status = ? WHERE id = ?");
                $stmt->execute([$title, $slug, $excerpt, $content, $image_url, $author, $status, $post_id]);
                $_SESSION['success_message'] = "Blog post updated successfully!";
            } else {
                // Create new post
                $stmt = $pdo->prepare("INSERT INTO blog_posts (title, slug, excerpt, content, image_url, author, status) VALUES (?, ?, ?, ?, ?, ?, ?)");
                $stmt->execute([$title, $slug, $excerpt, $content, $image_url, $author, $status]);
                $_SESSION['success_message'] = "Blog post created successfully!";
            }
            
            header("Location: " . $_SERVER['PHP_SELF']);
            exit();
        } catch (PDOException $e) {
            $_SESSION['error_message'] = "Database error: " . $e->getMessage();
            header("Location: " . $_SERVER['PHP_SELF']);
            exit();
        }
    } else {
        $_SESSION['error_message'] = "Title and content are required";
        header("Location: " . $_SERVER['PHP_SELF']);
        exit();
    }
}

// Include the admin layout
include 'includes/admin_layout.php';
?>

<!-- Include Quill CSS -->
<link href="https://cdn.quilljs.com/1.3.6/quill.snow.css" rel="stylesheet">

<div class="container-fluid">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <h2 class="mb-0">Manage Blog Posts</h2>
        <?php if (!$editing_post): ?>
            <button type="button" class="btn btn-primary" id="toggleFormBtn">
                 + Create New Post
            </button>
        <?php endif; ?>
    </div>
    
    <?php if ($success_message): ?>
        <div class="alert alert-success alert-dismissible fade show" role="alert">
            <?php echo htmlspecialchars($success_message); ?>
            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
        </div>
    <?php endif; ?>
    
    <?php if ($error_message): ?>
        <div class="alert alert-danger alert-dismissible fade show" role="alert">
            <?php echo htmlspecialchars($error_message); ?>
            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
        </div>
    <?php endif; ?>
    
    <div class="card" id="blogFormCard" style="<?php echo $editing_post ? '' : 'display: none;'; ?>">
        <div class="card-header d-flex justify-content-between align-items-center">
            <h5 class="mb-0"><?php echo $editing_post ? 'Edit Blog Post' : 'Create New Blog Post'; ?></h5>
            <?php if (!$editing_post): ?>
                <button type="button" class="btn btn-sm btn-secondary" id="closeFormBtn">
                    <i class="fas fa-times"></i> Close
                </button>
            <?php endif; ?>
        </div>
        <div class="card-body">
            <form method="POST" id="blogForm">
                <?php if ($editing_post): ?>
                    <input type="hidden" name="post_id" value="<?php echo $editing_post['id']; ?>">
                <?php endif; ?>
                
                <div class="mb-3">
                    <label for="title" class="form-label">Title *</label>
                    <input type="text" class="form-control" id="title" name="title" value="<?php echo $editing_post ? htmlspecialchars($editing_post['title']) : ''; ?>" required>
                </div>
                
                <div class="mb-3">
                    <label for="slug" class="form-label">Slug (URL-friendly name)</label>
                    <input type="text" class="form-control" id="slug" name="slug" value="<?php echo $editing_post ? htmlspecialchars($editing_post['slug']) : ''; ?>" placeholder="auto-generated-from-title">
                    <div class="form-text">Leave empty to auto-generate from title</div>
                </div>
                
                <div class="row">
                    <div class="col-md-6">
                        <div class="mb-3">
                            <label for="author" class="form-label">Author</label>
                            <input type="text" class="form-control" id="author" name="author" value="<?php echo $editing_post ? htmlspecialchars($editing_post['author']) : 'Admin'; ?>">
                        </div>
                    </div>
                    <div class="col-md-6">
                        <div class="mb-3">
                            <label for="status" class="form-label">Status</label>
                            <select class="form-select" id="status" name="status">
                                <option value="draft" <?php echo ($editing_post && $editing_post['status'] === 'draft') ? 'selected' : ''; ?>>Draft</option>
                                <option value="published" <?php echo ($editing_post && $editing_post['status'] === 'published') ? 'selected' : ''; ?>>Published</option>
                            </select>
                        </div>
                    </div>
                </div>
                
                <div class="mb-3">
                    <label for="excerpt" class="form-label">Excerpt</label>
                    <textarea class="form-control" id="excerpt" name="excerpt" rows="2" placeholder="Short description for blog listing"><?php echo $editing_post ? htmlspecialchars($editing_post['excerpt']) : ''; ?></textarea>
                </div>
                
                <div class="mb-3">
                    <label for="image_url" class="form-label">Featured Image</label>
                    <div class="input-group">
                        <input type="url" class="form-control" id="image_url" name="image_url" value="<?php echo $editing_post ? htmlspecialchars($editing_post['image_url']) : ''; ?>" placeholder="https://example.com/image.jpg">
                        <button type="button" class="btn btn-secondary" onclick="document.getElementById('imageUpload').click()">
                            Upload Image
                        </button>
                    </div>
                    <input type="file" id="imageUpload" accept="image/*" style="display: none;">
                    <div class="form-text">Upload an image or paste URL. Supports JPG, PNG, GIF, WebP (max 5MB)</div>
                    <div id="uploadProgress" style="display: none; margin-top: 10px;">
                        <div class="progress">
                            <div class="progress-bar progress-bar-striped progress-bar-animated" role="progressbar" style="width: 100%">Uploading...</div>
                        </div>
                    </div>
                    <div id="imagePreview" style="margin-top: 15px; <?php echo ($editing_post && $editing_post['image_url']) ? '' : 'display: none;'; ?>">
                        <img id="previewImg" src="<?php echo $editing_post ? htmlspecialchars($editing_post['image_url']) : ''; ?>" alt="Preview" style="max-width: 300px; max-height: 200px; border-radius: 8px; border: 2px solid #ddd; object-fit: cover;">
                    </div>
                </div>
                
                <div class="mb-3">
                    <label for="content" class="form-label">Content *</label>
                    <div id="editor" style="min-height: 400px; background: white;"></div>
                    <textarea name="content" id="content" style="display:none;"></textarea>
                    <div class="form-text">Use the rich text editor to format your content</div>
                </div>
                
                <button type="submit" class="btn btn-primary">
                    <i class="fas fa-save"></i> <?php echo $editing_post ? 'Update Blog Post' : 'Create Blog Post'; ?>
                </button>
                <?php if ($editing_post): ?>
                    <a href="<?php echo $_SERVER['PHP_SELF']; ?>" class="btn btn-secondary">
                        <i class="fas fa-times"></i> Cancel
                    </a>
                <?php endif; ?>
            </form>
        </div>
    </div>
    
    <div class="card mt-4">
        <div class="card-header">
            <h5>Existing Blog Posts</h5>
        </div>
        <div class="card-body">
            <div class="table-responsive" style="overflow-x: auto; -webkit-overflow-scrolling: touch;">
                <table class="table table-striped table-hover" style="min-width: 800px;">
                    <thead>
                        <tr>
                            <th style="min-width: 50px;">#</th>
                            <th style="min-width: 200px;">Title</th>
                            <th style="min-width: 200px;">Slug</th>
                            <th style="min-width: 100px;">Author</th>
                            <th style="min-width: 100px;">Status</th>
                            <th style="min-width: 120px;">Created</th>
                            <th style="min-width: 150px;">Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php
                        try {
                            $stmt = $pdo->query("SELECT * FROM blog_posts ORDER BY created_at DESC");
                            $posts = $stmt->fetchAll(PDO::FETCH_ASSOC);
                            
                            if (count($posts) > 0) {
                                foreach ($posts as $post):
                        ?>
                        <tr>
                            <td><?php echo $post['id']; ?></td>
                            <td style="max-width: 250px; white-space: nowrap; overflow: hidden; text-overflow: ellipsis;" title="<?php echo htmlspecialchars($post['title']); ?>">
                                <?php echo htmlspecialchars($post['title']); ?>
                            </td>
                            <td style="max-width: 200px;">
                                <code style="display: block; white-space: nowrap; overflow: hidden; text-overflow: ellipsis;" title="<?php echo htmlspecialchars($post['slug']); ?>">
                                    <?php echo htmlspecialchars($post['slug']); ?>
                                </code>
                            </td>
                            <td><?php echo htmlspecialchars($post['author']); ?></td>
                            <td>
                                <?php if ($post['status'] === 'published'): ?>
                                    <span class="badge bg-success">Published</span>
                                <?php else: ?>
                                    <span class="badge bg-secondary">Draft</span>
                                <?php endif; ?>
                            </td>
                            <td><?php echo date('M d, Y', strtotime($post['created_at'])); ?></td>
                            <td style="white-space: nowrap;">
                                <a href="../kamateraho/blog/<?php echo urlencode($post['slug']); ?>" target="_blank" class="btn btn-sm btn-info mb-1" title="View">
                                    <i class="fas fa-eye"></i>
                                </a>
                                <a href="?edit=<?php echo $post['id']; ?>" class="btn btn-sm btn-warning mb-1" title="Edit">
                                    <i class="fas fa-edit"></i>
                                </a>
                                <form method="POST" style="display: inline-block;">
                                    <input type="hidden" name="post_id" value="<?php echo $post['id']; ?>">
                                    <input type="hidden" name="delete_post" value="1">
                                    <button type="submit" class="btn btn-sm btn-danger mb-1" onclick="return confirm('Are you sure you want to delete this blog post?')" title="Delete">
                                        <i class="fas fa-trash"></i>
                                    </button>
                                </form>
                            </td>
                        </tr>
                        <?php 
                                endforeach;
                            } else {
                                echo '<tr><td colspan="7" class="text-center">No blog posts found. Create your first post!</td></tr>';
                            }
                        } catch (PDOException $e) {
                            echo '<tr><td colspan="7" class="text-center text-danger">Error loading posts: ' . htmlspecialchars($e->getMessage()) . '</td></tr>';
                        }
                        ?>
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</div>

<!-- Include Quill JS -->
<script src="https://cdn.quilljs.com/1.3.6/quill.js"></script>

<script>
// Initialize Quill editor
var quill = new Quill('#editor', {
    theme: 'snow',
    modules: {
        toolbar: [
            [{ 'header': [1, 2, 3, 4, 5, 6, false] }],
            [{ 'font': [] }],
            [{ 'size': ['small', false, 'large', 'huge'] }],
            ['bold', 'italic', 'underline', 'strike'],
            [{ 'color': [] }, { 'background': [] }],
            [{ 'script': 'sub'}, { 'script': 'super' }],
            [{ 'list': 'ordered'}, { 'list': 'bullet' }],
            [{ 'indent': '-1'}, { 'indent': '+1' }],
            [{ 'align': [] }],
            ['blockquote', 'code-block'],
            ['link', 'image', 'video'],
            ['clean']
        ]
    },
    placeholder: 'Write your blog content here...'
});

// Load existing content if editing
<?php if ($editing_post && !empty($editing_post['content'])): ?>
quill.root.innerHTML = <?php echo json_encode($editing_post['content']); ?>;
<?php endif; ?>

// Auto-generate slug from title
document.getElementById('title').addEventListener('input', function() {
    var slugField = document.getElementById('slug');
    if (!slugField.value || slugField.dataset.autoGenerated === 'true') {
        var slug = this.value
            .toLowerCase()
            .replace(/[^a-z0-9]+/g, '-')
            .replace(/^-+|-+$/g, '');
        slugField.value = slug;
        slugField.dataset.autoGenerated = 'true';
    }
});

document.getElementById('slug').addEventListener('input', function() {
    if (this.value) {
        this.dataset.autoGenerated = 'false';
    }
});

// Sync Quill content to hidden textarea before form submission
document.getElementById('blogForm').addEventListener('submit', function(e) {
    var content = quill.root.innerHTML;
    document.getElementById('content').value = content;
    
    // Basic validation
    if (content === '<p><br></p>' || content.trim() === '') {
        e.preventDefault();
        alert('Please add some content to your blog post');
        return false;
    }
});

// Image preview functionality
function updateImagePreview(url) {
    const previewDiv = document.getElementById('imagePreview');
    const previewImg = document.getElementById('previewImg');
    
    if (url && url.trim() !== '') {
        previewImg.src = url;
        previewDiv.style.display = 'block';
    } else {
        previewDiv.style.display = 'none';
    }
}

// Update preview when URL changes
document.getElementById('image_url').addEventListener('input', function(e) {
    updateImagePreview(e.target.value);
});

// Image upload functionality
document.getElementById('imageUpload').addEventListener('change', function(e) {
    const file = e.target.files[0];
    if (!file) return;
    
    const formData = new FormData();
    formData.append('image', file);
    
    const progressDiv = document.getElementById('uploadProgress');
    progressDiv.style.display = 'block';
    
    fetch('upload_blog_image.php', {
        method: 'POST',
        body: formData
    })
    .then(response => response.json())
    .then(data => {
        progressDiv.style.display = 'none';
        if (data.success) {
            document.getElementById('image_url').value = data.url;
            updateImagePreview(data.url);
            alert('Image uploaded successfully!');
        } else {
            alert('Upload failed: ' + data.message);
        }
    })
    .catch(error => {
        progressDiv.style.display = 'none';
        alert('Upload error: ' + error.message);
    });
});

// Toggle form visibility
<?php if (!$editing_post): ?>
const toggleFormBtn = document.getElementById('toggleFormBtn');
const closeFormBtn = document.getElementById('closeFormBtn');
const blogFormCard = document.getElementById('blogFormCard');

if (toggleFormBtn) {
    toggleFormBtn.addEventListener('click', function() {
        blogFormCard.style.display = 'block';
        toggleFormBtn.style.display = 'none';
        // Scroll to form
        blogFormCard.scrollIntoView({ behavior: 'smooth', block: 'start' });
    });
}

if (closeFormBtn) {
    closeFormBtn.addEventListener('click', function() {
        blogFormCard.style.display = 'none';
        toggleFormBtn.style.display = 'inline-block';
        // Scroll to top
        window.scrollTo({ top: 0, behavior: 'smooth' });
    });
}
<?php endif; ?>
</script>

<?php include 'includes/admin_footer.php'; ?>
