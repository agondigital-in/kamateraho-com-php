<?php
include 'config/db.php';

echo "<h2>Debug: Withdrawal Requests and Wallet History</h2>";

try {
    // Get all withdrawal requests
    echo "<h3>All Withdrawal Requests</h3>";
    $stmt = $pdo->query("SELECT wr.*, u.name, u.email FROM withdraw_requests wr JOIN users u ON wr.user_id = u.id ORDER BY wr.created_at DESC");
    $requests = $stmt->fetchAll(PDO::FETCH_ASSOC);
    
    if (!empty($requests)) {
        echo "<table border='1' cellpadding='5' cellspacing='0'>";
        echo "<tr><th>ID</th><th>User</th><th>Email</th><th>Amount</th><th>UPI ID</th><th>Status</th><th>Date</th></tr>";
        foreach ($requests as $request) {
            echo "<tr>";
            echo "<td>" . $request['id'] . "</td>";
            echo "<td>" . htmlspecialchars($request['name']) . "</td>";
            echo "<td>" . htmlspecialchars($request['email']) . "</td>";
            echo "<td>₹" . number_format($request['amount'], 2) . "</td>";
            echo "<td>" . htmlspecialchars($request['upi_id']) . "</td>";
            echo "<td>" . ucfirst($request['status']) . "</td>";
            echo "<td>" . date('d M Y H:i:s', strtotime($request['created_at'])) . "</td>";
            echo "</tr>";
        }
        echo "</table>";
    } else {
        echo "<p>No withdrawal requests found.</p>";
    }
    
    // Get all wallet history
    echo "<h3>All Wallet History</h3>";
    $stmt = $pdo->query("SELECT wh.*, u.name, u.email FROM wallet_history wh JOIN users u ON wh.user_id = u.id ORDER BY wh.created_at DESC");
    $history = $stmt->fetchAll(PDO::FETCH_ASSOC);
    
    if (!empty($history)) {
        echo "<table border='1' cellpadding='5' cellspacing='0'>";
        echo "<tr><th>ID</th><th>User</th><th>Email</th><th>Amount</th><th>Type</th><th>Status</th><th>Description</th><th>Date</th></tr>";
        foreach ($history as $entry) {
            echo "<tr>";
            echo "<td>" . $entry['id'] . "</td>";
            echo "<td>" . htmlspecialchars($entry['name']) . "</td>";
            echo "<td>" . htmlspecialchars($entry['email']) . "</td>";
            echo "<td>₹" . number_format($entry['amount'], 2) . "</td>";
            echo "<td>" . ucfirst($entry['type']) . "</td>";
            echo "<td>" . ucfirst($entry['status']) . "</td>";
            echo "<td>" . htmlspecialchars($entry['description']) . "</td>";
            echo "<td>" . date('d M Y H:i:s', strtotime($entry['created_at'])) . "</td>";
            echo "</tr>";
        }
        echo "</table>";
    } else {
        echo "<p>No wallet history found.</p>";
    }
    
    // Check for any potential duplicate entries
    echo "<h3>Potential Duplicate Entries</h3>";
    $stmt = $pdo->query("SELECT user_id, amount, type, description, COUNT(*) as count 
                         FROM wallet_history 
                         GROUP BY user_id, amount, type, description 
                         HAVING COUNT(*) > 1");
    $duplicates = $stmt->fetchAll(PDO::FETCH_ASSOC);
    
    if (!empty($duplicates)) {
        echo "<p>Found potential duplicate entries in wallet history:</p>";
        echo "<table border='1' cellpadding='5' cellspacing='0'>";
        echo "<tr><th>User ID</th><th>Amount</th><th>Type</th><th>Description</th><th>Count</th></tr>";
        foreach ($duplicates as $dup) {
            echo "<tr>";
            echo "<td>" . $dup['user_id'] . "</td>";
            echo "<td>₹" . number_format($dup['amount'], 2) . "</td>";
            echo "<td>" . ucfirst($dup['type']) . "</td>";
            echo "<td>" . htmlspecialchars($dup['description']) . "</td>";
            echo "<td>" . $dup['count'] . "</td>";
            echo "</tr>";
        }
        echo "</table>";
    } else {
        echo "<p>No duplicate entries found in wallet history.</p>";
    }
    
} catch(PDOException $e) {
    echo "<p>Error: " . $e->getMessage() . "</p>";
}

echo "<p><a href='index.php'>Back to Home</a></p>";
?>