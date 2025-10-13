<?php
session_start();
include 'config/db.php';
include 'includes/navbar.php';

// Check if user is logged in
if (!isset($_SESSION['user_id'])) {
    header("Location: login.php");
    exit;
}

$user_id = $_SESSION['user_id'];

// Fetch user's replied messages
$messages = [];
if ($pdo) {
    try {
        $stmt = $pdo->prepare("SELECT * FROM contact_messages WHERE user_id = ? AND status = 'replied' AND reply IS NOT NULL ORDER BY replied_at DESC");
        $stmt->execute([$user_id]);
        $messages = $stmt->fetchAll(PDO::FETCH_ASSOC);
    } catch (PDOException $e) {
        $error = "Error fetching messages: " . $e->getMessage();
    }
}

// Ensure Bootstrap CSS/JS are available as a fallback (in case navbar/header didn't include them)
echo '<!-- Fallback Bootstrap CSS (CDN) + meta viewport + small inline fallback styles -->';
echo '<meta name="viewport" content="width=device-width, initial-scale=1">';
echo '<link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">';
echo '<style>
/* minimal fallback styles so layout is readable if Bootstrap isn\'t loaded */
.container{max-width:1140px;margin:0 auto;padding:1rem;}
.card{border:1px solid #e9ecef;border-radius:.375rem;padding:1rem;background:#fff;margin-bottom:1rem;}
.card-header{font-weight:600;background:transparent;border-bottom:none;padding:0 0 .5rem 0;}
.badge{display:inline-block;padding:.35em .65em;font-size:.75em;font-weight:700;color:#fff;background:#28a745;border-radius:.25rem;}
.breadcrumb{background:transparent;padding:.5rem 0;margin-bottom:1rem;}
</style>';
?>

<div class="container mt-5">
    <nav aria-label="breadcrumb">
        <ol class="breadcrumb">
            <li class="breadcrumb-item"><a href="index.php">Home</a></li>
            <li class="breadcrumb-item active" aria-current="page">My Messages</li>
        </ol>
    </nav>
    
    <div class="row">
        <div class="col-lg-12">
            <h1>My Messages</h1>
            <p class="lead">View replies from admin to your messages</p>
            
            <?php if (isset($error)): ?>
                <div class="alert alert-danger"><?php echo $error; ?></div>
            <?php endif; ?>
            
            <?php if (empty($messages)): ?>
                <div class="alert alert-info">
                    <p>You don't have any replies from admin yet.</p>
                    <p><a href="contact.php" class="btn btn-primary">Send a Message</a></p>
                </div>
            <?php else: ?>
                <?php foreach ($messages as $msg): ?>
                    <div class="card mb-3">
                        <div class="card-header">
                            <div class="d-flex justify-content-between">
                                <h5><?php echo htmlspecialchars($msg['subject']); ?></h5>
                                <!-- support for both BS4 (badge-success) and BS5 (bg-success) -->
                                <span class="badge bg-success badge-success">Replied</span>
                            </div>
                        </div>
                        <div class="card-body">
                            <p><strong>Sent:</strong> <?php echo $msg['created_at']; ?></p>
                            <p><strong>Your Message:</strong></p>
                            <p><?php echo nl2br(htmlspecialchars($msg['message'])); ?></p>
                            
                            <div class="border-top mt-3 pt-3">
                                <p><strong>Admin Reply:</strong></p>
                                <p><?php echo nl2br(htmlspecialchars($msg['reply'])); ?></p>
                                <p><strong>Replied at:</strong> <?php echo $msg['replied_at']; ?></p>
                            </div>
                        </div>
                    </div>
                <?php endforeach; ?>
            <?php endif; ?>
        </div>
    </div>
</div>

<?php
// Load Bootstrap JS (CDN) as fallback for interactive components
echo '<script src="https://cdn.jsdelivr.net/npm/@popperjs/core@2.11.8/dist/umd/popper.min.js"></script>';
echo '<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.min.js"></script>';

include 'includes/footer.php';
?>