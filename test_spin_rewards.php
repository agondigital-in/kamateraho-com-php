<?php
// Test script to verify that all rewards can be selected

// Define possible rewards
$rewards = [1, 3, 5, 10, 15, 0]; // 0 means "Better Luck Next Time"

echo "Testing reward selection:\n";

// Test 100 spins to see distribution
$reward_counts = [
    1 => 0,
    3 => 0,
    5 => 0,
    10 => 0,
    15 => 0,
    0 => 0
];

for ($i = 0; $i < 100; $i++) {
    // Simulate the reward selection logic for first spin
    $reward_key = array_rand([0, 1, 2, 3, 4]); // Select from indices 0-4 (1, 3, 5, 10, 15)
    $reward_amount = (rand(1, 3) == 1) ? $rewards[$reward_key] : 0;
    
    $reward_counts[$reward_amount]++;
}

echo "Reward distribution over 100 simulated spins:\n";
foreach ($reward_counts as $reward => $count) {
    $label = $reward == 0 ? "Better Luck Next Time" : "₹" . $reward;
    echo "$label: $count times\n";
}
?>