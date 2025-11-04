<?php
session_start();
include 'config/db.php';

header('Content-Type: application/json');

// Check if user is logged in
if (!isset($_SESSION['user_id'])) {
    echo json_encode(['success' => false, 'message' => 'Please login to spin']);
    exit;
}

$user_id = $_SESSION['user_id'];

// Check if it's a POST request to spin
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    try {
        // Check how many spins user has today
        $stmt = $pdo->prepare("SELECT COUNT(*) as spin_count FROM spin_history WHERE user_id = ? AND spin_date = CURDATE()");
        $stmt->execute([$user_id]);
        $spin_count = $stmt->fetch(PDO::FETCH_ASSOC)['spin_count'];
        
        // Limit to 1 spin per day
        if ($spin_count >= 1) {
            echo json_encode(['success' => false, 'message' => 'You have already spun today. Better Luck! Try Again Tomorrow.']);
            exit;
        }
        
        // Define possible rewards (1, 3, 5 only as per requirements)
        $rewards = [1, 3, 5]; // Only these values should appear
        
        // Select a random reward
        $reward_amount = $rewards[array_rand($rewards)];
        
        // Record the spin in database
        $stmt = $pdo->prepare("INSERT INTO spin_history (user_id, reward_amount, spin_date) VALUES (?, ?, CURDATE())");
        $stmt->execute([$user_id, $reward_amount]);
        
        // Update wallet balance with the reward amount
        if ($reward_amount > 0) {
            // Update user's wallet balance
            $stmt = $pdo->prepare("UPDATE users SET wallet_balance = wallet_balance + ? WHERE id = ?");
            $stmt->execute([$reward_amount, $user_id]);
            
            // Add to wallet history
            $stmt = $pdo->prepare("INSERT INTO wallet_history (user_id, amount, type, status, description) VALUES (?, ?, 'credit', 'approved', 'Spin & Earn Reward')");
            $stmt->execute([$user_id, $reward_amount]);
        }
        
        // Create celebratory messages for wins
        $celebration_messages = [
            1 => "🎉 Great! You won ₹1!",
            3 => "🎊 Awesome! You won ₹3!",
            5 => "🥳 Excellent! You won ₹5!"
        ];
        
        echo json_encode([
            'success' => true,
            'reward' => $reward_amount,
            'message' => $reward_amount > 0 ? $celebration_messages[$reward_amount] : "Better Luck! Try Again Tomorrow.",
            'spins_left' => 0 // No spins left after first spin
        ]);
        
    } catch(PDOException $e) {
        echo json_encode(['success' => false, 'message' => 'Database error: ' . $e->getMessage()]);
    }
} else {
    // GET request - return spin count for today
    try {
        $stmt = $pdo->prepare("SELECT COUNT(*) as spin_count FROM spin_history WHERE user_id = ? AND spin_date = CURDATE()");
        $stmt->execute([$user_id]);
        $spin_count = $stmt->fetch(PDO::FETCH_ASSOC)['spin_count'];
        
        echo json_encode([
            'success' => true,
            'spins_used' => $spin_count,
            'spins_left' => 1 - $spin_count
        ]);
    } catch(PDOException $e) {
        echo json_encode(['success' => false, 'message' => 'Database error: ' . $e->getMessage()]);
    }
}
?>