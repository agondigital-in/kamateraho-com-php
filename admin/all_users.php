<?php
include 'db_connect.php';

// Handle email sending
$email_sent = false;
$email_error = '';
$email_message = '';
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['send_email'])) {
    $selected_users = $_POST['selected_users'] ?? [];
    $subject = $_POST['subject'] ?? '';
    $message = $_POST['message'] ?? '';
    
    if (empty($selected_users)) {
        $email_error = 'Please select at least one user.';
    } elseif (empty($subject)) {
        $email_error = 'Please enter a subject.';
    } elseif (empty($message)) {
        $email_error = 'Please enter a message.';
    } else {
        // Get selected user emails
        $placeholders = str_repeat('?,', count($selected_users) - 1) . '?';
        $sql = "SELECT email, name FROM users WHERE id IN ($placeholders)";
        $stmt = $pdo->prepare($sql);
        $stmt->execute($selected_users);
        $users = $stmt->fetchAll(PDO::FETCH_ASSOC);
        
        if (!empty($users)) {
            // Send emails using the provided API
            $success_count = 0;
            $failed_emails = [];
            
            foreach ($users as $user) {
                // Include the email template
                include '../admin/email_template.php';
                
                // Generate HTML email content
                $htmlContent = getEmailTemplate($subject, $message, $user['name']);
                
                $api_data = [
                    'email' => $user['email'],
                    'subject' => $subject,
                    'message' => $message,
                    'html' => $htmlContent
                ];
                
                // API endpoint for sending emails
                $url = 'https://mail.kamateraho.com/send-email';
                
                // Authorization token (same as used in forgot_password.php)
                $token = 'km_ritik_ritikyW8joeSZUHp6zgPm8Y8';
                
                // Initialize cURL
                $ch = curl_init($url);
                curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
                curl_setopt($ch, CURLOPT_POST, true);
                curl_setopt($ch, CURLOPT_POSTFIELDS, json_encode($api_data));
                curl_setopt($ch, CURLOPT_HTTPHEADER, [
                    'Content-Type: application/json',
                    'Authorization: Bearer ' . $token
                ]);
                
                // Execute the request
                $response = curl_exec($ch);
                $http_code = curl_getinfo($ch, CURLINFO_HTTP_CODE);
                curl_close($ch);
                
                if ($http_code === 200) {
                    $success_count++;
                } else {
                    $failed_emails[] = $user['email'];
                }
            }
            
            if ($success_count > 0) {
                $email_sent = true;
                $email_message = "Email sent successfully to $success_count out of " . count($users) . " users.";
                
                if (!empty($failed_emails)) {
                    $email_message .= " Failed to send to: " . implode(', ', $failed_emails);
                }
            } else {
                $email_error = 'Failed to send emails to all selected users.';
                if (!empty($failed_emails)) {
                    $email_error .= " Failed emails: " . implode(', ', $failed_emails);
                }
            }
        } else {
            $email_error = 'No valid users found.';
        }
    }
}

// Pagination settings
$users_per_page = 20;
$page = isset($_GET['page']) && is_numeric($_GET['page']) ? (int)$_GET['page'] : 1;
$offset = ($page - 1) * $users_per_page;

// Search functionality
$search = isset($_GET['search']) ? trim($_GET['search']) : '';
$search_condition = '';
$search_params = [];

if (!empty($search)) {
    $search_condition = "WHERE name LIKE ? OR email LIKE ? OR phone LIKE ?";
    $search_param = "%$search%";
    $search_params = [$search_param, $search_param, $search_param];
}

// Fetch total users count
$total_users = 0;
try {
    $count_sql = "SELECT COUNT(*) FROM users " . $search_condition;
    $count_stmt = $pdo->prepare($count_sql);
    
    if (!empty($search)) {
        $count_stmt->execute($search_params);
    } else {
        $count_stmt->execute();
    }
    
    $total_users = $count_stmt->fetchColumn();
} catch (PDOException $e) {
    $error = "Error fetching user count: " . $e->getMessage();
}

// Calculate total pages
$total_pages = ceil($total_users / $users_per_page);

// Fetch users with pagination
$users = [];
try {
    $sql = "SELECT id, name, email, phone, city, state, wallet_balance, referral_code, referral_source, created_at 
            FROM users 
            $search_condition 
            ORDER BY created_at DESC 
            LIMIT ? OFFSET ?";
    
    $stmt = $pdo->prepare($sql);
    
    // Bind search parameters if search is active
    $param_index = 1;
    if (!empty($search)) {
        foreach ($search_params as $param) {
            $stmt->bindValue($param_index++, $param, PDO::PARAM_STR);
        }
    }
    
    // Bind pagination parameters
    $stmt->bindValue($param_index++, $users_per_page, PDO::PARAM_INT);
    $stmt->bindValue($param_index++, $offset, PDO::PARAM_INT);
    
    $stmt->execute();
    $users = $stmt->fetchAll(PDO::FETCH_ASSOC);
} catch (PDOException $e) {
    $error = "Error fetching users: " . $e->getMessage();
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>All Users - Admin Panel</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.10.0/font/bootstrap-icons.css">
    <link rel="stylesheet" href="assets/admin.css">
    <link rel="stylesheet" href="assets/admin_batch.css">
    <link rel="stylesheet" href="assets/admin_email.css">
    
    <style>
        .search-box {
            max-width: 300px;
        }
        
        .table-responsive {
            border-radius: 8px;
            overflow: hidden;
            box-shadow: 0 2px 10px rgba(0, 0, 0, 0.1);
        }
        
        .pagination {
            margin-top: 20px;
        }
        
        .pagination .page-link {
            color: #4361ee;
        }
        
        .pagination .page-item.active .page-link {
            background-color: #4361ee;
            border-color: #4361ee;
        }
        
        .user-avatar {
            width: 40px;
            height: 40px;
            border-radius: 50%;
            background-color: #4361ee;
            color: white;
            display: flex;
            align-items: center;
            justify-content: center;
            font-weight: bold;
        }
        
        .referral-badge {
            font-size: 0.8em;
        }
        
        .source-badge {
            padding: 0.25em 0.5em;
            border-radius: 12px;
            font-size: 0.75em;
            font-weight: 500;
        }
        
        .source-youtube { background-color: #ff0000; color: white; }
        .source-facebook { background-color: #1877f2; color: white; }
        .source-instagram { background: linear-gradient(45deg, #f09433 0%,#e6683c 25%,#dc2743 50%,#cc2366 75%,#bc1888 100%); color: white; }
        .source-twitter { background-color: #1da1f2; color: white; }
        .source-other { background-color: #6c757d; color: white; }
        
        /* Email button styling */
        .email-actions {
            margin-bottom: 20px;
        }
        
        .email-btn {
            background: linear-gradient(135deg, #4361ee, #3f37c9);
            border: none;
            color: white;
            padding: 8px 15px;
            border-radius: 5px;
            font-weight: 500;
        }
        
        .email-btn:hover {
            background: linear-gradient(135deg, #3f37c9, #4361ee);
            color: white;
        }
        
        .email-btn:disabled {
            background: #ccc;
            cursor: not-allowed;
        }
    </style>
</head>
<body>
    <?php include 'includes/admin_layout.php'; ?>
    
    <div class="container-fluid">
        <div class="row">
            <main class="col-md-9 ms-sm-auto col-lg-10 px-md-4">
                <div class="d-flex justify-content-between flex-wrap flex-md-nowrap align-items-center pt-3 pb-2 mb-3 border-bottom">
                    <h1 class="h2">All Users</h1>
                    <div class="btn-toolbar mb-2 mb-md-0">
                        <div class="input-group search-box">
                            <form method="GET" class="d-flex w-100">
                                <input type="text" name="search" class="form-control" placeholder="Search users..." value="<?php echo htmlspecialchars($search); ?>">
                                <button class="btn btn-outline-secondary" type="submit">Search</button>
                            </form>
                        </div>
                    </div>
                </div>
                
                <?php if (isset($error)): ?>
                    <div class="alert alert-danger"><?php echo $error; ?></div>
                <?php endif; ?>
                
                <?php if ($email_sent): ?>
                    <div class="alert alert-success"><?php echo $email_message; ?></div>
                <?php endif; ?>
                
                <?php if ($email_error): ?>
                    <div class="alert alert-danger"><?php echo $email_error; ?></div>
                <?php endif; ?>
                
                <div class="card">
                    <div class="card-header">
                        <h5 class="card-title mb-0">User List</h5>
                    </div>
                    <div class="card-body">
                        <div class="d-flex justify-content-between align-items-center mb-3">
                            <p class="mb-0">Showing <?php echo count($users); ?> of <?php echo $total_users; ?> users</p>
                        </div>
                        
                        <?php if (empty($users)): ?>
                            <div class="text-center py-5">
                                <i class="bi bi-people text-muted" style="font-size: 3rem;"></i>
                                <p class="mt-3">No users found<?php echo !empty($search) ? ' matching your search' : ''; ?>.</p>
                                <?php if (!empty($search)): ?>
                                    <a href="all_users.php" class="btn btn-primary">View All Users</a>
                                <?php endif; ?>
                            </div>
                        <?php else: ?>
                            <!-- Email Actions -->
                            <div class="email-actions">
                                <button type="button" class="btn email-btn" id="sendEmailBtn" disabled>
                                    <i class="bi bi-envelope me-1"></i> Send Email 
                                    <span class="selected-users-count" id="selectedUsersCount">0</span>
                                </button>
                            </div>
                            
                            <div class="table-responsive">
                                <table class="table table-striped table-hover">
                                    <thead>
                                        <tr>
                                            <th>
                                                <input type="checkbox" id="selectAll">
                                            </th>
                                            <th>ID</th>
                                            <th>User</th>
                                            <th>Contact</th>
                                            <th>Location</th>
                                            <th>Wallet Balance</th>
                                            <th>Referral Info</th>
                                            <th>Source</th>
                                            <th>Joined</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        <?php foreach ($users as $user): ?>
                                            <tr>
                                                <td>
                                                    <input type="checkbox" name="selected_users[]" value="<?php echo $user['id']; ?>">
                                                </td>
                                                <td><?php echo htmlspecialchars($user['id']); ?></td>
                                                <td>
                                                    <div class="d-flex align-items-center">
                                                        <div class="user-avatar me-2">
                                                            <?php echo strtoupper(substr($user['name'], 0, 1)); ?>
                                                        </div>
                                                        <div>
                                                            <div><?php echo htmlspecialchars($user['name']); ?></div>
                                                            <small class="text-muted"><?php echo htmlspecialchars($user['email']); ?></small>
                                                        </div>
                                                    </div>
                                                </td>
                                                <td>
                                                    <div><?php echo htmlspecialchars($user['phone']); ?></div>
                                                </td>
                                                <td>
                                                    <div><?php echo htmlspecialchars($user['city']); ?></div>
                                                    <small class="text-muted"><?php echo htmlspecialchars($user['state']); ?></small>
                                                </td>
                                                <td>
                                                    <span class="badge bg-success">₹<?php echo number_format($user['wallet_balance'], 2); ?></span>
                                                </td>
                                                <td>
                                                    <?php if (!empty($user['referral_code'])): ?>
                                                        <span class="badge bg-primary referral-badge"><?php echo htmlspecialchars($user['referral_code']); ?></span>
                                                    <?php else: ?>
                                                        <span class="badge bg-secondary referral-badge">No code</span>
                                                    <?php endif; ?>
                                                </td>
                                                <td>
                                                    <?php if (!empty($user['referral_source'])): ?>
                                                        <?php
                                                        $source = htmlspecialchars($user['referral_source']);
                                                        $source_class = 'source-' . $source;
                                                        switch ($source) {
                                                            case 'youtube':
                                                                $source_text = 'YouTube';
                                                                break;
                                                            case 'facebook':
                                                                $source_text = 'Facebook';
                                                                break;
                                                            case 'instagram':
                                                                $source_text = 'Instagram';
                                                                break;
                                                            case 'twitter':
                                                                $source_text = 'Twitter';
                                                                break;
                                                            default:
                                                                $source_text = ucfirst($source);
                                                                $source_class = 'source-other';
                                                        }
                                                        ?>
                                                        <span class="source-badge <?php echo $source_class; ?>"><?php echo $source_text; ?></span>
                                                    <?php else: ?>
                                                        <span class="badge bg-secondary">Unknown</span>
                                                    <?php endif; ?>
                                                </td>
                                                <td>
                                                    <div><?php echo date('M d, Y', strtotime($user['created_at'])); ?></div>
                                                    <small class="text-muted"><?php echo date('h:i A', strtotime($user['created_at'])); ?></small>
                                                </td>
                                            </tr>
                                        <?php endforeach; ?>
                                    </tbody>
                                </table>
                            </div>
                            
                            <!-- Pagination -->
                            <?php if ($total_pages > 1): ?>
                                <nav aria-label="User pagination">
                                    <ul class="pagination justify-content-center">
                                        <?php if ($page > 1): ?>
                                            <li class="page-item">
                                                <a class="page-link" href="?page=<?php echo $page - 1; ?><?php echo !empty($search) ? '&search=' . urlencode($search) : ''; ?>" aria-label="Previous">
                                                    <span aria-hidden="true">&laquo;</span>
                                                </a>
                                            </li>
                                        <?php endif; ?>
                                        
                                        <?php
                                        // Calculate start and end page numbers to display
                                        $start_page = max(1, $page - 2);
                                        $end_page = min($total_pages, $page + 2);
                                        
                                        // Adjust if near the beginning or end
                                        if ($start_page === 1) {
                                            $end_page = min(5, $total_pages);
                                        } elseif ($end_page === $total_pages) {
                                            $start_page = max(1, $total_pages - 4);
                                        }
                                        ?>
                                        
                                        <?php for ($i = $start_page; $i <= $end_page; $i++): ?>
                                            <li class="page-item <?php echo $i == $page ? 'active' : ''; ?>">
                                                <a class="page-link" href="?page=<?php echo $i; ?><?php echo !empty($search) ? '&search=' . urlencode($search) : ''; ?>"><?php echo $i; ?></a>
                                            </li>
                                        <?php endfor; ?>
                                        
                                        <?php if ($page < $total_pages): ?>
                                            <li class="page-item">
                                                <a class="page-link" href="?page=<?php echo $page + 1; ?><?php echo !empty($search) ? '&search=' . urlencode($search) : ''; ?>" aria-label="Next">
                                                    <span aria-hidden="true">&raquo;</span>
                                                </a>
                                            </li>
                                        <?php endif; ?>
                                    </ul>
                                </nav>
                            <?php endif; ?>
                        <?php endif; ?>
                    </div>
                </div>
            </main>
        </div>
    </div>
    
    <!-- Email Modal -->
    <div class="modal fade email-modal" id="emailModal" tabindex="-1" aria-labelledby="emailModalLabel" aria-hidden="true">
        <div class="modal-dialog modal-lg">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="emailModalLabel">Send Email to Selected Users</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <form id="emailForm" method="POST">
                    <div class="modal-body">
                        <input type="hidden" name="send_email" value="1">
                        <div class="mb-3">
                            <label for="emailSubject" class="form-label">Subject</label>
                            <input type="text" class="form-control" id="emailSubject" name="subject" required>
                        </div>
                        <div class="mb-3">
                            <label for="emailMessage" class="form-label">Message</label>
                            <textarea class="form-control" id="emailMessage" name="message" rows="6" required></textarea>
                        </div>
                        <div id="emailPreview" class="email-preview">
                            <!-- Preview will be populated by JavaScript -->
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                        <button type="submit" class="btn btn-primary">Send Email</button>
                    </div>
                </form>
            </div>
        </div>
    </div>
    
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    <script src="assets/admin_batch.js"></script>
    <script src="assets/admin_email.js"></script>
    <script>
        // Handle send email button click
        document.getElementById('sendEmailBtn').addEventListener('click', function() {
            const selectedCheckboxes = document.querySelectorAll('input[name="selected_users[]"]:checked');
            if (selectedCheckboxes.length > 0) {
                // Create hidden inputs for selected users
                const form = document.getElementById('emailForm');
                
                // Remove any existing hidden inputs
                const existingInputs = form.querySelectorAll('input[name="selected_users[]"]');
                existingInputs.forEach(input => input.remove());
                
                // Add hidden inputs for selected users
                selectedCheckboxes.forEach(checkbox => {
                    const hiddenInput = document.createElement('input');
                    hiddenInput.type = 'hidden';
                    hiddenInput.name = 'selected_users[]';
                    hiddenInput.value = checkbox.value;
                    form.appendChild(hiddenInput);
                });
                
                // Show the modal
                const emailModal = new bootstrap.Modal(document.getElementById('emailModal'));
                emailModal.show();
            } else {
                alert('Please select at least one user to send the email.');
            }
        });
    </script>
</body>
</html>