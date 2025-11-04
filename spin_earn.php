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
        
        // Limit to 3 spins per day
        if ($spin_count >= 3) {
            echo json_encode(['success' => false, 'message' => 'You have reached your maximum spins for today']);
            exit;
        }
        
        // Define possible rewards (1, 3, 5 only as per requirements)
        $rewards = [1, 3, 5]; // Only these values should appear
        
        // For tracking spin behavior
        $spin_behavior = '';
        
        // Determine reward based on spin count
        if ($spin_count == 0) {
            // First spin - normal behavior, random selection from 1, 3, 5
            $reward_amount = $rewards[array_rand($rewards)];
            $spin_behavior = 'normal';
        } elseif ($spin_count == 1) {
            // Second spin - more rotations
            $reward_amount = $rewards[array_rand($rewards)];
            $spin_behavior = 'more_rotations';
        } else {
            // Third spin - full rotation
            $reward_amount = $rewards[array_rand($rewards)];
            $spin_behavior = 'full_rotation';
        }
        
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
        
        // Create messages for non-wins (shouldn't happen with our setup)
        $consolation_messages = [
            "Better Luck Next Time! 🍀",
            "Almost! Try again! 💪",
            "So close! Spin again! 🎯",
            "Next spin is your lucky one! 🍀"
        ];
        
        echo json_encode([
            'success' => true,
            'reward' => $reward_amount,
            'spin_behavior' => $spin_behavior,
            'message' => $reward_amount > 0 ? $celebration_messages[$reward_amount] : 
                        $consolation_messages[array_rand($consolation_messages)],
            'spins_left' => 2 - $spin_count
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
            'spins_left' => 3 - $spin_count
        ]);
    } catch(PDOException $e) {
        echo json_encode(['success' => false, 'message' => 'Database error: ' . $e->getMessage()]);
    }
}
?>