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
                // Check if this is a special Diwali offer
                $isDiwaliOffer = (strpos(strtolower($subject), 'diwali') !== false);
                
                if ($isDiwaliOffer) {
                    // Include the Diwali email template
                    include_once '../admin/diwali_email_template.php';
                    $htmlContent = getDiwaliEmailTemplate($user['name']);
                } else {
                    // Include the regular email template
                    include_once '../admin/email_template.php';
                    $htmlContent = getEmailTemplate($subject, $message, $user['name']);
                }
                
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
$id_filter = isset($_GET['id_filter']) ? trim($_GET['id_filter']) : '';
$search_condition = '';
$search_params = [];

if (!empty($search)) {
    $search_condition = "WHERE name LIKE ? OR email LIKE ? OR phone LIKE ?";
    $search_param = "%$search%";
    $search_params = [$search_param, $search_param, $search_param];
} elseif (!empty($id_filter)) {
    $search_condition = "WHERE id = ?";
    $search_params = [$id_filter];
}

// Fetch total users count
$total_users = 0;
try {
    $count_sql = "SELECT COUNT(*) FROM users " . $search_condition;
    $count_stmt = $pdo->prepare($count_sql);
    
    if (!empty($search) || !empty($id_filter)) {
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
    if (!empty($search) || !empty($id_filter)) {
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
        
        body {
            background-color: #f0f2f5;
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
            color: var(--dark-text);
            padding-top: 20px;
        }
        
        .main-container {
            max-width: 100%;
            padding: 0 15px;
            margin-bottom: 0;
        }
        
        .page-header {
            background: white;
            border-radius: 10px;
            padding: 15px 20px;
            margin-bottom: 15px;
            box-shadow: 0 2px 10px rgba(0, 0, 0, 0.05);
        }
        
        .page-title {
            font-weight: 700;
            font-size: 1.75rem;
            margin-bottom: 0;
            color: var(--primary-color);
        }
        
        .search-container {
            background: white;
            border-radius: 50px;
            padding: 5px 5px 5px 20px;
            box-shadow: 0 2px 5px rgba(0, 0, 0, 0.05);
            border: 1px solid var(--border-color);
        }
        
        .search-container input {
            border: none;
            outline: none;
            width: 100%;
            padding: 8px 0;
        }
        
        .search-container button {
            background: var(--primary-color);
            color: white;
            border: none;
            border-radius: 50px;
            padding: 8px 20px;
            font-weight: 500;
            transition: all 0.3s ease;
        }
        
        .search-container button:hover {
            background: var(--secondary-color);
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
            padding: 12px 15px;
        }
        
        .card-title {
            font-weight: 600;
            color: var(--dark-text);
            margin-bottom: 0;
            font-size: 1.25rem;
        }
        
        .stats-container {
            display: flex;
            gap: 10px;
            margin-bottom: 15px;
            flex-wrap: wrap;
        }
        
        .stats-box {
            background: white;
            border-radius: 10px;
            padding: 15px;
            box-shadow: 0 2px 5px rgba(0, 0, 0, 0.05);
            flex: 1;
            min-width: 200px;
            border-left: 4px solid var(--primary-color);
            transition: transform 0.3s ease;
            margin-bottom: 0;
        }
        
        .stats-box:hover {
            transform: translateY(-3px);
            box-shadow: 0 5px 15px rgba(0, 0, 0, 0.1);
        }
        
        .stats-number {
            font-size: 2rem;
            font-weight: 700;
            color: var(--primary-color);
            margin-bottom: 5px;
        }
        
        .stats-label {
            color: var(--light-text);
            font-size: 0.9rem;
            font-weight: 500;
        }
        
        .table-container {
            overflow-x: auto;
            margin-bottom: 0;
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
        
        .user-avatar {
            width: 40px;
            height: 40px;
            border-radius: 50%;
            background: linear-gradient(135deg, var(--primary-color), var(--accent-color));
            color: white;
            display: flex;
            align-items: center;
            justify-content: center;
            font-weight: 600;
            font-size: 1rem;
        }
        
        .badge-custom {
            padding: 0.5em 0.75em;
            font-weight: 500;
            border-radius: 20px;
            font-size: 0.8rem;
        }
        
        /* Wallet column - Green theme with black text */
        .badge-success {
            background-color: rgba(40, 167, 69, 0.15) !important;
            color: #000000 !important;
            border: 1px solid rgba(40, 167, 69, 0.3) !important;
        }
        
        /* Referral column - Purple theme with black text */
        .badge-primary {
            background-color: rgba(102, 92, 220, 0.15) !important;
            color: #000000 !important;
            border: 1px solid rgba(102, 92, 220, 0.3) !important;
        }
        
        /* Source column - Orange theme with black text */
        .badge-warning {
            background-color: rgba(255, 152, 0, 0.15) !important;
            color: #000000 !important;
            border: 1px solid rgba(255, 152, 0, 0.3) !important;
        }
        
        /* Unknown/No code - Gray theme with black text */
        .badge-secondary {
            background-color: rgba(108, 117, 125, 0.15) !important;
            color: #000000 !important;
            border: 1px solid rgba(108, 117, 125, 0.3) !important;
        }
        
        .source-badge {
            padding: 0.35em 0.75em;
            border-radius: 20px;
            font-size: 0.75rem;
            font-weight: 500;
        }
        
        /* Source-specific colors with black text */
        .source-youtube { 
            background: rgba(255, 0, 0, 0.15) !important; 
            color: #000000 !important; 
            border: 1px solid rgba(255, 0, 0, 0.3) !important;
        }
        
        .source-facebook { 
            background: rgba(24, 119, 242, 0.15) !important; 
            color: #000000 !important; 
            border: 1px solid rgba(24, 119, 242, 0.3) !important;
        }
        
        .source-instagram { 
            background: linear-gradient(45deg, rgba(240, 148, 51, 0.2), rgba(220, 39, 67, 0.2)) !important; 
            color: #000000 !important; 
            border: 1px solid rgba(220, 39, 67, 0.3) !important;
        }
        
        .source-twitter { 
            background: rgba(29, 161, 242, 0.15) !important; 
            color: #000000 !important; 
            border: 1px solid rgba(29, 161, 242, 0.3) !important;
        }
        
        .source-other { 
            background: rgba(108, 117, 125, 0.15) !important; 
            color: #000000 !important; 
            border: 1px solid rgba(108, 117, 125, 0.3) !important;
        }
        
        .email-btn {
            background: linear-gradient(135deg, var(--primary-color), var(--secondary-color));
            border: none;
            color: white;
            padding: 10px 20px;
            border-radius: 8px;
            font-weight: 500;
            box-shadow: 0 4px 6px rgba(111, 66, 193, 0.2);
            transition: all 0.3s ease;
        }
        
        .email-btn:hover {
            transform: translateY(-2px);
            box-shadow: 0 6px 10px rgba(111, 66, 193, 0.3);
        }
        
        .email-btn:disabled {
            background: #e9ecef;
            cursor: not-allowed;
            transform: none;
            box-shadow: none;
            color: #6c757d;
        }
        
        .pagination .page-link {
            color: var(--primary-color);
            border: 1px solid var(--border-color);
            padding: 10px 15px;
        }
        
        .pagination .page-item.active .page-link {
            background: var(--primary-color);
            border-color: var(--primary-color);
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
        
        /* Responsive styles */
        @media (max-width: 992px) {
            .page-title {
                font-size: 1.5rem;
            }
            
            .stats-box {
                min-width: 150px;
            }
            
            .stats-number {
                font-size: 1.5rem;
            }
        }
        
        @media (max-width: 768px) {
            body {
                padding-top: 15px;
            }
            
            .page-header {
                padding: 15px;
            }
            
            .page-title {
                font-size: 1.25rem;
                margin-bottom: 10px;
            }
            
            .stats-container {
                gap: 10px;
            }
            
            .stats-box {
                padding: 15px;
                min-width: 130px;
            }
            
            .stats-number {
                font-size: 1.25rem;
                margin-bottom: 3px;
            }
            
            .stats-label {
                font-size: 0.8rem;
            }
            
            .table th, .table td {
                padding: 10px 8px;
                font-size: 0.85rem;
            }
            
            .user-avatar {
                width: 32px;
                height: 32px;
                font-size: 0.8rem;
            }
        }
        
        @media (max-width: 576px) {
            body {
                padding-top: 10px;
            }
            
            .main-container {
                padding: 0 10px;
            }
            
            .page-header {
                padding: 12px;
            }
            
            .page-title {
                font-size: 1.1rem;
            }
            
            .search-container {
                padding: 3px 3px 3px 15px;
            }
            
            .search-container button {
                padding: 6px 15px;
                font-size: 0.9rem;
            }
            
            .card-header {
                padding: 12px 15px;
            }
            
            .card-title {
                font-size: 1.1rem;
            }
            
            .stats-container {
                flex-direction: column;
                gap: 10px;
            }
            
            .stats-box {
                width: 100%;
                min-width: auto;
            }
            
            .stats-number {
                font-size: 1.5rem;
            }
            
            .table th, .table td {
                padding: 8px 5px;
                font-size: 0.75rem;
            }
            
            .user-avatar {
                width: 28px;
                height: 28px;
                font-size: 0.7rem;
            }
            
            .source-badge, .badge-custom {
                font-size: 0.65rem;
                padding: 0.25em 0.5em;
            }
        }
        
        @media (max-width: 400px) {
            .table th, .table td {
                padding: 6px 3px;
                font-size: 0.7rem;
            }
            
            .stats-number {
                font-size: 1.25rem;
            }
            
            .source-badge, .badge-custom {
                font-size: 0.6rem;
            }
        }
    </style>
</head>
<body>
    <?php include 'includes/admin_layout.php'; ?>
    
    <div class="main-container">
        <div class="row">
            <main>
            <!-- Header Section-->
                <div class="page-header">
                    <div class="d-flex justify-content-between align-items-center flex-wrap">
                        <h1 class="page-title">All Users</h1>
                        <div class="d-flex flex-wrap gap-2 mt-3 mt-md-0">
                           
                            <div class="search-container" style="width:300px;">
                                <form method="GET" class="d-flex align-items-center">
                                    <input type="number" name="id_filter" class="form-control" placeholder="User ID..." value="<?php echo htmlspecialchars($id_filter); ?>">
                                    <button class="btn" type="submit">
                                        <i class="bi bi-search me-1"></i> Search
                                    </button>
                                </form>
                            </div>
                            <?php if (!empty($search) || !empty($id_filter)): ?>
                                <a href="all_users.php" class="btn btn-outline-secondary">
                                    <i class="bi bi-x-circle"></i> Clear
                                </a>
                            <?php endif; ?>
                        </div>
                    </div>
                </div>
                
                <!-- Alerts -->
                <?php if (isset($error)): ?>
                    <div class="alert alert-danger alert-dismissible fade show" role="alert">
                        <i class="bi bi-exclamation-triangle-fill me-2"></i>
                        <?php echo $error; ?>
                        <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                    </div>
                <?php endif; ?>
                
                <?php if ($email_sent): ?>
                    <div class="alert alert-success alert-dismissible fade show" role="alert">
                        <i class="bi bi-check-circle-fill me-2"></i>
                        <?php echo $email_message; ?>
                        <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                    </div>
                <?php endif; ?>
                
                <?php if ($email_error): ?>
                    <div class="alert alert-danger alert-dismissible fade show" role="alert">
                        <i class="bi bi-exclamation-triangle-fill me-2"></i>
                        <?php echo $email_error; ?>
                        <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                    </div>
                <?php endif; ?>
                
                <!-- Stats Section -->
                <div class="stats-container">
                    <div class="stats-box">
                        <div class="stats-number"><?php echo number_format($total_users); ?></div>
                        <div class="stats-label">Total Users</div>
                    </div>
                    <div class="stats-box">
                        <div class="stats-number"><?php echo count($users); ?></div>
                        <div class="stats-label">Current Page</div>
                    </div>
                    <div class="stats-box">
                        <div class="stats-number"><?php echo $total_pages; ?></div>
                        <div class="stats-label">Total Pages</div>
                    </div>
                    <div class="stats-box">
                        <div class="stats-number"><?php echo $page; ?></div>
                        <div class="stats-label">Current Page</div>
                    </div>
                </div>
                
                <!-- Users Table Card -->
                <div class="card">
                    <div class="card-header d-flex justify-content-between align-items-center">
                        <h5 class="card-title mb-0">User Management</h5>
                        <div class="d-flex align-items-center">
                            <span class="text-muted small me-3">Showing <?php echo count($users); ?> of <?php echo $total_users; ?> users</span>
                        </div>
                    </div>
                    <div class="card-body">
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
                            <div class="mb-3">
                                <button type="button" class="btn email-btn" id="sendEmailBtn" disabled>
                                    <i class="bi bi-envelope me-2"></i> Send Email 
                                    <span class="badge bg-white text-primary ms-2" id="selectedUsersCount">0</span>
                                </button>
                            </div>
                            
                            <!-- Users Table -->
                            <div class="table-container">
                                <table class="table table-hover">
                                    <thead>
                                        <tr>
                                            <th style="width: 40px;">
                                                <div class="form-check">
                                                    <input class="form-check-input" type="checkbox" id="selectAll">
                                                </div>
                                            </th>
                                            <th style="width: 60px;">ID</th>
                                            <th>User</th>
                                            <th>Contact</th>
                                            <th>Location</th>
                                            <th>Wallet</th>
                                            <th>Referral</th>
                                            <th>Source</th>
                                            <th>Joined</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        <?php foreach ($users as $user): ?>
                                            <tr>
                                                <td>
                                                    <div class="form-check">
                                                        <input class="form-check-input" type="checkbox" name="selected_users[]" value="<?php echo $user['id']; ?>">
                                                    </div>
                                                </td>
                                                <td><?php echo htmlspecialchars($user['id']); ?></td>
                                                <td>
                                                    <div class="d-flex align-items-center">
                                                        <div class="user-avatar me-3">
                                                            <?php echo strtoupper(substr($user['name'], 0, 1)); ?>
                                                        </div>
                                                        <div>
                                                            <div class="fw-medium"><?php echo htmlspecialchars($user['name']); ?></div>
                                                            <div class="text-muted small"><?php echo htmlspecialchars($user['email']); ?></div>
                                                        </div>
                                                    </div>
                                                </td>
                                                <td>
                                                    <div><?php echo htmlspecialchars($user['phone']); ?></div>
                                                </td>
                                                <td>
                                                    <div><?php echo htmlspecialchars($user['city']); ?></div>
                                                    <div class="text-muted small"><?php echo htmlspecialchars($user['state']); ?></div>
                                                </td>
                                                <td>
                                                    <span class="badge badge-custom badge-success">₹<?php echo number_format($user['wallet_balance'], 2); ?></span>
                                                </td>
                                                <td>
                                                    <?php if (!empty($user['referral_code'])): ?>
                                                        <span class="badge badge-custom badge-primary"><?php echo htmlspecialchars($user['referral_code']); ?></span>
                                                    <?php else: ?>
                                                        <span class="badge badge-custom badge-secondary">No code</span>
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
                                                        <span class="badge badge-custom badge-secondary">Unknown</span>
                                                    <?php endif; ?>
                                                </td>
                                                <td>
                                                    <div><?php echo date('M d, Y', strtotime($user['created_at'])); ?></div>
                                                    <div class="text-muted small"><?php echo date('h:i A', strtotime($user['created_at'])); ?></div>
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
                                                    <span aria-hidden="true"><i class="bi bi-chevron-left"></i></span>
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
                                                    <span aria-hidden="true"><i class="bi bi-chevron-right"></i></span>
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
    <div class="modal fade" id="emailModal" tabindex="-1" aria-labelledby="emailModalLabel" aria-hidden="true">
        <div class="modal-dialog modal-lg">
            <div class="modal-content border-0">
                <div class="modal-header bg-primary text-white">
                    <h5 class="modal-title" id="emailModalLabel">Send Email to Selected Users</h5>
                    <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <form id="emailForm" method="POST">
                    <div class="modal-body">
                        <input type="hidden" name="send_email" value="1">
                        <div class="mb-3">
                            <label for="emailSubject" class="form-label fw-medium">Subject</label>
                            <input type="text" class="form-control" id="emailSubject" name="subject" required>
                        </div>
                        <div class="mb-3">
                            <label for="emailMessage" class="form-label fw-medium">Message</label>
                            <textarea class="form-control" id="emailMessage" name="message" rows="6" required></textarea>
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
        
        // Handle select all checkbox
        document.getElementById('selectAll').addEventListener('change', function() {
            const checkboxes = document.querySelectorAll('input[name="selected_users[]"]');
            checkboxes.forEach(checkbox => {
                checkbox.checked = this.checked;
            });
            
            updateSelectedUsersCount();
        });
        
        // Update selected users count when individual checkboxes are changed
        document.querySelectorAll('input[name="selected_users[]"]').forEach(checkbox => {
            checkbox.addEventListener('change', updateSelectedUsersCount);
        });
        
        // Function to update selected users count
        function updateSelectedUsersCount() {
            const selectedCount = document.querySelectorAll('input[name="selected_users[]"]:checked').length;
            document.getElementById('selectedUsersCount').textContent = selectedCount;
            document.getElementById('sendEmailBtn').disabled = selectedCount === 0;
        }
    </script>
</body>
</html>