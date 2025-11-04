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
        
        // Define possible rewards
        $rewards = [1, 3, 5, 10, 15, 0]; // 0 means "Better Luck Next Time"
        
        // Determine the reward based on the rules:
        // Out of 3 spins: 2 times should show "Better Luck Next Time", 1 time gives one of ₹1, ₹3, ₹5, ₹10, or ₹15
        if ($spin_count == 0) {
            // First spin - 33% chance of reward (1, 3, 5, 10, or 15), 67% chance of "Better Luck Next Time"
            // Use array_rand on the rewards array directly to ensure all rewards can be selected
            $reward_key = array_rand([0, 1, 2, 3, 4]); // Select from indices 0-4 (1, 3, 5, 10, 15)
            $reward_amount = (rand(1, 3) == 1) ? $rewards[$reward_key] : 0;
        } elseif ($spin_count == 1) {
            // Second spin - if first was reward, this should be "Better Luck Next Time"
            // If first was "Better Luck Next Time", 50% chance of reward
            $stmt = $pdo->prepare("SELECT reward_amount FROM spin_history WHERE user_id = ? AND spin_date = CURDATE() ORDER BY id DESC LIMIT 1");
            $stmt->execute([$user_id]);
            $last_spin = $stmt->fetch(PDO::FETCH_ASSOC);
            
            if ($last_spin && $last_spin['reward_amount'] > 0) {
                $reward_amount = 0; // Second spin is "Better Luck Next Time"
            } else {
                // Use array_rand on the rewards array directly to ensure all rewards can be selected
                $reward_key = array_rand([0, 1, 2, 3, 4]); // Select from indices 0-4 (1, 3, 5, 10, 15)
                $reward_amount = (rand(0, 1) == 1) ? $rewards[$reward_key] : 0;
            }
        } else {
            // Third spin - if we haven't had a reward yet, this must be a reward
            // If we already had a reward, this should be "Better Luck Next Time"
            $stmt = $pdo->prepare("SELECT COUNT(*) as reward_count FROM spin_history WHERE user_id = ? AND spin_date = CURDATE() AND reward_amount > 0");
            $stmt->execute([$user_id]);
            $reward_count = $stmt->fetch(PDO::FETCH_ASSOC)['reward_count'];
            
            if ($reward_count == 0) {
                // Must be a reward (1, 3, 5, 10, or 15)
                $reward_key = array_rand([0, 1, 2, 3, 4]); // Select from indices 0-4 (1, 3, 5, 10, 15)
                $reward_amount = $rewards[$reward_key];
            } else {
                $reward_amount = 0; // "Better Luck Next Time"
            }
        }
        
        // Record the spin in database
        $stmt = $pdo->prepare("INSERT INTO spin_history (user_id, reward_amount, spin_date) VALUES (?, ?, CURDATE())");
        $stmt->execute([$user_id, $reward_amount]);
        
        // If user won a real reward (1, 3, 5, 10, or 15), update wallet balance
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
            3 => "🎉 Nice! You won ₹3!",
            5 => "🎉 Good! You won ₹5!",
            10 => "🎊 Awesome! You won ₹10!",
            15 => "🥳 Excellent! You won ₹15!"
        ];
        
        // Create messages for non-wins
        $consolation_messages = [
            "Better Luck Next Time! 🍀",
            "Almost! Try again! 💪",
            "So close! Spin again! 🎯",
            "Next spin is your lucky one! 🍀"
        ];
        
        echo json_encode([
            'success' => true,
            'reward' => $reward_amount,
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