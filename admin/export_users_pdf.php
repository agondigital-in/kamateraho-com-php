<?php
/**
 * Export All Users to PDF
 * This file generates a PDF file with all user data
 * Using HTML to PDF conversion (browser print)
 */

include 'db_connect.php';

// Check if admin is logged in
if (!isset($_SESSION['admin_logged_in']) || !$_SESSION['admin_logged_in']) {
    die('Unauthorized access');
}

// Fetch all users
try {
    $sql = "SELECT id, name, email, phone, city, state, wallet_balance, referral_code, referral_source, created_at 
            FROM users 
            ORDER BY created_at DESC";
    
    $stmt = $pdo->prepare($sql);
    $stmt->execute();
    $users = $stmt->fetchAll(PDO::FETCH_ASSOC);
} catch (PDOException $e) {
    die("Error fetching users: " . $e->getMessage());
}

// Calculate totals
$total_users = count($users);
$total_wallet_balance = array_sum(array_column($users, 'wallet_balance'));
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>KamateRaho - All Users Report</title>
    <style>
        @page {
            size: A4 landscape;
            margin: 15mm;
        }
        
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }
        
        body {
            font-family: Arial, Helvetica, sans-serif;
            font-size: 10px;
            line-height: 1.4;
            color: #333;
        }
        
        .header {
            text-align: center;
            margin-bottom: 20px;
            padding-bottom: 15px;
            border-bottom: 3px solid #6f42c1;
        }
        
        .header h1 {
            color: #6f42c1;
            font-size: 24px;
            margin-bottom: 5px;
        }
        
        .header p {
            color: #666;
            font-size: 12px;
        }
        
        .summary {
            display: flex;
            justify-content: space-around;
            margin-bottom: 20px;
            background: #f8f9fa;
            padding: 15px;
            border-radius: 8px;
        }
        
        .summary-item {
            text-align: center;
        }
        
        .summary-item .label {
            font-size: 11px;
            color: #666;
            margin-bottom: 5px;
        }
        
        .summary-item .value {
            font-size: 18px;
            font-weight: bold;
            color: #6f42c1;
        }
        
        table {
            width: 100%;
            border-collapse: collapse;
            margin-bottom: 20px;
        }
        
        thead {
            background: #6f42c1;
            color: white;
        }
        
        th {
            padding: 10px 5px;
            text-align: left;
            font-weight: bold;
            font-size: 10px;
            border: 1px solid #5a32a3;
        }
        
        td {
            padding: 8px 5px;
            border: 1px solid #ddd;
            font-size: 9px;
        }
        
        tbody tr:nth-child(even) {
            background-color: #f8f9fa;
        }
        
        tbody tr:hover {
            background-color: #e9ecef;
        }
        
        .footer {
            text-align: center;
            margin-top: 20px;
            padding-top: 15px;
            border-top: 2px solid #6f42c1;
            font-size: 11px;
            color: #666;
        }
        
        .badge {
            display: inline-block;
            padding: 3px 8px;
            border-radius: 12px;
            font-size: 8px;
            font-weight: bold;
        }
        
        .badge-success {
            background: #28a745;
            color: white;
        }
        
        .badge-info {
            background: #17a2b8;
            color: white;
        }
        
        .badge-secondary {
            background: #6c757d;
            color: white;
        }
        
        /* Print styles */
        @media print {
            body {
                -webkit-print-color-adjust: exact;
                print-color-adjust: exact;
            }
            
            .no-print {
                display: none;
            }
            
            thead {
                display: table-header-group;
            }
            
            tr {
                page-break-inside: avoid;
            }
        }
        
        .print-button {
            position: fixed;
            top: 20px;
            right: 20px;
            padding: 12px 24px;
            background: #6f42c1;
            color: white;
            border: none;
            border-radius: 8px;
            cursor: pointer;
            font-size: 14px;
            font-weight: bold;
            box-shadow: 0 4px 6px rgba(0, 0, 0, 0.2);
            z-index: 1000;
        }
        
        .print-button:hover {
            background: #5a32a3;
        }
    </style>
</head>
<body>
    <button class="print-button no-print" onclick="window.print()">
        🖨️ Print / Save as PDF
    </button>
    
    <div class="header">
        <h1>KamateRaho - All Users Report</h1>
        <p>Generated on: <?php echo date('d F Y, h:i A'); ?></p>
    </div>
    
    <div class="summary">
        <div class="summary-item">
            <div class="label">Total Users</div>
            <div class="value"><?php echo number_format($total_users); ?></div>
        </div>
        <div class="summary-item">
            <div class="label">Total Wallet Balance</div>
            <div class="value">₹<?php echo number_format($total_wallet_balance, 2); ?></div>
        </div>
        <div class="summary-item">
            <div class="label">Average Balance</div>
            <div class="value">₹<?php echo number_format($total_users > 0 ? $total_wallet_balance / $total_users : 0, 2); ?></div>
        </div>
    </div>
    
    <table>
        <thead>
            <tr>
                <th style="width: 4%;">ID</th>
                <th style="width: 15%;">Name</th>
                <th style="width: 18%;">Email</th>
                <th style="width: 10%;">Phone</th>
                <th style="width: 10%;">City</th>
                <th style="width: 8%;">State</th>
                <th style="width: 8%;">Wallet</th>
                <th style="width: 10%;">Referral</th>
                <th style="width: 8%;">Source</th>
                <th style="width: 9%;">Joined</th>
            </tr>
        </thead>
        <tbody>
            <?php foreach ($users as $user): ?>
                <tr>
                    <td><?php echo htmlspecialchars($user['id']); ?></td>
                    <td><?php echo htmlspecialchars($user['name']); ?></td>
                    <td><?php echo htmlspecialchars($user['email']); ?></td>
                    <td><?php echo htmlspecialchars($user['phone']); ?></td>
                    <td><?php echo htmlspecialchars($user['city']); ?></td>
                    <td><?php echo htmlspecialchars($user['state']); ?></td>
                    <td>
                        <span class="badge badge-success">
                            ₹<?php echo number_format($user['wallet_balance'], 2); ?>
                        </span>
                    </td>
                    <td>
                        <?php if (!empty($user['referral_code'])): ?>
                            <span class="badge badge-info"><?php echo htmlspecialchars($user['referral_code']); ?></span>
                        <?php else: ?>
                            <span class="badge badge-secondary">N/A</span>
                        <?php endif; ?>
                    </td>
                    <td><?php echo !empty($user['referral_source']) ? htmlspecialchars(ucfirst($user['referral_source'])) : 'N/A'; ?></td>
                    <td><?php echo date('d-M-Y', strtotime($user['created_at'])); ?></td>
                </tr>
            <?php endforeach; ?>
        </tbody>
    </table>
    
    <div class="footer">
        <p><strong>KamateRaho Admin Panel</strong> | Total Users: <?php echo number_format($total_users); ?> | 
        Report Generated: <?php echo date('d F Y, h:i:s A'); ?></p>
        <p style="margin-top: 5px; font-size: 9px;">This is a computer-generated report. No signature required.</p>
    </div>
    
    <script>
        // Auto-print dialog can be triggered if needed
        // window.onload = function() { window.print(); }
    </script>
</body>
</html>
