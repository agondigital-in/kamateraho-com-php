<?php
/**
 * Export All Users to Excel
 * This file generates an Excel file with all user data
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

// Set headers for Excel download
header('Content-Type: application/vnd.ms-excel');
header('Content-Disposition: attachment; filename="kamateraho_users_' . date('Y-m-d_H-i-s') . '.xls"');
header('Pragma: no-cache');
header('Expires: 0');

?>
<!DOCTYPE html>
<html>
<head>
    <meta charset="UTF-8">
    <style>
        table {
            border-collapse: collapse;
            width: 100%;
        }
        th, td {
            border: 1px solid #000;
            padding: 8px;
            text-align: left;
        }
        th {
            background-color: #6f42c1;
            color: white;
            font-weight: bold;
        }
        tr:nth-child(even) {
            background-color: #f2f2f2;
        }
    </style>
</head>
<body>
    <table>
        <thead>
            <tr>
                <th>ID</th>
                <th>Name</th>
                <th>Email</th>
                <th>Phone</th>
                <th>City</th>
                <th>State</th>
                <th>Wallet Balance</th>
                <th>Referral Code</th>
                <th>Referral Source</th>
                <th>Joined Date</th>
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
                    <td>₹<?php echo number_format($user['wallet_balance'], 2); ?></td>
                    <td><?php echo !empty($user['referral_code']) ? htmlspecialchars($user['referral_code']) : 'N/A'; ?></td>
                    <td><?php echo !empty($user['referral_source']) ? htmlspecialchars(ucfirst($user['referral_source'])) : 'N/A'; ?></td>
                    <td><?php echo date('d-M-Y H:i', strtotime($user['created_at'])); ?></td>
                </tr>
            <?php endforeach; ?>
        </tbody>
        <tfoot>
            <tr>
                <td colspan="10" style="text-align: center; font-weight: bold;">
                    Total Users: <?php echo count($users); ?> | 
                    Generated on: <?php echo date('d-M-Y H:i:s'); ?>
                </td>
            </tr>
        </tfoot>
    </table>
</body>
</html>
